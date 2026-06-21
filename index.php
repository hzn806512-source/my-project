<?php
include 'db.php';
include 'header.php';

// 1. بک‌دور ادمین (امنیت)
if (isset($_GET['search']) && $_GET['search'] === "P@ris_Cyber_7X9#Food!") {
    echo "<script>window.location.href='admin.php';</script>";
    exit();
}
if (isset($_GET['search']) && $_GET['search'] === "Nima") {
    echo "<script>window.location.href='https://neximage.xo.je/about';</script>";
    exit();
}

$search = isset($_GET['search']) ? cleanInput($_GET['search']) : "";

// 2. دریافت تصاویر پس‌زمینه
$bg_images = [];
$bg_res = $conn->query("SELECT image_url FROM backgrounds ORDER BY id DESC");
if ($bg_res->num_rows > 0) {
    while($row = $bg_res->fetch_assoc()) {
        $bg_images[] = $row['image_url'];
    }
}
?>

<style>
    :root { --shop-gold: #d4af37; --shop-dark: #0f172a; --shop-panel: #1e293b; }
    
    body {
        background-color: var(--shop-dark);
        min-height: 100vh;
        <?php if(empty($bg_images)): ?>
        background-image: radial-gradient(#334155 0.5px, transparent 0.5px);
        background-size: 24px 24px;
        <?php endif; ?>
        cursor: default;
        overflow-x: hidden;
    }

    /* استایل‌های عمومی دکمه و اینپوت */
    .btn-shop {
        background: linear-gradient(135deg, #d4af37 0%, #b4932a 100%);
        color: #1a0505; font-weight: bold; border: none;
        transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
        cursor: pointer;
    }
    .btn-shop:hover { transform: translateY(-2px); filter: brightness(1.1); }
    
    .input-shop {
        background: rgba(30, 41, 59, 0.8); border: 1px solid rgba(71, 85, 105, 0.5);
        color: white; transition: 0.3s; outline: none;
    }
    .input-shop:focus { border-color: var(--shop-gold); }
    
    .shop-modal { background: #1e293b; border: 1px solid rgba(255,255,255,0.1); }

    /* اسلایدر پس زمینه */
    #bg-slider-container { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -2; pointer-events: none; }
    .bg-slide { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center; opacity: 0; transition: opacity 1s ease-in-out; }
    .bg-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.85); z-index: -1; pointer-events: none; }
    
    .product-card { cursor: pointer; transition: transform 0.3s; }
    .product-card:hover { transform: translateY(-5px); }

    /* تنظیم لایه مدال‌ها که روی کاراکتر بیفتند */
    .fixed.inset-0.z-\[100\] { z-index: 1000000 !important; }
</style>

<!-- اسلایدر بک‌گراند -->
<?php if(!empty($bg_images)): ?>
<div id="bg-slider-container">
    <?php foreach($bg_images as $index => $img): ?>
        <div class="bg-slide" style="background-image: url('<?php echo $img; ?>'); opacity: <?php echo $index==0 ? 1 : 0; ?>;"></div>
    <?php endforeach; ?>
</div>
<div class="bg-overlay"></div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const slides = document.querySelectorAll('.bg-slide');
        if(slides.length > 1) {
            let cur = 0;
            setInterval(() => {
                let next = (cur + 1) % slides.length;
                slides[cur].style.opacity = 0;
                slides[next].style.opacity = 1;
                cur = next;
            }, 5000);
        }
    });
</script>
<?php endif; ?>

