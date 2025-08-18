<?php 
require_once('../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once('../models/model.select.php');
$select = new select();

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Relatórios - STGM Estoque</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                            '0%': {
                                transform: 'translateY(20px)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateY(0)',
                                opacity: '1'
                            }
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
            border: 2px solid transparent;
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

        .stats-card:hover .card-shine,
        .report-card:hover .card-shine {
            left: 150%;
        }

        .stats-card:hover .card-icon,
        .report-card:hover .card-icon {
            transform: scale(1.1);
            color: #FFA500;
        }
        
        .stats-card p,
        .report-card p {
            z-index: 2;
            position: relative;
            transition: color 0.3s ease;
        }

        .stats-card:hover p,
        .report-card:hover p {
            color: #005A24;
        }

        .stats-card a,
        .stats-card button,
        .report-card a,
        .report-card button {
            position: relative;
            z-index: 3;
        }

        .header-nav-link {
            position: relative;
                transition: all 0.3s ease;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
        }

        .header-nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .header-nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 0;
            height: 2px;
            background-color: #FFA500;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .header-nav-link:hover::after {
            width: 80%;
        }

        .header-nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .header-nav-link.active::after {
            width: 80%;
        }

        .mobile-menu-button {
            display: none;
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

        .modal-content input[type="date"],
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

        .modal-content input[type="date"]:focus,
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

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin: 1rem 0;
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

        @media (max-width: 768px) {
            .header-nav {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(135deg, #005A24 0%, #1A3C34 100%);
                padding: 2rem 1rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                z-index: 50;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                backdrop-filter: blur(10px);
            }

            .header-nav.show {
                display: flex;
                animation: slideIn 0.3s ease-out;
            }

            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .header-nav-link {
                padding: 1rem 1.5rem;
                text-align: center;
                margin: 0.5rem 0;
                font-size: 1.1rem;
                border-radius: 0.75rem;
                transition: all 0.3s ease;
                width: 100%;
                max-width: 300px;
            }

            .header-nav-link:hover {
                background-color: rgba(255, 255, 255, 0.15);
                transform: translateX(5px);
            }

            .mobile-menu-button {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                width: 30px;
                height: 21px;
                background: transparent;
            border: none;
            cursor: pointer;
                padding: 0;
                z-index: 60;
                position: relative;
            }

            .mobile-menu-button span {
                width: 100%;
                height: 3px;
                background-color: white;
                border-radius: 10px;
            transition: all 0.3s ease;
                position: relative;
                transform-origin: center;
            }

            .mobile-menu-button span:first-child.active {
                transform: rotate(45deg) translate(6px, 6px);
            }

            .mobile-menu-button span:nth-child(2).active {
                opacity: 0;
                transform: scale(0);
            }

            .mobile-menu-button span:nth-child(3).active {
                transform: rotate(-45deg) translate(6px, -6px);
            }

            /* Overlay para fechar menu ao clicar fora */
            .header-nav::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.3);
                z-index: -1;
            }

            .quick-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            /* Responsividade da sidebar */
            #sidebar {
                transform: translateX(-100%);
            }
            
            #sidebar.show {
                transform: translateX(0);
            }

            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            /* Botão do menu mobile */
            #menuButton {
                transition: all 0.3s ease;
            }
            
            #menuButton.hidden {
                opacity: 0;
                visibility: hidden;
                transform: scale(0.8);
            }
            
            /* Footer responsivo para mobile */
            footer {
                margin-left: 0 !important;
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            
            footer .ml-64 {
                margin-left: 0 !important;
            }

            /* Main content responsivo */
            main {
                margin-left: 0 !important;
            }
        }

        /* Estilos dos links do sidebar */
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
    </style>
</head>

