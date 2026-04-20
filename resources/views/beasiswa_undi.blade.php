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
        .top-brand img { height: 68px; width: auto; border-radius: 18px; box-shadow: 0 12px 30px rgba(0,0,0,0.25); }
        .top-brand h1 { margin: 0; font-size: 2rem; letter-spacing: 2px; color: #ffd966; text-shadow: 0 4px 18px rgba(0,0,0,0.35); }
        .top-brand p { margin: 0; color: #dfe1e5; font-size: 0.95rem; }
        .control-panel { background: rgba(255,255,255,0.94); backdrop-filter: blur(12px); border-radius: 24px; padding: 24px; border: 1px solid rgba(255,255,255,0.7); box-shadow: 0 28px 60px rgba(0,0,0,0.18); margin-top: 10px; }
        .grid-container { display: grid; grid-template-columns: repeat(4, minmax(240px, 1fr)); gap: 16px; margin-top: 24px; }
        .box-undi { background: rgba(255,255,255,0.92); border-radius: 28px; min-height: 260px; border: 1px solid rgba(255,255,255,0.85); padding: 20px; box-shadow: 0 14px 32px rgba(0,0,0,0.12); transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .box-undi:hover { transform: translateY(-3px); box-shadow: 0 18px 38px rgba(0,0,0,0.16); }
        .winner-item { background: linear-gradient(135deg, rgba(255,255,255,0.98), rgba(253,245,214,0.9)); border-left: 6px solid #ffd54f; padding: 22px 20px; margin-bottom: 16px; box-shadow: 0 18px 34px rgba(0,0,0,0.12); border-radius: 22px; opacity: 0; transform: translateY(20px) scale(0.96); animation: revealName 0.45s forwards ease-out; }
        .winner-item strong { display: block; font-size: 1.35rem; letter-spacing: 0.5px; color: #1f1a16; }
        .winner-item small { display: block; color: #5b5448; margin-top: 8px; font-size: 0.95rem; }
        .text-brown { color: #fff; }
        .btn-round { border-radius: 50px; }
        .btn-undi { background: #ffcb3d; border: none; color: #2b1700; font-weight: 700; }
        .btn-undi:hover { background: #e5b62a; }
        .btn-export { background: #1a7d4d; border: none; color: #fff; }
        .btn-export:hover { background: #166a43; }
        .header-panel { background: rgba(28, 33, 50, 0.92); border-radius: 30px; padding: 20px; border: 1px solid rgba(255,255,255,0.18); box-shadow: 0 15px 42px rgba(0,0,0,0.28); }
        .header-panel label { color: #f0f0f0; font-weight: 600; }
        .header-panel .form-select, .header-panel .form-control { border-radius: 16px; min-height: 54px; }
        .header-panel .form-control { background: rgba(255,255,255,0.95); }
        .header-panel .text-start { min-height: 56px; }
        .header-panel .btn-undi, .header-panel .btn-export { min-height: 50px; font-size: 0.95rem; }
        .header-panel .btn-undi { box-shadow: 0 12px 25px rgba(255,203,61,0.32); }
        .header-panel .btn-export { box-shadow: 0 12px 25px rgba(26,125,77,0.28); }
        #boxesContainer { margin-bottom: 30px; }
        .box-undi h5 { letter-spacing: 1px; }
        .box-undi .text-muted { color: #837668 !important; }
        .reveal-show { opacity: 1 !important; transform: translateY(0) scale(1) !important; }
        @keyframes revealName { to { opacity: 1; transform: translateY(0) scale(1); } }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="top-brand">
        <img src="/images/rat_k2ms.png" alt="RAT K2MS Logo">
        <div>
            <h1>PENGUNDIAN BEASISWA</h1>
            <p class="text-white-50 mb-0">Sistem undian beasiswa berbasis plant dan jenjang</p>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const quotaData = @json($quotaData);
    let currentWinners = [];
    let currentPlantName = '';

    function onPlantChange() {
        const plantId = document.getElementById('pilih_plant').value;
        const infoBox = document.getElementById('plantQuotaInfo');

        document.querySelectorAll('.box-undi').forEach(box => box.classList.add('d-none'));
        document.querySelectorAll('[id^="list-"]').forEach(list => list.innerHTML = '');

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
                if (q.jumlah_slot > 0) {
                    box.classList.remove('d-none');
                }
            }
        });
    }

    function animateWinner(div, delay) {
        div.style.animationDelay = `${delay}ms`;
        div.classList.add('reveal-show');
        playRevealSound();
    }

    function mulaiKocok() {
        const plantId = document.getElementById('pilih_plant').value;
        if (!plantId) {
            return Swal.fire('Error', 'Pilih plant terlebih dahulu!', 'error');
        }

        const quotas = quotaData[plantId] || [];
        const targets = quotas.filter(q => q.jumlah_slot > 0);
        if (targets.length === 0) {
            return Swal.fire('Info', 'Tidak ada kuota tersedia untuk plant ini.', 'info');
        }

        currentWinners = [];
        currentPlantName = document.querySelector('#pilih_plant option:checked').textContent;
        document.getElementById('btnMulai').disabled = true;

        const fetches = targets.map(q =>
            fetch(`/beasiswa/kocok?jenjang=${encodeURIComponent(q.jenjang_sekolah)}&jumlah=${q.jumlah_slot}&plant_id=${plantId}`)
                .then(res => res.json())
                .then(data => ({ q, data }))
        );

        Promise.all(fetches).then(results => {
            let hasAny = false;
            let allWinners = [];
            let totalAnimations = 0;

            results.forEach(result => {
                const container = document.getElementById('list-' + result.q.jenjang_slug);
                container.innerHTML = '';

                if (result.data.length === 0) {
                    const div = document.createElement('div');
                    div.className = 'text-muted';
                    div.textContent = 'Tidak ada peserta tersedia untuk jenjang ini.';
                    container.appendChild(div);
                    return;
                }

                hasAny = true;
                result.data.forEach(item => {
                    currentWinners.push({
                        plant: currentPlantName,
                        nama_anak: item.nama_anak,
                        nama_orang_tua: item.nama_orang_tua,
                        npk_orang_tua: item.npk_orang_tua,
                        jenjang_sekolah: item.jenjang_sekolah,
                    });

                    const div = document.createElement('div');
                    div.className = 'winner-item';
                    div.innerHTML = `<strong>${item.nama_anak}</strong><br><small>${item.nama_orang_tua} (${item.npk_orang_tua})</small>`;
                    container.appendChild(div);
                    allWinners.push(div);
                });
            });

            if (hasAny) {
                // Animate winners one by one
                allWinners.forEach((div, index) => {
                    setTimeout(() => {
                        animateWinner(div, 0);
                        totalAnimations++;
                        if (totalAnimations === allWinners.length) {
                            // All animations done, show success alert
                            setTimeout(() => {
                                Swal.fire('Selesai', 'Undian beasiswa selesai dijalankan.', 'success');
                                document.getElementById('btnExport').classList.remove('d-none');
                            }, 500);
                        }
                    }, index * 800);
                });
            } else {
                document.getElementById('btnMulai').disabled = false;
                Swal.fire('Info', 'Tidak ada pemenang yang terpilih.', 'info');
            }

            document.getElementById('btnMulai').disabled = false;
        }).catch(() => {
            document.getElementById('btnMulai').disabled = false;
            Swal.fire('Error', 'Terjadi kesalahan saat mengundi.', 'error');
        });
    }

    function exportPemenang() {
        if (currentWinners.length === 0) {
            return Swal.fire('Info', 'Belum ada pemenang untuk diekspor.', 'info');
        }

        const rows = [
            ['Plant', 'Nama Anak', 'Nama Orang Tua', 'NPK Orang Tua', 'Jenjang']
        ];
        currentWinners.forEach(item => {
            rows.push([item.plant, item.nama_anak, item.nama_orang_tua, item.npk_orang_tua, item.jenjang_sekolah]);
        });

        const csvContent = rows.map(r => r.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(',')).join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const filename = `pemenang_beasiswa_${currentPlantName.replace(/\s+/g, '_')}_${new Date().toISOString().slice(0,19).replace(/[:T]/g, '-')}.csv`;
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }
</script>
</body>
</html>
