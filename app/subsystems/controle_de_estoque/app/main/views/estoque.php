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
    <title>Visualizar Estoque</title>
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
                        white: '#FFFFFF'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Poppins', 'sans-serif']
                    },
                    boxShadow: {
                        card: '0 10px 15px -3px rgba(0, 90, 36, 0.1), 0 4px 6px -2px rgba(0, 90, 36, 0.05)',
                        'card-hover': '0 20px 25px -5px rgba(0, 90, 36, 0.2), 0 10px 10px -5px rgba(0, 90, 36, 0.1)'
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; scroll-behavior: smooth; background-color: #F8FAF9; }
        .gradient-bg { background: linear-gradient(135deg, #005A24 0%, #1A3C34 100%); }
        .page-title { position: relative; display: inline-block; }
        .page-title::after { content: ''; position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%); width: 80px; height: 3px; background-color: #FFA500; border-radius: 3px; }
        .header-nav-link { position: relative; transition: all 0.3s ease; font-weight: 500; padding: 0.5rem 1rem; border-radius: 0.5rem; }
        .header-nav-link:hover { background-color: rgba(255,255,255,0.1); }
        .header-nav-link::after { content: ''; position: absolute; bottom: -2px; left: 50%; width: 0; height: 2px; background-color: #FFA500; transition: all 0.3s ease; transform: translateX(-50%); }
        .header-nav-link:hover::after, .header-nav-link.active::after { width: 80%; }
        .header-nav-link.active { background-color: rgba(255,255,255,0.15); }
        .mobile-menu-button { display: none; }
        
        /* Estilos para a sidebar */
        .sidebar-link {
            transition: all 0.3s ease;
            border-radius: 0.5rem;
        }
        
        .sidebar-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(0.5rem);
        }
        
        .sidebar-link.active {
            background-color: rgba(255, 165, 0, 0.2);
            color: #FFA500;
        }
        
        /* Responsividade da sidebar */
        @media (max-width: 768px) {
            #sidebar {
                transform: translateX(-100%);
            }
            
            #sidebar.show {
                transform: translateX(0);
            }
            
            main {
                margin-left: 0 !important;
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
        }
        .desktop-table { display: block; width: 100%; }
        .mobile-cards { display: none; }
        @media screen and (max-width: 768px) { .desktop-table { display: none; } .mobile-cards { display: flex; flex-direction: column; gap: 0.75rem; margin-top: 1rem; padding: 0 0.5rem; width: 100%; } .card-item { margin-bottom: 0.75rem; } .categoria-header { margin-top: 1.5rem; margin-bottom: 0.75rem; } }
        .card-item { transition: all 0.3s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card-item:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .quantidade-critica { color: #FF0000; font-weight: bold; }
        .max-w-5xl { max-width: 64rem; width: 100%; }
        .flex-1.w-full { max-width: 100%; }
        #exportarBtn { margin-top: 1.5rem; }
    </style>
</head>
<body class="min-h-screen flex flex-col font-sans bg-light">
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
                <a href="estoque.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2 active">
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
                <a href="relatorios.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
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
        <div class="text-center mb-10">
            <h1 class="text-primary text-3xl md:text-4xl font-bold mb-8 md:mb-6 text-center page-title tracking-tight font-heading inline-block mx-auto">VISUALIZAR ESTOQUE</h1>
        </div>
        <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4 max-w-5xl mx-auto">
            <div class="flex-1 w-full">
                <input type="text" id="pesquisar" placeholder="Pesquisar produto..." class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
            </div>
            <div class="flex gap-2 flex-wrap justify-center items-center">
                <select id="filtroCategoria" class="px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                    <option value="">Todas as categorias</option>
                    <option value="limpeza">Limpeza</option>
                    <option value="expedientes">Expedientes</option>
                    <option value="manutencao">Manutenção</option>
                    <option value="eletrico">Elétrico</option>
                    <option value="hidraulico">Hidráulico</option>
                    <option value="educacao_fisica">Educação Física</option>
                    <option value="epi">EPI</option>
                    <option value="copa_e_cozinha">Copa e Cozinha</option>
                    <option value="informatica">Informática</option>
                    <option value="ferramentas">Ferramentas</option>
                </select>
                <a href="perdas.php">
                    <button class="bg-red-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-red-700 transition-colors flex items-center shadow-md">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Perdas
                    </button>
                </a>
                <button onclick="abrirModalCategoria()" class="bg-primary text-white font-bold py-3 px-4 rounded-lg hover:bg-primary/90 transition-colors flex items-center shadow-md">
                    <i class="fas fa-plus mr-2"></i>
                    Nova Categoria
                </button>
            </div>
        </div>
        <!-- Tabela para desktop -->
        <div class="desktop-table bg-white rounded-xl shadow-lg overflow-hidden border-2 border-primary max-w-5xl mx-auto">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">Barcode</th>
                            <th class="py-3 px-4 text-left">Nome</th>
                            <th class="py-3 px-4 text-left">Quantidade</th>
                            <th class="py-3 px-4 text-left">Categoria</th>
                            <th class="py-3 px-4 text-left">Validade</th>
                            <th class="py-3 px-4 text-left">Data Cadastro</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaEstoque">
                        <?php
                        $dados = $select->select_produtos();
                        if (count($dados) > 0) {
                            foreach ($dados as $produto) {
                                $quantidadeClass = $produto['quantidade'] <= 5 ? 'text-red-600 font-bold' : 'text-gray-700';
                                $rowClass = $produto['quantidade'] <= 5 ? 'border-b border-gray-200 hover:bg-red-50 bg-red-50' : 'border-b border-gray-200 hover:bg-gray-50';
                                ?>
                                <tr class="<?=$rowClass?>">
                                    <td class="py-3 px-4"><?=htmlspecialchars($produto['barcode'])?></td>
                                    <td class="py-3 px-4"><?=htmlspecialchars($produto['nome_produto'])?></td>
                                    <td class="py-3 px-4 <?=$quantidadeClass?>"><?=htmlspecialchars($produto['quantidade'])?></td>
                                    <td class="py-3 px-4"><?=htmlspecialchars($produto['categoria'])?></td>
                                    <td class="py-3 px-4"><?= htmlspecialchars($produto['vencimento'] == '' ? 'Sem vencimento' : $produto['vencimento'])?></td>
                                    <td class="py-3 px-4"><?= date('d/m/Y H:i', strtotime($produto['data']))?></td>
                                </tr>
                            <?php 
                            }
                        } else {
                            ?>
                            <tr><td colspan="6" class="py-4 px-4 text-center text-gray-500">Nenhum produto encontrado</td></tr>
                        <?php } ?>
                        
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Cards para mobile -->
        <div class="mobile-cards mt-6 max-w-5xl mx-auto">
            <?php
            if ($dados && count($dados) > 0) {
                $categoriaAtual = '';
                foreach ($dados as $produto) {
                    if ($categoriaAtual != $produto['natureza']) {
                        $categoriaAtual = $produto['natureza'];
                        echo '<div class="bg-primary text-white font-bold py-2 px-4 rounded-lg mt-6 mb-3 categoria-header"><h3 class="text-sm uppercase tracking-wider">' . htmlspecialchars(ucfirst($produto['natureza'])) . '</h3></div>';
                    }
                    $quantidadeClass = $produto['quantidade'] <= 5 ? 'quantidade-critica' : '';
                    echo '<div class="card-item bg-white shadow rounded-lg border-l-4 border-primary p-4 mb-3">';
                    echo '<div class="flex justify-between items-start w-full">';
                    echo '<div class="flex-1">';
                    echo '<h3 class="font-bold text-lg text-primary mb-1">' . htmlspecialchars($produto['nome_produto']) . '</h3>';
                    echo '<div class="flex flex-col space-y-1">';
                    echo '<p class="text-sm text-gray-500 flex items-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" /></svg><span>' . htmlspecialchars($produto['barcode']) . '</span></p>';
                    echo '<p class="text-sm flex items-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3v8a3 3 0 003 3z" /></svg><span class="' . $quantidadeClass . '">Quantidade: ' . htmlspecialchars($produto['quantidade']) . '</span></p>';
                    echo '<p class="text-sm text-gray-500 flex items-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg><span>Cadastrado: ' . date('d/m/Y H:i', strtotime($produto['data'])) . '</span></p>';
                    if ($produto['vencimento'] != '') {
                        echo '<p class="text-sm text-gray-500 flex items-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span>Vencimento: ' . htmlspecialchars($produto['vencimento']) . '</span></p>';
                    }
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="text-center text-gray-500 py-8">Nenhum produto encontrado</div>';
            }
            ?>
        </div>
       
        <!-- Botão Voltar ao Topo -->
        <button class="back-to-top hidden fixed bottom-6 right-6 z-50 bg-secondary hover:bg-secondary/90 text-white w-12 h-12 rounded-full shadow-lg transition-all duration-300 flex items-center justify-center group">
            <i class="fas fa-chevron-up text-lg group-hover:scale-110 transition-transform duration-300"></i>
        </button>

        <!-- Modal para Nova Categoria -->
        <div id="modalCategoria" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-primary flex items-center">
                        <i class="fas fa-tags mr-3 text-secondary"></i>
                        Nova Categoria
                    </h2>
                    <button onclick="fecharModalCategoria()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="formCategoria" class="space-y-6">
                    <div>
                        <label for="nomeCategoria" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nome da Categoria
                        </label>
                        <input type="text" id="nomeCategoria" name="nomeCategoria" required
                               class="w-full px-4 py-3 border-2 border-primary/30 rounded-lg focus:outline-none focus:ring-1 focus:ring-secondary focus:border-secondary transition-all duration-200"
                               placeholder="Ex: Informática">
                    </div>
                    
                    <div class="flex gap-3 pt-4">
                        <button type="button" onclick="fecharModalCategoria()" 
                                class="flex-1 bg-gray-300 text-gray-700 font-semibold py-2 px-2 rounded-lg hover:bg-gray-400 transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-md">
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="flex-1 bg-gradient-to-r from-secondary to-orange-500 text-white font-semibold py-3 px-4 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl">
                            <i class="fas fa-save mr-2"></i>
                            Salvar Categoria
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>


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
    }

    // Back to top button visibility and functionality
    const backToTop = document.querySelector('.back-to-top');
    if (backToTop) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                backToTop.classList.add('visible');
                backToTop.classList.remove('hidden');
            } else {
                backToTop.classList.remove('visible');
                backToTop.classList.add('hidden');
            }
        });
        
        // Funcionalidade do botão voltar ao topo
        backToTop.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

                // Funcionalidade de pesquisa
    const pesquisarInput = document.getElementById('pesquisar');
    const filtroCategoria = document.getElementById('filtroCategoria');
    const tabelaEstoque = document.getElementById('tabelaEstoque');
        
        function filtrarProdutos() {
            const termo = pesquisarInput.value.toLowerCase();
                    const categoria = filtroCategoria.value;
                    
                    // Implementar lógica de filtro aqui
                    console.log('Filtrando produtos:', { termo, categoria });
                }

                if (pesquisarInput) {
                    pesquisarInput.addEventListener('input', filtrarProdutos);
                }

                if (filtroCategoria) {
                    filtroCategoria.addEventListener('change', filtrarProdutos);
                }

                // Formulário de nova categoria
                const formCategoria = document.getElementById('formCategoria');
                if (formCategoria) {
                    formCategoria.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        const nomeCategoria = document.getElementById('nomeCategoria').value.trim();
                        const descricaoCategoria = document.getElementById('descricaoCategoria').value.trim();
                        
                        if (!nomeCategoria) {
                            alert('Por favor, insira o nome da categoria');
            return;
        }
        
                        // Aqui você pode implementar a lógica para salvar a categoria
                        // Por exemplo, fazer uma requisição AJAX para o controller
                        console.log('Salvando categoria:', { nomeCategoria, descricaoCategoria });
                        
                        // Simular salvamento (substitua por sua lógica real)
                        alert('Categoria salva com sucesso!');
                        
                        // Fechar modal e limpar formulário
                        fecharModalCategoria();
                        formCategoria.reset();
                        
                        // Opcional: recarregar a página ou atualizar a lista de categorias
                        // location.reload();
                    });
                }
            });

            // Funções para controlar o modal
            function abrirModalCategoria() {
                const modal = document.getElementById('modalCategoria');
                const modalContent = document.getElementById('modalContent');
                
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                
                // Animar entrada
        setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
                
                // Focar no primeiro campo
                document.getElementById('nomeCategoria').focus();
    }

    function fecharModalCategoria() {
        const modal = document.getElementById('modalCategoria');
                const modalContent = document.getElementById('modalContent');
                
                // Animar saída
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
                
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }, 300);
                
                // Limpar formulário
                document.getElementById('formCategoria').reset();
            }

            // Fechar modal ao clicar fora
            document.getElementById('modalCategoria').addEventListener('click', function(e) {
                if (e.target === this) {
            fecharModalCategoria();
        }
    });

            // Fechar modal com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
                    const modal = document.getElementById('modalCategoria');
                    if (!modal.classList.contains('hidden')) {
                fecharModalCategoria();
            }
        }
});
</script>
    </main>
</body>
</html>