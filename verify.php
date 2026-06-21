<?php
session_start();
include 'db.php';
include 'header.php';

// **************************************************
// تنظیمات درگاه پرداخت
$merchant_id = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"; 
// **************************************************

$authority = isset($_GET['Authority']) ? $_GET['Authority'] : null;
$status = isset($_GET['Status']) ? $_GET['Status'] : null;

if (!$authority) {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}
?>

<style>
    body { background-color: #111827; color: #f3f4f6; }
    
    #result-container {
        min-height: 80vh;
        display: flex; flex-direction: column; justify-content: center; align-items: center;
        padding: 20px;
    }
    
    .result-box {
        background: #1f2937;
        border: 1px solid #374151;
        border-top: 4px solid #d4af37;
        padding: 30px;
        border-radius: 24px; width: 100%; max-width: 500px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        text-align: center;
        animation: fadeIn 0.5s ease-out;
    }
    
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    
    .btn-home {
        background: linear-gradient(135deg, #d4af37 0%, #b4932a 100%);
        color: #111827; padding: 12px 30px; border-radius: 12px;
        margin-top: 25px; display: inline-block; transition: 0.3s; 
        font-weight: bold; text-decoration: none;
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
    }
    .btn-home:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(212, 175, 55, 0.5); color: black; }

    .icon-box {
        width: 70px; height: 70px; margin: 0 auto 20px auto;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 35px;
    }
    .icon-success { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 2px solid #10b981; }
    .icon-error { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 2px solid #ef4444; }

    /* استایل فاکتور */
    .invoice-list {
        background: rgba(0,0,0,0.2);
        border-radius: 12px;
        padding: 15px;
        margin-top: 20px;
        text-align: right;
        border: 1px solid #374151;
    }
    .invoice-row {
        display: flex; justify-content: space-between;
        border-bottom: 1px dashed #4b5563;
        padding: 8px 0;
        font-size: 0.9rem;
        color: #d1d5db;
    }
    .invoice-row:last-child { border-bottom: none; }
    .item-title { color: white; font-weight: bold; font-size: 0.95rem; }
    .item-detail { font-size: 0.8rem; color: #9ca3af; margin-right: 5px; }
</style>

<div id="result-container">
<?php

if ($status == 'OK') {
    // 1. یافتن سفارش
    $sql = "SELECT * FROM orders WHERE authority = '$authority'";
    $res = $conn->query($sql);

    if ($res->num_rows > 0) {
        $order = $res->fetch_assoc();
        $amount = $order['amount'];
        $order_id = $order['id'];

        // 2. تایید تراکنش با زرین‌پال
        $data = array("merchant_id" => $merchant_id, "authority" => $authority, "amount" => $amount);
        $jsonData = json_encode($data);
        
        $ch = curl_init('https://payment.zarinpal.com/pg/v4/payment/verify.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($jsonData)));
        
        $result = curl_exec($ch);
        $err = curl_error($ch);
        $result = json_decode($result, true);
        curl_close($ch);

        if ($err) {
             showError("خطا در اتصال به بانک", "cURL Error: $err");
        } elseif (isset($result['data']['code']) && ($result['data']['code'] == 100 || $result['data']['code'] == 101)) {
            
            // *** پرداخت موفق ***
            $ref_id = $result['data']['ref_id'];
            
            // آپدیت وضعیت سفارش
            $payment_msg = "پرداخت موفق | کد پیگیری: $ref_id";
            $conn->query("UPDATE orders SET status='paid', payment_status='$payment_msg', ref_id='$ref_id' WHERE id=$order_id");

            // ساخت لیست اقلام برای نمایش
            $items_html = "";
            $details = !empty($order['order_details_json']) ? json_decode($order['order_details_json'], true) : null;
            
            if ($details && is_array($details)) {
                foreach($details as $item) {
                    $iName = isset($item['name']) ? $item['name'] : 'محصول';
                    $iColor = isset($item['color']) && $item['color'] !== '-' ? $item['color'] : '';
                    $iQty = isset($item['qty']) ? $item['qty'] : 1;
                    
                    $colorSpan = $iColor ? "<span class='item-detail'>(رنگ: $iColor)</span>" : "";
                    
                    $items_html .= "
                    <div class='invoice-row'>
                        <div>
                            <span class='item-title'>$iName</span>
                            $colorSpan
                        </div>
                        <div class='text-[#d4af37] font-mono'>x$iQty</div>
                    </div>";
                }
            } else {
                $items_html = "<div class='invoice-row'><div>خرید کلی</div></div>";
            }

            // نمایش باکس موفقیت
            echo "
            <div class='result-box'>
                <div class='icon-box icon-success'>✓</div>
                <h1 style='color: white; margin:0; font-size: 1.5rem;'>پرداخت موفقیت‌آمیز بود!</h1>
                <p style='color: #d4af37; margin-top: 10px; font-size: 0.9rem; font-family: monospace;'>کد رهگیری: $ref_id</p>
                
                <div class='invoice-list'>
                    <div style='font-size: 0.8rem; color: #6b7280; margin-bottom: 10px; text-align: center;'>جزئیات سفارش</div>
                    $items_html
                    <div class='invoice-row' style='border-top: 1px solid #4b5563; margin-top: 10px; padding-top: 10px;'>
                        <span>جمع کل پرداخت شده</span>
                        <span style='color: #10b981; font-weight: bold;'>".number_format($amount)." تومان</span>
                    </div>
                </div>

                <p style='color: #9ca3af; font-size:0.8rem; margin-top:20px;'>
                    سفارش شما ثبت شد و به زودی پردازش و ارسال خواهد شد.
                </p>
                <a href='index.php' class='btn-home'>بازگشت به فروشگاه</a>
            </div>";

        } else {
            showError("تراکنش ناموفق", "کد خطا: " . (isset($result['errors']['code']) ? $result['errors']['code'] : 'نامشخص'));
        }

    } else {
        showError("سفارش یافت نشد", "شناسه پرداخت معتبر نیست.");
    }

} else {
    showError("لغو پرداخت", "عملیات پرداخت توسط شما لغو شد.");
}

function showError($title, $desc) {
    echo '
    <div class="result-box" style="border-top-color: #ef4444;">
        <div class="icon-box icon-error">✕</div>
        <h1 style="color: #ef4444; font-weight:bold;">'.$title.'</h1>
        <p style="color: #d1d5db; margin: 15px 0;">'.$desc.'</p>
        <a href="index.php" class="btn-home" style="background: #374151; color: white; box-shadow:none;">بازگشت به سایت</a>
    </div>';
}
?>
</div>
</body>
</html>