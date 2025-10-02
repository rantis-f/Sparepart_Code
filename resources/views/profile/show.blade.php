<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - Sistem Manajemen Sparepart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4e73df;
            --secondary: #6c757d;
            --success: #1cc88a;
            --light-bg: #f8f9fc;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        .main-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .profile-card {
            border-radius: 15px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            display: flex;
            flex-direction: row;
            height: 480px;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary) 0%, #2a3d8f 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
            width: 35%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .avatar-container {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid rgba(255, 255, 255, 0.3);
            object-fit: cover;
            background-color: #f8f9fc;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 3rem;
        }

        .role-badge {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: var(--success);
            color: white;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid white;
        }

        .profile-body {
            background: white;
            padding: 2rem;
            width: 65%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .info-item {
            display: flex;
            padding: 1rem;
            border-radius: 10px;
            background: #f8f9fc;
            transition: all 0.3s;
        }

        .info-item:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            margin-right: 15px;
            flex-shrink: 0;
            box-shadow: 0 0.15rem 0.5rem rgba(0, 0, 0, 0.1);
        }

        .info-content {
            flex-grow: 1;
        }

        .info-label {
            font-size: 0.85rem;
            color: var(--secondary);
            margin-bottom: 0.2rem;
        }

        .info-value {
            font-weight: 500;
            color: #5a5c69;
        }

        .btn-back {
            background: white;
            color: var(--primary);
            border: 1px solid var(--primary);
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            margin-top: 2rem;
            align-self: center;
        }

        .btn-back:hover {
            background: var(--primary);
            color: white;
        }

        .role-admin {
            background: linear-gradient(135deg, #4e73df 0%, #2a3d8f 100%);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        .role-karo {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        .role-kagud {
            background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            white-space: nowrap;
            /* cegah teks turun ke bawah */

        }

        .role-user {
            background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        @media (max-width: 992px) {
            .profile-card {
                flex-direction: column;
                height: auto;
                max-width: 600px;
            }

            .profile-header,
            .profile-body {
                width: 100%;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .avatar {
                width: 100px;
                height: 100px;
                font-size: 2.5rem;
            }

            .profile-header,
            .profile-body {
                padding: 1.5rem;
            }

            body {
                overflow: auto;
                height: auto;
            }

            .main-container {
                padding: 10px;
            }
        }
    </style>
</head>

<body>

    <div class="main-container">
        <div class="profile-card">
            <div class="profile-header">
                <div class="avatar-container">
                    <div class="avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="role-badge">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
                <h3>{{ $user->name }}</h3>
                <p class="mb-0">Sistem Manajemen Sparepart</p>
                <p>{{ $user->bagian  }}</p>
            </div>

            <div class="profile-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Nama Lengkap</div>
                            <div class="info-value">{{ $user->name }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Alamat Email</div>
                            <div class="info-value">{{ $user->email }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-user-tag"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Role</div>
                            <div class="info-value">
                                @if($user->role == 1)
                                    <span class="role-admin">Superadmin</span>
                                @elseif($user->role == 2)
                                    <span class="role-karo">Kepala RO</span>
                                @elseif($user->role == 3)
                                    <span class="role-kagud">Kepala Gudang</span>
                                @elseif($user->role == 4)
                                    <span class="role-user">User</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Regional Office</div>
                            <div class="info-value">{{ $user->region ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Atasan</div>
                            <div class="info-value">{{ $user->atasan ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ url()->previous() }}" class="btn btn-back">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>