<?php 
require_once('../models/sessions.php');
require_once('../models/model.usuario.php');

$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

// Buscar dados do usuário
$model_usuario = new model_usuario();
$dados_usuario = $model_usuario->getDadosUsuario($_SESSION['id']);

// Processar atualização do telefone e upload de foto
$mensagem = '';
$tipo_mensagem = '';

// Verificar se há mensagem na sessão (após redirect)
if (isset($_SESSION['mensagem_perfil'])) {
    $mensagem = $_SESSION['mensagem_perfil'];
    $tipo_mensagem = $_SESSION['tipo_mensagem_perfil'];
    unset($_SESSION['mensagem_perfil']);
    unset($_SESSION['tipo_mensagem_perfil']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['atualizar_telefone'])) {
        $telefone = trim($_POST['telefone']);
        
        if (!empty($telefone)) {
            $resultado = $model_usuario->atualizarTelefone($_SESSION['id'], $telefone);
            
            if ($resultado === 1) {
                $_SESSION['mensagem_perfil'] = 'Telefone atualizado com sucesso!';
                $_SESSION['tipo_mensagem_perfil'] = 'success';
            } else {
                $_SESSION['mensagem_perfil'] = 'Erro ao atualizar telefone. Tente novamente.';
                $_SESSION['tipo_mensagem_perfil'] = 'error';
            }
        } else {
            $_SESSION['mensagem_perfil'] = 'Por favor, insira um número de telefone válido.';
            $_SESSION['tipo_mensagem_perfil'] = 'error';
        }
        
        // Redirect para evitar reenvio de dados
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
        
    } elseif (isset($_POST['upload_foto'])) {
        // Verificar se é uma imagem recortada ou upload normal
        if (isset($_POST['cropped_image_data']) && !empty($_POST['cropped_image_data'])) {
            // Processar imagem recortada
            $resultado = $model_usuario->uploadFotoPerfilRecortada($_SESSION['id'], $_POST['cropped_image_data']);
            
            if ($resultado['success']) {
                $_SESSION['mensagem_perfil'] = $resultado['message'];
                $_SESSION['tipo_mensagem_perfil'] = 'success';
            } else {
                $_SESSION['mensagem_perfil'] = $resultado['message'];
                $_SESSION['tipo_mensagem_perfil'] = 'error';
            }
        } elseif (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            // Processar upload normal
            $resultado = $model_usuario->uploadFotoPerfil($_SESSION['id'], $_FILES['foto_perfil']);
            
            if ($resultado['success']) {
                $_SESSION['mensagem_perfil'] = $resultado['message'];
                $_SESSION['tipo_mensagem_perfil'] = 'success';
            } else {
                $_SESSION['mensagem_perfil'] = $resultado['message'];
                $_SESSION['tipo_mensagem_perfil'] = 'error';
            }
        } else {
            $_SESSION['mensagem_perfil'] = 'Por favor, selecione uma imagem válida.';
            $_SESSION['tipo_mensagem_perfil'] = 'error';
        }
        
        // Redirect para evitar reenvio de dados
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
        
    } elseif (isset($_POST['remover_foto'])) {
        $resultado = $model_usuario->removerFotoPerfil($_SESSION['id']);
        
        if ($resultado['success']) {
            $_SESSION['mensagem_perfil'] = $resultado['message'];
            $_SESSION['tipo_mensagem_perfil'] = 'success';
        } else {
            $_SESSION['mensagem_perfil'] = $resultado['message'];
            $_SESSION['tipo_mensagem_perfil'] = 'error';
        }
        
        // Redirect para evitar reenvio de dados
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="content-language" content="pt-BR">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="theme-color" content="#005A24">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <title>Perfil do Usuário - CREDE</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="Perfil do usuário - Sistema CREDE - Coordenadoria Regional de Desenvolvimento da Educação">
    <meta name="author" content="CREDE">
    <meta name="keywords" content="perfil, usuário, CREDE, sistema, educação">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png">

    <!-- Fontes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

    <style>
        /* Reset e base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #005A24;
            --secondary: #FFA500;
            --accent: #E6F4EA;
            --dark: #1A3C34;
            --light: #F8FAF9;
            --success: #10B981;
            --warning: #F59E0B;
            --error: #EF4444;
            --info: #3B82F6;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--light) 0%, var(--accent) 50%, #F0F9FF 100%);
            min-height: 100vh;
            line-height: 1.6;
            font-size: clamp(14px, 2.5vw, 16px);
        }

        /* Utility Classes */
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 clamp(1rem, 4vw, 2rem);
        }

        .flex {
            display: flex;
        }

        .items-center {
            align-items: center;
        }

        .justify-between {
            justify-content: space-between;
        }

        .justify-center {
            justify-content: center;
        }

        .space-x-4 > * + * {
            margin-left: 1rem;
        }

        .text-center {
            text-align: center;
        }

        .hidden {
            display: none !important;
        }

        .w-full {
            width: 100%;
        }

        .h-full {
            height: 100%;
        }

        .relative {
            position: relative;
        }

        .absolute {
            position: absolute;
        }

        .fixed {
            position: fixed;
        }

        .top-0 {
            top: 0;
        }

        .left-0 {
            left: 0;
        }

        .right-0 {
            right: 0;
        }

        .bottom-0 {
            bottom: 0;
        }

        .z-50 {
            z-index: 50;
        }

        .z-100 {
            z-index: 100;
        }

        .z-2000 {
            z-index: 2000;
        }

        .rounded-full {
            border-radius: 50%;
        }

        .object-cover {
            object-fit: cover;
        }

        .object-contain {
            object-fit: contain;
        }

        .overflow-hidden {
            overflow: hidden;
        }

        .overflow-y-auto {
            overflow-y: auto;
        }

        .pointer-events-none {
            pointer-events: none;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .select-none {
            user-select: none;
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        /* Typography */
        .text-xs {
            font-size: clamp(0.6rem, 2vw, 0.75rem);
        }

        .text-sm {
            font-size: clamp(0.7rem, 2.2vw, 0.875rem);
        }

        .text-base {
            font-size: clamp(0.8rem, 2.5vw, 1rem);
        }

        .text-lg {
            font-size: clamp(0.9rem, 3vw, 1.125rem);
        }

        .text-xl {
            font-size: clamp(1rem, 3.5vw, 1.25rem);
        }

        .text-2xl {
            font-size: clamp(1.2rem, 4vw, 1.5rem);
        }

        .text-3xl {
            font-size: clamp(1.5rem, 5vw, 1.875rem);
        }

        .text-4xl {
            font-size: clamp(2rem, 6vw, 2.25rem);
        }

        .font-medium {
            font-weight: 500;
        }

        .font-semibold {
            font-weight: 600;
        }

        .font-bold {
            font-weight: 700;
        }

        .font-extrabold {
            font-weight: 800;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .italic {
            font-style: italic;
        }

        /* Colors */
        .text-primary {
            color: var(--primary);
        }

        .text-secondary {
            color: var(--secondary);
        }

        .text-white {
            color: var(--white);
        }

        .text-gray-400 {
            color: var(--gray-400);
        }

        .text-gray-500 {
            color: var(--gray-500);
        }

        .text-gray-600 {
            color: var(--gray-600);
        }

        .text-gray-700 {
            color: var(--gray-700);
        }

        .text-gray-800 {
            color: var(--gray-800);
        }

        .bg-white {
            background-color: var(--white);
        }

        .bg-gray-100 {
            background-color: var(--gray-100);
        }

        .bg-gray-200 {
            background-color: var(--gray-200);
        }

        .bg-primary {
            background-color: var(--primary);
        }

        .bg-secondary {
            background-color: var(--secondary);
        }

        /* Spacing */
        .p-1 {
            padding: 0.25rem;
        }

        .p-2 {
            padding: 0.5rem;
        }

        .p-4 {
            padding: 1rem;
        }

        .p-6 {
            padding: 1.5rem;
        }

        .p-8 {
            padding: 2rem;
        }

        .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .px-6 {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .py-2 {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .py-4 {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .py-6 {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }

        .py-8 {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }

        .py-12 {
            padding-top: 3rem;
            padding-bottom: 3rem;
        }

        .pt-4 {
            padding-top: 1rem;
        }

        .pb-4 {
            padding-bottom: 1rem;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .mb-6 {
            margin-bottom: 1.5rem;
        }

        .mb-8 {
            margin-bottom: 2rem;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .mt-4 {
            margin-top: 1rem;
        }

        .mt-6 {
            margin-top: 1.5rem;
        }

        .mx-auto {
            margin-left: auto;
            margin-right: auto;
        }

        /* Borders */
        .border {
            border-width: 1px;
        }

        .border-2 {
            border-width: 2px;
        }

        .border-4 {
            border-width: 4px;
        }

        .border-gray-200 {
            border-color: var(--gray-200);
        }

        .border-primary {
            border-color: var(--primary);
        }

        .rounded {
            border-radius: 0.25rem;
        }

        .rounded-lg {
            border-radius: 0.5rem;
        }

        .rounded-xl {
            border-radius: 0.75rem;
        }

        .rounded-2xl {
            border-radius: 1rem;
        }

        /* Shadows */
        .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .shadow {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        /* Animações */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @keyframes pulseSoft {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        @keyframes bounceIn {
            0% { opacity: 0; transform: translateY(50px); }
            50% { opacity: 1; transform: translateY(-10px); }
            70% { transform: translateY(5px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes modalSlideIn {
            from { 
                opacity: 0; 
                transform: translate(-50%, -40%);
            }
            to { 
                opacity: 1; 
                transform: translate(-50%, -50%);
            }
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        .animate-slide-up {
            animation: slideUp 0.8s ease-out;
        }

        .animate-bounce-in {
            animation: bounceIn 0.8s ease-out;
        }

        .animate-slide-in-right {
            animation: slideInRight 0.6s ease-out;
        }

        /* Header */
        .header-gradient {
            background: linear-gradient(135deg, var(--white) 0%, var(--light) 100%);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 90, 36, 0.08);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .header-gradient::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 50%, var(--primary) 100%);
            box-shadow: 0 2px 8px rgba(0, 90, 36, 0.3);
        }

        .header-logo {
            width: clamp(2.5rem, 8vw, 3.5rem);
            height: clamp(2.5rem, 8vw, 3.5rem);
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--white);
            border-radius: 1rem;
            flex-shrink: 0;
        }

        .header-logo img {
            width: clamp(1.5rem, 6vw, 2.5rem);
            height: clamp(1.5rem, 6vw, 2.5rem);
            object-fit: contain;
        }

        .header-title {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(1rem, 4vw, 1.5rem);
            font-weight: 600;
            line-height: 1.2;
        }

        /* Nova estrutura de layout mais moderna e flexível */
        .profile-container {
            display: flex;
            flex-direction: column;
            gap: clamp(1.5rem, 4vw, 2.5rem);
            max-width: 1400px;
            margin: 0 auto;
            padding: clamp(1rem, 4vw, 2rem);
        }

        /* Hero section com avatar e info principal */
        .profile-hero {
            background: linear-gradient(135deg, rgba(0, 90, 36, 0.05) 0%, rgba(255, 165, 0, 0.05) 100%);
            border-radius: clamp(1rem, 3vw, 2rem);
            padding: clamp(2rem, 6vw, 4rem);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .profile-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: rotate(45deg) translateX(-100%); }
            100% { transform: rotate(45deg) translateX(100%); }
        }

        /* Layout de cards em grid responsivo */
        .profile-content {
            display: grid;
            grid-template-columns: 1fr;
            gap: clamp(1.5rem, 4vw, 2rem);
        }

        @media (min-width: 768px) {
            .profile-content {
                grid-template-columns: 2fr 1fr;
            }
        }

        @media (min-width: 1200px) {
            .profile-content {
                grid-template-columns: 1fr 1fr 1fr;
            }
        }

        /* Card principal de informações */
        .profile-main-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 90, 36, 0.1);
            border-radius: clamp(1rem, 3vw, 1.5rem);
            padding: clamp(1.5rem, 5vw, 2.5rem);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            animation: bounceIn 0.8s ease-out;
        }

        .profile-main-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 48px rgba(0, 90, 36, 0.12);
        }

        /* Cards de ações rápidas */
        .quick-actions-card {
            background: linear-gradient(135deg, var(--white) 0%, rgba(0, 90, 36, 0.02) 100%);
            border: 1px solid rgba(0, 90, 36, 0.1);
            border-radius: clamp(1rem, 3vw, 1.5rem);
            padding: clamp(1.5rem, 4vw, 2rem);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .quick-actions-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 90, 36, 0.1);
        }

        /* Card de estatísticas */
        .stats-card {
            background: linear-gradient(135deg, rgba(255, 165, 0, 0.05) 0%, var(--white) 100%);
            border: 1px solid rgba(255, 165, 0, 0.1);
            border-radius: clamp(1rem, 3vw, 1.5rem);
            padding: clamp(1.5rem, 4vw, 2rem);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255, 165, 0, 0.1);
        }

        /* Avatar redimensionado para hero */
        .profile-avatar {
            width: clamp(8rem, 25vw, 12rem);
            height: clamp(8rem, 25vw, 12rem);
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: clamp(3rem, 10vw, 5rem);
            margin: 0 auto 2rem;
            box-shadow: 0 16px 48px rgba(0, 90, 36, 0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .profile-avatar::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            transition: all 0.6s ease;
        }

        .profile-avatar:hover::before {
            transform: rotate(45deg) translate(50%, 50%);
        }

        .profile-avatar:hover {
            box-shadow: 0 20px 60px rgba(0, 90, 36, 0.4);
            transform: scale(1.05);
        }

        .avatar-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: clamp(1.5rem, 5vw, 2rem);
            opacity: 0;
            transition: all 0.3s ease;
            border-radius: 50%;
        }

        .profile-avatar:hover .avatar-overlay {
            opacity: 1;
        }

        /* Grid de informações otimizado */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: clamp(1rem, 3vw, 1.5rem);
        }

        @media (min-width: 480px) {
            .info-grid {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            }
        }

        .info-card {
            background: linear-gradient(135deg, var(--white) 0%, var(--light) 100%);
            border: 1px solid rgba(0, 90, 36, 0.08);
            border-radius: clamp(0.75rem, 2vw, 1rem);
            padding: clamp(1rem, 4vw, 1.5rem);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .info-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 90, 36, 0.1);
            border-color: rgba(0, 90, 36, 0.2);
        }

        .info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .info-card:hover::before {
            transform: translateX(0);
        }

        .info-icon {
            width: clamp(2.5rem, 8vw, 3rem);
            height: clamp(2.5rem, 8vw, 3rem);
            border-radius: 0.75rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: clamp(1rem, 3vw, 1.25rem);
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .info-card:hover .info-icon {
            transform: rotate(5deg);
        }

        .info-label {
            font-size: clamp(0.6rem, 2vw, 0.75rem);
            font-weight: 600;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .info-value {
            font-size: clamp(0.8rem, 2.5vw, 1rem);
            font-weight: 600;
            color: var(--gray-800);
            line-height: 1.4;
            word-break: break-word;
        }

        .info-value.empty {
            color: var(--gray-400);
            font-style: italic;
        }

        /* Botões */
        .edit-button {
            background: linear-gradient(135deg, var(--primary) 0%, var(--dark) 100%);
            color: white;
            border: none;
            padding: clamp(0.75rem, 3vw, 0.875rem) clamp(1rem, 4vw, 1.75rem);
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 90, 36, 0.25);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            width: 100%;
            text-align: center;
        }

        .edit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 90, 36, 0.35);
        }

        .edit-button:active {
            transform: translateY(0);
        }

        .back-button {
            background: rgba(0, 90, 36, 0.1);
            color: var(--primary);
            border: 2px solid var(--primary);
            padding: clamp(0.5rem, 2vw, 0.75rem) clamp(0.75rem, 3vw, 1.5rem);
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            white-space: nowrap;
        }

        .back-button:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 90, 36, 0.25);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 2000;
            backdrop-filter: blur(8px);
            animation: fadeIn 0.3s ease-out;
            align-items: center;
            justify-content: center;
            padding: clamp(1rem, 3vw, 2rem);
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            position: relative;
            background: white;
            border-radius: clamp(1rem, 3vw, 1.5rem);
            padding: clamp(1.5rem, 4vw, 2.5rem);
            max-width: min(95vw, 32rem);
            width: 100%;
            max-height: min(90vh, 600px);
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.4);
            animation: modalSlideIn 0.3s ease-out;
            overflow-y: auto;
            margin: auto;
        }

        .modal-content.large {
            max-width: min(95vw, 48rem);
            max-height: min(90vh, 700px);
        }

        @keyframes modalSlideIn {
            from { 
                opacity: 0; 
                transform: scale(0.9) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (max-height: 600px) {
            .modal {
                align-items: flex-start;
                padding: 1rem;
            }
            
            .modal-content {
                margin-top: 1rem;
                max-height: calc(100vh - 2rem);
            }
        }

        @media (max-width: 640px) {
            .modal {
                padding: 0.5rem;
            }
            
            .modal-content {
                border-radius: 1rem;
                padding: 1.25rem;
                max-height: calc(100vh - 1rem);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1.25rem;
            border-bottom: 2px solid var(--gray-100);
            flex-wrap: wrap;
            gap: 1rem;
        }

        .modal-title {
            font-size: clamp(1.125rem, 4vw, 1.5rem);
            font-weight: 700;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex: 1;
            min-width: 0;
        }

        .close-modal {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: var(--gray-100);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-600);
            cursor: pointer;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .close-modal:hover {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: clamp(0.75rem, 3vw, 0.875rem) 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 0.75rem;
            font-size: clamp(0.875rem, 2.5vw, 1rem);
            transition: all 0.3s ease;
            background: var(--gray-50);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(0, 90, 36, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        @media (max-width: 480px) {
            .form-actions {
                flex-direction: column;
                gap: 0.75rem;
            }
        }

        .btn-cancel {
            background: var(--gray-100);
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
            padding: clamp(0.75rem, 3vw, 0.875rem) clamp(1rem, 4vw, 1.75rem);
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            flex: 1;
            min-width: 0;
        }

        .btn-cancel:hover {
            background: var(--gray-200);
            transform: translateY(-1px);
        }

        .btn-save {
            background: linear-gradient(135deg, var(--primary) 0%, var(--dark) 100%);
            color: white;
            border: none;
            padding: clamp(0.75rem, 3vw, 0.875rem) clamp(1rem, 4vw, 1.75rem);
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            flex: 1;
            min-width: 0;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 90, 36, 0.25);
        }

        .btn-remove {
            background: linear-gradient(135deg, var(--error) 0%, #dc2626 100%);
            color: white;
            border: none;
            padding: clamp(0.75rem, 3vw, 0.875rem) clamp(1rem, 4vw, 1.5rem);
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
        }

        .btn-remove:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.25);
        }

        /* Mensagens */
        .message {
            padding: clamp(0.75rem, 3vw, 1rem) clamp(1rem, 4vw, 1.5rem);
            border-radius: 1rem;
            margin-bottom: 2rem;
            font-weight: 500;
            animation: slideUp 0.5s ease-out;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-left: 4px solid;
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
        }

        .message.success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border-left-color: var(--success);
        }

        .message.error {
            background: linear-gradient(135deg, #fee2e2 0%, #fca5a5 100%);
            color: #991b1b;
            border-left-color: var(--error);
        }

        /* Estilos para o cropper */
        .cropper-container {
            max-width: 100%;
            max-height: min(60vh, 400px);
            margin: 0 auto;
            overflow: hidden;
            position: relative;
        }

        .cropper-view-box {
            border-radius: 50%;
        }

        .cropper-face {
            border-radius: 50%;
        }

        .cropper-line, .cropper-point {
            background-color: var(--primary);
        }

        .cropper-view-box {
            outline: 2px solid var(--primary);
        }

        .crop-preview {
            width: clamp(5rem, 15vw, 7.5rem);
            height: clamp(5rem, 15vw, 7.5rem);
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--primary);
            margin: 0 auto;
            flex-shrink: 0;
        }

        .crop-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cropper-controls {
            display: flex;
            gap: clamp(0.25rem, 1vw, 0.5rem);
            justify-content: center;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .cropper-btn {
            padding: clamp(0.5rem, 2vw, 0.75rem) clamp(0.75rem, 3vw, 1rem);
            border-radius: 0.5rem;
            font-size: clamp(0.6rem, 2vw, 0.8125rem);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            white-space: nowrap;
            background: var(--gray-100);
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
            flex: 1;
            min-width: 0;
            text-align: center;
        }

        .cropper-btn.primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--dark) 100%);
            color: white;
            border: none;
        }

        .cropper-btn.secondary {
            background: var(--gray-100);
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
        }

        .cropper-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .cropper-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        @media (max-width: 480px) {
            .cropper-controls {
                flex-direction: column;
                align-items: stretch;
            }

            .cropper-btn {
                flex: none;
                width: 100%;
                margin: 0.25rem 0;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, var(--dark) 0%, #E76F51 100%);
        }

        /* Animações de entrada */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Loading states */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            border: 2px solid var(--gray-100);
            border-top: 2px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            transform: translate(-50%, -50%);
        }

        /* Responsividade adicional */
        @media (max-width: 360px) {
            .container {
                padding: 0.75rem;
            }

            .profile-main-card {
                padding: 1rem;
                margin: 0.5rem 0;
            }

            .info-card {
                padding: 1rem;
            }

            .info-icon {
                width: 2.5rem;
                height: 2.5rem;
                font-size: 1rem;
            }

        @media (max-width: 640px) {
            .modal-content {
                padding: 1rem;
                margin: 0.5rem;
            }

            .cropper-container {
                max-height: 35vh;
            }
        }

        /* Melhorias para touch */
        @media (hover: none) and (pointer: coarse) {
            .info-card:hover,
            .profile-avatar:hover,
            .edit-button:hover,
            .back-button:hover {
                transform: none;
            }

            .info-card:active {
                transform: scale(0.98);
            }

            .edit-button:active,
            .back-button:active {
                transform: scale(0.95);
            }

            .profile-avatar:active {
                transform: scale(0.95);
            }
        }

        /* Acessibilidade */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Focus states */
        .edit-button:focus,
        .back-button:focus,
        .btn-save:focus,
        .btn-cancel:focus,
        .btn-remove:focus,
        .cropper-btn:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        .form-input:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        /* High contrast mode */
        @media (prefers-contrast: high) {
            .profile-main-card {
                border: 2px solid var(--gray-800);
            }

            .info-card {
                border: 2px solid var(--gray-600);
            }

            .modal-content {
                border: 2px solid var(--gray-800);
            }
        }
    }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header-gradient shadow-sm">
        <div class="container">
            <div class="flex items-center justify-between py-4">
                <div class="flex items-center space-x-4">
                    <div class="header-logo">
                        <img src="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png" 
                             alt="Logo Ceará">
                    </div>
                    <div>
                        <h1 class="header-title">
                            <span class="text-primary">Perfil do</span> <span class="text-secondary">Usuário</span>
                        </h1>
                    </div>
                </div>
                
                <a href="subsystems.php" class="back-button">
                    <i class="fas fa-arrow-left"></i>
                    <span class="hidden sm:inline">Voltar</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Nova estrutura de layout com hero section e grid moderno -->
    <main class="profile-container">
        
        <!-- Mensagem de feedback -->
        <div id="messageContainer" class="<?php echo !empty($mensagem) ? 'message ' . $tipo_mensagem : 'hidden message success'; ?>">
            <i class="fas fa-check-circle text-xl"></i>
            <span id="messageText"><?php echo $mensagem; ?></span>
        </div>

        <!-- Hero Section com Avatar e Nome -->
        <section class="profile-hero">
            <div class="profile-avatar" onclick="openPhotoModal()">
                <img id="profileImage" src="<?php echo !empty($dados_usuario['foto_perfil']) ? $dados_usuario['foto_perfil'] : '/placeholder.svg'; ?>" alt="Foto de Perfil" class="<?php echo !empty($dados_usuario['foto_perfil']) ? 'w-full h-full object-cover rounded-full' : 'w-full h-full object-cover rounded-full hidden'; ?>">
                <i id="profileIcon" class="<?php echo !empty($dados_usuario['foto_perfil']) ? 'fas fa-user hidden' : 'fas fa-user'; ?>"></i>
                <div class="avatar-overlay">
                    <i class="fas fa-camera"></i>
                </div>
            </div>
            
            <h1 class="text-4xl font-bold text-gray-800 mb-2">
                <?php echo $dados_usuario['nome']; ?>
            </h1>
            <p class="text-xl text-gray-600 mb-4"><?php echo $dados_usuario['nome_funcao']; ?></p>
            <div class="flex justify-center gap-4 flex-wrap">
                <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                    <i class="fas fa-check-circle mr-2"></i>Perfil Ativo
                </span>
                <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                    <i class="fas fa-shield-alt mr-2"></i>Verificado
                </span>
            </div>
        </section>

        <!-- Grid de Conteúdo -->
        <div class="profile-content">
            
            <!-- Card Principal de Informações -->
            <div class="profile-main-card">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-user-circle mr-3 text-green-600"></i>
                    Informações Pessoais
                </h3>
                
                <div class="info-grid">
                    <!-- Email -->
                    <div class="info-card animate-on-scroll">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-label">E-mail</div>
                        <div class="info-value">
                            <?php echo $dados_usuario['email']; ?>
                        </div>
                    </div>

                    <!-- CPF -->
                    <div class="info-card animate-on-scroll">
                        <div class="info-icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div class="info-label">CPF</div>
                        <div class="info-value">
                            <?php echo $dados_usuario['cpf']; ?>
                        </div>
                    </div>

                    <!-- Telefone -->
                    <div class="info-card animate-on-scroll">
                        <div class="info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="info-label">Telefone</div>
                        <div class="info-value <?php echo empty($dados_usuario['telefone']) ? 'empty' : ''; ?>" id="phoneValue">
                            <?php echo !empty($dados_usuario['telefone']) ? $dados_usuario['telefone'] : 'Não informado'; ?>
                        </div>
                        <button onclick="openEditModal('<?php echo $dados_usuario['telefone']; ?>')" class="edit-button mt-4">
                            <i class="fas fa-edit"></i>
                            <span id="phoneButtonText"><?php echo !empty($dados_usuario['telefone']) ? 'Editar Telefone' : 'Adicionar Telefone'; ?></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Card de Ações Rápidas -->
            <div class="quick-actions-card">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-bolt mr-3 text-orange-500"></i>
                    Ações Rápidas
                </h3>
                
                <div class="space-y-3">
                    <button onclick="openPhotoModal()" class="w-full p-3 bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg transition-all duration-300 flex items-center justify-between group">
                        <div class="flex items-center">
                            <i class="fas fa-camera text-green-600 mr-3"></i>
                            <span class="text-green-800 font-medium">Alterar Foto</span>
                        </div>
                        <i class="fas fa-chevron-right text-green-600 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                    
                    <button class="w-full p-3 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-all duration-300 flex items-center justify-between group">
                        <div class="flex items-center">
                            <i class="fas fa-key text-blue-600 mr-3"></i>
                            <span class="text-blue-800 font-medium">Alterar Senha</span>
                        </div>
                        <i class="fas fa-chevron-right text-blue-600 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                    
                    <button class="w-full p-3 bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded-lg transition-all duration-300 flex items-center justify-between group">
                        <div class="flex items-center">
                            <i class="fas fa-download text-purple-600 mr-3"></i>
                            <span class="text-purple-800 font-medium">Baixar Dados</span>
                        </div>
                        <i class="fas fa-chevron-right text-purple-600 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>
            </div>

            <!-- Card de Estatísticas -->
            <div class="stats-card">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-line mr-3 text-orange-500"></i>
                    Atividade
                </h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-white rounded-lg border">
                        <div>
                            <p class="text-sm text-gray-600">Último Acesso</p>
                            <p class="font-semibold text-gray-800">Hoje, 14:30</p>
                        </div>
                        <i class="fas fa-clock text-2xl text-green-500"></i>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-white rounded-lg border">
                        <div>
                            <p class="text-sm text-gray-600">Perfil Completo</p>
                            <p class="font-semibold text-gray-800">85%</p>
                        </div>
                        <div class="w-12 h-12 relative">
                            <svg class="w-12 h-12 transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-gray-200" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                <path class="text-orange-500" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="85, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-xs font-bold text-orange-600">85%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-white rounded-lg border">
                        <div>
                            <p class="text-sm text-gray-600">Sessões Este Mês</p>
                            <p class="font-semibold text-gray-800">24</p>
                        </div>
                        <i class="fas fa-calendar-alt text-2xl text-blue-500"></i>
                    </div>
                </div>
            </div>

        </div>

    </main>

    <!-- Modal de Edição de Telefone -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-phone"></i>
                    <span id="modalTitle">Adicionar Telefone</span>
                </h3>
                <button onclick="closeEditModal()" class="close-modal" aria-label="Fechar modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="form-group">
                    <label for="telefone" class="form-label">Número de Telefone</label>
                    <input type="tel" 
                           id="telefone" 
                           name="telefone" 
                           class="form-input" 
                           placeholder="(85) 99999-9999"
                           pattern="^$$\d{2}$$\s\d{4,5}-\d{4}$"
                           maxlength="15"
                           required>
                </div>
                
                <div class="form-actions">
                    <button type="button" onclick="closeEditModal()" class="btn-cancel">
                        Cancelar
                    </button>
                    <input type="hidden" name="atualizar_telefone" value="1">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i>
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Upload de Foto -->
    <div id="photoModal" class="modal">
        <div class="modal-content large">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-camera"></i>
                    Gerenciar Foto de Perfil
                </h3>
                <button onclick="closePhotoModal()" class="close-modal" aria-label="Fechar modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <!-- Foto Atual -->
                <div class="text-center">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Foto Atual</h4>
                    <div class="inline-block">
                        <div id="currentPhotoContainer" class="w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center mx-auto">
                            <?php if (!empty($dados_usuario['foto_perfil'])): ?>
                                <img src="<?php echo $dados_usuario['foto_perfil']; ?>" alt="Foto Atual" class="w-full h-full object-cover rounded-full">
                            <?php else: ?>
                                <i class="fas fa-user text-4xl text-gray-400"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Área de Upload e Recorte -->
                <div id="uploadArea" class="text-center">
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="foto_perfil" class="form-label">Selecionar Nova Foto</label>
                            <input type="file" 
                                   id="foto_perfil" 
                                   name="foto_perfil" 
                                   class="form-input" 
                                   accept="image/*"
                                   onchange="handleImageSelect(event)">
                            <small class="text-gray-500 mt-2 block">Formatos: JPG, PNG, GIF. Máximo: 5MB</small>
                        </div>
                    </form>
                </div>

                <!-- Área de Recorte (inicialmente oculta) -->
                <div id="cropArea" class="hidden">
                    <div class="text-center mb-4">
                        <h4 class="text-lg font-semibold text-gray-800">Recortar Foto</h4>
                        <p class="text-gray-600">Arraste e redimensione para selecionar a área desejada</p>
                    </div>
                    
                    <div class="cropper-container mb-4">
                        <img id="cropImage" src="/placeholder.svg" alt="Imagem para recorte" style="max-width: 100%;">
                    </div>

                    <!-- Preview do recorte -->
                    <div class="text-center mb-4">
                        <h5 class="text-base font-semibold text-gray-800 mb-2">Preview</h5>
                        <div class="crop-preview">
                            <img id="cropPreview" src="/placeholder.svg" alt="Preview do recorte">
                        </div>
                    </div>

                    <!-- Controles do cropper -->
                    <div class="cropper-controls">
                        <button type="button" class="cropper-btn secondary" onclick="rotateImage(-90)">
                            <i class="fas fa-undo"></i> <span class="hidden sm:inline">Girar Esquerda</span>
                        </button>
                        <button type="button" class="cropper-btn secondary" onclick="rotateImage(90)">
                            <i class="fas fa-redo"></i> <span class="hidden sm:inline">Girar Direita</span>
                        </button>
                        <button type="button" class="cropper-btn secondary" onclick="resetCrop()">
                            <i class="fas fa-refresh"></i> <span class="hidden sm:inline">Resetar</span>
                        </button>
                        <button type="button" class="cropper-btn primary" onclick="cropAndUpload()">
                            <i class="fas fa-check"></i> <span class="hidden sm:inline">Aplicar</span>
                        </button>
                    </div>
                </div>

                <!-- Remover Foto -->
                <div id="removePhotoSection" class="border-t pt-4 text-center <?php echo empty($dados_usuario['foto_perfil']) ? 'hidden' : ''; ?>">
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="remover_foto" value="1">
                        <button type="submit" class="btn-remove">
                            <i class="fas fa-trash"></i>
                            Remover Foto
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Estado da aplicação
        let currentPhone = '<?php echo $dados_usuario['telefone']; ?>';
        let hasProfilePhoto = <?php echo !empty($dados_usuario['foto_perfil']) ? 'true' : 'false'; ?>;
        let cropper = null;
        let selectedFile = null;

        document.addEventListener('DOMContentLoaded', function() {
            initializeApp();
        });

        function initializeApp() {
            setupPhoneMask();
            setupModalEvents();
            setupKeyboardEvents();
            setupScrollAnimations();
            setupTouchOptimizations();
            
            // Simular dados do usuário (em produção, viria do backend)
            //loadUserData();
        }

        function loadUserData() {
            // Simular carregamento de dados do usuário
            const userData = {
                phone: '', // Vazio para demonstrar funcionalidade
                hasPhoto: false
            };

            currentPhone = userData.phone;
            hasProfilePhoto = userData.hasPhoto;
            
            updatePhoneDisplay();
            updatePhotoDisplay();
        }

        function updatePhoneDisplay() {
            const phoneValue = document.getElementById('phoneValue');
            const phoneButton = document.getElementById('phoneButtonText');
            const modalTitle = document.getElementById('modalTitle');
            const telefoneInput = document.getElementById('telefone');

            if (currentPhone) {
                phoneValue.textContent = currentPhone;
                phoneValue.classList.remove('empty');
                phoneButton.textContent = 'Editar Telefone';
                modalTitle.textContent = 'Editar Telefone';
                telefoneInput.value = currentPhone;
            } else {
                phoneValue.textContent = 'Não informado';
                phoneValue.classList.add('empty');
                phoneButton.textContent = 'Adicionar Telefone';
                modalTitle.textContent = 'Adicionar Telefone';
                telefoneInput.value = '';
            }
        }

        function updatePhotoDisplay() {
            const profileImage = document.getElementById('profileImage');
            const profileIcon = document.getElementById('profileIcon');
            const currentPhotoContainer = document.getElementById('currentPhotoContainer');
            const removePhotoSection = document.getElementById('removePhotoSection');

            if (hasProfilePhoto) {
                profileImage.classList.remove('hidden');
                profileIcon.classList.add('hidden');
                currentPhotoContainer.innerHTML = '<img src="' + profileImage.src + '" alt="Foto Atual" class="w-full h-full object-cover rounded-full">';
                removePhotoSection.classList.remove('hidden');
            } else {
                profileImage.classList.add('hidden');
                profileIcon.classList.remove('hidden');
                currentPhotoContainer.innerHTML = '<i class="fas fa-user text-4xl text-gray-400"></i>';
                removePhotoSection.classList.add('hidden');
            }
        }

        function setupPhoneMask() {
            const telefoneInput = document.getElementById('telefone');
            
            telefoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                
                if (value.length <= 11) {
                    if (value.length >= 2) {
                        value = value.replace(/^(\d{2})/, '($1) ');
                    }
                    if (value.length >= 7) {
                        value = value.replace(/^(\d{2})\s(\d{5})/, '($1) $2-');
                    } else if (value.length >= 6) {
                        value = value.replace(/^(\d{2})\s(\d{4})/, '($1) $2-');
                    }
                }
                
                e.target.value = value;
            });
        }

        function setupModalEvents() {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        if (modal.id === 'editModal') {
                            closeEditModal();
                        } else if (modal.id === 'photoModal') {
                            closePhotoModal();
                        }
                    }
                });
            });
        }

        function setupKeyboardEvents() {
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeEditModal();
                    closePhotoModal();
                }
            });
        }

        function setupScrollAnimations() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.classList.add('visible');
                        }, index * 100);
                    }
                });
            }, observerOptions);

            const animatedElements = document.querySelectorAll('.animate-on-scroll');
            animatedElements.forEach(element => {
                observer.observe(element);
            });
        }

        function setupTouchOptimizations() {
            // Melhorar experiência touch em dispositivos móveis
            const touchElements = document.querySelectorAll('.info-card, .edit-button, .back-button');
            
            touchElements.forEach(element => {
                element.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                }, { passive: true });

                element.addEventListener('touchend', function() {
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                }, { passive: true });
            });
        }

        function showMessage(text, type = 'success') {
            const messageContainer = document.getElementById('messageContainer');
            const messageText = document.getElementById('messageText');
            const icon = messageContainer.querySelector('i');

            messageText.textContent = text;
            messageContainer.className = `message ${type}`;
            
            if (type === 'success') {
                icon.className = 'fas fa-check-circle text-xl';
            } else {
                icon.className = 'fas fa-exclamation-circle text-xl';
            }

            messageContainer.classList.remove('hidden');

            // Auto-hide após 5 segundos
            setTimeout(() => {
                messageContainer.style.opacity = '0';
                setTimeout(() => {
                    messageContainer.classList.add('hidden');
                    messageContainer.style.opacity = '1';
                }, 500);
            }, 5000);
        }

        function openPhotoModal() {
            const modal = document.getElementById('photoModal');
            document.body.style.overflow = 'hidden';
            modal.classList.add('show');
        }

        function closePhotoModal() {
            const modal = document.getElementById('photoModal');
            document.body.style.overflow = '';
            modal.classList.remove('show');
            
            // Reset cropper if exists
            if (window.cropper) {
                window.cropper.destroy();
                window.cropper = null;
            }
            
            // Reset upload area
            const uploadArea = document.getElementById('uploadArea');
            const cropArea = document.getElementById('cropArea');
            
            if (uploadArea) uploadArea.style.display = 'block';
            if (cropArea) cropArea.classList.add('hidden');
            
            // Reset file input
            const fileInput = document.getElementById('foto_perfil');
            if (fileInput) fileInput.value = '';
        }

        function openEditModal(telefone = '', isEdit = false) {
            const modal = document.getElementById('editModal');
            const title = document.getElementById('modalTitle');
            const input = document.getElementById('telefone');
            
            document.body.style.overflow = 'hidden';
            
            if (isEdit) {
                title.textContent = 'Editar Telefone';
                input.value = telefone;
            } else {
                title.textContent = 'Adicionar Telefone';
                input.value = '';
            }
            
            modal.classList.add('show');
            // Focus on input for better UX
            setTimeout(() => input.focus(), 100);
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            document.body.style.overflow = '';
            modal.classList.remove('show');
            
            // Reset form
            const form = modal.querySelector('form');
            if (form) form.reset();
        }

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                if (e.target.id === 'editModal') {
                    closeEditModal();
                } else if (e.target.id === 'photoModal') {
                    closePhotoModal();
                }
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const editModal = document.getElementById('editModal');
                const photoModal = document.getElementById('photoModal');
                
                if (editModal.classList.contains('show')) {
                    closeEditModal();
                } else if (photoModal.classList.contains('show')) {
                    closePhotoModal();
                }
            }
        });

        function handlePhoneSubmit(event) {
            event.preventDefault();
            
            const telefone = document.getElementById('telefone').value;
            const telefoneRegex = /^(\d{2})\s\d{4,5}-\d{4}$/;
            
            if (!telefoneRegex.test(telefone)) {
                showMessage('Por favor, insira um número de telefone válido no formato (85) 99999-9999', 'error');
                return false;
            }

            // Simular salvamento
            currentPhone = telefone;
            updatePhoneDisplay();
            closeEditModal();
            showMessage('Telefone atualizado com sucesso!', 'success');
        }

        function handleImageSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validar tamanho do arquivo (5MB)
            if (file.size > 5 * 1024 * 1024) {
                showMessage('O arquivo é muito grande. Tamanho máximo: 5MB', 'error');
                event.target.value = '';
                return;
            }

            // Validar tipo de arquivo
            if (!file.type.startsWith('image/')) {
                showMessage('Por favor, selecione apenas arquivos de imagem.', 'error');
                event.target.value = '';
                return;
            }

            selectedFile = file;
            
            // Mostrar loading
            const cropArea = document.getElementById('cropArea');
            cropArea.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-primary"></i><p class="mt-2">Carregando imagem...</p></div>';
            cropArea.classList.remove('hidden');
            document.getElementById('uploadArea').classList.add('hidden');
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                // Restaurar conteúdo da área de recorte
                cropArea.innerHTML = `
                    <div class="text-center mb-4">
                        <h4 class="text-lg font-semibold text-gray-800">Recortar Foto</h4>
                        <p class="text-gray-600">Arraste e redimensione para selecionar a área desejada</p>
                    </div>
                    
                    <div class="cropper-container mb-4">
                        <img id="cropImage" src="/placeholder.svg" alt="Imagem para recorte" style="max-width: 100%;">
                    </div>

                    <div class="text-center mb-4">
                        <h5 class="text-base font-semibold text-gray-800 mb-2">Preview</h5>
                        <div class="crop-preview">
                            <img id="cropPreview" src="/placeholder.svg" alt="Preview do recorte">
                        </div>
                    </div>

                    <div class="cropper-controls">
                        <button type="button" class="cropper-btn secondary" onclick="rotateImage(-90)">
                            <i class="fas fa-undo"></i> <span class="hidden sm:inline">Girar Esquerda</span>
                        </button>
                        <button type="button" class="cropper-btn secondary" onclick="rotateImage(90)">
                            <i class="fas fa-redo"></i> <span class="hidden sm:inline">Girar Direita</span>
                        </button>
                        <button type="button" class="cropper-btn secondary" onclick="resetCrop()">
                            <i class="fas fa-refresh"></i> <span class="hidden sm:inline">Resetar</span>
                        </button>
                        <button type="button" class="cropper-btn primary" onclick="cropAndUpload()">
                            <i class="fas fa-check"></i> <span class="hidden sm:inline">Aplicar</span>
                        </button>
                    </div>
                `;
                
                const cropImage = document.getElementById('cropImage');
                const cropPreview = document.getElementById('cropPreview');
                
                cropImage.src = e.target.result;
                cropPreview.src = e.target.result;
                
                // Aguardar um pouco para a imagem carregar
                setTimeout(() => {
                    initCropper();
                    adjustCropperSize();
                }, 100);
            };
            
            reader.readAsDataURL(file);
        }

        function adjustCropperSize() {
            const cropperContainer = document.querySelector('.cropper-container');
            if (!cropperContainer) return;

            const windowHeight = window.innerHeight;
            const maxHeight = Math.min(windowHeight * 0.5, 400);
            
            cropperContainer.style.maxHeight = `${maxHeight}px`;
            
            if (cropper) {
                cropper.destroy();
                initCropper();
            }
        }

        function initCropper() {
            const cropImage = document.getElementById('cropImage');
            
            if (cropper) {
                cropper.destroy();
            }
            
            cropper = new Cropper(cropImage, {
                aspectRatio: 1,
                viewMode: 2,
                dragMode: 'move',
                autoCropArea: 1,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
                ready: function() {
                    updatePreview();
                },
                crop: function(event) {
                    clearTimeout(cropper.previewTimeout);
                    cropper.previewTimeout = setTimeout(updatePreview, 100);
                }
            });
        }

        function updatePreview() {
            if (!cropper) return;
            
            try {
                const cropPreview = document.getElementById('cropPreview');
                const canvas = cropper.getCroppedCanvas({
                    width: 200,
                    height: 200,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'medium'
                });
                
                if (canvas) {
                    cropPreview.src = canvas.toDataURL('image/jpeg', 0.8);
                }
            } catch (error) {
                console.log('Erro ao atualizar preview:', error);
            }
        }

        function rotateImage(degree) {
            if (cropper) {
                cropper.rotate(degree);
            }
        }

        function resetCrop() {
            if (cropper) {
                cropper.reset();
            }
        }

        function cropAndUpload() {
            if (!cropper) {
                showMessage('Nenhuma imagem selecionada para recorte.', 'error');
                return;
            }

            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span class="hidden sm:inline">Processando...</span>';
            btn.disabled = true;

            try {
                const canvas = cropper.getCroppedCanvas({
                    width: 300,
                    height: 300,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });

                if (canvas) {
                    canvas.toBlob(function(blob) {
                        // Simular upload da imagem
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            // Atualizar foto de perfil
                            const profileImage = document.getElementById('profileImage');
                            profileImage.src = e.target.result;
                            hasProfilePhoto = true;
                            updatePhotoDisplay();
                            
                            closePhotoModal();
                            showMessage('Foto de perfil atualizada com sucesso!', 'success');
                        };
                        reader.onerror = function() {
                            showMessage('Erro ao processar imagem. Tente novamente.', 'error');
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                        };
                        reader.readAsDataURL(blob);
                    }, 'image/jpeg', 0.85);
                } else {
                    throw new Error('Erro ao gerar canvas');
                }
            } catch (error) {
                console.error('Erro no recorte:', error);
                showMessage('Erro ao processar imagem. Tente novamente.', 'error');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        function removePhoto() {
            if (confirm('Tem certeza que deseja remover sua foto de perfil?')) {
                const profileImage = document.getElementById('profileImage');
                profileImage.src = '';
                hasProfilePhoto = false;
                updatePhotoDisplay();
                closePhotoModal();
                showMessage('Foto de perfil removida com sucesso!', 'success');
            }
        }

        function resetCropArea() {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            
            if (cropper && cropper.previewTimeout) {
                clearTimeout(cropper.previewTimeout);
            }
            
            document.getElementById('foto_perfil').value = '';
            selectedFile = null;
            
            document.getElementById('uploadArea').classList.remove('hidden');
            document.getElementById('cropArea').classList.add('hidden');
        }

        // Ajustar tamanho do cropper quando a janela for redimensionada
        window.addEventListener('resize', adjustCropperSize);

        // Otimizações para performance
        window.addEventListener('scroll', function() {
            // Throttle scroll events
        }, { passive: true });

        // Preload de recursos críticos
        window.addEventListener('load', function() {
            // Preload de imagens ou recursos necessários
        });
    </script>
</body>

</html>
