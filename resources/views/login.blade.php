<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin System</title>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Source Sans Pro', sans-serif;
            background-color: #f4f6f9; /* Warna abu-abu khas background dashboard */
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .login-box {
            width: 400px;
        }

        .login-logo {
            font-size: 2.1rem;
            font-weight: 300;
            margin-bottom: .9rem;
            text-align: center;
        }

        .login-logo b {
            font-weight: 700;
        }

        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            border-radius: 0.25rem;
            border-top: 3px solid #007bff; /* Warna biru primer dashboard */
            background-color: #fff;
        }

        .card-body {
            padding: 2rem;
        }

        .login-box-msg {
            margin: 0;
            padding: 0 20px 20px;
            text-align: center;
            color: #666;
        }

        .form-control {
            border-radius: 0; /* Dashboard biasanya pakai kotak tegas */
            border: 1px solid #ced4da;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: none;
        }

        .input-group-text {
            background-color: transparent;
            border-radius: 0;
            color: #777;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 0.25rem;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #0069d9;
        }

        /* Styling untuk alert agar rapi */
        .alert {
            font-size: 0.9rem;
            border-radius: 0;
        }
    </style>
</head>
<body>

<div class="login-box">
    <div class="login-logo">
        <a href="#" class="text-decoration-none text-dark"><b>ADMIN</b>UNDIAN</a>
    </div>

    <div class="card">
        <div class="card-body">
            <p class="login-box-msg">Sign in to start your session</p>

            @if(session()->has('loginError'))
                <div class="alert alert-danger mb-3">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('loginError') }}
                </div>
            @endif

            <form action="/login" method="POST">
                @csrf
                <div class="input-group mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" value="{{ old('username') }}" required autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text h-100">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>

                <div class="input-group mb-4">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text h-100">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100">SIGN IN</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
