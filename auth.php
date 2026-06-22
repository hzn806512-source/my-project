<?php
/**
 * Authentication Handler
 * Improved with prepared statements and security measures
 */

session_start();
include 'db.php';

header('Content-Type: application/json');

// CSRF Token validation
function validateCSRFToken() {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'CSRF token validation failed']);
        exit();
    }
}

// Generate CSRF Token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// --- 1. درخواست ثبت نام (ارسال کد تایید) ---
if (isset($_POST['action']) && $_POST['action'] == 'register_request') {
    validateCSRFToken();
    
    // Rate limiting
    $ip = $_SERVER['REMOTE_ADDR'];
    if (!checkRateLimit($ip)) {
        http_response_code(429);
        echo json_encode(['status' => 'error', 'message' => 'درخواست‌های بیش‌ازحد. لطفاً بعداً تلاش کنید.']);
        exit();
    }
    
    $name = cleanInput($_POST['name'] ?? '');
    $phone = cleanInput($_POST['phone'] ?? '');
    $email = cleanInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($name) || empty($phone) || empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'تمام فیلدها الزامی هستند']);
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'ایمیل نامعتبر است']);
        exit();
    }
    
    if (strlen($password) < 8) {
        echo json_encode(['status' => 'error', 'message' => 'رمز عبور باید حداقل 8 کاراکتر باشد']);
        exit();
    }
    
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $verify_code = rand(1000, 9999);
    
    // Check for existing user
    $result = dbSelect(
        "SELECT id, is_verified FROM users WHERE email = ?",
        [$email],
        's'
    );
    
    if (!empty($result)) {
        $user = $result[0];
        if ($user['is_verified'] == 1) {
            echo json_encode(['status' => 'error', 'message' => 'این ایمیل قبلاً ثبت شده است. لطفاً وارد شوید.']);
            exit();
        } else {
            // Update unverified user
            $update_result = dbExecute(
                "UPDATE users SET name = ?, phone = ?, password = ?, verification_code = ? WHERE email = ?",
                [$name, $phone, $hashed_password, $verify_code, $email],
                'sssss'
            );
        }
    } else {
        // Insert new user
        $insert_result = dbExecute(
            "INSERT INTO users (name, phone, email, password, verification_code, is_verified) VALUES (?, ?, ?, ?, ?, 0)",
            [$name, $phone, $email, $hashed_password, $verify_code],
            'sssss'
        );
    }
    
    // Send verification email
    $subject = "کد تایید بوتیک پاریس";
    $message = "کد تایید شما: $verify_code";
    $headers = "From: " . $_ENV['MAIL_FROM'] . "\r\nContent-Type: text/plain; charset=UTF-8";
    
    @mail($email, $subject, $message, $headers);
    
    echo json_encode([
        'status' => 'success',
        'message' => "کد تایید به ایمیل $email ارسال شد."
    ]);
}

// --- 2. تایید کد ایمیل ---
if (isset($_POST['action']) && $_POST['action'] == 'verify_code') {
    validateCSRFToken();
    
    $email = cleanInput($_POST['email'] ?? '');
    $code = cleanInput($_POST['code'] ?? '');
    
    $result = dbSelect(
        "SELECT id, password FROM users WHERE email = ? AND verification_code = ?",
        [$email, $code],
        'ss'
    );
    
    if (!empty($result)) {
        $user = $result[0];
        
        dbExecute(
            "UPDATE users SET is_verified = 1, verification_code = NULL WHERE id = ?",
            [$user['id']],
            'i'
        );
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_phone'] = $user['phone'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_pic'] = $user['profile_pic'] ?? 'https://cdn.jsdelivr.net/gh/microsoft/fluentui-emoji@latest/assets/Person/3D/person_3d.png';
        $_SESSION['csrf_token'] = generateCSRFToken();
        
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'کد اشتباه است!']);
    }
}

