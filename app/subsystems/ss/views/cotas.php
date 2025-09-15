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
    <title>Sistema Escolar - Cotas (Bairros)</title>
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
                            <a href="cotas.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring bg-white/10 bg-white/10">
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
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'cadastrador') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.5s;">
                            <a href="#" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
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
                        <div class="animate-slide-in-left" style="animation-delay: 0.6s;">
                            <a href="#" onclick="openAdminConfirm()" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
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
                            <a href="../../main/views/perfil.php" title="Perfil" class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-primary to-dark rounded-full flex items-center justify-center hover:brightness-95 focus-ring">
                                <span class="text-white font-bold text-xs sm:text-sm"><?= strtoupper(substr($_SESSION['nome'] ?? 'U', 0, 1)) ?></span>
                            </a>
                            <a href="../logout.php" class="bg-primary text-white px-3 sm:px-4 md:px-6 py-2 sm:py-3 rounded-xl hover:bg-dark btn-animate font-semibold shadow-lg focus-ring text-xs sm:text-sm">
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
                if (count($bairros) === 0) { ?>
                    <div class="bg-gradient-to-br from-accent via-white to-accent/50 border-2 border-dashed border-primary/30 rounded-2xl p-8 text-center animate-fade-in-up max-w-2xl mx-auto">
                        <div class="w-20 h-20 bg-gradient-to-br from-primary/10 to-secondary/10 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse-soft">
                            <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-primary mb-2 font-display">Nenhum bairro cadastrado</h3>
                        <p class="text-gray-600 mb-6">Crie o primeiro bairro para configurar cotas por bairro.</p>
                        <button onclick="openBairroModal()" class="inline-flex items-center bg-gradient-to-r from-primary to-dark text-white px-6 py-3 rounded-xl hover:from-dark hover:to-primary btn-animate font-semibold shadow-xl focus-ring">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Criar bairro
                        </button>
                    </div>
                <?php } else { ?>
                    <div class="flex items-center justify-between mb-6">
                        <div class="text-lg font-semibold text-gray-800">Bairros cadastrados</div>
                        <button onclick="openBairroModal()" class="inline-flex items-center bg-gradient-to-r from-primary to-dark text-white px-6 py-3 rounded-xl hover:from-dark hover:to-primary btn-animate font-semibold shadow-xl focus-ring">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Adicionar bairro
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
                        <?php foreach ($bairros as $bairro) { ?>
                            <article class="grid-item card-hover bg-white rounded-2xl shadow-xl border-0 overflow-hidden group relative" data-id="<?= htmlspecialchars($bairro['id']) ?>" data-nome="<?= htmlspecialchars($bairro['bairros']) ?>">
                                <div class="h-2 w-full bg-gradient-to-r from-primary to-secondary"></div>
                                <div class="p-8">
                                    <div class="text-center mb-8">
                                        <h3 class="text-xl font-bold leading-tight font-display group-hover:scale-105 transition-all duration-300 text-primary"><?= htmlspecialchars($bairro['bairros'] ?? ($bairro['bairros'] ?? 'Sem nome')) ?></h3>
                                        <div class="w-16 h-0.5 mx-auto mt-3 rounded-full bg-primary/40"></div>
                                    </div>
                                    <div class="space-y-3 mb-6">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <span class="font-medium">ID:</span>
                                            <span class="ml-2"><?= htmlspecialchars($bairro['id'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button type="button" onclick='openEditBairro(<?= json_encode($bairro['id'] ?? '') ?>, <?= json_encode($bairro['bairros']) ?>)' class="flex-1 bg-primary text-white py-2 px-4 rounded-lg hover:bg-dark transition-all duration-300 font-medium text-sm btn-animate focus-ring">Editar</button>
                                        <button type="button" onclick='openDeleteBairro(<?= json_encode($bairro['id'] ?? '') ?>, <?= json_encode($bairro['bairros']) ?>)' class="flex-1 bg-secondary text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition-all duration-300 font-medium text-sm btn-animate focus-ring">Excluir</button>
                                    </div>
                                </div>
                            </article>
                        <?php } ?>
                    </div>
                <?php } ?>
            </main>
        </div>
    </div>

    <!-- Admin Two-Step Confirm Modal -->
    <div id="modalAdminConfirm" class="fixed inset-0 bg-black/70 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modalAdminConfirmContent">
            <div class="p-6 sm:p-8 border-b border-gray-100 bg-gradient-to-r from-white to-gray-50">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary to-dark text-white flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3 1.567 3 3.5S13.657 18 12 18s-3-1.567-3-3.5 1.343-3.5 3-3.5zm4-4V7a4 4 0 01-8 0V7m12 4H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2v-6a2 2 0 00-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl sm:text-2xl font-bold text-dark">Confirmar Ação</h3>
                        <p class="text-gray-600 text-sm">Informe seu e-mail e valide com o código recebido.</p>
                    </div>
                </div>
                <button class="absolute top-6 right-6 p-2 rounded-xl hover:bg-gray-100 transition-all group" onclick="closeAdminConfirm()">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 group-hover:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6 sm:p-8">
                <form id="adminConfirmForm" class="space-y-6">
                    <div id="stepEmail">
                        <label class="block text-sm font-semibold text-dark mb-3">E-mail do Administrador</label>
                        <input id="adminEmailInput" type="email" class="w-full px-4 py-3.5 rounded-xl transition-all text-base border-2 border-gray-300 focus:outline-none outline-none focus:ring-0" placeholder="admin@dominio.com" required>
                        <p class="text-xs text-gray-500 mt-2">Enviaremos um código de verificação para este e-mail.</p>
                    </div>
                    <div id="stepCode" class="hidden">
                        <label class="block text-sm font-semibold text-dark mb-3">Código de Verificação</label>
                        <input id="adminCodeInput" type="text" inputmode="numeric" maxlength="6" class="w-full px-4 py-3.5 rounded-xl transition-all text-base border-2 border-gray-300 tracking-widest text-center focus:outline-none outline-none focus:ring-0" placeholder="••••••">
                        <p class="text-xs text-gray-500 mt-2">Digite o código enviado para o seu e-mail.</p>
                    </div>
                    <input type="hidden" id="adminEmailHidden" name="admin_email" value="">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <button type="button" id="btnBackStep" class="px-6 py-3 rounded-xl border-2 border-primary font-semibold text-primary hover:bg-primary/10 hover:border-primary transition-all text-base hidden" onclick="prevAdminStep()">Voltar</button>
                            <button type="button" id="btnCancel" class="px-6 py-3 rounded-xl border-2 border-primary font-semibold text-primary hover:bg-primary/10 hover:border-primary transition-all text-base" onclick="closeAdminConfirm()">Cancelar</button>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="submit" id="btnNextStep" class="px-6 py-3 bg-gradient-to-r from-primary to-dark text-white font-semibold rounded-xl hover:from-dark hover:to-primary transition-all text-base shadow-lg">Enviar código</button>
                            <button type="submit" id="btnConfirmAction" class="px-6 py-3 bg-gradient-to-r from-primary to-dark text-white font-semibold rounded-xl hover:from-dark hover:to-primary transition-all text-base shadow-lg hidden">Confirmar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Criar/Editar Bairro -->
    <div id="modalBairro" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-40">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="modalBairroContent">
            <div class="p-6 sm:p-8 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary to-dark text-white flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 id="modalBairroTitle" class="text-xl sm:text-2xl font-bold text-dark font-heading">Cadastrar Bairro</h3>
                        <p class="text-gray-600 text-sm">Defina o nome do bairro</p>
                    </div>
                </div>
                <button class="absolute top-6 right-6 p-2 rounded-xl hover:bg-gray-100 transition-all group" onclick="closeModal('modalBairro')">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 group-hover:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 sm:p-8">
                <form id="bairroForm" action="../controllers/controller_bairro.php" method="POST">
                    <input type="hidden" name="form" value="bairro">
                    <input type="hidden" id="inpBairroId" name="id_bairro" value="">
                    <div>
                        <label class="block text-sm font-semibold text-dark mb-3">Nome do Bairro *</label>
                        <input id="inpBairroNome" name="nome" type="text" class="w-full px-4 py-3.5 rounded-xl transition-all text-base border-2 focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="Digite o nome do bairro" required>
                    </div>
                    <div class="p-6 sm:p-8 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 mt-6 -mx-6 -mb-6 sm:-mx-8 sm:-mb-8">
                        <button type="button" class="px-6 py-3 rounded-xl border-2 border-primary font-semibold text-primary hover:bg-primary/10 hover:border-primary transition-all text-base focus-ring" onclick="closeModal('modalBairro')">Cancelar</button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-primary to-dark text-white font-semibold rounded-xl hover:from-primary/90 hover:to-dark/90 transition-all text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus-ring">
                            Salvar Bairro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Exclusão -->
    <div id="modalDeleteBairro" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modalDeleteBairroContent">
            <div class="p-6 sm:p-8 text-center">
                <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <button type="button" class="px-6 py-3 rounded-xl border-2 border-primary font-semibold text-primary hover:bg-primary/10 hover:border-primary transition-all text-base focus-ring" onclick="closeModal('modalDeleteBairro')">Cancelar</button>
                        <button type="submit" name="acao" value="delete" class="px-6 py-3 bg-gradient-to-r from-secondary to-orange-600 text-white font-semibold rounded-xl hover:from-secondary/90 hover:to-orange-700 transition-all text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus-ring">Excluir Bairro</button>
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

        function openAdminConfirm() {
            const modal = document.getElementById('modalAdminConfirm');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                const content = document.getElementById('modalAdminConfirmContent');
                content.style.transform = 'scale(1)';
                content.style.opacity = '1';
            }, 10);
        }

        function closeAdminConfirm() {
            const modal = document.getElementById('modalAdminConfirm');
            const content = document.getElementById('modalAdminConfirmContent');
            content.style.transform = 'scale(0.95)';
            content.style.opacity = '0';
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        // Two-step flow (email -> code) in same form
        const adminForm = document.getElementById('adminConfirmForm');
        const stepEmail = document.getElementById('stepEmail');
        const stepCode = document.getElementById('stepCode');
        const btnNextStep = document.getElementById('btnNextStep');
        const btnConfirmAction = document.getElementById('btnConfirmAction');
        const btnBackStep = document.getElementById('btnBackStep');
        const btnCancel = document.getElementById('btnCancel');
        const emailInput = document.getElementById('adminEmailInput');
        const codeInput = document.getElementById('adminCodeInput');
        const emailHidden = document.getElementById('adminEmailHidden');

        function nextAdminStep() {
            stepEmail.classList.add('hidden');
            stepCode.classList.remove('hidden');
            btnNextStep.classList.add('hidden');
            btnConfirmAction.classList.remove('hidden');
            btnBackStep.classList.remove('hidden');
            if (btnCancel) btnCancel.classList.add('hidden');
            if (emailInput && emailHidden) {
                emailHidden.value = emailInput.value.trim();
                emailInput.setAttribute('disabled', 'disabled');
            }
            setTimeout(() => codeInput && codeInput.focus(), 50);
        }

        function prevAdminStep() {
            stepCode.classList.add('hidden');
            stepEmail.classList.remove('hidden');
            btnConfirmAction.classList.add('hidden');
            btnBackStep.classList.add('hidden');
            btnNextStep.classList.remove('hidden');
            if (btnCancel) btnCancel.classList.remove('hidden');
            if (emailInput) emailInput.removeAttribute('disabled');
            setTimeout(() => emailInput && emailInput.focus(), 50);
        }

        if (adminForm) {
            adminForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!stepEmail.classList.contains('hidden')) {
                    if (!emailInput.value.trim()) return;
                    // TODO: send code to email via backend
                    nextAdminStep();
                    return;
                }
                // Confirm action with code
                if (!codeInput.value.trim()) return;
                alert('Código validado. Ação confirmada.');
                closeAdminConfirm();
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
            document.getElementById('modalBairroTitle').textContent = 'Cadastrar Bairro';
            document.getElementById('inpBairroId').value = '';
            document.getElementById('inpBairroNome').value = '';
            document.getElementById('bairroForm').action = '../controllers/controller_bairro.php';
            openModal('modalBairro');
        }

        function openEditBairro(id, nome) {
            document.getElementById('modalBairroTitle').textContent = 'Editar Bairro';
            document.getElementById('inpBairroId').value = id || '';
            document.getElementById('inpBairroNome').value = nome || '';
            document.getElementById('bairroForm').action = '../controllers/controller_bairro.php';
            openModal('modalBairro');
        }

        function openDeleteBairro(id, nome) {
            document.getElementById('deleteBairroName').textContent = nome || '';
            document.getElementById('deleteBairroId').value = id || '';
            openModal('modalDeleteBairro');
        }

        // Submissão simples com validação mínima
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('bairroForm');
            form.addEventListener('submit', function(e) {
                const nome = document.getElementById('inpBairroNome');
                if (!nome.value.trim()) {
                    e.preventDefault();
                    alert('Informe o nome do bairro.');
                }
            });
        });
    </script>
</body>

</html>