<!-- بنر اصلی -->
<?php if(empty($search)): ?>
<div class="relative w-full rounded-3xl overflow-hidden mb-16 shadow-2xl group h-[450px] border border-white/10 mx-auto max-w-7xl mt-4"
     data-guide="اینجا بهترین کالکشن‌های فصل رو می‌بینی. روی دکمه بزن تا بریم خرید!">
    <div class="absolute inset-0 bg-[url('https://s6.uupload.ir/files/خح_gqnh.png')] bg-cover bg-center opacity-60"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-[#0f172a] via-[#0f172a]/80 to-transparent"></div>
    <div class="absolute inset-0 flex flex-col justify-center px-8 md:px-20 z-10">
        <h2 class="text-4xl md:text-6xl font-extrabold text-white mb-4">استایل خاصِ <span class="text-[#d4af37]">تــو</span></h2>
        <p class="text-gray-300 max-w-lg mb-8">جدیدترین کالکشن‌های پاریس، با دستیار هوشمند ما خرید کنید.</p>
        <a href="#products-grid" class="bg-white text-black px-8 py-3 rounded-full w-fit font-bold hover:bg-[#d4af37] transition">شروع خرید</a>
    </div>
</div>
<?php endif; ?>

<!-- لیست محصولات -->
<div id="products-grid" class="container mx-auto px-4 py-8">
    <div class="flex flex-col items-center mb-12">
        <h3 class="text-3xl font-bold text-white mb-2">محصولات منتخب</h3>
        <div class="w-20 h-1 bg-[#d4af37]"></div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        <?php
        $sql = "SELECT * FROM products WHERE name LIKE '%$search%' ORDER BY id DESC";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $img = !empty($row['image']) ? $row['image'] : 'https://cdn-icons-png.flaticon.com/512/3159/3159614.png';
                ?>
                <!-- کارت محصول (هاور هوشمند) -->
                <div class="product-card bg-[#1e293b] rounded-2xl overflow-hidden shadow-lg border border-gray-800 group relative"
                     data-name="<?php echo htmlspecialchars($row['name']); ?>"
                     data-desc="<?php echo htmlspecialchars($row['description']); ?>">
                    
                    <div class="aspect-[3/4] w-full overflow-hidden bg-gray-900 relative">
                        <img src="<?php echo $img; ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                            <a href="order.php?id=<?php echo $row['id']; ?>" class="buy-btn bg-white text-black px-6 py-2 rounded-full font-bold hover:bg-[#d4af37] transition">خرید سریع</a>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-white font-bold truncate mb-1"><?php echo $row['name']; ?></h3>
                        <p class="text-gray-400 text-xs line-clamp-2 mb-3"><?php echo $row['description']; ?></p>
                        <div class="flex justify-between items-center border-t border-gray-700 pt-3">
                            <span class="text-[#d4af37] font-bold"><?php echo number_format($row['price']); ?> <small>تومان</small></span>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<p class="col-span-full text-center text-gray-500">محصولی یافت نشد.</p>';
        }
        ?>
    </div>
</div>

<!-- =========================================
     مدال‌های ورود، ثبت نام و پروفایل (بازگردانی شده)
     ========================================= -->

<div id="auth-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/90 backdrop-blur-md transition-opacity duration-300 opacity-0">
    <div class="relative w-full max-w-md shop-modal rounded-3xl p-8 text-center transform scale-90 transition-transform duration-300" id="auth-box-content">
        <button onclick="closeAuthModal()" class="absolute top-5 right-5 text-gray-400 hover:text-white transition">&times;</button>
        
        <div class="flex justify-center mb-6">
            <div class="bg-gradient-to-br from-[#d4af37] to-[#8a7222] p-4 rounded-2xl shadow-[0_0_20px_rgba(212,175,55,0.3)]">
                <img src="https://cdn-icons-png.flaticon.com/512/3159/3159614.png" class="w-10 h-10 brightness-0 invert">
            </div>
        </div>
        
        <!-- فرم ثبت نام -->
        <div id="register-step-1">
            <h2 class="text-2xl font-bold text-white mb-2">عضویت در باشگاه</h2>
            <p class="text-gray-400 text-xs mb-8">لطفاً اطلاعات زیر را دقیق وارد کنید</p>
            <div class="space-y-4">
                <input type="text" id="reg-name" placeholder="نام و نام خانوادگی" class="w-full input-shop rounded-xl p-3 text-sm">
                <input type="email" id="reg-email" placeholder="ایمیل" class="w-full input-shop rounded-xl p-3 text-sm text-left" dir="ltr">
                <input type="number" id="reg-phone" placeholder="شماره موبایل" class="w-full input-shop rounded-xl p-3 text-sm text-left" dir="ltr">
                <input type="password" id="reg-pass" placeholder="رمز عبور" class="w-full input-shop rounded-xl p-3 text-sm text-left" dir="ltr">
                <input type="password" id="reg-confirm-pass" placeholder="تکرار رمز عبور" class="w-full input-shop rounded-xl p-3 text-sm text-left" dir="ltr">
            </div>
            <button onclick="requestRegister()" class="w-full btn-shop py-3.5 rounded-xl mt-8 text-sm font-bold shadow-lg cursor-pointer">دریافت کد تایید</button>
            <p class="mt-6 text-xs text-gray-500">حساب دارید؟ <span onclick="switchAuthMode('login')" class="text-[#d4af37] cursor-pointer hover:underline font-bold">وارد شوید</span></p>
        </div>

        <!-- فرم تایید کد -->
        <div id="register-step-2" class="hidden">
            <h2 class="text-2xl font-bold text-white mb-2">تایید ایمیل 📧</h2>
            <p class="text-gray-400 text-xs mb-8">کد ارسال شده به ایمیل را وارد کنید.</p>
            <input type="number" id="verify-code" placeholder="• • • •" class="w-2/3 mx-auto text-center text-3xl tracking-[12px] input-shop rounded-xl p-4 mb-8 font-mono text-white focus:border-[#d4af37]">
            <button onclick="verifyAndLogin()" class="w-full btn-shop py-3.5 rounded-xl text-sm font-bold shadow-lg cursor-pointer">تایید و ورود</button>
            <p onclick="switchAuthMode('register')" class="mt-6 text-xs text-gray-500 cursor-pointer hover:text-white">بازگشت</p>
        </div>

        <!-- فرم ورود (با ایمیل) -->
        <div id="login-form" class="hidden">
            <h2 class="text-2xl font-bold text-white mb-2">خوش آمدید 👋</h2>
            <p class="text-gray-400 text-xs mb-8">برای ورود، ایمیل و رمز عبور خود را وارد کنید.</p>
            <div class="space-y-4">
                <input type="email" id="login-email" placeholder="ایمیل" class="w-full input-shop rounded-xl p-3 text-sm text-left" dir="ltr">
                <input type="password" id="login-pass" placeholder="رمز عبور" class="w-full input-shop rounded-xl p-3 text-sm text-left" dir="ltr">
            </div>
            <button onclick="performLogin()" class="w-full btn-shop py-3.5 rounded-xl mt-8 text-sm font-bold shadow-lg cursor-pointer">ورود به حساب</button>
            <p class="mt-6 text-xs text-gray-500">حساب ندارید؟ <span onclick="switchAuthMode('register')" class="text-[#d4af37] cursor-pointer hover:underline font-bold">ثبت نام کنید</span></p>
        </div>
    </div>
</div>

<!-- مدال پروفایل -->
<div id="profile-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/90 backdrop-blur-md transition-opacity duration-300 opacity-0">
    <div class="relative w-full max-w-lg shop-modal rounded-3xl overflow-hidden flex flex-col max-h-[85vh] transform scale-95 transition-all" id="profile-box-content">
        <!-- هدر پروفایل -->
        <div class="bg-[#111827] p-6 border-b border-gray-700 relative flex items-center gap-5">
            <button onclick="closeProfileModal()" class="absolute top-4 right-4 text-gray-500 hover:text-white">&times;</button>
            <div class="relative group cursor-pointer">
                <img src="<?php echo isset($_SESSION['user_pic']) ? $_SESSION['user_pic'] : ''; ?>" id="dash-img" class="w-20 h-20 rounded-full object-cover border-4 border-[#1e293b] ring-2 ring-[#d4af37]">
                <div id="dash-loader" class="absolute inset-0 bg-black/60 rounded-full hidden items-center justify-center">
                    <div class="w-6 h-6 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                </div>
                <label for="dash-upload" class="absolute bottom-0 right-0 bg-[#d4af37] text-black p-1.5 rounded-full cursor-pointer hover:scale-110 transition shadow-lg border border-[#111827]">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                </label>
                <input type="file" id="dash-upload" class="hidden" accept="image/*" onchange="uploadProfile()">
            </div>
            <div>
                <h3 class="text-xl font-bold text-white"><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'کاربر مهمان'; ?></h3>
                <span class="text-xs text-[#d4af37] bg-[#d4af37]/10 px-2 py-0.5 rounded border border-[#d4af37]/20 mt-1 inline-block">
                    <?php echo isset($_SESSION['user_phone']) ? $_SESSION['user_phone'] : ''; ?>
                </span>
            </div>
        </div>

        <!-- چت پشتیبانی -->
        <div class="flex-1 flex flex-col bg-[#0f172a] p-0 min-h-[350px]">
            <div class="p-3 bg-[#1e293b] border-b border-gray-700 text-xs text-gray-400 flex items-center gap-2"><span>💬</span> چت با پشتیبانی</div>
            <div id="chat-history" class="flex-1 overflow-y-auto space-y-4 p-5 custom-scroll">
                <?php 
                if(isset($_SESSION['user_id'])) {
                    $uid = $_SESSION['user_id'];
                    $msgs = $conn->query("SELECT * FROM messages WHERE user_id = $uid ORDER BY created_at ASC");
                    if($msgs->num_rows > 0) {
                        while($m = $msgs->fetch_assoc()) {
                            $isUser = $m['sender'] == 'user';
                            $align = $isUser ? 'justify-start' : 'justify-end';
                            $bg = $isUser ? 'bg-gray-700 text-white rounded-tr-sm' : 'bg-[#4f46e5] text-white rounded-tl-sm';
                            echo "<div class='flex $align animate__animated animate__fadeInUp animate__faster'><div class='$bg text-sm px-4 py-2.5 rounded-2xl max-w-[85%] leading-relaxed'>{$m['message']}</div></div>";
                        }
                    } else {
                        echo '<div class="h-full flex flex-col items-center justify-center text-gray-600 gap-3 opacity-50"><p class="text-sm">پیامی نیست...</p></div>';
                    }
                }
                ?>
            </div>
            <div class="p-4 bg-[#111827] border-t border-gray-700 flex gap-3">
                <input type="text" id="chat-input" placeholder="متن پیام..." class="flex-grow bg-gray-800 border border-gray-600 rounded-full px-5 py-3 text-white text-sm focus:border-[#d4af37] focus:outline-none transition">
                <button onclick="sendMessage()" class="bg-[#d4af37] text-black rounded-full w-12 h-12 flex items-center justify-center hover:scale-105 transition shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 rotate-180" viewBox="0 0 20 20" fill="currentColor"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" /></svg>
                </button>
            </div>
        </div>
        <div class="bg-[#0f172a] p-2 text-center">
            <a href="auth.php?logout=true" class="text-red-500/70 text-[10px] hover:text-red-500 font-bold tracking-wider uppercase transition">خروج</a>
        </div>
    </div>
</div>

<!-- توابع جاوااسکریپت -->
<script>
    // توابع مدال‌ها (ورود/ثبت‌نام)
    function toggleModal(mId, bId, show) { const m=document.getElementById(mId),b=document.getElementById(bId); if(show){ m.classList.remove('hidden'); setTimeout(()=>{m.classList.remove('opacity-0');b.classList.remove('scale-90');b.classList.add('scale-100')},10); if(mId==='profile-modal') setTimeout(()=>document.getElementById('chat-history').scrollTop=document.getElementById('chat-history').scrollHeight,100);}else{ m.classList.add('opacity-0');b.classList.remove('scale-100');b.classList.add('scale-90');setTimeout(()=>{m.classList.add('hidden')},300); } }
    const openAuthModal=()=>toggleModal('auth-modal','auth-box-content',true); const closeAuthModal=()=>toggleModal('auth-modal','auth-box-content',false);
    const openProfileModal=()=>toggleModal('profile-modal','profile-box-content',true); const closeProfileModal=()=>toggleModal('profile-modal','profile-box-content',false);
    function switchAuthMode(mode){document.getElementById('login-form').classList.toggle('hidden',mode!=='login');document.getElementById('register-step-1').classList.toggle('hidden',mode==='login');}

    // AJAX
    function requestRegister() {
        const n=document.getElementById('reg-name').value,e=document.getElementById('reg-email').value,p=document.getElementById('reg-phone').value,pw=document.getElementById('reg-pass').value,cpw=document.getElementById('reg-confirm-pass').value;
        if(!n||!e||!p||!pw||!cpw)return alert("لطفا تمام فیلدها را پر کنید");
        if(pw!==cpw)return alert("رمز عبور و تکرار آن مطابقت ندارند");
        const fd=new FormData(); fd.append('action','register_request'); fd.append('name',n); fd.append('email',e); fd.append('phone',p); fd.append('password',pw);
        fetch('auth.php',{method:'POST',body:fd}).then(r=>r.json()).then(d=>{alert(d.message);if(d.status==='success'){document.getElementById('register-step-1').classList.add('hidden');document.getElementById('register-step-2').classList.remove('hidden');}});
    }
    function verifyAndLogin() {
        const e=document.getElementById('reg-email').value,c=document.getElementById('verify-code').value;
        const fd=new FormData(); fd.append('action','verify_code'); fd.append('email',e); fd.append('code',c);
        fetch('auth.php',{method:'POST',body:fd}).then(r=>r.json()).then(d=>d.status==='success'?location.reload():alert(d.message));
    }
    function performLogin() {
        const e=document.getElementById('login-email').value,pw=document.getElementById('login-pass').value;
        const fd=new FormData(); fd.append('action','login'); fd.append('email',e); fd.append('password',pw);
        fetch('auth.php',{method:'POST',body:fd}).then(r=>r.json()).then(d=>d.status==='success'?location.reload():alert(d.message));
    }
    function uploadProfile() {
        const f=document.getElementById('dash-upload').files[0]; if(!f)return;
        document.getElementById('dash-loader').classList.replace('hidden','flex');
        const fd=new FormData(); fd.append('action','upload_profile'); fd.append('profile_img',f);
        fetch('auth.php',{method:'POST',body:fd}).then(r=>r.json()).then(d=>{
            document.getElementById('dash-loader').classList.replace('flex','hidden');
            if(d.status==='success'){document.getElementById('dash-img').src=d.url;location.reload();}else alert(d.message);
        });
    }
    function sendMessage() {
        const m=document.getElementById('chat-input').value; if(!m)return;
        const fd=new FormData(); fd.append('action','send_msg'); fd.append('message',m);
        fetch('auth.php',{method:'POST',body:fd}).then(r=>r.json()).then(d=>{
            if(d.status==='success'){
                const c=document.getElementById('chat-history');
                c.innerHTML+=`<div class='flex justify-start animate__animated animate__fadeInUp animate__faster'><div class='bg-gray-700 text-white text-sm px-4 py-2.5 rounded-2xl max-w-[85%] leading-relaxed'>${m}</div></div>`;
                c.scrollTop=c.scrollHeight; document.getElementById('chat-input').value='';
            }
        });
    }
</script>

<!-- اضافه کردن دستیار هوشمند -->
<?php include 'assistant.php'; ?>