<?php
require_once(__DIR__ . '/models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/config/connect.php');
$escola = $_SESSION['escola'];

new connect($escola);
require_once(__DIR__ . '/models/model.select.php');
$select = new select($escola);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Dashboard</title>
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

        .loading-shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
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

        .focus-ring:focus {
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
    </style>
</head>

<body class="bg-white min-h-screen font-body">
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
                    <!-- Dashboard -->
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'cadastrador') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.1s;">
                            <a href="index.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring bg-white/10">
                                <div class="w-12 h-12 bg-secondary rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
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
                            <a href="views/cursos.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
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
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'cadastrador') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.3s;">
                            <a href="views/candidatos.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
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
                            <a href="views/cotas.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
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
                            <a href="views/usuario.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
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
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'cadastrador') { ?>
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Resultados</span>
                                    <p class="text-green-200 text-xs mt-1">Gerar documentos</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                    <!-- Limpar Banco -->
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.6s;">
                            <a href="views/limpar_banco.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
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
        <div class="main-content flex-1 bg-white">
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
                            <a href="../main/views/perfil.php" title="Perfil" class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-primary to-dark rounded-full flex items-center justify-center hover:brightness-95 focus-ring">
                                <span class="text-white font-bold text-xs sm:text-sm"><?= strtoupper(substr($_SESSION['nome'] ?? 'U', 0, 1)) ?></span>
                            </a>
                            <a href="../gerenciador_escolas/index.php" class="bg-primary text-white px-3 sm:px-4 md:px-6 py-2 sm:py-3 rounded-xl hover:bg-dark btn-animate font-semibold shadow-lg focus-ring text-xs sm:text-sm">
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
                $dados = $select->select_cursos();
                if (count($dados) > 0) {
                ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
                        <?php foreach ($dados as $index => $dado) { ?>
                            <article class="grid-item card-hover bg-white rounded-2xl shadow-xl border-0 overflow-hidden group relative">
                                <div class="h-2 w-full" style="background-color: <?= $dado['cor_curso'] ?>"></div>
                                <div class="p-8">
                                    <div class="text-center mb-8">
                                        <h3 class="text-2xl font-bold leading-tight font-display group-hover:scale-105 transition-all duration-300" style="color: <?= $dado['cor_curso'] ?>"><?= $dado['nome_curso'] ?></h3>
                                        <div class="w-16 h-0.5 mx-auto mt-3 rounded-full" style="background-color: <?= $dado['cor_curso'] ?>40"></div>
                                    </div>
                                    <div class="space-y-4">
                                        <a href="views/cadastro.php?curso_id=<?= $dado['id'] ?>&curso_nome=<?= urlencode($dado['nome_curso']) ?>&curso_cor=<?= urlencode($dado['cor_curso']) ?>&tipo_escola=publica" class="w-full bg-transparent py-2.5 px-5 rounded-lg hover:bg-gray-50 btn-animate flex items-center justify-center font-medium shadow-sm group/btn focus-ring transition-all duration-300 border hover:border-opacity-80" style="border-color: <?= $dado['cor_curso'] ?>; color: <?= $dado['cor_curso'] ?>">
                                            <div class="w-6 h-6 rounded-md flex items-center justify-center mr-3 group-hover/btn:scale-110 transition-all duration-300" style="background-color: <?= $dado['cor_curso'] ?>15">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: <?= $dado['cor_curso'] ?>">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium">Escola Pública</span>
                                        </a>
                                        <a href="views/cadastro.php?curso_id=<?= $dado['id'] ?>&curso_nome=<?= urlencode($dado['nome_curso']) ?>&curso_cor=<?= urlencode($dado['cor_curso']) ?>&tipo_escola=privada" class="w-full bg-transparent py-2.5 px-5 rounded-lg hover:bg-gray-50 btn-animate flex items-center justify-center font-medium shadow-sm group/btn focus-ring transition-all duration-300 border hover:border-opacity-80" style="border-color: <?= $dado['cor_curso'] ?>; color: <?= $dado['cor_curso'] ?>">
                                            <div class="w-6 h-6 rounded-md flex items-center justify-center mr-3 group-hover/btn:scale-110 transition-all duration-300" style="background-color: <?= $dado['cor_curso'] ?>15">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: <?= $dado['cor_curso'] ?>">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium">Escola Privada</span>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <div class="bg-gradient-to-br from-accent via-white to-accent/50 border-2 border-dashed border-primary/30 rounded-2xl sm:rounded-3xl p-6 sm:p-8 lg:p-12 text-center animate-fade-in-up max-w-2xl mx-auto">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 lg:w-32 lg:h-32 bg-gradient-to-br from-primary/10 to-secondary/10 rounded-full flex items-center justify-center mx-auto mb-6 sm:mb-8 animate-pulse-soft">
                            <svg class="w-10 h-10 sm:w-12 sm:h-12 lg:w-16 lg:h-16 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-primary mb-3 sm:mb-4 font-display">Nenhum curso cadastrado</h3>
                        <p class="text-gray-600 text-sm sm:text-base lg:text-lg mb-6 sm:mb-8 max-w-md mx-auto leading-relaxed px-4">Comece adicionando cursos para gerenciar o sistema educacional de forma eficiente.</p>
                        <button onclick="window.location.href='./views/cursos.php'" class="bg-gradient-to-r from-primary to-dark text-white px-6 sm:px-8 lg:px-10 py-3 sm:py-4 rounded-xl sm:rounded-2xl hover:from-dark hover:to-primary btn-animate font-semibold shadow-xl focus-ring">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Adicionar Primeiro Curso
                            </span>
                        </button>
                    </div>
                <?php } ?>
            </main>
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
                <form action="controllers/controller_relatorios.php" method="POST" class="space-y-6">
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
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Curso (Opcional)</label>
                        <select name="curso_id" class="w-full px-4 py-3.5 border border-gray-300 rounded-xl input-modern focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-base">
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
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="modalResultadosContent">
            <div class="bg-gradient-to-r from-secondary to-orange-600 text-white p-6">
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
                <form action="controllers/controller_relatorios.php" method="POST" class="space-y-6">
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
                        <button type="submit" class="w-full bg-gradient-to-r from-secondary to-orange-600 text-white px-6 py-3.5 rounded-xl hover:from-orange-500 hover:to-orange-700 btn-animate font-semibold shadow-lg focus-ring transition-all text-base">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Consultar Resultados
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- First Modal: Personal Information and 6th-8th Year Grades -->
    <div id="modalCadastro" class="fixed inset-0 bg-black/70 backdrop-blur-md z-50 hidden animate-scale-in">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl h-[90vh] flex flex-col border border-gray-200/50 relative">
                <!-- Modal Header -->
                <div id="headerCadastro" class="text-white p-4 rounded-t-2xl relative overflow-hidden" style="background: linear-gradient(135deg, #005A24, #1A3C34);">
                    <div class="absolute inset-0 bg-gradient-to-br from-black/20 to-transparent"></div>
                    <div class="relative flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/30 shadow-lg">
                            <div id="debugDetails" class="hidden">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg sm:text-xl font-bold font-display tracking-tight">Formulário de <span id="cursoNomeCadastro" class="text-secondary"></span></h2>
                                <p id="tipoEscolaCadastro" class="text-white/90 text-sm mt-1 font-medium"></p>
                            </div>
                        </div>
                        <button onclick="closeModal('modalCadastro')" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center transition-all duration-300 backdrop-blur-sm group border border-white/30 shadow-lg">
                            <svg class="w-4 h-4 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Content -->
                <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                    <form id="cadastroForm" class="space-y-4">
                        <!-- Personal Information -->
                        <div class="bg-white rounded-xl p-4 shadow-md border border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informações Pessoais</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo *</label>
                                    <input type="text" name="nome" required placeholder="Digite o nome completo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Data de Nascimento *</label>
                                    <input type="text" name="data_nascimento" required placeholder="DD/MM/AAAA" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 text-sm" oninput="applyDateMask(this)">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Curso</label>
                                    <input type="text" id="cursoInput" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm text-gray-600">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Bairro *</label>
                                    <select name="bairro" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 text-sm">
                                        <option value="">Selecione um bairro</option>
                                        <option value="1">Centro</option>
                                        <option value="2">Bairro A</option>
                                        <option value="3">Bairro B</option>
                                    </select>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="pcd" id="pcd" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                    <label for="pcd" class="ml-2 text-sm font-medium text-gray-700">PCD</label>
                                </div>
                            </div>
                        </div>

                        <!-- Grades Section -->
                        <div id="gradesSection" class="space-y-4">
                            <div class="bg-white rounded-xl p-4 shadow-md border border-gray-100">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    Notas por Ano
                                </h3>

                                <!-- Subjects Header -->
                                <div class="grid grid-cols-10 gap-2 mb-3">
                                    <div class="text-xs font-medium text-gray-500 text-center"></div>
                                    <div class="text-xs font-medium text-gray-500 text-center">Port.</div>
                                    <div class="text-xs font-medium text-gray-500 text-center">Mat.</div>
                                    <div class="text-xs font-medium text-gray-500 text-center">Hist.</div>
                                    <div class="text-xs font-medium text-gray-500 text-center">Geo.</div>
                                    <div class="text-xs font-medium text-gray-500 text-center">Ciên.</div>
                                    <div class="text-xs font-medium text-gray-500 text-center">Ing.</div>
                                    <div class="text-xs font-medium text-gray-500 text-center">Artes</div>
                                    <div class="text-xs font-medium text-gray-500 text-center">Ed.Fís.</div>
                                    <div class="text-xs font-medium text-gray-500 text-center">Rel.</div>
                                </div>

                                <div class="grid grid-cols-10 gap-2 mb-3 p-3 rounded-lg border">
                                    <div class="flex items-center justify-center">
                                        <div class="w-6 h-6 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <span class="text-gray-600 font-bold text-sm">6º </span>
                                        </div>
                                    </div>
                                    <input type="number" name="portugues_6" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-sm">
                                    <input type="number" name="matematica_6" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-sm">
                                    <input type="number" name="historia_6" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-sm">
                                    <input type="number" name="geografia_6" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-sm">
                                    <input type="number" name="ciencias_6" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-sm">
                                    <input type="number" name="ingles_6" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-sm">
                                    <input type="number" name="artes_6" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-sm">
                                    <input type="number" name="edfisica_6" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-sm">
                                    <input type="number" name="religiao_6" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-sm">
                                </div>

                                <!-- 7th Year Row -->
                                <div class="grid grid-cols-10 gap-2 mb-3 p-3 rounded-lg border">
                                    <div class="flex items-center justify-center">
                                        <div class="w-6 h-6 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <span class="text-gray-600 font-bold text-sm">7º</span>
                                        </div>
                                    </div>
                                    <input type="number" name="portugues_7" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-sm">
                                    <input type="number" name="matematica_7" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-sm">
                                    <input type="number" name="historia_7" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-sm">
                                    <input type="number" name="geografia_7" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-sm">
                                    <input type="number" name="ciencias_7" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-sm">
                                    <input type="number" name="ingles_7" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-sm">
                                    <input type="number" name="artes_7" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-sm">
                                    <input type="number" name="edfisica_7" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-sm">
                                    <input type="number" name="religiao_7" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-sm">
                                </div>

                                <!-- 8th Year Row -->
                                <div class="grid grid-cols-10 gap-2 mb-3 p-3 rounded-lg border">
                                    <div class="flex items-center justify-center">
                                        <div class="w-6 h-6 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <span class="text-gray-600 font-bold text-sm">8º </span>
                                        </div>
                                    </div>
                                    <input type="number" name="portugues_8" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-center text-sm">
                                    <input type="number" name="matematica_8" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-center text-sm">
                                    <input type="number" name="historia_8" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-center text-sm">
                                    <input type="number" name="geografia_8" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-center text-sm">
                                    <input type="number" name="ciencias_8" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-center text-sm">
                                    <input type="number" name="ingles_8" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-center text-sm">
                                    <input type="number" name="artes_8" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-center text-sm">
                                    <input type="number" name="edfisica_8" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-center text-sm">
                                    <input type="number" name="religiao_8" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-center text-sm">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Actions -->
                <div class="flex justify-between p-4 border-t border-gray-200 bg-white">
                    <button onclick="closeModal('modalCadastro')" class="px-6 py-3 border-2 border-primary text-primary rounded-lg hover:bg-primary/10 transition-all duration-300 font-semibold text-sm group">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancelar
                        </span>
                    </button>
                    <button onclick="openNonoAnoModal()" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-dark transition-all duration-300 font-semibold text-sm group">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Próximo
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Modal: 9th Year Grades -->
    <div id="modalNonoAno" class="fixed inset-0 bg-black/70 backdrop-blur-md z-50 hidden animate-scale-in">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl h-[90vh] flex flex-col border border-gray-200/50 relative">
                <!-- Modal Header -->
                <div id="headerNonoAno" class="text-white p-4 rounded-t-2xl relative overflow-hidden" style="background: linear-gradient(135deg, #005A24, #1A3C34);">
                    <div class="absolute inset-0 bg-gradient-to-br from-black/20 to-transparent"></div>
                    <div class="relative flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/30 shadow-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5 freeze-frameH7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01. graus293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg sm:text-xl font-bold font-display tracking-tight">Notas do 9° Ano - <span id="cursoNomeNonoAno" class="text-secondary"></span></h2>
                                <p id="tipoEscolaNonoAno" class="text-white/90 text-sm mt-1 font-medium"></p>
                            </div>
                        </div>
                        <button onclick="closeModal('modalNonoAno')" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center transition-all duration-300 backdrop-blur-sm group border border-white/30 shadow-lg">
                            <svg class="w-4 h-4 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Content -->
                <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                    <form id="nonoAnoForm" class="space-y-4">
                        <div class="bg-white rounded-xl p-4 shadow-md border border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">

                                9° Ano - <span id="tipoEscolaNonoAnoForm"></span>
                            </h3>
                            <!-- Subjects Header -->
                            <div class="grid grid-cols-10 gap-2 mb-3">
                                <div class="text-xs font-medium text-gray-500 text-center"></div>
                                <div class="text-xs font-medium text-gray-500 text-center">Port.</div>
                                <div class="text-xs font-medium text-gray-500 text-center">Mat.</div>
                                <div class="text-xs font-medium text-gray-500 text-center">Hist.</div>
                                <div class="text-xs font-medium text-gray-500 text-center">Geo.</div>
                                <div class="text-xs font-medium text-gray-500 text-center">Ciên.</div>
                                <div class="text-xs font-medium text-gray-500 text-center">Ing.</div>
                                <div class="text-xs font-medium text-gray-500 text-center">Artes</div>
                                <div class="text-xs font-medium text-gray-500 text-center">Ed.Fís.</div>
                                <div class="text-xs font-medium text-gray-500 text-center">Rel.</div>
                            </div>

                            <!-- 1º Bimestre Row -->
                            <div class="grid grid-cols-10 gap-2 mb-3 p-3 rounded-lg border">
                                <div class="flex items-center justify-center">
                                    <div class="w-6 h-6 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <span class="text-gray-600 font-bold text-sm">1º</span>
                                    </div>
                                </div>
                                <input type="number" name="portugues_9_1" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-sm">
                                <input type="number" name="matematica_9_1" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-sm">
                                <input type="number" name="historia_9_1" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-sm">
                                <input type="number" name="geografia_9_1" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-sm">
                                <input type="number" name="ciencias_9_1" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-sm">
                                <input type="number" name="ingles_9_1" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-sm">
                                <input type="number" name="artes_9_1" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-sm">
                                <input type="number" name="edfisica_9_1" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-sm">
                                <input type="number" name="religiao_9_1" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-sm">
                            </div>

                            <!-- 2º Bimestre Row -->
                            <div class="grid grid-cols-10 gap-2 mb-3 p-3 rounded-lg border">
                                <div class="flex items-center justify-center">
                                    <div class="w-6 h-6 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <span class="text-gray-600 font-bold text-sm">2º</span>
                                    </div>
                                </div>
                                <input type="number" name="portugues_9_2" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-sm">
                                <input type="number" name="matematica_9_2" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-sm">
                                <input type="number" name="historia_9_2" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-sm">
                                <input type="number" name="geografia_9_2" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-sm">
                                <input type="number" name="ciencias_9_2" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-sm">
                                <input type="number" name="ingles_9_2" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-sm">
                                <input type="number" name="artes_9_2" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-sm">
                                <input type="number" name="edfisica_9_2" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-sm">
                                <input type="number" name="religiao_9_2" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-sm">
                            </div>

                            <!-- 3º Bimestre Row -->
                            <div class="grid grid-cols-10 gap-2 mb-3 p-3 rounded-lg border">
                                <div class="flex items-center justify-center">
                                    <div class="w-6 h-6 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <span class="text-gray-600 font-bold text-sm">3º</span>
                                    </div>
                                    <input type="number" name="portugues_9_3" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-center text-sm">
                                    <input type="number" name="matematica_9_3" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-center text-sm">
                                    <input type="number" name="historia_9_3" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-center text-sm">
                                    <input type="number" name="geografia_9_3" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-center text-sm">
                                    <input type="number" name="ciencias_9_3" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-center text-sm">
                                    <input type="number" name="ingles_9_3" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-center text-sm">
                                    <input type="number" name="artes_9_3" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-center text-sm">
                                    <input type="number" name="edfisica_9_3" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-center text-sm">
                                    <input type="number" name="religiao_9_3" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-center text-sm">
                                </div>

                                <!-- Média Geral Row -->
                                <div class="grid grid-cols-10 gap-2 mb-3 p-3 rounded-lg border">
                                    <div class="flex items-center justify-center">
                                        <div class="w-6 h-6 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <span class="text-gray-600 font-bold text-sm">MG</span>
                                        </div>
                                    </div>
                                    <input type="number" name="portugues_9_media" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-center text-sm bg-yellow-50 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                    <input type="number" name="matematica_9_media" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-center text-sm bg-yellow-50 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                    <input type="number" name="historia_9_media" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-center text-sm bg-yellow-50 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                    <input type="number" name="geografia_9_media" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-center text-sm bg-yellow-50 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                    <input type="number" name="ciencias_9_media" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-center text-sm bg-yellow-50 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                    <input type="number" name="ingles_9_media" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-center text-sm bg-yellow-50 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                    <input type="number" name="artes_9_media" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-center text-sm bg-yellow-50 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                    <input type="number" name="edfisica_9_media" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-center text-sm bg-yellow-50 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                    <input type="number" name="religiao_9_media" step="0.01" min="0" max="10" placeholder="0.00" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-center text-sm bg-yellow-50 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                </div>
                            </div>
                    </form>
                </div>

                <!-- Modal Actions -->
                <div class="flex justify-between p-4 border-t border-gray-200 bg-white">
                    <button onclick="voltarParaModalCadastro()" class="px-6 py-3 border-2 border-primary text-primary rounded-lg hover:bg-primary/10 transition-all duration-300 font-semibold text-sm group">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Voltar
                        </span>
                    </button>
                    <button onclick="submitForm()" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-dark transition-all duration-300 font-semibold text-sm group">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Cadastrar
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const openSidebar = document.getElementById('openSidebar');
        const closeSidebar = document.getElementById('closeSidebar');
        let currentTipoEscola = 'publica';
        let currentCursoNome = '';
        let currentCorCurso = '#005A24';
        let firstModalData = {}; // Para armazenar os dados do primeiro modal
        let currentCursoId = null; // Para armazenar o ID do curso selecionado

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


        function openModalWithCourse(modalId, cursoNome, corCurso, tipoEscola, cursoId = null) {
            currentTipoEscola = tipoEscola;
            currentCursoNome = cursoNome;
            currentCorCurso = corCurso;
            if (cursoId) currentCursoId = cursoId;

            const modal = document.getElementById(modalId);
            const cursoNomeElement = document.getElementById(modalId === 'modalCadastro' ? 'cursoNomeCadastro' : 'cursoNomeNonoAno');
            const tipoEscolaElement = document.getElementById(modalId === 'modalCadastro' ? 'tipoEscolaCadastro' : 'tipoEscolaNonoAno');
            const tipoEscolaFormElement = document.getElementById('tipoEscolaNonoAnoForm');
            const cursoInput = document.getElementById('cursoInput');
            const header = document.getElementById(modalId === 'modalCadastro' ? 'headerCadastro' : 'headerNonoAno');

            cursoNomeElement.textContent = cursoNome;
            tipoEscolaElement.textContent = tipoEscola === 'publica' ? 'Escola Pública' : 'Escola Privada';
            if (tipoEscolaFormElement) {
                tipoEscolaFormElement.textContent = tipoEscola === 'publica' ? 'Escola Pública' : 'Escola Privada';
            }
            cursoInput.value = cursoNome;
            header.style.background = `linear-gradient(135deg, ${corCurso}, #1A3C34)`;

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Aplicar cores do curso aos fundos das linhas
            setTimeout(() => {
                applyCourseColorsToBackgrounds();
            }, 100);

            // Inicializar campos exclusivos se for o modal do 9º ano
            if (modalId === 'modalNonoAno') {
                // Pequeno delay para garantir que o modal esteja renderizado
                setTimeout(() => {
                    const debugContent = document.getElementById('debugContent');
                    if (debugContent && typeof data !== 'undefined' && data.debug) {
                        debugContent.textContent = data.debug;
                    }
                }, 100);
            }

            // Event listeners para campos de bimestre
            bimestreInputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value.trim() !== '') {
                        disableMediaFields();
                    } else {
                        // Se todos os campos de bimestre estiverem vazios, habilitar médias
                        const allBimestreEmpty = Array.from(bimestreInputs).every(inp => inp.value.trim() === '');
                        if (allBimestreEmpty) {
                            enableAllFields();
                        }
                    }
                });
            });

            // Event listeners para campos de média
            mediaInputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value.trim() !== '') {
                        disableBimestreFields();
                    } else {
                        // Se todos os campos de média estiverem vazios, habilitar bimestres
                        const allMediaEmpty = Array.from(mediaInputs).every(inp => inp.value.trim() === '');
                        if (allMediaEmpty) {
                            enableAllFields();
                        }
                    }
                });
            });
        }

        // Inicializar campos exclusivos quando o modal do 9º ano for aberto
        function initializeExclusiveFields() {
            setupExclusiveFields();
        }

        // Função para converter hex para rgba com transparência
        function hexToRgba(hex, alpha = 0.1) {
            const r = parseInt(hex.slice(1, 3), 16);
            const g = parseInt(hex.slice(3, 5), 16);
            const b = parseInt(hex.slice(5, 7), 16);
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }

        // Função para aplicar cores do banco aos fundos das linhas de notas
        function applyCourseColorsToBackgrounds() {
            if (!currentCorCurso) return;

            // Aplicar cor apenas às linhas que contêm campos de notas (6º, 7º, 8º ano)
            const noteRows = document.querySelectorAll('#cadastroForm .grid');
            noteRows.forEach(row => {
                // Verificar se a linha contém campos de notas (inputs numéricos)
                const hasNoteInputs = row.querySelectorAll('input[type="number"]').length > 0;
                if (hasNoteInputs) {
                    row.style.backgroundColor = hexToRgba(currentCorCurso, 0.05);
                    row.style.borderColor = hexToRgba(currentCorCurso, 0.15);
                }
            });

            // Aplicar cor apenas às linhas que contêm campos de notas do 9º ano
            const nonoAnoNoteRows = document.querySelectorAll('#nonoAnoForm .grid');
            if (nonoAnoNoteRows.length > 0) {
                nonoAnoNoteRows.forEach(row => {
                    // Verificar se a linha contém campos de notas
                    const hasNoteInputs = row.querySelectorAll('input[type="number"]').length > 0;
                    if (hasNoteInputs) {
                        row.style.backgroundColor = hexToRgba(currentCorCurso, 0.05);
                        row.style.borderColor = hexToRgba(currentCorCurso, 0.15);
                    }
                });
            }
        }

        // Função para limpar campos desabilitados antes de enviar o formulário
        function clearDisabledFields() {
            const bimestreInputs = document.querySelectorAll('#nonoAnoForm input[name*="_9_1"], #nonoAnoForm input[name*="_9_2"], #nonoAnoForm input[name*="_9_3"]');
            const mediaInputs = document.querySelectorAll('#nonoAnoForm input[name*="_9_media"]');

            // Limpar campos desabilitados
            [...bimestreInputs, ...mediaInputs].forEach(input => {
                if (input.disabled) {
                    input.value = '';
                }
            });
        }



        // Função para salvar dados do primeiro modal
        function saveFirstModalData() {
            const form = document.getElementById('cadastroForm');
            const formData = new FormData(form);

            // Salvar todos os campos do formulário
            firstModalData = {};
            for (let [key, value] of formData.entries()) {
                firstModalData[key] = value;
            }

            // Salvar também o tipo de escola e curso
            firstModalData.tipo_escola = currentTipoEscola;
            firstModalData.curso = currentCursoNome;
        }

        // Função para restaurar dados do primeiro modal
        function restoreFirstModalData() {
            const form = document.getElementById('cadastroForm');

            // Restaurar todos os campos
            Object.keys(firstModalData).forEach(key => {
                const input = form.querySelector(`[name="${key}"]`);
                if (input) {
                    input.value = firstModalData[key];
                }
            });
        }

        // Função para voltar ao modal de cadastro
        function voltarParaModalCadastro() {
            closeModal('modalNonoAno');

            // Restaurar dados do primeiro modal
            restoreFirstModalData();

            // Usar a cor armazenada na variável global
            openModalWithCourse('modalCadastro', currentCursoNome, currentCorCurso, currentTipoEscola);
        }

        // Form submission
        function submitForm() {
            const cadastroForm = document.getElementById('cadastroForm');
            const nonoAnoForm = document.getElementById('nonoAnoForm');

            // Limpar campos desabilitados antes de enviar
            clearDisabledFields();

            const formData = new FormData(cadastroForm);
            formData.append('tipo_escola', currentTipoEscola);
            formData.append('curso', currentCursoNome);
            formData.append('curso_id', currentCursoId);

            const nonoAnoData = new FormData(nonoAnoForm);
            for (let [key, value] of nonoAnoData.entries()) {
                formData.append(key, value);
            }

            fetch('processar_cadastro.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Cadastro realizado com sucesso!');
                        closeModal('modalNonoAno');
                        cadastroForm.reset();
                        nonoAnoForm.reset();
                        // Limpar dados salvos
                        firstModalData = {};
                    } else {
                        alert('Erro ao realizar cadastro: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Ocorreu um erro ao processar o cadastro.');
                });
        }
        
        // Funções genéricas de abrir/fechar modais (Relatórios/Resultados e outros)
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

        // Fechar modais com tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const openModals = document.querySelectorAll('[id^="modal"]:not(.hidden)');
                openModals.forEach(modal => closeModal(modal.id));
            }
        });
    </script>
</body>

</html>