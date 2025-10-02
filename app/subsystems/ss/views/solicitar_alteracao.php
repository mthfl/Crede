<?php
require_once(__DIR__ . '/../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../config/connect.php');
$escola = $_SESSION['escola'];

new connect($escola);
require_once(__DIR__ . '/../models/model.select.php');
$select = new select($escola);

$cursos = $select->select_cursos();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Solicitação de Alteração</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/pt-BR.js"></script>
    <style>
        /* Remover foco preto de todos os elementos */
        *:focus {
            outline: none !important;
        }
        .tab-button:focus, .border-b:focus {
            outline: none !important;
            box-shadow: none !important;
        }
    </style>
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
                    },
                    fontFamily: {
                        'display': ['Inter', 'system-ui', 'sans-serif'],
                        'body': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    animation: {
                        'slide-in-left': 'slideInLeft 0.5s ease-out',
                        'fade-in-up': 'fadeInUp 0.6s ease-out',
                        'scale-in': 'scaleIn 0.4s ease-out',
                        'pulse-soft': 'pulseSoft 2s ease-in-out infinite',
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
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            backdrop-filter: blur(10px);
            background: linear-gradient(135deg, var(--primary) 0%, var(--dark) 100%);
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
            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }
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

        .focus-ring:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="number"]:focus,
        select:focus,
        textarea:focus,
        button:focus,
        .btn-animate:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
            border-color: var(--primary);
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
     
            box-shadow: 0 20px 40px -12px rgba(0, 90, 36, 0.15);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: 9999px;
            font-size: 1rem;
            font-weight: 500;
        }

        .status-pendente {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-concluido {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-recusado {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .grid-item {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }

        .grid-item:nth-child(1) {
            animation-delay: 0.1s;
        }

        .grid-item:nth-child(2) {
            animation-delay: 0.2s;
        }

        @media (max-width: 768px) {
            .card-hover:hover {
                transform: none;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }
        }
        /* Harmonizar Select2 com Tailwind */
        .select2-container .select2-selection--single {
            height: 3rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 3rem;
            padding-left: 1rem;
            color: #111827;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 3rem;
            right: 0.75rem;
        }
        .select2-dropdown {
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            overflow: hidden;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen font-body">
    <div id="overlay" class="overlay fixed inset-0 bg-black/30 z-40 lg:hidden"></div>
    <div class="flex min-h-screen bg-gray-50 overflow-y-auto lg:overflow-hidden">
        <aside id="sidebar" class="sidebar fixed left-0 top-0 h-screen w-80 shadow-2xl z-50 lg:translate-x-0 lg:static lg:z-auto custom-scrollbar overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-8 pb-6 border-b border-white/20">
                    <div class="animate-slide-in-left">
                        <div class="flex items-center space-x-3 mb-2">
                            <img src="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png" alt="Brasão do Ceará" class="w-8 h-10 transition-transform hover:scale-105">
                            <h2 class="text-white text-2xl font-bold font-display">Sistema Seleção</h2>
                        </div>
                    </div>
                    <button id="closeSidebar" class="text-white lg:hidden btn-animate p-2 rounded-xl hover:bg-white/10 focus-ring">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <nav class="space-y-2">
                    <?php if (isset($_SESSION['tipo_usuario']) && ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'cadastrador')) { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.1s;">
                            <a href="../index.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Dashboard</span>
                                    <p class="text-green-200 text-xs mt-1">Página inicial</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.2s;">
                            <a href="cursos.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Cursos</span>
                                    <p class="text-green-200 text-xs mt-1">Administrar cursos</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                    <!-- Candidatos -->
                    <?php if (isset($_SESSION['tipo_usuario']) && ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'cadastrador')) { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.3s;">
                            <a href="candidatos.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring ">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Candidatos</span>
                                    <p class="text-green-200 text-xs mt-1">Gerenciar inscrições</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.4s;">
                            <a href="cotas.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a5 5 0 10-10 0v2M5 9h14l-1 11H6L5 9z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Cotas</span>
                                    <p class="text-green-200 text-xs mt-1">Regras e perfis</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                    

                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.45s;">
                            <a href="usuario.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Usuários</span>
                                    <p class="text-green-200 text-xs mt-1">Controle de acesso</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.5s;">
                            <a href="#" onclick="openModal('modalRelatorios')" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Relatórios</span>
                                    <p class="text-green-200 text-xs mt-1">Gerar documentos</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.55s;">
                            <a href="#" onclick="openModal('modalResultados')" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Resultados</span>
                                    <p class="text-green-200 text-xs mt-1">Consultar dados</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>
                    <?php if (isset($_SESSION['tipo_usuario']) && ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'cadastrador')) { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.6s;">
                            <a href="solicitar_alteracao.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring bg-white/10">
                                <div class="w-12 h-12 bg-secondary rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Requisição de cadastro</span>
                                    <p class="text-green-200 text-xs mt-1">Alteração de candidato</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.6s;">
                            <a href="limpar_banco.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-red-500 group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Limpar Banco</span>
                                    <p class="text-green-200 text-xs mt-1">Resetar dados</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>
                </nav>
            </div>
        </aside>

        <div class="main-content flex-1">
            <header class="bg-white shadow-sm border-b border-gray-200 z-30 sticky top-0">
                <div class="px-3 sm:px-4 md:px-6 lg:px-8 py-3 sm:py-4">
                    <div class="flex items-center justify-between">
                        <button id="openSidebar" class="text-primary lg:hidden btn-animate p-2 sm:p-3 rounded-xl hover:bg-accent focus-ring">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <div class="flex items-center space-x-2 sm:space-x-4 lg:ml-auto">
                            <div class="hidden sm:block text-right">
                                <p class="text-xs sm:text-sm font-semibold text-gray-900">Bem-vindo,</p>
                                <p class="text-xs sm:text-sm text-primary font-medium"><?= $_SESSION['nome'] ?? 'Usuário' ?></p>
                            </div>
                            <a href="../../main/views/perfil.php" title="Perfil" class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-primary to-dark rounded-full flex items-center justify-center hover:brightness-95 focus-ring">
                                <span class="text-white font-bold text-xs sm:text-sm"><?= strtoupper(substr($_SESSION['nome'] ?? 'U', 0, 1)) ?></span>
                            </a>
                            <a href="../models/sessions.php?sair" class="bg-primary text-white px-3 sm:px-4 md:px-6 py-2 sm:py-3 rounded-xl hover:bg-dark btn-animate font-semibold shadow-lg focus-ring text-xs sm:text-sm">
                                <span class="hidden sm:inline">Sair</span>
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-4 sm:p-6 lg:p-8 max-w-none mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 sm:gap-12">
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'cadastrador') { ?>
                        <!-- Formulário de Solicitação -->
                        <div class="grid-item lg:col-span-2">
                            <div class="bg-white rounded-2xl shadow-xl border-0 overflow-hidden card-hover mx-6 sm:mx-10 lg:mx-16">
                                <div class="h-2 w-full bg-gradient-to-r from-primary to-secondary"></div>
                                <div class="p-12">
                                    <div class="flex items-center gap-4 mb-8">
                                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary to-dark text-white flex items-center justify-center">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h2 class="text-2xl font-bold text-primary font-display">Nova Solicitação</h2>
                                            <p class="text-gray-600 text-base">Solicite alterações nos dados dos alunos</p>
                                        </div>
                                    </div>

                                    <form action="../controllers/controller_usuario.php" method="post" class="space-y-8">
                                        <input type="hidden" name="id_usuario" value="<?= $_SESSION['id'] ?>">
                                        <div>
                                            <label class="block text-base font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                Selecionar Aluno *
                                            </label>
                                            <select id="selectAluno" name="id_candidato" class="w-full px-4 py-4 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-base" required>
                                                <option value="" selected disabled>Selecionar aluno</option>
                                                <?php
                                                $dados = $select->select_candidatos();
                                                foreach ($dados as $dado) { ?>
                                                    <option value="<?= $dado['id'] ?>"><?= htmlspecialchars($dado['nome']) ?> | <?= htmlspecialchars($dado['nome_curso']) ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-base font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Descrição da Alteração *
                                            </label>
                                            <textarea id="descricaoAlteracao" name="descricao" class="w-full px-4 py-4 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-base resize-none" rows="8" placeholder="Descreva detalhadamente a alteração necessária nos dados do aluno (ex: correção de nome, CPF, endereço, etc.)" required maxlength="500"></textarea>
                                            <div class="flex justify-between items-center mt-2">
                                                <span class="text-sm text-gray-500">Máximo 500 caracteres</span>
                                                <span id="contadorCaracteres" class="text-sm text-gray-500 font-semibold">0/500</span>
                                            </div>
                                            <script>
                                                $(document).ready(function() {
                                                    $('#descricaoAlteracao').on('input', function() {
                                                        var currentLength = $(this).val().length;
                                                        var maxLength = 500;
                                                        $('#contadorCaracteres').text(currentLength + '/' + maxLength);
                                                        
                                                        if(currentLength >= maxLength) {
                                                            $('#contadorCaracteres').addClass('text-red-500');
                                                        } else if(currentLength >= maxLength * 0.8) {
                                                            $('#contadorCaracteres').addClass('text-yellow-500').removeClass('text-red-500');
                                                        } else {
                                                            $('#contadorCaracteres').removeClass('text-yellow-500 text-red-500');
                                                        }
                                                    });
                                                });
                                            </script>
                                        </div>

                                        <div class="pt-4">
                                            <button type="submit" class="w-full bg-gradient-to-r from-primary to-dark text-white px-6 py-4 rounded-xl hover:from-dark hover:to-primary btn-animate font-semibold shadow-lg focus-ring text-base">
                                                <span class="flex items-center justify-center">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                                    </svg>
                                                    Enviar Solicitação
                                                </span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php } else if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="grid-item lg:col-span-2">
                         
                            <!-- Filtros e estatísticas -->
                            <div class="mb-6">
                                <!-- Filtro de busca -->
                                <div class=" rounded-xl p-4 inline-block">
                                    <div class="relative">
                                        <input type="text" id="searchSolicitacoes" placeholder="Buscar solicitações..." class="pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:border-primary focus:outline-none focus:ring-0 w-full min-w-[200px] sm:w-64 md:w-80">
                                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                
                                <?php
                                $requisicoes_pendentes = $select->select_requisicoes_pendentes();
                                $requisicoes_recusadas = $select->select_requisicoes_rejeitadas();
                                $requisicoes_concluidas = $select->select_requisicoes_realizadas();
                                $total_pendentes = count($requisicoes_pendentes);
                                $total_recusadas = count($requisicoes_recusadas);
                                $total_concluidas = count($requisicoes_concluidas);
                                $total_geral = $total_pendentes + $total_recusadas + $total_concluidas;
                                ?>
                            </div>

                            <!-- Abas de navegação -->
                            <div class="mb-6">
                                <div class="flex border-b border-gray-200 focus:outline-none">
                                    <button data-tab="tab-pendentes" class="tab-button py-3 px-6 border-b-2 border-secondary text-secondary font-semibold focus:outline-none flex items-center">
                                        Pendentes
                                        <?php if ($total_pendentes > 0) { ?>
                                            <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-semibold text-white bg-yellow-500 rounded-full"><?= $total_pendentes ?></span>
                                        <?php } ?>
                                    </button>
                                    <button data-tab="tab-recusadas" class="tab-button py-3 px-6 border-b-2 border-transparent text-gray-500 hover:text-gray-700 focus:outline-none flex items-center">
                                        Recusadas
                                        <?php if ($total_recusadas > 0) { ?>
                                            <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-semibold text-white bg-red-500 rounded-full"><?= $total_recusadas ?></span>
                                        <?php } ?>
                                    </button>
                                    <button data-tab="tab-concluidas" class="tab-button py-3 px-6 border-b-2 border-transparent text-gray-500 hover:text-gray-700 focus:outline-none flex items-center">
                                        Concluídas
                                        <?php if ($total_concluidas > 0) { ?>
                                            <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-semibold text-white bg-green-500 rounded-full"><?= $total_concluidas ?></span>
                                        <?php } ?>
                                    </button>
                                </div>
                            </div>

                            <!-- Conteúdo das abas -->
                            <div class="grid grid-cols-1 gap-6 w-full px-2 sm:px-0">
                                <!-- Aba Pendente -->
                                <div id="tab-pendentes" class="tab-content">
                                    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                                        <?php
                                        if (empty($requisicoes_pendentes)) {
                                            echo '<div class="col-span-full flex items-center justify-center p-4 sm:p-12 bg-gray-50 rounded-xl">
                                                    <div class="text-center">
                                                        <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto text-gray-400 mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                        <p class="text-base sm:text-lg text-gray-500 font-medium">Nenhuma solicitação pendente</p>
                                                        <p class="text-sm sm:text-base text-gray-400 mt-1">Todas as solicitações foram processadas</p>
                                                    </div>
                                                  </div>';
                                        } else {
                                            foreach ($requisicoes_pendentes as $requisicao) { ?>
                                                <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-yellow-400 hover:shadow-lg transition-all duration-300 solicitacao-card w-full">
                                                    <div class="p-3 sm:p-5">
                                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-3 sm:mb-4">
                                                            <div class="w-full sm:w-auto">
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mb-2">
                                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                    </svg>
                                                                    Pendente
                                                                </span>
                                                                <h4 class="font-semibold text-gray-900 text-sm sm:text-base"><?= htmlspecialchars($requisicao['nome_user'] ?? 'Usuário Desconhecido') ?></h4>
                                                                <div class="flex items-center gap-2 mt-1">
                                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                                    </svg>
                                                                    <p class="text-xs sm:text-sm text-gray-500">Candidato: <span class="font-medium text-gray-700"><?= htmlspecialchars($requisicao['nome'] ?? 'Aluno Desconhecido') ?></span></p>
                                                                </div>
                                                                <div class="flex items-center gap-2 mt-1">
                                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                    </svg>
                                                                    <p class="text-xs sm:text-sm text-gray-500"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($requisicao['data_requisicao'] ?? 'now'))) ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="flex mt-2 sm:mt-0">
                                                                <button type="button" class="text-gray-400 hover:text-gray-600 p-1" onclick="toggleDetails(this)">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="bg-gray-50 p-3 sm:p-4 rounded-lg mb-3 sm:mb-4 solicitacao-details">
                                                            <h5 class="font-medium text-gray-700 mb-2 text-sm sm:text-base">Descrição da solicitação:</h5>
                                                            <p class="text-xs sm:text-sm text-gray-600 whitespace-pre-line"><?= htmlspecialchars($requisicao['texto']) ?></p>
                                                        </div>
                                                        
                                                        <div class="flex flex-col sm:flex-row gap-2">
                                                            <form action="../controllers/controller_usuario.php" method="post" class="update-status-form flex-1">
                                                                <input type="hidden" name="id_requisicao" value="<?= $requisicao['id_requisicao'] ?>">
                                                                <input type="hidden" name="novo_status" value="Recusado">
                                                                <button type="submit" class="w-full bg-white border border-red-500 text-red-500 px-3 py-2 rounded-lg hover:bg-red-50 transition-all font-medium text-xs sm:text-sm flex items-center justify-center">
                                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                    Recusar
                                                                </button>
                                                            </form>
                                                            <form action="../controllers/controller_usuario.php" method="post" class="update-status-form flex-1">
                                                                <input type="hidden" name="id_requisicao" value="<?= $requisicao['id_requisicao'] ?>">
                                                                <input type="hidden" name="novo_status" value="Concluido">
                                                                <button type="submit" class="w-full bg-primary text-white px-3 py-2 rounded-lg hover:bg-primary/90 transition-all font-medium text-xs sm:text-sm flex items-center justify-center">
                                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                    </svg>
                                                                    Concluir
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php }
                                        } ?>
                                    </div>
                                </div>
                                
                                <!-- Aba Recusada -->
                                <div id="tab-recusadas" class="tab-content hidden">
                                    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                                        <?php
                                        if (empty($requisicoes_recusadas)) {
                                            echo '<div class="col-span-full flex items-center justify-center p-4 sm:p-12 bg-gray-50 rounded-xl">
                                                    <div class="text-center">
                                                        <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto text-gray-400 mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                        <p class="text-base sm:text-lg text-gray-500 font-medium">Nenhuma solicitação recusada</p>
                                                        <p class="text-sm sm:text-base text-gray-400 mt-1">Não há solicitações recusadas no momento</p>
                                                    </div>
                                                  </div>';
                                        } else {
                                            foreach ($requisicoes_recusadas as $requisicao) { ?>
                                                <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-red-500 hover:shadow-lg transition-all duration-300 solicitacao-card w-full">
                                                    <div class="p-3 sm:p-5">
                                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-3 sm:mb-4">
                                                            <div class="w-full sm:w-auto">
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mb-2">
                                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                    Recusada
                                                                </span>
                                                                <h4 class="font-semibold text-gray-900 text-sm sm:text-base"><?= htmlspecialchars($requisicao['nome_user'] ?? 'Usuário Desconhecido') ?></h4>
                                                                <div class="flex items-center gap-2 mt-1">
                                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                                    </svg>
                                                                    <p class="text-sm text-gray-500">Candidato: <span class="font-medium text-gray-700"><?= htmlspecialchars($requisicao['nome'] ?? 'Aluno Desconhecido') ?></span></p>
                                                                </div>
                                                                <div class="flex items-center gap-2 mt-1">
                                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                    </svg>
                                                                    <p class="text-sm text-gray-500"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($requisicao['data_requisicao'] ?? 'now'))) ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="flex">
                                                                <button type="button" class="text-gray-400 hover:text-gray-600 p-1" onclick="toggleDetails(this)">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="bg-gray-50 p-4 rounded-lg mb-4 solicitacao-details">
                                                            <h5 class="font-medium text-gray-700 mb-2">Descrição da solicitação:</h5>
                                                            <p class="text-gray-600 whitespace-pre-line"><?= htmlspecialchars($requisicao['texto']) ?></p>
                                                        </div>
                                                        
                                                        <div class="flex gap-2">
                                                            <form action="../controllers/controller_usuario.php" method="post" class="update-status-form flex-1">
                                                                <input type="hidden" name="id_requisicao" value="<?= $requisicao['id_requisicao'] ?>">
                                                                <input type="hidden" name="novo_status" value="Pendente">
                                                                <button type="submit" class="w-full bg-white border border-yellow-500 text-yellow-500 px-3 py-2 rounded-lg hover:bg-yellow-50 transition-all font-medium text-sm flex items-center justify-center">
                                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                                    </svg>
                                                                    Marcar como Pendente
                                                                </button>
                                                            </form>
                                                            <form action="../controllers/controller_usuario.php" method="post" class="update-status-form flex-1">
                                                                <input type="hidden" name="id_requisicao" value="<?= $requisicao['id_requisicao'] ?>">
                                                                <input type="hidden" name="novo_status" value="Concluido">
                                                                <button type="submit" class="w-full bg-primary text-white px-3 py-2 rounded-lg hover:bg-primary/90 transition-all font-medium text-sm flex items-center justify-center">
                                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                    </svg>
                                                                    Concluir
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php }
                                        } ?>
                                    </div>
                                </div>
                                
                                <!-- Aba Concluída -->
                                <div id="tab-concluidas" class="tab-content hidden">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                        <?php
                                        $requisicoes_concluidas = $select->select_requisicoes_realizadas();
                                        if (empty($requisicoes_concluidas)) {
                                            echo '<div class="col-span-full flex items-center justify-center p-12 bg-gray-50 rounded-xl">
                                                    <div class="text-center">
                                                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                        <p class="text-lg text-gray-500 font-medium">Nenhuma solicitação concluída</p>
                                                        <p class="text-gray-400 mt-1">Não há solicitações concluídas no momento</p>
                                                    </div>
                                                  </div>';
                                        } else {
                                            foreach ($requisicoes_concluidas as $requisicao) { ?>
                                                <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-green-500 hover:shadow-lg transition-all duration-300 solicitacao-card">
                                                    <div class="p-5">
                                                        <div class="flex justify-between items-start mb-4">
                                                            <div>
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mb-2">
                                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                    </svg>
                                                                    Concluída
                                                                </span>
                                                                <h4 class="font-semibold text-gray-900"><?= htmlspecialchars($requisicao['nome_user'] ?? 'Usuário Desconhecido') ?></h4>
                                                                <div class="flex items-center gap-2 mt-1">
                                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                                    </svg>
                                                                    <p class="text-sm text-gray-500">Candidato: <span class="font-medium text-gray-700"><?= htmlspecialchars($requisicao['nome'] ?? 'Aluno Desconhecido') ?></span></p>
                                                                </div>
                                                                <div class="flex items-center gap-2 mt-1">
                                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                    </svg>
                                                                    <p class="text-sm text-gray-500"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($requisicao['data_requisicao'] ?? 'now'))) ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="flex">
                                                                <button type="button" class="text-gray-400 hover:text-gray-600 p-1" onclick="toggleDetails(this)">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="bg-gray-50 p-4 rounded-lg mb-4 solicitacao-details">
                                                            <h5 class="font-medium text-gray-700 mb-2">Descrição da solicitação:</h5>
                                                            <p class="text-gray-600 whitespace-pre-line"><?= htmlspecialchars($requisicao['texto']) ?></p>
                                                        </div>
                                                        
                                                        <div class="flex gap-2">
                                                            <form action="../controllers/controller_usuario.php" method="post" class="update-status-form flex-1">
                                                                <input type="hidden" name="id_requisicao" value="<?= $requisicao['id_requisicao'] ?>">
                                                                <input type="hidden" name="novo_status" value="Pendente">
                                                                <button type="submit" class="w-full bg-white border border-yellow-500 text-yellow-500 px-3 py-2 rounded-lg hover:bg-yellow-50 transition-all font-medium text-sm flex items-center justify-center">
                                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                                    </svg>
                                                                    Reabrir como Pendente
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php }
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </main>
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div id="modalConfirmacao" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-4 z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modalConfirmacaoContent">
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-dark font-display mb-4">Solicitação Enviada!</h3>
                <p class="text-gray-600 text-base mb-6 leading-relaxed">
                    Sua solicitação de alteração foi enviada com sucesso e será analisada pelo administrador.
                </p>
                <button type="button" class="px-6 py-3 rounded-xl border-2 border-primary font-semibold text-primary hover:bg-primary/10 hover:border-primary transition-all text-base focus-ring" onclick="closeModal('modalConfirmacao')">
                    Fechar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Relatórios -->
    <div id="modalRelatorios" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="modalRelatoriosContent">
            <div class="bg-gradient-to-r from-primary to-dark text-white p-6">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/30 shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold font-display">Gerar Relatórios PDF</h2>
                        <p class="text-white/90 text-sm mt-1">Crie documentos em PDF com dados do sistema</p>
                    </div>
                </div>
                <button class="absolute top-6 right-6 p-2 rounded-xl hover:bg-white/10 transition-all group" onclick="closeModal('modalRelatorios')">
                    <svg class="w-5 h-5 text-white group-hover:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <form action="../controllers/controller_relatorios.php" method="POST" class="space-y-6">
                    <input type="hidden" name="form" value="relatorio_pdf">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Tipo de Relatório *</label>
                        <select name="tipo_relatorio" required class="w-full px-4 py-3.5 border border-gray-300 rounded-xl input-modern focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-base">
                            <option value="" selected disabled>Selecione o tipo de relatório</option>
                            <option value="privada_ac">Privada AC</option>
                            <option value="privada_cotas">Privada Cotas</option>
                            <option value="privada_geral">Privada Geral</option>
                            <option value="publica_ac">Pública AC</option>
                            <option value="publica_cotas">Publica Cotas</option>
                            <option value="publica_geral">Pública Geral</option>
                            <option value="comissao_selecao">Comissão de Seleção</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Curso (Opcional)</label>
                        <select name="curso_id" class="w-full px-4 py-3.5 border border-gray-300 rounded-xl input-modern focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-base">
                            <option value="">Todos os cursos</option>
                            <?php foreach ($cursos as $curso) { ?>
                                <option value="<?= htmlspecialchars($curso['id']) ?>"><?= htmlspecialchars($curso['nome_curso']) ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <button type="submit" class="w-full bg-gradient-to-r from-primary to-dark text-white px-6 py-3.5 rounded-xl hover:from-dark hover:to-primary btn-animate font-semibold shadow-lg focus-ring transition-all text-base">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Gerar PDF
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Resultados -->
    <div id="modalResultados" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="modalRelatoriosContent">
            <div class="bg-gradient-to-r from-primary to-dark text-white p-6">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/30 shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold font-display">Consultar Resultados</h2>
                        <p class="text-white/90 text-sm mt-1">Visualize rankings e estatísticas do sistema</p>
                    </div>
                </div>
                <button class="absolute top-6 right-6 p-2 rounded-xl hover:bg-white/10 transition-all group" onclick="closeModal('modalResultados')">
                    <svg class="w-5 h-5 text-white group-hover:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <form action="../controllers/controller_relatorios.php" method="POST" class="space-y-6">
                    <input type="hidden" name="form" value="resultados">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Tipo de Consulta *</label>
                        <select name="tipo_consulta" required class="w-full px-4 py-3.5 border border-gray-300 rounded-xl input-modern focus:border-secondary focus:ring-4 focus:ring-secondary/10 transition-all text-base">
                            <option value="" selected disabled>Selecione o tipo de consulta</option>
                            <option value="classificados">Classificados</option>
                            <option value="classificaveis">Classificáveis</option>
                            <option value="resultado_final">Resultado Final</option>
                            <option value="resultado_preliminar">Resultado Preliminar</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Curso (Opcional)</label>
                        <select name="curso_id" class="w-full px-4 py-3.5 border border-gray-300 rounded-xl input-modern focus:border-secondary focus:ring-4 focus:ring-secondary/10 transition-all text-base">
                            <option value="">Todos os cursos</option>
                            <?php foreach ($cursos as $curso) { ?>
                                <option value="<?= htmlspecialchars($curso['id']) ?>"><?= htmlspecialchars($curso['nome_curso']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="pt-4 border-t border-gray-200">
                        <button type="submit" class="w-full bg-gradient-to-r from-primary to-dark text-white px-6 py-3.5 rounded-xl hover:from-dark hover:to-primary btn-animate font-semibold shadow-lg focus-ring transition-all text-base">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Consultar Resultados
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const openSidebar = document.getElementById('openSidebar');
        const closeSidebar = document.getElementById('closeSidebar');

        if (openSidebar) {
            openSidebar.addEventListener('click', () => {
                sidebar.classList.add('open');
                overlay.classList.add('show');
            });
        }
        if (closeSidebar) {
            closeSidebar.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            });
        }
        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            });
        }

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                const content = modal.querySelector('[id$="Content"]');
                if (content) {
                    content.style.transform = 'scale(1)';
                    content.style.opacity = '1';
                }
            }, 10);
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            const content = modal.querySelector('[id$="Content"]');
            if (content) {
                content.style.transform = 'scale(0.95)';
                content.style.opacity = '0';
            }
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        // Inicializa Select2 no campo de aluno
        $(document).ready(function() {
            const $selectAluno = $('#selectAluno');
            if ($selectAluno.length) {
                $selectAluno.select2({
                    width: '100%',
                    placeholder: 'Selecionar aluno',
                    allowClear: true,
                    language: 'pt-BR'
                });
            }
        });

        // Contador de caracteres
        const textarea = document.getElementById('descricaoAlteracao');
        const contador = document.getElementById('contadorCaracteres');

        if (textarea && contador) {
            textarea.addEventListener('input', function() {
                const caracteresDigitados = this.value.length;
                const limite = 500;

                if (caracteresDigitados > limite) {
                    this.value = this.value.substring(0, limite);
                    return;
                }

                contador.textContent = `${caracteresDigitados}/500`;

                if (caracteresDigitados > 450) {
                    contador.classList.add('text-red-500');
                    contador.classList.remove('text-gray-500');
                } else {
                    contador.classList.add('text-gray-500');
                    contador.classList.remove('text-red-500');
                }
            });
        }

        // Sistema de abas
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            // Verificar se há uma âncora na URL
            const hash = window.location.hash;
            if (hash) {
                const targetTabId = hash.substring(1); // Remove o # do início
                const targetButton = document.querySelector(`[data-tab="${targetTabId}"]`);
                
                if (targetButton) {
                    // Simular clique no botão da aba correspondente
                    setTimeout(() => {
                        targetButton.click();
                    }, 100);
                }
            }
            
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const tabId = button.getAttribute('data-tab');
                    
                    // Remover classes ativas de todos os botões
                    tabButtons.forEach(btn => {
                        btn.classList.remove('border-secondary', 'text-secondary', 'border-red-500', 'text-red-700', 'border-green-500', 'text-green-700', 'font-semibold');
                        btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700');
                    });
                    
                    // Adicionar classes ativas ao botão clicado com base no tipo de aba
                    button.classList.add('font-semibold');
                    button.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700');
                    
                    if (tabId === 'tab-pendentes') {
                        button.classList.add('border-secondary', 'text-secondary');
                    } else if (tabId === 'tab-recusadas') {
                        button.classList.add('border-red-500', 'text-red-700');
                    } else if (tabId === 'tab-concluidas') {
                        button.classList.add('border-green-500', 'text-green-700');
                    }
                    
                    // Esconder todos os conteúdos
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });
                    
                    // Mostrar o conteúdo correspondente
                    const activeContent = document.getElementById(tabId);
                    if (activeContent) {
                        activeContent.classList.remove('hidden');
                    }
                });
            });
        });

        // Função de notificação
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-xl shadow-lg transform transition-all duration-300 translate-x-full ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${type === 'success' ? 
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' : 
                            type === 'error' ? 
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>' :
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01"></path>'
                        }
                    </svg>
                    <span class="font-medium">${message}</span>
                </div>
            `;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 10);

            setTimeout(() => {
                notification.style.transform = 'translateX(full)';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }
    </script>
</body>

</html>