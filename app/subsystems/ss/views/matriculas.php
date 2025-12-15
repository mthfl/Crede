<?php
require_once __DIR__ . "/../models/sessions.php";
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once __DIR__ . "/../config/connect.php";
if (!isset($_SESSION["escola"]) || empty($_SESSION["escola"])) {
    die("Escola não definida na sessão");
}
$escola = $_SESSION["escola"];

new connect($escola);
require_once __DIR__ . "/../models/model.select.php";
$select = new select($escola);
require_once __DIR__ . "/../models/model.admin.php";
$admin = new admin($escola);

// Processar formulário de matrícula
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_matricula'])) {
    $dias_matricula = $_POST['dias_matricula'] ?? [];

    if (!empty($dias_matricula)) {
        $sucesso_total = true;
        foreach ($dias_matricula as $dia) {
            $curso_id = $dia['curso_id'] ?? null;
            $data = $dia['data'] ?? '';
            $hora = $dia['hora'] ?? '';

            if (!empty($data) && !empty($hora)) {
                $result = $admin->cadastrar_matricula($curso_id, $data, $hora);
                if ($result !== 1) {
                    $sucesso_total = false;
                }
            }
        }

        if ($sucesso_total) {
            header('Location: matriculas.php?sucesso=1');
            exit();
        } else {
            header('Location: matriculas.php?erro=1');
            exit();
        }
    }
}

// Processar exclusão de matrícula
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['excluir_matricula'])) {
    $id_matricula = $_GET['excluir_matricula'] ?? null;
    if ($id_matricula) {
        $result = $admin->excluir_matricula((int)$id_matricula);
        if ($result === 1) {
            header('Location: matriculas.php?sucesso=1');
            exit();
        } else {
            header('Location: matriculas.php?erro=1');
            exit();
        }
    }
}

