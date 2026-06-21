<?php
// اطمینان از شروع سشن
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/3159/3159614.png" type="image/png">
    <title>بوتیک پاریس | Paris Boutique</title>
    
    <!-- 1. Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- 2. فونت فارسی وزیر -->
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font/dist/font-face.css" rel="stylesheet" type="text/css" />

    <!-- ============================================================
         3. کتابخانه‌های انیمیشن (طبق درخواست شما)
    ============================================================ -->
    
    <!-- AOS CSS (Animate On Scroll) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- GSAP (GreenSock Animation Platform) - برای انیمیشن‌های حرفه‌ای -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Typed.js - برای افکت تایپ خودکار -->
    <script src="https://unpkg.com/typed.js@2.0.16/dist/typed.umd.js"></script>

    <!-- تنظیمات تم -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'shop-gold': '#d4af37',       
                        'shop-dark': '#111827',       
                        'shop-panel': '#1f2937',      
                        'shop-accent': '#4f46e5',     
                    },
                    fontFamily: {
                        'vazir': ['Vazir', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #111827;
            color: #f3f4f6;
            font-family: 'Vazir', sans-serif;
            overflow-x: hidden; 
        }

        /* هدر شیشه‌ای و مدرن */
        header {
            background: rgba(17, 24, 39, 0.85);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 30px rgba(0,0,0,0.3);
        }

        .nav-btn {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #d1d5db;
            border-radius: 12px;
            transition: border-color 0.3s ease;
        }
        
        .search-box-header {
            background: rgba(31, 41, 55, 0.6);
            border: 1px solid rgba(75, 85, 99, 0.4);
            border-radius: 14px;
            transition: 0.3s;
        }
        
        /* استایل نشانگر تایپ Typed.js */
        .typed-cursor {
            color: #d4af37;
            font-size: 1.2rem;
            opacity: 0.7;
        }
    </style>
</head>
<body>

<!-- هدر چسبان -->
<header class="sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3 flex flex-col md:flex-row justify-between items-center gap-4">
        
        <!-- لوگو (با انیمیشن GSAP) -->
        <a href="index.php" class="logo-wrapper flex items-center gap-3 cursor-pointer text-decoration-none opacity-0">
            <div class="logo-icon bg-gradient-to-br from-[#d4af37] to-[#b4932a] p-2 rounded-xl shadow-lg">
                <img src="https://cdn-icons-png.flaticon.com/512/3159/3159614.png" width="24" class="brightness-0 invert">
            </div>
            <div class="flex flex-col">
                <h1 class="text-xl font-bold tracking-wider m-0 leading-none text-white">آلس <span class="text-shop-gold">اسپورت</span></h1>
                <span class="text-[10px] text-gray-500 tracking-[0.2em] uppercase mt-1">لوکس و سبک</span>
            </div>
        </a>

        <!-- باکس جستجو (با انیمیشن Typed.js) -->
        <div class="search-box-header flex-grow w-full md:w-auto max-w-lg mx-4 px-4 py-2 flex items-center gap-2">
            <span class="text-gray-500 text-lg">🔍</span>
            <form action="index.php" method="GET" class="w-full m-0 flex items-center">
                <!-- خاصیت placeholder توسط Typed.js پر می‌شود -->
                <input type="text" id="search-input" name="search" autocomplete="off"
                       class="w-full bg-transparent text-white outline-none text-sm font-medium h-full">
            </form>
        </div>

        <!-- پنل کاربری -->
        <div class="nav-items-container flex flex-wrap items-center justify-center gap-3">
            
            <div class="hidden lg:flex items-center gap-2 text-gray-400 text-xs font-mono border-l border-gray-700 pl-4 ml-1">
                <span id="clock" class="tracking-widest">00:00</span>
            </div>

            <?php if(isset($_SESSION['user_id'])): ?>
                <button onclick="openProfileModal()" class="nav-btn px-3 py-1.5 flex items-center gap-3 group gs-hover-btn">
                    <img src="<?php echo !empty($_SESSION['user_pic']) ? $_SESSION['user_pic'] : 'https://cdn.jsdelivr.net/gh/microsoft/fluentui-emoji@latest/assets/Person/3D/person_3d.png'; ?>" 
                         class="w-8 h-8 rounded-full border border-gray-600 object-cover group-hover:border-shop-gold transition-colors">
                    <span class="text-sm font-bold truncate max-w-[80px]"><?php echo $_SESSION['user_name']; ?></span>
                </button>
            <?php else: ?>
                <button onclick="openAuthModal()" class="bg-[#d4af37] hover:bg-[#c5a028] text-black px-6 py-2 rounded-xl font-bold shadow-[0_4px_15px_rgba(212,175,55,0.2)] flex items-center gap-2 text-sm gs-hover-btn">
                    <span>👤</span> ورود / عضویت
                </button>
            <?php endif; ?>

            <?php if(isset($_GET['admin']) || strpos($_SERVER['REQUEST_URI'], 'admin.php') !== false): ?>
                 <a href="admin.php" class="bg-gray-800 text-gray-400 text-[10px] px-2 py-1 rounded hover:bg-gray-700 hover:text-white transition">پنل</a>
            <?php endif; ?>
        </div>

    </div>
</header>

<!-- اسکریپت‌های فعال‌سازی کتابخانه‌ها -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. فعال‌سازی AOS (برای اسکرول)
        AOS.init({
            duration: 800,        // مدت زمان انیمیشن
            easing: 'ease-out-cubic',
            once: true,           // انیمیشن فقط یکبار اجرا شود
            offset: 60            // فاصله از پایین صفحه برای شروع
        });

        // 2. فعال‌سازی Typed.js (برای جستجو)
        if(document.getElementById('search-input')) {
            new Typed('#search-input', {
                strings: [
                    'جستجو در کالکشن...', 
                    'کت و شلوار مردانه...', 
                    'کفش‌های چرم ایتالیایی...', 
                    'اکسسوری و ساعت...'
                ],
                typeSpeed: 60,      // سرعت تایپ
                backSpeed: 30,      // سرعت پاک کردن
                backDelay: 2000,    // وقفه قبل از پاک کردن
                startDelay: 500,
                attr: 'placeholder', // متن داخل placeholder تایپ شود
                bindInputFocusEvents: true,
                loop: true
            });
        }

        // 3. فعال‌سازی GSAP (انیمیشن‌های ورودی)
        
        // لوگو: از بالا به پایین با حالت فنری
        gsap.to(".logo-wrapper", {
            duration: 1.5,
            opacity: 1,
            y: 0,
            from: { y: -50 },
            ease: "elastic.out(1, 0.5)"
        });

        // آیکون لوگو: چرخش نامحدود و آرام
        gsap.to(".logo-icon", {
            rotation: 360,
            duration: 20,
            repeat: -1,
            ease: "linear"
        });

        // دکمه‌های نویگیشن: ظاهر شدن یکی پس از دیگری (Stagger)
        gsap.from(".nav-items-container > *", {
            duration: 0.8,
            y: -20,
            opacity: 0,
            stagger: 0.1, // فاصله زمانی بین هر آیتم
            ease: "power2.out",
            delay: 0.5
        });

        // افکت هاور حرفه‌ای با GSAP برای دکمه‌ها
        const buttons = document.querySelectorAll('.gs-hover-btn');
        buttons.forEach(btn => {
            btn.addEventListener('mouseenter', () => {
                gsap.to(btn, { scale: 1.05, duration: 0.2, ease: "power1.out" });
            });
            btn.addEventListener('mouseleave', () => {
                gsap.to(btn, { scale: 1, duration: 0.2, ease: "power1.out" });
            });
        });
    });

    // اسکریپت ساعت
    function updateClock() {
        const now = new Date();
        const el = document.getElementById('clock');
        if(el) el.innerText = now.toLocaleTimeString('fa-IR', {hour:'2-digit', minute:'2-digit'});
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>

<div class="container mx-auto px-4 pb-20 pt-6">