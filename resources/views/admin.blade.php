<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Undian - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .nav-admin { background: #2d3436; color: white; padding: 15px 0; margin-bottom: 25px; }
        .card { border-radius: 18px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 20px; }
        
        /* Gallery Hadiah */
        .prize-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 15px; }
        .prize-card { background: white; border-radius: 15px; border: 1px solid #dee2e6; overflow: hidden; position: relative; transition: 0.3s; }
        .prize-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .img-container { width: 100%; aspect-ratio: 1/1; background: #f8f9fa; display: flex; align-items: center; justify-content: center; }
        .prize-img { width: 100%; height: 100%; object-fit: contain; padding: 10px; }
        .card-info { padding: 10px; text-align: center; font-weight: bold; font-size: 0.75rem; text-transform: uppercase; color: #636e72; }
        
        /* Floating Delete Button for Prize */
        .btn-delete-prize { position: absolute; top: 5px; right: 5px; background: rgba(255, 71, 87, 0.9); color: white; border: none; border-radius: 50%; width: 24px; height: 24px; font-size: 10px; z-index: 5; transition: 0.2s; }
        .btn-delete-prize:hover { background: #ff4757; transform: scale(1.1); }

        .btn-round { border-radius: 100px; font-weight: bold; }
        .table thead { background: #f8f9fa; }
    </style>
</head>
<body>

<div class="nav-admin shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <h4 class="mb-0 fw-bold"></i>Website Undian</h4>
        <a href="{{ url('/undian') }}" class="btn btn-warning btn-round px-4 shadow-sm">
            <i class="fas fa-play me-1"></i> UNDI SEKARANG
        </a>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-4">
            <div class="card p-4 mb-4">
                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-user-plus me-2"></i>Tambah Peserta</h6>
                <form action="/admin/add-employee" method="POST">
                    @csrf
                    <div class="mb-2">
                        <input type="text" name="employee_number" class="form-control btn-round px-3" placeholder="Nomor Karyawan" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" name="employee_name" class="form-control btn-round px-3" placeholder="Nama Lengkap" required>
                    </div>
                    <button class="btn btn-primary w-100 btn-round">SIMPAN DATA</button>
                </form>
            </div>

            <div class="card p-4">
                <h6 class="fw-bold text-success mb-3"><i class="fas fa-gift me-2"></i>Tambah Hadiah</h6>
                <form action="/admin/add-prize" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-2">
                        <input type="text" name="nama_hadiah" class="form-control btn-round px-3" placeholder="Nama Barang" required>
                    </div>
                    <div class="mb-3">
                        <input type="file" name="foto" class="form-control" required>
                    </div>
                    <button class="btn btn-success w-100 btn-round">UPLOAD HADIAH</button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card p-4 mb-4">
                <h6 class="fw-bold mb-3 text-secondary">HADIAH</h6>
                <div class="prize-container">
                    @foreach($prizes as $p)
                    <div class="prize-card shadow-sm">
                        <button class="btn-delete-prize" onclick="prepareDeletePrize('{{ $p->id }}', '{{ $p->nama_hadiah }}')" data-bs-toggle="modal" data-bs-target="#deletePrizeModal">
                            <i class="fas fa-times"></i>
                        </button>
                        <div class="img-container">
                            <img src="/images/{{ $p->foto_hadiah }}" class="prize-img">
                        </div>
                        <div class="card-info">{{ $p->nama_hadiah }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card p-4">
                <h6 class="fw-bold mb-3 text-secondary">DAFTAR PESERTA</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nomor Karyawan</th>
                                <th>Nama Karyawan</th>
                                <th class="text-center" width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $index => $e)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $e->employee_number }}</td>
                                <td>{{ $e->employee_name }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-warning" 
                                            onclick="fillEditModal('{{ $e->id }}', '{{ $e->employee_number }}', '{{ $e->employee_name }}')" 
                                            data-bs-toggle="modal" data-bs-target="#editModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" 
                                            onclick="prepareDeleteEmployee('{{ $e->id }}', '{{ $e->employee_name }}')" 
                                            data-bs-toggle="modal" data-bs-target="#deleteEmployeeModal">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
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

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <form id="editForm" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <h5 class="fw-bold mb-4 text-center">Edit Data Peserta</h5>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted">Nomor Karyawan</label>
                        <input type="text" name="employee_number" id="edit_number" class="form-control btn-round" required>
                    </div>
                    <div class="mb-4">
                        <label class="small fw-bold text-muted">Nama Lengkap</label>
                        <input type="text" name="employee_name" id="edit_name" class="form-control btn-round" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-round py-2">UPDATE DATA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteEmployeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-body p-4 text-center">
                <div class="text-danger mb-3">
                    <i class="fas fa-user-times fa-4x"></i>
                </div>
                <h5 class="fw-bold">Hapus Peserta?</h5>
                <p class="text-muted">Apakah Anda yakin ingin menghapus <span id="del_emp_name" class="fw-bold text-dark"></span> dari daftar undian?</p>
                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-light w-100 btn-round" data-bs-dismiss="modal">BATAL</button>
                    <a id="confirm_del_emp" href="#" class="btn btn-danger w-100 btn-round">YA, HAPUS</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deletePrizeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-body p-4 text-center">
                <div class="text-danger mb-3">
                    <i class="fas fa-box-open fa-4x"></i>
                </div>
                <h5 class="fw-bold">Hapus Hadiah?</h5>
                <p class="text-muted">Hapus hadiah <span id="del_prize_name" class="fw-bold text-dark"></span>? Tindakan ini tidak bisa dibatalkan.</p>
                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-light w-100 btn-round" data-bs-dismiss="modal">BATAL</button>
                    <a id="confirm_del_prize" href="#" class="btn btn-danger w-100 btn-round">HAPUS HADIAH</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Fungsi Mengisi Data Edit
    function fillEditModal(id, num, name) {
        document.getElementById('editForm').action = '/admin/update/' + id;
        document.getElementById('edit_number').value = num;
        document.getElementById('edit_name').value = name;
    }

    // Fungsi Persiapan Hapus Peserta
    function prepareDeleteEmployee(id, name) {
        document.getElementById('del_emp_name').innerText = name;
        document.getElementById('confirm_del_emp').href = '/admin/delete/' + id;
    }

    // Fungsi Persiapan Hapus Hadiah
    function prepareDeletePrize(id, name) {
        document.getElementById('del_prize_name').innerText = name;
        document.getElementById('confirm_del_prize').href = '/admin/delete-prize/' + id;
    }
</script>
</body>
</html>