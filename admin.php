<?php
// تنظیمات سرور برای آپلود فایل‌های سنگین
set_time_limit(600);
ini_set('max_execution_time', 600);
ini_set('post_max_size', '64M');
ini_set('upload_max_filesize', '64M');

include 'db.php';
include 'header.php'; 

// ************************************************
// تنظیمات Cloudinary
$cloud_name = "dldqhucgq"; 
$api_key = "153d95586911eb57fa039b6b6a9d44da"; 
$api_secret = "8YN79Fqq7TpedAvwN-wnLoGsRsQ"; 
// ************************************************

// آمار پیام‌ها
$unread_count = 0;
$res_count = $conn->query("SELECT COUNT(DISTINCT user_id) as cnt FROM messages WHERE sender='user' AND is_read=0");
if($res_count) { $unread_count = $res_count->fetch_assoc()['cnt']; }

// متغیرهای محصول
$edit_mode = false;
$edit_id = 0;
$p_name = ""; $p_desc = ""; $p_price = ""; $p_shipping = 0;
$p_image = ""; $p_colors = ""; $p_gallery = "[]";

if (isset($_GET['edit_id'])) {
    $edit_mode = true;
    $edit_id = $_GET['edit_id'];
    $res = $conn->query("SELECT * FROM products WHERE id = $edit_id");
    if($res->num_rows > 0){
        $row = $res->fetch_assoc();
        $p_name = $row['name']; 
        $p_desc = $row['description']; 
        $p_price = $row['price'];
        $p_shipping = isset($row['shipping_cost']) ? $row['shipping_cost'] : 0;
        $p_image = $row['image'];
        $p_colors = $row['available_colors']; // لیست رنگ‌ها (ذخیره شده به صورت رشته)
        $p_gallery = $row['gallery_images'] ? $row['gallery_images'] : "[]";
    }
}

// تابع آپلود
function uploadSingleFile($fileArray, $manualUrl, $c_name, $a_key, $a_secret) {
    $final_path = "";
    if (isset($fileArray) && $fileArray['error'] == 0) {
        try {
            $timestamp = time();
            $str = "timestamp=" . $timestamp . $a_secret;
            $sig = sha1($str);
            $cfile = new CURLFile($fileArray['tmp_name']);
            $post = ['file' => $cfile, 'api_key' => $a_key, 'timestamp' => $timestamp, 'signature' => $sig];
            $ch = curl_init("https://api.cloudinary.com/v1_1/$c_name/image/upload");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $res = json_decode(curl_exec($ch), true);
            curl_close($ch);
            if (isset($res['secure_url'])) $final_path = $res['secure_url'];
        } catch (Exception $e) {}
        
        if (empty($final_path)) {
             $target_dir = "uploads/";
             if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
             $ext = pathinfo($fileArray['name'], PATHINFO_EXTENSION);
             $local = $target_dir . uniqid() . ".$ext";
             if(move_uploaded_file($fileArray['tmp_name'], $local)) $final_path = $local;
        }
    } 
    if(empty($final_path) && !empty($manualUrl)) $final_path = $manualUrl;
    return $final_path;
}