// --- 3. ورود (Login) ---
if (isset($_POST['action']) && $_POST['action'] == 'login') {
    validateCSRFToken();
    
    // Rate limiting
    $ip = $_SERVER['REMOTE_ADDR'];
    if (!checkRateLimit("login_" . $ip)) {
        http_response_code(429);
        echo json_encode(['status' => 'error', 'message' => 'درخواست‌های بیش‌ازحد. لطفاً بعداً تلاش کنید.']);
        exit();
    }
    
    $email = cleanInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'ایمیل و رمز عبور الزامی هستند']);
        exit();
    }
    
    $result = dbSelect(
        "SELECT id, name, phone, email, profile_pic, password, is_verified FROM users WHERE email = ?",
        [$email],
        's'
    );
    
    if (!empty($result)) {
        $user = $result[0];
        
        if (password_verify($password, $user['password'])) {
            if ($user['is_verified'] == 0) {
                echo json_encode(['status' => 'error', 'message' => 'حساب تایید نشده است.']);
                exit();
            }
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_phone'] = $user['phone'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_pic'] = $user['profile_pic'] ?? 'https://cdn.jsdelivr.net/gh/microsoft/fluentui-emoji@latest/assets/Person/3D/person_3d.png';
            $_SESSION['csrf_token'] = generateCSRFToken();
            $_SESSION['login_time'] = time();
            
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'رمز عبور اشتباه است.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'کاربری با این ایمیل یافت نشد.']);
    }
}

// --- 4. آپلود عکس پروفایل ---
if (isset($_POST['action']) && $_POST['action'] == 'upload_profile') {
    validateCSRFToken();
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'لطفا وارد شوید']);
        exit();
    }
    
    if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] == 0) {
        // Validate file
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['profile_img']['type'], $allowed_types)) {
            echo json_encode(['status' => 'error', 'message' => 'نوع فایل مجاز نیست']);
            exit();
        }
        
        $max_size = 5 * 1024 * 1024; // 5MB
        if ($_FILES['profile_img']['size'] > $max_size) {
            echo json_encode(['status' => 'error', 'message' => 'حجم فایل بزرگ است']);
            exit();
        }
        
        $target_dir = "uploads/profiles/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        $ext = pathinfo($_FILES['profile_img']['name'], PATHINFO_EXTENSION);
        $filename = "Profile_" . $_SESSION['user_id'] . "_" . time() . "." . $ext;
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES['profile_img']['tmp_name'], $target_file)) {
            $uid = $_SESSION['user_id'];
            dbExecute(
                "UPDATE users SET profile_pic = ? WHERE id = ?",
                [$target_file, $uid],
                'si'
            );
            $_SESSION['user_pic'] = $target_file;
            echo json_encode(['status' => 'success', 'url' => $target_file]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'خطا در ذخیره فایل']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فایلی انتخاب نشده است']);
    }
}

// --- 5. ارسال پیام کاربر به ادمین ---
if (isset($_POST['action']) && $_POST['action'] == 'send_msg') {
    validateCSRFToken();
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'لطفا وارد شوید']);
        exit();
    }
    
    $uid = $_SESSION['user_id'];
    $msg = cleanInput($_POST['message'] ?? '');
    
    if (empty($msg)) {
        echo json_encode(['status' => 'error', 'message' => 'پیام نمی‌تواند خالی باشد']);
        exit();
    }
    
    $result = dbExecute(
        "INSERT INTO messages (user_id, sender, message, created_at) VALUES (?, 'user', ?, NOW())",
        [$uid, $msg],
        'is'
    );
    
    if ($result['success']) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'خطا در ارسال پیام']);
    }
}

// --- 6. خروج ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// --- 7. ارسال اعلان تحویل (ادمین) ---
if (isset($_POST['action']) && $_POST['action'] == 'notify_delivery') {
    validateCSRFToken();
    
    $user_id = intval($_POST['user_id'] ?? 0);
    $order_id = intval($_POST['order_id'] ?? 0);
    
    if (empty($user_id) || empty($order_id)) {
        echo json_encode(['status' => 'error', 'message' => 'اطلاعات ناقص است']);
        exit();
    }
    
    $message = "سفارش شما (کد $order_id) ارسال شد 🛵. لطفاً آماده تحویل باشید.";
    
    dbExecute(
        "INSERT INTO messages (user_id, sender, message, is_read, created_at) VALUES (?, 'admin', ?, 0, NOW())",
        [$user_id, $message],
        'is'
    );
    
    dbExecute(
        "UPDATE orders SET status = 'sent' WHERE id = ?",
        [$order_id],
        'i'
    );
    
    echo json_encode(['status' => 'success']);
}

?>
