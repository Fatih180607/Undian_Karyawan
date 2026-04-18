<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Undian Beasiswa - RAT K2MS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f4ede7; font-family: 'Segoe UI', sans-serif; }
        .control-panel { background: white; border-radius: 20px; padding: 20px; border: 3px solid #8d7d77; margin-top: 20px; }
        .grid-container { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 30px; }
        .box-undi { background: white; border-radius: 20px; min-height: 300px; border: 2px solid #ddd; padding: 20px; }
        .winner-item { background: #fff; border-left: 5px solid #ffc107; padding: 10px; margin-bottom: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); animation: fadeIn 0.5s; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .btn-round { border-radius: 50px; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="text-center mb-4">
        <h2 class="fw-bold text-brown">PENGUNDIAN BEASISWA</h2>
    </div>

    <div class="control-panel shadow-sm">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="fw-bold">Pilih Jenjang</label>
                <select id="pilih_jenjang" class="form-select btn-round">
                    <option value="">-- Pilih --</option>
                    @foreach($kategori as $k)
                    <option value="{{ $k->jenjang_sekolah }}">{{ $k->jenjang_sekolah }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="fw-bold">Jumlah Pemenang</label>
                <input type="number" id="jumlah_pemenang" class="form-control btn-round" placeholder="0">
            </div>
            <div class="col-md-4">
                <button onclick="mulaiKocok()" id="btnMulai" class="btn btn-warning w-100 btn-round fw-bold">MULAI ACAK</button>
            </div>
        </div>
    </div>

    <div class="grid-container">
        @foreach(['SD', 'SMP', 'SMA', 'KULIAH'] as $j)
        <div class="box-undi shadow-sm" id="box-{{ $j }}">
            <h5 class="fw-bold text-center border-bottom pb-2">JENJANG {{ $j }}</h5>
            <div id="list-{{ $j }}" class="mt-3"></div>
        </div>
        @endforeach
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function mulaiKocok() {
    const jenjang = document.getElementById('pilih_jenjang').value;
    const jumlah = document.getElementById('jumlah_pemenang').value;

    if(!jenjang || !jumlah) return Swal.fire('Error', 'Isi data dulu!', 'error');

    document.getElementById('btnMulai').disabled = true;

    fetch(`/beasiswa/kocok?jenjang=${jenjang}&jumlah=${jumlah}`)
    .then(res => res.json())
    .then(data => {
        if(data.length == 0) {
            Swal.fire('Info', 'Tidak ada peserta tersedia', 'info');
            document.getElementById('btnMulai').disabled = false;
            return;
        }

        let i = 0;
        const container = document.getElementById('list-' + jenjang);
        const interval = setInterval(() => {
            if(i < data.length) {
                const div = document.createElement('div');
                div.className = 'winner-item';
                div.innerHTML = `<strong>${data[i].nama_anak}</strong><br><small>${data[i].nama_orang_tua} (${data[i].npk_orang_tua})</small>`;
                container.prepend(div);
                i++;
            } else {
                clearInterval(interval);
                document.getElementById('btnMulai').disabled = false;
                Swal.fire('Selesai', 'Pemenang telah terpilih', 'success');
            }
        }, 1000);
    });
}
</script>
</body>
</html>