<body class="min-h-screen flex flex-col font-sans">
    <!-- Sidebar -->
    <div class="fixed left-0 top-0 h-full w-64 bg-gradient-to-b from-primary to-dark text-white shadow-xl z-50 transform transition-transform duration-300 ease-in-out" id="sidebar">
        <div class="flex flex-col h-full">
            <!-- Logo e título -->
            <div class="p-6 border-b border-white/20">
                <div class="flex items-center">
                    <img src="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png" alt="Logo STGM" class="h-12 mr-3 transition-transform hover:scale-105">
                    <span class="text-white font-heading text-lg font-semibold">CREDE Estoque</span>
                </div>
            </div>
            
            <!-- Menu de navegação -->
            <nav class="flex-1 p-4 space-y-2">
                <a href="index.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                    <i class="fas fa-home mr-3 text-lg"></i>
                    <span>Início</span>
                </a>
                <a href="estoque.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                    <i class="fas fa-boxes mr-3 text-lg"></i>
                    <span>Estoque</span>
                </a>
                <a href="./products/adc_produto.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                    <i class="fas fa-plus-circle mr-3 text-lg"></i>
                    <span>Adicionar</span>
                </a>
              
                <a href="solicitar.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                    <i class="fas fa-clipboard-list mr-3 text-lg"></i>
                    <span>Solicitar</span>
                </a>
                <a href="relatorios.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2 active">
                    <i class="fas fa-chart-bar mr-3 text-lg"></i>
                    <span>Relatórios</span>
                </a>
            </nav>

            <!-- Botão de Sair -->
            <div class="p-4 border-t border-white/20">
                <a href="../../../../../main/views/subsystems.php" class="w-full bg-transparent border border-white/40 hover:bg-white/10 text-white py-2 px-4 rounded-lg transition-all duration-200 flex items-center justify-center">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Sair
                </a>
            </div>

            <!-- Botão de fechar sidebar no mobile -->
            <div class="p-4 border-t border-white/20 md:hidden">
                <button class="w-full bg-white/10 hover:bg-white/20 text-white py-2 px-4 rounded-lg transition-all duration-200" id="closeSidebar">
                    <i class="fas fa-times mr-2"></i>
                    Fechar Menu
                </button>
            </div>
        </div>
    </div>

    <button class="fixed top-4 left-4 z-50 md:hidden  text-primary p-3 rounded-lg  hover:bg-primary/90 transition-all duration-200" id="menuButton">
        <i class="fas fa-bars text-lg"></i>
    </button>
    
    <!-- Overlay para mobile -->
    <div class="fixed inset-0 bg-black/50 z-40 md:hidden hidden" id="overlay"></div>
    
    <!-- Botão Voltar ao Topo -->
    <button class="back-to-top hidden fixed bottom-6 right-6 z-50 bg-secondary hover:bg-secondary/90 text-white w-12 h-12 rounded-full shadow-lg transition-all duration-300 flex items-center justify-center group">
        <i class="fas fa-chevron-up text-lg group-hover:scale-110 transition-transform duration-300"></i>
    </button>

    <!-- Main content -->
    <main class="ml-0 md:ml-64 px-4 py-8 md:py-12 flex-1 transition-all duration-300">
        <!-- Título da Página -->
        <div class="text-center mb-8">
            <h1 class="text-primary text-3xl md:text-4xl font-bold mb-4 page-title tracking-tight font-heading">
                <i class="fas fa-chart-line mr-3 text-secondary"></i>
                CENTRO DE RELATÓRIOS
            </h1>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                Acesse relatórios detalhados e estatísticas em tempo real do seu estoque
            </p>
        </div>

        <!-- Estatísticas Rápidas -->
        <div class="quick-stats mb-8">
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-boxes">
                        <?php 
                        $dados = $select->select_produtos_total();
                        foreach ($dados as $dado) {
                            echo $dado['total'];
                        }
                        ?>
                    </i>
                </div>
                <div class="stat-number" id="totalProdutos">-</div>
                    <div class="stat-label">Total de Produtos</div>
                </div>
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle text-warning">
                    <?php 
                        $dados = $select->select_produtos_critico();
                        foreach ($dados as $dado) {
                            echo $dado['total'];
                        }
                        ?>
                    </i>
                </div>
                <div class="stat-number" id="produtosCriticos">-</div>
                <div class="stat-label">Estoque Crítico</div>
                </div>
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-tags">
                        <?php 
                        $dados = $select->select_total_categorias();
                        foreach ($dados as $dado) {
                            echo $dado['total'];
                        }
                        ?>
                        </i>
                </div>
                <div class="stat-number" id="totalCategorias">-</div>
                <div class="stat-label">Categorias</div>
                </div>
            </div>

        <!-- Gráfico de Estoque -->
        <div class="bg-white rounded-xl shadow-card p-6 mb-8">
            <h2 class="text-xl font-bold text-primary mb-4 flex items-center">
                <i class="fas fa-chart-pie mr-2 text-secondary"></i>
                Visão Geral do Estoque
            </h2>
                <div class="chart-container">
                <canvas id="estoqueChart"></canvas>
                    </div>
                </div>

        <!-- Relatórios Disponíveis -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Relatório de Estoque Completo -->
            <div class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in">
                <div class="card-shine"></div>
                <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-4xl"></i>
                    </div>
                <h3 class="text-lg font-bold text-primary mb-2 text-center">Relatório Completo</h3>
                <p class="text-gray-600 text-center mb-4 text-sm">Relatório detalhado de todo o estoque atual</p>
                <a href="reports/relatorio_produtos_cadastrados.php" class="bg-gradient-to-r from-secondary to-orange-500 text-white py-2 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold transform hover:scale-105" target="_blank">
                    <i class="fas fa-download mr-2"></i>
                    Gerar PDF
                </a>
                </div>

            <!-- Relatório por Período -->
            <div class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in" style="animation-delay: 0.1s">
                <div class="card-shine"></div>
                <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-4xl"></i>
                    </div>
                <h3 class="text-lg font-bold text-primary mb-2 text-center">Relatório de Movimentações</h3>
                <p class="text-gray-600 text-center mb-4 text-sm">Relatório de movimentação de estoque por período específico</p>
                <button id="openDateModal" class="bg-gradient-to-r from-secondary to-orange-500 text-white py-2 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold transform hover:scale-105">
                    <i class="fas fa-calendar-check mr-2"></i>
                    Selecionar Data
                </button>
                </div>

            <!-- Relatório de Estoque Crítico -->
            <div class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in" style="animation-delay: 0.2s">
                <div class="card-shine"></div>
                <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-4xl"></i>
                    </div>
                <h3 class="text-lg font-bold text-primary mb-2 text-center">Estoque Crítico</h3>
                <p class="text-gray-600 text-center mb-4 text-sm">Produtos com estoque baixo (≤ 5 unidades)</p>
                <a href="reports/relatorio_critico.php" class="bg-gradient-to-r from-secondary to-orange-500 text-white py-2 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold transform hover:scale-105" target="_blank">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Gerar PDF
                </a>
                </div>

            <!-- Relatório por Produto -->
            <div class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in" style="animation-delay: 0.3s">
                <div class="card-shine"></div>
                <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                    <i class="fas fa-search text-4xl"></i>
                </div>
                <h3 class="text-lg font-bold text-primary mb-2 text-center">Por Produto</h3>
                <p class="text-gray-600 text-center mb-4 text-sm">Relatório detalhado de produto específico</p>
                <button id="openProductModal" class="bg-gradient-to-r from-secondary to-orange-500 text-white py-2 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold transform hover:scale-105">
                    <i class="fas fa-search-plus mr-2"></i>
                    Selecionar Produto
                </button>
            </div>

            <!-- Relatório por Data (Produtos Cadastrados) -->
            <div class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in" style="animation-delay: 0.4s">
                <div class="card-shine"></div>
                <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                    <i class="fas fa-plus-circle text-4xl"></i>
                </div>
                <h3 class="text-lg font-bold text-primary mb-2 text-center">Produtos Cadastrados</h3>
                <p class="text-gray-600 text-center mb-4 text-sm">Relatório de produtos adicionados em período específico</p>
                <button id="openProdutosCadastradosModal" class="bg-gradient-to-r from-secondary to-orange-500 text-white py-2 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold transform hover:scale-105">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    Selecionar Período
                </button>
            </div>

            <!-- Relatório de Produtos sem Código de Barras -->
            <div class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in" style="animation-delay: 0.5s">
                <div class="card-shine"></div>
                <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                    <i class="fas fa-barcode text-4xl"></i>
                </div>
                <h3 class="text-lg font-bold text-primary mb-2 text-center">Sem Código de Barras</h3>
                <p class="text-gray-600 text-center mb-4 text-sm">Relatório detalhado de produtos sem código de barras</p>
                <a href="reports/relatorioscb.php" class="bg-gradient-to-r from-secondary to-orange-500 text-white py-2 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold transform hover:scale-105" target="_blank">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Gerar Relatório
                </a>
            </div>

            <!-- Relatório de Perdas -->
            <div class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in" style="animation-delay: 0.6s">
                <div class="card-shine"></div>
                <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-4xl"></i>
                </div>
                <h3 class="text-lg font-bold text-primary mb-2 text-center">Relatório de Perdas</h3>
                <p class="text-gray-600 text-center mb-4 text-sm">Relatório detalhado de todas as perdas registradas no sistema</p>
                <a href="reports/relatorio_perdas.php" class="bg-gradient-to-r from-secondary to-orange-500 text-white py-2 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold transform hover:scale-105" target="_blank">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Gerar PDF
                </a>
            </div>

            <!-- Relatório por categoria -->
            <div class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in" style="animation-delay: 0.6s">
                <div class="card-shine"></div>
                <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                    <i class="fas fa-tags text-4xl"></i>
                </div>
                <h3 class="text-lg font-bold text-primary mb-2 text-center">Relatório por Categoria</h3>
                <p class="text-gray-600 text-center mb-4 text-sm">Relatório detalhado de produtos por categoria específica</p>
                <button onclick="openCategoryModal()" class="bg-gradient-to-r from-secondary to-orange-500 text-white py-2 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold transform hover:scale-105">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Gerar PDF
                </button>
            </div>
        </div>

    </main>

    <!-- Modal para Seleção de Data -->
    <div id="dateModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" id="closeDateModal">×</button>
            <h2 class="font-heading">
                <i class="fas fa-calendar-alt mr-2 text-secondary"></i>
                Selecionar Período
            </h2>
            <form id="dateForm" action="reports/relatorio_movimentacoes.php" method="GET" target="_blank" class="space-y-4">
                <div class="form-group">
                    <label for="data_inicio" class="font-semibold">
                        <i class="fas fa-play mr-1"></i>
                        Data de Início
                    </label>
                    <input type="date" id="data_inicio" name="data_inicio" required>
                </div>
                <div class="form-group">
                    <label for="data_fim" class="font-semibold">
                        <i class="fas fa-stop mr-1"></i>
                        Data de Fim
                    </label>
                    <input type="date" id="data_fim" name="data_fim" required>
                </div>
                <button type="submit" class="confirm-btn">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Gerar Relatório
                </button>
            </form>
        </div>
    </div>

    <!-- Modal para Seleção de Produto -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" id="closeProductModal">×</button>
            <h2 class="font-heading">
                <i class="fas fa-search mr-2 text-secondary"></i>
                Selecionar Produto
            </h2>
            <form id="productForm" action="reports/relatorio_produto.php" method="GET" target="_blank" class="space-y-4">
                <div class="form-group">
                    <label for="data_inicio" class="font-semibold">
                        <i class="fas fa-play mr-1"></i>
                        Data de Início
                    </label>
                    <input type="date" id="data_inicio_product" name="data_inicio" required>
                </div>
                <div class="form-group">
                    <label for="data_fim" class="font-semibold">
                        <i class="fas fa-stop mr-1"></i>
                        Data de Fim
                    </label>
                    <input type="date" id="data_fim_product" name="data_fim" required>
                </div>
                <div class="form-group">
                    <label for="produto" class="font-semibold">
                        <i class="fas fa-box mr-1"></i>
                        Produto
                    </label>
                    <select id="produto" name="produto" required>
                        <option value="" disabled selected>SELECIONAR PRODUTO</option>
                        <?php
                        $select->modalRelatorio();
                        ?>
                    </select>
                </div>
                <button type="submit" class="confirm-btn">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Gerar Relatório
                </button>
            </form>
        </div>
    </div>

    <!-- Modal para Produtos Cadastrados -->
    <div id="produtosCadastradosModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" id="closeProdutosCadastradosModal">×</button>
            <h2 class="font-heading">
                <i class="fas fa-plus-circle mr-2 text-secondary"></i>
                Selecionar Período
            </h2>
            <form id="produtosCadastradosForm" action="reports/relatorio_produtos_cadastrados.php" method="GET" target="_blank" class="space-y-4">
                <div class="form-group">
                    <label for="data_inicio_cadastrados" class="font-semibold">
                        <i class="fas fa-play mr-1"></i>
                        Data de Início
                    </label>
                    <input type="date" id="data_inicio_cadastrados" name="data_inicio" required>
                </div>
                <div class="form-group">
                    <label for="data_fim_cadastrados" class="font-semibold">
                        <i class="fas fa-stop mr-1"></i>
                        Data de Fim
                    </label>
                    <input type="date" id="data_fim_cadastrados" name="data_fim" required>
                </div>
                <button type="submit" class="confirm-btn">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Gerar Relatório
                </button>
            </form>
        </div>
    </div>

    <!-- Modal para Seleção de Categoria -->
    <div id="categoryModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" id="closeCategoryModal">×</button>
            <h2 class="font-heading">
                <i class="fas fa-tags mr-2 text-secondary"></i>
                Selecionar Categoria
            </h2>
            <form id="categoryForm" action="reports/relatorio_categoria.php" method="POST" target="_blank" class="space-y-4">
                <div class="form-group">
                    <label for="categoria" class="font-semibold">
                        <i class="fas fa-tag mr-1"></i>
                        Categoria do Produto
                    </label>
                    <select id="categoria" name="categoria" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="" disabled selected>SELECIONAR CATEGORIA</option>
                        <option value="informatica">Informática</option>
                        <option value="epi">EPI (Equipamentos de Proteção Individual)</option>
                        <option value="limpeza">Limpeza</option>
                        <option value="escritorio">Material de Escritório</option>
                        <option value="manutencao">Material de Manutenção</option>
                        <option value="seguranca">Segurança</option>
                        <option value="alimentacao">Alimentação</option>
                        <option value="higiene">Higiene</option>
                        <option value="ferramentas">Ferramentas</option>
                        <option value="outros">Outros</option>
                    </select>
                </div>
                <button type="submit" class="confirm-btn">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Gerar Relatório
                </button>
            </form>
        </div>
    </div>

  
    <footer class="bg-gradient-to-r from-primary to-dark text-white py-8 md:py-10 mt-auto relative transition-all duration-300">
        <!-- Efeito de brilho sutil no topo -->
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-secondary to-transparent opacity-30"></div>

        <div class="px-4 md:px-8 transition-all duration-300 ml-0 md:ml-64" id="footerContent">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
                    <!-- Sobre a Escola -->
                    <div class="group">
                        <h3 class="font-heading text-lg md:text-xl font-semibold mb-4 flex items-center text-white group-hover:text-secondary transition-colors duration-300">
                            <i class="fas fa-school mr-3 text-secondary group-hover:scale-110 transition-transform duration-300"></i>
                           CREDE 1
                        </h3>
                        <p class="text-sm md:text-base leading-relaxed text-gray-200 group-hover:text-white transition-colors duration-300">
                            <i class="fas fa-map-marker-alt mr-2 text-secondary"></i>
                            Av. Sen. Virgílio Távora, 1103 - Distrito Industrial I, 
                        </p>
                    </div>

                    <!-- Contato -->
                    <div class="group">
                        <h3 class="font-heading text-lg md:text-xl font-semibold mb-4 flex items-center text-white group-hover:text-secondary transition-colors duration-300">
                            <i class="fas fa-address-book mr-3 text-secondary group-hover:scale-110 transition-transform duration-300"></i>
                            Contato
                        </h3>
                        <div class="space-y-3">
                            <a href="tel:+558533413990" class="flex items-center text-sm md:text-base text-gray-200 hover:text-white transition-colors duration-300 group/item">
                                <i class="fas fa-phone-alt mr-3 text-secondary group-hover/item:scale-110 transition-transform duration-300"></i>
                                (85) 3341-3990
                            </a>
                        
                        </div>
                    </div>

                    <!-- Desenvolvedores -->
                    <div class="group">
                        <h3 class="font-heading text-lg md:text-xl font-semibold mb-4 flex items-center text-white group-hover:text-secondary transition-colors duration-300">
                            <i class="fas fa-code mr-3 text-secondary group-hover:scale-110 transition-transform duration-300"></i>
                            Dev Team
                        </h3>
                        <div class="grid grid-cols-1 gap-3">
                            <a href="#" class="flex items-center text-sm md:text-base text-gray-200 hover:text-white transition-all duration-300 group/item hover:translate-x-1">
                                <i class="fab fa-instagram mr-3 text-secondary group-hover/item:scale-110 transition-transform duration-300"></i>
                                Matheus Felix
                            </a>
                            <a href="#" class="flex items-center text-sm md:text-base text-gray-200 hover:text-white transition-all duration-300 group/item hover:translate-x-1">
                                <i class="fab fa-instagram mr-3 text-secondary group-hover/item:scale-110 transition-transform duration-300"></i>
                                Pedro Uchoa
                            </a>

                        </div>
                    </div>
                </div>

                <!-- Rodapé inferior -->
                <div class="border-t border-white/20 pt-6 mt-8 text-center">
                    <p class="text-sm md:text-base text-gray-300 hover:text-white transition-colors duration-300">
                        © 2024 STGM v1.2.0 | Desenvolvido por alunos EEEP STGM
                    </p>
                </div>
            </div>
        </div>

        <!-- Efeito de brilho sutil na base -->
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-secondary to-transparent opacity-30"></div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar mobile toggle
            const menuButton = document.getElementById('menuButton');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const closeSidebar = document.getElementById('closeSidebar');

            if (menuButton && sidebar) {
                menuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    sidebar.classList.toggle('show');
                    overlay.classList.toggle('hidden');
                    
                    // Mostrar/ocultar o botão do menu
                    if (sidebar.classList.contains('show')) {
                        menuButton.classList.add('hidden');
                    } else {
                        menuButton.classList.remove('hidden');
                    }
                    
                    document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
                });

                // Fechar sidebar ao clicar no overlay
                if (overlay) {
                    overlay.addEventListener('click', function() {
                        sidebar.classList.remove('show');
                        overlay.classList.add('hidden');
                        menuButton.classList.remove('hidden');
                        document.body.style.overflow = '';
                    });
                }

                // Fechar sidebar ao clicar no botão fechar
                if (closeSidebar) {
                    closeSidebar.addEventListener('click', function() {
                        sidebar.classList.remove('show');
                        overlay.classList.add('hidden');
                        menuButton.classList.remove('hidden');
                        document.body.style.overflow = '';
                    });
                }

                // Fechar sidebar ao clicar em um link
                const navLinks = sidebar.querySelectorAll('a');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth <= 768) {
                            sidebar.classList.remove('show');
                            overlay.classList.add('hidden');
                            menuButton.classList.remove('hidden');
                            document.body.style.overflow = '';
                        }
                    });
                });

                // Fechar sidebar ao pressionar ESC
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                        overlay.classList.add('hidden');
                        menuButton.classList.remove('hidden');
                        document.body.style.overflow = '';
                    }
                });
            }

            // Elementos do modal
            const openDateModalBtn = document.getElementById('openDateModal');
            const dateModal = document.getElementById('dateModal');
            const closeDateModalBtn = document.getElementById('closeDateModal');
            const dateForm = document.getElementById('dateForm');

            const openProductModalBtn = document.getElementById('openProductModal');
            const productModal = document.getElementById('productModal');
            const closeProductModalBtn = document.getElementById('closeProductModal');
            const productForm = document.getElementById('productForm');

            const openProdutosCadastradosModalBtn = document.getElementById('openProdutosCadastradosModal');
            const produtosCadastradosModal = document.getElementById('produtosCadastradosModal');
            const closeProdutosCadastradosModalBtn = document.getElementById('closeProdutosCadastradosModal');
            const produtosCadastradosForm = document.getElementById('produtosCadastradosForm');

            const categoryModal = document.getElementById('categoryModal');
            const closeCategoryModalBtn = document.getElementById('closeCategoryModal');
            const categoryForm = document.getElementById('categoryForm');

            // Carregar estatísticas em tempo real
            loadStatistics();

            // Resetar estados dos botões ao carregar a página
            resetButtonStates();

            // Configurar gráfico
            // setupChart(); // Remover chamada antiga

            // Abrir Modal de Data
            openDateModalBtn.addEventListener('click', function() {
                dateModal.classList.add('show');
                const today = new Date();
                const thirtyDaysAgo = new Date(today);
                thirtyDaysAgo.setDate(today.getDate() - 30);
                document.getElementById('data_inicio').value = thirtyDaysAgo.toISOString().split('T')[0];
                document.getElementById('data_fim').value = today.toISOString().split('T')[0];
            });

            // Fechar Modal de Data
            closeDateModalBtn.addEventListener('click', function() {
                dateModal.classList.remove('show');
                dateForm.reset();
            });

            // Abrir Modal de Produto
            openProductModalBtn.addEventListener('click', function() {
                productModal.classList.add('show');
                const today = new Date();
                const thirtyDaysAgo = new Date(today);
                thirtyDaysAgo.setDate(today.getDate() - 30);
                document.getElementById('data_inicio_product').value = thirtyDaysAgo.toISOString().split('T')[0];
                document.getElementById('data_fim_product').value = today.toISOString().split('T')[0];
            });

            // Fechar Modal de Produto
            closeProductModalBtn.addEventListener('click', function() {
                productModal.classList.remove('show');
                productForm.reset();
            });

            // Abrir Modal de Produtos Cadastrados
            openProdutosCadastradosModalBtn.addEventListener('click', function() {
                produtosCadastradosModal.classList.add('show');
                const today = new Date();
                const thirtyDaysAgo = new Date(today);
                thirtyDaysAgo.setDate(today.getDate() - 30);
                document.getElementById('data_inicio_cadastrados').value = thirtyDaysAgo.toISOString().split('T')[0];
                document.getElementById('data_fim_cadastrados').value = today.toISOString().split('T')[0];
            });

            // Fechar Modal de Produtos Cadastrados
            closeProdutosCadastradosModalBtn.addEventListener('click', function() {
                produtosCadastradosModal.classList.remove('show');
                produtosCadastradosForm.reset();
            });

            // Fechar Modal de Categoria
            closeCategoryModalBtn.addEventListener('click', function() {
                categoryModal.classList.remove('show');
                categoryForm.reset();
            });

            // Fechar Modais ao clicar fora
            [dateModal, productModal, produtosCadastradosModal, categoryModal].forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.classList.remove('show');
                        if (modal === dateModal) {
                            dateForm.reset();
                        } else if (modal === productModal) {
                            productForm.reset();
                        } else if (modal === produtosCadastradosModal) {
                            produtosCadastradosForm.reset();
                        } else if (modal === categoryModal) {
                            categoryForm.reset();
                        }
                    }
                });
            });

            // Validação do formulário de data
            dateForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const data_inicio = document.getElementById('data_inicio').value;
                const data_fim = document.getElementById('data_fim').value;

                if (!data_inicio || !data_fim) {
                    showNotification('Por favor, preencha ambas as datas.', 'error');
                    return;
                }

                if (new Date(data_inicio) > new Date(data_fim)) {
                    showNotification('A data de início deve ser anterior à data de fim.', 'error');
                    return;
                }

                // Mostrar loading
                const submitBtn = dateForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="loading-spinner"></span> Gerando...';
                submitBtn.disabled = true;

                // Simular delay e enviar
                setTimeout(() => {
                    dateForm.submit();
                }, 1000);

                // Timeout de segurança para resetar o botão caso algo dê errado
                setTimeout(() => {
                    if (submitBtn.disabled) {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                }, 5000);
            });

            // Validação do formulário de produtos cadastrados
            produtosCadastradosForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const data_inicio = document.getElementById('data_inicio_cadastrados').value;
                const data_fim = document.getElementById('data_fim_cadastrados').value;

                if (!data_inicio || !data_fim) {
                    showNotification('Por favor, preencha ambas as datas.', 'error');
                    return;
                }

                if (new Date(data_inicio) > new Date(data_fim)) {
                    showNotification('A data de início deve ser anterior à data de fim.', 'error');
                    return;
                }

                // Mostrar loading
                const submitBtn = produtosCadastradosForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="loading-spinner"></span> Gerando...';
                submitBtn.disabled = true;

                // Simular delay e enviar
                setTimeout(() => {
                    produtosCadastradosForm.submit();
                }, 1000);

                // Timeout de segurança para resetar o botão caso algo dê errado
                setTimeout(() => {
                    if (submitBtn.disabled) {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                }, 5000);
            });

            // Validação do formulário de produto
            productForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const produto = document.getElementById('produto').value;
                const data_inicio = document.getElementById('data_inicio_product').value;
                const data_fim = document.getElementById('data_fim_product').value;

                if (!produto) {
                    showNotification('Por favor, selecione um produto.', 'error');
                    return;
                }
                if (!data_inicio || !data_fim) {
                    showNotification('Por favor, preencha ambas as datas.', 'error');
                    return;
                }
                if (new Date(data_inicio) > new Date(data_fim)) {
                    showNotification('A data de início deve ser anterior à data de fim.', 'error');
                    return;
                }

                // Mostrar loading
                const submitBtn = productForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="loading-spinner"></span> Gerando...';
                submitBtn.disabled = true;

                // Simular delay e enviar
                setTimeout(() => {
                    productForm.submit();
                }, 1000);

                // Timeout de segurança para resetar o botão caso algo dê errado
                setTimeout(() => {
                    if (submitBtn.disabled) {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                }, 5000);
            });

            // Menu mobile
            const mobileMenuButton = document.querySelector('.mobile-menu-button');
            const headerNav = document.querySelector('.header-nav');
            const menuSpans = document.querySelectorAll('.mobile-menu-button span');

            if (mobileMenuButton && headerNav) {
                mobileMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    headerNav.classList.toggle('show');

                    // Animação para o botão do menu
                    menuSpans.forEach(span => {
                        span.classList.toggle('active');
                    });

                    // Prevenir scroll do body quando menu está aberto
                    document.body.style.overflow = headerNav.classList.contains('show') ? 'hidden' : '';
                });

                // Fechar menu ao clicar em um link
                const navLinks = headerNav.querySelectorAll('a');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        headerNav.classList.remove('show');
                        menuSpans.forEach(span => {
                            span.classList.remove('active');
                        });
                        document.body.style.overflow = '';
                    });
                });

                // Fechar menu ao clicar fora
                document.addEventListener('click', function(e) {
                    if (!headerNav.contains(e.target) && !mobileMenuButton.contains(e.target)) {
                        headerNav.classList.remove('show');
                        menuSpans.forEach(span => {
                            span.classList.remove('active');
                        });
                        document.body.style.overflow = '';
                    }
                });

                // Fechar menu ao pressionar ESC
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && headerNav.classList.contains('show')) {
                        headerNav.classList.remove('show');
                        menuSpans.forEach(span => {
                            span.classList.remove('active');
                        });
                        document.body.style.overflow = '';
                    }
                });
            }

            // Função para carregar estatísticas e gráfico reais
            function loadStatistics() {
                console.log('Carregando estatísticas...');
                fetch('control/controllerEstatisticas.php')
                    .then(response => {
                        console.log('Response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Dados recebidos:', data);
                        if (data.success) {
                            document.getElementById('totalProdutos').textContent = data.estatisticas.total_produtos;
                            document.getElementById('produtosCriticos').textContent = data.estatisticas.produtos_criticos;
                            document.getElementById('totalCategorias').textContent = data.estatisticas.total_categorias;
                            setupChart(data.grafico);
                            console.log('Estatísticas carregadas com sucesso');
                    } else {
                            console.error('Erro nos dados:', data.error);
                            document.getElementById('totalProdutos').textContent = '-';
                            document.getElementById('produtosCriticos').textContent = '-';
                            document.getElementById('totalCategorias').textContent = '-';
                            setupChart({em_estoque: 0, estoque_critico: 0, sem_estoque: 0});
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao carregar estatísticas:', error);
                        document.getElementById('totalProdutos').textContent = '-';
                        document.getElementById('produtosCriticos').textContent = '-';
                        document.getElementById('totalCategorias').textContent = '-';
                        setupChart({em_estoque: 0, estoque_critico: 0, sem_estoque: 0});
                    });
            }

            // Função para resetar estado dos botões
            function resetButtonStates() {
                const submitButtons = document.querySelectorAll('button[type="submit"]');
                submitButtons.forEach(button => {
                    if (button.classList.contains('confirm-btn')) {
                        // Verificar se o botão está em estado de loading
                        if (button.innerHTML.includes('loading-spinner')) {
                            button.innerHTML = '<i class="fas fa-file-pdf mr-2"></i>Gerar Relatório';
                            button.disabled = false;
                        }
                    }
                });
            }

            // Função para configurar gráfico com dados reais
            function setupChart(graficoData) {
                const ctx = document.getElementById('estoqueChart').getContext('2d');
                if (window.estoqueChartInstance) {
                    window.estoqueChartInstance.destroy();
                }
                window.estoqueChartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Em Estoque', 'Estoque Crítico', 'Sem Estoque'],
                        datasets: [{
                            data: [
                                graficoData.em_estoque || 0,
                                graficoData.estoque_critico || 0,
                                graficoData.sem_estoque || 0
                            ],
                            backgroundColor: [
                                '#28A745',
                                '#FFC107',
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
                                    padding: 20,
                                    usePointStyle: true,
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Função para mostrar notificações
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
                
                // Animar entrada
                setTimeout(() => {
                    notification.classList.remove('translate-x-full');
                }, 100);
                
                // Remover após 3 segundos
                setTimeout(() => {
                    notification.classList.add('translate-x-full');
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 3000);
            }

            // Listener para quando a página volta a ficar visível (resolve o bug do loading infinito)
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    // Página voltou a ficar visível, resetar estados dos botões
                    resetButtonStates();
                }
            });

            // Listener para quando a página é carregada novamente
            window.addEventListener('pageshow', function(event) {
                // Se a página foi carregada do cache (back/forward), resetar estados
                if (event.persisted) {
                    resetButtonStates();
                }
            });

            // Listener para quando a página é focada novamente
            window.addEventListener('focus', function() {
                resetButtonStates();
            });

            // Função para abrir modal de categoria
            window.openCategoryModal = function() {
                categoryModal.classList.add('show');
            };

            // Validação do formulário de categoria
            categoryForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const categoria = document.getElementById('categoria').value;

                if (!categoria) {
                    showNotification('Por favor, selecione uma categoria.', 'error');
                    return;
                }

                // Mostrar loading
                const submitBtn = categoryForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="loading-spinner"></span> Gerando...';
                submitBtn.disabled = true;

                // Simular delay e enviar
                setTimeout(() => {
                    categoryForm.submit();
                }, 1000);

                // Timeout de segurança para resetar o botão caso algo dê errado
                setTimeout(() => {
                    if (submitBtn.disabled) {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                }, 5000);
            });

            // Ajustar footer quando sidebar é aberta/fechada no mobile
            const footerContent = document.getElementById('footerContent');
            if (footerContent) {
                const adjustFooter = () => {
                    if (window.innerWidth <= 768) {
                        if (sidebar.classList.contains('show')) {
                            footerContent.style.marginLeft = '0';
                        } else {
                            footerContent.style.marginLeft = '0';
                        }
                    } else {
                        footerContent.style.marginLeft = '16rem'; // 64 * 0.25rem = 16rem
                    }
                };
                
                // Ajustar na inicialização
                adjustFooter();
                
                // Ajustar quando a sidebar é aberta/fechada
                menuButton.addEventListener('click', adjustFooter);
                
                // Ajustar quando a janela é redimensionada
                window.addEventListener('resize', adjustFooter);
            }
        });
    </script>
</body>

</html>