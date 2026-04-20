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
        <a class="navbar-brand fw-bold" href="#">RAT K2MS <span class="text-warning">BEASISWA</span></a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="/"><i class="fas fa-gift me-1"></i> Dashboard Doorprize</a></li>
                <li class="nav-item ms-lg-3"><a class="nav-link active" href="/beasiswa-admin"><i class="fas fa-graduation-cap me-1"></i> Dashboard Beasiswa</a></li>
                <li class="nav-item ms-lg-3"><a class="nav-link text-warning fw-bold" href="/setting"><i class="fas fa-cog me-1"></i>Settings</a></li>
            </ul>
            <a href="/beasiswa-undi" target="_blank" class="btn btn-warning btn-round btn-sm px-4 text-dark">
                <i class="fas fa-play me-1"></i> UNDI BEASISWA DISINI
            </a>
        </div>
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
                        <small class="text-muted">Pilih plant terlebih dahulu, lalu pilih jenjang yang tersedia untuk plant tersebut.</small>
                    </div>

                    <button class="btn btn-success w-100 btn-round">SIMPAN PESERTA</button>
                </form>
            </div>

            <div class="card p-4 mb-3">
                <h6 class="fw-bold text-info mb-3"><i class="fas fa-file-excel me-2"></i>Import Peserta (Excel/CSV)</h6>
                <form action="/beasiswa/peserta/import" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-2"><small class="text-muted text-center d-block">Gunakan file CSV untuk data massal.</small></div>
                    <div class="mb-2"><small class="text-muted text-center d-block">Format: nama_anak,jenjang_sekolah,npk_orang_tua,nama_orang_tua,nama_plant</small></div>
                    <div class="mb-2"><small class="text-muted text-center d-block">Contoh: Fatih,SMA,98718273,Daffa Al Rizik,BEKASI PLANT</small></div>
                    <input type="file" name="file_excel" class="form-control mb-2" accept=".csv,text/csv" required>
                    <button type="submit" class="btn btn-info w-100 btn-round text-white">UPLOAD DATA ANAK</button>
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
                                <div>
                                    <strong>{{ $p->nama_plant }}</strong>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditJenjang{{ $p->id }}">Atur Jenjang</button>
                            </div>
                            <div>
                                @php $plantCategories = $kategori->where('plant_id', $p->id); @endphp
                                @if($plantCategories->isEmpty())
                                    <div class="text-muted">Belum ada jenjang untuk plant ini.</div>
                                @else
                                    @foreach($plantCategories as $k)
                                        <span class="badge bg-secondary me-1 mb-1">{{ $k->jenjang_sekolah }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="fas fa-list me-2"></i>Daftar Peserta Beasiswa</h6>
                    <form action="/beasiswa/peserta/reset" method="POST" onsubmit="return confirm('Hapus semua peserta beasiswa?');">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger">Hapus Semua Data</button>
                    </form>
                </div>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
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
                            <tr>
                                <td>{{ $ps->nama_anak }}</td>
                                <td><span class="badge bg-secondary">{{ $ps->jenjang_sekolah }}</span></td>
                                <td>{{ $ps->plant->nama_plant ?? '-' }}</td>
                                <td>{{ $ps->nama_orang_tua }} ({{ $ps->npk_orang_tua }})</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#editPesertaModal" data-id="{{ $ps->id }}" data-nama="{{ $ps->nama_anak }}" data-jenjang="{{ $ps->jenjang_sekolah }}" data-npk="{{ $ps->npk_orang_tua }}" data-namaortu="{{ $ps->nama_orang_tua }}" data-plant="{{ $ps->plant_id }}" onclick="openEditPeserta(this)">Edit</button>
                                    <form action="/beasiswa/peserta/delete/{{ $ps->id }}" method="POST" class="d-inline-block" onsubmit="return confirm('Hapus peserta ini?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
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

@foreach($plants as $p)
<!-- Modal Edit Jenjang untuk Plant {{ $p->nama_plant }} -->
<div class="modal fade" id="modalEditJenjang{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Jenjang untuk Plant: {{ $p->nama_plant }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/beasiswa/kategori/simpan" method="POST">
                @csrf
                <input type="hidden" name="plant_id" value="{{ $p->id }}">
                <div class="modal-body">
                    <div class="row gx-2 mb-3 text-muted small fw-semibold">
                        <div class="col-5">Jenjang</div>
                        <div class="col-4">Nominal Beasiswa</div>
                        <div class="col-3">Kuota</div>
                    </div>
                    <div id="jenjangRows{{ $p->id }}">
                        @foreach($kategori->where('plant_id', $p->id) as $k)
                        <div class="row g-2 jenjang-row mb-3 align-items-end">
                            <input type="hidden" name="kategori_id[]" value="{{ $k->id }}">
                            <div class="col-5">
                                <input type="text" name="jenjang_sekolah[]" class="form-control btn-round" value="{{ $k->jenjang_sekolah }}" required placeholder="Nama Jenjang (misal SD)">
                            </div>
                            <div class="col-4">
                                <input type="number" name="nominal[]" class="form-control btn-round" value="{{ $k->nominal }}" required placeholder="Nominal Beasiswa">
                            </div>
                            <div class="col-2">
                                <input type="number" name="kuota[]" class="form-control btn-round" value="{{ $k->kuota }}" min="0" placeholder="Kuota">
                            </div>
                            <div class="col-1">
                                <button type="button" class="btn btn-danger btn-sm w-100" onclick="hapusJenjangRow(this)">×</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-outline-success btn-sm" onclick="tambahJenjangRow({{ $p->id }})">+ Tambah Jenjang</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Jenjang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Edit Peserta -->
<div class="modal fade" id="editPesertaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Peserta Beasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditPeserta" method="POST" action="/beasiswa/peserta/update/0">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="editPesertaId" name="peserta_id">
                    <div class="mb-3">
                        <label class="small fw-bold">Plant</label>
                        <select id="editPlantSelect" name="plant_id" class="form-select btn-round mb-2" required onchange="refreshEditJenjangOptions()">
                            <option value="">Pilih Plant...</option>
                            @foreach($plants as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_plant }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Jenjang</label>
                        <select id="editJenjangSelect" name="jenjang_sekolah" class="form-select btn-round" disabled required>
                            <option value="">Pilih plant dulu...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">NPK Orang Tua</label>
                        <input type="text" id="editNpk" name="npk_orang_tua" class="form-control btn-round" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Nama Orang Tua</label>
                        <input type="text" id="editNamaOrtu" name="nama_orang_tua" class="form-control btn-round" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Nama Anak</label>
                        <input type="text" id="editNamaAnak" name="nama_anak" class="form-control btn-round" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const kategoriByPlant = @json($kategori->groupBy('plant_id')->map(function($items){ return $items->map(function($item){ return ['jenjang_sekolah' => $item->jenjang_sekolah, 'nominal' => $item->nominal]; }); })->toArray());

    function refreshJenjangOptions() {
        const plantId = document.getElementById('plantSelect').value;
        const jenjangSelect = document.getElementById('jenjangSelect');
        jenjangSelect.innerHTML = '';

        if (!plantId || !kategoriByPlant[plantId] || kategoriByPlant[plantId].length === 0) {
            jenjangSelect.innerHTML = '<option value="">Tidak ada jenjang untuk plant ini</option>';
            jenjangSelect.disabled = true;
            return;
        }

        jenjangSelect.disabled = false;
        jenjangSelect.innerHTML = '<option value="">Pilih Jenjang...</option>';
        kategoriByPlant[plantId].forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.jenjang_sekolah;
            opt.textContent = `${item.jenjang_sekolah} (Rp ${Number(item.nominal).toLocaleString('id-ID')})`;
            jenjangSelect.appendChild(opt);
        });
    }

    function refreshEditJenjangOptions(selectedJenjang = '') {
        const plantId = document.getElementById('editPlantSelect').value;
        const jenjangSelect = document.getElementById('editJenjangSelect');
        jenjangSelect.innerHTML = '';

        if (!plantId || !kategoriByPlant[plantId] || kategoriByPlant[plantId].length === 0) {
            jenjangSelect.innerHTML = '<option value="">Tidak ada jenjang untuk plant ini</option>';
            jenjangSelect.disabled = true;
            return;
        }

        jenjangSelect.disabled = false;
        jenjangSelect.innerHTML = '<option value="">Pilih Jenjang...</option>';
        kategoriByPlant[plantId].forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.jenjang_sekolah;
            opt.textContent = `${item.jenjang_sekolah} (Rp ${Number(item.nominal).toLocaleString('id-ID')})`;
            if (item.jenjang_sekolah === selectedJenjang) {
                opt.selected = true;
            }
            jenjangSelect.appendChild(opt);
        });
    }

    function openEditPeserta(button) {
        const pesertaId = button.dataset.id;
        const namaAnak = button.dataset.nama;
        const jenjang = button.dataset.jenjang;
        const npk = button.dataset.npk;
        const namaOrtu = button.dataset.namaortu;
        const plantId = button.dataset.plant;

        document.getElementById('editPesertaId').value = pesertaId;
        document.getElementById('editNamaAnak').value = namaAnak;
        document.getElementById('editNpk').value = npk;
        document.getElementById('editNamaOrtu').value = namaOrtu;
        document.getElementById('editPlantSelect').value = plantId;
        refreshEditJenjangOptions(jenjang);

        const form = document.getElementById('formEditPeserta');
        form.action = '/beasiswa/peserta/update/' + pesertaId;
    }

    function tambahJenjangRow(plantId) {
        const container = document.getElementById('jenjangRows' + plantId);
        const row = document.createElement('div');
        row.className = 'row g-2 jenjang-row mb-3 align-items-end';
        row.innerHTML = `
            <div class="col-5">
                <input type="text" name="jenjang_sekolah[]" class="form-control btn-round" placeholder="Nama Jenjang (misal SD)" required>
            </div>
            <div class="col-4">
                <input type="number" name="nominal[]" class="form-control btn-round" placeholder="Nominal Beasiswa" required>
            </div>
            <div class="col-2">
                <input type="number" name="kuota[]" class="form-control btn-round" placeholder="Kuota" min="0" value="0">
            </div>
            <div class="col-1">
                <button type="button" class="btn btn-danger btn-sm w-100" onclick="hapusJenjangRow(this)">×</button>
            </div>
        `;
        container.appendChild(row);
    }

    function hapusJenjangRow(button) {
        const row = button.closest('.jenjang-row');
        if (row) {
            row.remove();
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
