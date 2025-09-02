<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta http-equiv="content-language" content="pt-BR">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="theme-color" content="#005A24">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <title>Gerenciador de Escolas - CREDE 1</title>
    <meta name="description" content="Gerenciador de Escolas do Sistema CREDE 1 - Coordenadoria Regional de Desenvolvimento da Educação">
    <meta name="author" content="CREDE 1">
    <meta name="keywords" content="escolas, CREDE 1, sistema, educação, gestão escolar">
    <link rel="icon" type="image/png" href="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#005A24',
                        secondary: '#FFA500',
                        accent: '#E6F4EA',
                        dark: '#1A3C34',
                        light: '#F8FAF9',
                        success: '#10B981',
                        warning: '#F59E0B',
                        error: '#EF4444',
                        info: '#3B82F6'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Poppins', 'sans-serif']
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'slide-up': 'slideUp 0.8s ease-out',
                        'scale-in': 'scaleIn 0.5s ease-out',
                        'float': 'float 3s ease-in-out infinite',
                        'pulse-soft': 'pulseSoft 2s ease-in-out infinite'
                    }
                }
            }
        }
    </script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #F8FAF9 0%, #E6F4EA 50%, #F0F9FF 100%);
            min-height: 100vh;
            line-height: 1.6;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 80%, rgba(0, 90, 36, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 165, 0, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(0, 90, 36, 0.02) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes pulseSoft {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -200px 0;
            }

            100% {
                background-position: calc(200px + 100%) 0;
            }
        }

        .header-gradient {
            background: linear-gradient(135deg, #ffffff 0%, #F8FAF9 100%);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 90, 36, 0.08);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .header-gradient::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #005A24 0%, #FFA500 50%, #005A24 100%);
            box-shadow: 0 2px 8px rgba(0, 90, 36, 0.3);
        }





        .search-section {
            margin-bottom: 48px;
        }

        .search-container {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .search-input {
            width: 100%;
            padding: 16px 24px 16px 56px;
            border: 2px solid rgba(0, 90, 36, 0.2);
            border-radius: 16px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .search-input:focus {
            outline: none;
            border-color: #FFA500;
            box-shadow: 0 0 0 4px rgba(255, 165, 0, 0.1), 0 8px 30px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .search-icon {
            position: absolute;
            left: 44px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 18px;
            transition: color 0.3s ease;
            z-index: 10;
        }

        .search-input:focus+.search-icon {
            color: #FFA500;
        }

        .schools-section {
            margin-bottom: 48px;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
        }

        .section-title i {
            color: #005A24;
            font-size: 1.5rem;
        }

        .schools-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .school-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(0, 90, 36, 0.1);
            border-radius: 20px;
            padding: 24px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
            min-height: 280px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .school-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 165, 0, 0.08), transparent);
            transition: left 0.6s ease;
        }

        .school-card:hover::before {
            left: 100%;
        }

        .school-card:hover {
            transform: translateY(-8px) scale(1.02);
            border-color: rgba(255, 165, 0, 0.3);
            box-shadow: 0 12px 32px rgba(0, 90, 36, 0.15);
        }

        .school-header {
            display: flex;
            align-items: flex-start;
            gap: 18px;
            margin-bottom: 20px;
            flex: 1;
        }

        .school-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #166534;
            font-size: 22px;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #dcfce7 0%, #f0fdf4 100%);
            border: 1px solid rgba(22, 101, 52, 0.2);
        }

        .school-icon::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, transparent 100%);
        }

        .school-info h3 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .school-type {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background: linear-gradient(135deg, #E6F4EA 0%, #F0F9FF 100%);
            color: #005A24;
            border-radius: 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid rgba(0, 90, 36, 0.1);
        }

        .school-details {
            margin-bottom: 20px;
            flex: 1;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
            line-height: 1.4;
        }

        .detail-item i {
            width: 16px;
            color: #005A24;
            font-size: 14px;
        }

        .school-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 20px;
            border-top: 1px solid rgba(0, 90, 36, 0.1);
            margin-top: auto;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .status-active {
            background: linear-gradient(135deg, #dcfce7 0%, #f0fdf4 100%);
            color: #166534;
            border: 1px solid rgba(22, 101, 52, 0.2);
        }

        .status-inactive {
            background: linear-gradient(135deg, #fee2e2 0%, #fef2f2 100%);
            color: #991b1b;
            border: 1px solid rgba(153, 27, 27, 0.2);
        }

        .login-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #005A24 0%, #1A3C34 100%);
            color: white;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(0, 90, 36, 0.25);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 90, 36, 0.35);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        /* Mensagem de não encontrado melhorada */
        .no-results {
            text-align: center;
            padding: 80px 20px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 24px;
            border: 2px dashed rgba(0, 90, 36, 0.2);
            margin: 40px auto;
            max-width: 600px;
        }

        .no-results i {
            font-size: 4rem;
            color: #d1d5db;
            margin-bottom: 24px;
        }

        .no-results h3 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 12px;
        }

        .no-results p {
            color: #6b7280;
            font-size: 1rem;
        }

        /* Responsividade melhorada */
        @media (max-width: 1024px) {
            .schools-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 24px;
                max-width: 1000px;
            }

            .search-container {
                max-width: 1000px;
            }
        }

        @media (max-width: 768px) {
            .schools-grid {
                grid-template-columns: 1fr;
                gap: 20px;
                padding: 0 16px;
                max-width: 100%;
            }

            .search-container {
                padding: 0 16px;
                max-width: 100%;
            }

            .search-input {
                padding: 14px 20px 14px 48px;
                font-size: 16px;
            }

            .search-icon {
                left: 20px;
                font-size: 16px;
            }

            .school-card {
                padding: 24px;
            }

            .school-header {
                flex-direction: column;
                text-align: center;
                align-items: center;
            }

            .school-metrics {
                grid-template-columns: 1fr;
            }

            .section-header {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .search-input {
                padding: 12px 16px 12px 44px;
                font-size: 14px;
            }

            .search-icon {
                left: 16px;
                font-size: 14px;
            }

            .school-footer {
                flex-direction: column;
                gap: 16px;
            }
        }

        /* Animações de entrada */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Cards de escolas sempre visíveis */
        .school-card {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }

        /* Scrollbar customizada */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(241, 245, 249, 0.8);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #005A24 0%, #FFA500 100%);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #1A3C34 0%, #E76F51 100%);
        }
    </style>
