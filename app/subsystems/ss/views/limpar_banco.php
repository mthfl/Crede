<?php
require_once(__DIR__ . '/../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../config/connect.php');
$escola = $_SESSION['escola'];

new connect($escola);
require_once(__DIR__ . '/../models/model.select.php');
$select = new select($escola);
$step = 'email';

if (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) && !empty($_POST['email']) && !isset($_POST['senha'])) {
    $step = 'code';
}

if (isset($_POST['senha']) && !empty($_POST['senha']) && isset($_POST['email']) && !empty($_POST['email'])) {
    $senha = $_POST['senha'];
    $email = $_POST['email'];
    require_once(__DIR__ . '/../models/model.admin.php');
    $admin = new admin($escola);
    $result = $admin->verificar_senha($email, $senha);

    switch ($result) {
        case 1:
            header("Location: ../index.php?banco_limpo");
            exit();
        case 2:
            header("Location: ../index.php?erro_senha");
            exit();
        case 3:
            header("Location: ../index.php?erro_senha");
            exit();

        default:
            header("Location: ../index.php?fatal");
            exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Limpar Banco</title>
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
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .focus-ring:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        .btn-animate {
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .btn-animate:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <main class="p-4 sm:p-6 lg:p-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <button type="button" onclick="window.history.back()" class="flex items-center text-gray-600 hover:text-gray-800 transition-all duration-300 group">
                    <div class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center group-hover:scale-105 transition-all duration-300">
                        <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </div>
                    <span class="ml-3 font-medium">Voltar</span>
                </button>
            </div>

            <!-- Main Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                <!-- Header -->
                <div class="text-white p-6" style="background: linear-gradient(135deg, #DC2626, #991B1B);">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/30 shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold font-display tracking-tight">Limpar Banco de Dados</h2>
                            <p class="text-white/90 text-sm mt-1 font-medium">Resetar dados do sistema</p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6 space-y-6">
                    <!-- Warning -->
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Atenção!</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p>Esta operação irá <strong>permanentemente</strong> remover dados do sistema. Esta ação não pode ser desfeita.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if ($step === 'email') { ?>
                        <!-- Step 1: Email -->
                        <form action="limpar_banco.php" method="post" class="space-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">E-mail do Administrador</label>
                                <input type="email" name="email" id="email" required value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" placeholder="admin@dominio.com" class="w-full px-4 py-3.5 rounded-xl transition-all text-base border-2 border-gray-300 focus:outline-none outline-none focus:ring-0" />
                                <p class="text-xs text-gray-500 mt-2">Enviaremos uma senha para este e-mail.</p>
                            </div>
                            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                                <button type="submit" class="px-6 py-3 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition-all btn-animate focus-ring">
                                    Validar senha
                                </button>
                            </div>
                        </form>
                    <?php } else { ?>
                        <!-- Step 2: Senha -->
                        <form action="limpar_banco.php" method="post" class="space-y-6">
                            <input type="hidden" name="email" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" />
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Senha</label>
                                <input type="password" name="senha" required placeholder="senha" class="w-full px-4 py-3.5 rounded-xl transition-all text-base border-2 border-gray-300 tracking-widest text-center focus:outline-none outline-none focus:ring-0" />
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Digite a senha enviada para o e-mail <?= htmlspecialchars($_SESSION['email'] ?? '') ?>.</p></div>
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <a href="limpar_banco.php" class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-all">Voltar</a>
                                <button type="submit" class="px-6 py-3 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition-all btn-animate focus-ring">Validar senha</button>
                            </div>
                        </form>
                    <?php } ?>
                </div>
            </div>
    </main>

</body>

</html>