// پردازش فرم محصول
if (isset($_POST['save_product_btn'])) {
    $name = cleanInput($_POST['name']);
    $desc = cleanInput($_POST['description']);
    $price = cleanInput($_POST['price']);
    $shipping = cleanInput($_POST['shipping_cost']);
    $colors = cleanInput($_POST['final_colors_input']); // دریافت رنگ‌ها از اینپوت مخفی
    
    $final_image_path = $_POST['current_image_path'];
    $up_main = uploadSingleFile($_FILES['image_file'], $_POST['image_url_manual'], $cloud_name, $api_key, $api_secret);
    if(!empty($up_main)) $final_image_path = $up_main;
    if(empty($final_image_path)) $final_image_path = "https://cdn-icons-png.flaticon.com/512/3159/3159614.png";

    // پردازش گالری
    $gallery_json = $_POST['current_gallery_json'];
    $new_gallery_urls = [];
    if(isset($_FILES['gallery_files']) && count($_FILES['gallery_files']['name']) > 0 && $_FILES['gallery_files']['name'][0] != "") {
        $count = count($_FILES['gallery_files']['name']);
        for($i = 0; $i < $count; $i++) {
            $file_tmp = [
                'name' => $_FILES['gallery_files']['name'][$i],
                'type' => $_FILES['gallery_files']['type'][$i],
                'tmp_name' => $_FILES['gallery_files']['tmp_name'][$i],
                'error' => $_FILES['gallery_files']['error'][$i],
                'size' => $_FILES['gallery_files']['size'][$i]
            ];
            $url = uploadSingleFile($file_tmp, "", $cloud_name, $api_key, $api_secret);
            if(!empty($url)) $new_gallery_urls[] = $url;
        }
        // اگر عکس جدید آپلود شد، جایگزین قبلی شود
        if(!empty($new_gallery_urls)) {
            $gallery_json = json_encode($new_gallery_urls, JSON_UNESCAPED_UNICODE);
        }
    }

    if (isset($_POST['is_edit']) && $_POST['is_edit'] == 1) {
        $id_to_update = $_POST['target_id'];
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, shipping_cost=?, image=?, available_colors=?, gallery_images=? WHERE id=?");
        $stmt->bind_param("ssiisssi", $name, $desc, $price, $shipping, $final_image_path, $colors, $gallery_json, $id_to_update);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, shipping_cost, image, available_colors, gallery_images) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiisss", $name, $desc, $price, $shipping, $final_image_path, $colors, $gallery_json);
        $stmt->execute();
    }
    echo "<script>window.location.href='admin.php';</script>";
}

// پردازش بک‌گراند
if (isset($_POST['save_bg_btn'])) {
    $bg_path = uploadSingleFile($_FILES['bg_file'], $_POST['bg_url_manual'], $cloud_name, $api_key, $api_secret);
    if(!empty($bg_path)) { $conn->query("INSERT INTO backgrounds (image_url) VALUES ('$bg_path')"); echo "<script>window.location.href='admin.php';</script>"; }
}

// حذف‌ها
if (isset($_GET['delete_id'])) { $conn->query("DELETE FROM products WHERE id = ".$_GET['delete_id']); echo "<script>window.location.href='admin.php';</script>"; }
if (isset($_GET['delete_bg_id'])) { $conn->query("DELETE FROM backgrounds WHERE id = ".$_GET['delete_bg_id']); echo "<script>window.location.href='admin.php';</script>"; }
?>

