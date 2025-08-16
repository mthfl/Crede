<?php 
require_once('../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Estoque</title>
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

        .card-item {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
            will-change: transform;
        }

        .card-item::before {
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

        .card-item:hover::before {
            opacity: 1;
        }

        .card-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 90, 36, 0.2), 0 10px 10px -5px rgba(0, 90, 36, 0.1);
            border-color: #FFA500;
        }

        .card-icon {
            transition: all 0.3s ease;
            z-index: 2;
            position: relative;
        }

        .card-item:hover .card-icon {
            transform: scale(1.1);
            color: #FFA500;
        }

        .card-item p {
            z-index: 2;
            position: relative;
            transition: color 0.3s ease;
        }

        .card-item:hover p {
            color: #005A24;
        }

        .logo-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: translateY(-50%) translateX(-50%) scale(1);
            }

            50% {
                transform: translateY(-50%) translateX(-50%) scale(1.05);
            }

            100% {
                transform: translateY(-50%) translateX(-50%) scale(1);
            }
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

        .card-item:hover .card-shine {
            left: 150%;
        }

        .card-badge {
            transition: all 0.3s ease;
        }

        .card-item:hover .card-badge {
            background-color: #FFA500;
            color: white;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            background-color: #FF5252;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
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
    </style>
</head>

<body class="min-h-screen flex flex-col font-sans bg-light">
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

    <!-- Botão de menu mobile -->
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
        <div class="flex flex-col items-center justify-start pt-16 md:pt-20">
            <h1 class="text-primary text-3xl md:text-4xl font-bold mb-6 md:mb-8 text-center page-title tracking-tight font-heading">GERENCIAMENTO DE ESTOQUE</h1>

            <!-- Layout: Leitor de código de barras (somente visual) -->
            <div class="w-full max-w-5xl mx-auto px-4 mb-8 md:mb-10">

                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-primary">
                        <i class="fas fa-barcode text-xl"></i>
                    </span>
                    <input
                        id="barcodeHome"
                        type="text"
                        placeholder="Escaneie o código de barras aqui"
                        class="w-full pl-12 pr-4 py-4 bg-white border-2 border-primary/50 focus:border-secondary focus:ring-2 focus:ring-secondary/40 rounded-2xl outline-none placeholder:text-gray-400 text-gray-700 shadow-card text-lg"
                        autocomplete="off" />
                </div>
            </div>


            <div id="cardsGrid" class="w-full max-w-5xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8 px-4 justify-center">

                <a href="./products/adc_produto.php" class="group animate-fade-in" style="animation-delay: 0.1s">
                    <div class="card-item bg-white border-2 border-primary rounded-xl md:rounded-2xl shadow-card w-full h-48 md:h-56 flex flex-col items-center justify-center p-4 md:p-6 relative">
                        <div class="card-shine"></div>
                        <i class="fas fa-plus-circle card-icon text-4xl md:text-5xl text-primary mb-4 md:mb-5"></i>
                        <p class="text-secondary font-bold text-center text-base md:text-lg leading-tight">ADICIONAR</p>
                    </div>
                </a>

                <a href="estoque.php" class="group animate-fade-in">
                    <div class="card-item bg-white border-2 border-primary rounded-xl md:rounded-2xl shadow-card w-full h-48 md:h-56 flex flex-col items-center justify-center p-4 md:p-6 relative">
                        <div class="card-shine"></div>
                        <i class="fas fa-boxes card-icon text-4xl md:text-5xl text-primary mb-4 md:mb-5"></i>
                        <p class="text-secondary font-bold text-center text-base md:text-lg leading-tight">ESTOQUE</p>
                    </div>
                </a>

                <a href="solicitar.php" class="group animate-fade-in" style="animation-delay: 0.2s">
                    <div class="card-item bg-white border-2 border-primary rounded-xl md:rounded-2xl shadow-card w-full h-48 md:h-56 flex flex-col items-center justify-center p-4 md:p-6 relative">
                        <div class="card-shine"></div>
                        <i class="fas fa-clipboard-list card-icon text-4xl md:text-5xl text-primary mb-4 md:mb-5"></i>
                        <p class="text-secondary font-bold text-center text-base md:text-lg leading-tight">RETIRAR</p>
                    </div>
                </a>

                <a href="relatorios.php" class="group animate-fade-in" style="animation-delay: 0.4s">
                    <div class="card-item bg-white border-2 border-primary rounded-xl md:rounded-2xl shadow-card w-full h-48 md:h-56 flex flex-col items-center justify-center p-4 md:p-6 relative">
                        <div class="card-shine"></div>
                        <i class="fas fa-chart-bar card-icon text-4xl md:text-5xl text-primary mb-4 md:mb-5"></i>
                        <p class="text-secondary font-bold text-center text-base md:text-lg leading-tight">RELATÓRIOS</p>
                    </div>
                </a>
            </div>
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

            // Card entrance animation
            const cards = document.querySelectorAll('.card-item');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('translate-y-0', 'opacity-100');
                    card.classList.remove('translate-y-4', 'opacity-0');
                }, index * 100);
            });

            // Lazy loading for images
            if ('loading' in HTMLImageElement.prototype) {
                const images = document.querySelectorAll('img[loading="lazy"]');
                images.forEach(img => {
                    img.loading = 'lazy';
                });
            }

            // Preload linked pages
            const links = document.querySelectorAll('a[target="_blank"]');
            links.forEach(link => {
                link.addEventListener('mouseover', () => {
                    const href = link.getAttribute('href');
                    if (href) {
                        fetch(href, {
                                mode: 'no-cors'
                            })
                            .catch(() => {});
                    }
                });
            });

            // Funcionalidade do campo de código de barras
            const barcodeInput = document.getElementById('barcodeHome');

            if (barcodeInput) {
                // Função para redirecionar com o código de barras
                function redirecionarComBarcode() {
                    const barcode = barcodeInput.value.trim();
                    if (barcode) {
                        // Redirecionar para o controller com o código de barras como parâmetro
                        window.location.href = `../control/controller_main.php?barcode=${encodeURIComponent(barcode)}`;
                    }
                }

                // Redirecionar quando pressionar Enter
                barcodeInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        redirecionarComBarcode();
                    }
                });

                // Redirecionar quando o input perder o foco (opcional)
                barcodeInput.addEventListener('blur', function() {
                    if (this.value.trim()) {
                        redirecionarComBarcode();
                    }
                });

                // Focar no input quando a página carregar
                barcodeInput.focus();
            }

            // Funcionalidade da barra de pesquisa
            const searchInput = document.getElementById('searchInput');
            const searchButton = document.getElementById('searchButton');

            if (searchInput && searchButton) {
                // Função para realizar a pesquisa
                function realizarPesquisa() {
                    const termo = searchInput.value.trim();
                    if (termo) {
                        // Redirecionar para a página de estoque com o termo de pesquisa
                        window.location.href = `../view/estoque.php?search=${encodeURIComponent(termo)}`;
                    }
                }

                // Pesquisar quando pressionar Enter
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        realizarPesquisa();
                    }
                });

                // Pesquisar quando clicar no botão
                searchButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    realizarPesquisa();
                });

                // Efeito de hover no botão
                searchButton.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.1)';
                });

                searchButton.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            }
        });
    </script>
</body>

</html>