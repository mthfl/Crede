<?php
require_once(__DIR__ . '/../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../models/model.select.php');
$select = new select();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Produto</title>
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
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.5s ease-out'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            }
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
            background-color: #F8FAF9;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #005A24 0%, #1A3C34 100%);
        }

        .page-title {
            position: relative;
            display: inline-block;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: #FFA500;
            border-radius: 3px;
        }

        .social-icon {
            transition: all 0.3s ease;
        }

        .social-icon:hover {
            transform: translateY(-3px);
            filter: drop-shadow(0 4px 3px rgba(255, 165, 0, 0.3));
        }

        .custom-input,
        .custom-select {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 2px solid #005A24;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            color: #1A3C34;
            background-color: #F8FAF9;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            text-align: center;
        }

        @media (min-width: 768px) {

            .custom-input,
            .custom-select {
                padding: 0.75rem;
                font-size: 1rem;
            }
        }

        .custom-select {
            appearance: none;
            background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22%23005A24%22%3E%3Cpath%20d%3D%22M7%2010l5%205%205-5H7z%22%2F%3E%3C%2Fsvg%3E');
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1rem;
            text-align: center;
            text-align-last: center;
        }

        .select-wrapper {
            margin: 0 auto;
            width: 100%;
        }

        .custom-select option {
            text-align: left;
            padding-left: 10px;
        }

        .custom-input:focus,
        .custom-select:focus {
            border-color: #FFA500;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 165, 0, 0.2);
        }

        .nav-link {
            position: relative;
            transition: color 0.3s ease;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #FFA500;
            transition: width 0.3s ease;
        }

        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #FFA500;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease, transform 0.3s ease;
            z-index: 1000;
        }

        .back-to-top.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .back-to-top:hover {
            background-color: #E69500;
            transform: scale(1.1);
        }

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

        @media (max-width: 768px) {
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

            #menuButton {
                transition: all 0.3s ease;
            }

            #menuButton.hidden {
                opacity: 0;
                visibility: hidden;
                transform: scale(0.8);
            }

            footer {
                margin-left: 0 !important;
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            footer .ml-64 {
                margin-left: 0 !important;
            }
        }

        @media (max-width: 640px) {
            .page-title::after {
                width: 60px;
                height: 2px;
                bottom: -6px;
            }

            .custom-input,
            .custom-select {
                font-size: 0.875rem;
                padding: 0.5rem;
            }

            .custom-input::placeholder,
            .custom-select option {
                font-size: 0.875rem;
            }
        }

        @media (max-width: 480px) {
            .page-title::after {
                width: 50px;
                height: 2px;
                bottom: -4px;
            }

            .custom-input,
            .custom-select {
                font-size: 0.8rem;
                padding: 0.4rem;
            }
        }

        /* Select2: match .custom-input styling */
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            border: 2px solid #005A24;
            border-radius: 0.5rem;
            background-color: #F8FAF9;
            padding: 0.5rem 0.75rem;
            min-height: 2.5rem;
            display: flex;
            align-items: center;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1A3C34;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            font-size: 0.875rem;
            line-height: 1.25rem;
            text-align: center;
            padding-left: 0;
            padding-right: 1.5rem;
            width: 100%;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #1A3C34;
            opacity: 0.7;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            right: 0.75rem;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #FFA500;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 165, 0, 0.2);
        }

        .select2-dropdown {
            border: 2px solid #005A24;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .select2-results__option {
            text-align: left;
            padding-left: 10px;
            font-size: 0.875rem;
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #E6F4EA;
            color: #1A3C34;
        }

        /* Estilos para produtos com estoque crítico */
        .produto-critico {
            color: #dc2626 !important;
            font-weight: 600;
        }

        .produto-baixo {
            color: #d97706 !important;
            font-weight: 500;
        }

        .produto-normal {
            color: #059669 !important;
        }

        @media (min-width: 768px) {
            .select2-container--default .select2-selection--single {
                padding: 0.75rem;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered,
            .select2-results__option {
                font-size: 1rem;
            }
        }

        /* Select2 dropdown search input focus */
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 2px solid #005A24;
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            font-size: 0.875rem;
            color: #1A3C34;
            outline: none;
            background-color: #F8FAF9;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field:focus,
        .select2-container--default .select2-search--dropdown .select2-search__field:focus-visible {
            border-color: #FFA500;
            box-shadow: 0 0 0 3px rgba(255, 165, 0, 0.2);
            outline: none;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col font-sans bg-light">
    <div class="fixed left-0 top-0 h-full w-64 bg-gradient-to-b from-primary to-dark text-white shadow-xl z-50 transform transition-transform duration-300 ease-in-out" id="sidebar">
        <div class="flex flex-col h-full">
            <div class="p-6 border-b border-white/20">
                <div class="flex items-center">
                    <img src="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png" alt="Logo STGM" class="h-12 mr-3 transition-transform hover:scale-105">
                    <span class="text-white font-heading text-lg font-semibold">CREDE Estoque</span>
                </div>
            </div>

            <nav class="flex-1 p-4 space-y-2">
                <?php if (isset($_SESSION['Admin_estoque']) || isset($_SESSION['liberador_estoque']) || isset($_SESSION['Dev_estoque'])) { ?>
                    <a href="index.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                        <i class="fas fa-home mr-3 text-lg"></i>
                        <span>Início</span>
                    </a>
                <?php } ?>
                <?php if (isset($_SESSION['Admin_estoque']) || isset($_SESSION['liberador_estoque']) || isset($_SESSION['Dev_estoque'])) { ?>
                    <a href="estoque.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                        <i class="fas fa-boxes mr-3 text-lg"></i>
                        <span>Estoque</span>
                    </a>
                <?php } ?>
                <?php if (isset($_SESSION['Admin_estoque']) || isset($_SESSION['liberador_estoque']) || isset($_SESSION['Dev_estoque'])) { ?>
                    <a href="./products/adc_produto.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2 ">
                        <i class="fas fa-plus-circle mr-3 text-lg"></i>
                        <span>Adicionar</span>
                    </a>
                <?php } ?>
                <?php if (isset($_SESSION['Admin_estoque']) || isset($_SESSION['liberador_estoque']) || isset($_SESSION['Dev_estoque'])) { ?>
                    <a href="solicitar.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2 active">
                        <i class="fas fa-clipboard-list mr-3 text-lg"></i>
                        <span>Solicitar</span>
                    </a>
                <?php } ?>
                <?php if (isset($_SESSION['Admin_estoque']) || isset($_SESSION['Dev_estoque'])|| isset($_SESSION['liberador_estoque'])) { ?>
                    <a href="relatorios.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                        <i class="fas fa-clipboard-list mr-3 text-lg"></i>
                        <span>Relatórios</span>
                    </a>
                <?php } ?>

            </nav>

            <div class="p-4 border-t border-white/20">
                <a href="../../../../../main/views/subsystems.php" class="w-full bg-transparent border border-white/40 hover:bg-white/10 text-white py-2 px-4 rounded-lg transition-all duration-200 flex items-center justify-center">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Sair
                </a>
            </div>

            <div class="p-4 border-t border-white/20 md:hidden">
                <button class="w-full bg-white/10 hover:bg-white/20 text-white py-2 px-4 rounded-lg transition-all duration-200" id="closeSidebar">
                    <i class="fas fa-times mr-2"></i>
                    Fechar Menu
                </button>
            </div>
        </div>
    </div>

    <button class="fixed top-4 left-4 z-50 md:hidden text-primary p-3 rounded-lg hover:bg-primary/90 transition-all duration-200" id="menuButton">
        <i class="fas fa-bars text-lg"></i>
    </button>

    <div class="fixed inset-0 bg-black/50 z-40 md:hidden hidden" id="overlay"></div>

    <button class="back-to-top hidden fixed bottom-6 right-6 z-50 bg-secondary hover:bg-secondary/90 text-white w-12 h-12 rounded-full shadow-lg transition-all duration-300 flex items-center justify-center group">
        <i class="fas fa-chevron-up text-lg group-hover:scale-110 transition-transform duration-300"></i>
    </button>

    <main class="ml-0 md:ml-64 px-4 py-8 md:py-12 flex-1 transition-all duration-300">
        <div class="text-center mb-6 md:mb-10">
            <h1 class="text-primary text-2xl md:text-3xl lg:text-4xl font-bold mb-4 md:mb-6 lg:mb-8 text-center page-title tracking-tight font-heading inline-block mx-auto">SOLICITAR PRODUTO</h1>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 lg:p-8 max-w-2xl w-full border-2 border-primary mx-auto">
            <form action="../controllers/controller_solicitar.php" method="POST" class="space-y-4 md:space-y-6" id="solicitarForm">
                <div class="space-y-3 md:space-y-4">
                    <div class="mb-3 md:mb-4">
                        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                            <button type="button" id="btnSelect" class="w-full sm:flex-1 bg-primary text-white py-2 px-3 md:px-4 rounded-lg font-semibold transition-colors text-sm md:text-base" onclick="mostrarOpcao('select')">
                                <i class="fas fa-list mr-2"></i>Selecionar da Lista
                            </button>
                            <button type="button" id="btnBarcode" class="w-full sm:flex-1 bg-gray-300 text-gray-700 py-2 px-3 md:px-4 rounded-lg font-semibold transition-colors text-sm md:text-base" onclick="mostrarOpcao('barcode')">
                                <i class="fas fa-barcode mr-2"></i>Ler Código de Barras
                            </button>
                        </div>
                    </div>

                    <div id="opcaoSelect" class="select-wrapper">
                        <select id="produto" name="produto" required class="custom-select text-sm md:text-base" aria-label="Selecionar produto" onchange="validarSelecao()">
                            <option value="" disabled selected>SELECIONAR PRODUTO</option>
                            <?php
                            $produtos = $select->select_produtos();
                            if ($produtos && count($produtos) > 0) {
                                foreach ($produtos as $produto) {
                                    $quantidade = $produto['quantidade'];
                                    $status_class = $quantidade <= 5 ? 'text-red-600' : ($quantidade <= 10 ? 'text-yellow-600' : 'text-green-600');
                                    $status_text = $quantidade <= 5 ? ' (CRÍTICO)' : ($quantidade <= 10 ? ' (BAIXO)' : '');
                                    echo '<option value="' . $produto['id'] . '" data-barcode="' . $produto['barcode'] . '" data-quantidade="' . $quantidade . '">';
                                    echo htmlspecialchars($produto['nome_produto']) . ' - ' . htmlspecialchars($produto['categoria']). ' - ' . htmlspecialchars($produto['quantidade']) . $status_text;
                                    echo '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div id="opcaoBarcode" class="hidden">
                        <div class="relative">
                            <input type="text" id="barcodeInput" name="barcode" value="<?php echo $_GET['barcode'] ?? ''; ?>" placeholder="ESCANEIE O CÓDIGO DE BARRAS"
                                class="custom-input text-center text-base md:text-lg font-mono tracking-wider"
                                aria-label="Código de barras">
                            <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                <i class="fas fa-barcode text-gray-400"></i>
                            </div>
                        </div>
                        <div id="produtoInfo" class="mt-3 p-2 md:p-3 bg-green-50 border border-green-200 rounded-lg hidden">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-green-800 text-sm md:text-base truncate" id="produtoNome"></p>
                                    <p class="text-xs md:text-sm text-green-600" id="produtoEstoque"></p>
                                </div>
                                <i class="fas fa-check-circle text-green-500 text-lg md:text-xl ml-2 flex-shrink-0"></i>
                            </div>
                        </div>
                        <input type="hidden" id="produtoIdHidden" name="produto_id" value="">
                        <input type="hidden" id="opcaoAtualHidden" name="opcao_atual" value="barcode">
                    </div>

                    <div>
                        <input type="number" placeholder="QUANTIDADE" min="1" id="quantidade" name="quantidade" required
                            class="custom-input text-sm md:text-base" aria-label="Quantidade do produto">
                    </div>

                    <select id="produto" name="retirante" required class="custom-select text-sm md:text-base" aria-label="Solicitante" onchange="validarSelecao()">
                        <option value="" disabled selected>SOLICITADOR</option>
                        <?php
                        $produtos = $select->select_responsavel();
                        if ($produtos && count($produtos) > 0) {
                            foreach ($produtos as $produto) {
                                echo '<option value="' . $produto['nome'] . '">';
                                echo htmlspecialchars($produto['nome']) . ' - ' . htmlspecialchars($produto['nome_setor']);
                                echo '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" name="btn" value="Confirmar" class="w-full bg-secondary text-white font-bold py-2 md:py-3 px-4 rounded-lg hover:bg-opacity-90 transition-colors text-sm md:text-base"
                    aria-label="Confirmar solicitação">
                    CONFIRMAR
                </button>
            </form>
        </div>
    </main>

    <footer class="bg-gradient-to-r from-primary to-dark text-white py-8 md:py-10 mt-auto relative transition-all duration-300">
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-secondary to-transparent opacity-30"></div>
        <div class="px-4 md:px-8 transition-all duration-300 ml-0 md:ml-64" id="footerContent">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
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
                <div class="border-t border-white/20 pt-6 mt-8 text-center">
                    <p class="text-sm md:text-base text-gray-300 hover:text-white transition-colors duration-300">
                        © 2024 STGM v1.2.0 | Desenvolvido por alunos EEEP STGM
                    </p>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-secondary to-transparent opacity-30"></div>
    </footer>

    <script>
        $(document).ready(function() {
            // Configure Select2 for product selection
            $('#produto[name="produto"]').select2({
                placeholder: "SELECIONAR PRODUTO",
                allowClear: true,
                width: '100%'
            });
            
            // Configure Select2 for user selection
            $('#produto[name="retirante"]').select2({
                placeholder: "SOLICITADOR",
                allowClear: true,
                width: '100%'
            });
        });

        let opcaoAtual = 'select';
        let produtoSelecionado = null;

        function configurarCampos(opcao) {
            const produtoIdHidden = document.getElementById('produtoIdHidden');
            if (produtoIdHidden) {
                produtoIdHidden.name = opcao === 'select' ? 'produto' : 'barcode';
                produtoIdHidden.value = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const opcaoAtualHidden = document.getElementById('opcaoAtualHidden');
            if (opcaoAtualHidden) {
                opcaoAtualHidden.value = opcaoAtual;
            }
            configurarCampos(opcaoAtual);
            validarSelecao();
        });

        function mostrarOpcao(opcao) {
            const btnSelect = document.getElementById('btnSelect');
            const btnBarcode = document.getElementById('btnBarcode');
            const opcaoSelect = document.getElementById('opcaoSelect');
            const opcaoBarcode = document.getElementById('opcaoBarcode');
            const produtoSelect = document.getElementById('produto');
            const barcodeInput = document.getElementById('barcodeInput');
            const opcaoAtualHidden = document.getElementById('opcaoAtualHidden');
            const produtoIdHidden = document.getElementById('produtoIdHidden');

            produtoSelect.value = '';
            barcodeInput.value = '';
            document.getElementById('produtoInfo').classList.add('hidden');
            produtoSelecionado = null;
            produtoIdHidden.value = '';

            if (opcao === 'select') {
                btnSelect.className = 'w-full sm:flex-1 bg-primary text-white py-2 px-3 md:px-4 rounded-lg font-semibold transition-colors text-sm md:text-base';
                btnBarcode.className = 'w-full sm:flex-1 bg-gray-300 text-gray-700 py-2 px-3 md:px-4 rounded-lg font-semibold transition-colors text-sm md:text-base';
                opcaoSelect.classList.remove('hidden');
                opcaoBarcode.classList.add('hidden');
                produtoSelect.required = true;
                produtoSelect.removeAttribute('disabled');
                barcodeInput.required = false;
                barcodeInput.setAttribute('disabled', 'disabled');
                barcodeInput.name = '';
                produtoSelect.name = 'produto';
                opcaoAtual = 'select';
                opcaoAtualHidden.value = 'select';
                configurarCampos('select');
            } else {
                btnSelect.className = 'w-full sm:flex-1 bg-gray-300 text-gray-700 py-2 px-3 md:px-4 rounded-lg font-semibold transition-colors text-sm md:text-base';
                btnBarcode.className = 'w-full sm:flex-1 bg-primary text-white py-2 px-3 md:px-4 rounded-lg font-semibold transition-colors text-sm md:text-base';
                opcaoSelect.classList.add('hidden');
                opcaoBarcode.classList.remove('hidden');
                produtoSelect.required = false;
                produtoSelect.setAttribute('disabled', 'disabled');
                produtoSelect.name = '';
                barcodeInput.required = true;
                barcodeInput.removeAttribute('disabled');
                barcodeInput.name = 'barcode';
                opcaoAtual = 'barcode';
                opcaoAtualHidden.value = 'barcode';
                configurarCampos('barcode');
                setTimeout(() => barcodeInput.focus(), 100);
            }
            validarSelecao();
        }

        function exibirProdutoInfo(produto) {
            const produtoInfo = document.getElementById('produtoInfo');
            const produtoNome = document.getElementById('produtoNome');
            const produtoEstoque = document.getElementById('produtoEstoque');
            const produtoIdHidden = document.getElementById('produtoIdHidden');

            produtoNome.textContent = produto.nome_produto;
            produtoEstoque.textContent = `Estoque: ${produto.quantidade} unidades`;
            produtoInfo.classList.remove('hidden');
            produtoSelecionado = produto;
            produtoIdHidden.value = produto.id; // Use product ID for submission
            produtoIdHidden.name = 'produto_id';
            document.getElementById('barcodeInput').value = produto.barcode;
        }

        function validarSelecao() {
            const produtoSelect = document.getElementById('produto');
            const produtoIdHidden = document.getElementById('produtoIdHidden');

            if (opcaoAtual === 'select') {
                if (produtoSelect.value !== '' && produtoSelect.value !== null) {
                    produtoSelecionado = {
                        id: produtoSelect.value
                    };
                    produtoIdHidden.value = produtoSelect.value;
                    produtoIdHidden.name = 'produto';
                } else {
                    produtoSelecionado = null;
                    produtoIdHidden.value = '';
                }
            } else if (opcaoAtual === 'barcode') {
                if (produtoSelecionado) {
                    produtoIdHidden.value = produtoSelecionado.id;
                    produtoIdHidden.name = 'produto_id';
                } else {
                    produtoIdHidden.value = '';
                    produtoIdHidden.name = 'produto_id';
                }
            }
        }

        function validarFormulario() {
            const produtoSelect = document.getElementById('produto');
            const barcodeInput = document.getElementById('barcodeInput');
            const quantidade = document.getElementById('quantidade').value;

            if (opcaoAtual === 'select') {
                return produtoSelect.value !== '' && quantidade > 0;
            } else if (opcaoAtual === 'barcode') {
                return produtoSelecionado !== null && barcodeInput.value.trim() !== '' && quantidade > 0;
            }
            return false;
        }

        function mostrarNotificacao(mensagem, tipo) {
            const notificacao = document.createElement('div');
            notificacao.className = 'fixed top-4 right-4 z-50 p-3 md:p-4 rounded-lg shadow-lg text-sm md:text-base';
            notificacao.className += tipo === 'success' ? ' bg-green-500 text-white' : ' bg-red-500 text-white';
            notificacao.innerHTML = `
                <div class="flex items-center">
                    <i class="${tipo === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'} mr-2"></i>
                    <span>${mensagem}</span>
                </div>
            `;
            document.body.appendChild(notificacao);
            setTimeout(() => notificacao.remove(), 5000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const menuButton = document.getElementById('menuButton');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const closeSidebar = document.getElementById('closeSidebar');

            if (menuButton && sidebar) {
                menuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    sidebar.classList.toggle('show');
                    overlay.classList.toggle('hidden');
                    if (sidebar.classList.contains('show')) {
                        menuButton.classList.add('hidden');
                    } else {
                        menuButton.classList.remove('hidden');
                    }
                    document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
                });

                if (overlay) {
                    overlay.addEventListener('click', function() {
                        sidebar.classList.remove('show');
                        overlay.classList.add('hidden');
                        menuButton.classList.remove('hidden');
                        document.body.style.overflow = '';
                    });
                }

                if (closeSidebar) {
                    closeSidebar.addEventListener('click', function() {
                        sidebar.classList.remove('show');
                        overlay.classList.add('hidden');
                        menuButton.classList.remove('hidden');
                        document.body.style.overflow = '';
                    });
                }

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

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                        overlay.classList.add('hidden');
                        menuButton.classList.remove('hidden');
                        document.body.style.overflow = '';
                    }
                });

                const footerContent = document.getElementById('footerContent');
                if (footerContent) {
                    const adjustFooter = () => {
                        footerContent.style.marginLeft = window.innerWidth <= 768 ? '0' : '16rem';
                    };
                    adjustFooter();
                    menuButton.addEventListener('click', adjustFooter);
                    window.addEventListener('resize', adjustFooter);
                }
            }

            const backToTop = document.querySelector('.back-to-top');
            if (backToTop) {
                window.addEventListener('scroll', () => {
                    backToTop.classList.toggle('visible', window.scrollY > 300);
                    backToTop.classList.toggle('hidden', window.scrollY <= 300);
                });

                backToTop.addEventListener('click', () => {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }

            <?php if (isset($_GET['barcode']) && !empty($_GET['barcode'])): ?>
                const barcodeFromURL = '<?php echo htmlspecialchars($_GET['barcode']); ?>';
                if (barcodeFromURL) {
                    mostrarOpcao('barcode');
                    const barcodeInput = document.getElementById('barcodeInput');
                    if (barcodeInput) {
                        barcodeInput.value = barcodeFromURL;
                        setTimeout(() => {
                            buscarProdutoPorBarcode(barcodeFromURL).then(produto => {
                                if (produto) {
                                    exibirProdutoInfo(produto);
                                    mostrarNotificacao(`Produto encontrado: ${produto.nome_produto}`, 'success');
                                    validarSelecao();
                                } else {
                                    mostrarNotificacao('Produto não encontrado para este código de barras', 'error');
                                }
                            });
                        }, 500);
                    }
                }
            <?php endif; ?>

            const barcodeInput = document.getElementById('barcodeInput');
            if (barcodeInput) {
                let timeoutId;
                barcodeInput.addEventListener('input', function(e) {
                    clearTimeout(timeoutId);
                    const barcode = e.target.value.trim();
                    if (barcode.length >= 3) {
                        timeoutId = setTimeout(async () => {
                            const produto = await buscarProdutoPorBarcode(barcode);
                            if (produto) {
                                exibirProdutoInfo(produto);
                                mostrarNotificacao(`Produto encontrado: ${produto.nome_produto}`, 'success');
                            } else {
                                document.getElementById('produtoInfo').classList.add('hidden');
                                produtoSelecionado = null;
                                mostrarNotificacao('Produto não encontrado para este código de barras', 'error');
                            }
                            validarSelecao();
                        }, 500);
                    } else {
                        document.getElementById('produtoInfo').classList.add('hidden');
                        produtoSelecionado = null;
                        validarSelecao();
                    }
                });

                barcodeInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const barcode = e.target.value.trim();
                        if (barcode) {
                            buscarProdutoPorBarcode(barcode).then(produto => {
                                if (produto) {
                                    exibirProdutoInfo(produto);
                                    mostrarNotificacao(`Produto encontrado: ${produto.nome_produto}`, 'success');
                                } else {
                                    document.getElementById('produtoInfo').classList.add('hidden');
                                    produtoSelecionado = null;
                                    mostrarNotificacao('Produto não encontrado para este código de barras', 'error');
                                }
                                validarSelecao();
                            });
                        }
                    }
                });
            }

            // Função para buscar produto por código de barras
            async function buscarProdutoPorBarcode(barcode) {
                try {
                    const response = await fetch('../controllers/controller_solicitar.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'barcode=' + encodeURIComponent(barcode) + '&action=buscar'
                    });

                    // Verificar se a resposta é JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        console.error('Resposta não é JSON:', contentType);
                        const text = await response.text();
                        console.error('Resposta recebida:', text);
                        return null;
                    }

                    if (response.ok) {
                        const data = await response.json();
                        console.log('Resposta do servidor:', data);
                        if (data.success && data.produto) {
                            return data.produto;
                        } else if (data.debug) {
                            console.log('Debug info:', data.debug);
                        }
                    }
                    return null;
                } catch (error) {
                    console.error('Erro ao buscar produto:', error);
                    return null;
                }
            }

            // Função para atualizar informações do produto quando selecionado no select
            function atualizarInfoProduto() {
                const produtoSelect = document.getElementById('produto');
                const selectedOption = produtoSelect.options[produtoSelect.selectedIndex];

                if (selectedOption && selectedOption.value) {
                    const barcode = selectedOption.getAttribute('data-barcode');
                    const quantidade = selectedOption.getAttribute('data-quantidade');
                    const nomeProduto = selectedOption.textContent.split(' - ')[0];

                    // Atualizar o campo de código de barras se estiver na opção barcode
                    const barcodeInput = document.getElementById('barcodeInput');
                    if (barcodeInput) {
                        barcodeInput.value = barcode;
                    }

                    // Mostrar informações do produto
                    const produtoInfo = document.getElementById('produtoInfo');
                    const produtoNome = document.getElementById('produtoNome');
                    const produtoEstoque = document.getElementById('produtoEstoque');

                    if (produtoInfo && produtoNome && produtoEstoque) {
                        produtoNome.textContent = nomeProduto;
                        produtoEstoque.textContent = `Estoque: ${quantidade} unidades`;
                        produtoInfo.classList.remove('hidden');
                    }

                    // Atualizar produto selecionado
                    produtoSelecionado = {
                        id: selectedOption.value,
                        nome_produto: nomeProduto,
                        quantidade: quantidade,
                        barcode: barcode
                    };

                    validarSelecao();
                }
            }

            // Adicionar evento para atualizar informações quando produto for selecionado
            document.getElementById('produto').addEventListener('change', atualizarInfoProduto);
        });
    </script>
</body>

</html>