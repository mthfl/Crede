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
    <title>Sistema Escolar - Relatórios</title>
    <link rel="icon" type="image/png" href="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                        heading: ['Poppins', 'sans-serif']
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
                        'bounce-slow': 'bounce 2s infinite'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
            background: linear-gradient(135deg, #F8FAF9 0%, #E6F4EA 100%);
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
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
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
            transition: opacity 0.3s ease;
            z-index: 1;
        }

        .stats-card:hover::before {
            opacity: 1;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 90, 36, 0.2), 0 10px 10px -5px rgba(0, 90, 36, 0.1);
            border-color: #FFA500;
        }

        .report-card {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #FFFFFF 0%, #F8FAF9 100%);
            border: 2px solid #E6F4EA;
            border-radius: 1rem;
        }

        .report-card::before {
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

        .report-card:hover::before {
            opacity: 1;
        }

        .report-card:hover {
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

        .report-card:hover .card-shine {
            left: 150%;
        }

        .report-card:hover .card-icon {
            transform: scale(1.1);
            color: #FFA500;
        }

        .report-card p {
            z-index: 2;
            position: relative;
            transition: color 0.3s ease;
        }

        .report-card:hover p {
            color: #005A24;
        }

        .report-card a,
        .report-card button {
            position: relative;
            z-index: 3;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: linear-gradient(135deg, #FFFFFF 0%, #F8FAF9 100%);
            padding: 2rem;
            border-radius: 1rem;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 25px 50px -12px rgba(0, 90, 36, 0.25);
            animation: slideUp 0.5s ease-out;
            position: relative;
            border: 2px solid #E6F4EA;
        }

        .modal-content h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            color: #005A24;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .modal-content .form-group {
            margin-bottom: 1.5rem;
        }

        .modal-content label {
            display: block;
            font-size: 0.875rem;
            color: #1A3C34;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .modal-content select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #E6F4EA;
            border-radius: 0.5rem;
            font-size: 1rem;
            color: #1A3C34;
            background-color: #F8FAF9;
            transition: all 0.3s ease;
        }

        .modal-content select:focus {
            border-color: #FFA500;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 165, 0, 0.1);
        }

        .modal-content .confirm-btn {
            background: linear-gradient(135deg, #FFA500 0%, #FF8C00 100%);
            color: #FFFFFF;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .modal-content .confirm-btn:hover {
            background: linear-gradient(135deg, #E59400 0%, #E67E00 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 165, 0, 0.3);
        }

        .modal-content .close-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #1A3C34;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .modal-content .close-btn:hover {
            color: #DC3545;
        }

        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            backdrop-filter: blur(10px);
            background: linear-gradient(135deg, #005A24 0%, #1A3C34 100%);
            z-index: 50;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 100vw;
            max-width: 20rem;
        }

        @media (min-width: 1024px) {
            .sidebar {
                width: 20rem;
                position: static;
                flex-shrink: 0;
                transform: translateX(0);
            }

            .main-content {
                flex: 1;
                min-width: 0;
                margin-left: 0;
                overflow-x: hidden;
            }
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

        .chart-container {
            position: relative;
            height: 300px;
            margin: 1rem 0;
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

        @media (max-width: 768px) {
            .sidebar {
                width: 100vw;
                max-width: 320px;
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col font-sans">
    <div id="overlay" class="overlay fixed inset-0 bg-black/30 z-40 lg:hidden"></div>
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar fixed left-0 top-0 h-screen w-80 shadow-2xl z-50 lg:translate-x-0 lg:static lg:z-auto custom-scrollbar overflow-y-auto">
            <div class="flex flex-col h-full">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-8 pb-6 border-b border-white/20">
                        <div class="flex items-center space-x-3 mb-2">
                            <img src="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png" alt="Brasão do Ceará" class="w-8 h-10 transition-transform hover:scale-105">
                            <h2 class="text-white text-2xl font-bold font-heading">Sistema Seleção</h2>
                        </div>
                        <button id="closeSidebar" class="text-white lg:hidden p-2 rounded-xl hover:bg-white/10 focus-ring">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <nav class="space-y-2">
                        <?php if (isset($_SESSION['tipo_usuario']) && ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'cadastrador')) { ?>
                            <a href="../index.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                                <i class="fas fa-home mr-3 text-lg"></i>
                                <span>Início</span>
                            </a>
                        <?php } ?>
                        <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                            <a href="cursos.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                                <i class="fas fa-book mr-3 text-lg"></i>
                                <span>Cursos</span>
                            </a>
                        <?php } ?>
                        <?php if (isset($_SESSION['tipo_usuario']) && ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'cadastrador')) { ?>
                            <a href="candidatos.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                                <i class="fas fa-users mr-3 text-lg"></i>
                                <span>Candidatos</span>
                            </a>
                        <?php } ?>
                        <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                            <a href="cotas.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                                <i class="fas fa-balance-scale mr-3 text-lg"></i>
                                <span>Cotas</span>
                            </a>
                        <?php } ?>
                        <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                            <a href="usuario.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                                <i class="fas fa-user-cog mr-3 text-lg"></i>
                                <span>Usuários</span>
                            </a>
                        <?php } ?>
                        <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                            <a href="relatorios.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2 active">
                                <i class="fas fa-clipboard-list mr-3 text-lg"></i>
                                <span>Relatórios</span>
                            </a>
                        <?php } ?>
                        <?php if (isset($_SESSION['tipo_usuario']) && ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'cadastrador')) { ?>
                            <a href="solicitar_alteracao.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                                <i class="fas fa-edit mr-3 text-lg"></i>
                                <span>Requisições</span>
                            </a>
                        <?php } ?>
                        <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                            <a href="limpar_banco.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                                <i class="fas fa-trash-alt mr-3 text-lg"></i>
                                <span>Limpar Banco</span>
                            </a>
                        <?php } ?>
                    </nav>
                </div>
            </div>
        </aside>
        <!-- Main Content -->
        <div class="main-content flex-1 bg-white">
            <header class="bg-white shadow-sm border-b border-gray-200 z-30 sticky top-0">
                <div class="px-3 sm:px-4 md:px-6 lg:px-8 py-3 sm:py-4">
                    <div class="flex items-center justify-between">
                        <button id="openSidebar" class="text-primary lg:hidden p-2 sm:p-3 rounded-lg hover:bg-accent focus-ring">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        <div class="flex items-center space-x-2 sm:space-x-4 lg:ml-auto">
                            <div class="hidden sm:block text-right">
                                <p class="text-xs sm:text-sm font-semibold text-gray-900">Bem-vindo,</p>
                                <p class="text-xs sm:text-sm text-primary font-medium"><?= $_SESSION['nome'] ?? 'Usuário' ?></p>
                            </div>
                            <a href="perfil.php" title="Perfil" class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-primary to-dark rounded-full flex items-center justify-center hover:brightness-95 focus-ring">
                                <span class="text-white font-bold text-xs sm:text-sm"><?= strtoupper(substr($_SESSION['nome'] ?? 'U', 0, 1)) ?></span>
                            </a>
                            <a href="models/sessions.php?sair" class="bg-primary text-white px-3 sm:px-4 md:px-6 py-2 sm:py-3 rounded-lg hover:bg-dark font-semibold shadow-lg focus-ring text-xs sm:text-sm">
                                <span class="hidden sm:inline">Sair</span>
                                <i class="fas fa-sign-out-alt sm:hidden"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </header>
            <main class="p-4 sm:p-6 lg:p-8">
                <div class="text-center mb-8">
                    <h1 class="text-primary text-3xl md:text-4xl font-bold mb-4 page-title tracking-tight font-heading">
                        <i class="fas fa-chart-line mr-3 text-secondary"></i>
                        Relatórios
                    </h1>
                    <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                        Acesse relatórios detalhados e estatísticas em tempo real
                    </p>
                </div>

                <!-- Estatísticas Rápidas -->
                <div class="quick-stats mb-8">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="stat-number"><?php echo $select->countTotalAlunos(); ?></div>
                        <div class="stat-label">Total de Candidatos</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="stat-number"><?php echo $select->countTotalEscolas(); ?></div>
                        <div class="stat-label">Total de Cursos</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-number"><?php echo $select->countTotalRelatorios(); ?></div>
                        <div class="stat-label">Total de Requisições</div>
                    </div>
                </div>

                <!-- Gráfico de Estatísticas -->
                <div class="bg-white rounded-xl shadow-card p-6 mb-8">
                    <h2 class="text-xl font-bold text-primary mb-4 flex items-center">
                        <i class="fas fa-chart-pie mr-2 text-secondary"></i>
                        Distribuição de Alunos por Modalidade
                    </h2>
                    <div class="chart-container">
                        <canvas id="distributionChart"></canvas>
                    </div>
                </div>

                <!-- Escolas Públicas e Privadas -->
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center font-heading">
                    <i class="fas fa-school mr-2 text-secondary"></i>
                    Escolas Públicas e Privadas
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                    <div class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in">
                        <div class="card-shine"></div>
                        <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                            <i class="fas fa-school text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-primary mb-2 text-center font-heading">Privada AC</h3>
                        <p class="text-gray-600 text-center mb-4 text-sm">Relatório de Ampla Concorrência para Escolas Privadas</p>
                        <button onclick="openReportModal('privada_ac', 'Privada AC')" class="bg-gradient-to-r from-secondary to-orange-500 text-white py-2 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold transform hover:scale-105">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Gerar PDF
                        </button>
                    </div>
                    <div class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in" style="animation-delay: 0.1s">
                        <div class="card-shine"></div>
                        <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                            <i class="fas fa-users text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-primary mb-2 text-center font-heading">Privada Cotas</h3>
                        <p class="text-gray-600 text-center mb-4 text-sm">Relatório de Cotas para Escolas Privadas</p>
                        <button onclick="openReportModal('privada_cotas', 'Privada Cotas')" class="bg-gradient-to-r from-secondary to-orange-500 text-white py-2 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold transform hover:scale-105">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Gerar PDF
                        </button>
                    </div>
                    <div class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in" style="animation-delay: 0.2s">
                        <div class="card-shine"></div>
                        <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                            <i class="fas fa-file-alt text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-primary mb-2 text-center font-heading">Privada Geral</h3>
                        <p class="text-gray-600 text-center mb-4 text-sm">Relatório Geral para Escolas Privadas</p>
                        <button onclick="openReportModal('privada_geral', 'Privada Geral')" class="bg-gradient-to-r from-secondary to-orange-500 text-white py-2 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold transform hover:scale-105">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Gerar PDF
                        </button>
                    </div>
                    <div class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in" style="animation-delay: 0.3s">
                        <div class="card-shine"></div>
                        <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                            <i class="fas fa-school text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-primary mb-2 text-center font-heading">Pública AC</h3>
                        <p class="text-gray-600 text-center mb-4 text-sm">Relatório de Ampla Concorrência para Escolas Públicas</p>
                        <button onclick="openReportModal('publica_ac', 'Pública AC')" class="bg-gradient-to-r from-secondary to-orange-500 text-white py-2 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold transform hover:scale-105">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Gerar PDF
                        </button>
                    </div>
                    <div class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in" style="animation-delay: 0.4s">
                        <div class="card-shine"></div>
                        <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                            <i class="fas fa-users text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-primary mb-2 text-center font-heading">Pública Cotas</h3>
                        <p class="text-gray-600 text-center mb-4 text-sm">Relatório de Cotas para Escolas Públicas</p>
                        <button onclick="openReportModal('publica_cotas', 'Pública Cotas')" class="bg-gradient-to-r from-secondary to-orange-500 text-white py-2 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold transform hover:scale-105">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Gerar PDF
                        </button>
                    </div>
                    <div class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in" style="animation-delay: 0.5s">
                        <div class="card-shine"></div>
                        <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                            <i class="fas fa-file-alt text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-primary mb-2 text-center font-heading">Pública Geral</h3>
                        <p class="text-gray-600 text-center mb-4 text-sm">Relatório Geral para Escolas Públicas</p>
                        <button onclick="openReportModal('publica_geral', 'Pública Geral')" class="bg-gradient-to-r from-secondary to-orange-500 text-white py-2 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold transform hover:scale-105">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Gerar PDF
                        </button>
                    </div>
                </div>

                <!-- Resultados -->
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center font-heading">
                    <i class="fas fa-chart-line mr-2 text-secondary"></i>
                    Resultados
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6 mb-8">
                    <div class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in" style="animation-delay: 0.1s">
                        <div class="card-shine"></div>
                        <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                            <i class="fas fa-trophy text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-primary mb-2 text-center font-heading">Classificados</h3>
                        <p class="text-gray-600 text-center mb-4 text-sm">Relatório de Classificados</p>
                        <button onclick="openReportModal('Classificados', 'Classificados')" class="bg-gradient-to-r from-secondary to-orange-500 text-white py-2 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold transform hover:scale-105">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Gerar PDF
                        </button>
                    </div>
                    <div class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in" style="animation-delay: 0.2s">
                        <div class="card-shine"></div>
                        <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                            <i class="fas fa-list-alt text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-primary mb-2 text-center font-heading">Classificáveis</h3>
                        <p class="text-gray-600 text-center mb-4 text-sm">Relatório de Classificáveis</p>
                        <button onclick="openReportModal('Classificaveis', 'Classificáveis')" class="bg-gradient-to-r from-secondary to-orange-500 text-white py-2 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold transform hover:scale-105">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Gerar PDF
                        </button>
                    </div>
                    <div class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in" style="animation-delay: 0.3s">
                        <div class="card-shine"></div>
                        <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                            <i class="fas fa-flag-checkered text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-primary mb-2 text-center font-heading">Resultado Final</h3>
                        <p class="text-gray-600 text-center mb-4 text-sm">Resultado Final</p>
                        <button onclick="openReportModal('Resultado Final', 'Resultado Final')" class="bg-gradient-to-r from-secondary to-orange-500 text-white py-2 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold transform hover:scale-105">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Gerar PDF
                        </button>
                    </div>
                    <div class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in" style="animation-delay: 0.4s">
                        <div class="card-shine"></div>
                        <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                            <i class="fas fa-clipboard-list text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-primary mb-2 text-center font-heading">Resultado Pré-liminar</h3>
                        <p class="text-gray-600 text-center mb-4 text-sm">Resultado Pré-liminar</p>
                        <button onclick="openReportModal('Resultado pré-liminar', 'Resultado Pré-liminar')" class="bg-gradient-to-r from-secondary to-orange-500 text-white py-2 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold transform hover:scale-105">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Gerar PDF
                        </button>
                    </div>
                </div>

                <!-- Relatórios Específicos -->
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center font-heading">
                    <i class="fas fa-file-alt mr-2 text-secondary"></i>
                    Relatórios Específicos
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
                    <a href="../controllers/controller_relatorios.php?form=relatorio_pdf&tipo_relatorio=comissao_selecao" class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in" style="animation-delay: 0.1s">
                        <div class="card-shine"></div>
                        <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                            <i class="fas fa-users-cog text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-primary mb-2 text-center font-heading">Comissão de Seleção</h3>
                        <p class="text-gray-600 text-center mb-4 text-sm">Relatório da Comissão de Seleção</p>
                        <span class="bg-gradient-to-r from-secondary to-orange-500 text-white py-2 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold transform hover:scale-105">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Gerar PDF
                        </span>
                    </a>
                    <a href="../controllers/controller_relatorios.php?form=relatorio_pdf&tipo_relatorio=movimentacoes" class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in" style="animation-delay: 0.2s">
                        <div class="card-shine"></div>
                        <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                            <i class="fas fa-exchange-alt text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-primary mb-2 text-center font-heading">Movimentações</h3>
                        <p class="text-gray-600 text-center mb-4 text-sm">Relatório de Movimentações</p>
                        <span class="bg-gradient-to-r from-secondary to-orange-500 text-white py-2 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold transform hover:scale-105">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Gerar PDF
                        </span>
                    </a>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal for Report Course Selection -->
    <div id="reportModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('reportModal')">×</button>
            <h2 class="font-heading">
                <i class="fas fa-file-alt mr-2 text-secondary"></i>
                Selecionar Curso
            </h2>
            <form id="reportForm" action="../controllers/controller_relatorios.php" method="POST" class="space-y-4">
                <input type="hidden" name="form" value="relatorio_pdf">
                <input type="hidden" name="tipo_relatorio" id="reportTypeInput">
                <div class="form-group">
                    <label for="curso_id" class="font-semibold">
                        <i class="fas fa-book mr-1"></i>
                        Curso
                    </label>
                    <select name="curso" id="curso_id" required class="select2-curso">
                        <option value="" disabled selected>SELECIONAR CURSO</option>
                        <?php
                        $cursos = $select->select_cursos();
                        foreach ($cursos as $curso) { ?>
                            <option value="<?= htmlspecialchars($curso['id']) ?>"><?= htmlspecialchars($curso['nome_curso']) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <button type="submit" class="confirm-btn">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Gerar Relatório
                </button>
            </form>
        </div>
    </div>

    <script>
        // Inicializar o gráfico de distribuição
        const ctx = document.getElementById('distributionChart').getContext('2d');
        const distributionChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Pública AC', 'Pública Cotas', 'Privada AC', 'Privada Cotas'],
                datasets: [{
                    data: [
                        <?php echo $select->countAlunosPorTipo('publica_ac'); ?>,
                        <?php echo $select->countAlunosPorTipo('publica_cotas'); ?>,
                        <?php echo $select->countAlunosPorTipo('privada_ac'); ?>,
                        <?php echo $select->countAlunosPorTipo('privada_cotas'); ?>
                    ],
                    backgroundColor: [
                        '#28A745',
                        '#FFC107',
                        '#17A2B8',
                        '#DC3545'
                    ],
                    borderWidth: 2,
                    borderColor: '#FFFFFF'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                family: 'Inter',
                                size: 12
                            },
                            padding: 20
                        }
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                return label + ': ' + value + ' candidatos';
                            }
                        }
                    }
                }
            }
        });

        // Sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const openSidebar = document.getElementById('openSidebar');
        const closeSidebar = document.getElementById('closeSidebar');

        openSidebar.addEventListener('click', () => {
            sidebar.classList.add('open');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        });

        closeSidebar.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        });

        // Modal handling
        function openReportModal(reportType, reportLabel) {
            const modal = document.getElementById('reportModal');
            const modalTitle = document.getElementById('reportModalTitle');
            const reportTypeInput = document.getElementById('reportTypeInput');
            modalTitle.textContent = `Gerar Relatório: ${reportLabel}`;
            reportTypeInput.value = reportType;
            modal.classList.add('show');
            setTimeout(() => {
                $('.select2-curso').select2({
                    placeholder: "SELECIONAR CURSO",
                    allowClear: true,
                    width: '100%',
                    language: {
                        noResults: function() {
                            return "Nenhum curso encontrado";
                        },
                        searching: function() {
                            return "Pesquisando...";
                        }
                    }
                });
            }, 100);
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('show');
            document.getElementById('reportForm').reset();
            document.body.style.overflow = '';
            $('.select2-curso').select2('destroy');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal('reportModal');
            }
        });

        // Form validation
        const reportForm = document.getElementById('reportForm');
        reportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const curso = document.getElementById('curso_id').value;
            if (!curso) {
                showNotification('Por favor, selecione um curso.', 'error');
                return;
            }
            const submitBtn = reportForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="loading-spinner"></span> Gerando...';
            submitBtn.disabled = true;
            setTimeout(() => {
                reportForm.submit();
            }, 1000);
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            }, 5000);
        });

        // Notification function
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
            const bgColor = type === 'error' ? 'bg-red-500' : type === 'success' ? 'bg-green-500' : 'bg-blue-500';
            notification.className += ` ${bgColor} text-white`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${type === 'error' ? 'exclamation-circle' : type === 'success' ? 'check-circle' : 'info-circle'} mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        // Initialize Select2 on page load
        $(document).ready(function() {
            $('.select2-curso').select2({
                placeholder: "SELECIONAR CURSO",
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Nenhum curso encontrado";
                    },
                    searching: function() {
                        return "Pesquisando...";
                    }
                }
            });
        });
    </script>
</body>
</html>