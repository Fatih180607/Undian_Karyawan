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

        /* Baris Pemenang */
        .row-winner { background-color: #d1e7dd !important; }
        .row-winner td { color: #0f5132 !important; border-color: rgba(0,0,0,0.05) !important; }
        .badge-winner-info { background-color: #198754; color: white; font-weight: bold; border: none; border-radius: 50px; padding: 5px 12px; }
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
        <a href="/undian" target="_blank" class="btn btn-warning btn-round px-4 text-dark shadow-sm">
            <i class="fas fa-play-circle me-1"></i> MENU UNDI SEKARANG
        </a>
    </div>
</nav>


<div class="container">
    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show btn-round mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show btn-round mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- SISI KIRI: FORM INPUT --}}
        <div class="col-lg-4">
            <div class="card p-4 mb-3 text-center" style="background: #2d3436; color: white;">
                <h5 class="fw-bold mb-0 text-warning">DOORPRIZE MODE</h5>
                <small>Kelola hadiah & peserta utama</small>
            </div>

            <div class="card p-4 mb-3">
                <h6 class="fw-bold text-primary mb-3">Tambah Peserta Doorprize</h6>
                <form action="/admin/add-employee" method="POST">
                    @csrf
                    <input type="text" name="employee_number" class="form-control btn-round mb-2" placeholder="NPK" required>
                    <input type="text" name="employee_name" class="form-control btn-round mb-2" placeholder="Nama" required>
                    <select name="plant_id" class="form-select btn-round mb-3" required>
                        <option value="">Pilih Plant</option>
                        @foreach($plants as $plant)
                        <option value="{{ $plant->id }}">{{ $plant->nama_plant }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-primary w-100 btn-round shadow-sm">SIMPAN</button>
                </form>
            </div>

            <div class="card p-4 mb-3" style="border: 1px solid #0dcaf033;">
                <h6 class="fw-bold text-info mb-2"><i class="fas fa-file-csv me-2"></i>Import CSV (Excel)</h6>
                <small class="text-muted d-block mb-3">Upload File harus .csv</small>
                <form action="/admin/import-employees" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <input type="file" name="file" class="form-control form-control-sm" required>
                    </div>
                    <button type="submit" class="btn btn-info w-100 btn-round text-white shadow-sm">UPLOAD DATA</button>
                </form>
            </div>

            <div class="card p-4 shadow-sm">
                <h6 class="fw-bold text-success mb-3">Tambah Hadiah</h6>
                <form action="/admin/add-prize" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="text" name="nama_hadiah" class="form-control btn-round mb-2" placeholder="Nama Barang" required>
                    <div class="mb-3">
                        <input type="file" name="foto" class="form-control form-control-sm" required>
                    </div>
                    <button class="btn btn-success w-100 btn-round shadow-sm">UPLOAD HADIAH</button>
                </form>
            </div>
        </div>

        {{-- SISI KANAN: LIST HADIAH & TABEL --}}
        <div class="col-lg-8">
            <div class="card p-4 mb-3">
                <h6 class="fw-bold mb-3"><i class="fas fa-trophy me-2 text-warning"></i>List Hadiah Tersedia</h6>
                <div class="prize-container">
                    @foreach($prizes as $p)
                    <div class="prize-card shadow-sm text-center">
                        <a href="{{ url('/admin/delete-prize/'.$p->id) }}" class="btn-delete-prize" onclick="return confirm('Hapus hadiah?')"><i class="fas fa-times"></i></a>
                        <div class="img-container"><img src="/images/{{ $p->foto_hadiah }}" class="prize-img"></div>
                        <div class="p-2"><div class="small fw-bold text-uppercase">{{ $p->nama_hadiah }}</div></div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                    <h6 class="fw-bold mb-0 text-secondary">Data Peserta Terdaftar</h6>

                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        {{-- Filter --}}
                        <select onchange="window.location.href='/?status='+this.value" class="form-select form-select-sm btn-round w-auto">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Peserta</option>
                            <option value="winner" {{ request('status') == 'winner' ? 'selected' : '' }}>Pemenang</option>
                            <option value="not_winner" {{ request('status') == 'not_winner' ? 'selected' : '' }}>Belum Menang</option>
                        </select>

                        {{-- Reset --}}
                        <form action="/admin/reset-winners" method="POST" onsubmit="return confirm('Mereset semua pemenang?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-warning btn-round px-3">
                                <i class="fas fa-undo"></i> Reset Pemenang
                            </button>
                        </form>

                        {{-- Export --}}
                        <a href="/admin/export-full-pemenang" class="btn btn-sm btn-success btn-round px-3">
                            <i class="fas fa-file-excel"></i> Export Pemenang
                        </a>

                        {{-- Delete All --}}
                        <form action="/admin/delete-all-employees" method="POST" onsubmit="return confirm('Hapus SELURUH data peserta?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger btn-round px-3">
                                <i class="fas fa-trash-alt"></i> Hapus Semua
                            </button>
                        </form>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover align-middle">
                        <thead class="table-light sticky-top">
                            <tr><th>NPK</th><th>Nama</th><th>Plant</th><th>Keterangan</th><th class="text-center">Aksi</th></tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $e)
                                @if(request('status') == 'winner' && !$e->is_winner) @continue @endif
                                @if(request('status') == 'not_winner' && $e->is_winner) @continue @endif

                                <tr class="{{ $e->is_winner ? 'row-winner' : '' }}">
                                    <td>{{ $e->employee_number }}</td>
                                    <td class="fw-bold">{{ $e->employee_name }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $e->plant->nama_plant ?? 'N/A' }}</span></td>
                                    <td>
                                        @if($e->is_winner)
                                            <span class="badge badge-winner-info px-3">
                                                <i class="fas fa-trophy me-1"></i> {{ strtoupper($e->prize_won) }}
                                            </span>
                                        @else
                                            <span class="text-muted small">Peserta Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm border-0" style="color: inherit;"
                                            onclick="openEditModal('{{ $e->id }}', '{{ $e->employee_number }}', '{{ $e->employee_name }}', '{{ $e->plant_id }}')">
                                            <i class="fas fa-edit text-primary"></i>
                                        </button>
                                        <form action="{{ url('/admin/delete/'.$e->id) }}" method="GET" class="d-inline">
                                            <button type="submit" class="btn btn-sm border-0 text-danger" onclick="return confirm('Hapus?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-body p-4">
                <h5 class="fw-bold mb-3">Edit Data Peserta</h5>
                <form id="editForm" method="POST">
                    @csrf
                    <label class="small fw-bold">NPK Karyawan</label>
                    <input type="text" name="employee_number" id="edit_npk" class="form-control btn-round mb-2" required>

                    <label class="small fw-bold">Nama Lengkap</label>
                    <input type="text" name="employee_name" id="edit_nama" class="form-control btn-round mb-2" required>

                    <label class="small fw-bold">Plant</label>
                    <select name="plant_id" id="edit_plant" class="form-select btn-round mb-3" required>
                        <option value="">Pilih Plant</option>
                        @foreach($plants as $plant)
                        <option value="{{ $plant->id }}">{{ $plant->nama_plant }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary w-100 btn-round shadow-sm">SIMPAN PERUBAHAN</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function openEditModal(id, npk, nama, plant_id) {
        document.getElementById('edit_npk').value = npk;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_plant').value = plant_id;

        // Sesuai dengan route di web.php lu: /admin/update/{id}
        document.getElementById('editForm').action = "/admin/update/" + id;

        new bootstrap.Modal(document.getElementById('editModal')).show();
    }
</script>
</body>
</html>
