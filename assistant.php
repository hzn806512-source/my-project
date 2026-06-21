<!-- فایل: assistant.php -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<style>
    #assistant-container {
        position: fixed;
        bottom: 0; left: 20px;
        width: 250px; height: 350px;
        z-index: 99999; pointer-events: none;
        transition: all 0.3s ease;
    }
    @media (max-width: 768px) {
        #assistant-container { width: 160px; height: 240px; left: -10px; bottom: -10px; }
        #assistant-bubble { font-size: 10px !important; width: 130px !important; left: 80% !important; top: 20px !important; }
    }
    #assistant-canvas { width: 100%; height: 100%; pointer-events: auto; filter: drop-shadow(0 5px 15px rgba(0,0,0,0.5)); }
    #assistant-bubble {
        position: absolute; top: 40px; left: 70%; transform: translateX(-50%) scale(0);
        background: rgba(255, 255, 255, 0.98); color: #111827; padding: 10px 14px;
        border-radius: 15px 15px 15px 2px; font-family: 'Vazir', sans-serif; font-size: 12px; font-weight: 800;
        line-height: 1.5; max-width: 200px; min-width: 100px; text-align: right; direction: rtl;
        border: 2px solid #d4af37; box-shadow: 0 5px 20px rgba(0,0,0,0.4);
        transform-origin: bottom left; transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        pointer-events: none; z-index: 100000;
    }
    .typing-dots::after { content: ' .'; animation: dots 1s steps(5, end) infinite; }
    @keyframes dots { 0%, 20% { color: rgba(0,0,0,0); } 40% { color: black; } 100% { color: black; } }
</style>

<div id="assistant-container">
    <div id="assistant-bubble">...</div>
</div>

