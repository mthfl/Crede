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
            header("Location: ../index.php?erro");
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
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Limpar Banco de Dados</title>
    <link rel="icon" type="image/png" href="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png">
    
    <!-- Tailwind CSS -->
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
                        danger: '#DC2626',
                        'danger-dark': '#991B1B'
                    },
                    fontFamily: {
                        'display': ['Inter', 'system-ui', 'sans-serif'],
                        'body': ['Inter', 'system-ui', 'sans-serif']
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'float': 'float 6s ease-in-out infinite'
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-8px)' }
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        .focus-ring:focus-visible {
            outline: 3px solid rgb(239 68 68 / 0.3);
            outline-offset: 2px;
            border-radius: 0.75rem;
        }
        .btn-animate {
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .btn-animate:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(220, 38, 38, 0.25);
        }
        .input-focus:focus {
            border-color: #DC2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }
        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-red-400/10 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-orange-400/10 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 2s;"></div>
    </div>

    <main class="container mx-auto px-4 py-8 sm:py-12 lg:py-16 max-w-4xl">
        <!-- Botão Voltar -->
        <div class="mb-8">
            <button type="button" onclick="window.history.back()" 
                    class="group flex items-center gap-3 text-gray-600 hover:text-gray-900 transition-all duration-300">
                <div class="w-11 h-11 rounded-full bg-white shadow-md flex items-center justify-center group-hover:scale-110 transition-all duration-300 border border-gray-200">
                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </div>
                <span class="font-semibold text-sm sm:text-base">Voltar</span>
            </button>
        </div>

        <!-- Card Principal -->
        <div class="bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl border border-gray-200/50 overflow-hidden">
            <!-- Header com Gradiente -->
            <div class="bg-gradient-to-br from-danger to-danger-dark p-6 sm:p-8 text-white">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl glass flex items-center justify-center animate-float shadow-lg">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Limpar Banco de Dados</h1>
                        <p class="text-white/90 text-sm sm:text-base mt-1">Ação irreversível • Cuidado máximo necessário</p>
                    </div>
                </div>
            </div>

            <!-- Conteúdo -->
            <div class="p-6 sm:p-8 space-y-8">
                <!-- Alerta de Perigo -->
                <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-red-800 text-lg">Atenção Crítica!</h3>
                            <p class="mt-2 text-red-700 leading-relaxed">
                                Esta operação <strong class="underline decoration-red-500">excluirá permanentemente</strong> todos os dados do sistema escolar. 
                                <span class="block mt-1 text-sm">Não há recuperação possível após a confirmação.</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Passo 1: E-mail -->
                <?php if ($step === 'email'): ?>
                <form action="limpar_banco.php" method="post" class="space-y-6">
                    <div class="space-y-3">
                        <label for="email" class="block text-sm font-bold text-gray-800">
                            E-mail do Administrador
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            required 
                            value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" 
                            placeholder="admin@escola.ce.gov.br"
                            class="w-full px-5 py-4 rounded-2xl text-base font-medium border-2 border-gray-300 input-focus transition-all focus:border-red-500 focus:ring-0 focus:shadow-lg"
                            autocomplete="email"
                        />
                        <p class="text-xs text-gray-500 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
                            Uma senha de uso único será enviada para este e-mail.
                        </p>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-200">
                        <button type="submit" 
                                class="px-8 py-3.5 rounded-2xl bg-gradient-to-r from-danger to-danger-dark text-white font-bold text-base shadow-lg hover:shadow-xl btn-animate focus-ring flex items-center gap-2">
                            <span>Confirmar Email</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Passo 2: Senha -->
                <?php else: ?>
                <form action="limpar_banco.php" method="post" class="space-y-6">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
                    
                    <div class="space-y-3">
                        <label for="senha" class="block text-sm font-bold text-gray-800">
                            Senha de Confirmação
                        </label>
                        <input 
                            type="password" 
                            name="senha" 
                            id="senha" 
                            required 
                            placeholder="••••••"
                            class="w-full px-5 py-4 rounded-2xl text-base font-mono text-center tracking-widest text-xl border-2 border-gray-300 input-focus transition-all focus:border-red-500 focus:ring-0 focus:shadow-lg"
                            autocomplete="current-password"
                            autofocus
                            aria-label="Senha de confirmação (6 dígitos)"
                            
                        />
                        <p class="text-xs text-gray-500 text-center">
                            Digite a senha de 6 dígitos enviada para <strong class="text-red-600"><?= htmlspecialchars($_POST['email'] ?? '') ?></strong>
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 justify-between pt-4 border-t border-gray-200">
                        <a href="limpar_banco.php" 
                           class="px-6 py-3.5 rounded-2xl border-2 border-gray-300 text-gray-700 font-bold hover:bg-gray-50 transition-all text-center order-2 sm:order-1">
                            ← Voltar
                        </a>
                        <button type="submit" 
                                class="px-8 py-3.5 rounded-2xl bg-gradient-to-r from-danger to-danger-dark text-white font-bold shadow-lg hover:shadow-xl btn-animate focus-ring flex items-center justify-center gap-2 order-1 sm:order-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span>Confirmar Limpeza</span>
                        </button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Rodapé de Segurança -->
        <div class="mt-8 text-center">
            <p class="text-xs text-gray-500">
                Esta ação é registrada no log de auditoria do sistema.
            </p>
        </div>
    </main>
</body>
</html>