<style>
    body { background-color: #111827 !important; color: #f3f4f6; }
    .glass-panel { background: #1f2937; border: 1px solid #374151; border-radius: 16px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); position: relative; z-index: 10; }
    .admin-input { width: 100%; padding: 12px; background: #111827; border: 1px solid #4b5563; color: white; border-radius: 8px; margin-top: 6px; transition: 0.2s; }
    .admin-input:focus { border-color: #d4af37; outline:none; ring: 2px solid #d4af37; }
    .admin-btn { width: 100%; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 12px; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .btn-save { background: linear-gradient(to right, #d4af37, #b4932a); color: #111827; }
    .btn-bg { background: linear-gradient(to right, #4f46e5, #4338ca); color: white; }
    .order-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
    .order-table th { color: #9ca3af; text-align: right; padding: 12px 16px; font-size: 0.85rem; }
    .color-dot { display: inline-block; width: 12px; height: 12px; border-radius: 50%; margin-left: 4px; border: 1px solid rgba(255,255,255,0.3); vertical-align: middle; }
    
    /* استایل‌های انتخاب رنگ جدید */
    .color-palette { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; background: #111827; padding: 10px; border-radius: 8px; border: 1px solid #374151; }
    .color-swatch { width: 30px; height: 30px; border-radius: 50%; cursor: pointer; border: 2px solid transparent; transition: 0.2s; }
    .color-swatch:hover { transform: scale(1.2); border-color: white; }
    .selected-colors-box { display: flex; flex-wrap: wrap; gap: 5px; min-height: 40px; background: #374151; padding: 8px; border-radius: 8px; margin-top: 5px; }
    .color-tag { background: #111827; color: white; padding: 4px 8px; border-radius: 20px; font-size: 11px; display: flex; items-center; gap: 5px; border: 1px solid #4b5563; }
    .color-tag span { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
    .color-tag i { cursor: pointer; color: #ef4444; font-style: normal; font-weight: bold; margin-right: 3px; }
    
    .address-modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.8); align-items: center; justify-content: center; backdrop-filter: blur(4px); }
    .address-content { background: #1f2937; border: 1px solid #4b5563; padding: 30px; border-radius: 20px; width: 90%; max-width: 500px; text-align: center; }
</style>

<div class="container mx-auto px-4 mt-8 mb-20 pb-20">
    
    <div class="glass-panel flex flex-wrap justify-between items-center gap-4 border-l-4 border-l-[#d4af37] mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-white">پنل مدیریت <span class="text-[#d4af37]">آلس</span> 👔</h1>
            <p class="text-gray-400 text-sm mt-1">نسخه 3.5</p>
        </div>
        <div class="flex gap-3">
            <a href="./admin_chat.php" class="bg-[#4f46e5] hover:bg-[#4338ca] text-white px-4 py-2 rounded-xl flex items-center gap-2 text-sm font-bold shadow-lg">
                💬 پیام‌ها <?php if($unread_count > 0): ?><span class="bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs"><?php echo $unread_count; ?></span><?php endif; ?>
            </a>
            <a href="index.php" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition text-sm font-bold shadow-lg">🏪 فروشگاه</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- فرم افزودن محصول -->
        <div class="flex flex-col gap-6 lg:sticky lg:top-4 h-fit z-20">
            <div class="glass-panel">
                <h3 class="text-[#d4af37] font-bold text-lg mb-4 border-b border-gray-700 pb-3">
                    <?php echo $edit_mode ? '✏️ ویرایش محصول' : '➕ افزودن لباس جدید'; ?>
                </h3>
                
                <form method="POST" enctype="multipart/form-data" id="productForm">
                    <?php if($edit_mode): ?>
                        <input type="hidden" name="is_edit" value="1">
                        <input type="hidden" name="target_id" value="<?php echo $edit_id; ?>">
                        <input type="hidden" name="current_image_path" value="<?php echo $p_image; ?>">
                        <input type="hidden" name="current_gallery_json" value='<?php echo $p_gallery; ?>'>
                    <?php else: ?>
                        <input type="hidden" name="current_gallery_json" value="[]">
                    <?php endif; ?>

                    <div>
                        <label class="text-gray-400 text-xs">نام محصول</label>
                        <input type="text" name="name" value="<?php echo $p_name; ?>" class="admin-input" required>
                    </div>

                    <div class="mt-3">
                        <label class="text-gray-400 text-xs">توضیحات</label>
                        <textarea name="description" rows="3" class="admin-input" required><?php echo $p_desc; ?></textarea>
                    </div>

                    <!-- ردیف قیمت -->
                    <div class="grid grid-cols-2 gap-2 mt-3">
                        <div>
                            <label class="text-gray-400 text-xs">قیمت (تومان)</label>
                            <input type="number" name="price" value="<?php echo $p_price; ?>" class="admin-input" required>
                        </div>
                        <div>
                            <label class="text-[#d4af37] text-xs font-bold">هزینه ارسال</label>
                            <input type="number" name="shipping_cost" value="<?php echo $p_shipping; ?>" class="admin-input" placeholder="0">
                        </div>
                    </div>

                    <!-- بخش جدید انتخاب رنگ -->
                    <div class="mt-4">
                        <label class="text-gray-400 text-xs font-bold block mb-2">رنگ‌های موجود (انتخاب کنید)</label>
                        
                        <!-- اینپوت مخفی برای ارسال به سرور -->
                        <input type="hidden" name="final_colors_input" id="final_colors_input" value="<?php echo $p_colors; ?>">
                        
                        <!-- نمایش رنگ‌های انتخاب شده -->
                        <div id="selected-colors-display" class="selected-colors-box">
                            <span class="text-xs text-gray-500 self-center w-full text-center" id="no-color-msg">رنگی انتخاب نشده</span>
                        </div>

                        <!-- پالت انتخاب -->
                        <div class="color-palette">
                            <!-- رنگ‌های پیش‌فرض -->
                            <div class="color-swatch" style="background: #000000;" title="مشکی" onclick="addColorTag('مشکی:#000000')"></div>
                            <div class="color-swatch" style="background: #ffffff;" title="سفید" onclick="addColorTag('سفید:#ffffff')"></div>
                            <div class="color-swatch" style="background: #ef4444;" title="قرمز" onclick="addColorTag('قرمز:#ef4444')"></div>
                            <div class="color-swatch" style="background: #3b82f6;" title="آبی" onclick="addColorTag('آبی:#3b82f6')"></div>
                            <div class="color-swatch" style="background: #22c55e;" title="سبز" onclick="addColorTag('سبز:#22c55e')"></div>
                            <div class="color-swatch" style="background: #eab308;" title="زرد" onclick="addColorTag('زرد:#eab308')"></div>
                            <div class="color-swatch" style="background: #a855f7;" title="بنفش" onclick="addColorTag('بنفش:#a855f7')"></div>
                            <div class="color-swatch" style="background: #ea580c;" title="نارنجی" onclick="addColorTag('نارنجی:#ea580c')"></div>
                            <div class="color-swatch" style="background: #ec4899;" title="صورتی" onclick="addColorTag('صورتی:#ec4899')"></div>
                            <div class="color-swatch" style="background: #6b7280;" title="طوسی" onclick="addColorTag('طوسی:#6b7280')"></div>
                            <div class="color-swatch" style="background: #d4af37;" title="طلایی" onclick="addColorTag('طلایی:#d4af37')"></div>
                            <div class="color-swatch" style="background: #78350f;" title="قهوه‌ای" onclick="addColorTag('قهوه‌ای:#78350f')"></div>
                            <div class="color-swatch" style="background: #1e3a8a;" title="سرمه‌ای" onclick="addColorTag('سرمه‌ای:#1e3a8a')"></div>
                            <div class="color-swatch" style="background: #fef3c7; border:1px solid #ccc" title="کرم" onclick="addColorTag('کرم:#fef3c7')"></div>
                        </div>

                        <!-- انتخاب رنگ خاص -->
                        <div class="mt-2 flex gap-2 items-center">
                            <input type="color" id="custom-color-picker" class="h-9 w-9 rounded cursor-pointer bg-transparent border-0 p-0">
                            <input type="text" id="custom-color-name" class="admin-input !mt-0 !py-1 text-xs" placeholder="نام رنگ (مثلاً صدفی)">
                            <button type="button" onclick="addCustomColor()" class="bg-gray-700 text-white px-3 py-1 rounded text-xs border border-gray-500 hover:bg-gray-600">افزودن</button>
                        </div>
                    </div>

                    <div class="mt-4 bg-gray-800/50 p-3 rounded-lg border border-dashed border-gray-600">
                        <label class="text-gray-400 text-xs block mb-2">1. تصویر اصلی</label>
                        <input type="file" name="image_file" class="block w-full text-xs text-gray-400">
                        <input type="text" name="image_url_manual" class="admin-input text-xs mt-2 text-center" placeholder="لینک مستقیم...">
                    </div>

                    <div class="mt-4 bg-gray-800/50 p-3 rounded-lg border border-dashed border-[#d4af37]/50">
                        <label class="text-[#d4af37] text-xs block mb-2 font-bold">2. گالری تصاویر</label>
                        <input type="file" name="gallery_files[]" multiple class="block w-full text-xs text-gray-400">
                        <p class="text-[10px] text-gray-500 mt-1">می‌توانید همزمان چند عکس انتخاب کنید.</p>
                    </div>

                    <button type="submit" name="save_product_btn" class="admin-btn btn-save mt-6">
                        <?php echo $edit_mode ? '💾 ذخیره تغییرات' : '🚀 انتشار محصول'; ?>
                    </button>
                    
                    <?php if($edit_mode): ?>
                        <a href="admin.php" class="block text-center text-gray-500 text-xs mt-3">انصراف</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- فرم بک‌گراند -->
            <div class="glass-panel border-t-4 border-t-[#4f46e5]">
                <h3 class="text-[#4f46e5] font-bold text-lg mb-4 pb-2 border-b border-gray-700">🖼️ بک‌گراند متحرک</h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="file" name="bg_file" class="block w-full text-xs text-gray-400">
                    <input type="text" name="bg_url_manual" class="admin-input text-xs mt-2 text-center" placeholder="لینک...">
                    <button type="submit" name="save_bg_btn" class="admin-btn btn-bg mt-4">افزودن به اسلایدر</button>
                </form>
                <div class="mt-4 grid grid-cols-4 gap-2">
                    <?php
                    $bgs = $conn->query("SELECT * FROM backgrounds ORDER BY id DESC");
                    while($bg = $bgs->fetch_assoc()) {
                        echo "<div class='relative group'><img src='{$bg['image_url']}' class='w-full h-10 object-cover rounded'><a href='admin.php?delete_bg_id={$bg['id']}' onclick='return confirm(\"حذف؟\")' class='absolute inset-0 bg-red-500/80 hidden group-hover:flex items-center justify-center text-white text-xs font-bold'>✕</a></div>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- لیست سفارشات و محصولات -->
        <div class="lg:col-span-2 space-y-10">
            <div class="glass-panel !p-0 overflow-hidden border border-gray-700 flex flex-col">
                <div class="p-4 bg-[#27303f] border-b border-gray-700 flex justify-between items-center">
                    <h3 class="text-white font-bold text-sm">📦 سفارشات (همه وضعیت‌ها)</h3>
                    <button onclick="fetchOrders()" class="text-[#d4af37] text-xs border border-[#d4af37] px-2 py-1 rounded hover:bg-[#d4af37] hover:text-black transition">بروزرسانی</button>
                </div>
                <div class="p-0 overflow-x-auto min-h-[200px] bg-[#111827]">
                    <table class="order-table w-full">
                        <thead class="bg-gray-800">
                            <tr>
                                <th>محصول</th>
                                <th>جزئیات / رنگ</th>
                                <th>خریدار</th>
                                <th>آدرس</th>
                                <th class="text-center">کل</th>
                                <th>وضعیت</th>
                                <th class="text-center">عملیات</th>
                            </tr>
                        </thead>
                        <tbody id="orders-table-body" class="divide-y divide-gray-800"></tbody>
                    </table>
                </div>
            </div>

            <div class="glass-panel mt-8">
                <h3 class="text-white font-bold mb-6 border-b border-gray-700 pb-2">👕 محصولات موجود</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    <?php
                    $prods = $conn->query("SELECT * FROM products ORDER BY id DESC");
                    if ($prods->num_rows > 0) {
                        while($p = $prods->fetch_assoc()){
                            $img = !empty($p['image']) ? $p['image'] : "https://cdn-icons-png.flaticon.com/512/3159/3159614.png";
                            $ship = number_format($p['shipping_cost']);
                            echo "
                            <div class='bg-[#27303f] p-3 rounded-lg border border-gray-700 flex flex-col items-center text-center group hover:border-gray-500 transition relative'>
                                <div class='relative w-full'>
                                    <img src='$img' class='w-full h-32 object-cover rounded mb-2 bg-gray-800'>
                                    <span class='absolute bottom-1 left-1 bg-black/70 text-[#d4af37] text-[10px] px-1.5 rounded'>ارسال: $ship ت</span>
                                </div>
                                <div class='text-white text-xs font-bold truncate w-full'>{$p['name']}</div>
                                <div class='text-[#d4af37] text-[10px] my-1'>".number_format($p['price'])." ت</div>
                                <div class='flex gap-1 w-full mt-auto pt-2'>
                                    <a href='admin.php?edit_id={$p['id']}' class='flex-1 bg-blue-600 text-white text-[10px] py-1 rounded'>ویرایش</a>
                                    <a href='admin.php?delete_id={$p['id']}' onclick='return confirm(\"حذف؟\")' class='flex-1 bg-red-600 text-white text-[10px] py-1 rounded'>حذف</a>
                                </div>
                            </div>";
                        }
                    } else {
                        echo '<p class="col-span-full text-center text-gray-500 text-sm py-4">لیست خالی است.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="addressModal" class="address-modal" onclick="closeAddressModal()">
    <div class="address-content" onclick="event.stopPropagation()">
        <span class="text-4xl mb-4 block">📍</span>
        <h2 class="text-xl text-white font-bold mb-4">آدرس مشتری</h2>
        <p id="modalAddressText" class="text-gray-300 text-sm leading-7 bg-[#111827] p-4 rounded-xl border border-gray-700 mb-6 text-right" dir="rtl"></p>
        <button onclick="closeAddressModal()" class="bg-[#d4af37] text-[#111827] px-6 py-2 rounded-lg w-full font-bold">بستن</button>
    </div>
</div>

<script>
    // --- منطق انتخاب رنگ ---
    let selectedColors = []; // آرایه برای نگهداری رنگ‌ها به فرمت "نام:کد"

    // لود کردن رنگ‌های قبلی در حالت ویرایش
    window.addEventListener('DOMContentLoaded', () => {
        const initial = document.getElementById('final_colors_input').value;
        if(initial) {
            // چون قبلا فقط نام بود، ممکنه فرمت قدیمی باشه. سعی میکنیم هندل کنیم.
            // اگر فرمت جدید باشه "قرمز:#f00,آبی:#00f"
            const parts = initial.split(',');
            parts.forEach(p => {
                p = p.trim();
                if(p) {
                    if(p.includes(':')) {
                        selectedColors.push(p); // فرمت جدید
                    } else {
                        // تبدیل فرمت قدیمی به جدید (رنگ پیش فرض مشکی)
                        selectedColors.push(p + ":#000000"); 
                    }
                }
            });
            renderColors();
        }
    });

    function addColorTag(colorString) {
        // colorString باید به فرمت "نام:کد" باشد
        if(!selectedColors.includes(colorString)) {
            selectedColors.push(colorString);
            renderColors();
        }
    }

    function addCustomColor() {
        const hex = document.getElementById('custom-color-picker').value;
        const name = document.getElementById('custom-color-name').value.trim();
        if(name) {
            addColorTag(`${name}:${hex}`);
            document.getElementById('custom-color-name').value = '';
        } else {
            alert('لطفاً نام رنگ را بنویسید');
        }
    }

    function removeColor(index) {
        selectedColors.splice(index, 1);
        renderColors();
    }

    function renderColors() {
        const container = document.getElementById('selected-colors-display');
        const input = document.getElementById('final_colors_input');
        container.innerHTML = '';
        
        if(selectedColors.length === 0) {
            container.innerHTML = '<span class="text-xs text-gray-500 self-center w-full text-center">رنگی انتخاب نشده</span>';
        }

        selectedColors.forEach((c, i) => {
            const [name, hex] = c.split(':');
            // اگر کد رنگ نبود (برای سازگاری)
            const bg = hex ? hex : '#000'; 
            
            container.innerHTML += `
            <div class="color-tag">
                <span style="background:${bg}"></span>
                ${name}
                <i onclick="removeColor(${i})">×</i>
            </div>`;
        });

        input.value = selectedColors.join(',');
    }

    // --- سایر توابع ---
    function fetchOrders() {
        const tbody = document.getElementById('orders-table-body');
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-gray-500 py-10 text-sm">درحال بارگذاری...</td></tr>';
        fetch('get_orders.php').then(r => r.text()).then(html => tbody.innerHTML = html);
    }
    function notifyUser(uId, oId, rId) {
        if(!confirm("ارسال شود؟")) return;
        const row = document.getElementById(rId); if(row) row.style.opacity = '0.5';
        const fd = new FormData(); fd.append('action', 'notify_delivery'); fd.append('user_id', uId); fd.append('order_id', oId);
        fetch('auth.php', { method: 'POST', body: fd }).then(r => r.json()).then(d => {
            if(d.status === 'success') { if(row) row.remove(); const tb = document.getElementById('orders-table-body'); if(tb.children.length === 0) fetchOrders(); }
            else { alert("Error: " + d.message); if(row) row.style.opacity = '1'; }
        });
    }
    function showAddress(addr) { document.getElementById('modalAddressText').innerText = addr; document.getElementById('addressModal').style.display = 'flex'; }
    function closeAddressModal() { document.getElementById('addressModal').style.display = 'none'; }
    fetchOrders();
</script>