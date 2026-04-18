<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Undian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .nav-admin { background: #2d3436; color: white; padding: 15px 0; margin-bottom: 25px; }
        .card { border-radius: 18px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .btn-round { border-radius: 100px; font-weight: bold; }
        .prize-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 15px; }
        .prize-card { background: white; border-radius: 15px; border: 1px solid #dee2e6; overflow: hidden; position: relative; }
        .img-container { width: 100%; aspect-ratio: 1/1; background: #f8f9fa; display: flex; align-items: center; justify-content: center; }
        .prize-img { width: 100%; height: 100%; object-fit: contain; padding: 10px; }
        .btn-delete-prize { position: absolute; top: 5px; right: 5px; background: rgba(255, 71, 87, 0.9); color: white; border: none; border-radius: 50%; width: 22px; height: 22px; font-size: 10px; display: flex; align-items: center; justify-content: center; text-decoration: none; }
    </style>
</head>
<body>

<div class="nav-admin shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <h4 class="mb-0 fw-bold">Admin Undian</h4>
        <button onclick="gasUndian()" class="btn btn-warning btn-round px-4 shadow-sm">
            <i class="fas fa-play me-1"></i> BUKA LAYAR UNDIAN
        </button>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-4">
            <div class="card p-4 mb-3">
                <h6 class="fw-bold text-primary mb-3">Tambah Peserta</h6>
                <form action="/admin/add-employee" method="POST">
                    @csrf
                    <input type="text" name="employee_number" class="form-control btn-round mb-2" placeholder="NPK" required>
                    <input type="text" name="employee_name" class="form-control btn-round mb-3" placeholder="Nama" required>
                    <button class="btn btn-primary w-100 btn-round">SIMPAN</button>
                </form>
            </div>

            <div class="card p-4 mb-3">
                <h6 class="fw-bold text-info mb-3"><i class="fas fa-file-csv me-2"></i>Import CSV (Excel)</h6>
                <form action="/admin/import-employees" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-2">
                        <small class="text-muted">Upload File harus .csv</small>
                    </div>
                    <input type="file" name="file_excel" class="form-control mb-2" accept=".csv" required>
                    <button type="submit" class="btn btn-info w-100 btn-round text-white">UPLOAD DATA</button>
                </form>
            </div>

            <div class="card p-4">
                <h6 class="fw-bold text-success mb-3">Tambah Hadiah</h6>
                <form action="/admin/add-prize" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="text" name="nama_hadiah" class="form-control btn-round mb-2" placeholder="Nama Barang" required>
                    <input type="file" name="foto" class="form-control mb-3" required>
                    <button class="btn btn-success w-100 btn-round">UPLOAD HADIAH</button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card p-4 mb-3 border-warning" style="background: #fff9e6;">
                <h6 class="fw-bold text-warning">PILIH HADIAH UNTUK DIUNDI</h6>
                <input type="text" id="input_hadiah_manual" class="form-control form-control-lg btn-round text-center" placeholder="Klik tombol 'PILIH' pada hadiah di bawah...">
            </div>

            <div class="card p-4 mb-3">
                <div class="prize-container">
                    @foreach($prizes as $p)
                    <div class="prize-card shadow-sm text-center">
                        <a href="{{ url('/admin/delete-prize/'.$p->id) }}" class="btn-delete-prize" onclick="return confirm('Hapus hadiah?')">
                            <i class="fas fa-times"></i>
                        </a>
                        <div class="img-container"><img src="/images/{{ $p->foto_hadiah }}" class="prize-img"></div>
                        <div class="p-2">
                            <div class="small fw-bold">{{ $p->nama_hadiah }}</div>
                            <button class="btn btn-sm btn-primary btn-round w-100 mt-2" onclick="pilihHadiah('{{ $p->nama_hadiah }}')">PILIH</button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card p-4">
                <table class="table table-hover">
                    <thead><tr><th>NPK</th><th>Nama</th><th class="text-center">Aksi</th></tr></thead>
                    <tbody>
                        @foreach($employees as $e)
                        <tr>
                            <td>{{ $e->employee_number }}</td>
                            <td>{{ $e->employee_name }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary border-0" onclick="openEditModal('{{ $e->id }}', '{{ $e->employee_number }}', '{{ $e->employee_name }}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="{{ url('/admin/delete/'.$e->id) }}" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Hapus peserta?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-body p-4">
                <h5 class="fw-bold mb-3">Edit Data</h5>
                <form id="editForm" method="POST">
                    @csrf
                    <input type="text" name="employee_number" id="edit_npk" class="form-control btn-round mb-2" required>
                    <input type="text" name="employee_name" id="edit_nama" class="form-control btn-round mb-3" required>
                    <button type="submit" class="btn btn-primary w-100 btn-round">UPDATE</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function pilihHadiah(nama) { document.getElementById('input_hadiah_manual').value = nama; }
    
    function gasUndian() {
        const h = document.getElementById('input_hadiah_manual').value;
        if (!h) return alert("Pilih hadiah dulu!");
        window.open("{{ url('/undian') }}?hadiah=" + encodeURIComponent(h), '_blank');
    }

    function openEditModal(id, npk, nama) {
        document.getElementById('editForm').action = '/admin/update/' + id;
        document.getElementById('edit_npk').value = npk;
        document.getElementById('edit_nama').value = nama;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }
</script>
</body>
</html>