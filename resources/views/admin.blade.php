<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Undian - RAT K2MS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }

        /* Navbar Styling */
        .navbar-admin { background: #2d3436; padding: 10px 0; }
        .nav-link { color: rgba(255,255,255,0.7) !important; font-weight: 500; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: white !important; }
        .nav-link.active { border-bottom: 2px solid #ffc107; }

        .card { border-radius: 18px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .btn-round { border-radius: 100px; font-weight: bold; }
        .prize-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 15px; }
        .prize-card { background: white; border-radius: 15px; border: 1px solid #dee2e6; overflow: hidden; position: relative; }
        .img-container { width: 100%; aspect-ratio: 1/1; background: #f8f9fa; display: flex; align-items: center; justify-content: center; }
        .prize-img { width: 100%; height: 100%; object-fit: contain; padding: 10px; }
        .btn-delete-prize { position: absolute; top: 5px; right: 5px; background: rgba(255, 71, 87, 0.9); color: white; border: none; border-radius: 50%; width: 22px; height: 22px; font-size: 10px; display: flex; align-items: center; justify-content: center; text-decoration: none; }
        .plant-select { background-color: #f8f9fa; border: 2px solid #6c757d; color: #495057; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-admin shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">RAT K2MS <span class="text-warning">SYSTEM</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('/') ? 'active fw-bold' : '' }}" href="/">
                        <i class="fas fa-gift me-1"></i> Dashboard Doorprize
                    </a>
                </li>
                <li class="nav-item ms-lg-3">
                    <a class="nav-link {{ Request::is('beasiswa-admin*') ? 'active fw-bold' : '' }}" href="/beasiswa-admin">
                        <i class="fas fa-graduation-cap me-1"></i> Dashboard Beasiswa
                    </a>
                </li>
                <li class="nav-item ms-lg-3">
                    <a class="nav-link {{ Request::is('setting*') ? 'active fw-bold' : '' }}" href="/setting">
                        <i class="fas fa-cog me-1"></i> Settings
                    </a>
                </li>
                <li class="nav-item ms-lg-3">
                    <a class="nav-link {{ Request::is('about') ? 'active fw-bold' : '' }}" href="/about"
                       style="{{ Request::is('about') ? 'border-bottom: 2px solid #ffc107; color:white;' : '' }}">
                        <i class="fas fa-info-circle me-1"></i> Tentang
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    @if(session('success') || session('warning') || session('error'))
    <div class="row mb-3">
        <div class="col-12">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <div class="card p-4 mb-3 text-center" style="background: #2d3436; color: white;">
                <h5 class="fw-bold mb-0">DOORPRIZE MODE</h5>
                <small class="text-secondary">Kelola hadiah & peserta utama</small>
            </div>

            <div class="card p-4 mb-3">
                <h6 class="fw-bold text-primary mb-3">Tambah Peserta Doorprize</h6>
                <form action="/admin/add-employee" method="POST">
                    @csrf
                    <input type="text" name="employee_number" class="form-control btn-round mb-2" placeholder="NPK" required>
                    <input type="text" name="employee_name" class="form-control btn-round mb-2" placeholder="Nama" required>
                    <select name="plant_id" class="form-control btn-round mb-3 plant-select" required>
                        <option value="">Pilih Plant</option>
                        @foreach($plants as $plant)
                        <option value="{{ $plant->id }}">{{ $plant->nama_plant }}</option>
                        @endforeach
                    </select>
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
                <div class="d-flex gap-2">
                    <input type="text" id="input_hadiah_manual" class="form-control form-control-lg btn-round text-center" placeholder="Klik tombol 'PILIH' pada hadiah di bawah...">
                    <button class="btn btn-warning btn-round px-4" onclick="gasUndian()">Undi Disini</button>
                </div>
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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-secondary">Data Peserta Terdaftar</h6>
                    <form action="/admin/delete-all-employees" method="POST" onsubmit="return confirm('PERHATIAN: Ini akan menghapus SELURUH data peserta. Anda yakin?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger btn-round px-3">
                            <i class="fas fa-trash-alt me-1"></i> Hapus Semua
                        </button>
                    </form>
                </div>

                <table class="table table-hover">
                    <thead><tr><th>NPK</th><th>Nama</th><th>Plant</th><th class="text-center">Aksi</th></tr></thead>
                    <tbody>
                        @foreach($employees as $e)
                        <tr>
                            <td>{{ $e->employee_number }}</td>
                            <td>{{ $e->employee_name }}</td>
                            <td>{{ $e->plant->nama_plant ?? 'N/A' }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary border-0" onclick="openEditModal('{{ $e->id }}', '{{ $e->employee_number }}', '{{ $e->employee_name }}', '{{ $e->plant_id }}')">
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
                <h5 class="fw-bold mb-3">Edit Data Peserta</h5>
                <form id="editForm" method="POST">
                    @csrf
                    <input type="text" name="employee_number" id="edit_npk" class="form-control btn-round mb-2" required>
                    <input type="text" name="employee_name" id="edit_nama" class="form-control btn-round mb-2" required>
                    <select name="plant_id" id="edit_plant" class="form-control btn-round mb-3 plant-select" required>
                        <option value="">Pilih Plant</option>
                        @foreach($plants as $plant)
                        <option value="{{ $plant->id }}">{{ $plant->nama_plant }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary w-100 btn-round">UPDATE</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="plantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-body p-4">
                <h5 class="fw-bold mb-3">Pilih Plant untuk Undian</h5>
                <select id="select_plant" class="form-control btn-round mb-3 plant-select" required>
                    <option value="" selected disabled>Pilih Plant</option>
                    <option value="all">Semua Plant</option>
                    @foreach($plants as $plant)
                    <option value="{{ $plant->id }}">{{ $plant->nama_plant }}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-warning w-100 btn-round" onclick="startUndian()">MULAI UNDIAN</button>
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
        new bootstrap.Modal(document.getElementById('plantModal')).show();
    }

    function openEditModal(id, npk, nama, plant_id) {
        document.getElementById('editForm').action = '/admin/update/' + id;
        document.getElementById('edit_npk').value = npk;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_plant').value = plant_id;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }

    function startUndian() {
        const h = document.getElementById('input_hadiah_manual').value;
        const p = document.getElementById('select_plant').value;
        if (!p) return alert("Pilih plant dulu!");
        window.open("{{ url('/undian') }}?hadiah=" + encodeURIComponent(h) + "&plant_id=" + p, '_blank');
        bootstrap.Modal.getInstance(document.getElementById('plantModal')).hide();
    }
</script>
</body>
</html>
