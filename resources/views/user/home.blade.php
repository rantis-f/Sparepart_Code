<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Aplikasi Sparepart PGN.COM</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --navy-dark: #0f172a;
            --navy: #1e293b;
            --navy-light: #334155;
            --gold: #d4af37;
            --gold-light: #fef3c7;
            --text-light: #f1f5f9;
            --text-muted: #94a3b8;
            --card-bg: rgba(255, 255, 255, 0.05);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy) 100%);
            min-height: 100vh;
            color: var(--text-light);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .main-container {
            width: 100%;
            max-width: 1000px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .logo-container {
            margin-bottom: 2rem;
        }
        
        .logo {
            height: 70px;
            filter: brightness(0) invert(1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-light);
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }
        
        .subtitle {
            font-size: 1.7rem;
            font-weight: 600;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--gold) 0%, #fbbf24 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .description {
            font-size: 1.1rem;
            color: var(--text-muted);
            max-width: 500px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        .cards-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            width: 100%;
            max-width: 800px;
            margin-bottom: 2.5rem;
        }
        
        .card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            text-align: left;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.2);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .card-jenis:hover {
            border-color: var(--gold);
        }
        
        .card-request:hover {
            border-color: #10b981;
        }
        
        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .icon-container {
            width: 55px;
            height: 55px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.2rem;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .icon-jenis {
            color: var(--gold);
        }
        
        .icon-request {
            color: #10b981;
        }
        
        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-light);
            margin: 0;
        }
        
        .card-content {
            margin-bottom: 1.8rem;
        }
        
        .card-description {
            color: var(--text-muted);
            line-height: 1.6;
            font-size: 1rem;
        }
        
        .btn {
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            width: fit-content;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
        }
        
        .btn-jenis {
            background: linear-gradient(135deg, var(--gold) 0%, #b45309 100%);
            color: white;
        }
        
        .btn-jenis:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -5px rgba(212, 175, 55, 0.3);
        }
        
        .btn-request {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            color: white;
        }
        
        .btn-request:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -5px rgba(16, 185, 129, 0.3);
        }
        
        .footer {
            color: var(--text-muted);
            font-size: 0.9rem;
            text-align: center;
            margin-top: 1rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 900px) {
            .cards-container {
                grid-template-columns: 1fr;
                max-width: 500px;
            }
            
            .title {
                font-size: 2.2rem;
            }
            
            .subtitle {
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 600px) {
            body {
                padding: 15px;
                justify-content: flex-start;
                min-height: 100vh;
                height: auto;
            }
            
            .main-container {
                margin-top: 1rem;
            }
            
            .logo {
                height: 60px;
            }
            
            .header {
                margin-bottom: 2rem;
            }
            
            .title {
                font-size: 2rem;
            }
            
            .subtitle {
                font-size: 1.3rem;
            }
            
            .description {
                font-size: 1rem;
            }
            
            .card {
                padding: 1.5rem;
            }
            
            .icon-container {
                width: 50px;
                height: 50px;
                margin-right: 1rem;
            }
            
            .card-title {
                font-size: 1.3rem;
            }
            
            .card-description {
                font-size: 0.95rem;
            }
            
            .btn {
                padding: 0.7rem 1.3rem;
                font-size: 0.9rem;
            }
        }

        @media (max-height: 700px) and (min-width: 901px) {
            body {
                padding-top: 2rem;
                padding-bottom: 2rem;
            }
            
            .main-container {
                transform: scale(0.9);
            }
        }
    </style>
</head>
<body class="antialiased">
    <div class="main-container">

        <!-- Header -->
        <div class="header">
            <h1 class="title">Sistem Manajemen Sparepart</h1>
            <h2 class="subtitle">Field Technician</h2>
            <p class="description">
                Akses Data, Request, dan Monitoring Sparepart secara terintegrasi
            </p>
        </div>

        <!-- Cards -->
        <div class="cards-container">
            <!-- Jenis Barang -->
            <a href="{{ route('jenis.barang') }}" class="card card-jenis" style="text-decoration: none;">
                <div>
                    <div class="card-header">
                        <div class="icon-container icon-jenis">
                            <i class="fas fa-cogs fa-1x"></i>
                        </div>
                        <h3 class="card-title">Daftar Sparepart</h3>
                    </div>
                    <div class="card-content">
                        <p class="card-description">
                            Lihat daftar semua sparepart  .
                        </p>
                    </div>
                </div>
                <span class="btn btn-jenis">Lihat Daftar <i class="fas fa-arrow-right"></i></span>
            </a>

            <!-- Request Barang -->
            <a href="{{ route('request.barang.index') }}" class="card card-request" style="text-decoration: none;">
                <div>
                    <div class="card-header">
                        <div class="icon-container icon-request">
                            <i class="fas fa-clipboard-list fa-1x"></i>
                        </div>
                        <h3 class="card-title">Request Sparepart</h3>
                    </div>
                    <div class="card-content">
                        <p class="card-description">
                          Request sparepart dengan mudah dan lacak status request.
                        </p>
                    </div>
                </div>
                <span class="btn btn-request">Buat Request <i class="fas fa-plus"></i></span>
            </a>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} Aplikasi Sparepart - PT PGN.COM. Solusi Digital untuk Operasional Unggul.</p>
        </div>
    </div>
</body>
</html>