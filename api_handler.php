<?php
// api_handler.php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

include 'db.php'; // اتصال به دیتابیس برای ثبت هشدار

// 1. کلید Gemini (معمولا دیرتر تمام می‌شود)
define('GEMINI_KEY', 'AIzaSyDdd9zqKPjG_UtkJsg3fXp-M8bEeNyxCxk');

// 2. لیست کلیدهای D-ID (هر چقدر بخواهید اضافه کنید)
$did_keys = [
    "c2luYV9oejIwMDBAeWFob28uY29t:jClIOwk1BFOs-5jfkH2wH", 
    "aHpuODA2NTExMkBnbWFpbC5jb20:M4IUOR9sHCAhM5mjIn47d",
    "aHpuODA2NTEyQGdtYWlsLmNvbQ:zSIBFRTMN4HGa8Fpn8M4k",
    "aHpuODA2NTE1QGdtYWlsLmNvbQ:PHYy6DvdC6IDiIQfvdvt_",
];

$action = $_POST['action'] ?? '';

// تابع کمکی برای ارسال درخواست
function sendRequest($url, $method, $headers, $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        return ['status' => 'curl_error', 'msg' => curl_error($ch)];
    }
    
    curl_close($ch);
    
    return ['status' => $httpCode, 'body' => $response];
}

// --- 1. درخواست متن از Gemini ---
if ($action === 'gemini') {
    $pName = $_POST['name'] ?? 'محصول';
    $pDesc = $_POST['desc'] ?? '';
    
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . GEMINI_KEY;
    $rand = rand(100, 999);
    $prompt = "تو فروشنده بوتیک هستی. محصول: '$pName' ($pDesc). (Seed:$rand). یک جمله بسیار کوتاه (زیر 8 کلمه)، جذاب و عامیانه فارسی بگو. فقط متن خالی.";
    
    $data = json_encode(["contents" => [["parts" => [["text" => $prompt]]]]]);
    $headers = ["Content-Type: application/json"];
    
    $res = sendRequest($url, 'POST', $headers, $data);
    echo $res['body'];
    exit;
}

// --- 2. درخواست ساخت ویدیو (با سیستم چرخش کلید) ---
if ($action === 'did_create_talk') {
    $text = $_POST['text'];
    $imgUrl = $_POST['img_url'];
    
    $success = false;
    $finalResponse = null;

    // حلقه روی تمام کلیدها
    foreach ($did_keys as $key) {
        // اگر کلید خالی بود رد کن
        if ($key == "YOUR_NEW_KEY_2_HERE" || empty($key)) continue;

        $url = "https://api.d-id.com/talks";
        $headers = [
            "Authorization: Basic " . base64_encode($key),
            "Content-Type: application/json"
        ];
        
        $body = json_encode([
            "source_url" => $imgUrl,
            "script" => [
                "type" => "text",
                "input" => $text,
                "provider" => ["type" => "microsoft", "voice_id" => "fa-IR-FaridNeural"]
            ],
            "config" => ["fluent" => true, "pad_audio" => "0.0", "stitch" => true]
        ]);
        
        $result = sendRequest($url, 'POST', $headers, $body);
        
        // بررسی کد وضعیت
        if ($result['status'] == 201 || $result['status'] == 200) {
            // موفقیت!
            echo $result['body'];
            $success = true;
            break; // خروج از حلقه
        } elseif ($result['status'] == 402 || $result['status'] == 401 || $result['status'] == 403) {
            // اعتبار تمام شده یا کلید غلط است -> برو سراغ کلید بعدی
            continue;
        } else {
            // خطای فنی دیگر (مثلا تصویر مشکل دارد) -> ذخیره کن ولی ادامه نده چون ربطی به کلید ندارد
            $finalResponse = json_encode(['error' => 'Technical Error ' . $result['status'], 'details' => $result['body']]);
            break;
        }
    }

    // اگر هیچ کلیدی کار نکرد (همه سوختند)
    if (!$success) {
        // 1. ثبت پیام هشدار در دیتابیس برای ادمین
        notifyAdminOfFailure($conn);
        
        // 2. برگشت خطا به فرانت
        if ($finalResponse) {
            echo $finalResponse;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'All API Keys Exhausted! Admin Notified.']);
        }
    }
    exit;
}

// --- 3. دریافت وضعیت ویدیو ---
if ($action === 'did_get_talk') {
    $talkId = $_POST['talk_id'];
    
    // اینجا باید بدانیم کدام کلید استفاده شده، اما برای سادگی، با همان کلید اول چک میکنیم یا دوباره لوپ می‌زنیم.
    // برای بهینگی، ما فعلاً با کلید اول تست میکنیم، اگر 401 داد دومی.
    // نکته: D-ID معمولا برای GET سختگیری کمتری دارد یا باید کلید درست را داشته باشیم.
    // راه حل ساده: در این مرحله هم روی کلیدها لوپ می‌زنیم تا یکی جواب بدهد.
    
    foreach ($did_keys as $key) {
        if ($key == "YOUR_NEW_KEY_2_HERE" || empty($key)) continue;

        $url = "https://api.d-id.com/talks/" . $talkId;
        $headers = [
            "Authorization: Basic " . base64_encode($key),
            "Content-Type: application/json"
        ];
        
        $result = sendRequest($url, 'GET', $headers);
        
        if ($result['status'] == 200) {
            echo $result['body'];
            exit;
        }
    }
    echo json_encode(['status' => 'error', 'message' => 'Could not retrieve video']);
    exit;
}

// --- تابع ثبت هشدار در دیتابیس ---
function notifyAdminOfFailure($conn) {
    // بررسی وجود کاربر سیستم
    $sysPhone = "0000000000";
    $check = $conn->query("SELECT id FROM users WHERE phone = '$sysPhone'");
    
    if ($check->num_rows == 0) {
        // ساخت کاربر سیستم اگر نیست
        $conn->query("INSERT INTO users (name, phone, email, password, is_verified) VALUES ('System Alert', '$sysPhone', 'sys@admin.com', '123', 1)");
        $sysId = $conn->insert_id;
    } else {
        $sysId = $check->fetch_assoc()['id'];
    }
    
    // ثبت پیام
    $msg = "⚠️ هشدار: تمام کلیدهای هوش مصنوعی (D-ID) منقضی شده‌اند یا اعتبار ندارند. لطفاً فایل api_handler.php را باز کرده و کلیدهای جدید اضافه کنید.";
    
    // چک کنیم که همین پیام اخیرا ثبت نشده باشد (جلوگیری از اسپم)
    $lastMsg = $conn->query("SELECT created_at FROM messages WHERE user_id = $sysId ORDER BY id DESC LIMIT 1");
    $shouldInsert = true;
    if ($lastMsg->num_rows > 0) {
        $lastTime = strtotime($lastMsg->fetch_assoc()['created_at']);
        // اگر کمتر از 1 ساعت پیش پیام داده، دوباره نده
        if (time() - $lastTime < 3600) {
            $shouldInsert = false;
        }
    }
    
    if ($shouldInsert) {
        $conn->query("INSERT INTO messages (user_id, sender, message, is_read, created_at) VALUES ('$sysId', 'user', '$msg', 0, NOW())");
    }
}
?>