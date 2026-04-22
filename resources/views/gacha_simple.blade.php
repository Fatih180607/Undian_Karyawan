<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undian Doorprize - RAT K2MS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        .name-box {
            background: rgba(255,255,255,0.95);
            border-radius: 25px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border: 3px solid #007bff;
            margin: 20px auto;
            max-width: 600px;
        }
        #display-text {
            font-size: 3.5rem;
            font-weight: bold;
            color: #2c3e50;
            margin: 20px 0;
        }
        .winner-glow {
            animation: glow 2s ease-in-out infinite alternate;
        }
        @keyframes glow {
            from { text-shadow: 0 0 10px #007bff, 0 0 20px #007bff; }
            to { text-shadow: 0 0 20px #007bff, 0 0 30px #007bff; }
        }
        .btn-spin {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 1.2rem;
            border-radius: 50px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-spin:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,123,255,0.3);
        }
        .btn-spin:disabled {
            background: #6c757d;
            transform: none;
            box-shadow: none;
        }
        .prize-display {
            background: rgba(255,255,255,0.9);
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            margin: 20px auto;
            max-width: 400px;
            display: none;
        }
        .prize-img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 15px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-4">
                    <h1 class="text-white fw-bold">
                        <i class="fas fa-gift me-3"></i>UNDIAN DOORPRIZE
                    </h1>
                    <p class="text-white-50">{{ $nama_hadiah_manual }} - {{ $selected_plant == 'all' ? 'SEMUA PLANT' : \App\Models\Plant::find($selected_plant)->nama_plant }}</p>
                </div>

                <div class="name-box">
                    <div id="display-text">SIAP?</div>
                    <button id="btn-spin" class="btn btn-spin btn-lg" onclick="startDraw()">
                        <i class="fas fa-dice me-2"></i>MULAI UNDI
                    </button>
                </div>

                <div id="prize-display" class="prize-display">
                    <h5 class="fw-bold text-primary mb-3">SELAMAT!</h5>
                    <img id="prize-img" src="" alt="Hadiah" class="prize-img">
                    <h6 id="prize-title" class="fw-bold"></h6>
                </div>

                <!-- Tombol Export -->
                <div class="position-fixed bottom-0 start-0 m-3 d-none" id="btnActions">
                    <button class="btn btn-success btn-sm me-2 rounded-pill shadow" onclick="exportCSV()">
                        <i class="fas fa-file-csv me-1"></i>Export CSV
                    </button>
                    <button class="btn btn-primary btn-sm rounded-pill shadow" onclick="saveImage()">
                        <i class="fas fa-camera me-1"></i>Simpan Gambar
                    </button>
                </div>

                <!-- Tombol Summary -->
                <button class="btn btn-warning btn-lg position-fixed bottom-0 end-0 m-3 rounded-pill shadow-lg d-none" 
                        id="btnSummary" onclick="showSummary()">
                    <i class="fas fa-trophy me-2"></i>LIHAT PEMENANG (<span id="totalWinners">0</span>)
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Summary -->
    <div class="modal fade" id="summaryModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-trophy me-2"></i>SUMMARY PEMENANG
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="summaryContent">
                    <!-- Content akan diisi dengan JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="saveSummaryImage()">
                        <i class="fas fa-camera me-2"></i>Simpan Gambar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Data dari controller
        const employees = @json($employees);
        const prizes = @json($prizes);
        const selectedPrize = "{{ $nama_hadiah_manual }}";
        const selectedPlant = "{{ $selected_plant }}";
        
        let winners = [];
        let isDrawing = false;

        console.log('Data loaded:', {
            employees: employees.length,
            prizes: prizes.length,
            selectedPrize: selectedPrize,
            selectedPlant: selectedPlant
        });

        function startDraw() {
            if (isDrawing) return;
            
            const availableEmployees = employees.filter(emp => !emp.is_winner);
            if (availableEmployees.length === 0) {
                alert('Tidak ada karyawan yang tersedia untuk diundi!');
                return;
            }

            isDrawing = true;
            const btn = document.getElementById('btn-spin');
            const display = document.getElementById('display-text');
            const prizeDisplay = document.getElementById('prize-display');
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>MENGUNDI...';
            prizeDisplay.style.display = 'none';
            display.classList.remove('winner-glow');

            // Animasi shuffle
            let count = 0;
            const maxCount = 50;
            const interval = setInterval(() => {
                const random = availableEmployees[Math.floor(Math.random() * availableEmployees.length)];
                display.innerText = random.employee_number;
                count++;
                
                if (count >= maxCount) {
                    clearInterval(interval);
                    
                    // Pilih pemenang
                    const winner = availableEmployees[Math.floor(Math.random() * availableEmployees.length)];
                    display.innerText = winner.employee_name;
                    
                    setTimeout(() => {
                        showWinner(winner);
                    }, 1000);
                }
            }, 100);
        }

        function showWinner(winner) {
            const display = document.getElementById('display-text');
            const prizeDisplay = document.getElementById('prize-display');
            const prizeImg = document.getElementById('prize-img');
            const prizeTitle = document.getElementById('prize-title');
            
            display.innerHTML = winner.employee_name + '<br><small style="font-size: 1.5rem;">' + winner.employee_number + '</small>';
            display.classList.add('winner-glow');
            
            // Tampilkan hadiah
            prizeImg.src = '/images/mystery_box.jpg';
            prizeTitle.innerText = selectedPrize;
            prizeDisplay.style.display = 'block';
            
            // Confetti
            confetti({ particleCount: 300, spread: 100, origin: { y: 0.6 } });
            
            // Simpan pemenang
            saveWinner(winner);
            
            // Update UI
            updateWinnersList();
            
            // Reset button
            setTimeout(() => {
                const btn = document.getElementById('btn-spin');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-dice me-2"></i>MULAI UNDI';
                isDrawing = false;
            }, 3000);
        }

        function saveWinner(winner) {
            // Update di database
            fetch('/win', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id_employee: winner.id,
                    nama_hadiah: selectedPrize,
                    foto_hadiah: null,
                    nomor_undian: winners.length + 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update local data
                    const empIndex = employees.findIndex(emp => emp.id === winner.id);
                    if (empIndex !== -1) {
                        employees[empIndex].is_winner = true;
                        employees[empIndex].prize_won = selectedPrize;
                    }
                    
                    // Tambah ke winners list
                    winners.push({
                        nomor: winners.length + 1,
                        nama: winner.employee_name,
                        nomor_karyawan: winner.employee_number,
                        hadiah: selectedPrize,
                        plant: winner.plant?.nama_plant || 'Unknown',
                        waktu: new Date().toLocaleTimeString('id-ID')
                    });
                } else {
                    alert('Error menyimpan pemenang!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error menyimpan pemenang!');
            });
        }

        function updateWinnersList() {
            const btnActions = document.getElementById('btnActions');
            const btnSummary = document.getElementById('btnSummary');
            const totalWinners = document.getElementById('totalWinners');
            
            if (winners.length > 0) {
                btnActions.classList.remove('d-none');
                btnSummary.classList.remove('d-none');
                totalWinners.textContent = winners.length;
            }
        }

        function exportCSV() {
            if (winners.length === 0) return;
            
            let csv = 'No,Nama,Nomor Karyawan,Hadiah,Plant,Waktu\n';
            winners.forEach(w => {
                csv += `${w.nomor},"${w.nama}","${w.nomor_karyawan}","${w.hadiah}","${w.plant}","${w.waktu}"\n`;
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `pemenang_${new Date().toISOString().slice(0,10)}.csv`;
            a.click();
            URL.revokeObjectURL(url);
        }

        function saveImage() {
            html2canvas(document.body).then(canvas => {
                canvas.toBlob(blob => {
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `bukti_${new Date().toISOString().slice(0,10)}.jpg`;
                    a.click();
                    URL.revokeObjectURL(url);
                });
            });
        }

        function showSummary() {
            const content = document.getElementById('summaryContent');
            
            // Group by plant
            const grouped = {};
            winners.forEach(w => {
                if (!grouped[w.plant]) grouped[w.plant] = [];
                grouped[w.plant].push(w);
            });
            
            let html = '';
            Object.keys(grouped).forEach(plant => {
                html += `
                    <div class="mb-4">
                        <h5 class="fw-bold text-primary">
                            <i class="fas fa-building me-2"></i>${plant} (${grouped[plant].length} Pemenang)
                        </h5>
                        <div class="row">
                `;
                
                grouped[plant].forEach(w => {
                    html += `
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="fw-bold">${w.nama}</h6>
                                    <p class="mb-1"><small>${w.nomor_karyawan}</small></p>
                                    <p class="mb-0"><small class="text-muted">${w.hadiah}</small></p>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div></div>';
            });
            
            content.innerHTML = html;
            
            const modal = new bootstrap.Modal(document.getElementById('summaryModal'));
            modal.show();
        }

        function saveSummaryImage() {
            const modal = document.getElementById('summaryModal');
            const modalBody = modal.querySelector('.modal-body');
            
            html2canvas(modalBody).then(canvas => {
                canvas.toBlob(blob => {
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `summary_${new Date().toISOString().slice(0,10)}.jpg`;
                    a.click();
                    URL.revokeObjectURL(url);
                });
            });
        }
    </script>
</body>
</html>
