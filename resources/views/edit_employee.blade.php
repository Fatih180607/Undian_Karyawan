<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Karyawan | Modern UI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .card-header {
            background: #2d3436;
            color: white;
            padding: 20px;
            text-align: center;
            border: none;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px;
            border: 2px solid #eee;
        }
        .form-control:focus {
            border-color: #1e3c72;
            box-shadow: none;
        }
        .btn-update {
            background: #1e3c72;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: bold;
            color: white;
        }
        .btn-update:hover {
            background: #2a5298;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card glass-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i> EDIT DATA KARYAWAN</h5>
                </div>
                <div class="card-body p-4">
                    <form action="/admin/update/{{ $employee->id }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="fw-bold mb-2">Nomor ID Karyawan</label>
                            <input type="text" name="employee_number" class="form-control" 
                                   value="{{ $employee->employee_number }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="fw-bold mb-2">Nama Lengkap</label>
                            <input type="text" name="employee_name" class="form-control" 
                                   value="{{ $employee->employee_name }}" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-update">
                                <i class="fas fa-save me-2"></i> SIMPAN PERUBAHAN
                            </button>
                            <a href="/admin" class="btn btn-link text-decoration-none text-muted small">
                                Batal & Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>