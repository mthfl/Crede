<?php
require_once __DIR__ . '/controllers/controller_admin_dashboard.php';
?>
<!DOCTYPE html>
<html lang="pt-BR" style="height: auto; overflow-y: auto;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Admin - Progresso de Candidatos</title>
    <link rel="icon" type="image/png" href="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
                        white: '#FFFFFF',
                        success: '#28A745',
                        warning: '#FFC107',
                        danger: '#DC3545',
                        info: '#17A2B8'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Poppins', 'sans-serif'],
                        display: ['Inter', 'system-ui', 'sans-serif'],
                        body: ['Inter', 'system-ui', 'sans-serif']
                    },
                    boxShadow: {
                        card: '0 10px 15px -3px rgba(0, 90, 36, 0.1), 0 4px 6px -2px rgba(0, 90, 36, 0.05)',
                        'card-hover': '0 20px 25px -5px rgba(0, 90, 36, 0.2), 0 10px 10px -5px rgba(0, 90, 36, 0.1)',
                        'glow': '0 0 20px rgba(255, 165, 0, 0.3)'
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'pulse-slow': 'pulse 3s infinite',
                        'bounce-slow': 'bounce 2s infinite',
                        'slide-in-left': 'slideInLeft 0.5s ease-out',
                        'slide-in-right': 'slideInRight 0.5s ease-out',
                        'fade-in-up': 'fadeInUp 0.6s ease-out',
                        'scale-in': 'scaleIn 0.4s ease-out',
                        'pulse-soft': 'pulseSoft 2s ease-in-out infinite'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' }
                        },
                        slideInLeft: {
                            from: { opacity: '0', transform: 'translateX(-30px)' },
                            to: { opacity: '1', transform: 'translateX(0)' }
                        },
                        slideInRight: {
                            from: { opacity: '0', transform: 'translateX(30px)' },
                            to: { opacity: '1', transform: 'translateX(0)' }
                        },
                        fadeInUp: {
                            from: { opacity: '0', transform: 'translateY(20px)' },
                            to: { opacity: '1', transform: 'translateY(0)' }
                        },
                        scaleIn: {
                            from: { opacity: '0', transform: 'scale(0.95)' },
                            to: { opacity: '1', transform: 'scale(1)' }
                        },
                        pulseSoft: {
                            '0%, 100%': { opacity: '1' },
                            '50%': { opacity: '0.8' }
                        }
                    },
                    spacing: {
                        '18': '4.5rem',
                        '88': '22rem'
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        :root {
            --primary: #005A24;
            --secondary: #FFA500;
            --accent: #E6F4EA;
            --dark: #1A3C34;
            --light: #F8FAF9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
            background: linear-gradient(135deg, #F8FAF9 0%, #E6F4EA 100%);
            height: auto;
            min-height: 100%;
            overflow-y: auto;
            position: relative;
            line-height: 1.6;
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

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
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

        .gradient-bg {
            background: linear-gradient(135deg, #005A24 0%, #1A3C34 100%);
        }

        .page-title {
            position: relative;
            width: 100%;
            text-align: center;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #FFA500, #FF8C00);
            border-radius: 3px;
        }

        /* Select2 Styles */
        .select2-container {
            width: 100% !important;
            font-family: 'Inter', sans-serif;
        }

        .select2-selection__clear {
            margin-left: 0.5rem !important;
            position: relative;
            left: 0.2rem;
        }

        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            height: 3rem;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #374151;
            font-size: 0.875rem;
            line-height: 1.25rem;
            padding-left: 0.75rem;
            padding-right: 2rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af;
            font-weight: 500;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            width: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #005A24;
            box-shadow: 0 0 0 3px rgba(0, 90, 36, 0.1);
        }

        .select2-dropdown {
            border: 2px solid #005A24;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .select2-results__option {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #005A24;
            color: white;
        }

        .select2-search--dropdown .select2-search__field {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.5rem;
            font-size: 0.875rem;
        }

        .select2-search--dropdown .select2-search__field:focus {
            outline: none;
            border-color: #005A24;
            box-shadow: 0 0 0 3px rgba(0, 90, 36, 0.1);
        }

        .stats-card {
            transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #FFFFFF 0%, #F8FAF9 100%);
            border: 2px solid transparent;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 165, 0, 0.1) 0%, rgba(0, 90, 36, 0.05) 100%);
            opacity: 0;
            transition: opacity 0.2s ease;
            z-index: 1;
        }

        .stats-card:hover::before {
            opacity: 1;
        }

        .stats-card:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 10px 15px -3px rgba(0, 90, 36, 0.15), 0 5px 5px -2px rgba(0, 90, 36, 0.08);
            border-color: #FFA500;
        }

        .info-card {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #FFFFFF 0%, #F8FAF9 100%);
            border: 2px solid #E6F4EA;
            border-radius: 1rem;
        }

        .info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 165, 0, 0.1) 0%, rgba(0, 90, 36, 0.05) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }

        .info-card:hover::before {
            opacity: 1;
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 90, 36, 0.2), 0 10px 10px -5px rgba(0, 90, 36, 0.1);
            border-color: #FFA500;
        }

        .card-shine {
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0) 100%);
            transform: skewX(-25deg);
            transition: all 0.75s ease;
            z-index: 2;
        }

        .info-card:hover .card-shine {
            left: 150%;
        }

        .info-card:hover .card-icon {
            transform: scale(1.1);
            color: #FFA500;
        }

        .info-card p {
            z-index: 2;
            position: relative;
            transition: color 0.3s ease;
        }

        .info-card:hover p {
            color: #005A24;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #E6F4EA;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #FFA500, #FF8C00);
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        .school-selector {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            border: 2px solid #E6F4EA;
        }

        .school-selector h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #005A24;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .school-selector h3 i {
            margin-right: 0.5rem;
            color: #FFA500;
        }

        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            backdrop-filter: blur(10px);
            background: linear-gradient(135deg, var(--primary) 0%, var(--dark) 100%);
            z-index: 50;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .overlay {
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            backdrop-filter: blur(2px);
        }

        .overlay.show {
            opacity: 1;
            visibility: visible;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes pulseSoft {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            overflow: hidden;
        }

        .card-hover::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }

        .card-hover:hover::before {
            left: 100%;
        }

        .card-hover:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 90, 36, 0.25), 0 0 0 1px rgba(0, 90, 36, 0.05);
        }

        .btn-animate {
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .btn-animate::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.3s, height 0.3s;
        }

        .btn-animate:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-animate:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .btn-animate:active {
            transform: translateY(0);
        }

        .nav-item {
            position: relative;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border-radius: 12px;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--secondary);
            border-radius: 0 4px 4px 0;
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .nav-item:hover::before {
            transform: scaleY(1);
        }

        .nav-item:hover {
            transform: translateX(8px);
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar {
            z-index: 50;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 100vw;
            max-width: 20rem;
        }

        .overlay {
            z-index: 45;
        }

        @media (min-width: 1024px) {
            .sidebar {
                width: 20rem;
                position: static;
                flex-shrink: 0;
                transform: translateX(0) !important;
            }

            .main-content {
                flex: 1;
                min-width: 0;
                margin-left: 0;
                overflow-x: hidden;
            }

            body {
                overflow-x: hidden;
            }
        }

        .grid-item {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }

        .grid-item:nth-child(1) { animation-delay: 0.1s; }
        .grid-item:nth-child(2) { animation-delay: 0.2s; }
        .grid-item:nth-child(3) { animation-delay: 0.3s; }
        .grid-item:nth-child(4) { animation-delay: 0.4s; }
        .grid-item:nth-child(5) { animation-delay: 0.5s; }
        .grid-item:nth-child(6) { animation-delay: 0.6s; }

        .chart-container {
            position: relative;
            height: 300px;
            margin: 1rem 0;
        }

        .pizza-chart-wrapper {
            position: relative;
            height: 250px;
            margin: 0.5rem 0;
        }

        .sidebar-link {
            transition: all 0.3s ease;
        }

        .sidebar-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(0.5rem);
        }

        .sidebar-link.active {
            background-color: rgba(255, 165, 0, 0.2);
            color: #FFA500;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: var(--dark);
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100vw;
                max-width: 320px;
            }

            .card-hover:hover {
                transform: none;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }

            .nav-item:hover {
                transform: none;
            }

            .main-content {
                height: auto;
                min-height: 100vh;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch;
            }

            html, body {
                height: auto;
                overflow-y: auto !important;
                position: relative;
                -webkit-overflow-scrolling: touch;
            }
        }

        .focus-ring:focus {
            outline: 2px solid var(--secondary);
            outline-offset: 2px;
        }

        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-item {
            background: linear-gradient(135deg, #FFFFFF 0%, #F8FAF9 100%);
            border: 2px solid #E6F4EA;
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 90, 36, 0.15);
            border-color: #FFA500;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #005A24;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.875rem;
            color: #646464;
            font-weight: 500;
        }

        .stat-icon {
            font-size: 1.5rem;
            color: #FFA500;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col font-sans">
    <div class="flex flex-col min-h-screen bg-white">
        <!-- Header -->
        <header class="header-gradient shadow-sm">
            <div class="container mx-auto px-6 py-4">
                <div class="flex items-center justify-between header-content">
                    <div class="flex items-center space-x-4 sm:space-x-6">
                        <div class="w-14 h-14 flex items-center justify-center bg-white rounded-2xl">
                            <img src="./assets/Brasão_do_Ceará.svg.png"
                                alt="Logo Ceará"
                                class="w-10 h-10 object-contain">
                        </div>
                        <div>
                            <h1 class="text-xl sm:text-2xl font-semibold font-heading">
                                <span class="text-primary">Admin</span> <span class="text-secondary">CREDE</span>
                            </h1>
                        </div>
                    </div>


                    <!-- Botões de Navegação (Desktop) -->
                    <div class="hidden md:flex items-center space-x-4">
                        <button class="w-10 h-10 rounded-xl bg-accent hover:bg-primary text-primary hover:text-white transition-all duration-300 flex items-center justify-center" title="Voltar" onclick="window.location.href='../../main/views/subsystems.php'">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal de Candidatos -->
                <div id="candidatosModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                    <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl max-h-[80vh] overflow-hidden flex flex-col">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-primary flex items-center gap-2">
                                <i class="fas fa-users"></i>
                                Candidatos da escola selecionada
                            </h2>
                            <button type="button" onclick="closeCandidatosModal()" class="text-gray-500 hover:text-primary text-2xl leading-none">&times;</button>
                        </div>
                        <div class="p-6 overflow-auto">
                            <?php if (!empty($candidatos)) { ?>
                                <table class="min-w-full text-sm text-left">
                                    <thead class="border-b border-accent">
                                        <tr class="text-gray-600">
                                            <th class="py-2 pr-4">Nome</th>
                                            <th class="py-2 px-4">Curso</th>
                                            <th class="py-2 px-4">Responsável</th>
                                            <th class="py-2 px-4">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($candidatos as $cand) { ?>
                                            <tr class="border-b border-gray-100 hover:bg-accent/40">
                                                <td class="py-2 pr-4 font-semibold text-primary">
                                                    <?= htmlspecialchars($cand['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                                </td>
                                                <td class="py-2 px-4 text-gray-700">
                                                    <?= htmlspecialchars($cand['nome_curso'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                                </td>
                                                <td class="py-2 px-4 text-gray-700">
                                                    <?= htmlspecialchars($cand['nome_user'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                                </td>
                                                <td class="py-2 px-4 text-gray-700">
                                                    <?php
                                                    $status = $cand['status'] ?? null;
                                                    $label  = 'Indefinido';
                                                    if ($status === 1 || $status === '1') {
                                                        $label = 'Ativo';
                                                    } elseif ($status === 0 || $status === '0') {
                                                        $label = 'Inativo';
                                                    }
                                                    echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php } else { ?>
                                <p class="text-gray-600 text-center">Nenhum candidato encontrado para esta escola.</p>
                            <?php } ?>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                            <button type="button" onclick="closeCandidatosModal()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-all">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Main Content -->
        <div class="main-content flex-1 bg-white">

            <main class="p-4 sm:p-6 lg:p-8">
                <!-- Título da Página -->
                <div class="text-center mb-8">
                    <h1 class="text-primary text-3xl md:text-4xl font-bold mb-4 page-title tracking-tight font-heading">
                        <i class="fas fa-chart-line mr-3 text-secondary"></i>
                        Progresso de Candidatos
                    </h1>
                    <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                        Monitore o progresso de inscrições por escola
                    </p>
                </div>

                <!-- Seletor de Escolas -->
                <div class="school-selector">
                    <h3>
                        <i class="fas fa-building"></i>
                        Selecione uma Escola
                    </h3>
                    <select id="escolaSelect" class="select2-escola w-full">
                        <option value="" disabled <?php echo empty($escola) ? 'selected' : ''; ?>>Selecione uma escola para visualizar o progresso</option>
                        <?php if (!empty($schoolsConfig)) { ?>
                            <?php foreach ($schoolsConfig as $escolaRow) { 
                                $codigoEscola = $escolaRow['escola_banco'] ?? '';
                                $nomeEscola   = $escolaRow['nome_escola'] ?? $codigoEscola;
                                if (!$codigoEscola) continue;
                            ?>
                                <option value="<?= htmlspecialchars($codigoEscola, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($escola === $codigoEscola) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($nomeEscola, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>

                <!-- Estatísticas Rápidas (Dinâmicas) -->
                <div id="statsContainer" style="display: none;">
                    <div class="quick-stats mb-8">
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="stat-number"><?php echo (int)($quickStats['totalAlunos'] ?? 0); ?></div>
                            <div class="stat-label">Total de Candidatos</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-school"></i>
                            </div>
                            <div class="stat-number"><?php echo (int)($quickStats['totalPublicos'] ?? 0); ?></div>
                            <div class="stat-label">Candidatos Escola Pública</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="stat-number"><?php echo (int)($quickStats['totalPrivados'] ?? 0); ?></div>
                            <div class="stat-label">Candidatos Escola Privada</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-wheelchair"></i>
                            </div>
                            <div class="stat-number"><?php echo (int)($quickStats['totalPCDs'] ?? 0); ?></div>
                            <div class="stat-label">Candidatos PCD</div>
                        </div>
                    </div>

                    <!-- Gráficos de Pizza - Cotas por Curso -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b-2 border-accent">
                            <h3 class="text-xl font-bold text-primary font-heading flex items-center">
                                <i class="fas fa-chart-pie text-secondary mr-3"></i>
                                Distribuição de Cotas por Curso
                            </h3>
                        </div>
                        <div id="cursosPizzaContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            <!-- Os gráficos serão criados dinamicamente via JavaScript -->
                        </div>
                        <!-- Legenda única centralizada -->
                        <div id="legendaUnica" class="mt-6 flex flex-wrap justify-center items-center gap-6 bg-white rounded-xl shadow-card p-4 border-2 border-accent">
                            <!-- A legenda será criada dinamicamente via JavaScript -->
                        </div>
                    </div>

                    <!-- Lista de Usuários Cadastrados na Escola -->
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-primary font-heading mb-4 flex items-center">
                            <i class="fas fa-users text-secondary mr-2"></i>
                            Usuários cadastrados na escola
                        </h3>
                        <div class="info-card p-6 grid-item">
                            <div class="card-shine"></div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm text-left">
                                    <thead class="border-b border-accent">
                                        <tr class="text-gray-600">
                                            <th class="py-2 pr-4">Nome</th>
                                            <th class="py-2 px-4">Email</th>
                                            <th class="py-2 px-4">CPF</th>
                                            <th class="py-2 px-4">Tipo de usuário</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($usuariosEscola)) { ?>
                                            <?php foreach ($usuariosEscola as $usuario) { ?>
                                                <tr class="border-b border-gray-100 hover:bg-accent/40">
                                                    <td class="py-2 pr-4 font-semibold text-primary">
                                                        <?= htmlspecialchars($usuario['nome_user'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                                    </td>
                                                    <td class="py-2 px-4 text-gray-700">
                                                        <?= htmlspecialchars($usuario['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                                    </td>
                                                    <td class="py-2 px-4 text-gray-700">
                                                        <?= htmlspecialchars($usuario['cpf'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                                    </td>
                                                    <td class="py-2 px-4 text-gray-700">
                                                        <?= htmlspecialchars($usuario['tipo_usuario'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <tr>
                                                <td colspan="4" class="py-4 text-center text-gray-500">
                                                    Nenhum usuário cadastrado encontrado.
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mensagem quando nenhuma escola é selecionada -->
                <div id="noSchoolSelected" class="bg-gradient-to-r from-accent to-light border-2 border-primary rounded-lg p-8 text-center">
                    <i class="fas fa-school text-6xl text-primary mb-4"></i>
                    <h2 class="text-2xl font-bold text-primary mb-2">Selecione uma Escola</h2>
                    <p class="text-dark">Escolha uma escola no seletor acima para visualizar o progresso de candidatos</p>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Configuração do Select2
        $(document).ready(function() {
            $('.select2-escola').select2({
                placeholder: 'Selecione uma escola...',
                allowClear: true,
                width: '100%'
            });

            // Evento ao selecionar escola
            $('#escolaSelect').on('change', function() {
                const escolaId = $(this).val();
                if (escolaId) {
                    // Recarrega a página com o parâmetro da escola selecionada
                    const url = new URL(window.location.href);
                    url.searchParams.set('escola', escolaId);
                    window.location.href = url.toString();
                } else {
                    ocultarEstatisticas();
                }
            });

            // Se já houver uma escola selecionada no backend, exibe as estatísticas automaticamente
            const hasEscolaSelecionada = <?php echo $escola ? 'true' : 'false'; ?>;
            if (hasEscolaSelecionada) {
                mostrarEstatisticas();
            }
        });

        // Função para mostrar estatísticas
        function mostrarEstatisticas() {
            document.getElementById('statsContainer').style.display = 'block';
            document.getElementById('noSchoolSelected').style.display = 'none';
            
            // Animar charts - aguardar um pouco mais para garantir que o DOM está pronto
            setTimeout(() => {
                inicializarCharts();
            }, 300);
        }

        // Modal de candidatos
        function openCandidatosModal() {
            const modal = document.getElementById('candidatosModal');
            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        function closeCandidatosModal() {
            const modal = document.getElementById('candidatosModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        // Função para ocultar estatísticas
        function ocultarEstatisticas() {
            document.getElementById('statsContainer').style.display = 'none';
            document.getElementById('noSchoolSelected').style.display = 'block';
        }

        // Cores do sistema
        const systemColors = {
            primary: '#005A24',
            secondary: '#FFA500',
            dark: '#1A3C34',
            accent: '#E6F4EA',
            success: '#28A745',
            warning: '#FFC107',
            danger: '#DC3545'
        };

        // Inicializar charts
        function inicializarCharts() {
            // Gráfico de Pizza - Status
            const ctxPie = document.getElementById('statusChart');
            if (ctxPie) {
                new Chart(ctxPie, {
                    type: 'doughnut',
                    data: {
                        labels: ['Classificados', 'Lista de Espera', 'Inativado'],
                        datasets: [{
                            data: [45, 35, 20],
                            backgroundColor: [
                                systemColors.success,
                                systemColors.warning,
                                systemColors.danger
                            ],
                            borderColor: 'white',
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    font: { family: 'Inter', size: 13, weight: '600' },
                                    color: '#1A3C34',
                                    padding: 15
                                }
                            }
                        }
                    }
                });
            }

            // Gráficos de Pizza - Cotas por Curso
            const cotasPorCurso = <?php echo json_encode($cotasPorCurso ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
            const container = document.getElementById('cursosPizzaContainer');
            
            console.log('Dados de cotas por curso recebidos:', cotasPorCurso);
            console.log('Container encontrado:', container);
            
            // Limpar container antes de adicionar novos gráficos
            if (container) {
                container.innerHTML = '';
            }
            
            // Limpar legenda única
            const legendaUnica = document.getElementById('legendaUnica');
            if (legendaUnica) {
                legendaUnica.innerHTML = '';
            }
            
            if (container && cotasPorCurso && cotasPorCurso.length > 0) {
                console.log('Criando gráficos de pizza para', cotasPorCurso.length, 'cursos');
                // Cores do sistema para cada tipo de cota
                const coresCotas = {
                    'ampla_privada': systemColors.secondary,      // Laranja
                    'cota_privada': systemColors.warning,          // Amarelo
                    'pcd': '#FF8C42',                              // Laranja esverdeado (intermediário entre laranja e verde)
                    'cota_publica': systemColors.success,           // Verde
                    'ampla_publica': systemColors.primary          // Verde escuro
                };
                
                // Criar legenda única centralizada
                if (legendaUnica) {
                    const legendItems = [
                        { label: 'Ampla Privada', color: coresCotas.ampla_privada },
                        { label: 'Cota Privada', color: coresCotas.cota_privada },
                        { label: 'PCD', color: coresCotas.pcd },
                        { label: 'Cota Pública', color: coresCotas.cota_publica },
                        { label: 'Ampla Pública', color: coresCotas.ampla_publica }
                    ];
                    
                    legendItems.forEach(item => {
                        const legendItem = document.createElement('div');
                        legendItem.className = 'flex items-center gap-2';
                        
                        const colorBox = document.createElement('div');
                        colorBox.className = 'w-4 h-4 rounded-full';
                        colorBox.style.backgroundColor = item.color;
                        colorBox.style.border = '2px solid #FFFFFF';
                        colorBox.style.boxShadow = '0 1px 3px rgba(0,0,0,0.2)';
                        
                        const labelText = document.createElement('span');
                        labelText.className = 'text-sm font-semibold text-primary';
                        labelText.textContent = item.label;
                        
                        legendItem.appendChild(colorBox);
                        legendItem.appendChild(labelText);
                        legendaUnica.appendChild(legendItem);
                    });
                }

                cotasPorCurso.forEach((curso, index) => {
                    // Criar container para cada gráfico
                    const chartWrapper = document.createElement('div');
                    chartWrapper.className = 'bg-white rounded-xl shadow-card p-4 border-2 border-accent hover:border-secondary transition-all duration-300';
                    
                    const chartTitle = document.createElement('h4');
                    chartTitle.className = 'text-base font-bold text-primary mb-3 text-center font-heading';
                    chartTitle.textContent = curso.nome_curso || 'Curso sem nome';
                    chartWrapper.appendChild(chartTitle);
                    
                    const chartDiv = document.createElement('div');
                    chartDiv.className = 'pizza-chart-wrapper';
                    
                    const chartCanvas = document.createElement('canvas');
                    chartCanvas.id = `cursoPizzaChart_${index}`;
                    chartDiv.appendChild(chartCanvas);
                    chartWrapper.appendChild(chartDiv);
                    
                    container.appendChild(chartWrapper);
                    
                    // Preparar dados para o gráfico
                    const labels = [];
                    const data = [];
                    const backgroundColor = [];
                    
                    // Ampla Privada
                    const amplaPrivada = parseInt(curso.ampla_privada ?? 0, 10);
                    if (amplaPrivada > 0) {
                        labels.push('Ampla Privada');
                        data.push(amplaPrivada);
                        backgroundColor.push(coresCotas.ampla_privada);
                    }
                    
                    // Cota Privada
                    const cotaPrivada = parseInt(curso.cota_privada ?? 0, 10);
                    if (cotaPrivada > 0) {
                        labels.push('Cota Privada');
                        data.push(cotaPrivada);
                        backgroundColor.push(coresCotas.cota_privada);
                    }
                    
                    // PCD (combinando privada e pública)
                    const pcdPrivada = parseInt(curso.pcd_privada ?? 0, 10);
                    const pcdPublica = parseInt(curso.pcd_publica ?? 0, 10);
                    const totalPCD = pcdPrivada + pcdPublica;
                    if (totalPCD > 0) {
                        labels.push('PCD');
                        data.push(totalPCD);
                        backgroundColor.push(coresCotas.pcd);
                    }
                    
                    // Cota Pública
                    const cotaPublica = parseInt(curso.cota_publica ?? 0, 10);
                    if (cotaPublica > 0) {
                        labels.push('Cota Pública');
                        data.push(cotaPublica);
                        backgroundColor.push(coresCotas.cota_publica);
                    }
                    
                    // Ampla Pública
                    const amplaPublica = parseInt(curso.ampla_publica ?? 0, 10);
                    if (amplaPublica > 0) {
                        labels.push('Ampla Pública');
                        data.push(amplaPublica);
                        backgroundColor.push(coresCotas.ampla_publica);
                    }
                    
                    // Criar gráfico apenas se houver dados
                    if (data.length > 0 && data.some(val => val > 0)) {
                        // Usar setTimeout para garantir que o DOM está pronto
                        setTimeout(() => {
                            const ctx = document.getElementById(`cursoPizzaChart_${index}`);
                            if (ctx) {
                                try {
                                    new Chart(ctx, {
                                        type: 'pie',
                                        data: {
                                            labels: labels,
                                            datasets: [{
                                                data: data,
                                                backgroundColor: backgroundColor,
                                                borderColor: '#FFFFFF',
                                                borderWidth: 2,
                                                hoverOffset: 4
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: {
                                                legend: {
                                                    display: false
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(context) {
                                                            const label = context.label || '';
                                                            const value = context.parsed || 0;
                                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                                            return `${label}: ${value} (${percentage}%)`;
                                                        }
                                                    },
                                                    font: { family: 'Inter', size: 12 }
                                                }
                                            }
                                        }
                                    });
                                    console.log('Gráfico criado para:', curso.nome_curso);
                                } catch (error) {
                                    console.error('Erro ao criar gráfico para', curso.nome_curso, ':', error);
                                }
                            } else {
                                console.error('Canvas não encontrado para curso:', curso.nome_curso, 'ID:', `cursoPizzaChart_${index}`);
                            }
                        }, index * 150);
                    } else {
                        // Se não houver dados, mostrar mensagem
                        const noDataMsg = document.createElement('p');
                        noDataMsg.className = 'text-gray-500 text-center text-sm py-4';
                        noDataMsg.textContent = 'Nenhum candidato encontrado para este curso.';
                        chartWrapper.appendChild(noDataMsg);
                    }
                });
            } else {
                // Se não houver cursos ou container não encontrado
                if (container) {
                    const noDataMsg = document.createElement('div');
                    noDataMsg.className = 'col-span-full bg-white rounded-xl shadow-card p-6 border-2 border-accent text-center';
                    noDataMsg.innerHTML = '<p class="text-gray-500 text-lg">Nenhum curso com candidatos encontrado.</p>';
                    container.appendChild(noDataMsg);
                } else {
                    console.error('Container cursosPizzaContainer não encontrado');
                }
            }
        }
    </script>
</body>
</html>
