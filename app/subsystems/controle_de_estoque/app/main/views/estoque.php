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
                    echo '</div></div>';
                    echo '</div></div>';
                }
            } else {
                echo '<div class="text-center py-8 text-gray-500"><i class="fas fa-box-open text-4xl mb-2"></i><p>Nenhum produto encontrado</p></div>';
            }
            ?>
        </div>
       
        <!-- Modal de Edição -->
        <div id="modalEditar" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">
                <button onclick="fecharModalEditar()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <h2 class="text-2xl font-bold text-primary mb-4">Editar Produto</h2>
                <form id="formEditar" action="../control/controllerEditarProduto.php" method="POST" class="space-y-4" onsubmit="return enviarFormularioEdicao(event)">
                    <input type="hidden" id="editar_id" name="editar_id">
                    <div>
                        <label for="editar_barcode" class="block text-sm font-medium text-gray-700 mb-1">Código de Barras</label>
                        <input type="text" id="editar_barcode" name="editar_barcode" required class="w-full px-4 py-2 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                    </div>
                    <div>
                        <label for="editar_nome" class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto</label>
                        <input type="text" id="editar_nome" name="editar_nome" required class="w-full px-4 py-2 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                    </div>
                    <div>
                        <label for="editar_quantidade" class="block text-sm font-medium text-gray-700 mb-1">Quantidade</label>
                        <input type="number" id="editar_quantidade" name="editar_quantidade" min="0" required class="w-full px-4 py-2 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                    </div>
                    <div>
                        <label for="editar_natureza" class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                        <select id="editar_natureza" name="editar_natureza" required class="w-full px-4 py-2 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                            <option value="">Selecione a categoria</option>
                            <?php 
                            $dados = $select->select_categoria();

                            foreach ($dados as $dado) {
                                ?>
                                <option value="<?=$dado['id']?>"><?=$dado['nome_categoria']?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="fecharModalEditar()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90 transition-colors">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Modal de Exclusão -->
        <div id="modalExcluir" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">
                <button onclick="fecharModalExcluir()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Tem certeza?</h2>
                    <p class="text-gray-600 mb-6">Você está prestes a excluir <span id="nomeProdutoExcluir" class="font-semibold"></span>. Esta ação não pode ser desfeita.</p>
                </div>
                <div class="flex justify-center space-x-3">
                    <button onclick="fecharModalExcluir()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">Cancelar</button>
                    <a id="linkExcluir" href="#" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">Sim, excluir</a>
                </div>
            </div>
        </div>
        <!-- Modal de Categoria -->
        <div id="modalCategoria" class="fixed inset-0 bg-black bg-opacity-50 z-[9999] flex items-center justify-center" style="display: none;">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">
                <button onclick="fecharModalCategoria()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-primary/10 mb-4">
                        <i class="fas fa-chart-bar text-primary text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-primary mb-2">Nova Categoria</h2>
                    <p class="text-gray-600">Adicione uma nova categoria para organizar seus produtos</p>
                </div>
                <form id="formCategoria" action="../controllers/controller_crud_produto.php" method="POST" class="space-y-4" onsubmit="return enviarFormularioCategoria(event)">
                    <div>
                        <label for="categoria" class="block text-sm font-medium text-gray-700 mb-1">Nome da Categoria</label>
                        <input type="text" id="categoria" name="categoria" required 
                               class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent transition-all duration-200"
                               placeholder="Ex: Informática, Limpeza, etc.">
                    </div>
                                         <div class="flex justify-center space-x-3 mt-6">
                         <button type="button" onclick="fecharModalCategoria()" 
                                 class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                             Cancelar
                         </button>
                         <button type="submit" 
                                 class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors flex items-center">
                             <i class="fas fa-plus mr-2"></i>
                             Cadastrar Categoria
                         </button>
                     </div>
                </form>
            </div>
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
    document.addEventListener('DOMContentLoaded', function() {
    // Verificar se os elementos do alerta existem
    const alerta = document.getElementById('alertaMensagem');
    const mensagemTexto = document.getElementById('mensagemTexto');
    const alertaIcon = document.getElementById('alertaIcon');
    console.log('Elementos do alerta encontrados:', {
        alerta: !!alerta,
        mensagemTexto: !!mensagemTexto,
        alertaIcon: !!alertaIcon
    });

    // Mostrar alerta se houver mensagem
    <?php if ($mostrarAlerta): ?>
    console.log('Inicializando alerta com mensagem: "<?php echo addslashes($mensagem); ?>", tipo: "<?php echo $tipoMensagem; ?>"');
    try {
        mostrarAlerta('<?php echo addslashes($mensagem); ?>', '<?php echo $tipoMensagem; ?>');
    } catch (error) {
        console.error('Erro ao inicializar alerta:', error);
    }
    <?php endif; ?>

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

    // Filtro de produtos (desktop e mobile)
    const pesquisarInput = document.getElementById('pesquisar');
    const filtroCategoria = document.getElementById('filtroCategoria');
    const tabelaEstoque = document.getElementById('tabelaEstoque');
    const mobileCards = document.querySelector('.mobile-cards');
    
    if (pesquisarInput && filtroCategoria && tabelaEstoque) {
        pesquisarInput.addEventListener('input', filtrarProdutos);
        filtroCategoria.addEventListener('change', filtrarProdutos);
        
        function filtrarProdutos() {
            const termo = pesquisarInput.value.toLowerCase();
            const categoria = filtroCategoria.value.toLowerCase();
            
            // Filtrar tabela desktop
            const linhas = tabelaEstoque.querySelectorAll('tr');
            linhas.forEach(linha => {
                const colunas = linha.querySelectorAll('td');
                if (colunas.length > 0) {
                    const nome = colunas[1].textContent.toLowerCase();
                    const cat = colunas[3].textContent.toLowerCase();
                    const matchTermo = nome.includes(termo);
                    const matchCategoria = categoria === '' || cat === categoria;
                    linha.style.display = matchTermo && matchCategoria ? '' : 'none';
                }
            });
            
            // Filtrar cards mobile
            if (mobileCards) {
                const cards = mobileCards.querySelectorAll('.card-item');
                const categoriaHeaders = mobileCards.querySelectorAll('.categoria-header');
                
                cards.forEach(card => {
                    const nome = card.querySelector('h3').textContent.toLowerCase();
                    const cat = card.closest('.card-item').previousElementSibling?.classList.contains('categoria-header') ? 
                               card.closest('.card-item').previousElementSibling.textContent.toLowerCase() : 
                               card.closest('.card-item').previousElementSibling?.previousElementSibling?.textContent.toLowerCase() || '';
                    
                    const matchTermo = nome.includes(termo);
                    const matchCategoria = categoria === '' || cat.includes(categoria);
                    card.style.display = matchTermo && matchCategoria ? '' : 'none';
                });
                
                // Mostrar/ocultar headers de categoria baseado nos cards visíveis
                categoriaHeaders.forEach(header => {
                    const nextCard = header.nextElementSibling;
                    if (nextCard && nextCard.classList.contains('card-item')) {
                        const hasVisibleCards = Array.from(cards).some(card => 
                            card.style.display !== 'none' && 
                            card.previousElementSibling === header
                        );
                        header.style.display = hasVisibleCards ? '' : 'none';
                    }
                });
            }
        }
    }

    // Modais de edição/exclusão
    window.abrirModalEditar = function(id) {
        console.log('Abrindo modal de edição para ID:', id);
        fetch('../control/controllerEditarProduto.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.erro) {
                    alert('Erro ao carregar dados do produto: ' + data.erro);
                    return;
                }
                document.getElementById('editar_id').value = data.id;
                document.getElementById('editar_barcode').value = data.barcode;
                document.getElementById('editar_nome').value = data.nome_produto;
                document.getElementById('editar_quantidade').value = data.quantidade;
                document.getElementById('editar_natureza').value = data.natureza;
                document.getElementById('modalEditar').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Erro ao carregar dados do produto:', error);
                alert('Erro ao carregar dados do produto');
            });
    };

    window.fecharModalEditar = function() {
        console.log('Fechando modal de edição');
        document.getElementById('formEditar').reset();
        document.getElementById('editar_id').value = '';
        document.getElementById('editar_barcode').value = '';
        document.getElementById('editar_nome').value = '';
        document.getElementById('editar_quantidade').value = '';
        document.getElementById('editar_natureza').value = '';
        document.getElementById('modalEditar').classList.add('hidden');
    };

    window.abrirModalExcluir = function(id, nome) {
        console.log('Tentando abrir modal de exclusão para ID:', id, 'Nome:', nome);
        const modalExcluir = document.getElementById('modalExcluir');
        const nomeProdutoExcluir = document.getElementById('nomeProdutoExcluir');
        const linkExcluir = document.getElementById('linkExcluir');
        
        if (!modalExcluir || !nomeProdutoExcluir || !linkExcluir) {
            console.error('Elementos do modal de exclusão não encontrados');
            return;
        }
        
        nomeProdutoExcluir.textContent = nome;
        linkExcluir.href = '../control/controllerApagarProduto.php?id=' + id;
        modalExcluir.classList.remove('hidden');
        console.log('Modal de exclusão visível:', !modalExcluir.classList.contains('hidden'));
    };

    window.fecharModalExcluir = function() {
        console.log('Fechando modal de exclusão');
        document.getElementById('modalExcluir').classList.add('hidden');
    };

    // Fechar modais ao clicar fora, apenas no fundo do modal
    document.addEventListener('click', function(e) {
        const modalEditar = document.getElementById('modalEditar');
        const modalExcluir = document.getElementById('modalExcluir');
        
        if (e.target === modalEditar) {
            console.log('Clique fora do modal de edição');
            fecharModalEditar();
        }
        if (e.target === modalExcluir) {
            console.log('Clique fora do modal de exclusão');
            fecharModalExcluir();
        }
    });

    // Fechar modais ao pressionar ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modalEditar = document.getElementById('modalEditar');
            const modalExcluir = document.getElementById('modalExcluir');
            
            if (modalEditar && !modalEditar.classList.contains('hidden')) {
                console.log('Fechando modal de edição via ESC');
                fecharModalEditar();
            }
            if (modalExcluir && !modalExcluir.classList.contains('hidden')) {
                console.log('Fechando modal de exclusão via ESC');
                fecharModalExcluir();
            }
        }
    });

    // Função para mostrar alertas
    window.mostrarAlerta = function(mensagem, tipo) {
        console.log('Exibindo alerta:', mensagem, tipo);
        const alerta = document.getElementById('alertaMensagem');
        const mensagemTexto = document.getElementById('mensagemTexto');
        const alertaIcon = document.getElementById('alertaIcon');
        
        if (!alerta || !mensagemTexto || !alertaIcon) {
            console.error('Elementos do alerta não encontrados:', {
                alerta: !!alerta,
                mensagemTexto: !!mensagemTexto,
                alertaIcon: !!alertaIcon
            });
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
        console.log('Alerta visível:', !alerta.classList.contains('hidden'));
        
        // Auto-hide após 5 segundos e limpar URL
        setTimeout(() => {
            console.log('Iniciando ocultação do alerta e limpeza da URL');
            alerta.classList.add('hidden');
            console.log('Alerta oculto:', alerta.classList.contains('hidden'));
            
            try {
                // Tentar limpar a URL com pathname
                const cleanUrl = window.location.pathname;
                console.log('Tentando limpar URL para:', cleanUrl);
                window.history.replaceState({}, document.title, cleanUrl);
                console.log('URL após tentativa com pathname:', window.location.href);
                
                // Verificar se a URL ainda contém parâmetros
                if (window.location.search) {
                    console.log('URL ainda contém parâmetros, tentando caminho fixo');
                    const fixedUrl = '/GitHub/Salaberga/portalsalaberga/app/subsystems/ControledeEstoque/app/main/view/estoque.php';
                    window.history.replaceState({}, document.title, fixedUrl);
                    console.log('URL após tentativa com caminho fixo:', window.location.href);
                }
            } catch (error) {
                console.error('Erro ao limpar URL:', error);
                // Solução de contingência: recarregar a página sem parâmetros
                console.log('Recarregando página como contingência');
                window.location = '/GitHub/Salaberga/portalsalaberga/app/subsystems/ControledeEstoque/app/main/view/estoque.php';
            }
        }, 5000);
    };

    // Função para enviar formulário de edição
    window.enviarFormularioEdicao = function(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Salvando...';
        submitBtn.disabled = true;
        
        fetch('../control/controllerEditarProduto.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            fecharModalEditar();
            
            if (data.success) {
                mostrarAlerta(data.message || 'Produto atualizado com sucesso!', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                mostrarAlerta(data.message || 'Erro ao atualizar produto', 'error');
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Erro ao editar produto:', error);
            mostrarAlerta('Erro ao atualizar produto', 'error');
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
        
        return false;
    };

    // Funções para o modal de categoria
    function abrirModalCategoria() {
        console.log('=== FUNÇÃO ABRIR MODAL CHAMADA ===');
        console.log('Abrindo modal de categoria');
        
        const modal = document.getElementById('modalCategoria');
        console.log('Modal encontrado:', modal);
        
        if (modal) {
            console.log('Alterando display para flex');
            modal.style.display = 'flex';
            console.log('Display atual:', modal.style.display);
            
            const input = document.getElementById('categoria');
            if (input) {
                input.focus();
                console.log('Input focado');
            } else {
                console.error('Input categoria não encontrado');
            }
        } else {
            console.error('Modal de categoria não encontrado');
        }
    }

    function fecharModalCategoria() {
        console.log('Fechando modal de categoria');
        const modal = document.getElementById('modalCategoria');
        const form = document.getElementById('formCategoria');
        if (modal) modal.style.display = 'none';
        if (form) form.reset();
    }

    // Tornar as funções globais
    window.abrirModalCategoria = abrirModalCategoria;
    window.fecharModalCategoria = fecharModalCategoria;

    // Função para enviar formulário de categoria
    window.enviarFormularioCategoria = function(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Cadastrando...';
        submitBtn.disabled = true;
        
        fetch('../control/controller_categoria.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            fecharModalCategoria();
            
            // Verificar se a resposta indica sucesso
            if (data.includes('sucesso') || data.includes('success') || data.includes('cadastrada')) {
                mostrarAlerta('Categoria cadastrada com sucesso!', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                mostrarAlerta('Erro ao cadastrar categoria. Tente novamente.', 'error');
            }
        })
        .catch(error => {
            console.error('Erro ao cadastrar categoria:', error);
            mostrarAlerta('Erro ao cadastrar categoria', 'error');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
        
        return false;
    };

    // Fechar modal de categoria ao clicar fora
    document.addEventListener('click', function(e) {
        const modalCategoria = document.getElementById('modalCategoria');
        if (e.target === modalCategoria) {
            console.log('Fechando modal ao clicar fora');
            fecharModalCategoria();
        }
    });

    // Fechar modal de categoria ao pressionar ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modalCategoria = document.getElementById('modalCategoria');
            if (modalCategoria && !modalCategoria.classList.contains('hidden')) {
                fecharModalCategoria();
            }
        }
    });

    // Modal de categoria será aberto apenas quando o usuário clicar no botão
});
</script>
</body>
</html>