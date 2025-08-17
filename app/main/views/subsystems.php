<?php 
require_once('../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();
?>
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

    <title>Subsistemas - CREDE</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="Subsistemas do Sistema CREDE - Coordenadoria Regional de Desenvolvimento da Educação">
    <meta name="author" content="CREDE">
    <meta name="keywords" content="subsistemas, CREDE, sistema, educação, controle de estoque">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png">

    <!-- Fontes -->
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
        /* Reset e base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #F8FAF9 0%, #E6F4EA 100%);
            min-height: 100vh;
            line-height: 1.6;
        }

        /* Animações */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @keyframes pulseSoft {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* Header melhorado */
        .header-gradient {
            background: linear-gradient(135deg, #ffffff 0%, #F8FAF9 100%);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 90, 36, 0.08);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            position: relative;
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

        .header-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(0, 90, 36, 0.02), transparent);
            pointer-events: none;
        }

        .logo-container {
            background: linear-gradient(135deg, #005A24 0%, #1A3C34 100%);
            box-shadow: 0 4px 12px rgba(0, 90, 36, 0.3);
        }

        /* Search bar aprimorada */
        .search-container {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
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
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .search-input:focus + .search-icon {
            color: #FFA500;
        }

        /* Cards melhorados */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            max-width: 800px;
            margin: 0 auto;
            justify-content: start;
        }

        .system-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 90, 36, 0.1);
            border-radius: 20px;
            padding: 32px 24px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .system-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 165, 0, 0.1), transparent);
            transition: left 0.6s ease;
        }

        .system-card:hover::before {
            left: 100%;
        }

        .system-card:hover {
            transform: translateY(-8px) scale(1.02);
            border-color: #FFA500;
            box-shadow: 0 20px 40px rgba(0, 90, 36, 0.15);
        }

        .system-card:active {
            transform: translateY(-4px) scale(1.01);
        }

        .card-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .system-card:hover .card-icon {
            transform: scale(1.1) rotate(5deg);
            animation: float 3s ease-in-out infinite;
        }

        .card-title {
            font-family: 'Poppins', sans-serif;
            font-size: 20px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 12px;
            transition: color 0.3s ease;
        }

        .system-card:hover .card-title {
            color: #005A24;
        }

        .card-badge {
            display: inline-block;
            padding: 6px 16px;
            background: #005A24;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(0, 90, 36, 0.3);
        }

        /* Gradientes para ícones */
        .icon-primary { background: linear-gradient(135deg, #005A24 0%, #1A3C34 100%); }
        .icon-purple { background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); }
        .icon-orange { background: linear-gradient(135deg, #F97316 0%, #EA580C 100%); }
        .icon-green { background: linear-gradient(135deg, #10B981 0%, #059669 100%); }
        .icon-blue { background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%); }

        /* Responsividade melhorada */
        @media (max-width: 768px) {
            .card-grid {
                grid-template-columns: 1fr;
                gap: 16px;
                padding: 0 16px;
                max-width: 100%;
            }
            
            .search-container {
                padding: 0 16px;
                max-width: 100%;
            }
            
            .system-card {
                padding: 24px 20px;
            }
            
            .card-icon {
                width: 64px;
                height: 64px;
                font-size: 24px;
            }
            
            .search-input {
                padding: 14px 20px 14px 48px;
                font-size: 16px;
            }
        }

        @media (min-width: 769px) {
            .mobile-footer {
                display: none !important;
            }
            
            .header-content {
                justify-content: space-between;
            }
            
            body {
                padding-bottom: 0;
            }

            /* Animação de entrada do header */
            .header-gradient {
                animation: slideDown 0.6s ease-out;
            }

            @keyframes slideDown {
                from {
                    transform: translateY(-100%);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: row;
                gap: 16px;
                text-align: center;
                justify-content: center;
            }
            
            .mobile-footer {
                display: block !important;
            }
            
            body {
                padding-bottom: 100px;
            }
        }
        
        @media (max-width: 480px) {
            .header-content {
                gap: 12px;
            }
            
            .header-nav {
                gap: 8px;
            }
            
            .header-nav a {
                padding: 10px;
                font-size: 14px;
            }
            
            .header-nav button {
                width: 40px;
                height: 40px;
            }
        }

        /* Estados de loading e feedback */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .card-loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Footer Mobile */
        .mobile-footer {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #ffffff;
            border-top: 1px solid #e5e7eb;
            padding: 16px 0 24px 0;
            z-index: 1000;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 20px 20px 0 0;
        }

        .footer-buttons {
            display: flex;
            justify-content: space-around;
            align-items: center;
            max-width: 400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: none;
            border: none;
            color: #6b7280;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 12px;
            min-width: 60px;
        }

        .footer-btn i {
            font-size: 20px;
            margin-bottom: 4px;
            transition: all 0.3s ease;
        }

        .footer-btn .footer-text {
            font-size: 11px;
            font-weight: 500;
            line-height: 1.2;
        }

        .footer-btn:hover {
            color: #005A24;
            background: rgba(0, 90, 36, 0.05);
        }

        .footer-btn.active {
            color: #005A24;
            background: rgba(0, 90, 36, 0.1);
        }

        .footer-btn.active i {
            transform: scale(1.1);
        }

        /* Painéis de Acessibilidade e Perfil */
        .accessibility-panel,
        .profile-panel {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            backdrop-filter: blur(5px);
        }

        .accessibility-content,
        .profile-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: #ffffff;
            border-radius: 20px 20px 0 0;
            padding: 24px;
            max-height: 80vh;
            overflow-y: auto;
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(100%);
            }
            to {
                transform: translateY(0);
            }
        }

        .accessibility-header,
        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e5e7eb;
        }

        .accessibility-header h3,
        .profile-header h3 {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
        }

        .close-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f3f4f6;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .close-btn:hover {
            background: #e5e7eb;
            color: #374151;
        }

        .option-group {
            margin-bottom: 24px;
        }

        .option-group h4 {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 12px;
        }

        .option-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .option-btn {
            padding: 8px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #ffffff;
            color: #374151;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .option-btn:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }

        .option-btn.active {
            background: #005A24;
            color: #ffffff;
            border-color: #005A24;
        }

        /* Perfil */
        .profile-info {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
            padding: 16px;
            background: #f9fafb;
            border-radius: 12px;
        }

        .profile-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #005A24;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 24px;
        }

        .profile-details h4 {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .profile-details p {
            color: #6b7280;
            margin-bottom: 4px;
        }

        .profile-role {
            display: inline-block;
            padding: 4px 8px;
            background: #005A24;
            color: #ffffff;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .profile-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .profile-action-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #ffffff;
            color: #374151;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .profile-action-btn:hover {
            background: #f9fafb;
            border-color: #005A24;
            color: #005A24;
        }

        .profile-action-btn i {
            width: 16px;
            color: #6b7280;
        }

        /* Botões do Header (Desktop) */
        .header-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(0, 90, 36, 0.1);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #005A24;
            font-size: 16px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .header-btn:hover {
            background: rgba(0, 90, 36, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 90, 36, 0.2);
        }

        .header-btn:active {
            transform: translateY(0);
        }

        /* Botões do Header com Texto (Desktop) */
        .header-btn-with-text {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: none;
            border: none;
            color: #6b7280;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            padding: 12px 16px;
            border-radius: 16px;
            min-width: 70px;
            position: relative;
            overflow: hidden;
        }

        .header-btn-with-text::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 90, 36, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .header-btn-with-text:hover::before {
            left: 100%;
        }

        .header-btn-with-text i {
            font-size: 20px;
            margin-bottom: 6px;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .header-btn-with-text span {
            font-size: 11px;
            font-weight: 600;
            line-height: 1.2;
            position: relative;
            z-index: 1;
            letter-spacing: 0.3px;
        }

        .header-btn-with-text:hover {
            color: #005A24;
            background: rgba(0, 90, 36, 0.08);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 90, 36, 0.15);
        }

        .header-btn-with-text:hover i {
            transform: scale(1.15) translateY(-2px);
        }

        .header-btn-with-text:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(0, 90, 36, 0.1);
        }

        /* Botão Sair especial */
        .header-btn-with-text.text-red-600 {
            color: #dc2626;
        }

        .header-btn-with-text.text-red-600:hover {
            color: #b91c1c;
            background: rgba(220, 38, 38, 0.08);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);
        }

        .header-btn-with-text.text-red-600::before {
            background: linear-gradient(90deg, transparent, rgba(220, 38, 38, 0.1), transparent);
        }

        /* Classes de Acessibilidade */
        .contrast-high {
            filter: contrast(1.5);
        }

        .contrast-inverted {
            filter: invert(1) hue-rotate(180deg);
        }

        .font-large {
            font-size: 1.2em !important;
        }

        .font-large .card-title {
            font-size: 24px !important;
        }

        .font-large .footer-text {
            font-size: 13px !important;
        }

        .font-extra-large {
            font-size: 1.4em !important;
        }

        .font-extra-large .card-title {
            font-size: 28px !important;
        }

        .font-extra-large .footer-text {
            font-size: 15px !important;
        }



        /* Acessibilidade */
        .system-card:focus {
            outline: 3px solid #FFA500;
            outline-offset: 2px;
        }

        .search-input:focus {
            outline: none;
        }

        /* Scrollbar customizada */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #005A24 0%, #FFA500 100%);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #1A3C34 0%, #E76F51 100%);
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
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header-gradient shadow-sm">
        <div class="container mx-auto px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center justify-between header-content">
                <div class="flex items-center space-x-4 sm:space-x-6">
                    <div class="w-14 h-14 flex items-center justify-center bg-white rounded-2xl shadow-lg">
                        <img src="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png" 
                             alt="Logo Ceará" 
                             class="w-10 h-10 object-contain">
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-semibold font-heading">
                            <span class="text-primary">Subsistemas</span> <span class="text-secondary">CREDE</span>
                        </h1>
                    </div>
                </div>
                
                <!-- Botões de Navegação (Desktop) -->
                <div class="hidden md:flex items-center space-x-4">
                    <button class="header-btn-with-text" title="Início" onclick="window.location.href='#'">
                        <i class="fas fa-home"></i>
                        <span>Início</span>
                    </button>
                    <button class="header-btn-with-text" title="Acessibilidade" onclick="toggleAccessibility()">
                        <i class="fas fa-universal-access"></i>
                        <span>Acessibilidade</span>
                    </button>
                    <div class="w-px h-8 bg-gray-300 mx-3"></div>
                    <button class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-red-50 text-red-600 hover:text-red-700 transition-all duration-300 flex items-center justify-center" title="Sair" onclick="window.location.href='../models/sessions.php?sair'">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </div>
                

            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-12">
        
        

        <!-- Search Bar -->
        <div class="mb-12 animate-on-scroll">
            <div class="search-container">
                <input type="text" 
                       id="searchSubsystems" 
                       placeholder="Buscar aplicativos e sistemas..." 
                       class="search-input"
                       autocomplete="off">
                <i class="fas fa-search search-icon"></i>
            </div>
        </div>

        <!-- Application Cards Grid -->
        <div class="card-grid">
            
     

            <!-- Card 2: Controle de Estoque -->
             <a href="../../subsystems/controle_de_estoque/default.php">
            <div class="system-card animate-on-scroll" 
                 tabindex="0"
                 role="button"
                 aria-label="Acessar Controle de Estoque - Sistema de gestão de materiais e recursos"
                 data-system="estoque">
                <div class="card-icon icon-primary">
                    <i class="fas fa-boxes"></i>
                </div>
                <h3 class="card-title">Controle de Estoque</h3>
                <p class="text-gray-600 mb-4 text-sm">Gestão completa de materiais e recursos</p>
                <span class="card-badge">Sistema Ativo</span>
            </div>
            </a>

     

       

        </div>

       

    </main>

    <!-- Painel de Acessibilidade -->
    <div id="accessibilityPanel" class="accessibility-panel">
        <div class="accessibility-content">
            <div class="accessibility-header">
                <h3>Acessibilidade</h3>
                <button onclick="toggleAccessibility()" class="close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="accessibility-options">
                <div class="option-group">
                    <h4>Contraste</h4>
                    <div class="option-buttons">
                        <button onclick="setContrast('normal')" class="option-btn active">Normal</button>
                        <button onclick="setContrast('high')" class="option-btn">Alto</button>
                        <button onclick="setContrast('inverted')" class="option-btn">Invertido</button>
                    </div>
                </div>
                <div class="option-group">
                    <h4>Tamanho do Texto</h4>
                    <div class="option-buttons">
                        <button onclick="setFontSize('normal')" class="option-btn active">Normal</button>
                        <button onclick="setFontSize('large')" class="option-btn">Grande</button>
                        <button onclick="setFontSize('extra-large')" class="option-btn">Extra Grande</button>
                    </div>
                </div>

                <div class="option-group">
                    <h4>Navegação por Voz</h4>
                    <div class="option-buttons">
                        <button onclick="toggleVoiceNavigation()" class="option-btn">Ativar</button>
                        <button onclick="toggleTextToSpeech()" class="option-btn">Leitura</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Painel de Perfil -->
    <div id="profilePanel" class="profile-panel">
        <div class="profile-content">
            <div class="profile-header">
                <h3>Perfil do Usuário</h3>
                <button onclick="toggleProfile()" class="close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="profile-info">
                <div class="profile-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="profile-details">
                    <h4><?=$_SESSION['nome']?></h4>
                    <p><?=$_SESSION['email']?></p>
                    <span class="profile-role"><?=$_SESSION['setor']?></span>
                </div>
            </div>
            <div class="profile-actions">
                <button class="profile-action-btn">
                    <i class="fas fa-user-edit"></i>
                    Editar Perfil
                </button>
                <button class="profile-action-btn">
                    <i class="fas fa-cog"></i>
                    Configurações
                </button>
                <button class="profile-action-btn">
                    <i class="fas fa-question-circle"></i>
                    Ajuda
                </button>
            </div>
        </div>
    </div>

    <!-- Footer com Botões (Mobile) -->
    <footer class="mobile-footer">
        <div class="footer-buttons">
            <button class="footer-btn active" title="Início">
                <i class="fas fa-home"></i>
                <span class="footer-text">Início</span>
            </button>
            <button class="footer-btn" title="Sair" onclick="window.location.href='../models/sessions.php?sair'">
                <i class="fas fa-sign-out-alt"></i>
                <span class="footer-text">Sair</span>
            </button>
            <button class="footer-btn" title="Acessibilidade" onclick="toggleAccessibility()">
                <i class="fas fa-universal-access"></i>
                <span class="footer-text">Acessibilidade</span>
            </button>
            <button class="footer-btn" title="Perfil do usuário" onclick="toggleProfile()">
                <i class="fas fa-user"></i>
                <span class="footer-text">Perfil</span>
            </button>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elementos
            const searchInput = document.getElementById('searchSubsystems');
            const cards = document.querySelectorAll('.system-card');
            const animatedElements = document.querySelectorAll('.animate-on-scroll');

            // Função de busca melhorada
            function performSearch() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                let visibleCount = 0;

                cards.forEach((card, index) => {
                    const title = card.querySelector('.card-title').textContent.toLowerCase();
                    const description = card.querySelector('p').textContent.toLowerCase();
                    const isVisible = title.includes(searchTerm) || description.includes(searchTerm);
                    
                    if (isVisible) {
                        card.style.display = 'block';
                        card.style.animationDelay = `${visibleCount * 0.1}s`;
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Feedback visual se nenhum resultado
                const noResults = document.getElementById('noResults');
                if (visibleCount === 0 && searchTerm !== '') {
                    if (!noResults) {
                        const noResultsDiv = document.createElement('div');
                        noResultsDiv.id = 'noResults';
                        noResultsDiv.className = 'text-center py-12 animate-fade-in';
                        noResultsDiv.innerHTML = `
                            <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">Nenhum resultado encontrado</h3>
                            <p class="text-gray-500">Tente buscar por outros termos</p>
                        `;
                        document.querySelector('.card-grid').appendChild(noResultsDiv);
                    }
                } else if (noResults) {
                    noResults.remove();
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

            // Navegação por teclado nos cards
            cards.forEach(card => {
                card.setAttribute('tabindex', '0');
                card.setAttribute('role', 'button');
                
                card.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });

            // Navegação por teclado no footer
            const footerButtons = document.querySelectorAll('.footer-btn');
            footerButtons.forEach(btn => {
                btn.setAttribute('tabindex', '0');
            });

            // Função de navegação melhorada
            window.navigateToSystem = function(url) {
                const card = event.currentTarget;
                card.classList.add('loading');
                
                // Feedback visual
                const icon = card.querySelector('.card-icon');
                icon.style.transform = 'scale(0.9)';
                
                setTimeout(() => {
                    window.location.href = url;
                }, 300);
            };

            // Funções de Acessibilidade e Perfil
            window.toggleAccessibility = function() {
                const panel = document.getElementById('accessibilityPanel');
                const isVisible = panel.style.display === 'block';
                panel.style.display = isVisible ? 'none' : 'block';
                
                // Fechar painel de perfil se estiver aberto
                if (!isVisible) {
                    document.getElementById('profilePanel').style.display = 'none';
                }
            };

            window.toggleProfile = function() {
                const panel = document.getElementById('profilePanel');
                const isVisible = panel.style.display === 'block';
                panel.style.display = isVisible ? 'none' : 'block';
                
                // Fechar painel de acessibilidade se estiver aberto
                if (!isVisible) {
                    document.getElementById('accessibilityPanel').style.display = 'none';
                }
            };

            // Funções de Acessibilidade
            window.setContrast = function(contrast) {
                const body = document.body;
                const buttons = document.querySelectorAll('.option-group:nth-child(1) .option-btn');
                
                // Remove classes ativas do grupo de contraste
                buttons.forEach(btn => btn.classList.remove('active'));
                event.target.classList.add('active');
                
                // Remove classes de contraste anteriores
                body.classList.remove('contrast-high', 'contrast-inverted');
                
                switch(contrast) {
                    case 'high':
                        body.classList.add('contrast-high');
                        break;
                    case 'inverted':
                        body.classList.add('contrast-inverted');
                        break;
                }
            };

            window.setFontSize = function(size) {
                const body = document.body;
                const buttons = document.querySelectorAll('.option-group:nth-child(2) .option-btn');
                
                // Remove classes ativas do grupo de tamanho
                buttons.forEach(btn => btn.classList.remove('active'));
                event.target.classList.add('active');
                
                // Remove classes de tamanho anteriores
                body.classList.remove('font-large', 'font-extra-large');
                
                switch(size) {
                    case 'large':
                        body.classList.add('font-large');
                        break;
                    case 'extra-large':
                        body.classList.add('font-extra-large');
                        break;
                }
            };



            // Navegação por voz
            window.toggleVoiceNavigation = function() {
                const btn = event.target;
                if (btn.textContent === 'Ativar') {
                    btn.textContent = 'Desativar';
                    btn.style.background = '#dc2626';
                    btn.style.color = '#ffffff';
                    
                    try {
                        // Verificar se o navegador suporta reconhecimento de voz
                        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                        
                        if (SpeechRecognition) {
                            window.voiceRecognition = new SpeechRecognition();
                            window.voiceRecognition.continuous = true;
                            window.voiceRecognition.interimResults = false;
                            window.voiceRecognition.lang = 'pt-BR';
                            
                            window.voiceRecognition.onstart = function() {
                                console.log('Reconhecimento de voz iniciado');
                                const announcer = document.getElementById('screen-reader-announcer');
                                if (announcer) {
                                    announcer.textContent = 'Navegação por voz ativada. Diga "início", "sair", "estoque", "perfil" ou "acessibilidade".';
                                }
                            };
                            
                            window.voiceRecognition.onresult = function(event) {
                                const command = event.results[event.results.length - 1][0].transcript.toLowerCase();
                                console.log('Comando reconhecido:', command);
                                
                                // Comandos de voz
                                if (command.includes('início') || command.includes('home')) {
                                    document.querySelector('.footer-btn[title="Início"]').click();
                                } else if (command.includes('sair') || command.includes('logout')) {
                                    document.querySelector('.footer-btn[title="Sair"]').click();
                                } else if (command.includes('estoque') || command.includes('controle')) {
                                    document.querySelector('[data-system="estoque"]').click();
                                } else if (command.includes('perfil')) {
                                    document.querySelector('.footer-btn[title="Perfil do usuário"]').click();
                                } else if (command.includes('acessibilidade')) {
                                    document.querySelector('.footer-btn[title="Acessibilidade"]').click();
                                }
                                
                                // Anunciar comando reconhecido
                                const announcer = document.getElementById('screen-reader-announcer');
                                if (announcer) {
                                    announcer.textContent = `Comando reconhecido: ${command}`;
                                }
                            };
                            
                            window.voiceRecognition.onerror = function(event) {
                                console.error('Erro no reconhecimento de voz:', event.error);
                                alert(`Erro no reconhecimento de voz: ${event.error}. Verifique se o microfone está conectado e permitido.`);
                                btn.textContent = 'Ativar';
                                btn.style.background = '#005A24';
                                btn.style.color = '#ffffff';
                            };
                            
                            window.voiceRecognition.onend = function() {
                                console.log('Reconhecimento de voz finalizado');
                            };
                            
                            window.voiceRecognition.start();
                            
                        } else {
                            throw new Error('API de reconhecimento de voz não suportada');
                        }
                    } catch (error) {
                        console.error('Erro ao inicializar reconhecimento de voz:', error);
                        alert('Seu navegador não suporta reconhecimento de voz ou o microfone não está disponível. Tente usar Chrome, Edge ou Safari.');
                        btn.textContent = 'Ativar';
                        btn.style.background = '#005A24';
                        btn.style.color = '#ffffff';
                    }
                } else {
                    btn.textContent = 'Ativar';
                    btn.style.background = '#005A24';
                    btn.style.color = '#ffffff';
                    
                    // Parar reconhecimento de voz
                    if (window.voiceRecognition) {
                        try {
                            window.voiceRecognition.stop();
                        } catch (error) {
                            console.error('Erro ao parar reconhecimento de voz:', error);
                        }
                    }
                    
                    // Anunciar desativação
                    const announcer = document.getElementById('screen-reader-announcer');
                    if (announcer) {
                        announcer.textContent = 'Navegação por voz desativada.';
                    }
                }
            };

            // Text-to-Speech (Leitura de texto)
            window.toggleTextToSpeech = function() {
                const btn = event.target;
                if (btn.textContent === 'Leitura') {
                    btn.textContent = 'Parar';
                    btn.style.background = '#dc2626';
                    btn.style.color = '#ffffff';
                    
                    // Verificar se o navegador suporta síntese de voz
                    if ('speechSynthesis' in window) {
                        // Ler informações da página
                        const title = document.querySelector('h1').textContent;
                        const cards = document.querySelectorAll('.system-card');
                        let textToRead = `Página de subsistemas CREDE. ${title}. `;
                        
                        cards.forEach((card, index) => {
                            const cardTitle = card.querySelector('.card-title').textContent;
                            const cardDesc = card.querySelector('p') ? card.querySelector('p').textContent : '';
                            textToRead += `Card ${index + 1}: ${cardTitle}. ${cardDesc}. `;
                        });
                        
                        textToRead += 'Use Tab para navegar pelos elementos. Pressione Enter para ativar.';
                        
                        // Criar utterance
                        const utterance = new SpeechSynthesisUtterance(textToRead);
                        utterance.lang = 'pt-BR';
                        utterance.rate = 0.9;
                        utterance.pitch = 1;
                        
                        utterance.onend = function() {
                            btn.textContent = 'Leitura';
                            btn.style.background = '#005A24';
                            btn.style.color = '#ffffff';
                        };
                        
                        utterance.onerror = function(event) {
                            console.error('Erro na síntese de voz:', event.error);
                            btn.textContent = 'Leitura';
                            btn.style.background = '#005A24';
                            btn.style.color = '#ffffff';
                        };
                        
                        // Parar qualquer síntese anterior
                        window.speechSynthesis.cancel();
                        
                        // Iniciar nova síntese
                        window.speechSynthesis.speak(utterance);
                        
                    } else {
                        alert('Seu navegador não suporta síntese de voz.');
                        btn.textContent = 'Leitura';
                        btn.style.background = '#005A24';
                        btn.style.color = '#ffffff';
                    }
                } else {
                    btn.textContent = 'Leitura';
                    btn.style.background = '#005A24';
                    btn.style.color = '#ffffff';
                    
                    // Parar síntese de voz
                    if ('speechSynthesis' in window) {
                        window.speechSynthesis.cancel();
                    }
                }
            };

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

            // Efeitos de hover melhorados
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Feedback de clique
            cards.forEach(card => {
                card.addEventListener('click', function() {
                    // Ripple effect
                    const ripple = document.createElement('div');
                    ripple.style.cssText = `
                        position: absolute;
                        border-radius: 50%;
                        background: rgba(0, 90, 36, 0.3);
                        transform: scale(0);
                        animation: ripple 0.6s linear;
                        pointer-events: none;
                    `;
                    
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = (event.clientX - rect.left - size / 2) + 'px';
                    ripple.style.top = (event.clientY - rect.top - size / 2) + 'px';
                    
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });

            // Adicionar animação de ripple ao CSS
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);

            console.log('Sistema CREDE carregado com sucesso!');
        });
    </script>
</body>

</html>
