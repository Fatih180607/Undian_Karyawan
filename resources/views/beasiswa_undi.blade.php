<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Undian Beasiswa - RAT K2MS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        html, body { min-height: 100%; margin: 0; font-family: 'Segoe UI', sans-serif; background: url('/images/bg-undian.png') no-repeat center center fixed; background-size: cover; color: #2f2b24; }
        body { background-color: rgba(0,0,0,0.15); position: relative; }
        body::before { content: ''; position: fixed; inset: 0; background: rgba(13, 17, 31, 0.35); pointer-events: none; }
        .container { position: relative; z-index: 2; }
        .top-brand { display: flex; align-items: center; justify-content: center; gap: 16px; margin-bottom: 20px; }
        .top-brand img { height: 72px; width: auto; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.25); }
        .top-brand h1 { margin: 0; font-size: 2.4rem; letter-spacing: 2px; color: #ffe599; text-shadow: 0 3px 14px rgba(0,0,0,0.45); }
        .header-panel { background: rgba(20, 23, 35, 0.72); border-radius: 30px; padding: 26px; border: 1px solid rgba(255,255,255,0.15); }
        .header-panel label { color: #f5f3ef; }
        .grid-container { display: grid; grid-template-columns: repeat(2, minmax(280px, 1fr)); gap: 24px; margin-top: 30px; }
        .box-undi { background: rgba(255,255,255,0.92); border-radius: 28px; min-height: 320px; padding: 24px; box-shadow: 0 18px 40px rgba(0,0,0,0.12); border: 2px solid transparent; transition: all 0.5s ease; position: relative; }
        .box-active { border-color: #ffcb3d; transform: scale(1.02); box-shadow: 0 0 25px rgba(255, 203, 61, 0.5); }
        .winner-item { background: #fff; border-left: 6px solid #dee2e6; padding: 18px 16px; margin-bottom: 14px; box-shadow: 0 10px 20px rgba(0,0,0,0.08); border-radius: 0 8px 8px 0; }
        .running-text { color: #d9534f; font-weight: bold; font-size: 0.85rem; }
        .debug-info { position: absolute; bottom: 10px; right: 20px; font-size: 10px; color: #ccc; }
        #capture-area { background: #ffffff; padding: 30px; border-radius: 10px; color: #000; }
        .download-header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #f8b500; padding-bottom: 10px; }
        .jenjang-section { background: #f8f9fa; padding: 10px; margin-top: 15px; font-weight: bold; border-radius: 5px; color: #000; border-left: 5px solid #f8b500; }
        .pemenang-row { border-bottom: 1px solid #eee; padding: 10px 0; display: flex; justify-content: space-between; font-size: 0.95rem; }
        .btn-round { border-radius: 50px; }
        .btn-undi { background: #ffcb3d; border: none; color: #2b1700; font-weight: 700; }
        .btn-export { background: #1a7d4d; border: none; color: #fff; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="top-brand">
        <img src="/images/rat_k2ms.png" alt="RAT K2MS Logo">
        <div><h1>PENGUNDIAN BEASISWA</h1></div>
    </div>

    <div class="header-panel shadow-sm">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="fw-bold">Pilih Plant</label>
                <select id="pilih_plant" class="form-select btn-round" onchange="onPlantChange()">
                    <option value="">-- Pilih Plant --</option>
                    @foreach($plants as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_plant }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="fw-bold">Kategori & Kuota</label>
                <div id="plantQuotaInfo" class="form-control btn-round text-start" style="min-height: 58px; white-space: pre-line;">
                    Pilih plant...
                </div>
            </div>
            <div class="col-md-3 d-grid gap-2">
                <button onclick="mulaiKocok()" id="btnMulai" class="btn btn-undi w-100 btn-round fw-bold">MULAI UNDIAN</button>
                <button onclick="exportCSV()" id="btnExport" class="btn btn-export w-100 btn-round fw-bold d-none">EXPORT CSV</button>
                <button onclick="tampilkanModalPemenang()" id="btnListPemenang" class="btn btn-info w-100 btn-round fw-bold d-none">LIHAT LIST</button>
            </div>
        </div>
    </div>

    <div class="grid-container" id="boxesContainer">
        @foreach($kategori as $k)
        @php
            $slug = preg_replace('/[^a-z0-9_]/', '', strtolower(str_replace(' ', '_', $k->jenjang_sekolah)));
        @endphp
        <div class="box-undi shadow-sm d-none" id="box-{{ $slug }}">
            <h5 class="fw-bold text-center border-bottom pb-2">JENJANG {{ $k->jenjang_sekolah }}</h5>
            <div id="list-{{ $slug }}" class="mt-3"></div>
            <div id="debug-{{ $slug }}" class="debug-info"></div>
        </div>
        @endforeach
    </div>
</div>

<div class="modal fade" id="modalPemenang" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Daftar Pemenang Beasiswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-secondary-subtle">
                <div id="capture-area">
                    <div class="download-header">
                        <h2 id="download-plant-title">PLANT NAME</h2>
                        <p>DAFTAR PEMENANG BEASISWA RAT K2MS - {{ date('Y') }}</p>
                    </div>
                    <div id="listPemenangBody"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onclick="downloadSebagaiGambar()">
                    <i class="fas fa-image"></i> DOWNLOAD JPG
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    const quotaData = @json($quotaData);
    let currentWinners = [];
    let currentPlantName = '';

    function onPlantChange() {
        const plantId = document.getElementById('pilih_plant').value;
        document.querySelectorAll('.box-undi').forEach(box => box.classList.add('d-none'));
        if (!plantId) return;
        const quotas = quotaData[plantId] || [];
        document.getElementById('plantQuotaInfo').textContent = quotas.map(q => `${q.jenjang_sekolah}: ${q.jumlah_slot}`).join('\n');
        quotas.forEach(q => {
            const box = document.getElementById('box-' + q.jenjang_slug);
            if (box) box.classList.remove('d-none');
        });
    }

    async function mulaiKocok() {
        const plantId = document.getElementById('pilih_plant').value;
        if (!plantId) return alert("Pilih Plant Terlebih Dahulu!");

        const quotas = (quotaData[plantId] || []).filter(q => q.jumlah_slot > 0);
        currentWinners = [];
        currentPlantName = document.querySelector('#pilih_plant option:checked').textContent;

        document.getElementById('btnMulai').disabled = true;
        document.querySelectorAll('[id^="list-"]').forEach(el => el.innerHTML = '');

        for (const target of quotas) {
            const container = document.getElementById('list-' + target.jenjang_slug);
            const box = document.getElementById('box-' + target.jenjang_slug);
            const debug = document.getElementById('debug-' + target.jenjang_slug);

            // 1. AMBIL DATA DULU (PENTING!)
            const [resWin, resPool] = await Promise.all([
                fetch(`/beasiswa/kocok?jenjang=${encodeURIComponent(target.jenjang_sekolah)}&jumlah=${target.jumlah_slot}&plant_id=${plantId}`).then(r => r.json()),
                fetch(`/beasiswa/peserta/list?jenjang=${encodeURIComponent(target.jenjang_sekolah)}&plant_id=${plantId}`).then(r => r.json())
            ]);

            // Tampilkan jumlah data yang ditarik untuk pengecekan
            debug.innerText = `Data Pool: ${resPool.length} | Winner: ${resWin.length}`;

            // 2. AKTIFKAN BOX
            if (box) {
                box.classList.remove('d-none');
                box.classList.add('box-active');
                box.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            await new Promise(r => setTimeout(r, 500));

            const poolData = (resPool && resPool.length > 0) ? resPool : resWin;

            // 3. JALANKAN ANIMASI UNTUK SETIAP PEMENANG
            for (const winner of resWin) {
                const div = document.createElement('div');
                div.className = 'winner-item';
                container.appendChild(div);

                const duration = 4000;
                const startTime = Date.now();
                let lastIdx = -1;

                while (Date.now() - startTime < duration) {
                    let randomIdx;

                    if (poolData.length > 1) {
                        do {
                            randomIdx = Math.floor(Math.random() * poolData.length);
                        } while (randomIdx === lastIdx);
                        lastIdx = randomIdx;
                    } else {
                        randomIdx = 0;
                    }

                    const temp = poolData[randomIdx];
                    div.innerHTML = `
                        <span class="running-text"><i class="fas fa-sync fa-spin"></i> SEDANG MENGACAK...</span><br>
                        <strong style="color: #ff5722; font-size: 1.1rem;">${temp.nama_anak}</strong><br>
                        <small class="text-secondary">${temp.nama_orang_tua}</small>
                    `;
                    await new Promise(r => setTimeout(r, 50));
                }

                // HASIL AKHIR (Nama Anak, Nama Orang Tua (NPK))
                div.innerHTML = `
                    <span class="text-success fw-bold"><i class="fas fa-check-circle"></i> PEMENANG:</span><br>
                    <strong style="font-size: 1.25rem;">${winner.nama_anak}</strong><br>
                    <small>${winner.nama_orang_tua} (${winner.npk_orang_tua})</small>
                `;
                div.style.borderLeft = "6px solid #28a745";
                div.style.backgroundColor = "#f8fff9";

                currentWinners.push({...winner, plant: currentPlantName});
                await new Promise(r => setTimeout(r, 1000));
            }

            box.classList.remove('box-active');
            await new Promise(r => setTimeout(r, 1000));
        }

        document.getElementById('btnMulai').disabled = false;
        document.getElementById('btnExport').classList.remove('d-none');
        document.getElementById('btnListPemenang').classList.remove('d-none');
        tampilkanModalPemenang();
    }

    function tampilkanModalPemenang() {
        document.getElementById('download-plant-title').textContent = currentPlantName.toUpperCase();
        const container = document.getElementById('listPemenangBody');
        container.innerHTML = '';
        const grouped = currentWinners.reduce((acc, obj) => {
            acc[obj.jenjang_sekolah] = acc[obj.jenjang_sekolah] || [];
            acc[obj.jenjang_sekolah].push(obj);
            return acc;
        }, {});

        for (const jenjang in grouped) {
            container.innerHTML += `<div class="jenjang-section">${jenjang}</div>`;
            grouped[jenjang].forEach((w, i) => {
                container.innerHTML += `
                    <div class="pemenang-row">
                        <span>${i+1}. <b>${w.nama_anak}</b></span>
                        <span class="text-muted">${w.nama_orang_tua} (${w.npk_orang_tua})</span>
                    </div>`;
            });
        }
        new bootstrap.Modal(document.getElementById('modalPemenang')).show();
    }

    async function downloadSebagaiGambar() {
        const element = document.getElementById('capture-area');
        const canvas = await html2canvas(element, { scale: 2 });
        const link = document.createElement('a');
        link.download = `Pemenang_${currentPlantName}.jpg`;
        link.href = canvas.toDataURL("image/jpeg");
        link.click();
    }

    function exportCSV() {
        let csv = 'Nama Anak,Jenjang,Orang Tua,NPK,Plant\n';
        currentWinners.forEach(w => {
            csv += `"${w.nama_anak}","${w.jenjang_sekolah}","${w.nama_orang_tua}","${w.npk_orang_tua}","${w.plant}"\n`;
        });
        const blob = new Blob([csv], { type: 'text/csv' });
        const a = document.createElement('a');
        a.href = window.URL.createObjectURL(blob);
        a.download = `Pemenang_${currentPlantName}.csv`;
        a.click();
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
