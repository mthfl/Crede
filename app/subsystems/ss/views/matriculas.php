<?php
require_once __DIR__ . "/../models/sessions.php";
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once __DIR__ . "/../config/connect.php";
if (!isset($_SESSION["escola"]) || empty($_SESSION["escola"])) {
    die("Escola não definida na sessão");
}
$escola = $_SESSION["escola"];

new connect($escola);
require_once __DIR__ . "/../models/model.select.php";
$select = new select($escola);
require_once __DIR__ . "/../models/model.admin.php";
$admin = new admin($escola);

// Processar formulário de matrícula
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_matricula'])) {
    $dias_matricula = $_POST['dias_matricula'] ?? [];

    if (!empty($dias_matricula)) {
        $sucesso_total = true;
        foreach ($dias_matricula as $dia) {
            $curso_id = $dia['curso_id'] ?? null;
            $data = $dia['data'] ?? '';
            $hora = $dia['hora'] ?? '';

            if (!empty($data) && !empty($hora)) {
                $result = $admin->cadastrar_matricula($curso_id, $data, $hora);
                if ($result !== 1) {
                    $sucesso_total = false;
                }
            }
        }

        if ($sucesso_total) {
            header('Location: matriculas.php?sucesso=1');
            exit();
        } else {
            header('Location: matriculas.php?erro=1');
            exit();
        }
    }
}

// Processar exclusão de matrícula
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['excluir_matricula'])) {
    $id_matricula = $_GET['excluir_matricula'] ?? null;
    if ($id_matricula) {
        $result = $admin->excluir_matricula((int)$id_matricula);
        if ($result === 1) {
            header('Location: matriculas.php?sucesso=1');
            exit();
        } else {
            header('Location: matriculas.php?erro=1');
            exit();
        }
    }
}

