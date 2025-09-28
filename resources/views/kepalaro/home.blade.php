<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Sparepart PGN - Kepala RO</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #1a365d;
            --primary-light: #2d3748;
            --secondary: #3182ce;
            --accent: #4299e1;
            --light: #ebf8ff;
            --white: #ffffff;
            --gray: #a0aec0;
            --dark-text: #2d3748;
            --shadow: 0 10px 25px -5px rgba(26, 54, 93, 0.2);
            --shadow-hover: 0 20px 25px -5px rgba(26, 54, 93, 0.25);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
            color: var(--white);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 90% 5%, rgba(66, 153, 225, 0.15) 0%, transparent 30%),
                radial-gradient(circle at 10% 80%, rgba(49, 130, 206, 0.1) 0%, transparent 40%);
            z-index: -1;
        }

        .container {
            width: 100%;
            max-width: 1000px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2.5rem;
            z-index: 1;
            padding: 2rem;
            margin: 2rem auto;
        }

        .header {
            text-align: center;
            max-width: 600px;
            animation: fadeIn 1s ease-out;
            padding: 0 20px;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.8rem;
            color: var(--white);
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        h2 {
            font-size: 1.3rem;
            font-weight: 400;
            margin-bottom: 1.2rem;
            color: var(--light);
            opacity: 0.9;
            line-height: 1.3;
        }

        .description {
            color: var(--gray);
            line-height: 1.5;
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.8rem;
            width: 100%;
            max-width: 800px;
            animation: slideUp 1s ease-out;
        }

        .card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background: rgba(255, 255, 255, 0.06);
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 2rem;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            min-height: 280px;
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, var(--accent), var(--secondary));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.5s ease;
        }

        .card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: var(--shadow-hover);
            background: rgba(255, 255, 255, 0.09);
        }

        .card:hover::before {
            transform: scaleX(1);
        }

        .card-icon {
            font-size: 2.5rem;
            color: var(--accent);
            margin-bottom: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.09);
            border: 1px solid rgba(255, 255, 255, 0.12);
            transition: transform 0.3s ease;
        }

        .card:hover .card-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .card-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 0.8rem;
            color: var(--white);
            line-height: 1.3;
        }

        .card-description {
            color: var(--gray);
            line-height: 1.5;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .btn {
            padding: 0.8rem 1.6rem;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            margin-top: auto;
            border: none;
            cursor: pointer;
            font-family: inherit;
            font-size: 0.9rem;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, var(--secondary), var(--accent));
            z-index: -1;
            transition: transform 0.3s ease;
            transform: scaleX(0);
            transform-origin: right;
        }

        .btn-primary {
            background: transparent;
            color: white;
            border: 1.5px solid rgba(255, 255, 255, 0.25);
        }

        .btn:hover::before {
            transform: scaleX(1);
            transform-origin: left;
        }

        .btn:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            border-color: transparent;
        }

        .btn i {
            transition: transform 0.3s ease;
        }

        .btn:hover i {
            transform: translateX(3px);
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Particle effect */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            animation: float 15s infinite linear;
        }

        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) rotate(360deg);
                opacity: 0;
            }
        }

        /* Responsive Design */
        @media (min-width: 1024px) {
            /* Desktop - tinggi viewport penuh tanpa scroll */
            body {
                padding: 0;
                overflow: hidden;
            }
            
            .container {
                height: 100vh;
                margin: 0;
                padding: 2rem;
            }
        }

        @media (max-width: 900px) {
            /* Tablet */
            .container {
                padding: 1.5rem;
            }
            
            h1 {
                font-size: 2.2rem;
            }
            
            h2 {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 768px) {
            /* Mobile - dengan scroll */
            body {
                padding: 0;
                display: block;
                overflow-y: auto;
            }
            
            .container {
                margin: 0;
                padding: 2rem 1.5rem;
                min-height: auto;
            }
            
            .cards-container {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .card {
                min-height: 250px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            h2 {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 480px) {
            /* Mobile kecil */
            .container {
                padding: 1.5rem 1rem;
            }
            
            .card {
                padding: 1.5rem;
            }
            
            .card-icon {
                width: 60px;
                height: 60px;
                font-size: 2rem;
            }
            
            h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>

<body>
    <div class="particles" id="particles"></div>

    <div class="container">
        <div class="header">
            <h1>Sistem Manajemen Sparepart</h1>
            <h2>Head Regional Office</h2>
            <p class="description">
                Monitoring dan Persetujuan Permintaan Sparepart
            </p>
        </div>

        <div class="cards-container">
            <!-- Dashboard Card -->
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <h3 class="card-title">Dashboard Request</h3>
                <p class="card-description">
                    Kelola permintaan sparepart dari tim lapangan 
                </p>
                <a href="{{ route('kepalaro.dashboard') }}" class="btn btn-primary">
                    Lihat Request <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- History Card -->
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h3 class="card-title">Riwayat Persetujuan</h3>
                <p class="card-description">
                    Telusuri riwayat persetujuan 
                </p>
                <a href="{{ route('kepalaro.history') }}" class="btn btn-primary">
                    Lihat Riwayat <i class="fas fa-list"></i>
                </a>
            </div>
        </div>
    </div>

    <script>
        // Create particle effect
        document.addEventListener('DOMContentLoaded', function () {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 25;
            
            // Sesuaikan jumlah particle untuk desktop
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                
                // Random properties
                const size = Math.random() * 4 + 2;
                const posX = Math.random() * 100;
                const delay = Math.random() * 15;
                const duration = Math.random() * 10 + 15;
                const opacity = Math.random() * 0.3 + 0.1;
                
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.left = `${posX}%`;
                particle.style.opacity = opacity;
                particle.style.animationDelay = `${delay}s`;
                particle.style.animationDuration = `${duration}s`;
                
                particlesContainer.appendChild(particle);
            }

            // Card hover effect enhancement
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    cards.forEach(otherCard => {
                        if (otherCard !== card) {
                            otherCard.style.transform = 'scale(0.97)';
                            otherCard.style.opacity = '0.9';
                        }
                    });
                });

                card.addEventListener('mouseleave', () => {
                    cards.forEach(otherCard => {
                        otherCard.style.transform = '';
                        otherCard.style.opacity = '';
                    });
                });
            });
        });
    </script>
</body>
</html>