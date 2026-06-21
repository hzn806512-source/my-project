<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// --- 1. درخواست ثبت نام (ارسال کد تایید) ---
if (isset($_POST['action']) && $_POST['action'] == 'register_request') {
    $name = cleanInput($_POST['name']);
    $phone = cleanInput($_POST['phone']);
    $email = cleanInput($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $verify_code = rand(1000, 9999);

    // چک کردن تکراری بودن ایمیل
    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        $user = $check->fetch_assoc();
        if ($user['is_verified'] == 1) {
            echo json_encode(['status' => 'error', 'message' => 'این ایمیل قبلاً ثبت شده است. لطفاً وارد شوید.']);
            exit();
        } else {
            // آپدیت کاربر تایید نشده
            $sql = "UPDATE users SET name='$name', phone='$phone', password='$password', verification_code='$verify_code' WHERE email='$email'";
        }
    } else {
        // کاربر جدید
        $sql = "INSERT INTO users (name, phone, email, password, verification_code, is_verified) 
                VALUES ('$name', '$phone', '$email', '$password', '$verify_code', 0)";
    }

    if ($conn->query($sql)) {
        // ارسال ایمیل واقعی (در هاست رایگان ممکن است اسپم شود)
        $subject = "کد تایید بوتیک پاریس";
        $message = "کد تایید شما: $verify_code";
        $headers = "From: noreply@pizzaparis.xo.je";
        @mail($email, $subject, $message, $headers);

        echo json_encode([
            'status' => 'success', 
            'message' => "کد تایید به ایمیل $email ارسال شد. (کد آزمایشی: $verify_code)" 
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'خطای دیتابیس: ' . $conn->error]);
    }
}

// --- 2. تایید کد ایمیل ---
if (isset($_POST['action']) && $_POST['action'] == 'verify_code') {
    $email = cleanInput($_POST['email']);
    $code = cleanInput($_POST['code']);

    $check = $conn->query("SELECT * FROM users WHERE email='$email' AND verification_code='$code'");
    
    if ($check->num_rows > 0) {
        $user = $check->fetch_assoc();
        $conn->query("UPDATE users SET is_verified=1, verification_code=NULL WHERE id=" . $user['id']);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_phone'] = $user['phone'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_pic'] = $user['profile_pic'];

        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'کد اشتباه است!']);
    }
}

// --- 3. ورود (Login) - اصلاح شده برای ایمیل ---
if (isset($_POST['action']) && $_POST['action'] == 'login') {
    // دریافت ایمیل به جای شماره
    $email = cleanInput($_POST['email']); 
    $password = $_POST['password'];

    // جستجو بر اساس ایمیل
    $res = $conn->query("SELECT * FROM users WHERE email = '$email'");
    
    if ($res->num_rows > 0) {
        $user = $res->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            if ($user['is_verified'] == 0) {
                echo json_encode(['status' => 'error', 'message' => 'حساب تایید نشده است.']);
                exit();
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_phone'] = $user['phone'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_pic'] = !empty($user['profile_pic']) ? $user['profile_pic'] : 'https://cdn.jsdelivr.net/gh/microsoft/fluentui-emoji@latest/assets/Person/3D/person_3d.png';
            
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
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'لطفا وارد شوید']);
        exit();
    }

    if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $ext = pathinfo($_FILES['profile_img']['name'], PATHINFO_EXTENSION);
        $filename = "Profile_" . $_SESSION['user_id'] . "_" . time() . "." . $ext;
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES['profile_img']['tmp_name'], $target_file)) {
            $uid = $_SESSION['user_id'];
            $conn->query("UPDATE users SET profile_pic = '$target_file' WHERE id = $uid");
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
    if (!isset($_SESSION['user_id'])) exit();

    $uid = $_SESSION['user_id'];
    $msg = cleanInput($_POST['message']);
    $conn->query("INSERT INTO messages (user_id, sender, message, created_at) VALUES ('$uid', 'user', '$msg', NOW())");
    echo json_encode(['status' => 'success']);
}

// --- 6. خروج ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
}

// --- 7. ارسال اعلان پیک (ادمین) ---
if (isset($_POST['action']) && $_POST['action'] == 'notify_delivery') {
    $user_id = $_POST['user_id'];
    $order_id = $_POST['order_id'];
    
    $message = "سفارش شما (کد $order_id) ارسال شد 🛵. لطفاً آماده تحویل باشید.";
    
    $conn->query("INSERT INTO messages (user_id, sender, message, is_read, created_at) VALUES ('$user_id', 'admin', '$message', 0, NOW())");
    $conn->query("UPDATE orders SET status = 'sent' WHERE id = '$order_id'");
    
    echo json_encode(['status' => 'success']);
}
?>