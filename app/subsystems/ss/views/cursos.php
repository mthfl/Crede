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


$step = 'email';
$postedEmail = '';

if (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) && !empty($_POST['email']) && !isset($_POST['senha'])) {
    $step = 'code';
}

if (isset($_POST['senha']) && !empty($_POST['senha']) && isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['id_curso']) && !empty($_POST['id_curso'])) {
    $senha = $_POST['senha'];
    $email = $_POST['email'];
    $id_curso = $_POST['id_curso'];
    require_once(__DIR__ . '/../models/model.admin.php');
    $admin = new admin($escola);
    $result = $admin->verificar_senha($email, $senha, $id_curso);

    switch ($result) {
        case 1:
            header("Location: cursos.php?excluido");
            exit();
        case 2:
            header("Location: cursos.php?erro_senha");
            exit();
        case 3:
            header("Location: cursos.php?erro_senha");
            exit();
        default:
            header("Location: cursos.php?fatal");
            exit();
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Cursos</title>
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

        .focus-ring:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        input[type="text"]:focus,
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

                    <!-- Cursos -->
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.2s;">
                            <a href="cursos.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring bg-white/10">
                                <div class="w-12 h-12 bg-secondary rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
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
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'cadastrador') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.3s;">
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

                    <!-- Cotas -->
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.35s;">
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



                    <!-- Usuários -->
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.4s;">
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

                    <!-- Relatórios -->
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

                    <!--Resultados-->
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.5s;">
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

                    <!-- Requisição de cadastro -->
                    <?php if (isset($_SESSION['tipo_usuario']) && ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'cadastrador')) { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.6s;">
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

                    <!-- Limpar Banco -->
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

                <?php $cursos = $select->select_cursos(); ?>
                <?php if (count($cursos) === 0) { ?>
                    <div class="bg-white border border-gray-200 rounded-2xl p-8 text-center max-w-2xl mx-auto">
                        <h3 class="text-2xl font-bold text-primary mb-2 font-display">Nenhum curso cadastrado</h3>
                        <p class="text-gray-600 mb-6">Crie o primeiro curso para começar a gerenciar.</p>
                        <button onclick="openCreateModal()" class="inline-flex items-center bg-gradient-to-r from-primary to-dark text-white px-6 py-3 rounded-xl hover:from-dark hover:to-primary btn-animate font-semibold shadow-xl focus-ring">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Criar novo curso
                        </button>
                    </div>
                <?php } else { ?>
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex space-x-3">
                            <button onclick="openCreateModal()" class="inline-flex items-center bg-gradient-to-r from-primary to-dark text-white px-6 py-3 rounded-xl hover:from-dark hover:to-primary btn-animate font-semibold shadow-xl focus-ring">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Criar novo curso
                            </button>

                        </div>
                    </div>
                    <div id="gridCursos" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
                        <?php foreach ($cursos as $index => $curso) { ?>
                            <?php if (isset($_GET['candidato_associado']) && $step === 'email') { ?>
                                <!-- Modal de Autenticação de Dois Fatores - Email -->
                                <div class="fixed inset-0 bg-black/20 backdrop-blur-sm z-50 flex items-center justify-center p-4 animate-fadeIn">
                                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md border border-gray-200/50 transform transition-all duration-300 animate-scaleIn">
                                        <div class="text-white p-6 rounded-t-2xl relative overflow-hidden" style="background: linear-gradient(135deg, #DC2626, #991B1B);">
                                            <div class="absolute inset-0 bg-gradient-to-br from-black/20 to-transparent"></div>
                                            <div class="relative flex justify-between items-center">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/30 shadow-lg transform hover:rotate-12 transition-transform duration-300">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h2 class="text-lg sm:text-xl font-bold font-display tracking-tight">Autenticação de Dois Fatores</h2>
                                                        <p class="text-white/90 text-sm mt-1 font-medium">Candidato associado detectado</p>
                                                    </div>
                                                </div>
                                                <button type="button" onclick="window.location.href='cursos.php'" class="text-white/80 hover:text-white transition-colors duration-300">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <form action="cursos.php?candidato_associado=1" method="post" class="space-y-4">
                                            <div class="p-6">
                                                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 shadow-sm hover:shadow-md transition-shadow duration-300">
                                                    <div class="flex items-start">
                                                        <div class="flex-shrink-0">
                                                            <svg class="h-6 w-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </div>
                                                        <div class="ml-3">
                                                            <h3 class="text-sm font-medium text-red-800">Atenção!</h3>
                                                            <div class="mt-2 text-sm text-red-700">
                                                                <p>Foi detectado um candidato associado a este curso. Para continuar, é necessária autenticação de dois fatores.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bg-gray-50 p-4 rounded-xl transition-all duration-300 hover:bg-gray-100">
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">E-mail do Administrador</label>
                                                    <div class="relative">
                                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                                            </svg>
                                                        </div>
                                                        <input type="email" name="email" required value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" placeholder="admin@dominio.com" class="w-full pl-10 px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300 text-sm shadow-sm">
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="flex justify-between p-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                                                <a href="cursos.php" class="px-6 py-3 rounded-lg border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-all text-sm shadow-sm hover:shadow-md">Cancelar</a>
                                                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 transition-all duration-300 font-semibold text-sm group shadow-md hover:shadow-lg">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                                        </svg>
                                                        confirmar email
                                                    </span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php } elseif (isset($_GET['candidato_associado']) && $step === 'code') { ?>
                                <!-- Modal de Autenticação de Dois Fatores - Código -->
                                <div class="fixed inset-0 bg-black/20 backdrop-blur-sm z-50 flex items-center justify-center p-4 animate-fadeIn">
                                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md border border-gray-200/50 transform transition-all duration-300 animate-scaleIn">
                                        <div class="text-white p-6 rounded-t-2xl relative overflow-hidden" style="background: linear-gradient(135deg, #DC2626, #991B1B);">
                                            <div class="absolute inset-0 bg-gradient-to-br from-black/20 to-transparent"></div>
                                            <div class="relative flex justify-between items-center">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/30 shadow-lg transform hover:rotate-12 transition-transform duration-300">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h2 class="text-lg sm:text-xl font-bold font-display tracking-tight">Verificar senha</h2>

                                                    </div>
                                                </div>
                                                <button type="button" onclick="window.location.href='cursos.php'" class="text-white/80 hover:text-white transition-colors duration-300">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <form action="cursos.php" method="post" class="space-y-4">
                                            <div class="p-6">
                                                <input type="hidden" name="id_curso" value="<?= $curso['id'] ?>">
                                                <input type="hidden" name="email" value="<?= htmlspecialchars($_SESSION['email']) ?>" />
                                                <div class="bg-gray-50 p-4 rounded-xl transition-all duration-300 hover:bg-gray-100">
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Código de Verificação</label>
                                                    <div class="relative">
                                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                            </svg>
                                                        </div>
                                                        <input type="password" name="senha" required placeholder="senha" class="w-full pl-10 px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300 text-sm tracking-widest text-center shadow-sm">
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-2 flex items-center">
                                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Digite a senha <?= htmlspecialchars($_SESSION['email']) ?>.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between p-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                                                <a href="cursos.php" class="px-6 py-3 rounded-lg border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-all text-sm shadow-sm hover:shadow-md">Cancelar</a>
                                                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 transition-all duration-300 font-semibold text-sm group shadow-md hover:shadow-lg">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Validar senha
                                                    </span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php } ?>
                            <article class="grid-item card-hover bg-white rounded-2xl shadow-xl border-0 overflow-hidden group relative">
                                <div class="h-2 w-full" style="background-color: <?= $curso['cor_curso'] ?>"></div>
                                <div class="p-8">
                                    <div class="text-center mb-8">
                                        <h3 class="text-2xl font-bold leading-tight font-display group-hover:scale-105 transition-all duration-300" style="color: <?= $curso['cor_curso'] ?>"><?= $curso['nome_curso'] ?></h3>
                                        <div class="w-16 h-0.5 mx-auto mt-3 rounded-full" style="background-color: <?= $curso['cor_curso'] ?>40"></div>
                                    </div>

                                    <div class="flex justify-center space-x-2">
                                        <button onclick="editCurso(<?= $curso['id'] ?>, '<?= $curso['nome_curso'] ?>', '<?= $curso['cor_curso'] ?>')" class="w-24 bg-transparent py-2 px-4 rounded-lg transition-all duration-300 font-medium text-sm btn-animate focus-ring hover:bg-gray-50 border" style="border-color: <?= $curso['cor_curso'] ?>; color: <?= $curso['cor_curso'] ?>;">
                                            <span class="flex items-center justify-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Editar
                                            </span>

                                        </button>
                                        <form action="../controllers/controller_curso.php" method="post">
                                            <input type="hidden" name="id_curso" value="<?= $curso['id'] ?>">
                                            <button class="w-24 bg-transparent py-2 px-4 rounded-lg transition-all duration-300 font-medium text-sm btn-animate focus-ring hover:bg-gray-50 border" style="border-color: <?= $curso['cor_curso'] ?>; color: <?= $curso['cor_curso'] ?>;">
                                                <span class="flex items-center justify-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Excluir
                                                </span>
                                            </button>
                                        </form>
                                    </div>
                                    </form>
                                </div>
                            </article>
                        <?php } ?>
                    </div>
                <?php } ?>
            </main>
        </div>
    </div>


    <div id="cursoModal" class="fixed inset-0 bg-black/70 backdrop-blur-md z-50 hidden animate-scale-in">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md border border-gray-200/50 relative">
                <div id="modalHeader" class="text-white p-4 rounded-t-2xl relative overflow-hidden" style="background: linear-gradient(135deg, #005A24, #1A3C34);">
                    <div class="absolute inset-0 bg-gradient-to-br from-black/20 to-transparent"></div>
                    <div class="relative flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/30 shadow-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 id="modalTitle" class="text-lg sm:text-xl font-bold font-display tracking-tight">Adicionar Curso</h2>
                                <p class="text-white/90 text-sm mt-1 font-medium">Preencha os dados do curso</p>
                            </div>
                        </div>
                        <button onclick="closeModal()" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center transition-all duration-300 backdrop-blur-sm group border border-white/30 shadow-lg">
                            <svg class="w-4 h-4 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <form action="../controllers/controller_curso.php" method="post" id="cursoForm" class="space-y-4">
                    <div class="p-6">
                        <input type="hidden" id="cursoId" name="curso_id">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome do Curso *</label>
                            <input type="text" id="nomeCurso" name="nome_curso" required placeholder="Digite o nome do curso" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cor do Curso *</label>
                            <div class="flex items-center space-x-3">
                                <input type="color" id="corCurso" name="cor_curso" value="#005A24" class="w-12 h-10 border border-gray-300 rounded-lg cursor-pointer">
                                <input type="text" id="corCursoText" placeholder="#005A24" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 text-sm">
                            </div>
                        </div>
                    </div>
                    <form action="../controllers/controller_curso.php" method="post">
                        <input type="hidden" name="id_curso" value="<?= $dado['id'] ?>">
                        <div class="flex justify-between p-4 border-t border-gray-200 bg-white">
                            <a href="./cursos.php" class="px-6 py-3 border-2 border-primary text-primary rounded-lg hover:bg-primary/10 transition-all duration-300 font-semibold text-sm group">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Cancelar
                                </span>
                            </a>
                            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-dark transition-all duration-300 font-semibold text-sm group">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span id="saveButtonText">Salvar</span>
                                </span>
                            </button>
                        </div>
                    </form>
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



        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Adicionar Curso';
            document.getElementById('saveButtonText').textContent = 'Salvar';
            document.getElementById('cursoForm').reset();
            document.getElementById('cursoId').value = '';
            document.getElementById('corCurso').value = '#005A24';
            document.getElementById('corCursoText').value = '#005A24';
            document.getElementById('cursoModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function editCurso(id, nome, cor) {
            document.getElementById('modalTitle').textContent = 'Editar Curso';
            document.getElementById('saveButtonText').textContent = 'Atualizar';
            document.getElementById('cursoId').value = id;
            document.getElementById('nomeCurso').value = nome;
            document.getElementById('corCurso').value = cor;
            document.getElementById('corCursoText').value = cor;
            document.getElementById('cursoModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function saveCurso() {
            const form = document.getElementById('cursoForm');
            const formData = new FormData(form);

            const action = document.getElementById('cursoId').value ? 'update' : 'create';
            formData.append('action', action);

            fetch('processar_curso.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(action === 'create' ? 'Curso criado com sucesso!' : 'Curso atualizado com sucesso!');
                        closeModal();
                        location.reload();
                    } else {
                        alert('Erro: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Ocorreu um erro ao processar o curso.');
                });
        }

        document.getElementById('corCurso').addEventListener('input', function() {
            document.getElementById('corCursoText').value = this.value;
        });

        document.getElementById('corCursoText').addEventListener('input', function() {
            if (this.value.match(/^#[0-9A-F]{6}$/i)) {
                document.getElementById('corCurso').value = this.value;
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

        // Feedback by GET flags
        (function() {
            const params = new URLSearchParams(window.location.search);
            if (!params.toString()) return;
            const entidade = 'Curso';
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
            } else if (params.has('erro_codigo')) {
                title = 'Código de verificação inválido';
                message = 'O código informado não confere. Tente novamente.';
                type = 'error';
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
        });
    </script>
</body>

</html>