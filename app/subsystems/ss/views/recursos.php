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
require_once(__DIR__ . '/../models/model.admin.php');
$admin = new admin($escola);

$cursos = $select->select_cursos();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['voltar_pendente'])) {
    $id_recurso = $_POST['id_recurso'];

    if ($id_recurso) {
        $result = $admin->recurso_motivo_pendente((int)$id_recurso);
        if ($result === 1) {
            header('Location: recursos.php?tab=tab-pendentes');
            exit();
        } else {
            header('Location: recursos.php?erro=1');
            exit();
        }
    }
}

// Processar formulário de resposta de recurso (agora com modal)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['responder_recurso'])) {
    $id_recurso = $_POST['id_recurso'];
    $resposta = $_POST['resposta'];
    $tipo_resposta = $_POST['tipo_resposta'];

    if ($id_recurso && !empty($resposta)) {
        $result = $admin->responder_recurso((int)$id_recurso, $resposta, $tipo_resposta);
        if ($result === 1) {
            header('Location: recursos.php?sucesso=1');
            exit();
        } else {
            header('Location: recursos.php?erro=1');
            exit();
        }
    }
}

// Processar formulário de cadastro de recurso (antigo)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar_recurso'])) {
    $id_candidato = $_POST['id_candidato'] ?? null;
    $texto_recurso = $_POST['texto_recurso'] ?? '';
    $id_usuario = $_SESSION['id'] ?? null;

    if ($id_candidato && !empty($texto_recurso) && $id_usuario) {
        $result = $admin->cadastrar_recurso((int)$id_candidato, (int)$id_usuario, $texto_recurso);
        if ($result === 1) {
            header('Location: recursos.php?sucesso=1');
            exit();
        } else {
            header('Location: recursos.php?erro=1');
            exit();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Recursos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="icon" type="image/png" href="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/pt-BR.js"></script>
    <style>
        *:focus {
            outline: none !important;
        }

        .tab-button:focus,
        .border-b:focus {
            outline: none !important;
            box-shadow: none !important;
        }
    </style>
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

        :root {
            --primary: #005A24;
            --secondary: #FFA500;
            --accent: #E6F4EA;
            --dark: #1A3C34;
            --light: #F8FAF9;
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

        .nav-item {
            position: relative;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border-radius: 12px;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--secondary);
            border-radius: 0 4px 4px 0;
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .nav-item:hover::before {
            transform: scaleY(1);
        }

        .nav-item:hover {
            transform: translateX(8px);
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar {
            z-index: 50;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 100vw;
            max-width: 20rem;
        }

        .overlay {
            z-index: 45;
        }

        @media (min-width: 1024px) {
            .sidebar {
                width: 20rem;
                position: static;
                flex-shrink: 0;
            }

            .main-content {
                flex: 1;
                min-width: 0;
                margin-left: 0;
                overflow-x: hidden;
            }

            body {
                overflow-x: hidden;
            }
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

        .select2-container .select2-selection--single {
            height: 3rem;
            border: 2px solid rgba(0, 90, 36, 0.25);
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            background-color: #ffffff;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .select2-container--default .select2-selection--single:hover {
            border-color: rgba(0, 90, 36, 0.45);
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 3rem;
            padding-left: 1rem;
            color: #111827;
            font-size: 1rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 3rem;
            right: 0.75rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: var(--primary) transparent transparent transparent;
        }

        .select2-container--default .select2-selection--single .select2-selection__clear {
            color: #6b7280;
            font-size: 1.25rem;
            margin-right: 0.25rem;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0, 90, 36, 0.12);
        }

        .select2-container--default .select2-dropdown {
            border: 1px solid rgba(0, 90, 36, 0.2);
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.12);
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid rgba(0, 90, 36, 0.25);
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            outline: none;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 90, 36, 0.12);
        }

        .select2-container--default .select2-results__option {
            padding: 0.6rem 0.9rem;
            font-size: 0.95rem;
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: rgba(0, 90, 36, 0.08);
            color: var(--primary);
        }

        .select2-container--default .select2-results__option--selected {
            background-color: rgba(0, 90, 36, 0.14);
            color: var(--dark);
        }

        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0, 90, 36, 0.1);
        }

        /* Estilos para o modal */
        .modal {
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .modal.show {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }

        .modal.show .modal-content {
            transform: scale(1);
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen font-body">
    <!-- Modal para resposta de recurso -->
    <div id="modalResposta" class="modal fixed inset-0 z-50 overflow-y-auto">
        <div class="modal-overlay absolute inset-0 bg-black/50"></div>
        <div class="modal-container min-h-screen px-4 text-center flex items-center justify-center">
            <div class="modal-content bg-white rounded-2xl shadow-2xl max-w-md w-full mx-auto p-6 relative">
                <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-primary to-secondary rounded-t-2xl"></div>

                <div class="text-center mb-6 pt-4">
                    <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-primary to-dark flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900" id="modalTitulo">Responder Recurso</h3>
                    <p class="text-gray-600 mt-2" id="modalSubtitulo">Digite a resposta para o recurso</p>
                </div>

                <form id="formResposta" action="recursos.php" method="post">
                    <input type="hidden" name="id_recurso" id="modalIdRecurso">
                    <input type="hidden" name="tipo_resposta" id="modalTipoResposta">

                    <div class="mb-6">
                        <label for="resposta" class="block text-sm font-medium text-gray-700 mb-2">
                            Resposta *
                        </label>
                        <textarea
                            name="resposta"
                            id="resposta"
                            rows="6"
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm resize-none"
                            placeholder="Digite a justificativa da resposta..."
                            required></textarea>
                    </div>

                    <div class="flex gap-3">
                        <button
                            type="button"
                            onclick="fecharModal()"
                            class="flex-1 bg-gray-100 text-gray-700 px-4 py-3 rounded-xl hover:bg-gray-200 transition-all font-medium">
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            name="responder_recurso"
                            class="flex-1 bg-gradient-to-r from-primary to-dark text-white px-4 py-3 rounded-xl hover:from-dark hover:to-primary transition-all font-medium">
                            Enviar Resposta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="overlay" class="overlay fixed inset-0 bg-black/30 z-40 lg:hidden"></div>
    <div class="flex h-screen bg-gray-50 overflow-hidden">
        <?php include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-1 h-screen overflow-y-auto custom-scrollbar">
            <header class="bg-white shadow-sm border-b border-gray-200 z-30 sticky top-0">
                <div class="px-3 sm:px-4 md:px-6 lg:px-8 py-3 sm:py-4">
                    <div class="flex items-center justify-between">
                        <button id="openSidebar" class="text-primary lg:hidden p-2 sm:p-3 rounded-xl hover:bg-accent">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <div class="flex items-center space-x-2 sm:space-x-4 lg:ml-auto">
                            <div class="hidden sm:block text-right">
                                <p class="text-xs sm:text-sm font-semibold text-gray-900">Bem-vindo,</p>
                                <p class="text-xs sm:text-sm text-primary font-medium"><?= $_SESSION['nome'] ?? 'Usuário' ?></p>
                            </div>
                            <a href="../../main/views/perfil.php" title="Perfil" class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-primary to-dark rounded-full flex items-center justify-center hover:brightness-95">
                                <span class="text-white font-bold text-xs sm:text-sm"><?= strtoupper(substr($_SESSION['nome'] ?? 'U', 0, 1)) ?></span>
                            </a>
                            <a href="../models/sessions.php?sair" class="bg-primary text-white px-3 sm:px-4 md:px-6 py-2 sm:py-3 rounded-xl hover:bg-dark font-semibold shadow-lg text-xs sm:text-sm">
                                <span class="hidden sm:inline">Sair</span>
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-4 sm:p-6 lg:p-8 max-w-none mx-auto">
                <?php if (isset($_GET['sucesso'])) { ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        Operação realizada com sucesso!
                    </div>
                <?php } ?>

                <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                    <!-- Formulário de Novo Recurso -->
                    <div class="bg-white rounded-2xl shadow-xl border-0 overflow-hidden mb-8">
                        <div class="h-2 w-full bg-gradient-to-r from-primary to-secondary"></div>
                        <div class="p-12">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary to-dark text-white flex items-center justify-center">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-primary font-display">Novo Recurso</h2>
                                    <p class="text-gray-600 text-base">Registre um novo recurso</p>
                                </div>
                            </div>

                            <form action="recursos.php" method="post" class="space-y-8">
                                <input type="hidden" name="id_usuario" value="<?= $_SESSION['id'] ?>">
                                <div>
                                    <label class="block text-base font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Selecionar Candidato *
                                    </label>
                                    <select id="selectCandidato" name="id_candidato" class="w-full" required>
                                        <option value="" selected disabled>Selecionar candidato</option>
                                        <?php
                                        $candidatos = $select->select_candidatos_ativos_recursos();
                                        foreach ($candidatos as $candidato) { ?>
                                            <option value="<?= $candidato['id'] ?>"><?= htmlspecialchars($candidato['nome']) ?> | <?= htmlspecialchars($candidato['nome_curso']) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-base font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Descrição do Recurso *
                                    </label>
                                    <textarea name="texto_recurso" class="w-full px-4 py-4 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-base resize-none" rows="8" placeholder="Descreva detalhadamente o recurso" required></textarea>
                                </div>

                                <div class="pt-4">
                                    <button type="submit" name="cadastrar_recurso" class="w-full bg-gradient-to-r from-primary to-dark text-white px-6 py-4 rounded-xl hover:from-dark hover:to-primary font-semibold shadow-lg text-base">
                                        <span class="flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                            </svg>
                                            Registrar Recurso
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php } ?>

                <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                    <!-- Abas de Recursos -->
                    <div class="bg-white rounded-2xl shadow-xl border-0 overflow-hidden">
                        <div class="h-2 w-full bg-gradient-to-r from-primary to-secondary"></div>
                        <div class="p-6">
                            <?php
                            $recursos_pendentes = $select->select_recursos_pendentes();
                            $recursos_deferidos = $select->select_recursos_deferidos();
                            $recursos_nao_deferidos = $select->select_recursos_nao_deferidos();
                            $total_pendentes = count($recursos_pendentes);
                            $total_deferidos = count($recursos_deferidos);
                            $total_nao_deferidos = count($recursos_nao_deferidos);
                            ?>

                            <!-- Abas de navegação -->
                            <div class="mb-6">
                                <div class="flex border-b border-gray-200">
                                    <button data-tab="tab-pendentes" class="tab-button py-3 px-6 border-b-2 border-secondary text-secondary font-semibold flex items-center">
                                        Pendentes
                                        <?php if ($total_pendentes > 0) { ?>
                                            <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-semibold text-white bg-yellow-500 rounded-full"><?= $total_pendentes ?></span>
                                        <?php } ?>
                                    </button>
                                    <button data-tab="tab-deferidos" class="tab-button py-3 px-6 border-b-2 border-transparent text-gray-500 hover:text-gray-700 flex items-center">
                                        Deferidos
                                        <?php if ($total_deferidos > 0) { ?>
                                            <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-semibold text-white bg-green-500 rounded-full"><?= $total_deferidos ?></span>
                                        <?php } ?>
                                    </button>
                                    <button data-tab="tab-nao-deferidos" class="tab-button py-3 px-6 border-b-2 border-transparent text-gray-500 hover:text-gray-700 flex items-center">
                                        Indeferidos
                                        <?php if ($total_nao_deferidos > 0) { ?>
                                            <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-semibold text-white bg-red-500 rounded-full"><?= $total_nao_deferidos ?></span>
                                        <?php } ?>
                                    </button>
                                </div>
                            </div>

                            <!-- Conteúdo das abas -->
                            <div class="grid grid-cols-1 gap-6 w-full">
                                <!-- Aba Pendentes -->
                                <div id="tab-pendentes" class="tab-content">
                                    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                                        <?php
                                        if (empty($recursos_pendentes)) {
                                            echo '<div class="col-span-full flex items-center justify-center p-12 bg-gray-50 rounded-xl">
                                                    <div class="text-center">
                                                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                        <p class="text-lg text-gray-500 font-medium">Nenhum recurso pendente</p>
                                                    </div>
                                                  </div>';
                                        } else {
                                            foreach ($recursos_pendentes as $recurso) { ?>
                                                <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-yellow-400 hover:shadow-lg transition-all duration-300">
                                                    <div class="p-5">
                                                        <div class="flex justify-between items-start mb-4">
                                                            <div>
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mb-2">
                                                                    Pendente
                                                                </span>
                                                                <h4 class="font-semibold text-gray-900"><?= htmlspecialchars($recurso['nome'] ?? 'Candidato Desconhecido') ?></h4>
                                                            </div>
                                                        </div>
                                                        <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                                            <h5 class="font-medium text-gray-700 mb-2">Descrição:</h5>
                                                            <p class="text-sm text-gray-600 whitespace-pre-line"><?= htmlspecialchars($recurso['texto'] ?? '') ?></p>
                                                        </div>
                                                        <div class="flex gap-2">
                                                            <button
                                                                type="button"
                                                                onclick="abrirModalResponder(<?= $recurso['id_recurso'] ?>, 'INDEFERIDO', '<?= htmlspecialchars(addslashes($recurso['nome'] ?? 'Candidato')) ?>')"
                                                                class="flex-1 bg-white border border-red-500 text-red-500 px-3 py-2 rounded-lg hover:bg-red-50 transition-all font-medium text-sm">
                                                                Não Deferir
                                                            </button>
                                                            <button
                                                                type="button"
                                                                onclick="abrirModalResponder(<?= $recurso['id_recurso'] ?>, 'DEFERIDO', '<?= htmlspecialchars(addslashes($recurso['nome'] ?? 'Candidato')) ?>')"
                                                                class="flex-1 bg-primary text-white px-3 py-2 rounded-lg hover:bg-primary/90 transition-all font-medium text-sm">
                                                                Deferir
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                        <?php }
                                        } ?>
                                    </div>
                                </div>

                                <!-- Aba Deferidos -->
                                <div id="tab-deferidos" class="tab-content hidden">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                        <?php
                                        if (empty($recursos_deferidos)) {
                                            echo '<div class="col-span-full flex items-center justify-center p-12 bg-gray-50 rounded-xl">
                                                    <div class="text-center">
                                                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                        <p class="text-lg text-gray-500 font-medium">Nenhum recurso deferido</p>
                                                    </div>
                                                  </div>';
                                        } else {
                                            foreach ($recursos_deferidos as $recurso) { ?>
                                                <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-green-500 hover:shadow-lg transition-all duration-300">
                                                    <div class="p-5">
                                                        <div class="flex justify-between items-start mb-4">
                                                            <div>
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mb-2">
                                                                    Deferido
                                                                </span>
                                                                <h4 class="font-semibold text-gray-900"><?= htmlspecialchars($recurso['nome'] ?? 'Candidato Desconhecido') ?></h4>
                                                            </div>
                                                        </div>
                                                        <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                                            <h5 class="font-medium text-gray-700 mb-2">Descrição:</h5>
                                                            <p class="text-sm text-gray-600 whitespace-pre-line"><?= htmlspecialchars($recurso['texto'] ?? '') ?></p>
                                                        </div>
                                                        <?php if ($recurso['resposta'] ?? '') { ?>
                                                            <div class="bg-green-50 p-4 rounded-lg mb-4 border border-green-200">
                                                                <h5 class="font-medium text-green-700 mb-2">Resposta:</h5>
                                                                <p class="text-sm text-green-600 whitespace-pre-line"><?= htmlspecialchars($recurso['resposta'] ?? '') ?></p>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="flex gap-2">
                                                            <form action="recursos.php" method="post">
                                                                <input type="hidden" name="id_recurso" value="<?= $recurso['id_recurso'] ?>">
                                                                <button type="submit" name="voltar_pendente" class="flex-1 bg-white border border-yellow-500 text-yellow-500 px-3 py-2 rounded-lg hover:bg-yellow-50 transition-all font-medium text-sm">
                                                                    Voltar Pendente
                                                                </button>
                                                            </form>
                                                            <button
                                                                type="button"
                                                                onclick="abrirModalResponder(<?= $recurso['id_recurso'] ?>, 'INDEFERIDO', '<?= htmlspecialchars(addslashes($recurso['nome'] ?? 'Candidato')) ?>')"
                                                                class="flex-1 bg-white border border-red-500 text-red-500 px-3 py-2 rounded-lg hover:bg-red-50 transition-all font-medium text-sm">
                                                                Indeferir
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                        <?php }
                                        } ?>
                                    </div>
                                </div>

                                <!-- Aba Indeferidos -->
                                <div id="tab-nao-deferidos" class="tab-content hidden">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                        <?php
                                        if (empty($recursos_nao_deferidos)) {
                                            echo '<div class="col-span-full flex items-center justify-center p-12 bg-gray-50 rounded-xl">
                                                    <div class="text-center">
                                                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                        <p class="text-lg text-gray-500 font-medium">Nenhum recurso não deferido</p>
                                                    </div>
                                                  </div>';
                                        } else {
                                            foreach ($recursos_nao_deferidos as $recurso) { ?>
                                                <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-red-500 hover:shadow-lg transition-all duration-300">
                                                    <div class="p-5">
                                                        <div class="flex justify-between items-start mb-4">
                                                            <div>
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mb-2">
                                                                    Indeferido
                                                                </span>
                                                                <h4 class="font-semibold text-gray-900"><?= htmlspecialchars($recurso['nome'] ?? 'Candidato Desconhecido') ?></h4>
                                                            </div>
                                                        </div>
                                                        <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                                            <h5 class="font-medium text-gray-700 mb-2">Descrição:</h5>
                                                            <p class="text-sm text-gray-600 whitespace-pre-line"><?= htmlspecialchars($recurso['texto'] ?? '') ?></p>
                                                        </div>
                                                        <?php if ($recurso['resposta'] ?? '') { ?>
                                                            <div class="bg-red-50 p-4 rounded-lg mb-4 border border-red-200">
                                                                <h5 class="font-medium text-red-700 mb-2">Resposta:</h5>
                                                                <p class="text-sm text-red-600 whitespace-pre-line"><?= htmlspecialchars($recurso['resposta'] ?? '') ?></p>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="flex gap-2">
                                                            <form action="recursos.php" method="post">
                                                                <input type="hidden" name="id_recurso" value="<?= $recurso['id_recurso'] ?>">
                                                                <button type="submit" name="voltar_pendente" class="flex-1 bg-white border border-yellow-500 text-yellow-500 px-3 py-2 rounded-lg hover:bg-yellow-50 transition-all font-medium text-sm">
                                                                    Voltar Pendente
                                                                </button>
                                                            </form>
                                                            <button
                                                                type="button"
                                                                onclick="abrirModalResponder(<?= $recurso['id_recurso'] ?>, 'DEFERIDO', '<?= htmlspecialchars(addslashes($recurso['nome'] ?? 'Candidato')) ?>')"
                                                                class="flex-1 bg-white border border-primary text-primary px-3 py-2 rounded-lg hover:bg-primary/5 transition-all font-medium text-sm">
                                                                Deferir
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                        <?php }
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </main>
        </div>
    </div>

    <script>
        // Funções para o modal (CORRIGIDO - agora abre corretamente)
        function abrirModalResponder(idRecurso, tipoResposta, nomeCandidato) {
            document.getElementById('modalIdRecurso').value = idRecurso;
            document.getElementById('modalTipoResposta').value = tipoResposta;
            document.getElementById('resposta').value = '';

            let titulo, subtitulo;
            switch (tipoResposta) {
                case 'DEFERIDO':
                    titulo = 'Deferir Recurso';
                    subtitulo = 'Digite a justificativa para deferir o recurso de ' + nomeCandidato;
                    break;
                case 'INDEFERIDO':
                    titulo = 'Indeferir Recurso';
                    subtitulo = 'Digite a justificativa para indeferir o recurso de ' + nomeCandidato;
                    break;
                case 'PENDENTE':
                    titulo = 'Voltar para Pendente';
                    subtitulo = 'Digite a justificativa para voltar o recurso de ' + nomeCandidato + ' para pendente';
                    break;
                default:
                    titulo = 'Responder Recurso';
                    subtitulo = 'Digite a resposta para o recurso de ' + nomeCandidato;
            }

            document.getElementById('modalTitulo').textContent = titulo;
            document.getElementById('modalSubtitulo').textContent = subtitulo;

            const modal = document.getElementById('modalResposta');
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';

            // Impede que cliques dentro do conteúdo fechem o modal
            const modalContent = modal.querySelector('.modal-content');
            modalContent.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        function fecharModal() {
            const modal = document.getElementById('modalResposta');
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        // Fechar ao clicar fora (apenas no overlay)
        document.getElementById('modalResposta').addEventListener('click', function(e) {
            if (e.target === this || e.target.classList.contains('modal-overlay')) {
                fecharModal();
            }
        });

        // Fechar com ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('modalResposta').classList.contains('show')) {
                fecharModal();
            }
        });

        // Sidebar functions
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const openSidebar = document.getElementById('openSidebar');
        const closeSidebar = document.getElementById('closeSidebar');

        if (openSidebar) {
            openSidebar.addEventListener('click', () => {
                sidebar.classList.add('open');
                overlay.classList.add('show');
            });
        }
        if (closeSidebar) {
            closeSidebar.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            });
        }
        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            });
        }

        // Sistema de abas
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            const urlParams = new URLSearchParams(window.location.search);
            const tabFromUrl = urlParams.get('tab');

            if (tabFromUrl) {
                const targetButton = document.querySelector(`[data-tab="${tabFromUrl}"]`);
                if (targetButton) {
                    setTimeout(() => {
                        targetButton.click();
                    }, 100);
                }
            }

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const tabId = button.getAttribute('data-tab');

                    tabButtons.forEach(btn => {
                        btn.classList.remove('border-secondary', 'text-secondary', 'border-green-500', 'text-green-700', 'border-red-500', 'text-red-700', 'font-semibold');
                        btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700');
                    });

                    button.classList.add('font-semibold');
                    button.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700');

                    if (tabId === 'tab-pendentes') {
                        button.classList.add('border-secondary', 'text-secondary');
                    } else if (tabId === 'tab-deferidos') {
                        button.classList.add('border-green-500', 'text-green-700');
                    } else if (tabId === 'tab-nao-deferidos') {
                        button.classList.add('border-red-500', 'text-red-700');
                    }

                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });

                    const activeContent = document.getElementById(tabId);
                    if (activeContent) {
                        activeContent.classList.remove('hidden');
                    }
                });
            });
        });

        // Inicializa Select2
        $(document).ready(function() {
            $('#selectCandidato').select2({
                width: '100%',
                placeholder: 'Selecionar candidato',
                allowClear: true,
                language: 'pt-BR'
            });
        });
    </script>
</body>

</html>