</head>

<body>
    <header class="header-gradient shadow-sm">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 sm:space-x-6">
                    <div class="w-14 h-14 flex items-center justify-center bg-white rounded-2xl">
                        <img src="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png"
                            alt="Logo Ceará"
                            class="w-10 h-10 object-contain">
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-semibold font-heading">
                            <span class="text-primary">Escolas</span> <span class="text-secondary">Crede 1</span>
                        </h1>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <button class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 transition-all duration-300 flex items-center justify-center"
                        title="Voltar"
                        onclick="window.location.href='../../main/views/subsystems.php'">
                        <i class="fas fa-arrow-left"></i>
                    </button>

                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-6 py-12">
        <div class="max-w-7xl mx-auto">
            <div class="search-section animate-on-scroll">
                <div class="search-container">
                    <input type="text"
                        id="searchSchools"
                        placeholder="Buscar escolas por nome, endereço ou tipo de ensino..."
                        class="search-input"
                        autocomplete="off">
                    <i class="fas fa-search search-icon mx-2"></i>
                </div>
            </div>

            <div class="schools-section animate-on-scroll">

                <div class="schools-grid" id="schoolsGrid">
                    <!-- Card 1 -->
                    <div class="school-card">
                        <div class="school-header">
                            <div class="school-icon">
                                <i class="fas fa-school"></i>
                            </div>
                            <div class="school-info">
                                <h3>EEEP PROFESSORA ALDA FAÇANHA</h3>
                            </div>
                        </div>
                        <div class="school-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Rua das Flores, 123 - Centro</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-phone"></i>
                                <span>(85) 3333-4444</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-envelope"></i>
                                <span>salaberga@gmail.com</span>
                            </div>
                        </div>
                        <div class="school-footer">
                            <span class="status-badge status-active">
                                <i class="fas fa-circle"></i>
                                Ativa
                            </span>
                            <a href="views/login.php?escola=EEEP%20PROFESSORA%20ALDA%20FA%C3%87ANHA" class="login-btn">
                                <i class="fas fa-external-link-alt"></i>
                                Acessar
                            </a>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="school-card">
                        <div class="school-header">
                            <div class="school-icon">
                                <i class="fas fa-school"></i>
                            </div>
                            <div class="school-info">
                                <h3>EEEP PROFESSORA MARLY FERREIRA MARTINS</h3>
                            </div>
                        </div>
                        <div class="school-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Av. Principal, 456 - Bairro Novo</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-phone"></i>
                                <span>(85) 3333-5555</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-envelope"></i>
                                <span>falcao@gmail.com</span>
                            </div>
                        </div>
                        <div class="school-footer">
                            <span class="status-badge status-active">
                                <i class="fas fa-circle"></i>
                                Ativa
                            </span>
                            <a href="views/login.php?escola=EEEP%20PROFESSORA%20MARLY%20FERREIRA%20MARTINS" class="login-btn">
                                <i class="fas fa-external-link-alt"></i>
                                Acessar
                            </a>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="school-card">
                        <div class="school-header">
                            <div class="school-icon">
                                <i class="fas fa-school"></i>
                            </div>
                            <div class="school-info">
                                <h3>EEEP PROF. ANTONIO VALMIR</h3>
                            </div>
                        </div>
                        <div class="school-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Sítio Boa Vista, Zona Rural</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-phone"></i>
                                <span>(85) 3333-6666</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-envelope"></i>
                                <span>holanda@gmail.com</span>
                            </div>
                        </div>
                        <div class="school-footer">
                            <span class="status-badge status-active">
                                <i class="fas fa-circle"></i>
                                Ativa
                            </span>
                            <a href="views/login.php?escola=EEEP%20PROF.%20ANTONIO%20VALMIR" class="login-btn">
                                <i class="fas fa-external-link-alt"></i>
                                Acessar
                            </a>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div class="school-card">
                        <div class="school-header">
                            <div class="school-icon">
                                <i class="fas fa-school"></i>
                            </div>
                            <div class="school-info">
                                <h3>EEEP EUSÉBIO DE QUEIROZ</h3>
                            </div>
                        </div>
                        <div class="school-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Rua da Alegria, 789 - Jardim</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-phone"></i>
                                <span>(85) 3333-7777</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-envelope"></i>
                                <span>celeste@gmail.com</span>
                            </div>
                        </div>
                        <div class="school-footer">
                            <span class="status-badge status-active">
                                <i class="fas fa-circle"></i>
                                Ativa
                            </span>
                            <a href="views/login.php?escola=EEEP%20EUS%C3%89BIO%20DE%20QUEIROZ" class="login-btn">
                                <i class="fas fa-external-link-alt"></i>
                                Acessar
                            </a>
                        </div>
                    </div>

                    <!-- Card 5 -->
                    <div class="school-card">
                        <div class="school-header">
                            <div class="school-icon">
                                <i class="fas fa-school"></i>
                            </div>
                            <div class="school-info">
                                <h3>EEEP JOSÉ IVANILTON NOCRATO</h3>
                            </div>
                        </div>
                        <div class="school-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Av. da Educação, 321 - Cidade Nova</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-phone"></i>
                                <span>(85) 3333-8888</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-envelope"></i>
                                <span>futuro@gmail.com</span>
                            </div>
                        </div>
                        <div class="school-footer">
                            <span class="status-badge status-active">
                                <i class="fas fa-circle"></i>
                                Ativa
                            </span>
                            <a href="views/login.php?escola=EEEP%20JOS%C3%89%20IVANILTON%20NOCRATO" class="login-btn">
                                <i class="fas fa-external-link-alt"></i>
                                Acessar
                            </a>
                        </div>
                    </div>

                    <!-- Card 6 -->
                    <div class="school-card">
                        <div class="school-header">
                            <div class="school-icon">
                                <i class="fas fa-school"></i>
                            </div>
                            <div class="school-info">
                                <h3>EEEP PROFº Fcº ARISTÓTELES DE SOUSA</h3>
                            </div>
                        </div>
                        <div class="school-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Aldeia Indígena, Reserva Natural</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-phone"></i>
                                <span>(85) 3333-9999</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-envelope"></i>
                                <span>paje@gmail.com</span>
                            </div>
                        </div>
                        <div class="school-footer">
                            <span class="status-badge status-active">
                                <i class="fas fa-circle"></i>
                                Ativa
                            </span>
                            <a href="views/login.php?escola=EEEP%20PROF%C2%BA%20Fc%C2%BA%20ARIST%C3%93TELES%20DE%20SOUSA" class="login-btn">
                                <i class="fas fa-external-link-alt"></i>
                                Acessar
                            </a>
                        </div>
                    </div>

                    <!-- Card 7 -->
                    <div class="school-card">
                        <div class="school-header">
                            <div class="school-icon">
                                <i class="fas fa-school"></i>
                            </div>
                            <div class="school-info">
                                <h3>EEEP MARIA CARMEM VIEIRA MOREIRA</h3>
                            </div>
                        </div>
                        <div class="school-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Aldeia Indígena, Reserva Natural</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-phone"></i>
                                <span>(85) 3333-9999</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-envelope"></i>
                                <span>paje@gmail.com</span>
                            </div>
                        </div>
                        <div class="school-footer">
                            <span class="status-badge status-active">
                                <i class="fas fa-circle"></i>
                                Ativa
                            </span>
                            <a href="views/login.php?escola=EEEP%20MARIA%20CARMEM%20VIEIRA%20MOREIRA" class="login-btn">
                                <i class="fas fa-external-link-alt"></i>
                                Acessar
                            </a>
                        </div>
                    </div>

                    <!-- Card 8 -->
                    <div class="school-card">
                        <div class="school-header">
                            <div class="school-icon">
                                <i class="fas fa-school"></i>
                            </div>
                            <div class="school-info">
                                <h3>EEEP GOV. LUIZ GONZAGA FONSECA MOTA</h3>
                            </div>
                        </div>
                        <div class="school-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Aldeia Indígena, Reserva Natural</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-phone"></i>
                                <span>(85) 3333-9999</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-envelope"></i>
                                <span>paje@gmail.com</span>
                            </div>
                        </div>
                        <div class="school-footer">
                            <span class="status-badge status-active">
                                <i class="fas fa-circle"></i>
                                Ativa
                            </span>
                            <a href="views/login.php?escola=EEEP%20GOV.%20LUIZ%20GONZAGA%20FONSECA%20MOTA" class="login-btn">
                                <i class="fas fa-external-link-alt"></i>
                                Acessar
                            </a>
                        </div>
                    </div>

                    <!-- Card 9 -->
                    <div class="school-card">
                        <div class="school-header">
                            <div class="school-icon">
                                <i class="fas fa-school"></i>
                            </div>
                            <div class="school-info">
                                <h3>EEEP SALABERGA TORQUATO GOMES DE MATOS</h3>
                            </div>
                        </div>
                        <div class="school-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Aldeia Indígena, Reserva Natural</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-phone"></i>
                                <span>(85) 3333-9999</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-envelope"></i>
                                <span>paje@gmail.com</span>
                            </div>
                        </div>
                        <div class="school-footer">
                            <span class="status-badge status-active">
                                <i class="fas fa-circle"></i>
                                Ativa
                            </span>
                            <a href="views/login.php?escola=EEEP%20SALABERGA%20TORQUATO%20GOMES%20DE%20MATOS" class="login-btn">
                                <i class="fas fa-external-link-alt"></i>
                                Acessar
                            </a>
                        </div>
                    </div>

                    <!-- Card 10 -->
                    <div class="school-card">
                        <div class="school-header">
                            <div class="school-icon">
                                <i class="fas fa-school"></i>
                            </div>
                            <div class="school-info">
                                <h3>EEEP PROFª LUIZA DE TEODORO VIEIRA</h3>
                            </div>
                        </div>
                        <div class="school-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Aldeia Indígena, Reserva Natural</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-phone"></i>
                                <span>(85) 3333-9999</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-envelope"></i>
                                <span>paje@gmail.com</span>
                            </div>
                        </div>
                        <div class="school-footer">
                            <span class="status-badge status-active">
                                <i class="fas fa-circle"></i>
                                Ativa
                            </span>
                            <a href="views/login.php?escola=EEEP%20PROF%C2%AA%20LUIZA%20DE%20TEODORO%20VIEIRA" class="login-btn">
                                <i class="fas fa-external-link-alt"></i>
                                Acessar
                            </a>
                        </div>
                    </div>

                    <!-- Card 11 -->
                    <div class="school-card">
                        <div class="school-header">
                            <div class="school-icon">
                                <i class="fas fa-school"></i>
                            </div>
                            <div class="school-info">
                                <h3>EEEP RAIMUNDO CÉLIO RODRIGUES</h3>
                            </div>
                        </div>
                        <div class="school-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Aldeia Indígena, Reserva Natural</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-phone"></i>
                                <span>(85) 3333-9999</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-envelope"></i>
                                <span>paje@gmail.com</span>
                            </div>
                        </div>
                        <div class="school-footer">
                            <span class="status-badge status-active">
                                <i class="fas fa-circle"></i>
                                Ativa
                            </span>
                            <a href="views/login.php?escola=EEEP%20RAIMUNDO%20C%C3%89LIO%20RODRIGUES" class="login-btn">
                                <i class="fas fa-external-link-alt"></i>
                                Acessar
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div id="noResults" class="no-results" style="display: none;">
                <i class="fas fa-search-minus"></i>
                <h3>Nenhuma escola encontrada</h3>
                <p>Tente ajustar os filtros ou buscar por outros termos</p>
            </div>
        </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elementos
            const searchInput = document.getElementById('searchSchools');
            const animatedElements = document.querySelectorAll('.animate-on-scroll');
            const schoolCards = document.querySelectorAll('.school-card');

            // Função de busca simplificada
            function performSearch() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                let visibleCount = 0;

                schoolCards.forEach((card) => {
                    const schoolName = card.querySelector('h3').textContent.toLowerCase();
                    const schoolAddress = card.querySelector('.detail-item span').textContent.toLowerCase();

                    const isVisible = schoolName.includes(searchTerm) || schoolAddress.includes(searchTerm);

                    if (isVisible) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Mostrar/ocultar mensagem de "nenhum resultado"
                const noResults = document.getElementById('noResults');
                if (visibleCount === 0 && searchTerm !== '') {
                    noResults.style.display = 'block';
                } else {
                    noResults.style.display = 'none';
                }
            }

            // Event listeners para busca
            searchInput.addEventListener('input', performSearch);
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    performSearch();
                }
            });

            // Animação de entrada dos elementos
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.classList.add('visible');
                        }, index * 100);
                    }
                });
            }, observerOptions);

            animatedElements.forEach(element => {
                observer.observe(element);
            });

            console.log('Gerenciador de Escolas carregado com sucesso!');
        });
    </script>
</body>

</html>