<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings Master - RAT K2MS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .card { border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .navbar { background: #2c3e50 !important; padding: 10px 0; }
        .nav-link { color: rgba(255,255,255,0.7) !important; font-weight: 500; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: white !important; }
        .nav-link.active { border-bottom: 2px solid #ffc107; }
        .btn-round { border-radius: 100px; font-weight: bold; }
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
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card p-4 h-100">
                <h5 class="fw-bold mb-3 text-primary"><i class="fas fa-industry me-2"></i> Tambah Master Plant</h5>
                <p class="text-muted small">Daftarkan plant di sini agar muncul di pilihan Doorprize & Beasiswa.</p>

                <form action="/setting/plant/add" method="POST">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="nama_plant" class="form-control btn-round" placeholder="Contoh: Plant Bekasi" required style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                        <button class="btn btn-primary btn-round" type="submit" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">Simpan</button>
                    </div>
                </form>

                <div class="table-responsive mt-3">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Plant</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($plants as $p)
                            <tr>
                                <td>{{ $p->nama_plant }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary border-0" onclick="openEditPlant('{{ $p->id }}', '{{ $p->nama_plant }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="/setting/plant/delete/{{ $p->id }}" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Hapus plant ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle me-1"></i> Belum ada data plant.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editPlantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold"><i class="fas fa-edit me-2 text-primary"></i>Edit Nama Plant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editPlantForm" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small text-muted">Nama Plant Baru</label>
                        <input type="text" name="nama_plant" id="edit_nama_plant" class="form-control btn-round" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-round">SIMPAN PERUBAHAN</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Fungsi untuk membuka Modal Edit dan mengisi datanya secara dinamis
    function openEditPlant(id, nama) {
        // Set action form ke URL update yang sesuai ID-nya
        document.getElementById('editPlantForm').action = '/setting/plant/update/' + id;
        // Isi input dengan nama plant lama
        document.getElementById('edit_nama_plant').value = nama;
        // Tampilkan Modal
        var myModal = new bootstrap.Modal(document.getElementById('editPlantModal'));
        myModal.show();
    }
</script>

</body>
</html>
