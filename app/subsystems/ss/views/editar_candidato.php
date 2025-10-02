<?php
require_once(__DIR__ . '/../../ss/models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../config/connect.php');
$escola = $_SESSION['escola'];

new connect($escola);
require_once(__DIR__ . '/../models/model.select.php');
$select = new select($escola);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$candidato = [];
if ($id > 0) {
    $candidato = $select->select_candidato_notas($id) ?: [];
}

$cursoNome = $candidato['nome_curso'] ?? '';
$cursoCor = '#005A24';
try {
    $cursos = $select->select_cursos();
    foreach ($cursos as $curso) {
        if (($curso['nome_curso'] ?? '') === $cursoNome && !empty($curso['cor_curso'])) {
            $cursoCor = $curso['cor_curso'];
            break;
        }
    }
} catch (Exception $e) {
    $cursoCor = '#005A24';
}

function hex2rgba($hex, $alpha = 0.2) {
    $hex = str_replace('#', '', trim($hex));
    if (strlen($hex) === 3) {
        $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
        $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
        $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    $a = is_numeric($alpha) ? max(0, min(1, (float)$alpha)) : 0.2;
    return "rgba($r, $g, $b, $a)";
}

$primary_rgba_01 = hex2rgba($cursoCor, 0.10);
$primary_rgba_015 = hex2rgba($cursoCor, 0.15);
$primary_rgba_02 = hex2rgba($cursoCor, 0.20);

function fmt($v) {
    return htmlspecialchars((string)$v);
}
function simnao($v) {
    return ((int)$v) === 1 ? 'Sim' : 'Não';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Candidato</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '<?= $cursoCor ?>',
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
                        'slide-in-left': 'slideInLeft 0.5s ease-out',
                        'slide-in-right': 'slideInRight 0.5s ease-out',
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
            --primary: <?= $cursoCor ?>;
            --secondary: #FFA500;
            --accent: #E6F4EA;
            --dark: #1A3C34;
            --light: #F8FAF9;
        }

        * {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
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

        .input-focus:focus:not(:disabled) {
            border-color: var(--primary);
            outline: none;
        }

        .input-modern {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .input-modern:focus:not(:disabled) {
            outline: none;
            box-shadow: 0 0 0 3px <?= $primary_rgba_01 ?>, 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transform: translateY(-1px);
        }

        .input-modern:hover:not(:focus):not(:disabled) {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transform: translateY(-1px);
        }

        .input-disabled {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border-color: #e5e7eb;
            color: #6b7280;
            cursor: not-allowed;
        }

        .input-checkbox {
            transition: all 0.2s ease-in-out;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            width: 1rem;
            height: 1rem;
            border: 2px solid #cbd5e1;
            border-radius: 0.25rem;
            background-color: #fff;
            position: relative;
        }

        .input-checkbox:hover:not(:disabled) {
            transform: scale(1.05);
        }

        .input-checkbox:checked:not(:disabled) {
            border-color: var(--primary);
            background-color: var(--primary);
        }

        .input-checkbox:checked:not(:disabled)::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0.45rem;
            height: 0.45rem;
            background: #ffffff;
            transform: translate(-50%, -50%);
            clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 15%, 80% 0, 43% 62%);
        }

        .input-checkbox:focus:not(:disabled) {
            box-shadow: 0 0 0 3px <?= $primary_rgba_015 ?>;
        }

        .input-checkbox:disabled {
            background: #e5e7eb;
            border-color: #d1d5db;
            cursor: not-allowed;
        }

        .compact-table th,
        .compact-table td {
            padding: 0.4rem 0.5rem !important;
        }

        .compact-table input[type="text"] {
            padding-top: 0.3rem !important;
            padding-bottom: 0.3rem !important;
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
            font-size: 0.875rem !important;
            width: 4rem !important;
        }

        input[type="checkbox"] {
            accent-color: var(--primary);
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            max-width: 400px;
            width: 90%;
            transform: scale(0.9);
            transition: transform 0.3s ease;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-overlay.active .modal-content {
            transform: scale(1);
        }

        .modal-icon {
            width: 3rem;
            height: 3rem;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .modal-message {
            color: #6b7280;
            text-align: center;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        .modal-button {
            width: 100%;
            padding: 0.75rem 1rem;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .modal-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen font-body">
    <main class="p-4 sm:p-6 lg:p-8">
        <div class="max-w-6xl mx-auto">
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

            <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                <div class="text-white p-6" style="background: linear-gradient(135deg, <?= $cursoCor ?>, #1A3C34);">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/30 shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold font-display tracking-tight">Editar Candidato</h2>
                            <p class="text-white/90 text-sm mt-1 font-medium">
                                <?= $cursoNome ? 'Curso: ' . fmt($cursoNome) : 'Sistema de Seleção Escolar' ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Display Name and Date of Birth -->
                <div class="p-6 border-b border-gray-200">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Nome</p>
                            <p class="text-lg font-semibold text-gray-800"><?= fmt($candidato['nome'] ?? 'Não informado') ?></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Data de Nascimento</p>
                            <p class="text-lg font-semibold text-gray-800"><?= fmt($candidato['data_nascimento'] ?? 'Não informado') ?></p>
                        </div>
                    </div>
                </div>

                <form action="../controllers/controller_candidato.php" method="post" id="editarForm" class="p-6 space-y-6">
                    <input type="hidden" name="form" value="candidato">
                    <input type="hidden" name="acao" value="update">
                    <input type="hidden" name="id_candidato" value="<?= fmt($id) ?>">
                    <!-- Dados do candidato -->
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Informações Pessoais
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Nome</label>
                                <input type="text" name="nome" value="<?= fmt($candidato['nome'] ?? '') ?>" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl input-modern input-focus input-disabled" style="--tw-ring-color: <?= $cursoCor ?>; --tw-border-opacity: 0.5;" disabled required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Data de Nascimento</label>
                                <input type="text" name="data_nascimento" value="<?= fmt($candidato['data_nascimento'] ?? '') ?>" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl input-modern input-focus input-disabled" style="--tw-ring-color: <?= $cursoCor ?>; --tw-border-opacity: 0.5;" oninput="applyDateMask(this)" disabled required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Tipo de Escola</label>
                                <select name="tipo_escola" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl input-modern input-focus input-disabled" style="--tw-ring-color: <?= $cursoCor ?>; --tw-border-opacity: 0.5;" disabled required>
                                    <?php $isPublica = (int)($candidato['publica'] ?? 0) === 1; ?>
                                    <option value="publica" <?= $isPublica ? 'selected' : '' ?>>Escola Pública</option>
                                    <option value="privada" <?= !$isPublica ? 'selected' : '' ?>>Escola Privada</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="radio" id="pcd" name="status_candidato" value="pcd" class="w-5 h-5 text-primary border-gray-300 rounded input-checkbox input-disabled" style="--tw-ring-color: <?= $cursoCor ?>;" <?= $candidato['pcd'] == 1 ? 'checked' : '' ?> disabled>
                                <label for="pcd" class="text-sm font-medium text-gray-700">PCD</label>
                                
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="radio" id="cotas" name="status_candidato" value="cotas" class="w-5 h-5 text-primary border-gray-300 rounded input-checkbox input-disabled" style="--tw-ring-color: <?= $cursoCor ?>;" <?= $candidato['bairro'] == 1 ? 'checked' : '' ?> disabled>
                                <label for="cotas" class="text-sm font-medium text-gray-700">Cotas</label>
                                
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="radio" id="ampla" name="status_candidato" value="ampla" class="w-5 h-5 text-primary border-gray-300 rounded input-checkbox input-disabled" style="--tw-ring-color: <?= $cursoCor ?>;" <?= ($candidato['pcd'] == 0 && $candidato['bairro'] == 0) ? 'checked' : '' ?> disabled>
                                <label for="ampla" class="text-sm font-medium text-gray-700">Ampla Concorrência</label>
                            </div>
                        </div>
                    </div>

                    <!-- Notas do Ensino Fundamental -->
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Notas do Ensino Fundamental
                        </h3>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <!-- Tabela de notas 6º-8º -->
                            <div>
                                <h4 class="text-lg font-medium text-gray-700 mb-2">6º, 7º e 8º Ano</h4>
                                <div class="overflow-x-auto">
                                    <table class="w-full border-collapse compact-table text-sm">
                                        <thead>
                                            <tr class="bg-gray-100">
                                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-medium text-gray-700">Matéria</th>
                                                <th class="border border-gray-300 px-4 py-2 text-center text-sm font-medium text-gray-700">6º Ano</th>
                                                <th class="border border-gray-300 px-4 py-2 text-center text-sm font-medium text-gray-700">7º Ano</th>
                                                <th class="border border-gray-300 px-4 py-2 text-center text-sm font-medium text-gray-700">8º Ano</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $map = [
                                                ['Português', 'n6_portuguesa', 'n7_portuguesa', 'n8_portuguesa', 'portugues_6', 'portugues_7', 'portugues_8', true],
                                                ['Matemática', 'n6_matematica', 'n7_matematica', 'n8_matematica', 'matematica_6', 'matematica_7', 'matematica_8', true],
                                                ['História', 'n6_historia', 'n7_historia', 'n8_historia', 'historia_6', 'historia_7', 'historia_8', true],
                                                ['Geografia', 'n6_geografia', 'n7_geografia', 'n8_geografia', 'geografia_6', 'geografia_7', 'geografia_8', true],
                                                ['Ciências', 'n6_ciencias', 'n7_ciencias', 'n8_ciencias', 'ciencias_6', 'ciencias_7', 'ciencias_8', true],
                                                ['Inglês', 'n6_inglesa', 'n7_inglesa', 'n8_inglesa', 'ingles_6', 'ingles_7', 'ingles_8', true],
                                                ['Artes', 'n6_artes', 'n7_artes', 'n8_artes', 'artes_6', 'artes_7', 'artes_8', false],
                                                ['Educação Física', 'n6_educacao_fisica', 'n7_educacao_fisica', 'n8_educacao_fisica', 'edfisica_6', 'edfisica_7', 'edfisica_8', false],
                                                ['Religião', 'n6_religiao', 'n7_religiao', 'n8_religiao', 'religiao_6', 'religiao_7', 'religiao_8', false],
                                            ];
                                            foreach ($map as $row) {
                                                [$label, $a6, $a7, $a8, $n6, $n7, $n8, $required] = $row;
                                                echo '<tr class="hover:bg-gray-50">';
                                                echo '<td class="border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700">' . fmt($label) . '</td>';
                                                echo '<td class="border border-gray-300 px-2 py-2 text-center">';
                                                echo '<input type="text" name="' . $n6 . '" value="' . fmt($candidato[$a6] ?? '') . '" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg input-modern input-focus input-disabled text-center text-sm" style="--tw-ring-color: ' . $cursoCor . '; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)"' . ($required ? ' required' : '') . ' disabled>';
                                                echo '</td>';
                                                echo '<td class="border border-gray-300 px-2 py-2 text-center">';
                                                echo '<input type="text" name="' . $n7 . '" value="' . fmt($candidato[$a7] ?? '') . '" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg input-modern input-focus input-disabled text-center text-sm" style="--tw-ring-color: ' . $cursoCor . '; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)"' . ($required ? ' required' : '') . ' disabled>';
                                                echo '</td>';
                                                echo '<td class="border border-gray-300 px-2 py-2 text-center">';
                                                echo '<input type="text" name="' . $n8 . '" value="' . fmt($candidato[$a8] ?? '') . '" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg input-modern input-focus input-disabled text-center text-sm" style="--tw-ring-color: ' . $cursoCor . '; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)"' . ($required ? ' required' : '') . ' disabled>';
                                                echo '</td>';
                                                echo '</tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- Tabela 9º ano (bimestres e média) -->
                            <div>
                                <h4 class="text-lg font-medium text-gray-700 mb-2">9º Ano (Bimestres e Média)</h4>
                                <div class="overflow-x-auto">
                                    <table class="w-full border-collapse compact-table text-sm">
                                        <thead>
                                            <tr class="bg-gray-100">
                                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-medium text-gray-700">Matéria</th>
                                                <th class="border border-gray-300 px-4 py-2 text-center text-sm font-medium text-gray-700">1º Bim</th>
                                                <th class="border border-gray-300 px-4 py-2 text-center text-sm font-medium text-gray-700">2º Bim</th>
                                                <th class="border border-gray-300 px-4 py-2 text-center text-sm font-medium text-gray-700">3º Bim</th>
                                                <th class="border border-gray-300 px-4 py-2 text-center text-sm font-medium text-gray-700">Média</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $map9 = [
                                                ['Português', 'n1b_portuguesa', 'n2b_portuguesa', 'n3b_portuguesa', 'n9_portuguesa', 'portugues_9_1', 'portugues_9_2', 'portugues_9_3', 'portugues_9_media', true],
                                                ['Matemática', 'n1b_matematica', 'n2b_matematica', 'n3b_matematica', 'n9_matematica', 'matematica_9_1', 'matematica_9_2', 'matematica_9_3', 'matematica_9_media', true],
                                                ['História', 'n1b_historia', 'n2b_historia', 'n3b_historia', 'n9_historia', 'historia_9_1', 'historia_9_2', 'historia_9_3', 'historia_9_media', true],
                                                ['Geografia', 'n1b_geografia', 'n2b_geografia', 'n3b_geografia', 'n9_geografia', 'geografia_9_1', 'geografia_9_2', 'geografia_9_3', 'geografia_9_media', true],
                                                ['Ciências', 'n1b_ciencias', 'n2b_ciencias', 'n3b_ciencias', 'n9_ciencias', 'ciencias_9_1', 'ciencias_9_2', 'ciencias_9_3', 'ciencias_9_media', true],
                                                ['Inglês', 'n1b_inglesa', 'n2b_inglesa', 'n3b_inglesa', 'n9_inglesa', 'ingles_9_1', 'ingles_9_2', 'ingles_9_3', 'ingles_9_media', true],
                                                ['Artes', 'n1b_artes', 'n2b_artes', 'n3b_artes', 'n9_artes', 'artes_9_1', 'artes_9_2', 'artes_9_3', 'artes_9_media', false],
                                                ['Educação Física', 'n1b_educacao_fisica', 'n2b_educacao_fisica', 'n3b_educacao_fisica', 'n9_educacao_fisica', 'edfisica_9_1', 'edfisica_9_2', 'edfisica_9_3', 'edfisica_9_media', false],
                                                ['Religião', 'n1b_religiao', 'n2b_religiao', 'n3b_religiao', 'n9_religiao', 'religiao_9_1', 'religiao_9_2', 'religiao_9_3', 'religiao_9_media', false],
                                            ];
                                            foreach ($map9 as $row) {
                                                [$label, $b1, $b2, $b3, $med, $n1, $n2, $n3, $nMed, $required] = $row;
                                                echo '<tr class="hover:bg-gray-50">';
                                                echo '<td class="border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700">' . fmt($label) . '</td>';
                                                echo '<td class="border border-gray-300 px-2 py-2 text-center">';
                                                echo '<input type="text" name="' . $n1 . '" value="' . fmt($candidato[$b1] ?? '') . '" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg input-modern input-focus input-disabled text-center text-sm" style="--tw-ring-color: ' . $cursoCor . '; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" disabled>';
                                                echo '</td>';
                                                echo '<td class="border border-gray-300 px-2 py-2 text-center">';
                                                echo '<input type="text" name="' . $n2 . '" value="' . fmt($candidato[$b2] ?? '') . '" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg input-modern input-focus input-disabled text-center text-sm" style="--tw-ring-color: ' . $cursoCor . '; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" disabled>';
                                                echo '</td>';
                                                echo '<td class="border border-gray-300 px-2 py-2 text-center">';
                                                echo '<input type="text" name="' . $n3 . '" value="' . fmt($candidato[$b3] ?? '') . '" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg input-modern input-focus input-disabled text-center text-sm" style="--tw-ring-color: ' . $cursoCor . '; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" disabled>';
                                                echo '</td>';
                                                echo '<td class="border border-gray-300 px-2 py-2 text-center">';
                                                $bim1 = isset($candidato[$b1]) && !empty($candidato[$b1]);
                                                $bim2 = isset($candidato[$b2]) && !empty($candidato[$b2]);
                                                $bim3 = isset($candidato[$b3]) && !empty($candidato[$b3]);
                                                if ($bim1 && $bim2 && $bim3) {
                                                    echo '<span class="text-sm text-gray-500">Completo</span>';
                                                } else {
                                                    echo '<input type="text" name="' . $nMed . '" value="' . fmt($candidato[$med] ?? '') . '" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg input-modern input-focus input-disabled text-center text-sm bg-yellow-50" style="--tw-ring-color: ' . $cursoCor . '; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" disabled>';
                                                }
                                                echo '</td>';
                                                echo '</tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-3">
                        <button type="button" id="editBtn" class="px-6 py-3 rounded-xl bg-[<?= $cursoCor ?>] text-white font-semibold btn-animate" style="--hover-bg: <?= $cursoCor ?>dd;" onclick="toggleEditMode()">Editar</button>
                        <button type="submit" id="submitBtn" class="px-6 py-3 rounded-xl bg-[<?= $cursoCor ?>] text-white font-semibold btn-animate hidden" style="--hover-bg: <?= $cursoCor ?>dd;">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <div id="errorModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-icon">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="modal-title">Campos Obrigatórios Não Preenchidos</h3>
            <p id="errorMessage" class="modal-message">Por favor, preencha todos os campos obrigatórios antes de continuar.</p>
            <button class="modal-button" onclick="closeErrorModal()">Entendi</button>
        </div>
    </div>

    <script>
        function applyDateMask(input) {
            if (!input.disabled) {
                let value = input.value.replace(/\D/g, '');
                if (value.length > 8) value = value.slice(0, 8);
                if (value.length > 4) {
                    value = value.slice(0, 2) + '/' + value.slice(2, 4) + '/' + value.slice(4);
                } else if (value.length > 2) {
                    value = value.slice(0, 2) + '/' + value.slice(2);
                }
                input.value = value;
            }
        }

        function applyGradeMask(input) {
            if (!input.disabled) {
                let cursorPosition = input.selectionStart;
                let value = input.value;
                let cleanValue = value.replace(/[^\d.]/g, '');
                if (cleanValue === '' || cleanValue === '.') {
                    input.value = cleanValue === '.' ? '' : cleanValue;
                    return;
                }
                cleanValue = cleanValue.replace(/\.+/g, '.');
                if (cleanValue.startsWith('.')) {
                    cleanValue = '0' + cleanValue;
                }
                let parts = cleanValue.split('.');
                let integerPart = parts[0];
                let decimalPart = parts[1] || '';
                if (parts.length === 1 && integerPart.length >= 3) {
                    input.value = '10.00';
                    return;
                }
                if (integerPart.length > 2) {
                    integerPart = integerPart.slice(0, 2);
                }
                if (integerPart.length === 2 && integerPart !== '10' && parts.length === 1) {
                    decimalPart = integerPart[1] + decimalPart;
                    integerPart = integerPart[0];
                }
                if (decimalPart.length > 2) {
                    decimalPart = decimalPart.slice(0, 2);
                }
                let result = integerPart;
                if (parts.length > 1 || decimalPart.length > 0) {
                    result += '.' + decimalPart;
                }
                if (result.includes('.')) {
                    let numValue = parseFloat(result);
                    if (numValue > 10) {
                        result = '10.00';
                    }
                }
                input.value = result;
                let newCursorPos = result.length;
                setTimeout(() => {
                    input.setSelectionRange(newCursorPos, newCursorPos);
                }, 0);
            }
        }

        function validateForm() {
            const requiredInputs = document.querySelectorAll('#editarForm input[required]:not(:disabled)');
            const emptyFields = [];
            const subjectMap = {
                'portugues': 'Português',
                'matematica': 'Matemática',
                'historia': 'História',
                'geografia': 'Geografia',
                'ciencias': 'Ciências',
                'ingles': 'Inglês'
            };

            for (let input of requiredInputs) {
                if (!input.value.trim()) {
                    input.classList.add('border-red-500');
                    emptyFields.push(input);
                } else {
                    input.classList.remove('border-red-500');
                }
            }

            if (emptyFields.length > 0) {
                const missingSubjects = [...new Set(emptyFields.map(input => {
                    const subject = input.name.split('_')[0];
                    return subjectMap[subject] || subject;
                }))].join(', ');
                const message = `Por favor, preencha todas as notas obrigatórias para as matérias: ${missingSubjects} (6º, 7º, 8º anos e 1º, 2º, 3º bimestres do 9º ano).`;
                showErrorModal(message);
                return false;
            }
            return true;
        }

        function showErrorModal(message) {
            const modal = document.getElementById('errorModal');
            const errorMessage = document.getElementById('errorMessage');
            errorMessage.textContent = message;
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeErrorModal() {
            const modal = document.getElementById('errorModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function toggleEditMode() {
            const editBtn = document.getElementById('editBtn');
            const submitBtn = document.getElementById('submitBtn');
            const inputs = document.querySelectorAll('#editarForm input:not([type="hidden"]), #editarForm select');

            inputs.forEach(input => {
                input.disabled = false;
                input.classList.remove('input-disabled');
            });

            editBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
        }

        document.getElementById('errorModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeErrorModal();
            }
        });

        document.getElementById('editarForm').addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
            } else {
                const status = document.querySelector('input[name="status_candidato"]:checked').value;
                document.querySelector('input[name="pcd"]').value = status === 'pcd' ? '1' : '0';
                document.querySelector('input[name="bairro"]').value = status === 'cotas' ? '1' : '0';
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]), select');
            inputs.forEach(input => {
                input.classList.add('input-modern', 'input-focus', 'input-disabled', 'rounded-xl');
                input.style.setProperty('--tw-ring-color', '<?= $cursoCor ?>');
                input.style.setProperty('--tw-border-opacity', '0.5');
            });

            const checkboxes = document.querySelectorAll('input[type="checkbox"], input[type="radio"]');
            checkboxes.forEach(checkbox => {
                checkbox.classList.add('input-checkbox', 'input-disabled');
            });
        });
    </script>
</body>

</html>