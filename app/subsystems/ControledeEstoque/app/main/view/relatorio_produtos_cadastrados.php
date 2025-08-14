<!DOCTYPE html>
<html lang="pt-BR">
<?php
        require_once('../model/sessions.php');
        $session = new sessions();
        $session->autenticar_session();
        
    ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Produtos Cadastrados - STGM Estoque</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        .sidebar-link {
            position: relative;
            transition: all 0.3s ease;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }

        .sidebar-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.15);
            border-left: 3px solid #FFA500;
        }

        .mobile-menu-button {
            display: none;
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

        @media (max-width: 768px) {
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
                    <img src="../assets/imagens/logostgm.png" alt="Logo STGM" class="h-12 mr-3 transition-transform hover:scale-105">
                    <span class="text-white font-heading text-lg font-semibold">STGM Estoque</span>
                </div>
            </div>
            
            <!-- Menu de navegação -->
            <nav class="flex-1 p-4 space-y-2">
                <a href="paginainicial.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                    <i class="fas fa-home mr-3 text-lg"></i>
                    <span>Início</span>
                </a>
                <a href="estoque.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                    <i class="fas fa-boxes mr-3 text-lg"></i>
                    <span>Estoque</span>
                </a>
                <a href="adicionarproduto.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
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
            
            <!-- Botão de fechar sidebar no mobile -->
            <div class="p-4 border-t border-white/20 md:hidden">
                <button class="w-full bg-white/10 hover:bg-white/20 text-white py-2 px-4 rounded-lg transition-all duration-200" id="closeSidebar">
                    <i class="fas fa-times mr-2"></i>
                    Fechar Menu
                </button>
            </div>
        </div>
    </div>
    
    <!-- Botão de menu mobile -->
    <button class="fixed top-4 left-4 z-50 md:hidden bg-primary text-white p-3 rounded-lg shadow-lg hover:bg-primary/90 transition-all duration-200" id="menuButton">
        <i class="fas fa-bars text-lg"></i>
    </button>
    
    <!-- Overlay para mobile -->
    <div class="fixed inset-0 bg-black/50 z-40 md:hidden hidden" id="overlay"></div>

    <!-- Main content -->
    <main class="ml-64 px-4 py-8 md:py-12 flex-1 transition-all duration-300">
        <!-- Título da Página -->
        <div class="text-center mb-8">
            <h1 class="text-primary text-3xl md:text-4xl font-bold mb-4 page-title tracking-tight font-heading">
                <i class="fas fa-plus-circle mr-3 text-secondary"></i>
                RELATÓRIO DE PRODUTOS CADASTRADOS
            </h1>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                Período: <span id="periodo" class="font-semibold text-primary"></span>
            </p>
        </div>

        <!-- Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stats-card bg-white rounded-xl shadow-card p-6 text-center border-2 border-primary">
                <div class="text-3xl font-bold text-success mb-2" id="totalProdutos">-</div>
                <div class="text-gray-600 font-medium">Total de Produtos</div>
            </div>
            <div class="stats-card bg-white rounded-xl shadow-card p-6 text-center border-2 border-primary">
                <div class="text-3xl font-bold text-info mb-2" id="totalCategorias">-</div>
                <div class="text-gray-600 font-medium">Categorias</div>
            </div>
            <div class="stats-card bg-white rounded-xl shadow-card p-6 text-center border-2 border-primary">
                <div class="text-3xl font-bold text-secondary mb-2" id="estoqueTotal">-</div>
                <div class="text-gray-600 font-medium">Estoque Total</div>
            </div>
        </div>

        <!-- Tabela -->
        <div class="bg-white rounded-xl shadow-card overflow-hidden border-2 border-primary">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-primary to-primary/90">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-table mr-3"></i>
                    Produtos Cadastrados no Período
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">
                                <i class="fas fa-barcode mr-2"></i>Barcode
                            </th>
                            <th class="px-4 py-3 text-left font-semibold">
                                <i class="fas fa-box mr-2"></i>Nome do Produto
                            </th>
                            <th class="px-4 py-3 text-center font-semibold">
                                <i class="fas fa-sort-numeric-up mr-2"></i>Quantidade
                            </th>
                            <th class="px-4 py-3 text-left font-semibold">
                                <i class="fas fa-tags mr-2"></i>Categoria
                            </th>
                            <th class="px-4 py-3 text-center font-semibold">
                                <i class="fas fa-calendar-plus mr-2"></i>Data Cadastro
                            </th>
                        </tr>
                    </thead>
                    <tbody id="tabelaProdutos">
                        <!-- Dados serão carregados via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mensagem de carregamento -->
        <div id="loading" class="text-center py-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary/10 rounded-full mb-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            </div>
            <p class="text-gray-600 font-medium">Carregando dados...</p>
        </div>

        <!-- Mensagem de erro -->
        <div id="error" class="hidden text-center py-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
            </div>
            <p class="text-red-600 font-medium">Erro ao carregar os dados. Tente novamente.</p>
        </div>

        <!-- Mensagem de dados vazios -->
        <div id="empty" class="hidden text-center py-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                <i class="fas fa-inbox text-gray-400 text-2xl"></i>
            </div>
            <p class="text-gray-600 font-medium">Nenhum produto cadastrado encontrado para o período selecionado.</p>
        </div>

        <!-- Botões de ação -->
        <div class="mt-12 flex justify-center w-full gap-6">
            <a href="relatorios.php" class="group">
                <button class="bg-gradient-to-r from-secondary to-orange-500 text-white font-bold py-4 px-8 rounded-xl hover:from-orange-500 hover:to-secondary transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center">
                    <i class="fas fa-arrow-left mr-3 group-hover:-translate-x-1 transition-transform duration-300"></i>
                    Voltar aos Relatórios
                </button>
            </a>
            <button onclick="downloadPDF()" class="bg-gradient-to-r from-primary to-primary/90 text-white font-bold py-4 px-8 rounded-xl hover:from-primary/90 hover:to-primary transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center">
                <i class="fas fa-download mr-3"></i>
                Download PDF
            </button>
        </div>
    </main>

    <footer class="bg-gradient-to-r from-primary to-dark text-white py-8 md:py-10 mt-auto relative transition-all duration-300">
        <!-- Efeito de brilho sutil no topo -->
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-secondary to-transparent opacity-30"></div>
        
        <div class="ml-64 px-4 md:px-8 transition-all duration-300" id="footerContent">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
                    <!-- Sobre a Escola -->
                    <div class="group">
                        <h3 class="font-heading text-lg md:text-xl font-semibold mb-4 flex items-center text-white group-hover:text-secondary transition-colors duration-300">
                            <i class="fas fa-school mr-3 text-secondary group-hover:scale-110 transition-transform duration-300"></i>
                            EEEP STGM
                        </h3>
                        <p class="text-sm md:text-base leading-relaxed text-gray-200 group-hover:text-white transition-colors duration-300">
                            <i class="fas fa-map-marker-alt mr-2 text-secondary"></i>
                            AV. Marta Maria Carvalho Nojoza, SN<br>
                            Maranguape - CE
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
                            <a href="mailto:eeepsantariamata@gmail.com" class="flex items-center text-sm md:text-base text-gray-200 hover:text-white transition-colors duration-300 group/item">
                                <i class="fas fa-envelope mr-3 text-secondary group-hover/item:scale-110 transition-transform duration-300"></i>
                                eeepsantariamata@gmail.com
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
            // Menu mobile
            const menuButton = document.getElementById('menuButton');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const closeSidebar = document.getElementById('closeSidebar');

            if (menuButton && sidebar) {
                menuButton.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                    overlay.classList.toggle('hidden');
                });

                if (closeSidebar) {
                    closeSidebar.addEventListener('click', function() {
                        sidebar.classList.add('-translate-x-full');
                        overlay.classList.add('hidden');
                    });
                }

                if (overlay) {
                    overlay.addEventListener('click', function() {
                        sidebar.classList.add('-translate-x-full');
                        overlay.classList.add('hidden');
                    });
                }
            }

            // Obter parâmetros da URL
            const urlParams = new URLSearchParams(window.location.search);
            const dataInicio = urlParams.get('data_inicio');
            const dataFim = urlParams.get('data_fim');

            // Exibir período
            if (dataInicio && dataFim) {
                const inicio = new Date(dataInicio).toLocaleDateString('pt-BR');
                const fim = new Date(dataFim).toLocaleDateString('pt-BR');
                document.getElementById('periodo').textContent = `${inicio} a ${fim}`;
            }

            // Carregar dados
            async function carregarDados() {
                try {
                    const response = await fetch(`../control/controllerRelatorioProdutosCadastrados.php?data_inicio=${dataInicio}&data_fim=${dataFim}&format=json`);
                    
                    if (!response.ok) {
                        throw new Error('Erro na requisição');
                    }

                    const data = await response.json();
                    
                    if (data.success) {
                        if (data.produtos && data.produtos.length > 0) {
                            exibirDados(data.produtos);
                            calcularEstatisticas(data.produtos);
                        } else {
                            document.getElementById('loading').classList.add('hidden');
                            document.getElementById('empty').classList.remove('hidden');
                        }
                    } else {
                        throw new Error(data.error || 'Erro desconhecido');
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    document.getElementById('loading').classList.add('hidden');
                    document.getElementById('error').classList.remove('hidden');
                }
            }

            function exibirDados(produtos) {
                const tbody = document.getElementById('tabelaProdutos');
                tbody.innerHTML = '';

                produtos.forEach((produto, index) => {
                    const row = document.createElement('tr');
                    row.className = index % 2 === 0 ? 'bg-gray-50 hover:bg-gray-100' : 'bg-white hover:bg-gray-50';
                    row.classList.add('transition-colors', 'duration-200');
                    
                    const dataCadastro = new Date(produto.data).toLocaleString('pt-BR');
                    
                    row.innerHTML = `
                        <td class="px-4 py-3 font-mono text-sm text-primary font-medium">${produto.barcode}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">${produto.nome_produto}</td>
                        <td class="px-4 py-3 text-center font-bold text-success bg-green-50 rounded-lg mx-2">${produto.quantidade}</td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                ${produto.natureza}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-gray-600 font-medium">${dataCadastro}</td>
                    `;
                    
                    tbody.appendChild(row);
                });

                document.getElementById('loading').classList.add('hidden');
            }

            function calcularEstatisticas(produtos) {
                const totalProdutos = produtos.length;
                const categorias = [...new Set(produtos.map(p => p.natureza))];
                const estoqueTotal = produtos.reduce((sum, p) => sum + parseInt(p.quantidade), 0);

                document.getElementById('totalProdutos').textContent = totalProdutos;
                document.getElementById('totalCategorias').textContent = categorias.length;
                document.getElementById('estoqueTotal').textContent = estoqueTotal;
            }

            function downloadPDF() {
                window.open(`../control/controllerRelatorioProdutosCadastrados.php?data_inicio=${dataInicio}&data_fim=${dataFim}&pdf=1`, '_blank');
            }

            // Carregar dados quando a página carregar
            carregarDados();
        });
    </script>
</body>
</html> 