<?php
session_start();
include 'db.php';

// امنیت: اگر کاربر لاگین نیست، به صفحه اصلی برگردد
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/3159/3159614.png" type="image/png">
    <title>پشتیبانی بوتیک | مدیریت</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font/dist/font-face.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <script>
        tailwind.config = { 
            theme: { 
                extend: { 
                    colors: { 
                        'shop-gold': '#d4af37',
                        'shop-dark': '#111827',
                        'shop-panel': '#1f2937',
                        'shop-accent': '#6366f1',
                    }, 
                    fontFamily: { 
                        'vazir': ['Vazir', 'sans-serif'] 
                    } 
                } 
            } 
        }
    </script>

    <style>
        html, body {
            height: 100%;
            overflow: hidden;
            background-color: #111827;
            font-family: 'Vazir';
        }

        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 10px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }

        .chat-pattern {
            background-color: #0f172a;
            background-image: radial-gradient(#374151 1px, transparent 1px);
            background-size: 24px 24px;
        }

        .msg-bubble {
            max-width: 85%;
            padding: 12px 16px;
            border-radius: 16px;
            font-size: 0.95rem;
            position: relative;
            line-height: 1.5;
            word-wrap: break-word;
        }
        
        .msg-user { background: #374151; color: white; border-bottom-left-radius: 2px; }
        .msg-admin { background: #4f46e5; color: white; border-bottom-right-radius: 2px; }

        .msg-time {
            font-size: 0.65rem; opacity: 0.7; margin-top: 4px; display: block;
        }
        .msg-user .msg-time { text-align: left; }
        .msg-admin .msg-time { text-align: right; color: #e0e7ff; }

        /* فیکس کردن اینپوت در موبایل */
        @media (max-width: 768px) {
            #input-area {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                z-index: 50;
                border-top: 1px solid #374151;
                background-color: #1f2937;
                padding-bottom: env(safe-area-inset-bottom);
            }
            #chat-messages {
                padding-bottom: 80px !important; /* فضای خالی برای اینپوت */
            }
            #main-chat {
                height: 100vh;
                height: 100dvh;
            }
        }
    </style>
</head>
<body class="flex w-full h-full text-white">

    <!-- سایدبار لیست کاربران -->
    <aside id="sidebar-users" class="w-full md:w-80 bg-shop-panel border-l border-gray-700 flex flex-col z-20 h-full absolute md:relative">
        <div class="p-4 bg-shop-dark border-b border-gray-700 flex justify-between items-center shadow-md shrink-0">
            <h2 class="font-bold text-shop-gold flex items-center gap-2">💬 مشتریان</h2>
            <a href="admin.php" class="text-xs bg-gray-700 hover:bg-gray-600 px-3 py-1.5 rounded-lg transition">خروج</a>
        </div>
        <div id="users-list" class="flex-1 overflow-y-auto custom-scroll p-2 space-y-1">
            <div class="text-center text-gray-500 mt-10 text-sm">در حال بارگذاری...</div>
        </div>
    </aside>

    <!-- باکس چت اصلی -->
    <main id="main-chat" class="flex-1 flex flex-col chat-pattern relative w-full h-full hidden md:flex z-30 bg-[#0f172a]">
        
        <!-- هدر چت -->
        <div class="h-16 px-4 border-b border-gray-700 bg-shop-panel/95 backdrop-blur flex items-center gap-3 shadow-sm shrink-0 sticky top-0 z-40">
            <button onclick="toggleView('list')" class="md:hidden p-2 rounded-full bg-gray-700 hover:bg-gray-600">➜</button>
            <img id="header-img" src="" class="w-10 h-10 rounded-full border border-gray-600 bg-gray-800 object-cover">
            <div class="flex-1 min-w-0">
                <h3 id="header-name" class="font-bold truncate text-sm">انتخاب کنید</h3>
                <span class="text-xs text-green-400">آنلاین</span>
            </div>
        </div>

        <!-- پیام‌ها -->
        <div id="chat-messages" class="flex-1 overflow-y-auto custom-scroll p-4 space-y-3 pb-24 md:pb-4">
            <div class="h-full flex flex-col items-center justify-center text-gray-500 opacity-50">
                <p>یک مشتری را جهت گفتگو انتخاب کنید</p>
            </div>
        </div>

        <!-- ورودی پیام -->
        <div id="input-area" class="p-3 bg-shop-panel shrink-0 hidden">
            <form onsubmit="sendAdminMessage(event)" class="flex gap-2 max-w-4xl mx-auto w-full">
                <input type="text" id="msg-input" placeholder="پیام خود را بنویسید..." autocomplete="off"
                       class="flex-1 bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-600 focus:border-shop-gold focus:outline-none transition h-12 text-sm">
                
                <button type="submit" class="h-12 w-12 bg-shop-accent hover:bg-indigo-600 text-white rounded-xl flex items-center justify-center shadow-lg active:scale-95 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                </button>
            </form>
        </div>
    </main>

<script>
    let currentUserId = null;
    let chatInterval = null;
    let lastMsgId = 0;
    
    const sidebar = document.getElementById('sidebar-users');
    const mainChat = document.getElementById('main-chat');
    const inputArea = document.getElementById('input-area');

    function toggleView(view) {
        if (window.innerWidth >= 768) return; 

        if (view === 'chat') {
            sidebar.classList.add('hidden');
            mainChat.classList.remove('hidden');
            mainChat.classList.add('flex');
        } else {
            sidebar.classList.remove('hidden');
            mainChat.classList.add('hidden');
            mainChat.classList.remove('flex');
            if(chatInterval) clearInterval(chatInterval);
            currentUserId = null;
        }
    }

    function loadUsers() {
        const fd = new FormData();
        fd.append('action', 'get_users_list');
        
        fetch('api_chat.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(users => {
            const list = document.getElementById('users-list');
            let html = '';
            
            if(users.length === 0) {
                list.innerHTML = '<div class="text-center text-gray-500 mt-10 text-xs">پیامی نیست</div>';
                return;
            }

            users.forEach(u => {
                let img = u.profile_pic && u.profile_pic !== 'null' ? u.profile_pic : 'https://cdn.jsdelivr.net/gh/microsoft/fluentui-emoji@latest/assets/Person/3D/person_3d.png';
                let badge = u.unread > 0 ? `<span class="bg-red-500 text-white text-[10px] px-2 rounded-full ml-2">${u.unread}</span>` : '';
                let activeClass = (currentUserId == u.id) ? 'bg-gray-700/80 border-shop-gold' : 'border-transparent hover:bg-gray-800';

                html += `
                <div onclick="openChat(${u.id}, '${u.name}', '${img}')" class="cursor-pointer p-3 flex items-center gap-3 rounded-xl mb-1 border-r-4 transition ${activeClass}">
                    <img src="${img}" class="w-10 h-10 rounded-full bg-gray-900 border border-gray-600 object-cover">
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-center">
                            <h4 class="font-bold text-gray-200 text-sm truncate">${u.name}</h4>
                            ${badge}
                        </div>
                        <p class="text-xs text-gray-400 truncate">مشاهده...</p>
                    </div>
                </div>`;
            });
            list.innerHTML = html;
        });
    }

    function openChat(uid, name, img) {
        currentUserId = uid;
        document.getElementById('header-name').innerText = name;
        document.getElementById('header-img').src = img;
        
        inputArea.classList.remove('hidden');
        toggleView('chat');

        document.getElementById('chat-messages').innerHTML = '';
        lastMsgId = 0; 
        loadMessages();
        
        if(chatInterval) clearInterval(chatInterval);
        chatInterval = setInterval(loadMessages, 3000);
    }

    function loadMessages() {
        if(!currentUserId) return;
        
        const fd = new FormData();
        fd.append('action', 'get_conversation');
        fd.append('user_id', currentUserId);
        
        fetch('api_chat.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(msgs => {
            const container = document.getElementById('chat-messages');
            let shouldScroll = false;
            
            if(msgs.length === 0 && lastMsgId === 0) {
                container.innerHTML = '<div class="flex flex-col items-center justify-center h-full text-gray-500 text-sm"><p>هنوز پیامی نیست.</p></div>';
                return;
            }

            msgs.forEach(m => {
                if (parseInt(m.id) > lastMsgId) {
                    if(lastMsgId === 0) container.innerHTML = '';
                    
                    let cls = m.sender === 'user' ? 'justify-start' : 'justify-end';
                    let bubble = m.sender === 'user' ? 'msg-user' : 'msg-admin';
                    let tick = m.sender === 'admin' ? '✓ ' : '';

                    let html = `
                    <div class="flex ${cls} mb-3 animate__animated animate__fadeIn">
                        <div class="msg-bubble ${bubble}">
                            ${m.message}
                            <span class="msg-time">${tick}${m.created_at}</span>
                        </div>
                    </div>`;

                    container.insertAdjacentHTML('beforeend', html);
                    lastMsgId = parseInt(m.id);
                    shouldScroll = true;
                }
            });

            if(shouldScroll) {
                container.scrollTop = container.scrollHeight;
            }
        });
    }

    function sendAdminMessage(e) {
        e.preventDefault();
        
        // جلوگیری از ارسال اگر کاربر انتخاب نشده
        if (!currentUserId) {
            alert("لطفاً ابتدا یک مشتری را از لیست انتخاب کنید.");
            return;
        }

        const input = document.getElementById('msg-input');
        const msg = input.value.trim();
        
        if(!msg) return;
        
        input.disabled = true;

        const fd = new FormData();
        fd.append('action', 'admin_reply');
        fd.append('user_id', currentUserId);
        fd.append('message', msg);
        
        fetch('api_chat.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(d => {
            input.disabled = false;
            input.focus();
            if(d.status === 'success') {
                input.value = '';
                loadMessages(); 
            } else {
                alert('خطا در ارسال پیام');
            }
        })
        .catch(err => {
            input.disabled = false;
            console.error(err);
            // اینجا آلرت را برداشتم تا اگر خطای ریز شبکه بود کاربر اذیت نشود
        });
    }

    loadUsers();
    setInterval(loadUsers, 5000);
</script>
</body>
</html>