<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Movimentações - STGM Estoque</title>
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

        .max-w-5xl {
            max-width: 64rem;
            width: 100%;
        }

        .desktop-table {
            display: block;
            width: 100%;
        }

        .mobile-cards {
            display: none;
        }

        @media screen and (max-width: 768px) {
            .desktop-table {
                display: none;
            }
            .mobile-cards {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
                margin-top: 1rem;
                padding: 0 0.5rem;
                width: 100%;
            }
            .card-item {
                margin-bottom: 0.75rem;
            }
        }

        .card-item {
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .card-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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
                <i class="fas fa-exchange-alt mr-3 text-secondary"></i>
                RELATÓRIO DE MOVIMENTAÇÕES
            </h1>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                Período: <span id="periodo" class="font-semibold text-primary"></span>
            </p>
        </div>

        <!-- Tabela para desktop -->
        <div class="desktop-table bg-white rounded-xl shadow-card overflow-hidden border-2 border-primary max-w-5xl mx-auto">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-primary to-primary/90">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-table mr-3"></i>
                    Detalhamento das Movimentações
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="px-4 py-3 text-center font-semibold">
                                <i class="fas fa-hashtag mr-2"></i>ID
                            </th>
                            <th class="px-4 py-3 text-left font-semibold">
                                <i class="fas fa-barcode mr-2"></i>Barcode
                            </th>
                            <th class="px-4 py-3 text-left font-semibold">
                                <i class="fas fa-box mr-2"></i>Produto
                            </th>
                            <th class="px-4 py-3 text-center font-semibold">
                                <i class="fas fa-sort-numeric-down mr-2"></i>Qtd. Retirada
                            </th>
                            <th class="px-4 py-3 text-left font-semibold">
                                <i class="fas fa-user mr-2"></i>Responsável
                            </th>
                            <th class="px-4 py-3 text-left font-semibold">
                                <i class="fas fa-id-badge mr-2"></i>Cargo
                            </th>
                            <th class="px-4 py-3 text-center font-semibold">
                                <i class="fas fa-calendar-alt mr-2"></i>Data
                            </th>
                        </tr>
                    </thead>
                    <tbody id="tabelaMovimentacoes">
                        <!-- Dados serão carregados via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Cards para mobile -->
        <div class="mobile-cards mt-6 max-w-5xl mx-auto">
            <!-- Os cards serão gerados via JavaScript -->
        </div>

        <!-- Mensagem de carregamento -->
        <div id="loading" class="text-center py-8 max-w-5xl mx-auto">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary/10 rounded-full mb-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            </div>
            <p class="text-gray-600 font-medium">Carregando dados...</p>
        </div>

        <!-- Mensagem de erro -->
        <div id="error" class="hidden text-center py-8 max-w-5xl mx-auto">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
            </div>
            <p class="text-red-600 font-medium">Erro ao carregar os dados. Tente novamente.</p>
        </div>

        <!-- Mensagem de dados vazios -->
        <div id="empty" class="hidden text-center py-8 max-w-5xl mx-auto">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                <i class="fas fa-inbox text-gray-400 text-2xl"></i>
            </div>
            <p class="text-gray-600 font-medium">Nenhuma movimentação encontrada para o período selecionado.</p>
        </div>

        <!-- Botões de ação -->
        <div class="mt-12 flex justify-center w-full gap-6">
            <a href="relatorios.php" class="group">
                <button class="bg-gradient-to-r from-secondary to-orange-500 text-white font-bold py-4 px-8 rounded-xl hover:from-orange-500 hover:to-secondary transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center">
                    <i class="fas fa-arrow-left mr-3 group-hover:-translate-x-1 transition-transform duration-300"></i>
                    Voltar aos Relatórios
                </button>
            </a>
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
                    console.log('Iniciando carregamento de dados...');
                    console.log('Data início:', dataInicio);
                    console.log('Data fim:', dataFim);
                    
                    if (!dataInicio || !dataFim) {
                        throw new Error('Parâmetros de data não fornecidos');
                    }

                    const url = `../control/controllerRelatorioData.php?data_inicio=${dataInicio}&data_fim=${dataFim}&format=json`;
                    console.log('URL da requisição:', url);
                    
                    const response = await fetch(url);
                    console.log('Status da resposta:', response.status);
                    console.log('Headers da resposta:', response.headers);
                    
                    if (!response.ok) {
                        throw new Error(`Erro HTTP: ${response.status} - ${response.statusText}`);
                    }

                    const responseText = await response.text();
                    console.log('Resposta bruta:', responseText);
                    
                    let data;
                    try {
                        data = JSON.parse(responseText);
                    } catch (parseError) {
                        console.error('Erro ao fazer parse do JSON:', parseError);
                        throw new Error('Resposta do servidor não é um JSON válido');
                    }
                    
                    console.log('Dados parseados:', data);
                    
                    if (data.success) {
                        if (data.movimentacoes && data.movimentacoes.length > 0) {
                            console.log('Exibindo dados:', data.movimentacoes.length, 'movimentações');
                            exibirDados(data.movimentacoes);
                        } else {
                            console.log('Nenhuma movimentação encontrada');
                            document.getElementById('loading').classList.add('hidden');
                            document.getElementById('empty').classList.remove('hidden');
                        }
                    } else {
                        throw new Error(data.error || 'Erro desconhecido retornado pelo servidor');
                    }
                } catch (error) {
                    console.error('Erro detalhado:', error);
                    console.error('Stack trace:', error.stack);
                    document.getElementById('loading').classList.add('hidden');
                    document.getElementById('error').classList.remove('hidden');
                    
                    // Mostrar detalhes do erro para debug
                    const errorElement = document.getElementById('error');
                    if (errorElement) {
                        const errorText = errorElement.querySelector('p');
                        if (errorText) {
                            errorText.innerHTML = `Erro ao carregar os dados: ${error.message}<br><small class="text-red-400">Verifique o console para mais detalhes</small>`;
                        }
                    }
                }
            }

            function exibirDados(movimentacoes) {
                const tbody = document.getElementById('tabelaMovimentacoes');
                const mobileCards = document.querySelector('.mobile-cards');
                tbody.innerHTML = '';
                mobileCards.innerHTML = '';

                movimentacoes.forEach((mov, index) => {
                    // Linha da tabela desktop
                    const row = document.createElement('tr');
                    row.className = index % 2 === 0 ? 'bg-gray-50 hover:bg-gray-100' : 'bg-white hover:bg-gray-50';
                    row.classList.add('transition-colors', 'duration-200');
                    
                    const dataMov = new Date(mov.datareg).toLocaleString('pt-BR');
                    
                    row.innerHTML = `
                        <td class="px-4 py-3 text-center font-mono text-sm text-gray-600">${mov.id}</td>
                        <td class="px-4 py-3 font-mono text-sm text-primary font-medium">${mov.barcode_produto || 'N/A'}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">${mov.nome_produto || 'N/A'}</td>
                        <td class="px-4 py-3 text-center font-bold text-red-600 bg-red-50 rounded-lg mx-2">${mov.quantidade_retirada}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">${mov.nome_responsavel || 'N/A'}</td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                ${mov.cargo || 'N/A'}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-gray-600 font-medium">${dataMov}</td>
                    `;
                    
                    tbody.appendChild(row);

                    // Card mobile
                    const card = document.createElement('div');
                    card.className = 'card-item bg-white shadow rounded-lg border-l-4 border-primary p-4 mb-3';
                    
                    card.innerHTML = `
                        <div class="flex justify-between items-start w-full">
                            <div class="flex-1">
                                <h3 class="font-bold text-lg text-primary mb-2">${mov.nome_produto || 'N/A'}</h3>
                                <div class="flex flex-col space-y-2">
                                    <p class="text-sm text-gray-500 flex items-center">
                                        <i class="fas fa-hashtag text-primary mr-2"></i>
                                        <span class="font-mono">ID: ${mov.id}</span>
                                    </p>
                                    <p class="text-sm text-gray-500 flex items-center">
                                        <i class="fas fa-barcode text-primary mr-2"></i>
                                        <span class="font-mono">${mov.barcode_produto || 'N/A'}</span>
                                    </p>
                                    <p class="text-sm flex items-center">
                                        <i class="fas fa-sort-numeric-down text-red-500 mr-2"></i>
                                        <span class="font-bold text-red-600">Qtd. Retirada: ${mov.quantidade_retirada}</span>
                                    </p>
                                    <p class="text-sm text-gray-500 flex items-center">
                                        <i class="fas fa-user text-primary mr-2"></i>
                                        <span>${mov.nome_responsavel || 'N/A'}</span>
                                    </p>
                                    <p class="text-sm text-gray-500 flex items-center">
                                        <i class="fas fa-id-badge text-primary mr-2"></i>
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">${mov.cargo || 'N/A'}</span>
                                    </p>
                                    <p class="text-sm text-gray-500 flex items-center">
                                        <i class="fas fa-calendar-alt text-primary mr-2"></i>
                                        <span>${dataMov}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    mobileCards.appendChild(card);
                });

                document.getElementById('loading').classList.add('hidden');
            }

            // Carregar dados quando a página carregar
            carregarDados();
        });
    </script>
</body>
</html> 