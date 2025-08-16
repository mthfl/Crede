<?php 
require_once('../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once('../model/functionsViews.php');
$select = new select();

// Processar mensagens de URL
$mensagem = '';
$tipoMensagem = '';
$mostrarAlerta = false;

if (isset($_GET['success']) && $_GET['success'] == '1' && isset($_GET['message'])) {
    $mensagem = $_GET['message'];
    $tipoMensagem = 'success';
    $mostrarAlerta = true;
} elseif (isset($_GET['error']) && $_GET['error'] == '1' && isset($_GET['message'])) {
    $mensagem = $_GET['message'];
    $tipoMensagem = 'error';
    $mostrarAlerta = true;
} elseif (isset($_GET['mensagem'])) {
    $mensagem = $_GET['mensagem'];
    $tipoMensagem = 'success';
    $mostrarAlerta = true;
} elseif (isset($_GET['erro'])) {
    $mensagem = $_GET['erro'];
    $tipoMensagem = 'error';
    $mostrarAlerta = true;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Perdas - Estoque</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
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
        .max-w-5xl { max-width: 64rem; width: 100%; }
        .flex-1.w-full { max-width: 100%; }
        
        /* Estilos para o Select2 personalizado */
        .select2-container--default .select2-selection--single {
            height: 48px;
            border: 2px solid rgba(0, 90, 36, 0.3);
            border-radius: 8px;
            background-color: white;
            transition: all 0.2s ease;
        }
        
        .select2-container--default .select2-selection--single:hover {
            border-color: rgba(0, 90, 36, 0.5);
        }
        
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #FFA500;
            box-shadow: 0 0 0 3px rgba(255, 165, 0, 0.1);
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 46px;
            padding-left: 16px;
            color: #374151;
            font-weight: 500;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px;
            width: 30px;
        }
        
        .select2-dropdown {
            border: 2px solid #005A24;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #005A24;
            color: white;
        }
        
        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #E6F4EA;
            color: #005A24;
        }
        
        /* Remover ícones padrão do Select2 */
        .select2-container--default .select2-selection--single .select2-selection__clear {
            display: none !important;
        }
        
        .select2-container--default .select2-results__option .select2-results__option__icon {
            display: none !important;
        }
        
        /* Estilizar o ícone de seta */
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            display: none !important;
        }
        
        /* Animações */
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Estilos para campos com erro */
        .border-red-500 {
            border-color: #EF4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }
        
        /* Melhorias para o campo de quantidade */
        #quantidade_perdida:focus {
            border-color: #FFA500;
            box-shadow: 0 0 0 3px rgba(255, 165, 0, 0.1);
        }
        
        /* Estilos para as informações do produto */
        #produto-info {
            transition: all 0.3s ease;
        }
        
        #estoque-alerta {
            transition: all 0.3s ease;
        }
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
                <a href="index.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2 active">
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
    <main class="ml-64 px-4 py-8 md:py-12 flex-1 transition-all duration-300">
        <div class="text-center mb-10">
            <h1 class="text-primary text-3xl md:text-4xl font-bold mb-8 md:mb-6 text-center page-title tracking-tight font-heading inline-block mx-auto">GERENCIAR PERDAS</h1>
        </div>
        
        <!-- Formulário para registrar perda -->
        <div class="bg-white rounded-xl shadow-lg p-8 max-w-4xl w-full border-2 border-primary mx-auto mb-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
                </div>
                <h2 class="text-3xl font-bold text-primary mb-2">Registrar Nova Perda</h2>
                <p class="text-gray-600">Preencha os dados abaixo para registrar uma perda no estoque</p>
            </div>
            
            <form action="../control/controller_main.php" method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="produto_id" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-box text-primary mr-2"></i>
                            Produto
                        </label>
                        <div class="relative">
                            <select class="js-example-basic-single w-full" name="id_produto" id="produto_id" required>
                                <option value="">Selecione o produto</option>
                                <?php
                                $dados = $select->selectProdutosTotal();
                                foreach($dados as $dado){
                                ?>
                                <option value="<?= htmlspecialchars($dado['id']) ?>" 
                                        data-nome="<?= htmlspecialchars($dado['nome_produto']) ?>"
                                        data-quantidade="<?= htmlspecialchars($dado['quantidade'] ?? '0') ?>"
                                        data-categoria="<?= htmlspecialchars($dado['categoria'] ?? 'N/A') ?>"
                                        data-codigo="<?= htmlspecialchars($dado['codigo'] ?? 'N/A') ?>">
                                    <?= htmlspecialchars($dado['nome_produto']) ?> 
                                    (Estoque: <?= htmlspecialchars($dado['quantidade'] ?? '0') ?>)
                                </option>
                                <?php }?>
                            </select>
                            <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                <i class="fas fa-chevron-down text-primary"></i>
                            </div>
                        </div>
                        
                        <!-- Informações do produto selecionado -->
                        <div id="produto-info" class="hidden mt-3 p-3 bg-accent rounded-lg border border-primary/20">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-primary text-sm">Informações do Produto</h4>
                                <span class="text-xs text-gray-500">Estoque atual</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="font-medium text-gray-600">Nome:</span>
                                    <span id="info-nome" class="ml-1 text-gray-800"></span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-600">Quantidade:</span>
                                    <span id="info-quantidade" class="ml-1 text-gray-800 font-bold text-primary"></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Alerta de estoque baixo -->
                        <div id="estoque-alerta" class="hidden mt-2 p-2 bg-yellow-100 border border-yellow-300 rounded-lg">
                            <div class="flex items-center text-yellow-800 text-xs">
                                <span>Quantidade solicitada pode esgotar o estoque</span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label for="quantidade_perdida" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-sort-numeric-down text-primary mr-2"></i>
                            Quantidade Perdida
                        </label>
                        <input type="number" id="quantidade_perdida" name="quantidade_perdida" min="1" required 
                               class="w-full px-4 py-3 border-2 border-primary/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary transition-all duration-200 hover:border-primary/50 bg-white shadow-sm"
                               placeholder="Ex: 5">
                    </div>
                </div>
                
                <!-- Segunda linha: Tipo de Perda e Data -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="tipo_perda" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-tag text-primary mr-2"></i>
                            Tipo de Perda
                        </label>
                        <select id="tipo_perda" name="tipo_perda" required 
                                class="w-full px-4 py-3 border-2 border-primary/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary transition-all duration-200 hover:border-primary/50 bg-white shadow-sm">
                            <option value="">Selecione o tipo de perda</option>
                            <option value="Dano Físico" class="py-2">🚨 Dano Físico</option>
                            <option value="Vencimento" class="py-2">⏰ Vencimento</option>
                            <option value="Desaparecimento" class="py-2">🔍 Desaparecimento</option>
                            <option value="Má conservacao" class="py-2">🌡️ Má Conservação</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label for="data_perda" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-calendar-alt text-primary mr-2"></i>
                            Data da Perda
                        </label>
                        <input type="date" id="data_perda" name="data_perda" required 
                               class="w-full px-4 py-3 border-2 border-primary/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary transition-all duration-200 hover:border-primary/50 bg-white shadow-sm">
                    </div>
                </div>
                
               
                
                <!-- Botão de envio -->
                <div class="pt-4 flex justify-center">
                    <button type="submit" class="bg-gradient-to-r from-red-600 to-red-700 text-white font-bold py-3 px-8 rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <i class="fas fa-exclamation-triangle mr-3"></i>
                        Registrar Perda
                    </button>
                </div>
            </form>
        </div>
        

        
        <!-- Botões de ação -->
        <div class="mt-12 flex justify-center w-full gap-6">
            <a href="estoque.php" class="group">
                <button class="bg-gradient-to-r from-primary to-primary/90 text-white font-bold py-4 px-8 rounded-xl hover:from-primary/90 hover:to-primary transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center">
                    <i class="fas fa-arrow-left mr-3 group-hover:-translate-x-1 transition-transform duration-300"></i>
                    Voltar ao Estoque
                </button>
            </a>
           
        </div>
        
        <!-- Alerta de mensagem -->
        <div id="alertaMensagem" class="fixed bottom-4 right-4 p-4 rounded-lg shadow-lg max-w-md hidden animate-fade-in z-50">
            <div class="flex items-center">
                <svg id="alertaIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"></svg>
                <span id="mensagemTexto">Operação realizada com sucesso!</span>
            </div>
        </div>
    </main>
    <footer class="bg-gradient-to-r from-primary to-dark text-white py-8 md:py-10 mt-auto relative transition-all duration-300">
        <!-- Efeito de brilho sutil no topo -->
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-secondary to-transparent opacity-30"></div>
        
        <div class="px-4 md:px-8 transition-all duration-300" id="footerContent">
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
                            <a href="tel:+558531012783" class="flex items-center text-sm md:text-base text-gray-200 hover:text-white transition-colors duration-300 group/item">
                                <i class="fas fa-phone-alt mr-3 text-secondary group-hover/item:scale-110 transition-transform duration-300"></i>
                                (85) 3101-2783
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
                        © 2025 Crede v1.2.0 | Desenvolvido por alunos EEEP STGM
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Efeito de brilho sutil na base -->
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-secondary to-transparent opacity-30"></div>
    </footer>
    <script>
        $(document).ready(function() {
            // Configuração melhorada do Select2
            $('.js-example-basic-single').select2({
                placeholder: 'Selecione o produto',
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: Infinity, // Desabilita o campo de busca
                language: {
                    noResults: function() {
                        return "Nenhum produto encontrado";
                    }
                },
                templateResult: function(data) {
                    if (!data.id) return data.text;
                    return $(`<span class="flex items-center justify-between w-full">
                        <span class="truncate">${data.text}</span>
                        <span class="text-xs text-gray-500 ml-2">${$(data.element).data('quantidade') || '0'}</span>
                    </span>`);
                },
                templateSelection: function(data) {
                    if (!data.id) return data.text;
                    return $(`<span class="font-medium">${$(data.element).data('nome') || data.text}</span>`);
                },
                escapeMarkup: function(markup) {
                    return markup;
                }
            });

            // Evento para mostrar informações do produto selecionado
            $('#produto_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const produtoInfo = $('#produto-info');
                const estoqueAlerta = $('#estoque-alerta');
                
                if (selectedOption.val()) {
                    // Mostrar informações do produto
                    $('#info-nome').text(selectedOption.data('nome'));
                    $('#info-quantidade').text(selectedOption.data('quantidade'));
                    
                    produtoInfo.removeClass('hidden').addClass('animate-fade-in');
                    
                    // Verificar se há quantidade suficiente
                    const estoqueAtual = parseInt(selectedOption.data('quantidade')) || 0;
                    const quantidadePerdida = parseInt($('#quantidade_perdida').val()) || 0;
                    
                    if (quantidadePerdida > estoqueAtual) {
                        estoqueAlerta.removeClass('hidden').addClass('animate-fade-in');
                    } else {
                        estoqueAlerta.addClass('hidden');
                    }
                } else {
                    produtoInfo.addClass('hidden');
                    estoqueAlerta.addClass('hidden');
                }
            });

            // Evento para validar quantidade em tempo real
            $('#quantidade_perdida').on('input', function() {
                const quantidadePerdida = parseInt($(this).val()) || 0;
                const selectedOption = $('#produto_id').find('option:selected');
                const estoqueAlerta = $('#estoque-alerta');
                
                if (selectedOption.val() && quantidadePerdida > 0) {
                    const estoqueAtual = parseInt(selectedOption.data('quantidade')) || 0;
                    
                    if (quantidadePerdida > estoqueAtual) {
                        estoqueAlerta.removeClass('hidden').addClass('animate-fade-in');
                        $(this).addClass('border-red-500').removeClass('border-primary/30');
                    } else {
                        estoqueAlerta.addClass('hidden');
                        $(this).removeClass('border-red-500').addClass('border-primary/30');
                    }
                } else {
                    estoqueAlerta.addClass('hidden');
                    $(this).removeClass('border-red-500').addClass('border-primary/30');
                }
            });

            // Validação do formulário antes do envio
            $('form').on('submit', function(e) {
                const produtoId = $('#produto_id').val();
                const quantidadePerdida = parseInt($('#quantidade_perdida').val()) || 0;
                const selectedOption = $('#produto_id').find('option:selected');
                
                if (!produtoId) {
                    e.preventDefault();
                    mostrarAlerta('Selecione um produto', 'error');
                    $('#produto_id').focus();
                    return false;
                }
                
                if (quantidadePerdida <= 0) {
                    e.preventDefault();
                    mostrarAlerta('A quantidade deve ser maior que zero', 'error');
                    $('#quantidade_perdida').focus();
                    return false;
                }
                
                const estoqueAtual = parseInt(selectedOption.data('quantidade')) || 0;
                if (quantidadePerdida > estoqueAtual) {
                    e.preventDefault();
                    mostrarAlerta(`Quantidade solicitada (${quantidadePerdida}) é maior que o estoque disponível (${estoqueAtual})`, 'error');
                    $('#quantidade_perdida').focus();
                    return false;
                }
                
                // Confirmação antes de registrar a perda
                if (!confirm('Tem certeza que deseja registrar esta perda? Esta ação não pode ser desfeita.')) {
                    e.preventDefault();
                    return false;
                }
            });
        });
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

        // Definir data atual no campo de data
        const dataPerdaInput = document.getElementById('data_perda');
        if (dataPerdaInput) {
            const hoje = new Date().toISOString().split('T')[0];
            dataPerdaInput.value = hoje;
        }

        // Mostrar alerta se houver mensagem
        <?php if ($mostrarAlerta): ?>
        try {
            mostrarAlerta('<?php echo addslashes($mensagem); ?>', '<?php echo $tipoMensagem; ?>');
        } catch (error) {
            console.error('Erro ao inicializar alerta:', error);
        }
        <?php endif; ?>
    });

    // Função para mostrar alertas
    window.mostrarAlerta = function(mensagem, tipo) {
        const alerta = document.getElementById('alertaMensagem');
        const mensagemTexto = document.getElementById('mensagemTexto');
        const alertaIcon = document.getElementById('alertaIcon');
        
        if (!alerta || !mensagemTexto || !alertaIcon) {
            return;
        }
        
        mensagemTexto.textContent = mensagem;
        
        if (tipo === 'success') {
            alerta.className = 'fixed bottom-4 right-4 p-4 rounded-lg shadow-lg max-w-md z-50 bg-green-500 text-white';
            alertaIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
        } else if (tipo === 'error') {
            alerta.className = 'fixed bottom-4 right-4 p-4 rounded-lg shadow-lg max-w-md z-50 bg-red-500 text-white';
            alertaIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />';
        }
        
        alerta.classList.remove('hidden');
        
        // Auto-hide após 5 segundos
        setTimeout(() => {
            alerta.classList.add('hidden');
        }, 5000);
    };
    </script>
</body>
</html>
