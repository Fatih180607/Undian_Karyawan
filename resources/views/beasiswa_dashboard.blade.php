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
                <i class="fas fa-play me-1"></i> BUKA LAYAR BEASISWA
            </a>
        </div>
    </div>
</nav>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show btn-round px-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
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
                        <select name="jenjang_sekolah" class="form-select btn-round" required>
                            <option value="">Pilih Jenjang...</option>
                            @foreach($kategori as $k)
                                <option value="{{ $k->jenjang_sekolah }}">{{ $k->jenjang_sekolah }} (Rp {{ number_format($k->nominal) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold">Data Orang Tua</label>
                        <input type="text" name="npk_orang_tua" class="form-control btn-round mb-2" placeholder="NPK Orang Tua" required>
                        <input type="text" name="nama_orang_tua" class="form-control btn-round mb-2" placeholder="Nama Karyawan" required>
                        <select name="plant_id" class="form-select btn-round" required>
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
                <h6 class="fw-bold text-info mb-3"><i class="fas fa-file-excel me-2"></i>Import Peserta (Excel/CSV)</h6>
                <form action="/beasiswa/peserta/import" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-2"><small class="text-muted text-center d-block">Gunakan file CSV untuk data massal</small></div>
                    <input type="file" name="file_excel" class="form-control mb-2" required>
                    <button type="submit" class="btn btn-info w-100 btn-round text-white">UPLOAD DATA ANAK</button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card p-4 mb-3 border-brown bg-brown-light">
                <h6 class="fw-bold text-brown mb-3"><i class="fas fa-sliders-h me-2"></i>SETTING KUOTA PER PLANT</h6>
                <form action="/beasiswa/kuota-plant/update" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle bg-white rounded overflow-hidden">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>Nama Plant</th>
                                    @foreach($kategori as $k)
                                        <th>{{ $k->jenjang_sekolah }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($plants as $p)
                                <tr>
                                    <td class="fw-bold px-2">{{ $p->nama_plant }}</td>
                                    @foreach($kategori as $k)
                                        @php
                                            $val = \App\Models\BeasiswaKuotaPlant::where('plant_id', $p->id)->where('kategori_id', $k->id)->first();
                                        @endphp
                                        <td>
                                            <input type="number" name="kuota[{{ $p->id }}][{{ $k->id }}]"
                                                   class="form-control form-control-sm text-center border-0"
                                                   value="{{ $val->jumlah_slot ?? 0 }}">
                                        </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-brown w-100 btn-round mt-2">SIMPAN SEMUA SETTINGAN KUOTA</button>
                </form>
            </div>

            <div class="card p-4">
                <h6 class="fw-bold mb-3"><i class="fas fa-list me-2"></i>Daftar Peserta Beasiswa</h6>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover small">
                        <thead class="sticky-top bg-white">
                            <tr>
                                <th>Anak</th>
                                <th>Jenjang</th>
                                <th>Plant</th>
                                <th>Orang Tua</th>
                                <th class="text-center">Status</th>
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
                                    {!! $ps->is_winner ? '<span class="badge bg-success">Pemenang</span>' : '<span class="badge bg-light text-dark border">Peserta</span>' !!}
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
