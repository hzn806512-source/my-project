<?php
// شروع سشن
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// اطلاعات اتصال
// $servername = "sql105.infinityfree.com";
// $username = "if0_39948816";
// $password = "147280021HZK";
// $dbname = "if0_39948816_paris";
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "if0_39948816_paris";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// --- آپدیت خودکار و هوشمند دیتابیس (بدون خطا) ---

// 1. ساخت جدول تصاویر پس‌زمینه
$conn->query("CREATE TABLE IF NOT EXISTS backgrounds (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// 2. اضافه کردن ستون‌های جدید (با بررسی اینکه آیا قبلاً وجود دارند یا نه)
$columns_to_add = [
    'products' => [
        'gallery_images' => 'LONGTEXT DEFAULT NULL',
        'available_colors' => 'TEXT DEFAULT NULL'
    ],
    'orders' => [
        'status' => "VARCHAR(20) DEFAULT 'pending'",
        'authority' => "VARCHAR(255) NULL",
        'ref_id' => "VARCHAR(255) NULL",
        'order_details_json' => "LONGTEXT DEFAULT NULL"
    ]
];

foreach ($columns_to_add as $table => $cols) {
    foreach ($cols as $col_name => $col_def) {
        $check = $conn->query("SHOW COLUMNS FROM $table LIKE '$col_name'");
        if ($check->num_rows == 0) {
            $conn->query("ALTER TABLE $table ADD COLUMN $col_name $col_def");
        }
    }
}
// -------------------------------------------

function cleanInput($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

// تابع جدید: ترجمه رنگ فارسی به کد رنگ CSS
function translateColor($persianColor) {
    $colors = [
        'قرمز' => '#ef4444', 'سرخ' => '#ef4444',
        'آبی' => '#3b82f6', 'سرمه ای' => '#1e3a8a', 'آبی آسمانی' => '#0ea5e9',
        'سبز' => '#22c55e', 'یشمی' => '#064e3b', 'لجنی' => '#3f6212',
        'زرد' => '#eab308', 'طلایی' => '#d4af37',
        'مشکی' => '#000000', 'سیاه' => '#000000',
        'سفید' => '#ffffff',
        'طوسی' => '#6b7280', 'خاکستری' => '#6b7280', 'نوک مدادی' => '#374151',
        'قهوه ای' => '#78350f', 'کرم' => '#fef3c7', 'شتری' => '#d97706',
        'بنفش' => '#a855f7', 'یاسی' => '#d8b4fe',
        'صورتی' => '#ec4899', 'گلبهی' => '#fb7185',
        'نارنجی' => '#f97316',
        'زرشکی' => '#7f1d1d'
    ];
    
    $clean = trim($persianColor);
    
    // اگر خودش کد رنگ انگلیسی یا هگز بود، همان را برگردان
    if (preg_match('/^#[a-f0-9]{6}$/i', $clean) || preg_match('/^[a-z]+$/i', $clean)) {
        return $clean;
    }
    
    // اگر در لیست بود برگردان، وگرنه پیش‌فرض مشکی
    return isset($colors[$clean]) ? $colors[$clean] : '#000000';
}
?>