<script>
(function() {
    if(window.AssistantLoaded) return;
    window.AssistantLoaded = true;

    const container = document.getElementById('assistant-container');
    const bubble = document.getElementById('assistant-bubble');
    const scene = new THREE.Scene();
    
    // تنظیم دوربین (عقب‌تر برای کوچک‌نمایی)
    const camera = new THREE.PerspectiveCamera(40, 250/350, 0.1, 1000);
    camera.position.set(0, 0.5, 6.5);

    const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
    renderer.setSize(container.clientWidth, container.clientHeight);
    renderer.domElement.id = 'assistant-canvas';
    container.appendChild(renderer.domElement);

    window.addEventListener('resize', () => {
        const w = container.clientWidth; const h = container.clientHeight;
        renderer.setSize(w, h); camera.aspect = w / h; camera.updateProjectionMatrix();
    });

    const ambientLight = new THREE.AmbientLight(0xffffff, 0.7); scene.add(ambientLight);
    const keyLight = new THREE.DirectionalLight(0xffd700, 1.2); keyLight.position.set(2, 5, 5); scene.add(keyLight);

    const charGroup = new THREE.Group();
    const headGroup = new THREE.Group();
    const jawGroup = new THREE.Group(); // فک متحرک

    const skinMat = new THREE.MeshPhysicalMaterial({ color: 0xffccaa, metalness: 0.1, roughness: 0.4 });
    const suitMat = new THREE.MeshStandardMaterial({ color: 0x111111, roughness: 0.6 });
    const goldMat = new THREE.MeshStandardMaterial({ color: 0xd4af37, metalness: 0.9, roughness: 0.2 });
    const hairMat = new THREE.MeshStandardMaterial({ color: 0x1a1a1a, roughness: 0.9 });

    // بدن
    const body = new THREE.Mesh(new THREE.CylinderGeometry(0.6, 0.5, 1.6, 32), suitMat);
    body.position.y = -1.0; charGroup.add(body);
    
    // گردن
    const neck = new THREE.Mesh(new THREE.CylinderGeometry(0.15, 0.15, 0.4, 32), skinMat);
    neck.position.y = -0.1; charGroup.add(neck);

    // سر
    const headBase = new THREE.Mesh(new THREE.SphereGeometry(0.38, 32, 32), skinMat);
    headBase.scale.set(0.95, 1.15, 1); headGroup.add(headBase);

    // کلاه
    const hatBrim = new THREE.Mesh(new THREE.CylinderGeometry(0.65, 0.65, 0.05, 32), suitMat);
    hatBrim.position.y = 0.35; headGroup.add(hatBrim);
    const hatBody = new THREE.Mesh(new THREE.CylinderGeometry(0.42, 0.42, 0.6, 32), suitMat);
    hatBody.position.y = 0.65; headGroup.add(hatBody);
    const hatRibbon = new THREE.Mesh(new THREE.CylinderGeometry(0.43, 0.43, 0.12, 32), goldMat);
    hatRibbon.position.y = 0.45; headGroup.add(hatRibbon);

    // چشم‌ها
    const eyeGeo = new THREE.SphereGeometry(0.09, 16, 16);
    const eyeWhiteMat = new THREE.MeshBasicMaterial({color: 0xffffff});
    const pupilGeo = new THREE.SphereGeometry(0.04, 16, 16);
    const pupilMat = new THREE.MeshBasicMaterial({color: 0x000000});

    const eyeL = new THREE.Group();
    const ewL = new THREE.Mesh(eyeGeo, eyeWhiteMat);
    const epL = new THREE.Mesh(pupilGeo, pupilMat);
    epL.position.z = 0.08; eyeL.add(ewL); eyeL.add(epL);
    eyeL.position.set(-0.13, 0.08, 0.3); headGroup.add(eyeL);

    const eyeR = new THREE.Group();
    const ewR = new THREE.Mesh(eyeGeo, eyeWhiteMat);
    const epR = new THREE.Mesh(pupilGeo, pupilMat);
    epR.position.z = 0.08; eyeR.add(ewR); eyeR.add(epR);
    eyeR.position.set(0.13, 0.08, 0.3); headGroup.add(eyeR);

    // سیبیل نعل اسبی (Horseshoe) - یک تکه و متقارن
    const mustacheGroup = new THREE.Group();
    function createMustacheSide(mirror = false) {
        const curve = new THREE.CatmullRomCurve3([
            new THREE.Vector3(0.02, 0, 0),    // زیر بینی
            new THREE.Vector3(0.12, -0.05, 0.05), // گوشه لب
            new THREE.Vector3(0.12, -0.3, 0.1)    // پایین
        ]);
        const geo = new THREE.TubeGeometry(curve, 20, 0.04, 8, false);
        const mesh = new THREE.Mesh(geo, hairMat);
        if(mirror) mesh.scale.x = -1;
        return mesh;
    }
    mustacheGroup.add(createMustacheSide(true));
    mustacheGroup.add(createMustacheSide(false));
    mustacheGroup.position.set(0, -0.05, 0.35);
    jawGroup.add(mustacheGroup); // اتصال به فک

    // دهان
    const mouth = new THREE.Mesh(new THREE.BoxGeometry(0.1, 0.02, 0.01), new THREE.MeshBasicMaterial({color: 0x550000}));
    mouth.position.set(0, -0.15, 0.38);
    jawGroup.add(mouth);

    headGroup.add(jawGroup); // اتصال فک به سر
    
    // پاپیون (پایین‌تر)
    const bow = new THREE.Mesh(new THREE.TorusKnotGeometry(0.12, 0.04, 64, 8), goldMat);
    bow.position.set(0, -0.35, 0.25);
    headGroup.add(bow);

    headGroup.position.y = 0.2;
    charGroup.add(headGroup);
    charGroup.position.y = -0.5;
    scene.add(charGroup);

    // انیمیشن
    let mouseX = 0, mouseY = 0;
    document.addEventListener('mousemove', (e) => {
        mouseX = (e.clientX / window.innerWidth) * 2 - 1;
        mouseY = -(e.clientY / window.innerHeight) * 2 + 1;
    });

    function animate3D() {
        requestAnimationFrame(animate3D);
        charGroup.position.y = -0.5 + Math.sin(Date.now() * 0.002) * 0.02;
        
        // اصلاح جهت چرخش (مثبت شدن ضریب Y)
        const targetRotX = mouseY * 0.4; 
        const targetRotY = mouseX * 0.7;
        
        headGroup.rotation.x += (targetRotX - headGroup.rotation.x) * 0.1;
        headGroup.rotation.y += (targetRotY - headGroup.rotation.y) * 0.1;
        charGroup.rotation.y += (targetRotY * 0.15 - charGroup.rotation.y) * 0.05;

        epL.position.x = mouseX * 0.03; epL.position.y = mouseY * 0.03;
        epR.position.x = mouseX * 0.03; epR.position.y = mouseY * 0.03;

        renderer.render(scene, camera);
    }
    animate3D();

    // صحبت کردن
    let hideTimer;
    window.assistantSpeak = function(text) {
        bubble.innerHTML = text;
        bubble.style.transform = "translateX(-50%) scale(1)";
        
        // انیمیشن فک (تکان خوردن سیبیل و دهان)
        gsap.to(jawGroup.position, { y: -0.03, yoyo: true, repeat: 9, duration: 0.12 });
        gsap.to(headGroup.rotation, { z: 0.05, yoyo: true, repeat: 3, duration: 0.4 });

        clearTimeout(hideTimer);
        const readingTime = Math.max(3000, text.length * 80);
        hideTimer = setTimeout(() => {
            bubble.style.transform = "translateX(-50%) scale(0)";
            gsap.to(jawGroup.position, { y: 0, duration: 0.2 });
            gsap.to(headGroup.rotation, { z: 0, duration: 0.2 });
        }, readingTime);
    };

    // تعاملات
    const searchInput = document.querySelector('input[name="search"]');
    if(searchInput) {
        searchInput.addEventListener('mouseenter', () => assistantSpeak("دنبال چی می‌گردی؟ بگو تا راهنماییت کنم."));
    }
    document.querySelectorAll('button, a').forEach(el => {
        const text = el.innerText.trim();
        if(text.includes('ورود')) el.addEventListener('mouseenter', () => assistantSpeak("عضو شو!"));
        if(text.includes('خرید')) el.addEventListener('mouseenter', () => assistantSpeak("بهترین انتخابه!"));
    });
    let apiTimeout;
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            const pName = card.dataset.name; const pDesc = card.dataset.desc;
            clearTimeout(apiTimeout); assistantSpeak("اوه! این مدل...");
            apiTimeout = setTimeout(async () => {
                try {
                    bubble.innerHTML = "<span class='typing-dots'>...</span>";
                    const fd = new FormData(); fd.append('action', 'gemini'); fd.append('name', pName); fd.append('desc', pDesc);
                    const res = await fetch('api_handler.php', { method: 'POST', body: fd });
                    const data = await res.json();
                    let aiText = "کیفیت عالیه.";
                    if(data.candidates) aiText = data.candidates[0].content.parts[0].text.trim();
                    assistantSpeak(aiText);
                } catch(e) {}
            }, 1500);
        });
    });
    setTimeout(() => assistantSpeak("سلام! من اینجام."), 1000);
})();
</script>