<?php
require_once __DIR__ . "/../models/sessions.php";
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once __DIR__ . "/../config/connect.php";
$escola = $_SESSION["escola"];

new connect($escola);
require_once __DIR__ . "/../models/model.select.php";
$select = new select($escola);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Cotas</title>
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

        /* Remove default blue focus and align with system palette */
        input,
        select,
        textarea,
        button {
            outline: none;
        }

        input:focus,
        select:focus,
        textarea:focus,
        button:focus {
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(0, 90, 36, 0.15) !important;
            /* primary glow */
            border-color: var(--primary) !important;
        }

        /* Disabled input visual standard */
        .input-disabled[disabled] {
            background: #F1F5F9 !important;
            /* slate-100 */
            color: #334155 !important;
            /* slate-700 */
            border-color: rgba(0, 90, 36, 0.15) !important;
            cursor: not-allowed;
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
                                    <span class="font-semibold text-base">Início</span>
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
                            <a href="cotas.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group bg-white/10 focus-ring">
                                <div class="w-12 h-12 bg-secondary rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
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
                            <a href="candidatos.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
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

                    <!-- Perfil Escola -->
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.53s;">
                            <a href="perfil_escola.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 17l9 4 9-4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Perfil Escola</span>
                                    <p class="text-green-200 text-xs mt-1">Dados e foto da escola</p>
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
                                <p class="text-xs sm:text-sm text-primary font-medium"><?= $_SESSION["nome"] ?? "Usuário" ?></p>
                            </div>
                            <a href="../../main/views/perfil.php" title="Perfil" class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-primary to-dark rounded-full flex items-center justify-center hover:brightness-95 focus-ring">
                                <span class="text-white font-bold text-xs sm:text-sm"><?= strtoupper(
                                                                                            substr($_SESSION["nome"] ?? "U", 0, 1),
                                                                                        ) ?></span>
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
                <?php
                $bairros = $select->select_bairros();
                $cursos = $select->select_cursos();
                if (count($bairros) === 0) {
                    if (count($cursos) === 0) { ?>
                        <div class="bg-gradient-to-br from-red-50 via-white to-red-50 border-2 border-dashed border-red-300 rounded-2xl p-8 text-center animate-fade-in-up max-w-2xl mx-auto">
                            <div class="w-20 h-20 bg-gradient-to-br from-red-100 to-red-200 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse-soft">
                                <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M4.93 4.93l14.14 14.14"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-red-700 mb-2 font-display">Nenhum curso cadastrado</h3>
                            <p class="text-gray-600 mb-6">É necessário cadastrar pelo menos um curso antes de criar cotas.</p>
                            <a href="cursos.php" class="inline-flex items-center bg-gradient-to-r from-red-600 to-red-700 text-white px-6 py-3 rounded-xl hover:from-red-700 hover:to-red-800 btn-animate font-semibold shadow-xl focus-ring">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Cadastrar Cursos
                            </a>
                        </div>
                    <?php } else { ?>
                        <div class="bg-gradient-to-br from-accent via-white to-accent/50 border-2 border-dashed border-primary/30 rounded-2xl p-8 text-center animate-fade-in-up max-w-2xl mx-auto">
                            <div class="w-20 h-20 bg-gradient-to-br from-primary/10 to-secondary/10 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse-soft">
                                <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-primary mb-2 font-display">Nenhuma cota ativa</h3>
                            <p class="text-gray-600 mb-6">Aqui você definirá a distribuição de vagas para cotista.</p>
                            <button onclick="openBairroModal()" class="inline-flex items-center bg-gradient-to-r from-primary to-dark text-white px-6 py-3 rounded-xl hover:from-dark hover:to-primary btn-animate font-semibold shadow-xl focus-ring">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Criar cotas
                            </button>
                        </div>
                    <?php } ?>
                <?php
                } else {

                    $vagas = $select->select_vagas();
                    $pcd = $vagas["quantidade_alunos"] - 2;
                    $total_publica = $pcd * (80 / 100);
                    $total_privada = $pcd * (20 / 100);
                    $publica_cotas = $total_publica * (30 / 100);
                    $privada_cotas = $total_privada * (30 / 100);
                    $publica_ac = $total_publica * (70 / 100);
                    $privada_ac = $total_privada * (70 / 100);
                ?>
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-primary to-dark rounded-xl flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a5 5 0 10-10 0v2M5 9h14l-1 11H6L5 9z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h1 class="text-2xl font-bold text-primary font-display">Cotas</h1>
                                        <p class="text-sm text-gray-600">Distribuição de vagas por bairros</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-700 border border-green-200 shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M7 20H2v-2a3 3 0 015.356-1.857m9.288 0a5.002 5.002 0 00-9.288 0"></path>
                                        </svg>
                                        <?= count($bairros) ?> <?= count(
                                                                    $bairros,
                                                                ) === 1
                                                                    ? "bairro ativo"
                                                                    : "bairros ativos" ?>
                                    </span>

                                </div>
                            </div>
                        </div>
                        <div class="h-px bg-gradient-to-r from-transparent via-primary/20 to-transparent"></div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center mr-3">
                                <span class="font-bold">1</span>
                            </div>
                            <h3 class="text-xl font-bold text-primary">Distribuição de Vagas</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-green-50 p-5 rounded-xl border border-green-200 shadow-sm">
                                <h4 class="font-semibold text-green-700 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Total
                                </h4>
                                <div class="flex items-center">
                                    <input type="text" class="w-full px-4 py-3 rounded-lg border input-disabled" value="<?= $vagas["quantidade_alunos"] ?>" disabled>
                                </div>
                            </div>

                            <div class="bg-green-50 p-5 rounded-xl border border-green-200 shadow-sm">
                                <h4 class="font-semibold text-green-700 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    PCD
                                </h4>
                                <div class="flex items-center">
                                    <input type="text" class="w-full px-4 py-3 rounded-lg border input-disabled" value="2" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-green-50 p-5 rounded-xl border border-green-200 shadow-sm">
                                <h4 class="font-semibold text-green-700 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    Escola Pública
                                </h4>
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <span class="w-24 text-sm font-medium text-green-700">Total Pública:</span>
                                        <input type="text" class="flex-1 px-4 py-3 rounded-lg border input-disabled" value="<?= round(
                                                                                                                                $publica_ac + $publica_cotas,
                                                                                                                            ) ?>" disabled>
                                    </div>
                                    <div class="border-t border-green-200 my-2 pt-2"></div>
                                    <div class="flex items-center">
                                        <span class="w-24 text-sm font-medium text-green-700">AC:</span>
                                        <input type="text" class="flex-1 px-4 py-3 rounded-lg border input-disabled" value="<?= round(
                                                                                                                                $publica_ac,
                                                                                                                            ) ?>" disabled>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-24 text-sm font-medium text-green-700">Cota Bairro:</span>
                                        <input type="text" class="flex-1 px-4 py-3 rounded-lg border input-disabled" value="<?= round(
                                                                                                                                $publica_cotas,
                                                                                                                            ) ?>" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-green-50 p-5 rounded-xl border border-green-200 shadow-sm">
                                <h4 class="font-semibold text-green-700 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    Escola Privada
                                </h4>
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <span class="w-24 text-sm font-medium text-green-700">Total Privada:</span>
                                        <input type="text" class="flex-1 px-4 py-3 rounded-lg border input-disabled" value="<?= round(
                                                                                                                                $privada_ac + $privada_cotas,
                                                                                                                            ) ?>" disabled>
                                    </div>
                                    <div class="border-t border-green-200 my-2 pt-2"></div>
                                    <div class="flex items-center">
                                        <span class="w-24 text-sm font-medium text-green-700">AC:</span>
                                        <input type="text" class="flex-1 px-4 py-3 rounded-lg border input-disabled" value="<?= round(
                                                                                                                                $privada_ac,
                                                                                                                            ) ?>" disabled>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-24 text-sm font-medium text-green-700">Cota Bairro:</span>
                                        <input type="text" class="flex-1 px-4 py-3 rounded-lg border input-disabled" value="<?= round(
                                                                                                                                $privada_cotas,
                                                                                                                            ) ?>" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 md:flex-row md:justify-end mt-6">
                            <button type="button" onclick="openEditVagasModal()" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl border-2 border-primary text-primary font-semibold shadow-lg hover:bg-primary/5 transition-all duration-300 btn-animate focus-ring">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3zM3 21h6"></path>
                                </svg>
                                Editar vagas
                            </button>
                            <button type="button" onclick="openAddBairroModal()" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-primary to-dark text-white font-semibold shadow-lg hover:shadow-xl transition-all duration-300 btn-animate focus-ring">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Adicionar Bairro
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4">
                        <?php foreach ($bairros as $bairro) { ?>
                            <article class="grid-item card-hover bg-white rounded-2xl shadow-xl border-0 overflow-hidden group relative flex flex-col h-[200px]" data-id="<?= htmlspecialchars(
                                                                                                                                                                                $bairro["id"],
                                                                                                                                                                            ) ?>" data-nome="<?= htmlspecialchars(
                                                    $bairro["bairros"],
                                                ) ?>">
                                <div class="h-2 w-full bg-gradient-to-r from-primary to-secondary"></div>
                                <div class="p-6 flex flex-col flex-grow">
                                    <div class="text-center mb-4 flex-grow">
                                        <h3 class="text-xl font-bold leading-tight font-display group-hover:scale-105 transition-all duration-300 text-primary"><?= htmlspecialchars(
                                                                                                                                                                    $bairro["bairros"] ?? "Sem nome",
                                                                                                                                                                ) ?></h3>
                                        <div class="w-16 h-0.5 mx-auto mt-3 rounded-full bg-primary/40"></div>
                                    </div>
                                    <div class="flex space-x-2 mt-auto">
                                        <button type="button" onclick='openEditBairro(<?= json_encode(
                                                                                            $bairro["id"] ?? "",
                                                                                        ) ?>, <?= json_encode(
                                                    $bairro["bairros"],
                                                ) ?>)' class="flex-1 bg-primary text-white py-2 px-4 rounded-lg hover:bg-dark transition-all duration-300 font-medium text-sm btn-animate focus-ring">Editar</button>
                                        <button type="button" onclick='openDeleteBairro(<?= json_encode(
                                                                                            $bairro["id"] ?? "",
                                                                                        ) ?>, <?= json_encode(
                                                    $bairro["bairros"],
                                                ) ?>)' class="flex-1 bg-secondary text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition-all duration-300 font-medium text-sm btn-animate focus-ring">Excluir</button>
                                    </div>
                                </div>
                            </article>
                        <?php } ?>
                    
                    </div>
                <?php
                }
                ?>
            </main>
        </div>
    </div>


    <!-- Feedback Modal -->
    <div id="modalFeedback" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modalFeedbackContent">
            <div class="p-6 sm:p-8 text-center">
                <div id="modalFeedbackIcon" class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6"></div>
                <h3 id="modalFeedbackTitle" class="text-xl sm:text-2xl font-bold text-dark font-heading mb-2"></h3>
                <p id="modalFeedbackMsg" class="text-gray-600 text-base mb-6 leading-relaxed"></p>
                <button type="button" class="px-6 py-3 rounded-xl border-2 border-primary font-semibold text-primary hover:bg-primary/10 hover:border-primary transition-all text-base focus-ring" onclick="closeModal('modalFeedback')">Fechar</button>
            </div>
        </div>
    </div>

    <!-- Modal Criar/Editar Bairro -->
    <div id="modalBairro" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-40">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="modalBairroContent">
            <div class="p-6 sm:p-8 border-b border-primary/10 bg-gradient-to-r from-accent/30 to-white">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary to-dark text-white flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 id="modalBairroTitle" class="text-xl sm:text-2xl font-bold text-dark font-heading">Cadastrar Cota</h3>

                    </div>
                </div>
                <button class="absolute top-6 right-6 p-2 rounded-xl hover:bg-gray-100 transition-all group border border-gray-200" onclick="closeModal('modalBairro')">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 group-hover:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 sm:p-8">
                <form id="bairroForm" action="../controllers/controller_bairro.php" method="POST">
                    <input type="hidden" name="form" value="bairro">
                    <input type="hidden" id="inpBairroId" name="id_bairro" value="">

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-dark mb-3">Informe o nº de alunos por turma *</label>
                        <input id="inpBairroNome" name="quantidades" type="number" min="1" max="999" step="1" class="w-full px-4 py-3.5 rounded-xl transition-all text-base border-2 focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="Digite o número de alunos" required oninput="this.value = this.value.replace(/[^0-9]/g, '')" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-dark mb-3">Bairros da Cota *</label>
                        <div id="bairros-container" class="space-y-3">
                            <!-- Campo inicial: botão será ajustado dinamicamente para que o + fique sempre ao lado do último input -->
                            <div class="flex items-center bairro-field">
                                <input type="text" name="bairros[]" class="flex-1 px-4 py-3.5 rounded-xl transition-all text-base border-2 focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="Digite o nome do bairro" required>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 sm:p-8 border-t border-primary/10 bg-accent/20 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 mt-6 -mx-6 -mb-6 sm:-mx-8 sm:-mb-8">
                        <button type="button" class="px-6 py-3 rounded-xl border-2 border-primary font-semibold text-primary hover:bg-primary/10 hover:border-primary transition-all text-base focus-ring" onclick="closeModal('modalBairro'); window.history.replaceState({}, document.title, window.location.pathname);">Cancelar</button>
                        <button type="button" class="px-6 py-3 bg-gradient-to-r from-primary to-dark text-white font-semibold rounded-xl hover:from-primary/90 hover:to-dark/90 transition-all text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus-ring flex items-center" onclick="openReviewModal(); window.history.replaceState({}, document.title, window.location.pathname);">
                            Avançar
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Revisão de Cotas -->
    <div id="modalReview" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="modalReviewContent">
            <div class="bg-gradient-to-r from-primary to-dark text-white p-6">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/30 shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold font-display">Revisão das Cotas</h2>
                        <p class="text-white/90 text-sm mt-1">Confirme as informações antes de finalizar</p>
                    </div>
                </div>
                <button class="absolute top-6 right-6 p-2 rounded-xl hover:bg-white/10 transition-all group" onclick="closeModal('modalReview')">
                    <svg class="w-5 h-5 text-white group-hover:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Distribuição de Vagas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <p class="text-sm font-medium text-gray-600">Total:</p>
                            <p class="text-lg font-semibold text-gray-800" id="review-total"></p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <p class="text-sm font-medium text-green-700">PCD:</p>
                            <p class="text-lg font-semibold text-green-800" id="review-pcd">2</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <p class="text-sm font-medium text-green-700">Escola Pública:</p>
                            <div class="flex justify-between mt-1">
                                <span class="text-sm text-green-600">Total Pública:</span>
                                <span class="font-medium text-green-800" id="review-publica-total"></span>
                            </div>
                            <div class="mt-2 pt-2 border-t border-green-200">
                                <div class="flex justify-between">
                                    <span class="text-sm text-green-600">AC:</span>
                                    <span class="font-medium text-green-800" id="review-publica-ac"></span>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span class="text-sm text-green-600">Cota Bairro:</span>
                                    <span class="font-medium text-green-800" id="review-publica-cota"></span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <p class="text-sm font-medium text-green-700">Escola Privada:</p>
                            <div class="flex justify-between mt-1">
                                <span class="text-sm text-green-600">Total Privada:</span>
                                <span class="font-medium text-green-800" id="review-privada-total"></span>
                            </div>
                            <div class="mt-2 pt-2 border-t border-green-200">
                                <div class="flex justify-between">
                                    <span class="text-sm text-green-600">AC:</span>
                                    <span class="font-medium text-green-800" id="review-privada-ac"></span>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span class="text-sm text-green-600">Cota Bairro:</span>
                                    <span class="font-medium text-green-800" id="review-privada-cota"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Alunos por Turma</h3>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="text-lg font-semibold text-gray-800" id="review-alunos"></p>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Bairros da Cota</h3>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <ul class="list-disc pl-5 space-y-1" id="review-bairros">
                            <!-- Bairros serão inseridos aqui via JavaScript -->
                        </ul>
                    </div>
                </div>

                <div class="flex justify-between mt-6 pt-4 border-t border-gray-200">
                    <button type="button" class="px-6 py-3 rounded-xl border-2 border-primary font-semibold text-primary hover:bg-primary/10 hover:border-primary transition-all text-base focus-ring flex items-center" onclick="closeModal('modalReview'); openBairroModal(); window.history.replaceState({}, document.title, window.location.pathname);">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </button>
                    <button type="button" onclick="document.getElementById('bairroForm').submit(); window.history.replaceState({}, document.title, window.location.pathname);" class="px-6 py-3 bg-gradient-to-r from-primary to-dark text-white font-semibold rounded-xl hover:from-primary/90 hover:to-dark/90 transition-all text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus-ring flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Concluir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Adicionar Bairro -->
    <div id="modalAddBairro" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modalAddBairroContent">
            <div class="bg-gradient-to-r from-primary to-dark text-white p-4 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold">Adicionar Bairro</h3>
                    </div>
                    <button class="p-2 rounded-xl hover:bg-white/10" onclick="closeModal('modalAddBairro')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 sm:p-8">
                <form id="addBairroForm" action="../controllers/controller_bairro.php" method="POST">
                    <input type="hidden" name="form" value="bairro">
                    <input type="hidden" name="quantidades" id="addBairroQuantidade" value="<?= htmlspecialchars($vagas['quantidade_alunos'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-dark mb-3">Nome do Novo Bairro *</label>
                        <input id="addBairroNome" name="bairros[]" type="text" class="w-full px-4 py-3.5 rounded-xl transition-all text-base border-2 focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="Digite o nome do bairro" required>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 justify-end">
                        <button type="button" class="px-6 py-3 rounded-xl border-2 border-primary font-semibold text-primary hover:bg-primary/10 hover:border-primary transition-all text-base focus-ring" onclick="closeModal('modalAddBairro'); window.history.replaceState({}, document.title, window.location.pathname);">Cancelar</button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-primary to-dark text-white font-semibold rounded-xl hover:from-primary/90 hover:to-dark/90 transition-all text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus-ring flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Salvar Bairro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Vagas -->
    <div id="modalEditVagas" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modalEditVagasContent">
            <div class="bg-gradient-to-r from-primary to-dark text-white p-4 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v4m0 0H8m4 0h4M6 20h12"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold">Editar Quantidade de Vagas</h3>
                    </div>
                    <button class="p-2 rounded-xl hover:bg-white/10" onclick="closeModal('modalEditVagas')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 sm:p-8">
                <form id="editVagasForm" action="../controllers/controller_bairro.php" method="POST">
                    <input type="hidden" name="form" value="bairro">
                    <input type="hidden" name="acao" value="editar_vagas">
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-dark mb-3">Total de alunos por turma *</label>
                        <input id="editVagasQuantidade" name="quantidades" type="number" min="1" max="999" step="1" class="w-full px-4 py-3.5 rounded-xl transition-all text-base border-2 focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="Informe o total de alunos" required oninput="this.value = this.value.replace(/[^0-9]/g, '')" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 justify-end">
                        <button type="button" class="px-6 py-3 rounded-xl border-2 border-primary font-semibold text-primary hover:bg-primary/10 hover:border-primary transition-all text-base focus-ring" onclick="closeModal('modalEditVagas'); window.history.replaceState({}, document.title, window.location.pathname);">Cancelar</button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-primary to-dark text-white font-semibold rounded-xl hover:from-primary/90 hover:to-dark/90 transition-all text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus-ring flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Salvar alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Exclusão -->
    <div id="modalDeleteBairro" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modalDeleteBairroContent">
            <div class="bg-gradient-to-r from-primary to-dark text-white p-4 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M4.93 4.93l14.14 14.14"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold">Confirmar Exclusão</h3>
                    </div>
                    <button class="p-2 rounded-xl hover:bg-white/10" onclick="closeModal('modalDeleteBairro')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 sm:p-8 text-center">
                <div class="w-16 h-16 rounded-full bg-gradient-to-r from-primary to-dark flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M4.93 4.93l14.14 14.14"></path>
                    </svg>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-dark font-heading mb-4">Confirmar Exclusão</h3>
                <p class="text-gray-600 text-base mb-6 leading-relaxed">
                    Tem certeza que deseja excluir o bairro <span class="font-semibold text-dark" id="deleteBairroName"></span>?
                </p>
                <p class="text-sm text-red-600 bg-red-50 px-4 py-3 rounded-lg border border-red-200 mb-6">
                    Esta ação não pode ser desfeita.
                </p>
                <form id="deleteBairroForm" action="../controllers/controller_bairro.php" method="POST">
                    <input type="hidden" name="form" value="bairro">
                    <input type="hidden" id="deleteBairroId" name="id_bairro" value="">
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button type="button" class="px-6 py-3 rounded-xl border-2 border-primary font-semibold text-primary hover:bg-primary/10 hover:border-primary transition-all text-base focus-ring" onclick="closeModal('modalDeleteBairro'); window.history.replaceState({}, document.title, window.location.pathname);">Cancelar</button>
                        <button type="submit" name="acao" value="delete" class="px-6 py-3 bg-gradient-to-r from-primary to-dark text-white font-semibold rounded-xl hover:from-primary/90 hover:to-dark/90 transition-all text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus-ring" onclick="window.history.replaceState({}, document.title, window.location.pathname);">Excluir Bairro</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Bairro Individual -->
    <div id="modalEditBairro" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modalEditBairroContent">
            <div class="bg-gradient-to-r from-primary to-dark text-white p-4 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold">Editar Bairro</h3>
                    </div>
                    <button class="p-2 rounded-xl hover:bg-white/10" onclick="closeModal('modalEditBairro')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 sm:p-8">
                <form id="editBairroForm" action="../controllers/controller_bairro.php" method="POST">
                    <input type="hidden" name="form" value="bairro">
                    <input type="hidden" name="acao" value="edit">
                    <input type="hidden" id="editBairroId" name="id_bairro" value="">

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-dark mb-3">Nome do Bairro *</label>
                        <input id="editBairroNome" name="nome_bairro" type="text" class="w-full px-4 py-3.5 rounded-xl transition-all text-base border-2 focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="Digite o nome do bairro" required>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 justify-end">
                        <button type="button" class="px-6 py-3 rounded-xl border-2 border-primary font-semibold text-primary hover:bg-primary/10 hover:border-primary transition-all text-base focus-ring" onclick="closeModal('modalEditBairro'); window.history.replaceState({}, document.title, window.location.pathname);">Cancelar</button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-primary to-dark text-white font-semibold rounded-xl hover:from-primary/90 hover:to-dark/90 transition-all text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus-ring">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Salvar Alterações
                            </span>
                        </button>
                    </div>
                </form>
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
                        <select name="tipo_relatorio" id="tipo_relatorio" required class="w-full px-4 py-3.5 border border-gray-300 rounded-xl input-modern focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-base">
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
                        <label class="block text-sm font-semibold text-gray-700 mb-3" id="label_curso">Curso (Opcional)</label>
                        <select name="curso_id" id="curso_id" class="w-full px-4 py-3.5 border border-gray-300 rounded-xl input-modern focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-base">
                            <option value="">Todos os cursos</option>
                            <?php
                            $cursos = $select->select_cursos();
                            foreach ($cursos as $curso) { ?>
                                <option value="<?= htmlspecialchars(
                                                    $curso["id"],
                                                ) ?>"><?= htmlspecialchars(
                                            $curso["nome_curso"],
                                        ) ?></option>
                            <?php }
                            ?>
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
                            <?php
                            $cursos = $select->select_cursos();
                            foreach ($cursos as $curso) { ?>
                                <option value="<?= htmlspecialchars(
                                                    $curso["id"],
                                                ) ?>"><?= htmlspecialchars(
                                            $curso["nome_curso"],
                                        ) ?></option>
                            <?php }
                            ?>
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
        const bairros = <?php echo json_encode($bairros ?? []); ?>;

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

        function openBairroModal() {
            // Verificar se existem cursos cadastrados
            <?php if (count($cursos) === 0) { ?>
                alert('É necessário cadastrar pelo menos um curso antes de criar cotas.');
                return;
            <?php } ?>

            document.getElementById('modalBairroTitle').textContent = 'Cadastrar Cota';
            document.getElementById('inpBairroId').value = '';
            document.getElementById('inpBairroNome').value = '';
            document.getElementById('bairroForm').action = '../controllers/controller_bairro.php';
            openModal('modalBairro');
        }

        function openAddBairroModal() {
            const addForm = document.getElementById('addBairroForm');
            if (addForm) {
                addForm.reset();
                const quantidadeHidden = document.getElementById('addBairroQuantidade');
                if (quantidadeHidden && quantidadeHidden.defaultValue) {
                    quantidadeHidden.value = quantidadeHidden.defaultValue;
                }
            }
            openModal('modalAddBairro');
        }

        function openEditVagasModal() {
            const vagasInput = document.getElementById('editVagasQuantidade');
            if (vagasInput) {
                vagasInput.value = '<?= htmlspecialchars($vagas["quantidade_alunos"] ?? "", ENT_QUOTES, "UTF-8") ?>';
            }
            openModal('modalEditVagas');
        }

        function openEditBairro(id, nome) {
            document.getElementById('editBairroId').value = id || '';
            document.getElementById('editBairroNome').value = nome || '';
            openModal('modalEditBairro');
        }

        function openDeleteBairro(id, nome) {
            document.getElementById('deleteBairroName').textContent = nome || '';
            document.getElementById('deleteBairroId').value = id || '';
            openModal('modalDeleteBairro');
        }

        function openReviewModal() {
            // Validar o formulário antes de prosseguir
            const numAlunos = document.getElementById('inpBairroNome');
            if (!numAlunos || !numAlunos.value.trim()) {
                alert('Informe o número de alunos por turma.');
                return;
            }

            // Verificar se pelo menos um bairro foi adicionado
            const bairrosInputs = document.querySelectorAll('input[name="bairros[]"]');
            let bairrosValidos = false;

            for (let input of bairrosInputs) {
                if (input.value.trim()) {
                    bairrosValidos = true;
                    break;
                }
            }

            if (!bairrosValidos) {
                alert('Informe pelo menos um bairro para a cota.');
                return;
            }

            // Armazenar os valores que precisamos antes de fechar o modal
            const alunosPorTurma = parseInt(numAlunos.value);
            const bairrosArray = [];

            bairrosInputs.forEach(input => {
                if (input.value.trim()) {
                    bairrosArray.push(input.value.trim());
                }
            });

            // Calcular a distribuição de vagas via JavaScript
            const pcdFixed = 2;
            const remaining = alunosPorTurma - pcdFixed;
            const totalPublica = Math.round(remaining * 0.8);
            const totalPrivada = Math.round(remaining * 0.2);
            const publicaCotas = Math.round(totalPublica * 0.3);
            const privadaCotas = Math.round(totalPrivada * 0.3);
            const publicaAc = Math.round(totalPublica * 0.7);
            const privadaAc = Math.round(totalPrivada * 0.7);

            // Fechar o modal atual
            closeModal('modalBairro');

            // Abrir o modal de revisão após um pequeno delay
            setTimeout(() => {
                // Abrir o modal de revisão
                const modalReview = document.getElementById('modalReview');
                if (!modalReview) {
                    console.error("Modal de revisão não encontrado!");
                    return;
                }

                modalReview.classList.remove('hidden');
                modalReview.classList.add('flex');

                setTimeout(() => {
                    const content = modalReview.querySelector('#modalReviewContent');
                    if (content) {
                        content.style.transform = 'scale(1)';
                        content.style.opacity = '1';
                    }

                    // Agora que o modal está aberto, podemos acessar seus elementos
                    try {
                        // Preencher o número de alunos por turma
                        const reviewAlunos = document.getElementById('review-alunos');
                        if (reviewAlunos) {
                            reviewAlunos.textContent = alunosPorTurma;
                        }

                        // Preencher a distribuição de vagas com verificação de elementos
                        const reviewPcd = document.getElementById('review-pcd');
                        const reviewTotal = document.getElementById('review-total');
                        const reviewPublicaTotal = document.getElementById('review-publica-total');
                        const reviewPublicaAc = document.getElementById('review-publica-ac');
                        const reviewPublicaCota = document.getElementById('review-publica-cota');
                        const reviewPrivadaTotal = document.getElementById('review-privada-total');
                        const reviewPrivadaAc = document.getElementById('review-privada-ac');
                        const reviewPrivadaCota = document.getElementById('review-privada-cota');

                        if (reviewPcd) reviewPcd.textContent = pcdFixed;
                        if (reviewTotal) reviewTotal.textContent = alunosPorTurma;
                        if (reviewPublicaTotal) reviewPublicaTotal.textContent = totalPublica;
                        if (reviewPublicaAc) reviewPublicaAc.textContent = publicaAc;
                        if (reviewPublicaCota) reviewPublicaCota.textContent = publicaCotas;
                        if (reviewPrivadaTotal) reviewPrivadaTotal.textContent = totalPrivada;
                        if (reviewPrivadaAc) reviewPrivadaAc.textContent = privadaAc;
                        if (reviewPrivadaCota) reviewPrivadaCota.textContent = privadaCotas;

                        // Debug: mostrar os cálculos no console
                        console.log('Cálculos do Modal de Revisão:');
                        console.log('Alunos por turma:', alunosPorTurma);
                        console.log('PCD:', pcdFixed);
                        console.log('Restante:', remaining);
                        console.log('Pública Total:', totalPublica);
                        console.log('Privada Total:', totalPrivada);
                        console.log('Pública AC:', publicaAc);
                        console.log('Pública Cotas:', publicaCotas);
                        console.log('Privada AC:', privadaAc);
                        console.log('Privada Cotas:', privadaCotas);

                        // Debug: verificar se os elementos existem
                        console.log('Elementos encontrados:');
                        console.log('review-pcd:', !!reviewPcd);
                        console.log('review-total:', !!reviewTotal);
                        console.log('review-publica-ac:', !!reviewPublicaAc);
                        console.log('review-publica-cota:', !!reviewPublicaCota);
                        console.log('review-privada-ac:', !!reviewPrivadaAc);
                        console.log('review-privada-cota:', !!reviewPrivadaCota);

                        // Preencher a lista de bairros
                        const bairrosList = document.getElementById('review-bairros');
                        if (bairrosList) {
                            bairrosList.innerHTML = ''; // Limpar lista atual

                            bairrosArray.forEach(bairro => {
                                const li = document.createElement('li');
                                li.textContent = bairro;
                                li.className = 'text-gray-800';
                                bairrosList.appendChild(li);
                            });
                        }
                    } catch (error) {
                        console.error("Erro ao preencher o modal de revisão:", error);
                    }
                }, 10);
            }, 300);
        }

        // Submissão simples com validação mínima
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('bairroForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const nome = document.getElementById('inpBairroNome');
                    if (!nome.value.trim()) {
                        e.preventDefault();
                        alert('Informe o número de alunos por turma.');
                    }
                });
            }

            // Initialize bairro fields buttons (make + appear only on last input)
            try {
                renderBairroButtons();
            } catch (err) {
                console.error('Falha ao renderizar botões de bairros:', err);
            }
        });

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
                setTimeout(() => {
                    const content = modal.querySelector('[id$="Content"]');
                    if (content) {
                        content.style.transform = 'scale(1)';
                        content.style.opacity = '1';
                    }
                }, 10);
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                const content = modal.querySelector('[id$="Content"]');
                if (content) {
                    content.style.transform = 'scale(0.95)';
                    content.style.opacity = '0';
                }
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.style.overflow = 'auto';
                }, 300);
            }
        }

        // Helper to create an input field element (without action buttons)
        function createBairroField(value = '') {
            const wrapper = document.createElement('div');
            wrapper.className = 'flex items-center bairro-field';

            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'bairros[]';
            input.required = true;
            input.placeholder = 'Digite o nome do bairro';
            input.className = 'flex-1 px-4 py-3.5 rounded-xl transition-all text-base border-2 focus:border-primary focus:ring-4 focus:ring-primary/10';
            input.value = value;

            wrapper.appendChild(input);
            return wrapper;
        }

        // Renders action buttons: '+' on last field, 'x' on previous fields
        function renderBairroButtons() {
            const container = document.getElementById('bairros-container');
            const fields = Array.from(container.querySelectorAll('.bairro-field'));

            // Remove existing trailing buttons to avoid duplicates
            fields.forEach(f => {
                const existingBtn = f.querySelector('.bairro-action');
                if (existingBtn) existingBtn.remove();
            });

            fields.forEach((field, idx) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'ml-2 p-2 rounded-lg bairro-action transition-all';

                if (idx === fields.length - 1) {
                    // Last field: show + button (add)
                    btn.classList.add('bg-primary', 'text-white');
                    btn.title = 'Adicionar campo';
                    btn.innerHTML = `
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>`;
                    btn.onclick = () => {
                        addBairroField();
                    };
                } else {
                    // Previous fields: show x button (remove)
                    btn.classList.add('bg-secondary', 'text-white');
                    btn.title = 'Remover campo';
                    btn.innerHTML = `
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>`;
                    btn.onclick = () => {
                        removeBairroField(field);
                    };
                }

                field.appendChild(btn);
            });
        }

        function addBairroField(value = '') {
            const container = document.getElementById('bairros-container');
            const newField = createBairroField(value);
            container.appendChild(newField);
            renderBairroButtons();
            // focus the new input
            const input = newField.querySelector('input[name="bairros[]"]');
            if (input) input.focus();
        }

        function removeBairroField(fieldElement) {
            const container = document.getElementById('bairros-container');
            // Remove the field
            fieldElement.remove();
            // Ensure at least one field remains
            const remaining = container.querySelectorAll('.bairro-field');
            if (remaining.length === 0) {
                // add one empty field
                addBairroField('');
            } else {
                renderBairroButtons();
            }
        }


        // Feedback by GET flags
        (function() {
            const params = new URLSearchParams(window.location.search);
            if (!params.toString()) return;
            const entidade = 'Bairro';
            let title = '';
            let message = '';
            let type = 'info';
            if (params.has('criado')) {
                title = `${entidade} cadastrado com sucesso`;
                message = '';
                type = 'success';
            } else if (params.has('editado')) {
                title = `${entidade} editado com sucesso`;
                message = '';
                type = 'success';
            } else if (params.has('excluido')) {
                title = `${entidade} excluído com sucesso`;
                message = '';
                type = 'success';
            } else if (params.has('ja_existe')) {
                title = `${entidade} já existe`;
                message = '';
                type = 'warning';
            } else if (params.has('nao_existe')) {
                title = `${entidade} não encontrado`;
                message = '';
                type = 'warning';
            } else if (params.has('erro') || params.has('falha')) {
                title = `Erro ao processar ${entidade.toLowerCase()}`;
                message = '';
                type = 'error';
            } else if (params.has('vagas_editadas')) {
                title = 'Vagas atualizadas com sucesso';
                message = 'A quantidade de alunos por turma foi atualizada.';
                type = 'success';
            } else if (params.has('curso_obrigatorio')) {
                title = 'Curso obrigatório';
                message = 'É necessário cadastrar pelo menos um curso antes de criar cotas.';
                type = 'error';
            } else {
                return;
            }
            const icon = document.getElementById('modalFeedbackIcon');
            const titleEl = document.getElementById('modalFeedbackTitle');
            const msgEl = document.getElementById('modalFeedbackMsg');
            icon.className = 'w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 ' + (type === 'success' ? 'bg-green-100' : type === 'error' ? 'bg-red-100' : 'bg-yellow-100');
            icon.innerHTML = type === 'success' ?
                '<svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' :
                type === 'error' ?
                '<svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M4.93 4.93l14.14 14.14"></path></svg>' :
                '<svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M9.93 4.93l-7 12.12A2 2 0 004.76 21h14.48a2 2 0 001.83-2.95l-7-12.12a2 2 0 00-3.54 0z"></path></svg>';
            titleEl.textContent = title;
            msgEl.textContent = message;
            openModal('modalFeedback');
        })();

        // Controle do select de cursos baseado no tipo de relatório
        document.addEventListener('DOMContentLoaded', function() {
            const tipoRelatorio = document.getElementById('tipo_relatorio');
            const cursoSelect = document.getElementById('curso_id');
            const labelCurso = document.getElementById('label_curso');

            if (tipoRelatorio && cursoSelect && labelCurso) {
                tipoRelatorio.addEventListener('change', function() {
                    if (this.value === 'comissao_selecao') {
                        // Desabilitar e limpar o select de cursos
                        cursoSelect.disabled = true;
                        cursoSelect.value = '';
                        cursoSelect.classList.add('input-disabled');
                        labelCurso.textContent = 'Curso (Não aplicável)';
                        labelCurso.classList.add('text-gray-400');
                    } else {
                        // Habilitar o select de cursos
                        cursoSelect.disabled = false;
                        cursoSelect.classList.remove('input-disabled');
                        labelCurso.textContent = 'Curso (Obrigatório)';
                        labelCurso.classList.remove('text-gray-400');
                        labelCurso.classList.add('text-red-600');
                    }
                });
            }

            // Validação do formulário de edição de bairro
            const editVagasForm = document.getElementById('editVagasForm');
            if (editVagasForm) {
                editVagasForm.addEventListener('submit', function(e) {
                    const total = document.getElementById('editVagasQuantidade');
                    if (!total.value.trim()) {
                        e.preventDefault();
                        alert('Informe o total de alunos por turma.');
                        total.focus();
                    }
                });
            }

            const addBairroForm = document.getElementById('addBairroForm');
            if (addBairroForm) {
                addBairroForm.addEventListener('submit', function(e) {
                    const nomeNovoBairro = document.getElementById('addBairroNome');
                    if (!nomeNovoBairro.value.trim()) {
                        e.preventDefault();
                        alert('Por favor, informe o nome do novo bairro.');
                        nomeNovoBairro.focus();
                    }
                });
            }

            const editBairroForm = document.getElementById('editBairroForm');
            if (editBairroForm) {
                editBairroForm.addEventListener('submit', function(e) {
                    const nomeBairro = document.getElementById('editBairroNome');
                    if (!nomeBairro.value.trim()) {
                        e.preventDefault();
                        alert('Por favor, informe o nome do bairro.');
                        nomeBairro.focus();
                    }
                });
            }
        });
    </script>
</body>

</html>