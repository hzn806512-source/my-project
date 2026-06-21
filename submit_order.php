<?php
session_start();
include 'db.php';

// **************************************************
// تنظیمات درگاه پرداخت (کد مرچنت خود را وارد کنید)
$merchant_id = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"; 
// **************************************************

if (isset($_POST['final_total_amount'])) {
    
    // 1. دریافت اطلاعات پایه
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['main_product_id']; // محصول اصلی جهت ارجاع
    $customer_name = $_SESSION['user_name'];
    $phone = $_SESSION['user_phone'];
    $email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';
    $address = cleanInput($_POST['address']);
    $amount = intval($_POST['final_total_amount']); // مبلغ نهایی (شامل ارسال)
    
    // 2. دریافت جزئیات سبد خرید (JSON)
    // این رشته شامل تمام رنگ‌ها و اقلام جانبی است
    $order_details_json = $_POST['order_details']; 
    
    // محاسبه تعداد کل آیتم‌ها
    $details_array = json_decode($order_details_json, true);
    $total_qty = 0;
    if (is_array($details_array)) {
        foreach ($details_array as $item) {
            $total_qty += intval($item['qty']);
        }
    } else {
        $total_qty = 1;
    }

    // ایمن‌سازی JSON برای ذخیره در دیتابیس
    $safe_json = mysqli_real_escape_string($conn, $order_details_json);

    // 3. ثبت سفارش در دیتابیس
    // ستون order_details_json پر می‌شود تا بعداً در فاکتور و ادمین نمایش داده شود
    $sql = "INSERT INTO orders (user_id, product_id, customer_name, phone, email, address, amount, quantity, payment_status, status, order_details_json, created_at) 
            VALUES ('$user_id', '$product_id', '$customer_name', '$phone', '$email', '$address', '$amount', '$total_qty', 'در انتظار پرداخت', 'pending', '$safe_json', NOW())";

    if ($conn->query($sql) === TRUE) {
        $order_db_id = $conn->insert_id;
    } else {
        // نمایش خطای دیتابیس با استایل
        include 'header.php';
        die("<div class='container mx-auto py-20 text-center text-red-500 font-bold'>خطا در ثبت سفارش: " . $conn->error . "</div>");
    }

    // 4. اتصال به زرین‌پال
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $domain = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['PHP_SELF']);
    
    // آدرس بازگشت
    $callback_url = "$protocol://$domain$path/verify.php";

    $data = array(
        "merchant_id" => $merchant_id,
        "amount" => $amount,
        "currency" => "IRT",
        "callback_url" => $callback_url,
        "description" => "سفارش شماره $order_db_id | بوتیک پاریس",
        "metadata" => [
            "mobile" => $phone,
            "email" => $email
        ]
    );

    $jsonData = json_encode($data);
    $ch = curl_init('https://payment.zarinpal.com/pg/v4/payment/request.json');
    curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData)
    ));

    $result = curl_exec($ch);
    $err = curl_error($ch);
    $result = json_decode($result, true, JSON_PRETTY_PRINT);
    curl_close($ch);

    // 5. بررسی نتیجه و هدایت به درگاه
    if ($err) {
        include 'header.php';
        echo "<div class='container mx-auto py-20 text-center text-white'>خطای cURL: $err</div>";
    } else {
        if (isset($result['data']['code']) && $result['data']['code'] == 100) {
            $authority = $result['data']['authority'];
            
            // ذخیره Authority در دیتابیس
            $conn->query("UPDATE orders SET authority = '$authority' WHERE id = $order_db_id");

            // هدایت کاربر به صفحه پرداخت بانک
            header('Location: https://payment.zarinpal.com/pg/StartPay/' . $authority);
            exit;
        } else {
            // نمایش خطای زرین‌پال
            include 'header.php';
            echo '
            <div class="container mx-auto px-4 py-20 text-center">
                <div class="bg-[#1f2937] border border-red-500 p-8 rounded-2xl max-w-lg mx-auto shadow-2xl">
                    <h2 class="text-2xl font-bold text-red-500 mb-4">خطا در اتصال به بانک</h2>
                    <p class="text-white mb-4">کد خطا: ' . (isset($result['errors']['code']) ? $result['errors']['code'] : 'نامشخص') . '</p>
                    <p class="text-gray-400 text-sm">' . (isset($result['errors']['message']) ? $result['errors']['message'] : 'پاسخ نامعتبر از بانک') . '</p>
                    <a href="index.php" class="inline-block mt-6 bg-[#d4af37] text-black px-6 py-2 rounded-full font-bold transition hover:scale-105">بازگشت به فروشگاه</a>
                </div>
            </div>';
        }
    }

} else {
    header("Location: index.php");
}
?>