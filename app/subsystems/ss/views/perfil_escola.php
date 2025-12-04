<?php
require_once __DIR__ . '/../models/sessions.php';
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require_once __DIR__ . '/../config/connect.php';
$escola_banco = $_SESSION['escola'];

new connect($escola_banco);

// Conexão direta com banco crede_users para tabela escolas
$config = require __DIR__ . '/../../../.env/config.php';

try {
    $host_user = $config['local']["crede_users"]['host'];
    $database_user = $config['local']["crede_users"]['banco'];
    $user_user = $config['local']["crede_users"]['user'];
    $password_user = $config['local']["crede_users"]['senha'];

    $pdo_users = new PDO('mysql:host=' . $host_user . ';dbname=' . $database_user . ';charset=utf8', $user_user, $password_user);
    $pdo_users->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo_users->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Tenta hospedagem se local falhar
    $host_user = $config['hospedagem']["crede_users"]['host'];
    $database_user = $config['hospedagem']["crede_users"]['banco'];
    $user_user = $config['hospedagem']["crede_users"]['user'];
    $password_user = $config['hospedagem']["crede_users"]['senha'];

    $pdo_users = new PDO('mysql:host=' . $host_user . ';dbname=' . $database_user . ';charset=utf8', $user_user, $password_user);
    $pdo_users->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo_users->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}

$mensagem = '';
$tipo_mensagem = '';

// Remover foto de perfil da escola
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remover_foto_escola'])) {
    // Buscar foto atual
    $stmtFoto = $pdo_users->prepare("SELECT foto_perfil FROM escolas WHERE escola_banco = :escola_banco LIMIT 1");
    $stmtFoto->bindValue(':escola_banco', $escola_banco);
    $stmtFoto->execute();
    $dadosFoto = $stmtFoto->fetch();

    if ($dadosFoto && !empty($dadosFoto['foto_perfil'])) {
        $arquivo = __DIR__ . '/../assets/fotos_escola/' . $dadosFoto['foto_perfil'];
        if (is_file($arquivo)) {
            @unlink($arquivo);
        }
    }

    $stmtDel = $pdo_users->prepare("UPDATE escolas SET foto_perfil = NULL WHERE escola_banco = :escola_banco LIMIT 1");
    $stmtDel->bindValue(':escola_banco', $escola_banco);
    $stmtDel->execute();

    $mensagem = 'Foto de perfil da escola removida com sucesso!';
    $tipo_mensagem = 'success';
}

// Upload de nova foto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_foto_escola'])) {
    if (isset($_FILES['foto_escola']) && $_FILES['foto_escola']['error'] === UPLOAD_ERR_OK) {
        $fileTmp  = $_FILES['foto_escola']['tmp_name'];
        $fileName = basename($_FILES['foto_escola']['name']);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($ext, $permitidas)) {
            $mensagem = 'Formato de imagem inválido. Use JPG, PNG, GIF ou WEBP.';
            $tipo_mensagem = 'error';
        } else {
            $novoNome = 'escola_' . preg_replace('/[^a-z0-9_]/i', '_', $escola_banco) . '_' . time() . '.' . $ext;
            $destinoDir = __DIR__ . '/../assets/fotos_escola';
            if (!is_dir($destinoDir)) {
                @mkdir($destinoDir, 0775, true);
            }
            $destino = $destinoDir . '/' . $novoNome;

            if (move_uploaded_file($fileTmp, $destino)) {
                $stmt = $pdo_users->prepare("UPDATE escolas SET foto_perfil = :foto WHERE escola_banco = :escola_banco LIMIT 1");
                $stmt->bindValue(':foto', $novoNome);
                $stmt->bindValue(':escola_banco', $escola_banco);
                $stmt->execute();

                $mensagem = 'Foto de perfil da escola atualizada com sucesso!';
                $tipo_mensagem = 'success';

                // Evita reenvio do formulário ao atualizar a página
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $mensagem = 'Erro ao salvar a imagem enviada.';
                $tipo_mensagem = 'error';
            }
        }
    } else {
        $mensagem = 'Selecione uma imagem válida para enviar.';
        $tipo_mensagem = 'error';
    }
}

