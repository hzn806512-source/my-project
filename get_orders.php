<?php
// نام فایل: get_orders.php
include 'db.php';

// تغییر مهم: فقط سفارشاتی که وضعیتشان 'paid' (پرداخت موفق) است نمایش داده شوند
// سفارشات ارسال شده (sent) و ناموفق (pending) نمایش داده نمی‌شوند
$sql = "SELECT orders.*, products.name as prod_name, products.image as prod_img 
        FROM orders 
        LEFT JOIN products ON orders.product_id = products.id 
        WHERE orders.status = 'paid' 
        ORDER BY created_at DESC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while($r = $result->fetch_assoc()) {
        
        // چون فقط پرداخت شده‌ها را می‌گیریم، نیازی به بررسی وضعیت پرداخت نیست اما برای اطمینان بج را نگه می‌داریم
        $status_badge = "<span class='bg-green-500/20 text-green-400 border border-green-500/50 px-2 py-1 rounded text-[10px] font-bold'>پرداخت موفق</span>";
        
        $user_id = isset($r['user_id']) ? $r['user_id'] : 0;
        $order_id = $r['id']; 
        $row_id = "order-row-" . $order_id;
        
        $clean_address = htmlspecialchars(str_replace(array("\r", "\n"), ' ', $r['address']), ENT_QUOTES);
        $prod_img = !empty($r['prod_img']) ? $r['prod_img'] : 'https://cdn-icons-png.flaticon.com/512/3159/3159614.png';

        // --- پردازش جزئیات سفارش (JSON) ---
        $details_html = "";
        $json_details = !empty($r['order_details_json']) ? json_decode($r['order_details_json'], true) : null;

        if ($json_details && is_array($json_details) && count($json_details) > 0) {
            foreach ($json_details as $item) {
                $raw_color = isset($item['color']) ? $item['color'] : '';
                $qty = isset($item['qty']) ? $item['qty'] : 1;
                $item_name = isset($item['name']) ? $item['name'] : $r['prod_name'];
                
                // تفکیک رنگ
                $color_name = $raw_color;
                $hex_code = "#ccc"; 
                
                if(strpos($raw_color, ':') !== false) {
                    $parts = explode(':', $raw_color);
                    $color_name = $parts[0];
                    $hex_code = $parts[1];
                } else {
                    $hex_code = translateColor($raw_color);
                }

                $color_dot = "";
                if($raw_color && $raw_color !== '-') {
                    $color_dot = "<span class='color-dot' style='background-color:$hex_code;' title='$color_name'></span>";
                }

                $details_html .= "
                <div class='flex items-center justify-between text-xs text-gray-300 mb-1 border-b border-gray-700/50 pb-1 last:border-0'>
                    <div class='flex items-center gap-1 truncate max-w-[150px]'>
                        <span class='text-white'>$item_name</span>
                        $color_dot <span class='text-gray-500 text-[10px]'>$color_name</span>
                    </div>
                    <span class='font-bold text-white bg-gray-700 px-1.5 rounded'>x$qty</span>
                </div>";
            }
        } else {
            $details_html = "<span class='text-gray-500 text-[10px]'>تعداد کل: {$r['quantity']}</span>";
        }

        echo "
        <tr id='$row_id' class='bg-[#1f2937] hover:bg-[#2d3748] transition border-b border-gray-700 last:border-0'>
            
            <td class='p-3 first:rounded-r-lg last:rounded-l-lg align-top'>
                <div class='flex items-center gap-3'>
                    <img src='$prod_img' class='w-12 h-12 rounded-lg object-cover border border-gray-600'>
                    <div>
                        <span class='font-bold text-gray-200 text-xs block truncate max-w-[120px]'>{$r['prod_name']}</span>
                        <span class='text-gray-500 text-[10px] font-mono block mt-1'>" . date('Y/m/d H:i', strtotime($r['created_at'])) . "</span>
                    </div>
                </div>
            </td>
            
            <td class='p-3 align-top'>
                <div class='bg-gray-900/50 p-2 rounded border border-gray-700 max-h-24 overflow-y-auto custom-scroll w-56'>
                    $details_html
                </div>
            </td>
            
            <td class='p-3 align-top'>
                <div class='font-bold text-gray-300 text-xs'>{$r['customer_name']}</div>
                <div class='text-[#d4af37] text-[10px] mt-1 font-mono'>{$r['phone']}</div>
            </td>
            
            <td class='p-3 align-top text-center'>
                <button onclick='showAddress(\"$clean_address\")' class='text-gray-400 hover:text-white transition text-xs border border-gray-600 px-2 py-1 rounded'>
                    📍 نمایش
                </button>
            </td>
            
            <td class='p-3 text-center align-top'>
                <span class='text-[#d4af37] font-bold text-xs font-mono'>" . number_format($r['amount']) . "</span>
            </td>
            
            <td class='p-3 text-center align-top'>$status_badge</td>
            
            <td class='p-3 text-center align-top first:rounded-r-lg last:rounded-l-lg'>
                <button onclick='notifyUser($user_id, $order_id, \"$row_id\")' class='bg-[#4f46e5] hover:bg-[#4338ca] hover:shadow-lg text-white px-4 py-2 rounded-lg text-xs font-bold transition shadow-sm flex items-center justify-center gap-1 mx-auto'>
                    ارسال و حذف
                </button>
            </td>
        </tr>";
    }
} else {
    echo '<tr><td colspan="7" class="text-center py-12 text-gray-500 text-sm bg-[#1f2937]/50 rounded border border-dashed border-gray-700">
        سفارش جدیدی برای پردازش وجود ندارد.<br>
        <span class="text-xs opacity-60">(سفارشات ناموفق نمایش داده نمی‌شوند)</span>
    </td></tr>';
}
?>