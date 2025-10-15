<?php
session_start();
$escola = $_GET['escola'];
//print_r($_SESSION);

require_once(__DIR__ . '/../models/model.select.php');
$select = new model_select();
$escola_banco = $select->select_escolas_nome($escola);
function tempo_session($tempo = 600)
{
    if (isset($_SESSION['ultimo_acesso'])) {

        if (time() - $_SESSION['ultimo_acesso'] > $tempo) {

            session_unset();
            session_destroy();
            header('location:login.php');
            exit();
        }
    }
    $_SESSION['ultimo_acesso'] = time();
}
tempo_session();

if (isset($_GET['outro_email'])) {
    session_unset();
    session_destroy();
}

// Verifica se há mensagens de erro ou sucesso
$showModal = false;
$modalTitle = 'Aviso';
$modalMessage = '';

if (isset($_GET['erro'])) {
    $showModal = true;
    $modalTitle = 'Erro';
    $modalMessage = 'Ocorreu um erro ao processar sua solicitação. Tente novamente.';
}
if (isset($_GET['codigo_invalido'])) {
    $showModal = true;
    $modalTitle = 'Código inválido';
    $modalMessage = 'O código informado está incorreto ou expirou. Tente novamente.';
}
if (isset($_GET['senha_alterada'])) {
    $showModal = true;
    $modalTitle = 'Senha alterada';
    $modalMessage = 'Sua senha foi alterada com sucesso! Você pode fazer login agora.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta http-equiv="content-language" content="pt-BR">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="theme-color" content="#005A24">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Recuperar Senha - <?= $escola ?></title>
    <meta name="description" content="Recupere sua senha para acessar o Sistema CREDE1 - Coordenadoria Regional de Desenvolvimento da Educação">
    <meta name="author" content="CREDE 1">
    <meta name="keywords" content="recuperar senha, CREDE 1, sistema, educação">
    <link rel="icon" type="image/png" href="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
                    backgroundImage: {
                        'gradient-primary': 'linear-gradient(135deg, #005A24 0%, #7FB069 50%, #1A3C34 100%)',
                        'gradient-secondary': 'linear-gradient(135deg, #F4A261 0%, #E76F51 100%)',
                        'gradient-light': 'linear-gradient(135deg, #E8F4F8 0%, #F7F3E9 100%)',
                        'gradient-dark': 'linear-gradient(135deg, #2D5016 0%, #005A24 100%)'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Poppins', 'sans-serif']
                    },
                    boxShadow: {
                        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
                        'medium': '0 4px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
                        'strong': '0 10px 40px -10px rgba(0, 0, 0, 0.15), 0 2px 10px -2px rgba(0, 0, 0, 0.05)',
                        'primary': '0 10px 25px -5px rgba(0, 90, 36, 0.3)',
                        'secondary': '0 10px 25px -5px rgba(255, 165, 0, 0.3)'
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'sway': 'sway 4s ease-in-out infinite',
                        'drift': 'drift 8s linear infinite',
                        'sparkle': 'sparkle 2s ease-in-out infinite',
                        'bounce-gentle': 'bounce-gentle 3s ease-in-out infinite'
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        @keyframes sway {

            0%,
            100% {
                transform: translateX(0px) rotate(0deg);
            }

            50% {
                transform: translateX(10px) rotate(2deg);
            }
        }

        @keyframes drift {
            0% {
                transform: translateY(-100px) translateX(0px) rotate(0deg);
            }

            100% {
                transform: translateY(calc(100vh + 100px)) translateX(50px) rotate(360deg);
            }
        }

        @keyframes sparkle {

            0%,
            100% {
                opacity: 0.3;
                transform: scale(1);
            }

            50% {
                opacity: 1;
                transform: scale(1.2);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(0, 90, 36, 0.4);
            }

            50% {
                box-shadow: 0 0 30px rgba(0, 90, 36, 0.6);
            }
        }

        .floating-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
            overflow: hidden;
        }

        .leaf {
            position: absolute;
            width: 20px;
            height: 20px;
            background: #005A24;
            border-radius: 0 100% 0 100%;
            animation: drift 15s linear infinite;
            opacity: 0.6;
        }

        .leaf:nth-child(2n) {
            background: #FFA500;
            animation-duration: 20s;
            animation-delay: -5s;
        }

        .leaf:nth-child(3n) {
            background: #7FB069;
            animation-duration: 18s;
            animation-delay: -10s;
        }

        .cloud {
            position: absolute;
            background: rgba(248, 250, 249, 0.9);
            border-radius: 50px;
            animation: cloud-drift 30s linear infinite;
            opacity: 0.8;
            box-shadow: 0 4px 20px rgba(248, 250, 249, 0.3);
        }

        @keyframes cloud-drift {
            0% {
                transform: translateX(-200px);
            }

            100% {
                transform: translateX(calc(100vw + 200px));
            }
        }

        .cloud::before,
        .cloud::after {
            content: '';
            position: absolute;
            background: rgba(248, 250, 249, 0.9);
            border-radius: 50px;
        }

        .cloud.small {
            width: 60px;
            height: 30px;
        }

        .cloud.small::before {
            width: 40px;
            height: 40px;
            top: -15px;
            left: 10px;
        }

        .cloud.small::after {
            width: 30px;
            height: 30px;
            top: -10px;
            right: 10px;
        }

        .input-group {
            position: relative;
        }

        .input-group input {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(135deg, rgba(248, 250, 249, 0.8) 0%, rgba(255, 255, 255, 0.9) 100%);
            border: 2px solid rgba(0, 90, 36, 0.3);
            backdrop-filter: blur(5px);
        }

        .input-group input:focus {
            transform: translateY(-2px);
            outline: none;
            border-color: #FFA500;
            box-shadow: 0 8px 25px rgba(255, 165, 0, 0.3);
            background: rgba(255, 255, 255, 0.95);
        }

        .input-group label {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(26, 60, 52, 0.6);
            font-size: 1rem;
            transition: all 0.3s ease;
            pointer-events: none;
            padding: 0 5px;
            background-color: transparent;
            z-index: 1;
            font-family: 'Inter', sans-serif;
        }

        .input-group input:focus+label,
        .input-group input:not(:placeholder-shown)+label {
            top: -12px;
            left: 10px;
            font-size: 0.85rem;
            color: #FFA500;
            transform: translateY(0);
            background: linear-gradient(135deg, #F8FAF9 0%, #E8F4F8 100%);
            padding: 0 8px;
            font-weight: 600;
            border-radius: 4px;
        }

        .btn-enhanced {
            background: linear-gradient(135deg, #005A24 0%, #1A3C34 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
            box-shadow: 0 8px 20px rgba(0, 90, 36, 0.3);
        }

        .btn-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-enhanced:hover::before {
            left: 100%;
        }

        .btn-enhanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(0, 90, 36, 0.4);
        }

        .error-message {
            background: linear-gradient(135deg, #E76F51 0%, #F4A261 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            margin: 16px 0;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(231, 111, 81, 0.3);
            animation: fadeInUp 0.5s ease-out;
            font-family: 'Inter', sans-serif;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid transparent;
            border-top: 2px solid #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .sparkle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: #FFA500;
            border-radius: 50%;
            animation: sparkle 2s ease-in-out infinite;
        }

        .magical-glow {
            box-shadow: 0 0 20px rgba(0, 90, 36, 0.4), 0 0 40px rgba(255, 165, 0, 0.2);
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .animate-slide-in-left {
            animation: slideInLeft 0.8s ease-out forwards;
        }

        .animate-slide-in-right {
            animation: slideInRight 0.8s ease-out forwards;
        }

        .pulse-glow {
            animation: pulse-glow 2s infinite;
        }

        *:focus {
            outline: 2px solid #FFA500;
            outline-offset: 2px;
            border-radius: 4px;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #005A24 0%, #FFA500 100%);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #1A3C34 0%, #E76F51 100%);
        }

        @media (max-width: 640px) {
            .main-container {
                width: 95%;
                margin: 1rem auto;
            }

            .input-group label {
                font-size: 0.9rem;
            }

            .btn-enhanced {
                padding: 0.75rem 1rem;
            }
        }

        @media (max-width: 475px) {
            .main-container {
                width: 100%;
                margin: 0;
                border-radius: 0;
                min-height: 100vh;
            }

            .form-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body class="font-sans bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200 min-h-screen flex items-center justify-center p-0 sm:p-4">
    <!-- Modal de feedback -->
    <div id="feedbackModal" class="fixed inset-0 z-50 flex items-start justify-center pt-6 sm:pt-10 <?= $showModal ? '' : 'hidden' ?>">
        <div class="absolute inset-0 bg-black/50" aria-hidden="true"></div>
        <div role="dialog" aria-modal="true" aria-labelledby="modalTitle" aria-describedby="modalDescription"
            class="relative z-10 w-11/12 max-w-lg bg-white rounded-2xl shadow-strong overflow-hidden animate-fade-in-up">
            <div class="px-6 py-4 bg-gradient-to-r from-primary to-dark text-white flex items-center justify-between">
                <h3 id="modalTitle" class="text-lg font-semibold">
                    <?= htmlspecialchars($modalTitle, ENT_QUOTES, 'UTF-8'); ?>
                </h3>
                <button type="button" class="modal-close text-white/90 hover:text-secondary transition-colors" aria-label="Fechar">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <p id="modalDescription" class="text-gray-700 leading-relaxed">
                    <?= htmlspecialchars($modalMessage, ENT_QUOTES, 'UTF-8'); ?>
                </p>
            </div>
            <div class="px-6 pb-6">
                <button type="button" class="modal-close btn-enhanced w-full px-5 py-3 text-white rounded-xl">
                    Entendi
                </button>
            </div>
        </div>
    </div>

    <!-- Elementos flutuantes -->
    <div class="floating-elements">
        <div class="cloud small" style="top: 20%; animation-delay: 0s;"></div>
        <div class="cloud small" style="top: 60%; animation-delay: -10s;"></div>
        <div class="cloud small" style="top: 40%; animation-delay: -20s;"></div>
    </div>

    <div class="main-container w-full max-w-6xl bg-white rounded-none sm:rounded-3xl shadow-strong overflow-hidden animate-fade-in-up relative z-10">
        <div class="flex flex-col lg:flex-row min-h-[100vh] sm:min-h-[600px]">
            <!-- Image Container -->
            <div class="hidden md:block lg:flex-1 bg-gradient-primary relative overflow-hidden animate-slide-in-left">
                <div class="absolute inset-0 bg-black/40"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-primary/80 via-transparent to-secondary/80"></div>
                <div class="absolute top-10 left-10 w-20 h-20 border-2 border-white/30 rounded-full animate-float"></div>
                <div class="absolute bottom-20 right-10 w-16 h-16 border-2 border-white/30 rounded-full animate-sway"></div>
                <div class="absolute top-1/3 right-20 w-12 h-12 bg-white/20 rounded-full animate-bounce-gentle"></div>
                <div class="relative z-10 h-full flex flex-col justify-center items-center p-8 lg:p-12 text-center text-white">
                    <div class="mb-8">
                        <i class="fas fa-building text-6xl lg:text-8xl mb-6 text-secondary"></i>
                    </div>
                    <h1 class="text-3xl lg:text-5xl font-bold mb-6 leading-tight font-heading">
                        <span class="text-secondary"><?= $escola ?></span>
                    </h1>
                    <p class="text-lg lg:text-xl mb-8 max-w-md leading-relaxed opacity-90 font-sans">
                        Coordenadoria Regional de Desenvolvimento da Educação
                    </p>
                    <div class="flex space-x-4 text-sm opacity-80">
                        <div class="flex items-center animate-bounce-gentle" style="animation-delay: 0.5s;">
                            <i class="fas fa-graduation-cap mr-2 text-yellow-300"></i>
                            <span>Educação</span>
                        </div>
                        <div class="flex items-center animate-bounce-gentle" style="animation-delay: 1s;">
                            <i class="fas fa-users mr-2 text-yellow-300"></i>
                            <span>Desenvolvimento</span>
                        </div>
                        <div class="flex items-center animate-bounce-gentle" style="animation-delay: 1.5s;">
                            <i class="fas fa-star mr-2 text-yellow-300"></i>
                            <span>Qualidade</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Container -->
            <div class="w-full lg:flex-1 p-4 xs:p-6 sm:p-8 lg:p-12 flex flex-col justify-center animate-slide-in-right relative">
                <!-- Logo Container -->
                <div class="text-center mb-6 sm:mb-8">
                    <div class="inline-block p-3 sm:p-4 rounded-2xl">
                        <img src="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png"
                            alt="Logo Sistema"
                            class="w-16 h-16 sm:w-20 sm:h-20 object-contain">
                    </div>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800 mb-2 font-heading">
                        Recuperar Senha
                    </h2>
                    <p class="text-gray-600 text-base sm:text-lg font-sans">
                        Recupere o acesso à sua conta
                    </p>
                </div>

                <?php if (isset($_SESSION['email_recuperar_senha']) && isset($_SESSION['codigo']) && !isset($_SESSION['codigo_verificado'])) { ?>
                    <!-- Formulário de Verificação de Código -->
                    <form id="verifyCodeForm" action="../controllers/controller_auth.php" method="POST" class="space-y-4 sm:space-y-6">
                        <input type="hidden" name="escola" value="<?= $escola ?>">
                        <input type="hidden" name="recuperar_senha" value="true">
                        <input type="hidden" name="escola_banco" value="<?= $escola_banco ?>">
                        <input type="hidden" name="email" value="<?= $_SESSION['email_recuperar_senha'] ?>">
                        <!-- Código Input -->
                        <div class="input-group">
                            <div class="relative">
                                <input type="text"
                                    name="codigo"
                                    id="verifyCode"
                                    placeholder=" "
                                    required
                                    maxlength="6"
                                    class="w-full px-4 py-3 sm:py-4 pl-10 sm:pl-12 rounded-xl text-gray-800 focus:shadow-primary transition-all duration-300 peer text-sm sm:text-base">
                                <label for="verifyCode"
                                    class="absolute left-10 mx-10 sm:left-12 top-3 sm:top-4 transition-all duration-300 peer-focus:text-secondary peer-focus:text-xs sm:peer-focus:text-sm peer-focus:-translate-y--1 peer-focus:font-semibold peer-[:not(:placeholder-shown)]:text-xs sm:peer-[:not(:placeholder-shown)]:text-sm peer-[:not(:placeholder-shown)]:text-secondary peer-[:not(:placeholder-shown)]:font-semibold text-sm sm:text-base">
                                    Código de Verificação
                                </label>
                                <i class="fas fa-key absolute left-3 sm:left-4 top-1/2 transform -translate-y-1/2 text-primary text-base sm:text-lg"></i>
                            </div>
                        </div>
                        <!-- Submit Button -->
                        <div class="mt-4 sm:mt-6">
                            <button type="submit"
                                class="btn-enhanced w-full px-6 py-3 sm:py-4 text-white rounded-xl transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-accent focus:ring-opacity-50 text-sm sm:text-base font-semibold bg-gradient-to-r from-primary to-dark hover:from-dark hover:to-primary">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span>Verificar Código</span>
                            </button>
                        </div>
                    </form>
                    <div class="mt-4 text-center">
                        <p class="text-gray-600 text-sm">Código enviado para: <strong><?= htmlspecialchars($_SESSION['email_recuperar_senha'], ENT_QUOTES, 'UTF-8') ?></strong></p>
                        <a href="recuperar_senha.php?escola=<?= $escola ?>&outro_email" class="text-secondary hover:text-primary transition-colors duration-300 text-sm">
                            <i class="fas fa-arrow-left mr-1"></i>Usar outro e-mail
                        </a>
                    </div>
                <?php } else if (isset($_SESSION['email_recuperar_senha']) && isset($_SESSION['codigo_verificado'])) { ?>
                    <!-- Formulário de Nova Senha -->
                    <form id="newPasswordForm" action="../controllers/controller_auth.php" method="POST" class="space-y-4 sm:space-y-6">
                        <input type="hidden" name="escola" value="<?= $escola ?>">
                        <input type="hidden" name="recuperar_senha" value="true">
                        <input type="hidden" name="escola_banco" value="<?= $escola_banco ?>">
                        <input type="hidden" name="email" value="<?= $_SESSION['email_recuperar_senha'] ?>">
                        <input type="hidden" name="nova_senha" value="true">
                        <!-- Nova Senha Input -->
                        <div class="input-group">
                            <div class="relative">
                                <input type="password"
                                    name="nova_senha"
                                    id="newPassword"
                                    placeholder=" "
                                    required
                                    class="w-full px-4 py-3 sm:py-4 pl-10 sm:pl-12 pr-10 sm:pr-12 rounded-xl text-gray-800 focus:shadow-primary transition-all duration-300 peer text-sm sm:text-base">
                                <label for="newPassword"
                                    class="absolute left-10 mx-10 sm:left-12 top-3 sm:top-4 transition-all duration-300 peer-focus:text-secondary peer-focus:text-xs sm:peer-focus:text-sm peer-focus:-translate-y--1 peer-focus:font-semibold peer-[:not(:placeholder-shown)]:text-xs sm:peer-[:not(:placeholder-shown)]:text-sm peer-[:not(:placeholder-shown)]:text-secondary peer-[:not(:placeholder-shown)]:font-semibold text-sm sm:text-base">
                                    Nova Senha
                                </label>
                                <i class="fas fa-lock absolute left-3 sm:left-4 top-1/2 transform -translate-y-1/2 text-primary text-base sm:text-lg"></i>
                                <i class="fas fa-eye toggle-password absolute right-3 sm:right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-secondary cursor-pointer transition-colors duration-300 text-base sm:text-lg"></i>
                            </div>
                        </div>
                        <!-- Confirmar Nova Senha Input -->
                        <div class="input-group">
                            <div class="relative">
                                <input type="password"
                                    name="confirmar_senha"
                                    id="confirmPassword"
                                    placeholder=" "
                                    required
                                    class="w-full px-4 py-3 sm:py-4 pl-10 sm:pl-12 pr-10 sm:pr-12 rounded-xl text-gray-800 focus:shadow-primary transition-all duration-300 peer text-sm sm:text-base">
                                <label for="confirmPassword"
                                    class="absolute left-10 mx-10 sm:left-12 top-3 sm:top-4 transition-all duration-300 peer-focus:text-secondary peer-focus:text-xs sm:peer-focus:text-sm peer-focus:-translate-y--1 peer-focus:font-semibold peer-[:not(:placeholder-shown)]:text-xs sm:peer-[:not(:placeholder-shown)]:text-sm peer-[:not(:placeholder-shown)]:text-secondary peer-[:not(:placeholder-shown)]:font-semibold text-sm sm:text-base">
                                    Confirmar Nova Senha
                                </label>
                                <i class="fas fa-lock absolute left-3 sm:left-4 top-1/2 transform -translate-y-1/2 text-primary text-base sm:text-lg"></i>
                                <i class="fas fa-eye toggle-password absolute right-3 sm:right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-secondary cursor-pointer transition-colors duration-300 text-base sm:text-lg"></i>
                            </div>
                        </div>
                        <!-- Submit Button -->
                        <div class="mt-4 sm:mt-6">
                            <button type="submit"
                                class="btn-enhanced w-full px-6 py-3 sm:py-4 text-white rounded-xl transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-accent focus:ring-opacity-50 text-sm sm:text-base font-semibold bg-gradient-to-r from-primary to-dark hover:from-dark hover:to-primary">
                                <i class="fas fa-save mr-2"></i>
                                <span>Alterar Senha</span>
                            </button>
                        </div>
                    </form>
                <?php } else { ?>
                    <!-- Formulário de Recuperação de Senha -->
                    <form id="recoverPasswordForm" action="../controllers/controller_auth.php" method="POST" class="space-y-4 sm:space-y-6">
                        <input type="hidden" name="escola" value="<?= $escola ?>">
                        <input type="hidden" name="recuperar_senha" value="true">
                        <input type="hidden" name="escola_banco" value="<?= $escola_banco ?>">
                        <!-- Email Input -->
                        <div class="input-group">
                            <div class="relative">
                                <input type="email"
                                    name="email"
                                    id="recoverEmail"
                                    placeholder=" "
                                    required
                                    class="w-full px-4 py-3 sm:py-4 pl-10 sm:pl-12 rounded-xl text-gray-800 focus:shadow-primary transition-all duration-300 peer text-sm sm:text-base">
                                <label for="recoverEmail"
                                    class="absolute left-10 mx-10 sm:left-12 top-3 sm:top-4 transition-all duration-300 peer-focus:text-secondary peer-focus:text-xs sm:peer-focus:text-sm peer-focus:-translate-y--1 peer-focus:font-semibold peer-[:not(:placeholder-shown)]:text-xs sm:peer-[:not(:placeholder-shown)]:text-sm peer-[:not(:placeholder-shown)]:text-secondary peer-[:not(:placeholder-shown)]:font-semibold text-sm sm:text-base">
                                    E-mail
                                </label>
                                <i class="fas fa-envelope absolute left-3 sm:left-4 top-1/2 transform -translate-y-1/2 text-primary text-base sm:text-lg"></i>
                            </div>
                        </div>
                        <!-- Submit Button -->
                        <div class="mt-4 sm:mt-6">
                            <button type="submit"
                                class="btn-enhanced w-full px-6 py-3 sm:py-4 text-white rounded-xl transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-accent focus:ring-opacity-50 text-sm sm:text-base font-semibold bg-gradient-to-r from-primary to-dark hover:from-dark hover:to-primary">
                                <i class="fas fa-envelope mr-2"></i>
                                <span>Enviar Código</span>
                            </button>
                        </div>
                    </form>
                <?php } ?>
                <!-- Voltar para Login -->
                <div class="mt-6 sm:mt-8 text-center">
                    <a href="login.php?escola=<?= $escola ?>&outro_email" class="text-secondary hover:text-primary transition-colors duration-300 text-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Voltar para o login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal controls
            const modal = document.getElementById('feedbackModal');
            const modalCloses = document.querySelectorAll('.modal-close');
            const modalBackdrop = modal ? modal.querySelector('.absolute.inset-0') : null;

            function closeModal() {
                if (modal) modal.classList.add('hidden');
            }
            modalCloses.forEach(btn => btn.addEventListener('click', closeModal));
            if (modalBackdrop) modalBackdrop.addEventListener('click', closeModal);
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeModal();
            });

            // Password visibility toggle
            const togglePassword = document.querySelectorAll('.toggle-password');
            togglePassword.forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const input = this.previousElementSibling.previousElementSibling;
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                    this.style.transform = 'scale(1.2) rotate(10deg)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1) rotate(0deg)';
                    }, 200);
                });
            });

            // Form submission handling
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitButton = this.querySelector('button[type="submit"]');
                    submitButton.classList.add('loading');
                    submitButton.disabled = true;
                    const originalText = submitButton.innerHTML;
                    submitButton.innerHTML = '<span><i class="fas fa-spinner fa-spin mr-2"></i>Processando...</span>';
                    setTimeout(() => {
                        submitButton.classList.remove('loading');
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    }, 2000);
                });
            });

            // Input animations
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 0 20px rgba(0, 90, 36, 0.3)';
                });
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                    this.style.boxShadow = '';
                });
            });

            // Ripple effect for buttons
            const buttons = document.querySelectorAll('.btn-enhanced');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        background: rgba(255, 165, 0, 0.4);
                        border-radius: 50%;
                        transform: scale(0);
                        animation: ripple 0.6s ease-out;
                        pointer-events: none;
                    `;
                    this.appendChild(ripple);
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });

            // Add CSS for ripple animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(2);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);

            // Handle orientation changes
            window.addEventListener('orientationchange', function() {
                setTimeout(() => {
                    const vh = window.innerHeight * 0.01;
                    document.documentElement.style.setProperty('--vh', `${vh}px`);
                }, 100);
            });

            // Set initial viewport height
            const vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        });
    </script>
</body>

</html>