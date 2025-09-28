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
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --info: #4895ef;
            --warning: #f72585;
            --danger: #e63946;
            --light: #f8f9fa;
            --dark: #212529;
            --card-border-radius: 15px;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fb 0%, #e6e9f0 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 2rem 0;
        }
        
        .profile-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .profile-card {
            background: white;
            border-radius: var(--card-border-radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
            transition: var(--transition);
        }
        
        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .profile-header {
            background: linear-gradient(120deg, var(--primary), var(--secondary));
            padding: 2.5rem 2rem;
            text-align: center;
            color: white;
            position: relative;
        }
        
        .profile-avatar {
            width: 130px;
            height: 130px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: var(--transition);
        }
        
        .profile-avatar:hover {
            transform: scale(1.05);
        }
        
        .profile-name {
            margin-top: 1.2rem;
            font-weight: 700;
            font-size: 1.8rem;
        }
        
        .profile-role {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }
        
        .profile-contact {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 1rem;
        }
        
        .profile-contact a {
            color: white;
            font-size: 1.2rem;
            transition: var(--transition);
        }
        
        .profile-contact a:hover {
            color: rgba(255, 255, 255, 0.8);
            transform: translateY(-3px);
        }
        
        .profile-body {
            padding: 2.5rem;
        }
        
        .info-section {
            margin-bottom: 2rem;
        }
        
        .section-title {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .info-item {
            display: flex;
            margin-bottom: 1.2rem;
            align-items: flex-start;
        }
        
        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(67, 97, 238, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary);
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        
        .info-content {
            flex: 1;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.2rem;
            font-size: 0.95rem;
        }
        
        .info-value {
            color: #6c757d;
            font-size: 1.05rem;
        }
        
        .profile-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        
        .btn-profile {
            padding: 0.7rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-back {
            background: white;
            color: var(--primary);
            border: 2px solid var(--primary);
        }
        
        .btn-back:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
        }
        
        .btn-edit {
            background: linear-gradient(120deg, var(--primary), var(--secondary));
            color: white;
            border: none;
        }
        
        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
            color: white;
        }
        
        .btn-password {
            background: linear-gradient(120deg, var(--warning), #fb6090);
            color: white;
            border: none;
        }
        
        .btn-password:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(247, 37, 133, 0.3);
            color: white;
        }
        
        /* Stats Section */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: var(--card-border-radius);
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            text-align: center;
            transition: var(--transition);
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
        }
        
        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--primary);
        }
        
        .stat-label {
            color: #6c757d;
            font-weight: 500;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .profile-body {
                padding: 1.5rem;
            }
            
            .profile-actions {
                flex-direction: column;
            }
            
            .btn-profile {
                width: 100%;
                justify-content: center;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .profile-contact {
                flex-wrap: wrap;
            }
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .profile-card {
            animation: fadeIn 0.6s ease-out;
        }
        
        .stat-card {
            animation: fadeIn 0.6s ease-out;
        }
        
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
    </style>
</head>
<body>
    <div class="container profile-container">
        <!-- Profile Card -->
        <div class="profile-card">
            <!-- Header -->
            <div class="profile-header">
                <img src="https://ui-avatars.com/api/?name=John+Doe&background=fff&color=4361ee&size=200&font-size=0.4&bold=true" 
                     alt="Foto Profil" class="profile-avatar">
                <h2 class="profile-name">John Doe</h2>
                <p class="profile-role">Kepala Gudang</p>
                <p><i class="bi bi-envelope me-1"></i> kepala.gudang@example.com</p>
                
                <div class="profile-contact">
                    <a href="tel:+6281234567890" title="Telepon">
                        <i class="bi bi-telephone-fill"></i>
                    </a>
                    <a href="mailto:kepala.gudang@example.com" title="Email">
                        <i class="bi bi-envelope-fill"></i>
                    </a>
                    <a href="#" title="WhatsApp">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                    <a href="#" title="LinkedIn">
                        <i class="bi bi-linkedin"></i>
                    </a>
                </div>
            </div>
            
            <!-- Body -->
            <div class="profile-body">
                <!-- Informasi Pribadi -->
                <div class="info-section">
                    <h4 class="section-title"><i class="bi bi-person-badge"></i> Informasi Pribadi</h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Nama Lengkap</div>
                                    <div class="info-value">John Doe</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="bi bi-at"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Username</div>
                                    <div class="info-value">kepala_gudang</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="bi bi-briefcase"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Jabatan</div>
                                    <div class="info-value">Kepala Gudang</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="bi bi-telephone"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Nomor Telepon</div>
                                    <div class="info-value">+62 812 3456 7890</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Alamat</div>
                            <div class="info-value">Jl. Industri No. 123, Jakarta Selatan, DKI Jakarta 12345</div>
                        </div>
                    </div>
                </div>
                
                <!-- Informasi Gudang -->
                <div class="info-section">
                    <h4 class="section-title"><i class="bi bi-building"></i> Informasi Gudang</h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="bi bi-buildings"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Nama Gudang</div>
                                    <div class="info-value">Gudang Pusat Jakarta</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="bi bi-geo"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Lokasi Gudang</div>
                                    <div class="info-value">Jakarta Selatan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Bergabung Sejak</div>
                            <div class="info-value">15 Januari 2020</div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="profile-actions">
                    <a href="{{ route('kepalagudang.dashboard') }}" class="btn btn-profile btn-back">
                        <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
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
        
        <!-- Stats Section -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon" style="background-color: rgba(67, 97, 238, 0.1); color: var(--primary);">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-value">1,258</div>
                <div class="stat-label">Total Barang</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background-color: rgba(76, 201, 240, 0.1); color: var(--success);">
                    <i class="bi bi-box-arrow-in-down"></i>
                </div>
                <div class="stat-value">42</div>
                <div class="stat-label">Barang Masuk/Bulan</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background-color: rgba(247, 37, 133, 0.1); color: var(--warning);">
                    <i class="bi bi-box-arrow-up"></i>
                </div>
                <div class="stat-value">28</div>
                <div class="stat-label">Barang Keluar/Bulan</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background-color: rgba(230, 57, 70, 0.1); color: var(--danger);">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-value">96%</div>
                <div class="stat-label">Efisiensi Gudang</div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>