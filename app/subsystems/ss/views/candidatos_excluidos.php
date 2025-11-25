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

$candidatos_inativos = $select->select_candidatos_inativos();
$cursos = $select->select_cursos();
$usuarios = $select->select_usuarios();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Candidatos Excluídos</title>
    <link rel="icon" type="image/png" href="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png">
    <script src="https://cdn.tailwindcss.com"></script>
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

        .grid-item:nth-child(3) {
            animation-delay: 0.3s;
        }

        .grid-item:nth-child(4) {
            animation-delay: 0.4s;
        }

        .grid-item:nth-child(5) {
            animation-delay: 0.5s;
        }

        .grid-item:nth-child(6) {
            animation-delay: 0.6s;
        }

        @media (max-width: 768px) {
            .card-hover:hover {
                transform: none;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }
        }
    </style>
</head>

<body class="bg-white min-h-screen font-body">
    <div id="overlay" class="overlay fixed inset-0 bg-black/30 z-40 lg:hidden"></div>
    <div class="flex h-screen bg-gray-50 overflow-hidden">
        <aside id="sidebar" class="sidebar fixed left-0 top-0 h-screen w-80 shadow-2xl z-50 lg:translate-x-0 lg:static lg:z-auto custom-scrollbar overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-8 pb-6 border-b border-white/20">
                    <div class="animate-slide-in-left">
                        <div class="flex items-center space-x-3 mb-2">
                            <img src="../assets/Brasão_do_Ceará.svg.png" alt="Brasão do Ceará" class="w-8 h-10 transition-transform hover:scale-105">
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
                    <!-- Dashboard -->
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'cadastrador') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.1s;">
                            <a href="../index.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring ">
                                <div class="w-12 h-12 bg-white/10  rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
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

                    <!-- Usuários -->
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.2s;">
                            <a href="usuario.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring ">
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

                    <!-- Cursos -->
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.25s;">
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

                    <!-- Cotas -->
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.3s;">
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

                    <!-- Candidatos -->
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'cadastrador') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.35s;">
                            <a href="candidatos.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group bg-white/10 focus-ring">
                                <div class="w-12 h-12 bg-secondary rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
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

                    <!-- Requisições -->
                    <?php if (isset($_SESSION['tipo_usuario']) && ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'cadastrador')) { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.4s;">
                            <a href="solicitar_alteracao.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Requisições</span>
                                    <p class="text-green-200 text-xs mt-1">Alteração de candidato</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                    <!-- Relatórios -->
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.45s;">
                            <a href="./relatorios.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
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

                    <!-- Limpar Banco -->
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.5s;">
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

                    <!-- FAQ -->
                    <div class="animate-slide-in-left" style="animation-delay: 0.55s;">
                        <a href="faq.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <span class="font-semibold text-base">FAQ</span>
                                <p class="text-green-200 text-xs mt-1">Dúvidas frequentes</p>
                            </div>
                        </a>
                    </div>
                </nav>
            </div>
        </aside>
        <div class="main-content flex-1 h-screen overflow-y-auto custom-scrollbar bg-white">
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
            <main class="p-4 sm:p-6 lg:p-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="text-lg font-semibold text-gray-800">Candidatos Excluídos</div>
                    
                </div>

                <!-- Barra de Pesquisa -->
                <div class="mb-6">
                    <div class="flex items-center max-w-md space-x-3">
                        <div class="relative flex-1">
                            <input type="text" id="searchInput" placeholder="Pesquisar por nome do candidato..." class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm" onkeyup="filterCandidates()">
                            <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <a href="candidatos.php" class="bg-primary text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-xl hover:bg-dark transition-all duration-300 font-medium text-sm btn-animate focus-ring flex items-center shadow hover:shadow-lg">
                            <svg class="w-4 h-4 mr-1.5 sm:mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            <span>Voltar para Ativos</span>
                        </a>
                    </div>
                </div>                <style>
                    input:focus,
                    select:focus,
                    button:focus,
                    a:focus {
                        outline: 2px solid var(--primary);
                        outline-offset: 2px;
                    }
                </style>

                <script>
                    function filterCandidates() {
                        const searchInput = document.getElementById('searchInput').value.toLowerCase();
                        const tableRows = document.querySelectorAll('tbody tr');
                        const candidateCards = document.querySelectorAll('.candidate-card');

                        tableRows.forEach(row => {
                            const nome = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                            row.style.display = nome.includes(searchInput) ? '' : 'none';
                        });

                        candidateCards.forEach(card => {
                            const nome = card.querySelector('.field:nth-child(2) .field-value')?.textContent?.toLowerCase?.() || '';
                            card.style.display = nome.includes(searchInput) ? '' : 'none';
                        });
                    }
                </script>

                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm hidden sm:table">
                            <thead>
                                <tr class="bg-gradient-to-r from-primary to-dark text-white">
                                    <th class="px-6 py-4 text-left text-sm font-semibold font-display">Nome</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold font-display">Curso</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold font-display">Seguimento</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold font-display">Origem</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold font-display">Data</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold font-display">Cadastrador</th>
                                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                                        <th class="px-6 py-4 text-center text-sm font-semibold font-display">Ações</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php
                                foreach ($candidatos_inativos as $cand) {
                                    $id = $cand['id'] ?? '-';
                                    $nome = $cand['nome'] ?? '-';
                                    $cursoNome = $cand['nome_curso'] ?? '-';
                                    $origem = (isset($cand['publica']) && (int)$cand['publica'] === 1) ? 'Pública' : 'Privada';
                                    $data = $cand['data'] ?? '-';
                                    $cadastradorNome = $cand['nome_user'] ?? '-';

                                    if (($cand['bairro'] ?? 0) == 1) {
                                        $cota = 'BAIRRO';
                                    } else if (($cand['pcd'] ?? 0) == 1) {
                                        $cota = 'PCD';
                                    } else {
                                        $cota = 'AMPLA';
                                    }
                                ?>
                                    <tr class="hover:bg-gradient-to-r hover:from-primary/5 hover:to-accent/10 transition-all duration-200 bg-gray-50 opacity-75 group">
                                        <td class="px-6 py-4">
                                            <div>
                                                <div class="text-sm font-semibold text-gray-400"><?= htmlspecialchars((string)$nome) ?></div>
                                                <div class="text-xs text-gray-500 font-medium">Desativado</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-400">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                                <?= htmlspecialchars((string)$cursoNome) ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                                                <?= htmlspecialchars((string)$cota) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-600 border border-gray-300">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                <?= $origem ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-400">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <?= htmlspecialchars((string)$data) ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-400">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <?= htmlspecialchars((string)$cadastradorNome) ?>
                                            </div>
                                        </td>
                                        <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                                            <td class="px-6 py-4">
                                                <div class="flex space-x-2 justify-center">
                                                    <button type="button" onclick="openActivateModal(<?= $id ?>, '<?= htmlspecialchars($nome) ?>')" class="inline-flex items-center bg-primary text-white px-4 py-2 rounded-xl hover:bg-dark transition-all duration-300 font-medium text-sm btn-animate focus-ring shadow hover:shadow-lg">
                                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        <span>Ativar</span>
                                                    </button>
                                                </div>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                                <?php if (count($candidatos_inativos) === 0) { ?>
                                    <tr>
                                        <td colspan="<?= isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin' ? '7' : '6' ?>" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                                    </svg>
                                                </div>
                                                <h3 class="text-lg font-semibold text-gray-500 mb-2">Nenhum candidato excluído</h3>
                                                <p class="text-sm text-gray-400">Candidatos desativados aparecerão aqui</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <!-- Cards para mobile -->
                        <div class="block sm:hidden grid gap-4 p-4">
                            <?php foreach ($candidatos_inativos as $cand) {
                                $id = $cand['id'] ?? '-';
                                $nome = $cand['nome'] ?? '-';
                                $cursoNome = $cand['nome_curso'] ?? '-';
                                $origem = (isset($cand['publica']) && (int)$cand['publica'] === 1) ? 'Pública' : 'Privada';
                                $data = $cand['data'] ?? '-';
                                $cadastradorNome = $cand['nome_user'] ?? '-';

                                if (($cand['bairro'] ?? 0) == 1) {
                                    $cota = 'BAIRRO';
                                } else if (($cand['pcd'] ?? 0) == 1) {
                                    $cota = 'PCD';
                                } else {
                                    $cota = 'AMPLA';
                                }
                            ?>
                                <div class="candidate-card bg-white rounded-xl shadow-md p-4 border border-gray-100 hover:border-primary/20 transition-all duration-300">
                                    <div class="flex flex-col space-y-3">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <div class="text-gray-400 font-semibold"><?= htmlspecialchars($nome) ?></div>
                                                <div class="text-xs text-gray-500">Desativado</div>
                                            </div>
                                            <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                                                <button type="button" onclick="openActivateModal(<?= $id ?>, '<?= htmlspecialchars($nome) ?>')" class="inline-flex items-center bg-primary text-white px-3 py-1.5 rounded-xl hover:bg-dark transition-all duration-300 font-medium text-xs btn-animate focus-ring shadow hover:shadow-lg">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    <span>Ativar</span>
                                                </button>
                                            <?php } ?>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-3 text-sm">
                                            <div class="field">
                                                <div class="text-gray-500 text-xs mb-1">Curso</div>
                                                <div class="text-gray-400 font-medium flex items-center">
                                                    <svg class="w-3.5 h-3.5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    </svg>
                                                    <?= htmlspecialchars($cursoNome) ?>
                                                </div>
                                            </div>
                                            
                                            <div class="field">
                                                <div class="text-gray-500 text-xs mb-1">Seguimento</div>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                                                    <?= htmlspecialchars($cota) ?>
                                                </span>
                                            </div>

                                            <div class="field">
                                                <div class="text-gray-500 text-xs mb-1">Origem</div>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-600 border border-gray-300">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <?= $origem ?>
                                                </span>
                                            </div>

                                            <div class="field">
                                                <div class="text-gray-500 text-xs mb-1">Cadastrador</div>
                                                <div class="text-gray-400 font-medium flex items-center">
                                                    <svg class="w-3.5 h-3.5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    <?= htmlspecialchars($cadastradorNome) ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="field border-t border-gray-100 pt-3 mt-2">
                                            <div class="text-gray-400 text-xs flex items-center">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                Cadastrado em: <?= htmlspecialchars($data) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (count($candidatos_inativos) === 0) { ?>
                                <div class="text-center py-8">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-500 mb-2">Nenhum candidato excluído</h3>
                                        <p class="text-sm text-gray-400">Candidatos desativados aparecerão aqui</p>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Confirmar Ativação -->
    <div id="modalActivateConfirm" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modalActivateConfirmContent">
            <div class="p-6 sm:p-8 text-center">
                <div class="w-20 h-20 rounded-full bg-secondary/20 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M4.93 4.93l14.14 14.14"></path>
                    </svg>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-dark font-heading mb-4">Confirmar Ativação</h3>
                <p class="text-gray-600 text-base mb-6 leading-relaxed">
                    Tem certeza que deseja ativar o candidato <span class="font-semibold text-dark" id="activateCandidatoName"></span>?
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <button type="button" class="px-6 py-3 rounded-xl border-2 border-secondary font-semibold text-secondary hover:bg-secondary/10 hover:border-secondary transition-all text-base focus-ring" onclick="closeModal('modalActivateConfirm')">Cancelar</button>
                    <a id="activateLink" href="#" class="px-6 py-3 bg-gradient-to-r from-secondary to-orange-600 text-white font-semibold rounded-xl hover:from-secondary/90 hover:to-orange-700 transition-all text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus-ring text-center">Confirmar Ativação</a>
                </div>
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

        function openActivateModal(id, nome) {
            document.getElementById('activateCandidatoName').textContent = nome;
            document.getElementById('activateLink').href = `../controllers/controller_candidato.php?id_excluir=${id}&tipo=ativar`;
            openModal('modalActivateConfirm');
        }
    </script>
</body>

</html>