// Atualizar dados da escola (nome, endereço, telefone, e-mail)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_dados_escola'])) {
    $novoNome      = trim($_POST['nome_escola'] ?? '');
    $novoEndereco  = trim($_POST['localizacao'] ?? '');
    $novoTelefone  = trim($_POST['telefone'] ?? '');
    $novoEmail     = trim($_POST['email'] ?? '');

    try {
        $stmtUpdate = $pdo_users->prepare("UPDATE escolas 
            SET nome_escola = :nome,
                localizacao = :endereco,
                telefone = :telefone,
                email = :email
            WHERE escola_banco = :escola_banco
            LIMIT 1");

        $stmtUpdate->bindValue(':nome', $novoNome);
        $stmtUpdate->bindValue(':endereco', $novoEndereco);
        $stmtUpdate->bindValue(':telefone', $novoTelefone);
        $stmtUpdate->bindValue(':email', $novoEmail);
        $stmtUpdate->bindValue(':escola_banco', $escola_banco);
        $stmtUpdate->execute();

        $mensagem = 'Dados da escola atualizados com sucesso!';
        $tipo_mensagem = 'success';

        // Evita reenvio do formulário ao atualizar a página
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } catch (Exception $e) {
        $mensagem = 'Erro ao atualizar os dados da escola.';
        $tipo_mensagem = 'error';
    }
}

$stmtEscola = $pdo_users->prepare("SELECT * FROM escolas WHERE escola_banco = :escola_banco LIMIT 1");
$stmtEscola->bindValue(':escola_banco', $escola_banco);
$stmtEscola->execute();
$escola = $stmtEscola->fetch() ?: [];

