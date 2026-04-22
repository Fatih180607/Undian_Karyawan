<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Beasiswa - RAT K2MS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f4ede7; font-family: 'Segoe UI', sans-serif; }
        .navbar-admin { background: #5d534f; padding: 10px 0; }
        .nav-link { color: rgba(255,255,255,0.7) !important; font-weight: 500; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: white !important; }
        .nav-link.active { border-bottom: 2px solid #ffc107; }
        .card { border-radius: 18px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .btn-round { border-radius: 100px; font-weight: bold; }
        .text-brown { color: #5d534f; }
        .btn-brown { background: #8d7d77; color: white; border: none; }
        .btn-brown:hover { background: #5d534f; color: white; }
        .bg-brown-light { background: #ede3df; }
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
            </ul>
        </div>
        <a href="/beasiswa-undi" target="_blank" class="btn btn-warning btn-round btn-sm px-4 text-dark">
            <i class="fas fa-play me-1"></i> UNDI BEASISWA DISINI
        </a>
    </div>
</nav>

<div class="container">
    @if(session('success') || session('warning') || session('error'))
        <div class="alert alert-dismissible fade show btn-round px-4" role="alert">
            @if(session('success'))
                <div class="alert alert-success mb-0"><i class="fas fa-check-circle me-2"></i> {{ session('success') }}</div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle me-2"></i> {{ session('warning') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger mb-0"><i class="fas fa-times-circle me-2"></i> {{ session('error') }}</div>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <div class="card p-4 mb-3 text-center" style="background: #8d7d77; color: white;">
                <h5 class="fw-bold mb-0">INPUT PESERTA</h5>
                <small>Manual & Import Data</small>
            </div>

            <div class="card p-4 mb-3 border-top border-success border-4">
                <h6 class="fw-bold text-success mb-3"><i class="fas fa-user-plus me-2"></i>Tambah Peserta Manual</h6>
                <form action="/beasiswa/peserta/simpan" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label class="small fw-bold">Data Anak</label>
                        <input type="text" name="nama_anak" class="form-control btn-round mb-2" placeholder="Nama Lengkap Anak" required>
                        <label class="small fw-bold">Jenjang</label>
                        <select id="jenjangSelect" name="jenjang_sekolah" class="form-select btn-round mb-2" disabled required>
                            <option value="">Pilih plant dulu...</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold">Data Orang Tua</label>
                        <input type="text" name="npk_orang_tua" class="form-control btn-round mb-2" placeholder="NPK Orang Tua" required>
                        <input type="text" name="nama_orang_tua" class="form-control btn-round mb-2" placeholder="Nama Karyawan" required>
                        <select id="plantSelect" name="plant_id" class="form-select btn-round" required onchange="refreshJenjangOptions()">
                            <option value="">Pilih Plant...</option>
                            @foreach($plants as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_plant }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-success w-100 btn-round">SIMPAN PESERTA</button>
                </form>
            </div>

            <div class="card p-4 mb-3">
                <h6 class="fw-bold text-info mb-3"><i class="fas fa-file-excel me-2"></i>Import Peserta (CSV)</h6>
                <form action="/beasiswa/peserta/import" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file_excel" class="form-control mb-2" accept=".csv,text/csv" required>
                    <button type="submit" class="btn btn-info w-100 btn-round text-white">UPLOAD DATA</button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card p-4 mb-3 border-brown bg-brown-light">
                <h6 class="fw-bold text-brown mb-3"><i class="fas fa-sliders-h me-2"></i>SETTING KUOTA PER PLANT</h6>
                <div class="row g-3">
                    @foreach($plants as $p)
                    <div class="col-md-6">
                        <div class="card p-3 shadow-sm h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>{{ $p->nama_plant }}</strong>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditJenjang{{ $p->id }}">Atur</button>
                            </div>
                            <div>
                                @foreach($kategori->where('plant_id', $p->id) as $k)
                                    <span class="badge bg-secondary me-1 mb-1">{{ $k->jenjang_sekolah }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="fas fa-list me-2"></i>Daftar Peserta</h6>
                    <div class="d-flex gap-2">
                        <select id="filterStatus" class="form-select form-select-sm btn-round w-auto" onchange="filterPeserta()">
                            <option value="all">Semua ({{ $peserta->count() }})</option>
                            <option value="winner">Pemenang ({{ $peserta->where('is_winner', true)->count() }})</option>
                            <option value="candidate">Kandidat ({{ $peserta->where('is_winner', false)->count() }})</option>
                        </select>
                        <form action="/beasiswa/peserta/reset" method="POST" onsubmit="return confirm('Hapus semua peserta?');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">Reset</button>
                        </form>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                    <table class="table table-hover small">
                        <thead class="sticky-top bg-white">
                            <tr>
                                <th>Anak</th>
                                <th>Jenjang</th>
                                <th>Plant</th>
                                <th>Orang Tua</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peserta as $ps)
                            <tr class="{{ $ps->is_winner ? 'table-success is-winner-row' : 'is-candidate-row' }}">
                                <td>
                                    {{ $ps->nama_anak }}
                                    @if($ps->is_winner)
                                        <span class="badge bg-success ms-1"><i class="fas fa-trophy"></i></span>
                                    @endif
                                </td>
                                <td><span class="badge bg-secondary">{{ $ps->jenjang_sekolah }}</span></td>
                                <td>{{ $ps->plant->nama_plant ?? '-' }}</td>
                                <td>{{ $ps->nama_orang_tua }}</td>
                                <td class="text-center">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary me-1"
                                            onclick="openEditPeserta('{{ $ps->id }}', '{{ addslashes($ps->nama_anak) }}', '{{ $ps->jenjang_sekolah }}', '{{ $ps->npk_orang_tua }}', '{{ addslashes($ps->nama_orang_tua) }}', '{{ $ps->plant_id }}')">
                                        Edit
                                    </button>
                                    <form action="/beasiswa/peserta/delete/{{ $ps->id }}" method="POST" class="d-inline-block">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus?')">Hapus</button>
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

<div class="modal fade" id="editPesertaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditPeserta" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="editPesertaId" name="peserta_id">
                    <div class="mb-3">
                        <label class="small fw-bold">Plant</label>
                        <select id="editPlantSelect" name="plant_id" class="form-select btn-round" required onchange="refreshEditJenjangOptions()">
                            <option value="">Pilih Plant...</option>
                            @foreach($plants as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_plant }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Jenjang</label>
                        <select id="editJenjangSelect" name="jenjang_sekolah" class="form-select btn-round" required>
                            <option value="">Pilih Jenjang...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">NPK & Nama Orang Tua</label>
                        <div class="input-group">
                            <input type="text" id="editNpk" name="npk_orang_tua" class="form-control" placeholder="NPK" required>
                            <input type="text" id="editNamaOrtu" name="nama_orang_tua" class="form-control w-50" placeholder="Nama Karyawan" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Nama Anak</label>
                        <input type="text" id="editNamaAnak" name="nama_anak" class="form-control btn-round" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-round px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($plants as $p)
<div class="modal fade" id="modalEditJenjang{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Atur Jenjang: {{ $p->nama_plant }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/beasiswa/kategori/simpan" method="POST">
                @csrf
                <input type="hidden" name="plant_id" value="{{ $p->id }}">
                <div class="modal-body">
                    <div id="jenjangRows{{ $p->id }}">
                        @foreach($kategori->where('plant_id', $p->id) as $k)
                        <div class="row g-2 mb-2 jenjang-row">
                            <input type="hidden" name="kategori_id[]" value="{{ $k->id }}">
                            <div class="col-5"><input type="text" name="jenjang_sekolah[]" class="form-control" value="{{ $k->jenjang_sekolah }}" required></div>
                            <div class="col-4"><input type="number" name="nominal[]" class="form-control" value="{{ $k->nominal }}" required></div>
                            <div class="col-2"><input type="number" name="kuota[]" class="form-control" value="{{ $k->kuota }}"></div>
                            <div class="col-1"><button type="button" class="btn btn-danger" onclick="this.closest('.row').remove()">×</button></div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="tambahJenjangRow({{ $p->id }})">+ Tambah</button>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const kategoriByPlant = @json($kategori->groupBy('plant_id')->map(function($items){ return $items->map(function($item){ return ['jenjang_sekolah' => $item->jenjang_sekolah, 'nominal' => $item->nominal]; }); })->toArray());

    function refreshJenjangOptions() {
        const plantId = document.getElementById('plantSelect').value;
        const jenjangSelect = document.getElementById('jenjangSelect');
        jenjangSelect.innerHTML = '<option value="">Pilih Jenjang...</option>';
        if (kategoriByPlant[plantId]) {
            jenjangSelect.disabled = false;
            kategoriByPlant[plantId].forEach(item => {
                jenjangSelect.innerHTML += `<option value="${item.jenjang_sekolah}">${item.jenjang_sekolah}</option>`;
            });
        } else {
            jenjangSelect.disabled = true;
        }
    }

    // FUNGSI UTAMA EDIT
    function openEditPeserta(id, namaAnak, jenjang, npk, namaOrtu, plantId) {
        document.getElementById('editPesertaId').value = id;
        document.getElementById('editNamaAnak').value = namaAnak;
        document.getElementById('editNpk').value = npk;
        document.getElementById('editNamaOrtu').value = namaOrtu;
        document.getElementById('editPlantSelect').value = plantId;

        // Refresh dropdown jenjang di modal edit
        refreshEditJenjangOptions(jenjang);

        // Set Action URL
        document.getElementById('formEditPeserta').action = '/beasiswa/peserta/update/' + id;

        // Munculkan Modal
        var myModal = new bootstrap.Modal(document.getElementById('editPesertaModal'));
        myModal.show();
    }

    function refreshEditJenjangOptions(selectedJenjang = '') {
        const plantId = document.getElementById('editPlantSelect').value;
        const jenjangSelect = document.getElementById('editJenjangSelect');
        jenjangSelect.innerHTML = '<option value="">Pilih Jenjang...</option>';

        if (kategoriByPlant[plantId]) {
            kategoriByPlant[plantId].forEach(item => {
                const isSelected = item.jenjang_sekolah === selectedJenjang ? 'selected' : '';
                jenjangSelect.innerHTML += `<option value="${item.jenjang_sekolah}" ${isSelected}>${item.jenjang_sekolah}</option>`;
            });
        }
    }

    function filterPeserta() {
        const status = document.getElementById('filterStatus').value;
        document.querySelectorAll('tbody tr').forEach(row => {
            if (status === 'all') row.style.display = '';
            else if (status === 'winner') row.style.display = row.classList.contains('is-winner-row') ? '' : 'none';
            else row.style.display = row.classList.contains('is-candidate-row') ? '' : 'none';
        });
    }

    function tambahJenjangRow(plantId) {
        const container = document.getElementById('jenjangRows' + plantId);
        const html = `<div class="row g-2 mb-2"><div class="col-5"><input type="text" name="jenjang_sekolah[]" class="form-control" required></div><div class="col-4"><input type="number" name="nominal[]" class="form-control" required></div><div class="col-2"><input type="number" name="kuota[]" class="form-control" value="0"></div><div class="col-1"><button type="button" class="btn btn-danger" onclick="this.closest('.row').remove()">×</button></div></div>`;
        container.insertAdjacentHTML('beforeend', html);
    }
</script>
</body>
</html>
