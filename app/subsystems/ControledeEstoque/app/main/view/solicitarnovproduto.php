<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Novo Produto</title>
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

        .card {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
            will-change: transform;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 90, 36, 0.2), 0 10px 10px -5px rgba(0, 90, 36, 0.1);
            border-color: #FFA500;
        }

        .card::before {
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

        .card:hover::before {
            opacity: 1;
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

        .card:hover .card-shine {
            left: 150%;
        }

        /* Estilos para o header melhorado */
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
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: linear-gradient(135deg, #005A24 0%, #1A3C34 100%);
                padding: 1rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                z-index: 40;
            }

            .header-nav.show {
                display: flex;
                flex-direction: column;
            }

            .header-nav-link {
                padding: 0.75rem 1rem;
                text-align: center;
                margin: 0.25rem 0;
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
                z-index: 10;
            }

            .mobile-menu-button span {
                width: 100%;
                height: 3px;
                background-color: white;
                border-radius: 10px;
                transition: all 0.3s linear;
                position: relative;
                transform-origin: 1px;
            }

            .mobile-menu-button span:first-child.active {
                transform: rotate(45deg);
                top: 0px;
            }

            .mobile-menu-button span:nth-child(2).active {
                opacity: 0;
            }

            .mobile-menu-button span:nth-child(3).active {
                transform: rotate(-45deg);
                top: -1px;
            }
        }
    </style>
</head>

<body class="min-h-screen flex flex-col font-sans bg-light">
    <!-- Header Melhorado -->
    <header class="sticky top-0 bg-gradient-to-r from-primary to-dark text-white py-4 shadow-lg z-50">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center">
                <img src="../assets/imagens/logostgm.png" alt="Logo STGM" class="h-12 mr-3 transition-transform hover:scale-105">
                <span class="text-white font-heading text-xl font-semibold hidden md:inline">STGM Estoque</span>
            </div>

            <button class="mobile-menu-button focus:outline-none" aria-label="Menu" id="menuButton">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav class="header-nav md:flex items-center space-x-1" id="headerNav">

                <a href="paginainicial.php" class="header-nav-link flex items-center">

                    <i class="fas fa-home mr-2"></i>
                    <span>Início</span>
                </a>
                <a href="estoque.php" class="header-nav-link flex items-center">
                    <i class="fas fa-boxes mr-2"></i>
                    <span>Estoque</span>
                </a>
                <a href="adicionarproduto.php" class="header-nav-link flex items-center">
                    <i class="fas fa-plus-circle mr-2"></i>
                    <span>Adicionar</span>
                </a>
             
                <div class="relative group">
                    <a class="header-nav-link active flex items-center cursor-pointer">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        <span>Solicitar</span>
                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </a>
                    <div class="absolute left-0 mt-1 w-48 bg-white rounded-lg shadow-lg overflow-hidden transform scale-0 group-hover:scale-100 transition-transform origin-top z-50">
                        <a href="solicitar.php" class="block px-4 py-2 text-primary hover:bg-primary hover:text-white transition-colors">
                            <i class="fas fa-clipboard-check mr-2"></i>Solicitar Produto
                        </a>
                        <a href="solicitarnovproduto.php" class="block px-4 py-2 text-primary hover:bg-primary hover:text-white transition-colors bg-primary bg-opacity-10">
                            <i class="fas fa-plus-square mr-2"></i>Solicitar Novo Produto
                        </a>
                    </div>
                </div>
                <a href="relatorios.php" class="header-nav-link flex items-center">
                    <i class="fas fa-chart-bar mr-2"></i>
                    <span>Relatórios</span>
                </a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8 md:py-12 flex-1">
        <div class="text-center mb-10">
            <h1 class="text-primary text-3xl md:text-4xl font-bold mb-8 md:mb-6 text-center page-title tracking-tight font-heading inline-block mx-auto">SOLICITAR NOVO PRODUTO</h1>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8 max-w-5xl w-full border-2 border-primary mx-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mx-auto w-full">
                <!-- Card 1 -->
                <div class="card bg-white border-2 border-primary rounded-xl shadow-lg p-6 flex flex-col items-center">
                    <div class="card-shine"></div>
                    <div class="text-4xl mb-4">👤</div>
                    <p class="font-bold text-primary text-xl mb-2 text-center">PAPEL HIGIÊNICO</p>
                    <p class="text-gray-600 text-center">CONTATO: (85) 9-9999-9999</p>
                </div>

                <!-- Card 2 -->
                <div class="card bg-white border-2 border-primary rounded-xl shadow-lg p-6 flex flex-col items-center" style="animation-delay: 0.1s">
                    <div class="card-shine"></div>
                    <div class="text-4xl mb-4">👤</div>
                    <p class="font-bold text-primary text-xl mb-2 text-center">PAPEL TOALHA</p>
                    <p class="text-gray-600 text-center">CONTATO: (85) 9-8888-8888</p>
                </div>

                <!-- Card 3 -->
                <div class="card bg-white border-2 border-primary rounded-xl shadow-lg p-6 flex flex-col items-center" style="animation-delay: 0.2s">
                    <div class="card-shine"></div>
                    <div class="text-4xl mb-4">👤</div>
                    <p class="font-bold text-primary text-xl mb-2 text-center">SABÃO EM PÓ</p>
                    <p class="text-gray-600 text-center">CONTATO: (85) 9-7777-7777</p>
                </div>

                <!-- Card 4 -->
                <div class="card bg-white border-2 border-primary rounded-xl shadow-lg p-6 flex flex-col items-center" style="animation-delay: 0.3s">
                    <div class="card-shine"></div>
                    <div class="text-4xl mb-4">👤</div>
                    <p class="font-bold text-primary text-xl mb-2 text-center">DETERGENTE</p>
                    <p class="text-gray-600 text-center">CONTATO: (85) 9-6666-6666</p>
                </div>

                <!-- Card 5 -->
                <div class="card bg-white border-2 border-primary rounded-xl shadow-lg p-6 flex flex-col items-center" style="animation-delay: 0.4s">
                    <div class="card-shine"></div>
                    <div class="text-4xl mb-4">👤</div>
                    <p class="font-bold text-primary text-xl mb-2 text-center">ÁLCOOL EM GEL</p>
                    <p class="text-gray-600 text-center">CONTATO: (85) 9-5555-5555</p>
                </div>

                <!-- Card 6 -->
                <div class="card bg-white border-2 border-primary rounded-xl shadow-lg p-6 flex flex-col items-center" style="animation-delay: 0.5s">
                    <div class="card-shine"></div>
                    <div class="text-4xl mb-4">👤</div>
                    <p class="font-bold text-primary text-xl mb-2 text-center">ESPONJA</p>
                    <p class="text-gray-600 text-center">CONTATO: (85) 9-4444-4444</p>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gradient-to-r from-primary to-dark text-white py-6 mt-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Sobre a Escola -->
                <div>
                    <h3 class="font-heading text-lg font-semibold mb-3 flex items-center">
                        <i class="fas fa-school mr-2 text-sm"></i>
                        EEEP STGM
                    </h3>
                    <p class="text-xs leading-relaxed">
                        <i class="fas fa-map-marker-alt mr-1 text-xs"></i>
                        AV. Marta Maria Carvalho Nojoza, SN<br>
                        Maranguape - CE
                    </p>
                </div>

                <!-- Contato -->
                <div>
                    <h3 class="font-heading text-lg font-semibold mb-3 flex items-center">
                        <i class="fas fa-address-book mr-2 text-sm"></i>
                        Contato
                    </h3>
                    <div class="text-xs leading-relaxed space-y-1">
                        <p class="flex items-start">
                            <i class="fas fa-phone-alt mr-1 mt-0.5 text-xs"></i>
                            (85) 3341-3990
                        </p>
                        <p class="flex items-start">
                            <i class="fas fa-envelope mr-1 mt-0.5 text-xs"></i>
                            eeepsantariamata@gmail.com
                        </p>
                    </div>
                </div>

                <!-- Desenvolvedores em Grid -->
                <div>
                    <h3 class="font-heading text-lg font-semibold mb-3 flex items-center">
                        <i class="fas fa-code mr-2 text-sm"></i>
                        Dev Team
                    </h3>
                    <div class="grid grid-cols-2 gap-2">
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

            <!-- Rodapé inferior compacto -->
            <div class="border-t border-white/20 pt-4 mt-4 text-center">
                <p class="text-xs">
                    © 2024 STGM v1.2.0 | Desenvolvido por alunos EEEP STGM
                </p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Menu mobile toggle
            const menuButton = document.getElementById('menuButton');
            const headerNav = document.getElementById('headerNav');

            if (menuButton && headerNav) {
                menuButton.addEventListener('click', function() {
                    headerNav.classList.toggle('show');

                    // Animação para o botão do menu
                    const spans = menuButton.querySelectorAll('span');
                    spans.forEach(span => {
                        span.classList.toggle('active');
                    });
                });
            }

            // Adicionar suporte para dropdown no mobile
            const dropdownToggle = document.querySelector('.group > a');
            const dropdownMenu = document.querySelector('.group > div');

            if (window.innerWidth <= 768) {
                dropdownToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    dropdownMenu.classList.toggle('scale-0');
                    dropdownMenu.classList.toggle('scale-100');
                });
            }

            // Card entrance animation
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('translate-y-0', 'opacity-100');
                    card.classList.remove('translate-y-4', 'opacity-0');
                }, index * 100);
            });
        });
    </script>
</body>

</html>