<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Undian - Elegant Cream Theme</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700;900&display=swap');

      body { 
            /* 1. BACKGROUND DARI GAMBAR ASLI */
            background-image: url('/images/bg-undian.png'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-family: 'Montserrat', sans-serif; 
            margin: 0; 
            overflow: hidden; 
            position: relative;
        }

        .main-stage { 
            text-align: center; 
            width: 100%; 
            max-width: 1000px; 
            z-index: 10; 
            position: relative; 
        }

        /* Container Logo Tambahan (RAT K2MS) */
        .header-logo {
            margin-bottom: 20px;
        }
        .header-logo img {
            height: 80px; /* Ukuran logo rat_k2ms.png */
            object-fit: contain;
            filter: drop-shadow(0 5px 10px rgba(0,0,0,0.1));
        }

        #prize-container { min-height: 320px; margin-bottom: 20px; }
        
        #gift-preview { 
            font-size: 140px; 
            color: #8d7d77; 
            animation: float 3s infinite ease-in-out;
            filter: drop-shadow(0 10px 10px rgba(0,0,0,0.1));
        }
        
        #prize-final { display: none; }
        #prize-img { 
            height: 280px; 
            border-radius: 20px; 
            border: 12px solid #fff; 
            box-shadow: 0 25px 50px rgba(0,0,0,0.1); 
            margin-bottom: 20px; 
        }
        
        #prize-title { 
            color: #5d534f;
            font-size: 3rem; 
            text-transform: uppercase; 
            font-weight: 900; 
        }

        /* Box Nama Pemenang */
        .name-box { 
            background: rgba(255, 255, 255, 0.75); 
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.5);
            border-radius: 30px; 
            padding: 50px 40px; 
            margin-bottom: 30px; 
            min-height: 250px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
        }
        
        #display-text { 
            font-size: 5rem; 
            color: #3d3633; 
            font-weight: 900; 
            line-height: 1.1;
        }
        
        .winner-glow { 
            color: #8d7d77 !important; 
            animation: pulse 0.5s infinite alternate; 
        }

        /* Tombol Brown-Beige */
        .btn-draw { 
            background: linear-gradient(to right, #8d7d77, #5d534f);
            border: none; 
            padding: 20px 100px; 
            font-size: 1.8rem; 
            border-radius: 100px; 
            color: white; 
            font-weight: 900; 
            box-shadow: 0 10px 30px rgba(93,83,79,0.3); 
            cursor: pointer; 
            transition: 0.4s;
            text-transform: uppercase;
        }
        
        .btn-draw:hover { transform: translateY(-3px); }
        .btn-draw:disabled { opacity: 0; pointer-events: none; }

        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-25px); } }
        @keyframes pulse { from { transform: scale(1); } to { transform: scale(1.05); } }
    </style>
</head>
<body>

<div class="main-stage">
    <div class="header-logo">
        <img src="/images/rat_k2ms.png" alt="Logo RAT">
    </div>

    <div id="prize-container">
        <div id="gift-preview">
            <i class="fas fa-gift"></i>
        </div>
        <div id="prize-final">
            <img id="prize-img" src="">
            <div id="prize-title"></div>
        </div>
    </div>

    <div class="name-box">
        <div id="display-text">SIAP?</div>
    </div>

    <button class="btn-draw" id="btn-spin" onclick="startDraw()">MULAI UNDI</button>
</div>

<audio id="snd-roll" src="https://assets.mixkit.co/active_storage/sfx/2013/2013-preview.mp3" preload="auto"></audio>
<audio id="snd-win" src="https://assets.mixkit.co/active_storage/sfx/1435/1435-preview.mp3" preload="auto"></audio>

<script>
    const employees = @json($employees);
    const prizes = @json($prizes);
    const selectedPrizeName = "{{ $nama_hadiah_manual }}";

    function startDraw() {
        if (employees.length === 0) return alert("Selesai!");
        
        const btn = document.getElementById('btn-spin'), disp = document.getElementById('display-text');
        const gift = document.getElementById('gift-preview'), pFinal = document.getElementById('prize-final');
        const pImg = document.getElementById('prize-img'), pTitle = document.getElementById('prize-title');
        const sRoll = document.getElementById('snd-roll'), sWin = document.getElementById('snd-win');

        btn.disabled = true;
        gift.style.display = 'block';
        pFinal.style.display = 'none';
        disp.classList.remove('winner-glow');
        
        sRoll.currentTime = 0;
        sRoll.play();

        let startTime = Date.now();
        let shuffle = setInterval(() => {
            let rnd = employees[Math.floor(Math.random() * employees.length)];
            disp.innerText = rnd.employee_number;

            if (Date.now() - startTime > 3000) {
                clearInterval(shuffle);
                const winIdx = Math.floor(Math.random() * employees.length);
                const winner = employees[winIdx];
                disp.innerText = winner.employee_number;
                sRoll.pause(); 

                // DRAMA 5 DETIK
                setTimeout(() => {
                    sWin.play();
                    gift.style.display = 'none';
                    pTitle.innerText = selectedPrizeName;
                    
                    let pObj = prizes.find(p => p.nama_hadiah === selectedPrizeName);
                    if(pObj) pImg.src = "/images/" + pObj.foto_hadiah;
                    pFinal.style.display = 'block';

                    disp.innerHTML = winner.employee_name + "<br><small style='font-size: 2.2rem; opacity: 0.6;'>" + winner.employee_number + "</small>";
                    disp.classList.add('winner-glow');

                    confetti({
                        particleCount: 300, spread: 100, origin: { y: 0.6 },
                        colors: ['#8d7d77', '#f4ede7', '#ffffff', '#ffd700']
                    });

                    fetch('/win', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ id_employee: winner.id })
                    });
                    employees.splice(winIdx, 1);
                    
                    setTimeout(() => {
                        btn.disabled = false;
                        btn.innerText = "UNDI LAGI";
                    }, 2000);
                }, 5000);
            }
        }, 80);
    }
</script>

</body>
</html>