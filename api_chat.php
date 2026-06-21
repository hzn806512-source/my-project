<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

$action = isset($_POST['action']) ? $_POST['action'] : '';

// 1. دریافت لیست کاربران
if ($action == 'get_users_list') {
    $sql = "SELECT users.id, users.name, users.profile_pic, 
            (SELECT COUNT(*) FROM messages WHERE user_id = users.id AND sender = 'user' AND is_read = 0) as unread
            FROM users 
            WHERE users.id IN (SELECT DISTINCT user_id FROM messages)
            ORDER BY (SELECT MAX(created_at) FROM messages WHERE user_id = users.id) DESC";
    $result = $conn->query($sql);
    $users = [];
    if ($result) while($row = $result->fetch_assoc()) $users[] = $row;
    echo json_encode($users);
}

// 2. دریافت پیام‌ها
if ($action == 'get_conversation') {
    $user_id = $_POST['user_id'];
    $conn->query("UPDATE messages SET is_read = 1 WHERE user_id = $user_id AND sender = 'user'");
    $result = $conn->query("SELECT * FROM messages WHERE user_id = $user_id ORDER BY created_at ASC");
    $messages = [];
    if ($result) while($row = $result->fetch_assoc()) { $row['created_at'] = date('H:i', strtotime($row['created_at'])); $messages[] = $row; }
    echo json_encode($messages);
}

// 3. ارسال پاسخ ادمین (+ ارسال ایمیل)
if ($action == 'admin_reply') {
    $user_id = $_POST['user_id'];
    $message = cleanInput($_POST['message']);
    
    if(!empty($message)) {
        $sql = "INSERT INTO messages (user_id, sender, message, is_read, created_at) VALUES ('$user_id', 'admin', '$message', 0, NOW())"; // is_read=0 برای کاربر
        if($conn->query($sql)) {
            // --- ارسال ایمیل ---
            $u_res = $conn->query("SELECT email FROM users WHERE id = $user_id");
            if($u_res->num_rows > 0) {
                $user_email = $u_res->fetch_assoc()['email'];
                $subject = "پاسخ جدید از پشتیبانی بوتیک پاریس";
                $headers = "From: support@pizzaparis.xo.je\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                $msg_body = "<div style='direction:rtl;font-family:tahoma;'><h2>پاسخ پشتیبانی:</h2><p>$message</p><a href='https://pizzaparis.xo.je'>مشاهده در سایت</a></div>";
                // تابع mail ممکن است در هاست رایگان محدود باشد
                @mail($user_email, $subject, $msg_body, $headers); 
            }
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }
}

// 4. چک کردن پیام جدید برای کاربر (برای نوتیفیکیشن)
if ($action == 'check_new_msg' && isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $last_id = $_POST['last_id'];
    
    // آیا پیام جدیدی از ادمین آمده که آی‌دی آن بزرگتر از آخرین پیام دیده شده باشد؟
    $sql = "SELECT * FROM messages WHERE user_id = $uid AND sender = 'admin' AND id > $last_id ORDER BY id DESC LIMIT 1";
    $res = $conn->query($sql);
    
    if($res->num_rows > 0) {
        $msg = $res->fetch_assoc();
        echo json_encode(['status' => 'new_msg', 'message' => $msg['message'], 'last_id' => $msg['id']]);
    } else {
        echo json_encode(['status' => 'no_new']);
    }
}
?>