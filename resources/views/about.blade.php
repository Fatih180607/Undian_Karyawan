<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visi & Misi - RAT K2MS 2026</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.8)), url('/images/bg-undian.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
        }

        /* Navbar Style disamakan dengan Admin */
        .navbar-admin {
            background-color: #5a6570 !important;
            backdrop-filter: blur(10px);
        }

        .content-container {
            max-width: 850px;
            margin: 30px auto 50px auto;
            padding: 0 20px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }

        .title-gold {
            color: #ffc107;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .section-title {
            color: #ffc107;
            font-weight: 700;
            border-bottom: 2px solid #ffc107;
            display: inline-block;
            margin-bottom: 20px;
            padding-bottom: 5px;
        }

        .vision-text {
            font-size: 1.4rem;
            font-style: italic;
            line-height: 1.6;
            margin-bottom: 40px;
            text-align: center;
        }

        .mission-list {
            list-style: none;
            padding: 0;
        }

        .mission-list li {
            font-size: 1.1rem;
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
        }

        .mission-list li i {
            color: #ffc107;
            margin-right: 15px;
            margin-top: 5px;
        }

        .logo-small {
            max-width: 180px;
            filter: drop-shadow(2px 4px 6px rgba(0,0,0,0.5));
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-admin shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">RAT K2MS <span class="text-warning">ABOUT</span></a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/"><i class="fas fa-gift me-1"></i> Dashboard Doorprize</a>
                </li>
                <li class="nav-item ms-lg-3">
                    <a class="nav-link" href="/beasiswa-admin"><i class="fas fa-graduation-cap me-1"></i> Dashboard Beasiswa</a>
                </li>
                <li class="nav-item ms-lg-3">
                    <a class="nav-link" href="/setting"><i class="fas fa-cog me-1"></i> Settings</a>
                </li>
                <li class="nav-item ms-lg-3">
                    <a class="nav-link fw-bold" href="/about" style="border-bottom: 2px solid #ffc107;color:white; ">
                        <i class="fas fa-info-circle me-1"></i> Tentang
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container content-container">
    <div class="text-center mb-4">
        <h1 class="title-gold">TENTANG</h1>
    </div>

    <div class="glass-card">
        <div class="text-center">
            <h3 class="section-title">VISI</h3>
            <p class="vision-text">
                "Menjadi Koperasi yang Mandiri, Terpercaya, dan Menyejahterakan Anggota melalui Inovasi dan Pelayanan Prima."
            </p>
        </div>

        <hr style="border-color: rgba(255,255,255,0.2); margin: 40px 0;">

        <div>
            <h3 class="section-title">MISI</h3>
            <ul class="mission-list">
                <li>
                    <i class="fas fa-check-circle"></i>
                    <span>Meningkatkan profesionalisme pengelolaan koperasi secara transparan dan akuntabel.</span>
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    <span>Memberikan pelayanan yang cepat, tepat, dan ramah kepada seluruh anggota.</span>
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    <span>Mengembangkan unit usaha yang kompetitif dan inovatif bagi kemajuan bersama.</span>
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    <span>Membangun sinergi yang kuat antara pengurus, pengawas, dan anggota koperasi.</span>
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    <span>Menyelenggarakan program kesejahteraan anggota, termasuk beasiswa dan dana sosial secara adil.</span>
                </li>
            </ul>
        </div>
    </div>
</div>


</body>
</html>
