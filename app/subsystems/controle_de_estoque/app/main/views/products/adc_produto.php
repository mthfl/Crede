<?php
require_once('../../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once('../../models/model.select.php');
$select = new select();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Produto</title>
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
            background-color: #F8FAF9;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #005A24 0%, #1A3C34 100%);
        }

        .custom-radio {
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .custom-radio:hover {
            background-color: rgba(0, 90, 36, 0.05);
        }

        .custom-radio input[type="radio"] {
            position: relative;
            cursor: pointer;
            appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #005A24;
            border-radius: 50%;
            margin-right: 10px;
            outline: none;
        }

        .custom-radio input[type="radio"]:checked {
            background-color: #FFA500;
            border-color: #FFA500;
        }

        .custom-radio input[type="radio"]:checked::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: white;
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

        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background-color: white;
            margin: 2px 0;
            transition: all 0.3s ease;
        }

        .mobile-menu {
            transition: max-height 0.3s ease-in-out;
        }

        @media (max-width: 768px) {
            .hamburger {
                display: flex;
            }

            .nav-links {
                display: none;
            }

            .nav-links.active {
                display: flex;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: linear-gradient(135deg, #005A24 0%, #1A3C34 100%);
                padding: 1rem;
                max-height: 400px;
            }
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

        .social-icon {
            transition: all 0.3s ease;
        }

        .social-icon:hover {
            transform: translateY(-3px);
            filter: drop-shadow(0 4px 3px rgba(255, 165, 0, 0.3));
        }

        .page-title {
            position: relative;
            display: inline-block;
            background: linear-gradient(135deg, #005A24 0%, #1A3C34 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 2px 4px rgba(0, 90, 36, 0.3);
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 4px;
            background-color: #FFA500;
            border-radius: 4px;
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

            main {
                margin-left: 0 !important;
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
                <a href="../index.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                    <i class="fas fa-home mr-3 text-lg"></i>
                    <span>Início</span>
                </a>
                <a href="../estoque.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                    <i class="fas fa-boxes mr-3 text-lg"></i>
                    <span>Estoque</span>
                </a>
                <a href="./adc_produto.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2 active">
                    <i class="fas fa-plus-circle mr-3 text-lg"></i>
                    <span>Adicionar</span>
                </a>
                <a href="../solicitar.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                    <i class="fas fa-clipboard-list mr-3 text-lg"></i>
                    <span>Solicitar</span>
                </a>
                <a href="../relatorios.php" class="sidebar-link flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                    <i class="fas fa-chart-bar mr-3 text-lg"></i>
                    <span>Relatórios</span>
                </a>
            </nav>
            <div class="p-4 border-t border-white/20">
                <a href="../../../../../../main/views/subsystems.php" class="w-full bg-transparent border border-white/40 hover:bg-white/10 text-white py-2 px-4 rounded-lg transition-all duration-200 flex items-center justify-center">
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

    <main class="ml-0 md:ml-64 px-4 py-8 md:py-12 flex-1 transition-all duration-300">
        <div class="text-center mb-10">
            <h1 class="text-primary text-3xl md:text-4xl font-bold mb-8 md:mb-6 text-center page-title tracking-tight font-heading inline-block mx-auto">ADICIONAR</h1>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8 max-w-2xl w-full border-2 border-primary mx-auto">
            <form action="../../controllers/controller_crud_produto.php" method="POST" class="space-y-6">
                <div class="space-y-4">
                    <div class="space-y-3">
                        <label class="text-primary font-semibold text-sm">Tipo de Produto:</label>
                        <div class="space-y-2">
                            <label class="custom-radio flex items-center">
                                <input type="radio" name="tipo_produto" value="com_codigo" id="com_codigo" checked>
                                <span class="ml-2 text-gray-700">Produto com código</span>
                            </label>
                            <label class="custom-radio flex items-center">
                                <input type="radio" name="tipo_produto" value="sem_codigo" id="sem_codigo">
                                <span class="ml-2 text-gray-700">Produto sem código</span>
                            </label>
                        </div>
                    </div>

                    <div id="com_codigo_fields">
                        <input type="text" placeholder="BARCODE" id="barcode" name="barcode" required
                            class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent text-center font-semibold"
                            aria-label="Código de barras do produto">
                    </div>

                    <div id="sem_codigo_fields" class="hidden space-y-4">
                        
                    </div>

                    <button type="submit" name="btn" value="Adicionar" 
                        class="w-full bg-secondary text-white font-bold py-3 px-4 rounded-lg hover:bg-opacity-90 transition-colors"
                        aria-label="Adicionar produto">
                        CADASTRAR
                    </button>
                </div>
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

    <button class="back-to-top hidden fixed bottom-6 right-6 z-50 bg-secondary hover:bg-secondary/90 text-white w-12 h-12 rounded-full shadow-lg transition-all duration-300 flex items-center justify-center group">
        <i class="fas fa-chevron-up text-lg group-hover:scale-110 transition-transform duration-300"></i>
    </button>

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
                        if (window.innerWidth <= 768) {
                            footerContent.style.marginLeft = sidebar.classList.contains('show') ? '0' : '0';
                        } else {
                            footerContent.style.marginLeft = '16rem';
                        }
                    };
                    adjustFooter();
                    menuButton.addEventListener('click', adjustFooter);
                    window.addEventListener('resize', adjustFooter);
                }
            }

            // Back to top button
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

                backToTop.addEventListener('click', () => {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }

            // Initialize Select2
            $('.js-example-basic-single').select2({
                placeholder: "Selecione uma categoria",
                allowClear: true,
                width: '100%'
            });

            // Control form fields visibility
            const comCodigoRadio = document.getElementById('com_codigo');
            const semCodigoRadio = document.getElementById('sem_codigo');
            const comCodigoFields = document.getElementById('com_codigo_fields');
            const semCodigoFields = document.getElementById('sem_codigo_fields');
            const submitButton = document.querySelector('button[type="submit"]');

            function toggleFields() {
                if (comCodigoRadio.checked) {
                    comCodigoFields.classList.remove('hidden');
                    semCodigoFields.classList.add('hidden');
                    submitButton.classList.remove('hidden');
                    // Set required only for barcode
                    comCodigoFields.querySelector('input').required = true;
                    semCodigoFields.querySelectorAll('input, select').forEach(field => field.required = false);
                } else {
                    comCodigoFields.classList.add('hidden');
                    semCodigoFields.classList.remove('hidden');
                    submitButton.classList.remove('hidden');
                    // Set required for all fields in sem_codigo
                    comCodigoFields.querySelector('input').required = false;
                    semCodigoFields.querySelectorAll('input, select').forEach(field => field.required = true);
                }
            }

            // Initialize form state
            toggleFields();

            // Add event listeners
            comCodigoRadio.addEventListener('change', toggleFields);
            semCodigoRadio.addEventListener('change', toggleFields);

            // Force uppercase for barcode
            const barcodeInput = document.getElementById('barcode');
            barcodeInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });

            // Force uppercase for product name
            const nomeInput = document.getElementById('nome');
            nomeInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        });
    </script>
</body>
</html>