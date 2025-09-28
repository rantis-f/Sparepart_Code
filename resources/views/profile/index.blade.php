<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Kepala Gudang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #eef0ff;
            --secondary: #6c757d;
            --dark: #343a40;
            --light: #f8f9fa;
            --border-radius: 12px;
            --shadow: 0 5px 15px rgba(0, 0, 0, 0.06);
            --transition: all 0.3s ease;
        }

        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #495057;
            padding: 2rem 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .profile-card {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
        }

        .profile-card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            background: linear-gradient(120deg, var(--primary), #5a70f0);
            padding: 2.5rem 2rem;
            text-align: center;
            color: white;
            position: relative;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            background: white;
            padding: 4px;
        }

        .profile-name {
            margin-top: 1.2rem;
            font-weight: 700;
            font-size: 1.6rem;
            margin-bottom: 0.2rem;
        }

        .profile-role {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }

        .profile-contact {
            margin-top: 0.5rem;
            font-size: 0.95rem;
        }

        .profile-body {
            padding: 2rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            padding: 1rem;
            background: var(--primary-light);
            border-radius: 10px;
            transition: var(--transition);
        }

        .info-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.1);
        }

        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.3rem;
            font-size: 0.9rem;
        }

        .info-value {
            color: var(--secondary);
            font-size: 1rem;
        }

        .profile-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .btn-profile {
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .btn-back {
            background: white;
            color: var(--primary);
            border: 1px solid var(--primary);
        }

        .btn-back:hover {
            background: #f8f9fa;
        }

        .btn-edit {
            background: var(--primary);
            color: white;
            border: none;
        }

        .btn-edit:hover {
            background: #3a56e0;
            color: white;
        }

        .btn-password {
            background: white;
            color: #6c757d;
            border: 1px solid #dee2e6;
        }

        .btn-password:hover {
            background: #f8f9fa;
            color: var(--dark);
        }

        @media (max-width: 768px) {
            .profile-body {
                padding: 1.5rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .profile-actions {
                flex-direction: column;
            }

            .btn-profile {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="profile-card">
            <!-- Header -->
            <div class="profile-header">
                <img src="https://ui-avatars.com/api/?name=John+Doe&background=4361ee&color=fff&size=200"
                    alt="Foto Profil" class="profile-avatar">
                <h3 class="profile-name">John Doe</h3>
                <p class="profile-role">Kepala Gudang</p>
                <p class="profile-contact">
                    <i class="bi bi-envelope me-1"></i> kepala.gudang@example.com
                </p>
            </div>

            <!-- Body -->
            <div class="profile-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Nama Lengkap</div>
                            <div class="info-value">John Doe</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-at"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Username</div>
                            <div class="info-value">kepala_gudang</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Jabatan</div>
                            <div class="info-value">Kepala Gudang</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Nomor Telepon</div>
                            <div class="info-value">+62 812 3456 7890</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Alamat</div>
                            <div class="info-value">Jl. Industri No. 123, Jakarta</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Lokasi Gudang</div>
                            <div class="info-value">Gudang Pusat Jakarta</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="profile-actions">
                    <a href="{{ url()->previous() }}" class="btn btn-profile btn-back">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <a href="#" class="btn btn-profile btn-edit">
                        <i class="bi bi-pencil"></i> Edit Profil
                    </a>
                    <a href="#" class="btn btn-profile btn-password">
                        <i class="bi bi-key"></i> Ganti Password
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>