// Buscar matrículas existentes
$matriculas = $select->select_matriculas();
?>
<!DOCTYPE html>
<html lang="pt-BR" style="height: auto; overflow-y: auto;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Sistema Escolar - Matrículas</title>
    <link rel="icon" type="image/png" href="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
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
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
            background: linear-gradient(135deg, #F8FAF9 0%, #E6F4EA 100%);
            height: auto;
            min-height: 100%;
            overflow-y: auto;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            height: 3rem;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #374151;
            font-size: 0.875rem;
            line-height: 1.25rem;
            padding-left: 0.75rem;
            padding-right: 2rem;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #005A24;
            box-shadow: 0 0 0 3px rgba(0, 90, 36, 0.1);
        }

        .horario-item {
            animation: slideInUp 0.3s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="min-h-screen flex flex-col font-sans">
    <div id="overlay" class="overlay fixed inset-0 bg-black/30 z-40 lg:hidden" style="display: none;"></div>
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar bg-primary fixed left-0 top-0 h-screen w-80 shadow-2xl z-50 lg:translate-x-0 lg:static lg:z-auto custom-scrollbar overflow-y-auto" style="transform: translateX(-100%); transition: transform 0.4s;">
            <div class="p-6">
                <div class="flex items-center justify-between mb-8 pb-6 border-b border-white/20">
                    <div>
                        <div class="flex items-center space-x-3 mb-2">
                            <img src="../assets/Brasão_do_Ceará.svg.png" alt="Brasão do Ceará" class="w-8 h-10 transition-transform hover:scale-105">
                            <h2 class="text-white text-2xl font-bold font-display">Sistema Seleção</h2>
                        </div>
                    </div>
                    <button id="closeSidebar" class="text-white lg:hidden p-2 rounded-xl hover:bg-white/10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <nav class="space-y-2">
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'cadastrador') { ?>
                        <div>
                            <a href="../index.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
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
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div>
                            <a href="usuario.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group">
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
                    <!-- Usuários -->
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.2s;">
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

                    <!-- Recursos -->
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.42s;">
                            <a href="recursos.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Recursos</span>
                                    <p class="text-green-200 text-xs mt-1">Gerenciar recursos</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                    <!-- Relatórios -->
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.45s;">
                            <a href="relatorios.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
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
                    <!-- Matrículas -->
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.47s;">
                            <a href="matriculas.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all bg-white/10 group focus-ring">
                                <div class="w-12 h-12 bg-secondary rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Matrículas</span>
                                    <p class="text-green-200 text-xs mt-1">Gerenciar dias e horários</p>
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
        <!-- Main Content -->
        <div class="main-content flex-1 bg-white">
            <header class="bg-white shadow-sm border-b border-gray-200 z-30 sticky top-0">
                <div class="px-3 sm:px-4 md:px-6 lg:px-8 py-3 sm:py-4">
                    <div class="flex items-center justify-between">
                        <button id="openSidebar" class="text-primary lg:hidden p-2 sm:p-3 rounded-lg hover:bg-accent">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        <div class="flex items-center space-x-2 sm:space-x-4 lg:ml-auto">
                            <div class="hidden sm:block text-right">
                                <p class="text-xs sm:text-sm font-semibold text-gray-900">Bem-vindo,</p>
                                <p class="text-xs sm:text-sm text-primary font-medium"><?= $_SESSION["nome"] ?? "Usuário" ?></p>
                            </div>
                            <a href="perfil.php" title="Perfil" class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-primary to-dark rounded-full flex items-center justify-center hover:brightness-95">
                                <span class="text-white font-bold text-xs sm:text-sm"><?= strtoupper(substr($_SESSION["nome"] ?? "U", 0, 1)) ?></span>
                            </a>
                            <a href="../models/sessions.php?sair" class="bg-primary text-white px-3 sm:px-4 md:px-6 py-2 sm:py-3 rounded-lg hover:bg-dark font-semibold shadow-lg text-xs sm:text-sm">
                                <span class="hidden sm:inline">Sair</span>
                                <i class="fas fa-sign-out-alt sm:hidden"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </header>
            <main class="p-4 sm:p-6 lg:p-8">
                <div class="text-center mb-8">
                    <h1 class="text-primary text-3xl md:text-4xl font-bold mb-4 tracking-tight font-heading">
                        <i class="fas fa-calendar-alt mr-3 text-secondary"></i>
                        Gerenciar Matrículas
                    </h1>
                    <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                        Configure os dias e horários de matrícula para cada curso
                    </p>
                </div>

                <?php if (isset($_GET['sucesso'])) { ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        Matrícula(s) adicionada(s) com sucesso!
                    </div>
                <?php } ?>
                <?php if (isset($_GET['erro'])) { ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        Erro ao processar matrícula(s). Tente novamente.
                    </div>
                <?php } ?>

                <div class="max-w-4xl mx-auto space-y-6">
                    <!-- Lista de Matrículas Existentes -->
                    <?php if (!empty($matriculas)) { ?>
                        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-accent">
                            <h2 class="text-xl font-bold text-primary mb-4">
                                <i class="fas fa-list mr-2 text-secondary"></i>
                                Matrículas Cadastradas
                            </h2>
                            <div class="space-y-3">
                                <?php foreach ($matriculas as $matricula) { ?>
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-800">
                                                <?php if ($matricula['nome_curso']) { ?>
                                                    <?= htmlspecialchars($matricula['nome_curso']) ?>
                                                <?php } else { ?>
                                                    <span class="text-secondary">TODOS OS CURSOS</span>
                                                <?php } ?>
                                            </p>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <i class="fas fa-calendar mr-1"></i>
                                                <?= date('d/m/Y', strtotime($matricula['data'])) ?>
                                                <i class="fas fa-clock ml-3 mr-1"></i>
                                                <?= date('H:i', strtotime($matricula['hora'])) ?>
                                            </p>
                                        </div>
                                        <a href="?excluir_matricula=<?= $matricula['id'] ?>"
                                            onclick="return confirm('Tem certeza que deseja excluir esta matrícula?')"
                                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all text-sm">
                                            <i class="fas fa-trash mr-1"></i>
                                            Excluir
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Formulário de Nova Matrícula -->
                    <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-accent">
                        <form action="../controllers/controller_matricula.php" method="POST" id="formMatricula" class="space-y-6">
                            <div id="dias-matricula-container" class="space-y-6">
                                <!-- Primeiro dia de matrícula -->
                                <div class="dia-matricula-item bg-gray-50 p-6 rounded-lg border-2 border-gray-200">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-semibold text-gray-800">Dia de Matrícula 1</h3>
                                        <button type="button" onclick="removerDiaMatricula(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all text-sm">
                                            <i class="fas fa-trash mr-1"></i>
                                            Remover
                                        </button>
                                    </div>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-book mr-2 text-primary"></i>
                                                Curso
                                            </label>
                                            <select name="dias_matricula[0][curso_id]" class="select2-curso w-full" required>
                                                <option value="">SELECIONAR CURSO</option>
                                                <option value=0>TODOS OS CURSOS</option>
                                                <?php
                                                $cursos = $select->select_cursos();
                                                foreach ($cursos as $curso) { ?>
                                                    <option value="<?= htmlspecialchars($curso["id"]) ?>">
                                                        <?= htmlspecialchars($curso["nome_curso"]) ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-calendar mr-2 text-primary"></i>
                                                Data da Matrícula
                                            </label>
                                            <input type="date" name="dias_matricula[0][data]" required
                                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-clock mr-2 text-primary"></i>
                                                Hora da Matrícula
                                            </label>
                                            <input type="time" name="dias_matricula[0][hora]" required
                                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" onclick="adicionarDiaMatricula()" class="w-full px-4 py-3 bg-secondary text-white rounded-lg hover:bg-orange-600 transition-all font-semibold">
                                <i class="fas fa-plus mr-2"></i>
                                Adicionar Mais um Dia de Matrícula
                            </button>

                            <div class="flex gap-4 pt-4">
                                <button type="submit" name="adicionar_matricula" class="flex-1 bg-gradient-to-r from-primary to-dark text-white py-3 px-6 rounded-lg hover:from-dark hover:to-primary transition-all font-semibold">
                                    <i class="fas fa-save mr-2"></i>
                                    Salvar Matrícula
                                </button>
                                <button type="reset" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all font-semibold">
                                    <i class="fas fa-redo mr-2"></i>
                                    Limpar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        let diaMatriculaCount = 1;

        $(document).ready(function() {
            // Inicializar Select2 para todos os selects existentes
            $('.select2-curso').select2({
                placeholder: 'SELECIONAR CURSO',
                allowClear: true
            });
        });

        function adicionarDiaMatricula() {
            const container = document.getElementById('dias-matricula-container');
            const novoDia = document.createElement('div');
            novoDia.className = 'dia-matricula-item bg-gray-50 p-6 rounded-lg border-2 border-gray-200 horario-item';
            novoDia.innerHTML = `
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Dia de Matrícula ${diaMatriculaCount + 1}</h3>
                    <button type="button" onclick="removerDiaMatricula(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all text-sm">
                        <i class="fas fa-trash mr-1"></i>
                        Remover
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-book mr-2 text-primary"></i>
                            Curso
                        </label>
                        <select name="dias_matricula[${diaMatriculaCount}][curso_id]" class="select2-curso w-full" required>
                            <option value="">SELECIONAR CURSO</option>
                            <option value="todos">TODOS OS CURSOS</option>
                            <?php
                            $cursos = $select->select_cursos();
                            foreach ($cursos as $curso) { ?>
                                <option value="<?= htmlspecialchars($curso["id"]) ?>">
                                    <?= htmlspecialchars($curso["nome_curso"]) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar mr-2 text-primary"></i>
                            Data da Matrícula
                        </label>
                        <input type="date" name="dias_matricula[${diaMatriculaCount}][data]" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-clock mr-2 text-primary"></i>
                            Hora da Matrícula
                        </label>
                        <input type="time" name="dias_matricula[${diaMatriculaCount}][hora]" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                    </div>
                </div>
            `;
            container.appendChild(novoDia);
            diaMatriculaCount++;

            // Inicializar Select2 no novo select
            $(novoDia).find('.select2-curso').select2({
                placeholder: 'SELECIONAR CURSO',
                allowClear: true
            });
        }

        function removerDiaMatricula(button) {
            const container = document.getElementById('dias-matricula-container');
            if (container.children.length > 1) {
                const item = button.closest('.dia-matricula-item');
                // Destruir Select2 antes de remover
                $(item).find('.select2-curso').select2('destroy');
                item.remove();
                // Renumerar os dias
                container.querySelectorAll('.dia-matricula-item').forEach((item, index) => {
                    item.querySelector('h3').textContent = `Dia de Matrícula ${index + 1}`;
                });
            } else {
                alert('É necessário ter pelo menos um dia de matrícula!');
            }
        }

        // Sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const openSidebar = document.getElementById('openSidebar');
        const closeSidebar = document.getElementById('closeSidebar');

        openSidebar.addEventListener('click', () => {
            sidebar.style.transform = 'translateX(0)';
            overlay.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });

        closeSidebar.addEventListener('click', () => {
            sidebar.style.transform = 'translateX(-100%)';
            overlay.style.display = 'none';
            document.body.style.overflow = '';
        });

        overlay.addEventListener('click', () => {
            sidebar.style.transform = 'translateX(-100%)';
            overlay.style.display = 'none';
            document.body.style.overflow = '';
        });
    </script>
</body>

</html>