<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Undian Beasiswa - RAT K2MS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        html, body { min-height: 100%; margin: 0; font-family: 'Segoe UI', sans-serif; background: url('/images/bg-undian.png') no-repeat center center fixed; background-size: cover; color: #2f2b24; }
        body { background-color: rgba(0,0,0,0.15); position: relative; }
        body::before { content: ''; position: fixed; inset: 0; background: rgba(13, 17, 31, 0.35); pointer-events: none; }
        .container { position: relative; z-index: 2; }
        .top-brand { display: flex; align-items: center; justify-content: center; gap: 16px; margin-bottom: 20px; }
        .top-brand img { height: 72px; width: auto; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.25); }
        .top-brand h1 { margin: 0; font-size: 2.4rem; letter-spacing: 2px; color: #ffe599; text-shadow: 0 3px 14px rgba(0,0,0,0.45); }
        .grid-container { display: grid; grid-template-columns: repeat(2, minmax(280px, 1fr)); gap: 24px; margin-top: 30px; }
        .box-undi { background: rgba(255,255,255,0.92); border-radius: 28px; min-height: 320px; border: 1px solid rgba(255,255,255,0.85); padding: 24px; box-shadow: 0 18px 40px rgba(0,0,0,0.12); transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .winner-item { background: #fff; border-left: 6px solid #f8b500; padding: 18px 16px; margin-bottom: 14px; box-shadow: 0 10px 20px rgba(0,0,0,0.08); opacity: 0; transform: translateY(18px) scale(0.98); transition: all 0.5s ease-out; }
        .winner-item strong { display: block; font-size: 1.05rem; color: #2d2a28; }
        .winner-item small { color: #6b635c; }
        .btn-round { border-radius: 50px; }
        .btn-undi { background: #ffcb3d; border: none; color: #2b1700; font-weight: 700; }
        .btn-undi:hover { background: #e5b62a; }
        .btn-export { background: #1a7d4d; border: none; color: #fff; }
        .header-panel { background: rgba(20, 23, 35, 0.72); border-radius: 30px; padding: 26px; border: 1px solid rgba(255,255,255,0.15); box-shadow: 0 15px 40px rgba(0,0,0,0.25); }
        .header-panel label { color: #f5f3ef; }
        .header-panel .form-select, .header-panel .form-control { border-radius: 18px; }
        .header-panel .form-control { min-height: 58px; background: rgba(255,255,255,0.95); }
        .reveal-show { opacity: 1 !important; transform: translateY(0) scale(1) !important; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="top-brand">
        <img src="/images/rat_k2ms.png" alt="RAT K2MS Logo">
        <div>
            <h1>PENGUNDIAN BEASISWA</h1>
        </div>
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
                    Pilih plant untuk melihat kuota.
                </div>
            </div>
            <div class="col-md-3 d-grid gap-2">
                <button onclick="mulaiKocok()" id="btnMulai" class="btn btn-undi w-100 btn-round fw-bold">MULAI UNDIAN</button>
                <button onclick="exportPemenang()" id="btnExport" class="btn btn-export w-100 btn-round fw-bold d-none">EXPORT PEMENANG</button>
            </div>
        </div>
    </div>

    <div class="grid-container" id="boxesContainer">
        @foreach($kategori as $k)
        @php
            $slug = strtolower(str_replace(' ', '_', $k->jenjang_sekolah));
            $slug = preg_replace('/[^a-z0-9_\-]/', '', $slug);
        @endphp
        <div class="box-undi shadow-sm d-none" id="box-{{ $slug }}">
            <h5 class="fw-bold text-center border-bottom pb-2">JENJANG {{ $k->jenjang_sekolah }}</h5>
            <div class="text-center text-muted mb-2" id="quota-{{ $slug }}">Kuota: 0</div>
            <div id="list-{{ $slug }}" class="mt-3"></div>
        </div>
        @endforeach
    </div>
</div>

<script>
    const quotaData = @json($quotaData);
    let currentWinners = [];
    let currentPlantName = '';

    function onPlantChange() {
        const plantId = document.getElementById('pilih_plant').value;
        const infoBox = document.getElementById('plantQuotaInfo');

        document.querySelectorAll('.box-undi').forEach(box => box.classList.add('d-none'));
        document.querySelectorAll('[id^="list-"]').forEach(list => list.innerHTML = '');
        document.getElementById('btnExport').classList.add('d-none');

        if (!plantId) {
            infoBox.textContent = 'Pilih plant untuk melihat kuota.';
            return;
        }

        const quotas = quotaData[plantId] || [];
        if (quotas.length === 0) {
            infoBox.textContent = 'Belum ada kuota di plant ini.';
            return;
        }

        const lines = quotas.map(q => `${q.jenjang_sekolah}: ${q.jumlah_slot}`).join('\n');
        infoBox.textContent = lines;

        quotas.forEach(q => {
            const box = document.getElementById('box-' + q.jenjang_slug);
            const quotaLabel = document.getElementById('quota-' + q.jenjang_slug);
            if (box) {
                quotaLabel.textContent = 'Kuota: ' + q.jumlah_slot;
                if (q.jumlah_slot > 0) box.classList.remove('d-none');
            }
        });
    }

    const sleep = (ms) => new Promise(resolve => setTimeout(resolve, ms));

    async function mulaiKocok() {
        const plantId = document.getElementById('pilih_plant').value;
        if (!plantId) return;

        const quotas = quotaData[plantId] || [];
        const targets = quotas.filter(q => q.jumlah_slot > 0);
        if (targets.length === 0) return;

        currentWinners = [];
        currentPlantName = document.querySelector('#pilih_plant option:checked').textContent;
        document.getElementById('btnMulai').disabled = true;
        document.getElementById('btnExport').classList.add('d-none');

        const jenjangOrder = ['SD', 'SMP', 'SMA', 'SMK'];
        const sortedTargets = targets.sort((a, b) => {
            const indexA = jenjangOrder.findIndex(j => a.jenjang_sekolah.toUpperCase().includes(j));
            const indexB = jenjangOrder.findIndex(j => b.jenjang_sekolah.toUpperCase().includes(j));
            return indexA - indexB;
        });

        // Bersihkan list lama
        sortedTargets.forEach(t => {
            const list = document.getElementById('list-' + t.jenjang_slug);
            if (list) list.innerHTML = '';
        });

        try {
            for (const target of sortedTargets) {
                const response = await fetch(`/beasiswa/kocok?jenjang=${encodeURIComponent(target.jenjang_sekolah)}&jumlah=${target.jumlah_slot}&plant_id=${plantId}`);
                const winners = await response.json();

                if (winners.length > 0) {
                    const container = document.getElementById('list-' + target.jenjang_slug);
                    const box = document.getElementById('box-' + target.jenjang_slug);

                    box.classList.remove('d-none');

                    const jenjangHeader = document.createElement('div');
                    jenjangHeader.className = 'text-center fw-bold text-primary mb-3';
                    jenjangHeader.innerHTML = `<i class="fas fa-graduation-cap me-2"></i>Pemenang ${target.jenjang_sekolah}`;
                    container.appendChild(jenjangHeader);

                    // LOOP NAMA PESERTA
                    for (const item of winners) {
                        currentWinners.push({
                            plant: currentPlantName,
                            nama_anak: item.nama_anak,
                            nama_orang_tua: item.nama_orang_tua,
                            npk_orang_tua: item.npk_orang_tua,
                            jenjang_sekolah: item.jenjang_sekolah,
                        });

                        const div = document.createElement('div');
                        div.className = 'winner-item';
                        div.innerHTML = `<strong>${item.nama_anak}</strong><small>${item.nama_orang_tua} (${item.npk_orang_tua})</small>`;
                        container.appendChild(div);

                        // Munculkan animasi
                        await sleep(100);
                        div.classList.add('reveal-show');

                        // JEDA ANTAR NAMA (DIPERLAMA JADI 3 DETIK)
                        await sleep(3000);
                    }

                    // JEDA SINGKAT SEBELUM PINDAH JENJANG (CUMA 1 DETIK)
                    await sleep(1000);
                }
            }

            document.getElementById('btnExport').classList.remove('d-none');
            document.getElementById('btnMulai').disabled = false;

        } catch (error) {
            console.error("Error undian:", error);
            document.getElementById('btnMulai').disabled = false;
        }
    }

    function exportPemenang() {
        if (currentWinners.length === 0) return;
        const rows = [['Plant', 'Nama Anak', 'Nama Orang Tua', 'NPK Orang Tua', 'Jenjang']];
        currentWinners.forEach(item => {
            rows.push([item.plant, item.nama_anak, item.nama_orang_tua, item.npk_orang_tua, item.jenjang_sekolah]);
        });
        const csvContent = rows.map(r => r.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(',')).join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const filename = `pemenang_beasiswa_${currentPlantName.replace(/\s+/g, '_')}_${new Date().toISOString().slice(0,10)}.csv`;
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.click();
    }
</script>
</body>
</html>