$fotoPerfil = '';
if (!empty($escola['foto_perfil'])) {
    $fp = $escola['foto_perfil'];
    if (preg_match('/^https?:\/\//', $fp) || (isset($fp[0]) && $fp[0] === '/')) {
        $fotoPerfil = $fp;
    } else {
        $fotoPerfil = '../assets/fotos_escola/' . $fp;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil da Escola - Sistema Seleção</title>
    <link rel="icon" type="image/png" href="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png">
    <script src="https://cdn.tailwindcss.com"></script>
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
                    },
                    fontFamily: {
                        'display': ['Inter', 'system-ui', 'sans-serif'],
                        'body': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    spacing: {
                        '18': '4.5rem',
                        '88': '22rem',
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.6s ease-out',
                    }
                }
            }
        }
    </script>
   <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

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

        * {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            backdrop-filter: blur(10px);
            background: linear-gradient(135deg, var(--primary) 0%, var(--dark) 100%);
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .overlay {
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            backdrop-filter: blur(2px);
        }

        .overlay.show {
            opacity: 1;
            visibility: visible;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes bounceIn {
            0% { opacity: 0; transform: translateY(50px); }
            50% { opacity: 1; transform: translateY(-10px); }
            70% { transform: translateY(5px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @keyframes shimmer {
            0% { transform: rotate(45deg) translateX(-100%); }
            100% { transform: rotate(45deg) translateX(100%); }
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            overflow: hidden;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 30px -10px rgba(0, 90, 36, 0.3);
        }

        .nav-item {
            position: relative;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border-radius: 12px;
        }

        .nav-item:hover {
            transform: translateX(8px);
            background: rgba(255, 255, 255, 0.1);
        }

        .focus-ring:focus {
            outline: 2px solid var(--secondary);
            outline-offset: 2px;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: var(--dark);
        }

        /* Layout de perfil */
        .profile-container {
            display: flex;
            flex-direction: column;
            gap: clamp(1.5rem, 4vw, 2.5rem);
            max-width: 1400px;
            margin: 0 auto;
            padding: clamp(1rem, 4vw, 2rem);
        }

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

        .profile-content {
            display: grid;
            grid-template-columns: 1fr;
            gap: clamp(1.5rem, 4vw, 2rem);
        }

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
            cursor: default;
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
            z-index: 0;
        }

        .profile-avatar:hover::before {
            transform: rotate(45deg) translate(50%, 50%);
        }

        .profile-avatar:hover {
            box-shadow: 0 20px 60px rgba(0, 90, 36, 0.4);
            transform: scale(1.05);
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            position: relative;
            z-index: 1;
        }

        .profile-avatar span {
            position: relative;
            z-index: 1;
            font-weight: 700;
        }

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
            cursor: default;
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

/* Estilos para inputs do formulário */
.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-size: clamp(0.75rem, 2.5vw, 0.875rem);
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-input {
    width: 100%;
    padding: clamp(0.75rem, 3vw, 0.875rem) 1rem;
    border: 2px solid var(--gray-200);
    border-radius: 0.75rem;
    font-size: clamp(0.875rem, 2.5vw, 1rem);
    transition: all 0.3s ease;
    background: var(--gray-50);
    color: var(--gray-800);
    font-weight: 500;
}

.form-input:hover {
    border-color: var(--gray-300);
    background: var(--white);
}

.form-input:focus {
    outline: none;
    border-color: var(--primary);
    background: var(--white);
    box-shadow: 0 0 0 4px rgba(0, 90, 36, 0.1);
    transform: translateY(-1px);
}

.form-input::placeholder {
    color: var(--gray-400);
    font-weight: 400;
}

.form-input:disabled {
    background: var(--gray-100);
    color: var(--gray-500);
    cursor: not-allowed;
    opacity: 0.6;
}

/* Input com ícone */
.input-with-icon {
    position: relative;
}

.input-with-icon .form-input {
    padding-left: 3rem;
}

.input-with-icon .input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-400);
    pointer-events: none;
    transition: color 0.3s ease;
}

.input-with-icon .form-input:focus ~ .input-icon {
    color: var(--primary);
}

/* Estados de validação */
.form-input.valid {
    border-color: var(--success);
    background: rgba(16, 185, 129, 0.05);
}

.form-input.invalid {
    border-color: var(--error);
    background: rgba(239, 68, 68, 0.05);
}

.form-input.valid:focus {
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
}

.form-input.invalid:focus {
    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
}

/* Mensagem de erro/sucesso no input */
.input-feedback {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.input-feedback.success {
    color: var(--success);
}

.input-feedback.error {
    color: var(--error);
}

/* Textarea com mesmos estilos */
.form-textarea {
    width: 100%;
    padding: clamp(0.75rem, 3vw, 0.875rem) 1rem;
    border: 2px solid var(--gray-200);
    border-radius: 0.75rem;
    font-size: clamp(0.875rem, 2.5vw, 1rem);
    transition: all 0.3s ease;
    background: var(--gray-50);
    color: var(--gray-800);
    font-weight: 500;
    resize: vertical;
    min-height: 100px;
    font-family: inherit;
}

.form-textarea:hover {
    border-color: var(--gray-300);
    background: var(--white);
}

.form-textarea:focus {
    outline: none;
    border-color: var(--primary);
    background: var(--white);
    box-shadow: 0 0 0 4px rgba(0, 90, 36, 0.1);
}

/* Select customizado */
.form-select {
    width: 100%;
    padding: clamp(0.75rem, 3vw, 0.875rem) 1rem;
    padding-right: 2.5rem;
    border: 2px solid var(--gray-200);
    border-radius: 0.75rem;
    font-size: clamp(0.875rem, 2.5vw, 1rem);
    transition: all 0.3s ease;
    background: var(--gray-50);
    color: var(--gray-800);
    font-weight: 500;
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1.25rem;
}

.form-select:hover {
    border-color: var(--gray-300);
    background-color: var(--white);
}

.form-select:focus {
    outline: none;
    border-color: var(--primary);
    background-color: var(--white);
    box-shadow: 0 0 0 4px rgba(0, 90, 36, 0.1);
}

/* Checkbox e Radio customizados */
.form-checkbox,
.form-radio {
    width: 1.25rem;
    height: 1.25rem;
    border: 2px solid var(--gray-300);
    cursor: pointer;
    transition: all 0.3s ease;
}

.form-checkbox {
    border-radius: 0.375rem;
}

.form-radio {
    border-radius: 50%;
}

.form-checkbox:checked,
.form-radio:checked {
    background-color: var(--primary);
    border-color: var(--primary);
}

.form-checkbox:focus,
.form-radio:focus {
    outline: none;
    box-shadow: 0 0 0 4px rgba(0, 90, 36, 0.1);
}

/* Label para checkbox/radio */
.checkbox-label,
.radio-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    user-select: none;
    padding: 0.5rem;
    border-radius: 0.5rem;
    transition: background 0.2s ease;
}

.checkbox-label:hover,
.radio-label:hover {
    background: var(--gray-50);
}

/* Input file customizado */
.form-file {
    display: block;
    width: 100%;
    padding: clamp(0.75rem, 3vw, 0.875rem) 1rem;
    border: 2px dashed var(--gray-300);
    border-radius: 0.75rem;
    font-size: clamp(0.875rem, 2.5vw, 1rem);
    transition: all 0.3s ease;
    background: var(--gray-50);
    color: var(--gray-700);
    cursor: pointer;
}

.form-file:hover {
    border-color: var(--primary);
    background: rgba(0, 90, 36, 0.05);
}

.form-file:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(0, 90, 36, 0.1);
}

/* Responsividade dos inputs */
@media (max-width: 640px) {
    .form-input,
    .form-textarea,
    .form-select {
        font-size: 16px; /* Previne zoom no iOS */
    }
}

/* Animação de foco suave */
@keyframes inputFocus {
    0% {
        box-shadow: 0 0 0 0 rgba(0, 90, 36, 0.1);
    }
    100% {
        box-shadow: 0 0 0 4px rgba(0, 90, 36, 0.1);
    }
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
    animation: inputFocus 0.3s ease-out;
}

.info-edit-btn {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    width: 2rem;
    height: 2rem;
    border-radius: 999px;
    border: none;
    background: rgba(0, 90, 36, 0.06);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    cursor: pointer;
    transition: all 0.2s ease;
}

.info-edit-btn:hover {
    background: rgba(0, 90, 36, 0.12);
    transform: translateY(-1px);
}
        }

        .info-edit-btn:hover {
            background: rgba(0, 90, 36, 0.12);
            transform: translateY(-1px);
        }

        /* Modal (compatível com perfil.php) */
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

        /* Botões */
        .btn-primary {
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
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 90, 36, 0.35);
        }

        .btn-primary:active {
            transform: translateY(0);
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

        /* Responsividade adicional */
        @media (max-width: 360px) {
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
        }

        /* Melhorias para touch */
        @media (hover: none) and (pointer: coarse) {
            .info-card:hover,
            .profile-avatar:hover,
            .btn-primary:hover {
                transform: none;
            }

            .info-card:active {
                transform: scale(0.98);
            }

            .btn-primary:active {
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
        .btn-primary:focus {
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
        }
    </style>
</head>

<body class="bg-white min-h-screen font-body">
    <div id="overlay" class="overlay fixed inset-0 bg-black/30 z-40 lg:hidden"></div>
    <div class="flex h-screen bg-gray-50 overflow-hidden">
        <aside id="sidebar" class="sidebar fixed left-0 top-0 h-screen w-80 shadow-2xl z-50 lg:translate-x-0 lg:static lg:z-auto custom-scrollbar overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-8 pb-6 border-b border-white/20">
                    <div>
                        <div class="flex items-center space-x-3 mb-2">
                            <img src="../assets/Brasão_do_Ceará.svg.png" alt="Brasão do Ceará" class="w-8 h-10 transition-transform hover:scale-105">
                            <h2 class="text-white text-2xl font-bold font-display">Sistema Seleção</h2>
                        </div>
                    </div>
                    <button id="closeSidebar" class="text-white lg:hidden p-2 rounded-xl hover:bg-white/10 focus-ring">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <nav class="space-y-2">
                    <?php if (isset($_SESSION['tipo_usuario']) && ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'cadastrador')) { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.1s;">
                            <a href="../index.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring ">
                                <div class="w-12 h-12 bg-white/10  rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Início</span>
                                    <p class="text-green-200 text-xs mt-1">Página inicial</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.2s;">
                            <a href="usuario.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring ">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Usuários</span>
                                    <p class="text-green-200 text-xs mt-1">Controle de acesso</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.25s;">
                            <a href="cursos.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Cursos</span>
                                    <p class="text-green-200 text-xs mt-1">Administrar cursos</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.3s;">
                            <a href="cotas.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a5 5 0 10-10 0v2M5 9h14l-1 11H6L5 9z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Cotas</span>
                                    <p class="text-green-200 text-xs mt-1">Regras e perfis</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                    <?php if (isset($_SESSION['tipo_usuario']) && ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'cadastrador')) { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.35s;">
                            <a href="candidatos.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Candidatos</span>
                                    <p class="text-green-200 text-xs mt-1">Gerenciar inscrições</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                    <?php if (isset($_SESSION['tipo_usuario']) && ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'cadastrador')) { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.4s;">
                            <a href="solicitar_alteracao.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Requisições</span>
                                    <p class="text-green-200 text-xs mt-1">Alteração de candidato</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.45s;">
                            <a href="relatorios.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Relatórios</span>
                                    <p class="text-green-200 text-xs mt-1">Gerar documentos</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.5s;">
                            <a href="limpar_banco.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-red-500 group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Limpar Banco</span>
                                    <p class="text-green-200 text-xs mt-1">Resetar dados</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                    <div class="animate-slide-in-left" style="animation-delay: 0.53s;">
                        <a href="perfil_escola.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group bg-white/10 focus-ring">
                            <div class="w-12 h-12 bg-secondary rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 17l9 4 9-4"></path>
                                </svg>
                            </div>
                            <div>
                                <span class="font-semibold text-base">Perfil Escola</span>
                                <p class="text-green-200 text-xs mt-1">Dados e foto da escola</p>
                            </div>
                        </a>
                    </div>

                    <div class="animate-slide-in-left" style="animation-delay: 0.55s;">
                        <a href="faq.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <span class="font-semibold text-base">FAQ</span>
                                <p class="text-green-200 text-xs mt-1">Dúvidas frequentes</p>
                            </div>
                        </a>
                    </div>
                </nav>
            </div>
        </aside>
        <div class="main-content flex-1 bg-white h-screen overflow-y-auto">
            <header class="bg-white shadow-sm border-b border-gray-200 z-30 sticky top-0">
                <div class="px-3 sm:px-4 md:px-6 lg:px-8 py-3 sm:py-4">
                    <div class="flex items-center justify-between">
                        <button id="openSidebar" class="text-primary lg:hidden p-2 sm:p-3 rounded-xl hover:bg-accent focus-ring">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <div class="flex items-center space-x-2 sm:space-x-4 lg:ml-auto">
                            <div class="hidden sm:block text-right">
                                <p class="text-xs sm:text-sm font-semibold text-gray-900">Bem-vindo,</p>
                                <p class="text-xs sm:text-sm text-primary font-medium"><?= $_SESSION['nome'] ?? 'Usuário' ?></p>
                            </div>
                            <a href="../../main/views/perfil.php" title="Perfil" class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-primary to-dark rounded-full flex items-center justify-center hover:brightness-95 focus-ring">
                                <span class="text-white font-bold text-xs sm:text-sm"><?= strtoupper(substr($_SESSION['nome'] ?? 'U', 0, 1)) ?></span>
                            </a>
                            <a href="../models/sessions.php?sair" class="bg-primary text-white px-3 sm:px-4 md:px-6 py-2 sm:py-3 rounded-xl hover:bg-dark font-semibold shadow-lg focus-ring text-xs sm:text-sm">
                                <span class="hidden sm:inline">Sair</span>
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-4 sm:p-6 lg:p-8">
                <section class="profile-container">
                    <?php if ($mensagem): ?>
                        <div class="message <?php echo $tipo_mensagem === 'success' ? 'success' : 'error'; ?>">
                            <i class="fas <?php echo $tipo_mensagem === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> text-xl"></i>
                            <span><?php echo htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="profile-hero">
                        <div class="profile-avatar">
                            <?php if ($fotoPerfil): ?>
                                <img src="<?php echo htmlspecialchars($fotoPerfil, ENT_QUOTES, 'UTF-8'); ?>" alt="Foto da escola">
                            <?php else: ?>
                                <span><?php echo strtoupper(substr($escola['nome_escola'] ?? 'E', 0, 1)); ?></span>
                            <?php endif; ?>
                        </div>
                        <h1 class="text-4xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($escola['nome_escola'] ?? 'Escola', ENT_QUOTES, 'UTF-8'); ?></h1>
                       

                        <div class="flex items-center justify-center gap-3 flex-wrap mt-4 mb-4">
                            <button id="openModalFotoEscola" type="button" class="btn-primary inline-flex items-center justify-center gap-2 px-6 py-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                <span>Atualizar foto da escola</span>
                            </button>

                            <?php if ($fotoPerfil): ?>
                                <form method="POST" class="inline">
                                    <button type="submit" name="remover_foto_escola" class="btn-remove inline-flex items-center justify-center gap-2 px-4 py-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span>Remover foto</span>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="profile-content">
                        <div class="profile-main-card">
                            
                            <div class="info-grid">
                                <div class="info-card animate-on-scroll">
                                    <button type="button" class="info-edit-btn" onclick="openEditarCampo('nome')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L13 14l-4 1 1-4 8.5-8.5z" />
                                        </svg>
                                    </button>
                                    <div class="info-icon">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <div class="info-label">Nome da Escola</div>
                                    <div class="info-value"><?php echo htmlspecialchars($escola['nome_escola'] ?? 'Não encontrado', ENT_QUOTES, 'UTF-8'); ?></div>
                                </div>

                                <div class="info-card animate-on-scroll">
                                    <button type="button" class="info-edit-btn" onclick="openEditarCampo('endereco')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L13 14l-4 1 1-4 8.5-8.5z" />
                                        </svg>
                                    </button>
                                    <div class="info-icon">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="info-label">Endereço</div>
                                    <div class="info-value"><?php echo htmlspecialchars($escola['localizacao'] ?? 'Não informado', ENT_QUOTES, 'UTF-8'); ?></div>
                                </div>

                                <div class="info-card animate-on-scroll">
                                    <button type="button" class="info-edit-btn" onclick="openEditarCampo('telefone')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L13 14l-4 1 1-4 8.5-8.5z" />
                                        </svg>
                                    </button>
                                    <div class="info-icon">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                    <div class="info-label">Telefone</div>
                                    <div class="info-value"><?php echo htmlspecialchars($escola['telefone'] ?? 'Não informado', ENT_QUOTES, 'UTF-8'); ?></div>
                                </div>

                                <div class="info-card animate-on-scroll">
                                    <button type="button" class="info-edit-btn" onclick="openEditarCampo('email')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L13 14l-4 1 1-4 8.5-8.5z" />
                                        </svg>
                                    </button>
                                    <div class="info-icon">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="info-label">E-mail</div>
                                    <div class="info-value"><?php echo htmlspecialchars($escola['email'] ?? 'Não informado', ENT_QUOTES, 'UTF-8'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>

<!-- Modal de foto da escola (mesma estrutura visual do perfil de usuário) -->
<div id="modalFotoEscola" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">
                <div class="info-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <span>Atualizar foto da escola</span>
            </div>
            <button id="closeModalFotoEscola" type="button" class="close-modal">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div class="space-y-2">
                <label class="info-label block">Selecionar nova foto</label>
                <input type="file"
                       name="foto_escola"
                       accept="image/*"
                       class="block w-full text-sm text-gray-700 border-2 border-gray-200 rounded-xl cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all px-4 py-3">
                <p class="text-xs text-gray-500">Formatos aceitos: JPG, PNG, GIF, WEBP. Tamanho máximo recomendado: 5MB.</p>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="toggleModalFotoEscola(false)">Cancelar</button>

                <button type="submit" name="upload_foto_escola" class="btn-save">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Atualizar foto</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de edição dos dados da escola -->
<div id="modalEditarEscola" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">
                <div class="info-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L13 14l-4 1 1-4 8.5-8.5z" />
                    </svg>
                </div>
                <span>Editar dados da escola</span>
            </div>
            <button id="closeModalEditarEscola" type="button" class="close-modal">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" class="space-y-4">
            <div class="form-group" data-campo="nome">
                <label class="form-label" for="nome_escola">Nome da escola</label>
                <input type="text" id="nome_escola" name="nome_escola" class="form-input" required
                       value="<?php echo htmlspecialchars($escola['nome_escola'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="form-group" data-campo="endereco">
                <label class="form-label" for="localizacao">Endereço</label>
                <input type="text" id="localizacao" name="localizacao" class="form-input"
                       value="<?php echo htmlspecialchars($escola['localizacao'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="form-group" data-campo="telefone">
                <label class="form-label" for="telefone">Telefone</label>
                <input type="text" id="telefone" name="telefone" class="form-input"
                       value="<?php echo htmlspecialchars($escola['telefone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="form-group" data-campo="email">
                <label class="form-label" for="email">E-mail</label>
                <input type="email" id="email" name="email" class="form-input"
                       value="<?php echo htmlspecialchars($escola['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="toggleModalEditarEscola(false)">Cancelar</button>
                <button type="submit" name="salvar_dados_escola" class="btn-save">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Salvar alterações</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const openSidebar = document.getElementById('openSidebar');
    const closeSidebar = document.getElementById('closeSidebar');

    function toggleSidebar(show) {
        if (show) {
            sidebar.classList.add('open');
            overlay.classList.add('show');
        } else {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        }
    }

    if (openSidebar) openSidebar.addEventListener('click', () => toggleSidebar(true));
    if (closeSidebar) closeSidebar.addEventListener('click', () => toggleSidebar(false));
    if (overlay) overlay.addEventListener('click', () => toggleSidebar(false));

    // Scroll animations
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

    document.addEventListener('DOMContentLoaded', setupScrollAnimations);

    // Modal de foto da escola
    const modalFotoEscola = document.getElementById('modalFotoEscola');
    const openModalFotoEscola = document.getElementById('openModalFotoEscola');
    const closeModalFotoEscola = document.getElementById('closeModalFotoEscola');

    function toggleModalFotoEscola(show) {
        if (!modalFotoEscola) return;
        if (show) {
            modalFotoEscola.classList.add('show');
        } else {
            modalFotoEscola.classList.remove('show');
        }
    }

    if (openModalFotoEscola && modalFotoEscola) {
        openModalFotoEscola.addEventListener('click', () => toggleModalFotoEscola(true));
    }

    if (closeModalFotoEscola && modalFotoEscola) {
        closeModalFotoEscola.addEventListener('click', () => toggleModalFotoEscola(false));
    }

    if (modalFotoEscola) {
        modalFotoEscola.addEventListener('click', (e) => {
            if (e.target === modalFotoEscola) {
                toggleModalFotoEscola(false);
            }
        });
    }

    // Modal de edição dos dados da escola
    const modalEditarEscola = document.getElementById('modalEditarEscola');
    const openModalEditarEscola = document.getElementById('openModalEditarEscola');
    const closeModalEditarEscola = document.getElementById('closeModalEditarEscola');

    function toggleModalEditarEscola(show) {
        if (!modalEditarEscola) return;
        if (show) {
            modalEditarEscola.classList.add('show');
        } else {
            modalEditarEscola.classList.remove('show');
        }
    }

    if (openModalEditarEscola && modalEditarEscola) {
        openModalEditarEscola.addEventListener('click', () => toggleModalEditarEscola(true));
    }

    if (closeModalEditarEscola && modalEditarEscola) {
        closeModalEditarEscola.addEventListener('click', () => toggleModalEditarEscola(false));
    }

    if (modalEditarEscola) {
        modalEditarEscola.addEventListener('click', (e) => {
            if (e.target === modalEditarEscola) {
                toggleModalEditarEscola(false);
            }
        });
    }

    // Abre modal de edição mostrando apenas o campo solicitado
    window.openEditarCampo = function (campo) {
        if (!modalEditarEscola) return;

        const grupos = modalEditarEscola.querySelectorAll('.form-group[data-campo]');
        grupos.forEach(grupo => {
            const c = grupo.getAttribute('data-campo');
            const input = grupo.querySelector('.form-input');
            if (!input) return;

            if (c === campo) {
                grupo.style.display = '';
                input.required = true;
                setTimeout(() => input.focus(), 50);
            } else {
                grupo.style.display = 'none';
                input.required = false;
            }
        });

        toggleModalEditarEscola(true);
    };

    // Máscara de telefone no input de edição
    const inputTelefone = document.getElementById('telefone');
    if (inputTelefone) {
        inputTelefone.addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '');

            if (v.length > 11) v = v.slice(0, 11);

            if (v.length <= 10) {
                // Formato fixo ou celular antigo: (99) 9999-9999
                v = v.replace(/(\d{2})(\d)/, '($1) $2');
                v = v.replace(/(\d{4})(\d)/, '$1-$2');
            } else {
                // Celular: (99) 99999-9999
                v = v.replace(/(\d{2})(\d)/, '($1) $2');
                v = v.replace(/(\d{5})(\d)/, '$1-$2');
            }

            e.target.value = v;
        });
    }
 </script>
 </body>
 </html>
