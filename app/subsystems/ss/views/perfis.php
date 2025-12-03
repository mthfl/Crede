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
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Usuários</title>
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
                    spacing: {
                        '18': '4.5rem',
                        '88': '22rem',
                    },
                    animation: {
                        'slide-in-left': 'slideInLeft 0.5s ease-out',
                        'slide-in-right': 'slideInRight 0.5s ease-out',
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
        }

        .overlay {
            z-index: 45;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 100vw;
            max-width: 20rem;
            z-index: 50;
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

        .focus-ring:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="number"]:focus,
        input[type="color"]:focus,
        select:focus,
        textarea:focus,
        button:focus,
        .btn-animate:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
            border-color: var(--primary);
        }

        input[type="checkbox"]:focus,
        input[type="radio"]:focus {
            outline: 2px solid var(--secondary);
            outline-offset: 2px;
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
                            <a href="usuario.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring bg-white/10">
                                <div class="w-12 h-12 bg-secondary rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
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
                <?php $dados = $select->select_perfis(); ?>
                <?php $tiposUsuarios = method_exists($select, 'select_tipos_usuarios') ? $select->select_tipos_usuarios() : []; ?>
                <?php if (count($dados) === 0) { ?>
                    <div class="bg-gradient-to-br from-accent via-white to-accent/50 border-2 border-dashed border-primary/30 rounded-2xl sm:rounded-3xl p-6 sm:p-8 lg:p-12 text-center animate-fade-in-up max-w-2xl mx-auto">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 lg:w-32 lg:h-32 bg-gradient-to-br from-primary/10 to-secondary/10 rounded-full flex items-center justify-center mx-auto mb-6 sm:mb-8 animate-pulse-soft">
                            <svg class="w-10 h-10 sm:w-12 sm:h-12 lg:w-16 lg:h-16 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-primary mb-3 sm:mb-4 font-display">Nenhum perfil cadastrado</h3>
                        <p class="text-gray-600 text-sm sm:text-base lg:text-lg mb-6 sm:mb-8 max-w-md mx-auto leading-relaxed px-4">Comece adicionando perfis para gerenciar o acesso do sistema.</p>
                        <button onclick="openPerfilForm()" class="bg-gradient-to-r from-primary to-dark text-white px-6 sm:px-8 lg:px-10 py-3 sm:py-4 rounded-xl sm:rounded-2xl hover:from-dark hover:to-primary btn-animate font-semibold shadow-xl focus-ring">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Adicionar primeiro perfil
                            </span>
                        </button>
                    </div>
                <?php } else { ?>
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <a href="usuario.php" class="inline-flex items-center bg-gradient-to-r from-primary to-dark text-white px-6 py-3 rounded-xl hover:from-dark hover:to-primary btn-animate font-semibold shadow-xl focus-ring">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg> 
                                Voltar
                            </a>
                            <button onclick="openPerfilForm()" class="inline-flex items-center bg-gradient-to-r from-primary to-dark text-white px-6 py-3 rounded-xl hover:from-dark hover:to-primary btn-animate font-semibold shadow-xl focus-ring">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Adicionar
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
                        <?php foreach ($dados as $index => $dado) { ?>
                            <article class="grid-item card-hover bg-white rounded-2xl shadow-xl border-0 overflow-hidden group relative" data-nome="<?= htmlspecialchars($dado['nome_perfil'] ?? ($dado['nome'] ?? '')) ?>">
                                <div class="h-2 w-full bg-gradient-to-r from-primary to-secondary"></div>
                                <div class="p-8">
                                    <div class="text-center mb-8">
                                        <div class="w-16 h-16 bg-gradient-to-br from-primary to-dark rounded-full flex items-center justify-center mx-auto mb-4">
                                            <span class="text-white font-bold text-xl"><?= strtoupper(substr(($dado['nome_perfil'] ?? ($dado['nome'] ?? 'P')), 0, 1)) ?></span>
                                        </div>
                                        <h3 class="text-xl font-bold leading-tight font-display group-hover:scale-105 transition-all duration-300 text-primary"><?= htmlspecialchars($dado['nome_perfil'] ?? ($dado['nome'] ?? 'Perfil')) ?></h3>
                                        <div class="w-16 h-0.5 mx-auto mt-3 rounded-full bg-primary/40"></div>
                                    </div>
                                    <div class="space-y-3 mb-6">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <span class="font-medium">ID</span>
                                            <span class="ml-2"><?= htmlspecialchars($dado['id'] ?? '') ?></span>
                                        </div>
                                        
                                    </div>
                                    <div class="flex space-x-2">
                                        <button onclick="openEditPerfil(<?= (int)($dado['id'] ?? 0) ?>, '<?= htmlspecialchars($dado['nome_perfil'] ?? ($dado['nome'] ?? '')) ?>')" class="flex-1 bg-primary text-white py-2 px-4 rounded-lg hover:bg-dark transition-all duration-300 font-medium text-sm btn-animate focus-ring">
                                            <span class="flex items-center justify-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Editar
                                            </span>
                                        </button>
                                        <button name="excluir" onclick="openDeletePerfil(<?= (int)($dado['id'] ?? 0) ?>, '<?= htmlspecialchars($dado['nome_perfil'] ?? ($dado['nome'] ?? 'Perfil')) ?>')" class="flex-1 bg-secondary text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition-all duration-300 font-medium text-sm btn-animate focus-ring">
                                            <span class="flex items-center justify-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Excluir
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </article>
                        <?php } ?>
                    </div>
                <?php } ?>
            </main>
        </div>
    </div>

    <!-- Modal Cadastrar/Editar Perfil -->
    <div id="modalPerfil" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-40">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="modalPerfilContent">
            <div class="p-6 sm:p-8 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary to-dark text-white flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h3 id="modalPerfilTitle" class="text-xl sm:text-2xl font-bold text-dark font-heading">Cadastrar</h3>
                        <p class="text-gray-600 text-sm">Defina o nome e descrição do perfil</p>
                    </div>
                </div>
                <button class="absolute top-6 right-6 p-2 rounded-xl hover:bg-gray-100 transition-all group" onclick="closeModal('modalPerfil')">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 group-hover:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 sm:p-8">
                <form id="perfilForm" action="../controllers/controller_usuario.php" method="POST">
                    <input type="hidden" id="inpPerfilId" name="id_perfil" value="">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-dark mb-3">Nome *</label>
                            <input id="inpPerfilNome" name="nome_perfil" type="text" class="w-full px-4 py-4 rounded-xl border-2 focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="Ex.: Administrador" required>
                        </div>
                    </div>
                    <div class="p-6 sm:p-8 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 mt-6">
                        <button type="button" class="px-6 py-3 rounded-xl border-2 border-primary font-semibold text-primary hover:bg-primary/10 transition-all text-base focus-ring" onclick="closeModal('modalPerfil')">Cancelar</button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-primary to-dark text-white font-semibold rounded-xl hover:from-primary/90 hover:to-dark/90 transition-all text-base shadow-lg">Salvar Perfil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Excluir Perfil -->
    <div id="modalDeletePerfil" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modalDeletePerfilContent">
            <div class="p-6 sm:p-8 text-center">
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M4.93 4.93l14.14 14.14"/></svg>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-dark font-heading mb-4">Excluir Perfil</h3>
                <p class="text-gray-600 text-base mb-6 leading-relaxed">Tem certeza que deseja excluir o perfil <span id="deletePerfilName" class="font-semibold text-dark"></span>?</p>
                <form id="deletePerfilForm" action="../controllers/controller_usuario.php" method="POST">
                    <input type="hidden" name="form" value="perfil">
                    <input type="hidden" id="deletePerfilId" name="id_perfil" value="">
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button type="button" class="px-6 py-3 rounded-xl border-2 border-primary font-semibold text-primary hover:bg-primary/10 transition-all text-base focus-ring" onclick="closeModal('modalDeletePerfil')">Cancelar</button>
                        <button type="submit" name="excluir" value="1" class="px-6 py-3 bg-gradient-to-r from-secondary to-orange-600 text-white font-semibold rounded-xl hover:from-secondary/90 hover:to-orange-700 transition-all text-base shadow-lg">Excluir</button>
                    </div>
                </form>
            </div>
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


    <!-- Modal de Confirmação de Exclusão -->
    <div id="modalDeleteUser" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modalDeleteUserContent">
            <div class="p-6 sm:p-8 text-center">
                <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-exclamation-triangle text-3xl text-red-500"></i>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-dark font-heading mb-4">Confirmar Exclusão</h3>
                <p class="text-gray-600 text-base mb-6 leading-relaxed">
                    Tem certeza que deseja excluir o usuário <span class="font-semibold text-dark" id="deleteUserName"></span>?
                </p>
                <p class="text-sm text-red-600 bg-red-50 px-4 py-3 rounded-lg border border-red-200 mb-6">
                    <i class="fa-solid fa-info-circle mr-2"></i>
                    Esta ação não pode be desfeita.
                </p>
                <form id="deleteForm" action="../controllers/controller_usuario.php" method="POST">
                    <input type="hidden" name="form" value="usuario">
                    <input type="hidden" id="deleteUserId" name="id_usuario" value="">
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button type="button" class="px-6 py-3 rounded-xl border-2 border-primary font-semibold text-primary hover:bg-primary/10 hover:border-primary transition-all text-base focus-ring" onclick="closeModal('modalDeleteUser')">
                            <i class="fa-solid fa-times mr-2"></i>Cancelar
                        </button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-secondary to-orange-600 text-white font-semibold rounded-xl hover:from-secondary/90 hover:to-orange-700 transition-all text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus-ring">
                            <i class="fa-solid fa-trash mr-2"></i>Excluir Usuário
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
                            <?php 
                            $cursos = $select->select_cursos();
                            foreach ($cursos as $curso) { ?>
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
        const usuarios = <?php echo json_encode($dados ?? []); ?>;

        // Sidebar toggle functionality
        openSidebar.addEventListener('click', () => {
            sidebar.classList.add('open');
            overlay.classList.add('show');
        });

        closeSidebar.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });


        function openUserForm() {
            document.getElementById('modalTitle').textContent = 'Cadastrar Usuário';
            document.getElementById('inpUserId').value = '';
            document.getElementById('inpNome').value = '';
            document.getElementById('inpEmail').value = '';
            document.getElementById('inpCpf').value = '';
            document.getElementById('inpSetor').value = '';
            document.getElementById('userForm').action = '../controllers/controller_usuario.php';
            openModal('modalUser');
        }

        // Perfis: abrir modal criar
        function openPerfilForm() {
            document.getElementById('modalPerfilTitle').textContent = 'Cadastrar';
            document.getElementById('inpPerfilId').value = '';
            document.getElementById('inpPerfilNome').value = '';
            document.getElementById('perfilForm').action = '../controllers/controller_usuario.php';
            openModal('modalPerfil');
        }

        // Perfis: abrir modal editar
        function openEditPerfil(id, nome) {
            document.getElementById('modalPerfilTitle').textContent = 'Editar Perfil';
            document.getElementById('inpPerfilId').value = id || '';
            document.getElementById('inpPerfilNome').value = nome || '';
            document.getElementById('perfilForm').action = '../controllers/controller_usuario.php';
            openModal('modalPerfil');
        }

        // Perfis: excluir modal
        function openDeletePerfil(id, nome) {
            document.getElementById('deletePerfilId').value = id || '';
            document.getElementById('deletePerfilName').textContent = nome || '';
            openModal('modalDeletePerfil');
        }

        function openUserTypeForm() {
            document.getElementById('inpNomeTipo').value = '';
            openModal('modalTipoUsuario');
        }

        function openEditUser(userId) {
            const user = usuarios.find(u => parseInt(u.id, 10) === parseInt(userId, 10));
            if (user) {
                document.getElementById('modalTitle').textContent = 'Editar Usuário';
                document.getElementById('inpUserId').value = user.id || '';
                document.getElementById('inpNome').value = user.nome_user || '';
                document.getElementById('inpEmail').value = user.email || '';
                document.getElementById('inpCpf').value = formatarCPF(user.cpf || '');
                const selectTipo = document.getElementById('inpSetor');
                const tipoValor = user.tipo_usuario || '';
                let hasOption = false;
                Array.from(selectTipo.options).forEach(opt => { if (opt.value === tipoValor) hasOption = true; });
                if (!hasOption && tipoValor !== '') {
                    const opt = new Option(tipoValor, tipoValor, true, true);
                    selectTipo.add(opt);
                }
                selectTipo.value = tipoValor;
                document.getElementById('userForm').action = '../controllers/controller_usuario.php';
                openModal('modalUser');
            }
        }

        function openDeleteUser(userId, userName) {
            document.getElementById('deleteUserName').textContent = userName;
            document.getElementById('deleteUserId').value = userId;
            openModal('modalDeleteUser');
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

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-xl shadow-lg transform transition-all duration-300 translate-x-full ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="fa-solid ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                    <span class="font-medium">${message}</span>
                </div>
            `;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 10);
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        // Função para formatar CPF
        function formatarCPF(cpf) {
            let value = cpf.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            if (value.length > 0) {
                if (value.length <= 3) {
                    value = value;
                } else if (value.length <= 6) {
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                } else if (value.length <= 9) {
                    value = value.replace(/(\d{3})(\d{3})(\d)/, '$1.$2.$3');
                } else {
                    value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
                }
            }
            return value;
        }

        // Função para remover máscara do CPF
        function removerMascaraCPF(cpf) {
            return cpf.replace(/\D/g, '');
        }

        

        // Aplicar máscara de CPF
        function aplicarMascaraCPF(input) {
            input.value = formatarCPF(input.value);
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            const cpfInput = document.getElementById('inpCpf');
            if (cpfInput) {
                // Aplicar máscara em tempo real
                cpfInput.addEventListener('input', function() {
                    aplicarMascaraCPF(this);
                });

                // Bloquear caracteres não numéricos
                cpfInput.addEventListener('keypress', function(e) {
                    if (!/[0-9]/.test(e.key)) {
                        e.preventDefault();
                    }
                    if (removerMascaraCPF(this.value).length >= 11 && e.key !== 'Backspace' && e.key !== 'Delete') {
                        e.preventDefault();
                    }
                });

                // Tratar colagem
                cpfInput.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const text = (e.clipboardData || window.clipboardData).getData('text');
                    const digits = text.replace(/\D/g, '').slice(0, 11);
                    this.value = formatarCPF(digits);
                });

                // Aplicar máscara ao ganhar/perder foco
                cpfInput.addEventListener('focus', function() {
                    aplicarMascaraCPF(this);
                });
                cpfInput.addEventListener('blur', function() {
                    if (this.value) {
                        aplicarMascaraCPF(this);
                    }
                });
            }

            // Envio do formulário de usuário (somente com máscara e preenchimento obrigatório)
            document.getElementById('userForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const nomeEl = document.getElementById('inpNome');
                const emailEl = document.getElementById('inpEmail');
                const cpfEl = document.getElementById('inpCpf');
                const tipoEl = document.getElementById('inpSetor');

                if (!nomeEl.value.trim() || !emailEl.value.trim() || !cpfEl.value.trim() || !tipoEl.value) {
                    showNotification('Preencha todos os campos obrigatórios.', 'error');
                    return;
                }
                // Mantém a máscara; envio do valor exatamente como digitado
                this.submit();
            });

            // Validação do formulário de tipo de usuário
            document.getElementById('userTypeForm').addEventListener('submit', function(e) {
                if (!this.inpNomeTipo.value.trim()) {
                    e.preventDefault();
                    showNotification('Digite o nome do tipo de usuário.', 'error');
                }
            });

            // Smooth scroll
            document.documentElement.style.scrollBehavior = 'smooth';

            // Fechar modais com tecla Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const openModals = document.querySelectorAll('[id^="modal"]:not(.hidden)');
                    openModals.forEach(modal => {
                        if (!modal.classList.contains('hidden')) {
                            closeModal(modal.id);
                        }
                    });
                }
            });
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
    </script>

    <script>
        // Feedback by GET flags
        (function() {
            const params = new URLSearchParams(window.location.search);
            if (!params.toString()) return;
            const entidade = 'Usuário';
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
            } else {
                return;
            }
            const icon = document.getElementById('modalFeedbackIcon');
            const titleEl = document.getElementById('modalFeedbackTitle');
            const msgEl = document.getElementById('modalFeedbackMsg');
            icon.className = 'w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 ' + (type === 'success' ? 'bg-green-100' : type === 'error' ? 'bg-red-100' : 'bg-yellow-100');
            icon.innerHTML = type === 'success'
                ? '<svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
                : type === 'error'
                ? '<svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M4.93 4.93l14.14 14.14"></path></svg>'
                : '<svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M9.93 4.93l-7 12.12A2 2 0 004.76 21h14.48a2 2 0 001.83-2.95l-7-12.12a2 2 0 00-3.54 0z"></path></svg>';
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
        });
    </script>
</body>

</html>