// Buscar matrículas existentes
$matriculas = $select->select_matriculas();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Sistema Escolar - Matrículas</title>
    <link rel="icon" type="image/png" href="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
                        success: '#28A745',
                        warning: '#FFC107',
                        danger: '#DC3545',
                        info: '#17A2B8'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Poppins', 'sans-serif'],
                        display: ['Inter', 'system-ui', 'sans-serif'],
                        body: ['Inter', 'system-ui', 'sans-serif']
                    },
                    animation: {
                        'slide-in-left': 'slideInLeft 0.5s ease-out',
                        'fade-in-up': 'fadeInUp 0.6s ease-out',
                        'scale-in': 'scaleIn 0.4s ease-out',
                        'pulse-soft': 'pulseSoft 2s ease-in-out infinite',
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

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
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

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes pulseSoft {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }
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

        .btn-animate {
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .btn-animate::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.3s, height 0.3s;
        }

        .btn-animate:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-animate:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .btn-animate:active {
            transform: translateY(0);
        }

        .focus-ring:focus {
            outline: 2px solid var(--secondary);
            outline-offset: 2px;
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

        #sidebar nav > :not([hidden]) ~ :not([hidden]) {
            margin-top: 0.25rem !important;
        }

        #sidebar .nav-item {
            padding: 0.5rem 0.75rem !important;
        }

        #sidebar .nav-item span {
            font-size: 0.875rem !important;
            line-height: 1.25rem !important;
        }

        #sidebar .nav-item p {
            display: none !important;
        }

        #sidebar .nav-item > div:first-child {
            width: 2.5rem !important;
            height: 2.5rem !important;
            margin-right: 0.75rem !important;
        }

        #sidebar .nav-item svg {
            width: 1.25rem !important;
            height: 1.25rem !important;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #d1d5db;
            border-radius: 0.75rem;
            height: 3rem;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single:hover {
            border-color: #9ca3af;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #374151;
            font-size: 0.875rem;
            line-height: 1.25rem;
            padding-left: 0.75rem;
            padding-right: 2rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 3rem;
            right: 0.75rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #005A24 transparent transparent transparent;
        }

        .select2-container--default .select2-selection--single .select2-selection__clear {
            color: #6b7280;
            font-size: 1.25rem;
            margin-right: 0.25rem;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #005A24;
            box-shadow: 0 0 0 4px rgba(0, 90, 36, 0.1);
        }

        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #005A24;
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
            border-color: #005A24;
            box-shadow: 0 0 0 3px rgba(0, 90, 36, 0.12);
        }

        .select2-container--default .select2-results__option {
            padding: 0.6rem 0.9rem;
            font-size: 0.95rem;
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: rgba(0, 90, 36, 0.08);
            color: #005A24;
        }

        .select2-container--default .select2-results__option--selected {
            background-color: rgba(0, 90, 36, 0.14);
            color: #1A3C34;
        }

        .horario-item {
            animation: slideInUp 0.3s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-white min-h-screen font-body">
    <div id="overlay" class="overlay fixed inset-0 bg-black/30 z-40 lg:hidden"></div>
    <div class="flex h-screen bg-gray-50 overflow-hidden">
        <!-- Sidebar -->
       <?php include __DIR__ . '/partials/sidebar.php'; ?>
        <!-- Main Content -->
        <div class="main-content flex-1 h-screen overflow-y-auto custom-scrollbar bg-white">
            <header class="bg-white shadow-sm border-b border-gray-200 z-30 sticky top-0">
                <div class="px-3 sm:px-4 md:px-6 lg:px-8 py-3 sm:py-4">
                    <div class="flex items-center justify-between">
                        <button id="openSidebar" class="text-primary lg:hidden btn-animate p-2 sm:p-3 rounded-xl hover:bg-accent focus-ring">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        <div class="flex items-center space-x-2 sm:space-x-4 lg:ml-auto">
                            <div class="hidden sm:block text-right">
                                <p class="text-xs sm:text-sm font-semibold text-gray-900">Bem-vindo,</p>
                                <p class="text-xs sm:text-sm text-primary font-medium"><?= $_SESSION["nome"] ?? "Usuário" ?></p>
                            </div>
                            <a href="../../main/views/perfil.php" title="Perfil" class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-primary to-dark rounded-full flex items-center justify-center hover:brightness-95 focus-ring">
                                <span class="text-white font-bold text-xs sm:text-sm"><?= strtoupper(substr($_SESSION["nome"] ?? "U", 0, 1)) ?></span>
                            </a>
                            <a href="../models/sessions.php?sair" class="bg-primary text-white px-3 sm:px-4 md:px-6 py-2 sm:py-3 rounded-xl hover:bg-dark btn-animate font-semibold shadow-lg focus-ring text-xs sm:text-sm">
                                <span class="hidden sm:inline">Sair</span>
                                <i class="fas fa-sign-out-alt sm:hidden"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </header>
            <main class="p-4 sm:p-6 lg:p-8">
                <div class="mb-8">
                    <div class="text-center">
                        <h1 class="text-primary text-3xl md:text-4xl font-bold tracking-tight font-heading">
                            <i class="fas fa-calendar-alt mr-3 text-secondary"></i>
                            Gerenciar Matrículas
                        </h1>
                        <p class="text-gray-600 text-base md:text-lg mt-2 max-w-2xl mx-auto">
                            Configure os dias e horários de matrícula por curso
                        </p>
                        <div class="mt-6 inline-flex text-sm  px-6 py-3 ">
                           
                        </div>
                    </div>
                </div>

                <?php if (isset($_GET['sucesso'])) { ?>
                    <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-800 shadow-sm">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                                <i class="fas fa-check text-green-700"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold">Matrícula(s) adicionada(s) com sucesso!</p>
                                <p class="text-sm text-green-700 mt-1">As configurações foram salvas e já estão válidas.</p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if (isset($_GET['erro'])) { ?>
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                                <i class="fas fa-triangle-exclamation text-red-700"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold">Erro ao processar matrícula(s).</p>
                                <p class="text-sm text-red-700 mt-1">Revise os campos e tente novamente.</p>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="max-w-4xl mx-auto space-y-6">
                    <!-- Lista de Matrículas Existentes -->
                    <?php if (!empty($matriculas)) { ?>
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-200/70 overflow-hidden">
                            <div class="px-6 sm:px-8 py-6 border-b border-gray-100 flex items-center justify-between">
                                <h2 class="text-lg sm:text-xl font-bold text-primary">
                                    <i class="fas fa-list mr-2 text-secondary"></i>
                                    Matrículas Cadastradas
                                </h2>
                                <span class="text-sm text-gray-500">
                                    <?= count($matriculas) ?> registro(s)
                                </span>
                            </div>
                            <div class="divide-y divide-gray-100">
                                <?php foreach ($matriculas as $matricula) { ?>
                                    <div class="px-6 sm:px-8 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 truncate">
                                                <?php if ($matricula['nome_curso']) { ?>
                                                    <?= htmlspecialchars($matricula['nome_curso']) ?>
                                                <?php } else { ?>
                                                    <span class="inline-flex items-center rounded-full bg-secondary/10 text-secondary px-3 py-1 text-xs font-semibold">TODOS OS CURSOS</span>
                                                <?php } ?>
                                            </p>
                                            <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-gray-600">
                                                <span class="inline-flex items-center gap-2 rounded-lg bg-gray-50 border border-gray-200 px-3 py-1">
                                                    <i class="fas fa-calendar text-primary"></i>
                                                    <?= date('d/m/Y', strtotime($matricula['data'])) ?>
                                                </span>
                                                <span class="inline-flex items-center gap-2 rounded-lg bg-gray-50 border border-gray-200 px-3 py-1">
                                                    <i class="fas fa-clock text-primary"></i>
                                                    <?= date('H:i', strtotime($matricula['hora'])) ?>
                                                </span>
                                            </p>
                                        </div>
                                        <a href="?excluir_matricula=<?= $matricula['id'] ?>"
                                            onclick="return confirm('Tem certeza que deseja excluir esta matrícula?')"
                                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 transition-all text-sm font-semibold">
                                            <i class="fas fa-trash"></i>
                                            <span>Excluir</span>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Formulário de Nova Matrícula -->
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200/70 overflow-hidden">
                        <div class="px-6 sm:px-8 py-6 border-b border-gray-100">
                            <h2 class="text-lg sm:text-xl font-bold text-primary">
                                <i class="fas fa-plus-circle mr-2 text-secondary"></i>
                                Nova Configuração
                            </h2>
                            <p class="text-sm text-gray-600 mt-1">Adicione um ou mais dias de matrícula e salve ao final.</p>
                        </div>
                        <form action="../controllers/controller_matricula.php" method="POST" id="formMatricula" class="space-y-6 px-6 sm:px-8 py-6">
                            <div id="dias-matricula-container" class="space-y-6">
                                <!-- Primeiro dia de matrícula -->
                                <div class="dia-matricula-item bg-gray-50 p-6 rounded-2xl border border-gray-200">
                                    <div class="flex items-center justify-between mb-5">
                                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Dia de Matrícula 1</h3>
                                        <button type="button" onclick="removerDiaMatricula(this)" class="px-3 py-2 rounded-xl border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 transition-all text-sm font-semibold">
                                            <i class="fas fa-trash mr-1"></i>
                                            Remover
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="md:col-span-1">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-book mr-2 text-primary"></i>
                                                Curso
                                            </label>
                                            <select name="dias_matricula[0][curso_id]" class="select2-curso w-full" required>
                                                <option value="">SELECIONAR CURSO</option>
                                                <option value="0">TODOS OS CURSOS</option>
                                                <?php
                                                $cursos = $select->select_cursos();
                                                foreach ($cursos as $curso) { ?>
                                                    <option value="<?= htmlspecialchars($curso["id"]) ?>">
                                                        <?= htmlspecialchars($curso["nome_curso"]) ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="md:col-span-1">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-calendar mr-2 text-primary"></i>
                                                Data
                                            </label>
                                            <input type="date" name="dias_matricula[0][data]" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all bg-white">
                                        </div>

                                        <div class="md:col-span-1">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-clock mr-2 text-primary"></i>
                                                Hora
                                            </label>
                                            <input type="time" name="dias_matricula[0][hora]" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all bg-white">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" onclick="adicionarDiaMatricula()" class="w-full px-4 py-3 rounded-xl border-2 border-secondary text-secondary bg-white hover:bg-secondary/10 transition-all font-semibold">
                                <i class="fas fa-plus mr-2"></i>
                                Adicionar dia de matrícula
                            </button>

                            <div class="flex gap-4 pt-4">
                                <button type="submit" name="adicionar_matricula" class="flex-1 bg-gradient-to-r from-primary to-dark text-white py-3 px-6 rounded-xl hover:from-dark hover:to-primary transition-all font-semibold">
                                    <i class="fas fa-save mr-2"></i>
                                    Salvar Matrícula
                                </button>
                                <button type="reset" class="px-6 py-3 rounded-xl border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-all font-semibold">
                                    <i class="fas fa-redo mr-2"></i>
                                    Limpar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        let diaMatriculaCount = 1;

        $(document).ready(function() {
            // Inicializar Select2 para todos os selects existentes
            $('.select2-curso').select2({
                placeholder: 'SELECIONAR CURSO',
                allowClear: true
            });
        });

        function adicionarDiaMatricula() {
            const container = document.getElementById('dias-matricula-container');
            const novoDia = document.createElement('div');
            novoDia.className = 'dia-matricula-item bg-gray-50 p-6 rounded-2xl border border-gray-200 horario-item';
            novoDia.innerHTML = `
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Dia de Matrícula ${diaMatriculaCount + 1}</h3>
                    <button type="button" onclick="removerDiaMatricula(this)" class="px-3 py-2 rounded-xl border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 transition-all text-sm font-semibold">
                        <i class="fas fa-trash mr-1"></i>
                        Remover
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-book mr-2 text-primary"></i>
                            Curso
                        </label>
                        <select name="dias_matricula[${diaMatriculaCount}][curso_id]" class="select2-curso w-full" required>
                            <option value="">SELECIONAR CURSO</option>
                            <option value="0">TODOS OS CURSOS</option>
                            <?php
                            $cursos = $select->select_cursos();
                            foreach ($cursos as $curso) { ?>
                                <option value="<?= htmlspecialchars($curso["id"]) ?>">
                                    <?= htmlspecialchars($curso["nome_curso"]) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar mr-2 text-primary"></i>
                            Data
                        </label>
                        <input type="date" name="dias_matricula[${diaMatriculaCount}][data]" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all bg-white">
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-clock mr-2 text-primary"></i>
                            Hora
                        </label>
                        <input type="time" name="dias_matricula[${diaMatriculaCount}][hora]" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all bg-white">
                    </div>
                </div>
            `;
            container.appendChild(novoDia);
            diaMatriculaCount++;

            // Inicializar Select2 no novo select
            $(novoDia).find('.select2-curso').select2({
                placeholder: 'SELECIONAR CURSO',
                allowClear: true
            });
        }

        function removerDiaMatricula(button) {
            const container = document.getElementById('dias-matricula-container');
            if (container.children.length > 1) {
                const item = button.closest('.dia-matricula-item');
                // Destruir Select2 antes de remover
                $(item).find('.select2-curso').select2('destroy');
                item.remove();
                // Renumerar os dias
                container.querySelectorAll('.dia-matricula-item').forEach((item, index) => {
                    item.querySelector('h3').textContent = `Dia de Matrícula ${index + 1}`;
                });
            } else {
                alert('É necessário ter pelo menos um dia de matrícula!');
            }
        }

        // Sidebar toggle
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
    </script>
</body>

</html>