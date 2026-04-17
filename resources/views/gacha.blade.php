<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Undian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@900&display=swap');
        
        body { 
            background: radial-gradient(circle, #1a1a2e, #16213e, #0f3460); 
            color: white; 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-family: 'Montserrat', sans-serif; 
            overflow: hidden; 
            margin: 0;
        }

        .main-stage { text-align: center; width: 100%; max-width: 1000px; padding: 20px; z-index: 1; }

        /* Area Hadiah */
        #prize-area { min-height: 280px; display: flex; flex-direction: column; align-items: center; justify-content: center; margin-bottom: 20px; }
        #gift-icon { font-size: 120px; filter: drop-shadow(0 0 20px #f093fb); }
        #prize-img { 
            height: 220px; display: none; border-radius: 25px; 
            border: 4px solid rgba(255,255,255,0.2);
            box-shadow: 0 0 50px rgba(255, 215, 0, 0.3);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }

        /* Box Nama (Glassmorphism) */
        .name-display-box { 
            background: rgba(255, 255, 255, 0.05); 
            backdrop-filter: blur(15px);
            border: 2px solid rgba(255, 255, 255, 0.1); 
            border-radius: 40px; 
            padding: 60px 30px; 
            margin-bottom: 30px; 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .shaking-box { transform: scale(1.05); border-color: #f5576c; box-shadow: 0 0 40px rgba(245, 87, 108, 0.4); }

        /* Ukuran Font Nama + Nomor */
        #display-name { 
            font-size: 3.2rem; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            line-height: 1.2;
            word-wrap: break-word;
        }
        
        .winner-glow { 
            color: #f9d342; 
            text-shadow: 0 0 20px #f9d342, 0 0 40px #ff9f43;
            animation: winner-pulse 0.8s infinite alternate;
        }

        @keyframes winner-pulse {
            from { transform: scale(1); } to { transform: scale(1.08); }
        }

        .btn-draw { 
            background: linear-gradient(45deg, #f093fb, #f5576c); 
            border: none; padding: 20px 70px; font-size: 1.7rem; 
            border-radius: 100px; color: white; font-weight: 900; 
            box-shadow: 0 10px 30px rgba(245, 87, 108, 0.5);
            transition: 0.3s;
        }

        .btn-draw:hover:not(:disabled) { transform: translateY(-3px); box-shadow: 0 15px 40px rgba(245, 87, 108, 0.7); }
        .btn-draw:disabled { opacity: 0.5; cursor: not-allowed; transform: scale(0.98); }

        .counter-badge {
            background: rgba(0,0,0,0.3);
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 0.9rem;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .shake-anim { animation: shake 0.5s infinite; }
        @keyframes shake { 0%, 100% { transform: rotate(0); } 25% { transform: rotate(10deg); } 75% { transform: rotate(-10deg); } }
    </style>
</head>
<body>

<div class="main-stage">
    <div id="prize-area">
        <div id="gift-icon">🎁</div>
        <img id="prize-img" src="">
        <h4 id="prize-name" class="mt-4 fw-bold text-warning"></h4>
    </div>

    <div class="name-display-box shadow-lg" id="name-container">
        <div id="display-name">SIAP UNDI?</div>
    </div>

    <div class="controls mx-auto" style="max-width: 500px;">
        <div class="d-flex justify-content-between align-items-center mb-3 px-2">
            <span class="small text-white-50">LIST HADIAH:</span>
            <span class="counter-badge" id="count-text">Sisa: {{ count($employees) }} Peserta</span>
        </div>
        
        <select id="select-prize" class="form-select mb-4 bg-dark text-white border-secondary py-3 rounded-4 shadow-none">
            <option value="" disabled selected>-- PILIH HADIAH --</option>
            @foreach($prizes as $p)
                <option value="{{ $p->nama_hadiah }}" data-foto="{{ $p->foto_hadiah }}">{{ $p->nama_hadiah }}</option>
            @endforeach
        </select>
        
        <button class="btn-draw w-100" id="btn-spin" onclick="startDraw()">MULAI ACAK</button>
    </div>

    <div class="mt-5">
        <a href="{{ url('/') }}" class="text-white-50 text-decoration-none small">
            <i class="fas fa-arrow-left me-1"></i> KEMBALI KE ADMIN
        </a>
    </div>
</div>

<audio id="sound-roll" src="https://assets.mixkit.co/active_storage/sfx/2013/2013-preview.mp3" preload="auto"></audio>
<audio id="sound-win" src="https://assets.mixkit.co/active_storage/sfx/1435/1435-preview.mp3" preload="auto"></audio>

<script>
    let employees = JSON.parse('{!! json_encode($employees) !!}');

    function startDraw() {
        const selectPrize = document.getElementById('select-prize');
        if (selectPrize.value === "") return alert("Pilih hadiah dulu boss!");
        if (employees.length === 0) return alert("Yah, pesertanya sudah habis!");

        const btn = document.getElementById('btn-spin');
        const dispName = document.getElementById('display-name');
        const nameContainer = document.getElementById('name-container');
        const giftIcon = document.getElementById('gift-icon');
        const prizeImg = document.getElementById('prize-img');
        const prizeNameText = document.getElementById('prize-name');
        const countText = document.getElementById('count-text');
        
        const soundRoll = document.getElementById('sound-roll');
        const soundWin = document.getElementById('sound-win');

        // Reset Tampilan
        btn.disabled = true;
        dispName.classList.remove('winner-glow');
        nameContainer.classList.add('shaking-box');
        giftIcon.style.display = 'block';
        giftIcon.classList.add('shake-anim');
        prizeImg.style.display = 'none';
        prizeNameText.innerText = "MENGUNDI...";
        
        soundRoll.currentTime = 0;
        soundRoll.play();

        let duration = 4000; 
        let startTime = Date.now();

        function shuffle() {
            let elapsed = Date.now() - startTime;
            
            // Pilih data acak untuk visual "running"
            let randomIdx = Math.floor(Math.random() * employees.length);
            let emp = employees[randomIdx];
            
            // TAMPILKAN NAMA + NOMOR (CONTOH: BUDI - 12345)
            dispName.innerText = emp.employee_name + " - " + emp.employee_number;

            if (elapsed < duration) {
                setTimeout(shuffle, 70);
            } else {
                // SELESAI - TENTUKAN PEMENANG ASLI
                const winIdx = Math.floor(Math.random() * employees.length);
                const winner = employees[winIdx];
                const selectedOpt = selectPrize.options[selectPrize.selectedIndex];

                // Update UI Pemenang
                dispName.innerText = winner.employee_name + " - " + winner.employee_number;
                dispName.classList.add('winner-glow');
                nameContainer.classList.remove('shaking-box');
                
                giftIcon.classList.remove('shake-anim');
                giftIcon.style.display = 'none';

                prizeImg.src = "/images/" + selectedOpt.getAttribute('data-foto');
                prizeImg.style.display = 'block';
                prizeNameText.innerHTML = `<i class="fas fa-trophy"></i> HADIAH: ${selectPrize.value.toUpperCase()}`;

                // Efek Suara & Confetti
                soundRoll.pause();
                soundWin.play();
                confetti({
                    particleCount: 200,
                    spread: 90,
                    origin: { y: 0.6 },
                    colors: ['#f9d342', '#ffffff', '#f5576c']
                });

                // Hapus di Database (Permanen)
                fetch('/win', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ id_employee: winner.id })
                });

                // Update List & Counter Lokal
                employees.splice(winIdx, 1);
                countText.innerText = "Sisa: " + employees.length + " Peserta";
                
                btn.disabled = false;
            }
        }
        shuffle();
    }
</script>
</body>
</html>