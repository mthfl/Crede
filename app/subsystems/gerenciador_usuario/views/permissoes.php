<?php
require_once(__DIR__ . '/../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../models/model.select.php');
$select = new select();
$usuarios = $select->listar_usuarios();
$setores = $select->listar_setores();

// Get selected user ID from query parameter
$selected_user_id = isset($_GET['user_id']) && is_numeric($_GET['user_id']) ? (int)$_GET['user_id'] : null;
$permissions = [];
if ($selected_user_id !== null) {
    $permissions = $select->listarPermissoesUsuario($selected_user_id);
    if ($permissions === false) {
        $permissions = [];
    }
}

$userName = isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Usuário';
$userSetor = isset($_SESSION['setor']) ? $_SESSION['setor'] : 'Sistema de Gestão';
$userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$userInitial = function_exists('mb_substr') ? mb_strtoupper(mb_substr($userName, 0, 1, 'UTF-8'), 'UTF-8') : strtoupper(substr($userName, 0, 1));
$fotoPerfil = isset($_SESSION['foto_perfil']) ? $_SESSION['foto_perfil'] : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="content-language" content="pt-BR">
    <title>Gerenciar Permissões - CREDE</title>
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
        .card-enhanced {
            background: linear-gradient(145deg, #ffffff 0%, #f8faf9 100%);
            border: 1px solid rgba(229, 231, 235, 0.6);
            backdrop-filter: blur(10px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        .card-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
            transition: left 0.6s;
        }
        .card-enhanced:hover::before {
            left: 100%;
        }
        .card-enhanced:hover {
            box-shadow: 0 25px 80px -15px rgba(0, 0, 0, 0.15), 0 10px 30px -5px rgba(0, 0, 0, 0.1);
            border-color: rgba(255, 165, 0, 0.3);
        }
        .header-glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(229, 231, 235, 0.8);
        }
        .bg-decoration {
            position: fixed;
            pointer-events: none;
            z-index: -1;
        }
        .bg-circle-1 {
            top: 10%;
            right: 10%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(0, 90, 36, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }
        .bg-circle-2 {
            bottom: 20%;
            left: 5%;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(255, 165, 0, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: sway 6s ease-in-out infinite reverse;
        }
        .input-enhanced {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            border: 2px solid #E5E7EB;
        }
        .input-enhanced:focus {
            border-color: #FFA500;
            box-shadow: 0 0 0 4px rgba(255, 165, 0, 0.1);
            transform: translateY(-1px);
        }
        .btn-primary {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.2), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s;
        }
        .btn-primary:hover::before {
            transform: translateX(100%);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 90, 36, 0.3);
        }
        .notification-enter {
            transform: translateX(100%);
        }
        .notification-exit {
            transform: translateX(100%);
        }
    </style>
</head>
<body class="text-gray-800 font-sans min-h-screen">
    <!-- Background decorations -->
    <div class="bg-decoration bg-circle-1"></div>
    <div class="bg-decoration bg-circle-2"></div>
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="header-glass sticky top-0 z-40 px-3 sm:px-6 py-3 sm:py-4 animate-slide-in">
            <div class="flex items-center justify-between max-w-7xl mx-auto">
                <div class="flex items-center gap-2 sm:gap-4">
                    <a href="../index.php" class="p-2 sm:p-3 rounded-xl hover:bg-gray-100 text-gray-600 transition-all group">
                        <i class="fa-solid fa-arrow-left text-base sm:text-lg group-hover:scale-110 transition-transform"></i>
                    </a>
                    <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl flex items-center justify-center">
                        <img class="w-6 h-6 sm:w-10 sm:h-10 object-contain" src="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png" alt="Logo CREDE">
                    </div>
                    <div>
                        <h1 class="font-bold text-base sm:text-xl text-dark font-heading">CREDE</h1>
                        <p class="text-xs text-gray-500 font-medium hidden sm:block">Gerenciamento de Permissões</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-4">
                    <div class="hidden sm:block text-right">
                        <p class="text-sm font-semibold text-dark"><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($userSetor, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="relative">
                        <button id="userMenuButton" class="p-1 rounded-full hover:ring-2 hover:ring-primary/30 transition">
                            <?php if (!empty($fotoPerfil) && $fotoPerfil !== 'default.png') { ?>
                                <img src="<?php echo '../assets/fotos_perfil/' . htmlspecialchars($fotoPerfil, ENT_QUOTES, 'UTF-8'); ?>" alt="Foto de perfil" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full object-cover border border-gray-200">
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
                            <a href="<?php echo '../views/perfil.php'; ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fa-solid fa-user mr-2"></i> Meu Perfil
                            </a>
                            <button onclick="logout()" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Sair
                            </button>
                        </div>
                    </div>
                    <button onclick="logout()" class="p-2 sm:p-3 rounded-xl text-red-600 hover:bg-red-50 transition-all">
                        <i class="fa-solid fa-arrow-right-from-bracket text-base sm:text-lg"></i>
                    </button>
                </div>
            </div>
        </header>
        <!-- Main Content -->
        <main class="px-2 sm:px-6 py-4 sm:py-8 flex-1">
            <div class="min-h-full">
                <!-- Permissions Management Panel -->
                <div class="card-enhanced rounded-2xl overflow-hidden animate-slide-up">
                    <div class="p-4 sm:p-6">
                        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6">
                            <!-- Lista de Usuários -->
                            <div class="xl:col-span-1 flex flex-col">
                                <h4 class="text-lg sm:text-xl font-semibold text-dark mb-4 flex items-center gap-2">
                                    <i class="fa-solid fa-users text-primary"></i>
                                    Selecionar Usuário
                                </h4>
                                <div class="mb-4">
                                    <div class="relative">
                                        <input type="text" id="searchUser" placeholder="Pesquisar por nome..." class="input-enhanced w-full px-4 py-3 pl-10 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm bg-white">
                                        <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    </div>
                                </div>
                                <div class="space-y-3 max-h-64 sm:max-h-96 overflow-y-auto border border-gray-200 rounded-xl p-4 bg-gray-50/30" id="usersList">
                                    <?php foreach ($usuarios as $index => $user): ?>
                                        <div class="card-enhanced p-4 rounded-xl border-2 user-card cursor-pointer transition-all duration-300 hover:bg-gray-50 hover:border-gray-300 <?php echo $selected_user_id == $user['id'] ? 'bg-gradient-to-r from-primary/10 to-secondary/10 border-primary shadow-lg shadow-primary/20 ring-2 ring-primary/20' : ''; ?>" data-user-id="<?php echo htmlspecialchars($user['id']); ?>" data-nome="<?php echo strtolower(htmlspecialchars($user['nome'])); ?>" data-email="<?php echo strtolower(htmlspecialchars($user['email'])); ?>" data-setor="<?php echo strtolower(htmlspecialchars($user['nome_setor'])); ?>" onclick="selectUser(<?php echo $index; ?>, <?php echo htmlspecialchars($user['id']); ?>)">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-secondary text-white flex items-center justify-center font-semibold text-sm shadow-md transition-all duration-300 <?php echo $selected_user_id == $user['id'] ? 'ring-2 ring-primary/30 scale-110' : ''; ?>">
                                                    <?php echo htmlspecialchars($user['nome'][0]); ?>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="font-semibold text-dark text-sm <?php echo $selected_user_id == $user['id'] ? 'text-primary' : ''; ?>"><?php echo htmlspecialchars($user['nome']); ?></div>
                                                    <div class="text-xs text-gray-600"><?php echo htmlspecialchars($user['email']); ?></div>
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <span class="text-xs text-gray-500"><?php echo htmlspecialchars($user['nome_setor']); ?></span>
                                                    </div>
                                                </div>
                                                <?php if ($selected_user_id == $user['id']) { ?>
                                                    <div class="flex items-center justify-center w-6 h-6 rounded-full bg-primary text-white">
                                                        <i class="fa-solid fa-check text-xs"></i>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <!-- Permissões -->
                            <div class="xl:col-span-2 flex flex-col">
                                <h4 class="text-lg sm:text-xl font-semibold text-dark mb-4 flex items-center gap-2">
                                    <i class="fa-solid fa-key text-secondary"></i>
                                    Atribuir Permissões
                                </h4>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                                    <!-- Formulário de Permissões -->
                                    <div class="space-y-4">
                                        <button onclick="openAddPermissionModal()" class="w-full px-6 py-3 bg-gradient-to-r from-primary to-dark text-white font-semibold rounded-xl hover:from-primary/90 hover:to-dark/90 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 btn-primary">
                                            <i class="fa-solid fa-plus mr-2"></i>Adicionar Permissão
                                        </button>
                                    </div>
                                    <!-- Permissões Atuais -->
                                    <div class="flex flex-col">
                                        <h5 class="text-sm font-semibold text-dark mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-list-check text-primary"></i>
                                            Permissões Atuais
                                        </h5>
                                        <div id="currentPermissions" class="space-y-2 max-h-48 sm:max-h-64 overflow-y-auto">
                                            <?php if (empty($permissions)): ?>
                                                <div class="text-center py-8">
                                                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                                        <i class="fa-solid fa-shield-halved text-2xl text-gray-400"></i>
                                                    </div>
                                                    <p class="text-gray-500 text-sm font-medium">Nenhuma permissão atribuída</p>
                                                    <p class="text-gray-400 text-xs mt-1">Adicione permissões para o usuário</p>
                                                </div>
                                            <?php else: ?>
                                                <?php foreach ($permissions as $perm): ?>
                                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-accent/30 to-white rounded-xl border border-accent/50 shadow-sm hover:shadow-md transition-all duration-300">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white shadow-md">
                                                                <i class="fa-solid fa-key text-sm"></i>
                                                            </div>
                                                            <div>
                                                                <div class="font-semibold text-dark text-sm"><?php echo htmlspecialchars($perm['nome_sistema']); ?></div>
                                                                <div class="text-xs text-gray-600"><?php echo htmlspecialchars($perm['tipo_usuario']); ?></div>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <button data-perm-id="<?php echo htmlspecialchars($perm['id']); ?>" data-sistema-id="<?php echo htmlspecialchars($perm['id_sistemas']); ?>" data-tipo-permissao-id="<?php echo htmlspecialchars($perm['id_tipos_usuarios']); ?>" onclick="openEditPermissionModal(this)" class="p-2 rounded-lg text-gray-600 hover:bg-primary hover:text-white hover:border-primary border border-gray-200 transition-all duration-200 group">
                                                                <i class="fa-solid fa-pen text-sm group-hover:scale-110 transition-transform"></i>
                                                            </button>
                                                            <button onclick="openDeletePermissionModal(<?php echo $perm['id']; ?>, '<?php echo htmlspecialchars($perm['nome_sistema'] . ' - ' . $perm['tipo_usuario'], ENT_QUOTES, 'UTF-8'); ?>')" class="p-2 rounded-lg text-red-600 hover:bg-red-50 hover:text-red-700 transition-all duration-200 group">
                                                                <i class="fa-solid fa-trash text-sm group-hover:scale-110 transition-transform"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <!-- Modal Adicionar Permissão -->
    <div id="modalAddPermission" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-40">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="modalAddPermissionContent">
            <div class="p-6 sm:p-8 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary to-dark text-white flex items-center justify-center">
                        <i class="fa-solid fa-key text-xl"></i>
                    </div>
                    <div>
                        <h3 id="modalAddTitle" class="text-xl sm:text-2xl font-bold text-dark font-heading">Adicionar Permissão</h3>
                        <p class="text-gray-600 text-sm">Selecione o sistema e o tipo de usuário</p>
                    </div>
                </div>
                <button class="absolute top-6 right-6 p-2 rounded-xl hover:bg-gray-100 transition-all group" onclick="closeModal('modalAddPermission')">
                    <i class="fa-solid fa-xmark text-gray-400 text-lg group-hover:text-gray-600 group-hover:scale-110 transition-all"></i>
                </button>
            </div>
            <div class="p-6 sm:p-8">
                <form id="addPermissionForm" action="../controllers/controller_permissoes.php" method="POST">
                    <input type="hidden" id="addUserId" name="user_id" value="<?php echo htmlspecialchars($selected_user_id ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-dark mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-desktop text-primary"></i>
                                Sistema *
                            </label>
                            <select id="addSistema" name="sistema" class="input-enhanced w-full px-4 py-4 rounded-xl transition-all text-base border-2 focus:border-primary focus:ring-4 focus:ring-primary/10" required>
                                <option value="">Selecione um sistema</option>
                                <?php 
                                $dados = $select->listar_sistemas();
                                foreach ($dados as $dado) {
                                ?>
                                <option value="<?php echo $dado['id']; ?>"><?php echo htmlspecialchars($dado['nome']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-dark mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-user-tag text-secondary"></i>
                                Tipo de Usuário *
                            </label>
                            <select id="addTipoPermissao" name="tipo_permissao" class="input-enhanced w-full px-4 py-4 rounded-xl transition-all text-base border-2 focus:border-secondary focus:ring-4 focus:ring-secondary/10" required>
                                <option value="">Selecione o tipo</option>
                                <?php 
                                $dados = $select->listar_tipos_usuarios();
                                foreach ($dados as $dado) {
                                ?>
                                <option value="<?php echo $dado['id']; ?>"><?php echo htmlspecialchars($dado['tipo']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="p-6 sm:p-8 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                        <button type="button" class="px-6 py-3 rounded-xl border-2 border-gray-300 font-semibold text-gray-700 hover:bg-gray-100 hover:border-gray-400 transition-all text-base" onclick="closeModal('modalAddPermission')">
                            <i class="fa-solid fa-times mr-2"></i>Cancelar
                        </button>
                        <button type="submit" name="action" value="add" class="px-6 py-3 bg-gradient-to-r from-primary to-dark text-white font-semibold rounded-xl hover:from-primary/90 hover:to-dark/90 transition-all text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fa-solid fa-save mr-2"></i>Adicionar Permissão
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Editar Permissão -->
    <div id="modalEditPermission" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-40">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="modalEditPermissionContent">
            <div class="p-6 sm:p-8 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary to-dark text-white flex items-center justify-center">
                        <i class="fa-solid fa-key text-xl"></i>
                    </div>
                    <div>
                        <h3 id="modalEditTitle" class="text-xl sm:text-2xl font-bold text-dark font-heading">Editar Permissão</h3>
                        <p class="text-gray-600 text-sm">Atualize as informações da permissão</p>
                    </div>
                </div>
                <button class="absolute top-6 right-6 p-2 rounded-xl hover:bg-gray-100 transition-all group" onclick="closeModal('modalEditPermission')">
                    <i class="fa-solid fa-xmark text-gray-400 text-lg group-hover:text-gray-600 group-hover:scale-110 transition-all"></i>
                </button>
            </div>
            <div class="p-6 sm:p-8">
                <form id="editPermissionForm" action="../controllers/controller_permissoes.php" method="POST">
                    <input type="hidden" id="editUserId" name="user_id" value="">
                    <input type="hidden" id="editPermId" name="perm_id" value="">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-dark mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-desktop text-primary"></i>
                                Sistema *
                            </label>
                            <select id="editSistema" name="sistema" class="input-enhanced w-full px-4 py-4 rounded-xl transition-all text-base border-2 focus:border-primary focus:ring-4 focus:ring-primary/10" required>
                                <option value="">Selecione um sistema</option>
                                <?php 
                                $dados = $select->listar_sistemas();
                                foreach ($dados as $dado) {
                                ?>
                                <option value="<?php echo $dado['id']; ?>"><?php echo htmlspecialchars($dado['nome']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-dark mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-user-tag text-secondary"></i>
                                Tipo de Usuário *
                            </label>
                            <select id="editTipoPermissao" name="tipo_permissao" class="input-enhanced w-full px-4 py-4 rounded-xl transition-all text-base border-2 focus:border-secondary focus:ring-4 focus:ring-secondary/10" required>
                                <option value="">Selecione o tipo</option>
                                <?php 
                                $dados = $select->listar_tipos_usuarios();
                                foreach ($dados as $dado) {
                                ?>
                                <option value="<?php echo $dado['id']; ?>"><?php echo htmlspecialchars($dado['tipo']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="p-6 sm:p-8 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                        <button type="button" class="px-6 py-3 rounded-xl border-2 border-gray-300 font-semibold text-gray-700 hover:bg-gray-100 hover:border-gray-400 transition-all text-base" onclick="closeModal('modalEditPermission')">
                            <i class="fa-solid fa-times mr-2"></i>Cancelar
                        </button>
                        <button type="submit" name="action" value="edit" class="px-6 py-3 bg-gradient-to-r from-primary to-dark text-white font-semibold rounded-xl hover:from-primary/90 hover:to-dark/90 transition-all text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fa-solid fa-save mr-2"></i>Salvar Permissão
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal de Confirmação de Exclusão -->
    <div id="modalDeletePermission" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modalDeletePermissionContent">
            <div class="p-6 sm:p-8 text-center">
                <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-exclamation-triangle text-3xl text-red-500"></i>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-dark font-heading mb-4">Confirmar Exclusão</h3>
                <p class="text-gray-600 text-base mb-6 leading-relaxed">
                    Tem certeza que deseja remover a permissão <span class="font-semibold text-dark" id="deletePermissionName"></span>?
                </p>
                <p class="text-sm text-red-600 bg-red-50 px-4 py-3 rounded-lg border border-red-200 mb-6">
                    <i class="fa-solid fa-info-circle mr-2"></i>
                    Esta ação não pode ser undone.
                </p>
                <form id="deletePermissionForm" action="../controllers/controller_permissoes.php" method="POST">
                    <input type="hidden" id="deleteUserId" name="user_id" value="">
                    <input type="hidden" id="deletePermId" name="perm_id" value="">
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button type="button" class="px-6 py-3 rounded-xl border-2 border-gray-300 font-semibold text-gray-700 hover:bg-gray-100 hover:border-gray-400 transition-all text-base" onclick="closeModal('modalDeletePermission')">
                            <i class="fa-solid fa-times mr-2"></i>Cancelar
                        </button>
                        <button type="submit" name="action" value="delete" class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-xl hover:from-red-600 hover:to-red-700 transition-all text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fa-solid fa-trash mr-2"></i>Remover Permissão
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        const usuarios = <?php echo json_encode($usuarios); ?>;
        let selectedUserId = <?php echo json_encode($selected_user_id); ?>;

        function logout() {
            const confirmDialog = confirm('🚪 Deseja sair do sistema CREDE?');
            if (confirmDialog) {
                document.body.style.opacity = '0.7';
                document.body.style.pointerEvents = 'none';
                setTimeout(() => {
                    window.location.href = '../views/subsystems.php';
                }, 500);
            }
        }

        function showNotification(message, type = 'info') {
            const existingNotification = document.querySelector('.notification');
            if (existingNotification) {
                existingNotification.remove();
            }
            const notification = document.createElement('div');
            notification.className = `notification fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-lg transform transition-all duration-300 notification-enter ${
                type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                'bg-blue-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="fa-solid ${
                        type === 'success' ? 'fa-check-circle' :
                        type === 'error' ? 'fa-exclamation-circle' :
                        'fa-info-circle'
                    }"></i>
                    <span class="font-medium">${message}</span>
                </div>
            `;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.classList.remove('notification-enter');
            }, 100);
            setTimeout(() => {
                notification.classList.add('notification-exit');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            }, 4000);
        }

        function selectUser(index, userId) {
            selectedUserId = userId;
            // Redirect to the same page with user_id query parameter
            window.location.href = `permissoes.php?user_id=${encodeURIComponent(userId)}`;
        }

        function openAddPermissionModal() {
            if (selectedUserId === null) {
                showNotification('Selecione um usuário primeiro.', 'error');
                return;
            }
            document.getElementById('addUserId').value = selectedUserId;
            document.getElementById('addSistema').value = '';
            document.getElementById('addTipoPermissao').value = '';
            openModal('modalAddPermission');
        }

        function openEditPermissionModal(button) {
            if (selectedUserId === null) {
                showNotification('Selecione um usuário primeiro.', 'error');
                return;
            }
            const permId = button.getAttribute('data-perm-id');
            const sistemaId = button.getAttribute('data-sistema-id');
            const tipoPermissaoId = button.getAttribute('data-tipo-permissao-id');

            document.getElementById('editUserId').value = selectedUserId;
            document.getElementById('editPermId').value = permId;
            document.getElementById('editSistema').value = sistemaId;
            document.getElementById('editTipoPermissao').value = tipoPermissaoId;
            openModal('modalEditPermission');
        }

        function openDeletePermissionModal(permId, permissionName) {
            document.getElementById('deleteUserId').value = selectedUserId;
            document.getElementById('deletePermId').value = permId;
            document.getElementById('deletePermissionName').textContent = permissionName;
            openModal('modalDeletePermission');
        }

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
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

        function applyFilters() {
            const q = document.getElementById('searchUser').value.toLowerCase();
            const cards = document.querySelectorAll('.user-card');
            let count = 0;
            cards.forEach(card => {
                const nome = card.dataset.nome;
                const email = card.dataset.email;
                const setor = card.dataset.setor;
                const matchesQuery = (nome.includes(q) || email.includes(q) || setor.includes(q));
                if (matchesQuery) {
                    card.style.display = '';
                    count++;
                } else {
                    card.style.display = 'none';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            applyFilters();
            document.getElementById('searchUser').addEventListener('input', applyFilters);
            document.getElementById('addPermissionForm').addEventListener('submit', function(e) {
                if (!this.addSistema.value || !this.addTipoPermissao.value) {
                    e.preventDefault();
                    showNotification('Selecione o sistema e o tipo de usuário.', 'error');
                }
            });
            document.getElementById('editPermissionForm').addEventListener('submit', function(e) {
                if (!this.editSistema.value || !this.editTipoPermissao.value) {
                    e.preventDefault();
                    showNotification('Selecione o sistema e o tipo de usuário.', 'error');
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
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const openModals = document.querySelectorAll('[id^="modal"]:not(.hidden)');
                    openModals.forEach(modal => {
                        if (!modal.classList.contains('hidden')) {
                            closeModal(modal.id);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>