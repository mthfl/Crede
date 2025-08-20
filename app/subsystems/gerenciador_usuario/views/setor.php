<?php
require_once(__DIR__ . '/../../../main/models/sessions.php');
require_once(__DIR__ . '/../../../main/models/model.usuario.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

$modelUsuario = new model_usuario();
$dadosUsuario = [];
try {
    $dadosUsuario = $modelUsuario->getDadosUsuario((int)($_SESSION['id'] ?? 0));
} catch (Throwable $e) {
    $dadosUsuario = [];
}

$userName = isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Usuário';
$userSetor = isset($_SESSION['setor']) ? $_SESSION['setor'] : 'Sistema de Gestão';
$userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$userInitial = function_exists('mb_substr') ? mb_strtoupper(mb_substr($userName, 0, 1, 'UTF-8'), 'UTF-8') : strtoupper(substr($userName, 0, 1));
$fotoPerfil = isset($dadosUsuario['foto_perfil']) ? $dadosUsuario['foto_perfil'] : null;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="content-language" content="pt-BR">
    <title>Gerenciar Setores - CREDE</title>
    <link rel="icon" type="image/png" href="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
                        gray: {
                            50: '#F9FAFB',
                            100: '#F3F4F6',
                            200: '#E5E7EB',
                            300: '#D1D5DB',
                            400: '#9CA3AF',
                            500: '#6B7280',
                            600: '#4B5563',
                            700: '#374151',
                            800: '#1F2937',
                            900: '#111827'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Poppins', 'sans-serif']
                    },
                    backgroundImage: {
                        'gradient-primary': 'linear-gradient(135deg, #005A24 0%, #1A3C34 100%)',
                        'gradient-secondary': 'linear-gradient(135deg, #F4A261 0%, #E76F51 100%)',
                        'gradient-light': 'linear-gradient(135deg, #E8F4F8 0%, #F7F3E9 100%)',
                        'gradient-dark': 'linear-gradient(135deg, #2D5016 0%, #005A24 100%)',
                        'gradient-hero': 'linear-gradient(135deg, #005A24 0%, #2D5016 25%, #7FB069 50%, #005A24 75%, #1A3C34 100%)',
                        'gradient-card': 'linear-gradient(145deg, #ffffff 0%, #f8faf9 100%)',
                        'gradient-glass': 'linear-gradient(145deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.7) 100%)'
                    },
                    boxShadow: {
                        'strong': '0 10px 40px -10px rgba(0, 0, 0, 0.15), 0 2px 10px -2px rgba(0, 0, 0, 0.05)',
                        'primary': '0 10px 25px -5px rgba(0, 90, 36, 0.3)',
                        'secondary': '0 10px 25px -5px rgba(255, 165, 0, 0.3)',
                        'glass': '0 8px 32px 0 rgba(31, 38, 135, 0.37)',
                        'glow': '0 0 20px rgba(0, 90, 36, 0.3)',
                        'card-hover': '0 20px 60px -10px rgba(0, 0, 0, 0.15), 0 8px 25px -5px rgba(0, 0, 0, 0.1)'
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'slide-in': 'slideIn 0.5s ease-out',
                        'bounce-subtle': 'bounceSubtle 0.8s ease-in-out',
                        'float': 'float 6s ease-in-out infinite',
                        'sway': 'sway 4s ease-in-out infinite',
                        'pulse-glow': 'pulseGlow 2s ease-in-out infinite',
                        'scale-in': 'scaleIn 0.4s ease-out',
                        'shimmer': 'shimmer 2s linear infinite'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        slideIn: {
                            '0%': { transform: 'translateX(-100%)', opacity: '0' },
                            '100%': { transform: 'translateX(0)', opacity: '1' }
                        },
                        bounceSubtle: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-8px)' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px) rotate(0deg)' },
                            '50%': { transform: 'translateY(-20px) rotate(3deg)' }
                        },
                        sway: {
                            '0%, 100%': { transform: 'translateX(0px) rotate(0deg)' },
                            '50%': { transform: 'translateX(10px) rotate(1deg)' }
                        },
                        pulseGlow: {
                            '0%, 100%': { boxShadow: '0 0 20px rgba(0, 90, 36, 0.3)' },
                            '50%': { boxShadow: '0 0 30px rgba(0, 90, 36, 0.5)' }
                        },
                        scaleIn: {
                            '0%': { transform: 'scale(0.9)', opacity: '0' },
                            '100%': { transform: 'scale(1)', opacity: '1' }
                        },
                        shimmer: {
                            '0%': { transform: 'translateX(-100%)' },
                            '100%': { transform: 'translateX(100%)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #F8FAF9 0%, #E8F4F8 100%);
            min-height: 100vh;
        }

        /* Enhanced card styles */
        .card-enhanced {
            background: linear-gradient(145deg, #ffffff 0%, #f8faf9 100%);
            border: 1px solid rgba(229, 231, 235, 0.6);
            backdrop-filter: blur(10px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: visible;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            width: 100%;
            height: fit-content;
            min-height: 200px;
        }
        
        .card-enhanced:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 40px -10px rgba(0, 0, 0, 0.15), 0 8px 20px -5px rgba(0, 0, 0, 0.1);
            border-color: rgba(255, 165, 0, 0.3);
        }

        /* Responsive grid improvements - FIXED */
        @media (min-width: 1024px) {
            .card-enhanced {
                min-width: 100%;
                max-width: 100%;
                width: 100%;
            }
        }
        
        @media (min-width: 1280px) {
            .card-enhanced {
                min-width: 100%;
                max-width: 100%;
                width: 100%;
            }
        }
        
        @media (min-width: 1536px) {
            .card-enhanced {
                min-width: 100%;
                max-width: 100%;
                width: 100%;
            }
        }

        /* Icon container with gradient background */
        .icon-container {
            background: linear-gradient(135deg, var(--bg-from), var(--bg-to));
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .icon-container::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.3) 50%, transparent 70%);
            transform: translateX(-100%);
            transition: transform 0.6s;
        }

        .card-enhanced:hover .icon-container::after {
            transform: translateX(100%);
        }

        /* Enhanced header with better mobile spacing */
        .header-glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(229, 231, 235, 0.8);
        }

        /* Loading animation improvements */
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f4f6;
            border-top: 4px solid #005A24;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive floating background elements */
        .bg-decoration {
            position: fixed;
            pointer-events: none;
            z-index: -1;
        }

        .bg-circle-1 {
            top: 10%;
            right: 5%;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(0, 90, 36, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }

        .bg-circle-2 {
            bottom: 20%;
            left: 5%;
            width: 120px;
            height: 120px;
            background: radial-gradient(circle, rgba(255, 165, 0, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: sway 6s ease-in-out infinite reverse;
        }

        @media (min-width: 768px) {
            .bg-circle-1 {
                width: 200px;
                height: 200px;
                right: 10%;
            }
            
            .bg-circle-2 {
                width: 150px;
                height: 150px;
            }
        }

        /* Button enhancements */
        .btn-logout {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-logout::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.2), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s;
        }

        .btn-logout:hover::before {
            transform: translateX(100%);
        }

        .btn-logout:hover {
            background: #f3f4f6;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Staggered animation for cards */
        .card-1 { animation-delay: 0.1s; }
        .card-2 { animation-delay: 0.2s; }
        .card-3 { animation-delay: 0.3s; }

        /* Enhanced input styles */
        .input-enhanced {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            border: 2px solid #E5E7EB;
        }

        .input-enhanced:focus {
            transform: none;
            outline: none;
            border-color: #FFA500;
            box-shadow: 0 0 0 3px rgba(255, 165, 0, 0.1);
            background: white;
        }

        /* Table enhancements */
        .table-enhanced {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(229, 231, 235, 0.8);
        }

        .table-row {
            transition: all 0.2s ease;
        }
        
        .table-row:hover {
            background: rgba(249, 250, 251, 0.8);
            transform: translateY(-1px);
        }

        /* Status badge enhancements */
        .status-badge {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .status-badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }
        
        .status-badge:hover::before {
            left: 100%;
        }

        /* Notification styles */
        .notification-enter {
            transform: translateX(100%);
        }

        .notification-exit {
            transform: translateX(100%);
        }

        /* Enhanced modal responsiveness */
        .modal-content {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Better mobile modal sizing */
        @media (max-width: 640px) {
            .modal-content {
                margin: 1rem;
                max-height: calc(100vh - 2rem);
            }
        }

        /* Input focus improvements */
        .input-enhanced:focus {
            border-color: #FFA500;
            box-shadow: 0 0 0 4px rgba(255, 165, 0, 0.1);
            transform: translateY(-1px);
        }

        /* Improved responsive text scaling */
        @media (max-width: 640px) {
            .responsive-text-lg {
                font-size: 1rem;
                line-height: 1.5rem;
            }
            
            .responsive-text-xl {
                font-size: 1.125rem;
                line-height: 1.75rem;
            }
            
            .responsive-text-2xl {
                font-size: 1.25rem;
                line-height: 1.75rem;
            }
        }
        /* Container improvements */
        .main-container {
            width: 100%;
            max-width: 100%;
            padding: 0 1rem;
        }
        
        @media (min-width: 640px) {
            .main-container {
                padding: 0 1.5rem;
            }
        }
        
        @media (min-width: 1024px) {
            .main-container {
                padding: 0 2rem;
            }
        }
    </style>
</head>

<body class="text-gray-800 font-sans min-h-screen">
    <!-- Background decorations -->
    <div class="bg-decoration bg-circle-1"></div>
    <div class="bg-decoration bg-circle-2"></div>
    
    <div class="min-h-screen">
        <!-- Enhanced header with better responsive spacing and layout -->
        <header class="header-glass sticky top-0 z-40 px-3 sm:px-4 md:px-6 py-2 sm:py-3 md:py-4 animate-slide-in">
            <div class="flex items-center justify-between max-w-7xl mx-auto">
                <div class="flex items-center gap-2 sm:gap-3 md:gap-4">
                    <a href="../index.php" class="p-1.5 sm:p-2 md:p-3 rounded-lg sm:rounded-xl hover:bg-gray-100 text-gray-600 transition-all group">
                        <i class="fa-solid fa-arrow-left text-sm sm:text-base md:text-lg group-hover:scale-110 transition-transform"></i>
                    </a>
                    <div class="w-6 h-6 sm:w-8 sm:h-8 md:w-12 md:h-12 rounded-lg sm:rounded-xl md:rounded-2xl flex items-center justify-center">
                        <img class="w-5 h-5 sm:w-6 sm:h-6 md:w-10 md:h-10 object-contain" src="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png" alt="Logo CREDE">
                    </div>
                    <div>
                        <h1 class="font-bold text-sm sm:text-base md:text-xl text-dark font-heading">CREDE</h1>
                        <p class="text-xs sm:text-xs md:text-sm text-gray-500 font-medium hidden sm:block">Gerenciamento de Setores</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 sm:gap-3 md:gap-4">
                    <div class="hidden md:block text-right">
                        <p class="text-sm font-semibold text-dark"><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($userSetor, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="relative">
                        <button id="userMenuButton" class="p-1 rounded-full hover:ring-2 hover:ring-primary/30 transition">
                            <?php if (!empty($fotoPerfil) && $fotoPerfil !== 'default.png') { ?>
                                <img src="<?php echo '../../../main/assets/fotos_perfil/' . htmlspecialchars($fotoPerfil, ENT_QUOTES, 'UTF-8'); ?>" alt="Foto de perfil" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full object-cover border border-gray-200">
                            <?php } else { ?>
                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-gradient-to-br from-primary to-dark text-white flex items-center justify-center font-semibold">
                                    <?php echo htmlspecialchars($userInitial, ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            <?php } ?>
                        </button>
                        <div id="userMenu" class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-200 hidden">
                            <div class="p-4 border-b">
                                <p class="font-semibold text-dark truncate"><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php if (!empty($userEmail)) { ?><p class="text-sm text-gray-500 truncate"><?php echo htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8'); ?></p><?php } ?>
                                <p class="text-xs text-gray-400 mt-1"><?php echo htmlspecialchars($userSetor, ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                            <a href="<?php echo '../../../main/views/perfil.php'; ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fa-solid fa-user mr-2"></i> Meu Perfil
                            </a>
                            <button onclick="logout()" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Sair
                            </button>
                        </div>
                    </div>
                    <button onclick="logout()" class="btn-logout p-1.5 sm:p-2 md:p-3 rounded-lg sm:rounded-xl text-gray-600 hover:text-dark transition-all">
                        <i class="fa-solid fa-arrow-right-from-bracket text-sm sm:text-base md:text-lg"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="px-2 sm:px-4 py-4 sm:py-8">
            <div class="max-w-full mx-auto main-container">
                <!-- Action Buttons -->
                <div class="flex justify-center mb-8 sm:mb-12 animate-slide-up">
                    <button onclick="openSectorForm()" class="bg-gradient-to-r from-secondary to-orange-500 hover:from-secondary/90 hover:to-orange-500/90 text-white px-6 py-3 rounded-xl font-semibold flex items-center gap-3 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center">
                            <i class="fa-solid fa-building text-white text-sm"></i>
                        </div>
                        <span class="text-sm sm:text-base">Cadastrar Setor</span>
                    </button>
                </div>

                <!-- Setores Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4 sm:gap-6 w-full" id="setoresGrid">
                    <!-- Populado via JavaScript -->
                </div>
            </div>
        </main>
    </div>

    <!-- Enhanced modal with better mobile responsiveness -->
    <div id="modalSetor" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-3 sm:p-4 z-40">
        <div class="bg-white w-full max-w-md sm:max-w-lg rounded-xl sm:rounded-2xl shadow-2xl max-h-[95vh] sm:max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="modalSetorContent">
            <div class="p-4 sm:p-6 md:p-8 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-gradient-to-br from-secondary to-orange-500 text-white flex items-center justify-center">
                        <i class="fa-solid fa-building text-lg sm:text-xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 id="modalTitle" class="text-lg sm:text-xl md:text-2xl font-bold text-dark font-heading responsive-text-2xl">Cadastrar Setor</h3>
                        <p class="text-gray-600 text-xs sm:text-sm">Preencha as informações do setor</p>
                    </div>
                </div>
                <button class="absolute top-4 right-4 sm:top-6 sm:right-6 p-2 rounded-xl hover:bg-gray-100 transition-all group" onclick="closeModal('modalSetor')">
                    <i class="fa-solid fa-xmark text-gray-400 text-lg group-hover:text-gray-600 group-hover:scale-110 transition-all"></i>
                </button>
            </div>
            <div class="p-4 sm:p-6 md:p-8">
                <div class="space-y-4 sm:space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-dark mb-2 sm:mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-tag text-secondary"></i>
                            Nome do Setor *
                        </label>
                        <input id="inpNomeSetor" type="text" class="input-enhanced w-full px-3 sm:px-4 py-3 sm:py-4 rounded-lg sm:rounded-xl transition-all text-sm sm:text-base border-2 focus:border-secondary focus:ring-4 focus:ring-secondary/10" placeholder="Digite o nome do setor">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-dark mb-2 sm:mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-align-left text-secondary"></i>
                            Descrição
                        </label>
                        <textarea id="inpDescricaoSetor" class="input-enhanced w-full px-3 sm:px-4 py-3 sm:py-4 rounded-lg sm:rounded-xl transition-all text-sm sm:text-base border-2 focus:border-secondary focus:ring-4 focus:ring-secondary/10 resize-none" rows="3" placeholder="Digite uma descrição para o setor"></textarea>
                    </div>
                </div>
            </div>
            <div class="p-4 sm:p-6 md:p-8 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                <button class="px-4 sm:px-6 py-3 rounded-lg sm:rounded-xl border-2 border-gray-300 font-semibold text-gray-700 hover:bg-gray-100 hover:border-gray-400 transition-all text-sm sm:text-base order-2 sm:order-1" onclick="closeModal('modalSetor')">
                    <i class="fa-solid fa-times mr-2"></i>Cancelar
                </button>
                <button class="px-4 sm:px-6 py-3 bg-gradient-to-r from-secondary to-orange-500 text-white font-semibold rounded-lg sm:rounded-xl hover:from-secondary/90 hover:to-orange-500/90 transition-all text-sm sm:text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 order-1 sm:order-2" onclick="saveSector()">
                    <i class="fa-solid fa-save mr-2"></i>Salvar Setor
                </button>
            </div>
        </div>
    </div>

    <!-- Enhanced delete modal with better mobile layout -->
    <div id="modalDelete" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-3 sm:p-4 z-50">
        <div class="bg-white w-full max-w-sm sm:max-w-md rounded-xl sm:rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modalDeleteContent">
            <div class="p-4 sm:p-6 md:p-8 text-center">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4 sm:mb-6">
                    <i class="fa-solid fa-exclamation-triangle text-2xl sm:text-3xl text-red-500"></i>
                </div>
                <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-dark font-heading mb-3 sm:mb-4 responsive-text-2xl">Confirmar Exclusão</h3>
                <p class="text-gray-600 text-sm sm:text-base mb-4 sm:mb-6 leading-relaxed">
                    Tem certeza que deseja excluir o setor <span class="font-semibold text-dark" id="deleteSectorName"></span>?
                </p>
                <p class="text-xs sm:text-sm text-red-600 bg-red-50 px-3 sm:px-4 py-2 sm:py-3 rounded-lg border border-red-200 mb-4 sm:mb-6">
                    <i class="fa-solid fa-info-circle mr-2"></i>
                    Esta ação não pode ser desfeita.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <button class="px-4 sm:px-6 py-3 rounded-lg sm:rounded-xl border-2 border-gray-300 font-semibold text-gray-700 hover:bg-gray-100 hover:border-gray-400 transition-all text-sm sm:text-base order-2 sm:order-1" onclick="closeModal('modalDelete')">
                        <i class="fa-solid fa-times mr-2"></i>Cancelar
                    </button>
                    <button class="px-4 sm:px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-lg sm:rounded-xl hover:from-red-600 hover:to-red-700 transition-all text-sm sm:text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 order-1 sm:order-2" onclick="confirmDelete()">
                        <i class="fa-solid fa-trash mr-2"></i>Excluir Setor
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const setores = [
            { id: 1, nome: 'TI', descricao: 'Tecnologia da Informação', usuarios: 12, status: 'Ativo' },
            { id: 2, nome: 'Financeiro', descricao: 'Setor Financeiro e Contábil', usuarios: 8, status: 'Ativo' },
            { id: 3, nome: 'RH', descricao: 'Recursos Humanos', usuarios: 6, status: 'Ativo' },
            { id: 4, nome: 'Compras', descricao: 'Setor de Compras e Suprimentos', usuarios: 4, status: 'Ativo' },
            { id: 5, nome: 'Administrativo', descricao: 'Administração Geral', usuarios: 10, status: 'Ativo' },
            { id: 6, nome: 'Pedagógico', descricao: 'Coordenação Pedagógica', usuarios: 15, status: 'Ativo' },
            { id: 7, nome: 'Coordenação', descricao: 'Coordenação Geral', usuarios: 5, status: 'Ativo' }
        ];

        let editingSectorId = null;
        let deletingSectorId = null;

        function logout() {
            const confirmDialog = confirm('🚪 Deseja sair do sistema CREDE?');
            if (confirmDialog) {
                document.body.style.opacity = '0.7';
                document.body.style.pointerEvents = 'none';
                setTimeout(() => { window.location.href = '../../main/views/subsystems.php'; }, 500);
            }
        }

        function openSectorForm() {
            document.getElementById('modalTitle').textContent = 'Cadastrar Setor';
            document.getElementById('inpNomeSetor').value = '';
            document.getElementById('inpDescricaoSetor').value = '';
            editingSectorId = null;
            openModal('modalSetor');
        }

        function openEditSector(sectorId) {
            const sector = setores.find(s => s.id === sectorId);
            if (sector) {
                document.getElementById('modalTitle').textContent = 'Editar Setor';
                document.getElementById('inpNomeSetor').value = sector.nome;
                document.getElementById('inpDescricaoSetor').value = sector.descricao;
                editingSectorId = sectorId;
                openModal('modalSetor');
            }
        }

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Animar entrada do modal
            setTimeout(() => {
                const content = modal.querySelector('[id$="Content"]');
                if (content) {
                    content.style.transform = 'scale(1)';
                    content.style.opacity = '1';
                }
            }, 10);
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            const content = modal.querySelector('[id$="Content"]');
            
            if (content) {
                content.style.transform = 'scale(0.95)';
                content.style.opacity = '0';
            }
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        function saveSector() {
            const nome = document.getElementById('inpNomeSetor').value.trim();
            const descricao = document.getElementById('inpDescricaoSetor').value.trim();
            
            if (!nome) {
                showNotification('Digite o nome do setor.', 'error');
                return;
            }
            
            if (editingSectorId) {
                // Editar setor existente
                const sectorIndex = setores.findIndex(s => s.id === editingSectorId);
                if (sectorIndex !== -1) {
                    setores[sectorIndex].nome = nome;
                    setores[sectorIndex].descricao = descricao;
                    showNotification('Setor atualizado com sucesso!', 'success');
                }
            } else {
                // Cadastrar novo setor
                const newId = Math.max(...setores.map(s => s.id)) + 1;
                const newSector = {
                    id: newId,
                    nome: nome,
                    descricao: descricao,
                    usuarios: 0,
                    status: 'Ativo'
                };
                setores.push(newSector);
                showNotification('Setor cadastrado com sucesso!', 'success');
            }
            
            closeModal('modalSetor');
            renderSetores();
        }

        function openDeleteModal(sectorId) {
            const sector = setores.find(s => s.id === sectorId);
            if (sector) {
                if (sector.usuarios > 0) {
                    showNotification(`Não é possível excluir o setor "${sector.nome}" pois possui ${sector.usuarios} usuário(s) vinculado(s).`, 'error');
                    return;
                }
                
                deletingSectorId = sectorId;
                document.getElementById('deleteSectorName').textContent = sector.nome;
                openModal('modalDelete');
            }
        }

        function confirmDelete() {
            if (deletingSectorId) {
                const sectorIndex = setores.findIndex(s => s.id === deletingSectorId);
                if (sectorIndex !== -1) {
                    setores.splice(sectorIndex, 1);
                    renderSetores();
                    closeModal('modalDelete');
                    deletingSectorId = null;
                    
                    // Mostrar notificação de sucesso
                    showNotification('Setor excluído com sucesso!', 'success');
                }
            }
        }

        function showNotification(message, type = 'info') {
            // Criar notificação
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-3 sm:p-4 rounded-lg sm:rounded-xl shadow-lg transform transition-all duration-300 translate-x-full max-w-sm ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center gap-2 sm:gap-3">
                    <i class="fa-solid ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                    <span class="font-medium text-sm sm:text-base">${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animar entrada
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 10);
            
            // Remover após 3 segundos
            setTimeout(() => {
                notification.style.transform = 'translateX(full)';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        function renderSetores() {
            const grid = document.getElementById('setoresGrid');
            grid.innerHTML = '';
            
            setores.forEach((sector, index) => {
                const div = document.createElement('div');
                div.className = `card-enhanced p-4 sm:p-5 lg:p-6 rounded-xl sm:rounded-2xl animate-fade-in hover:scale-105 transition-all duration-300`;
                div.style.animationDelay = `${index * 0.1}s`;
                div.innerHTML = `
                    <div class="flex flex-col gap-3 sm:gap-4 mb-4 sm:mb-5">
                        <div class="flex items-start gap-3 sm:gap-4">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 lg:w-14 lg:h-14 rounded-xl sm:rounded-2xl bg-gradient-to-br from-secondary to-orange-500 text-white flex items-center justify-center relative overflow-hidden flex-shrink-0">
                                <i class="fa-solid fa-building text-sm sm:text-lg lg:text-xl relative z-10"></i>
                                <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"></div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-bold text-dark text-base sm:text-lg lg:text-xl mb-1 truncate">${sector.nome}</h3>
                                <p class="text-gray-600 text-xs sm:text-sm lg:text-base leading-relaxed">${sector.descricao}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 sm:gap-3 text-xs sm:text-sm lg:text-base text-gray-600 bg-gray-50 px-2 sm:px-3 py-1 sm:py-2 rounded-lg">
                                <i class="fa-solid fa-users text-secondary"></i>
                                <span class="font-medium">${sector.usuarios} usuário(s)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="status-badge px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm font-semibold rounded-full bg-green-100 text-green-700 border border-green-200 flex items-center gap-1 sm:gap-2">
                                    <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-green-500 rounded-full"></div>
                                    ${sector.status}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                        <button onclick="openEditSector(${sector.id})" class="p-1.5 sm:p-2 rounded-lg border border-gray-200 hover:bg-primary hover:text-white hover:border-primary text-gray-600 transition-all duration-300" title="Editar setor">
                            <i class="fa-solid fa-pen text-xs sm:text-sm"></i>
                        </button>
                        <button onclick="openDeleteModal(${sector.id})" class="p-1.5 sm:p-2 rounded-lg border border-red-200 hover:bg-red-500 hover:text-white text-red-600 transition-all duration-300" title="Excluir setor">
                            <i class="fa-solid fa-trash text-xs sm:text-sm"></i>
                        </button>
                    </div>
                `;
                grid.appendChild(div);
            });
        }

        // Initialize
        renderSetores();

        // Enhanced UX features
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth scroll behavior
            document.documentElement.style.scrollBehavior = 'smooth';
            
            // Add intersection observer for animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);
            
            // Observe all animated elements
            document.querySelectorAll('.animate-fade-in, .animate-slide-up').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                observer.observe(el);
            });

            // Add keyboard navigation and user menu toggle
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    // Close any open modals
                    const openModals = document.querySelectorAll('[id^="modal"]:not(.hidden)');
                    openModals.forEach(modal => {
                        if (!modal.classList.contains('hidden')) {
                            closeModal(modal.id);
                        }
                    });
                }
            });

            const userBtn = document.getElementById('userMenuButton');
            const userMenu = document.getElementById('userMenu');
            if (userBtn && userMenu) {
                userBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenu.classList.toggle('hidden');
                });
                document.addEventListener('click', function(e) {
                    if (!userMenu.contains(e.target)) {
                        userMenu.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</body>

</html>
