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

// Capturar parâmetros da URL
$curso_id = $_GET['curso_id'] ?? '';
$curso_nome = $_GET['curso_nome'] ?? '';
$curso_cor = $_GET['curso_cor'] ?? '#005A24';
$tipo_escola = $_GET['tipo_escola'] ?? '';

// Se houver curso_id, tenta obter a cor do curso a partir do banco
if (!empty($curso_id)) {
    try {
        $cursos = $select->select_cursos();
        foreach ($cursos as $curso) {
            if ((string)$curso['id'] === (string)$curso_id) {
                if (!empty($curso['cor_curso'])) {
                    $curso_cor = $curso['cor_curso'];
                }
                break;
            }
        }
    } catch (Exception $e) {
        // mantém a cor padrão ou a recebida por GET em caso de erro
    }
}

// helpers de cor para usar transparências no CSS
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

$primary_rgba_01 = hex2rgba($curso_cor, 0.10);
$primary_rgba_015 = hex2rgba($curso_cor, 0.15);
$primary_rgba_02 = hex2rgba($curso_cor, 0.20);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Cadastro de Candidatos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '<?= $curso_cor ?>',
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
            --primary: <?= $curso_cor ?>;
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

        .input-focus:focus {
            border-color: var(--primary);
            outline: none;
        }

        .input-modern {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .input-modern:focus {
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

        .input-radio {
            transition: all 0.2s ease-in-out;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            width: 1rem;
            height: 1rem;
            border: 2px solid #cbd5e1; /* gray-300 */
            border-radius: 9999px;
            background-color: #fff;
            position: relative;
        }

        .input-radio:hover {
            transform: scale(1.05);
        }

        .input-radio:checked {
            border-color: var(--primary);
        }

        .input-radio:checked::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0.5rem;
            height: 0.5rem;
            background-color: var(--primary);
            border-radius: 9999px;
            transform: translate(-50%, -50%);
        }

        .input-radio:focus {
            box-shadow: 0 0 0 3px <?= $primary_rgba_015 ?>;
        }

        .input-checkbox {
            transition: all 0.2s ease-in-out;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            width: 1rem;
            height: 1rem;
            border: 2px solid #cbd5e1; /* gray-300 */
            border-radius: 0.25rem;
            background-color: #fff;
            position: relative;
        }

        .input-checkbox:hover { transform: scale(1.05); }

        .input-checkbox:checked {
            border-color: var(--primary);
            background-color: var(--primary);
        }

        .input-checkbox:checked::after {
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

        .radio-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .radio-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            transform: translateY(-1px);
        }

        .radio-card.selected {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px <?= $primary_rgba_02 ?>;
        }

        .input-disabled {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: none;
        }

        .input-disabled::-ms-expand {
            display: none;
        }

        button[style*="--hover-bg"]:hover {
            background-color: var(--hover-bg) !important;
        }

        button[style*="border-color"]:hover {
            background-color: var(--hover-bg) !important;
        }

        .step {
            display: none;
        }

        .step.active {
            display: block;
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: #e5e7eb;
            border-radius: 9999px;
            position: relative;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: var(--primary);
            border-radius: 9999px;
            transition: width 0.3s ease-in-out;
        }

        .compact-table th,
        .compact-table td {
            padding: 0.5rem 0.5rem !important;
        }
        .compact-table input[type="text"] {
            padding-top: 0.4rem !important;
            padding-bottom: 0.4rem !important;
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }

        input[type="checkbox"], input[type="radio"] {
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
                <div class="text-white p-6" style="background: linear-gradient(135deg, <?= $curso_cor ?>, #1A3C34);">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/30 shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold font-display tracking-tight">Formulário de Inscrição</h2>
                            <p class="text-white/90 text-sm mt-1 font-medium">
                                <?php if ($curso_nome && $tipo_escola): ?>
                                    Curso: <?= htmlspecialchars($curso_nome) ?> -
                                    <?= $tipo_escola === 'publica' ? 'Escola Pública' : 'Escola Privada' ?>
                                <?php else: ?>
                                    Sistema de Seleção Escolar
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6 pb-2">
                    <div class="progress-bar">
                        <div class="progress-bar-fill" style="width: 33.33%"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-sm text-gray-600">
                        <span>Informações Pessoais</span>
                        <span>Notas 6º-8º Ano</span>
                        <span>Notas 9º Ano</span>
                    </div>
                </div>

                <div class="p-6 pt-2">
                    <form action="../controllers/controller_candidato.php" method="POST" id="cadastroForm" class="space-y-8">
                        <div class="step active" id="step-1">
                            <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Informações Pessoais
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <input type="text" name="nome" required placeholder="Digite seu nome completo" class="w-full px-4 py-3.5 border border-gray-300 rounded-xl input-modern input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;">
                                </div>
                                <div>
                                    <input type="text" name="data_nascimento" required placeholder="DD/MM/AAAA" class="w-full px-4 py-3.5 border border-gray-300 rounded-xl input-modern input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyDateMask(this)">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-6 mb-6">
                                <div class="flex space-x-4">
                                    <div class="flex-1">
                                        <div class="flex items-center px-4 py-3.5 border border-gray-300 rounded-xl input-modern radio-card">
                                            <input type="radio" name="cota" value="ampla" id="ampla" class="w-5 h-5 text-primary border-gray-300 rounded input-radio focus:ring-2 focus:ring-primary focus:ring-opacity-50" checked>
                                            <label for="ampla" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer">Ampla</label>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center px-4 py-3.5 border border-gray-300 rounded-xl input-modern radio-card">
                                            <input type="radio" name="cota" value="pcd" id="pcd" class="w-5 h-5 text-primary border-gray-300 rounded input-radio focus:ring-2 focus:ring-primary focus:ring-opacity-50">
                                            <label for="pcd" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer">Pessoa com Deficiência (PCD)</label>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center px-4 py-3.5 border border-gray-300 rounded-xl input-modern radio-card">
                                            <input type="radio" name="cota" value="bairro" id="bairro" class="w-5 h-5 text-primary border-gray-300 rounded input-radio focus:ring-2 focus:ring-primary focus:ring-opacity-50">
                                            <label for="bairro" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer">Cota bairro</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <select name="curso_id" required disabled class="w-full px-4 py-3.5 border border-gray-300 rounded-xl input-disabled">
                                        <option value="">Curso selecionado</option>
                                        <?php
                                        $cursos = $select->select_cursos();
                                        foreach ($cursos as $curso) {
                                            $selected = ($curso['id'] == $curso_id) ? 'selected' : '';
                                            echo "<option value='{$curso['id']}' data-cor='{$curso['cor_curso']}' {$selected}>{$curso['nome_curso']}</option>";
                                        }
                                        ?>
                                    </select>
                                    <input type="hidden" name="curso_id" value="<?= htmlspecialchars($curso_id) ?>">
                                </div>
                                <div>
                                    <select name="tipo_escola" required disabled class="w-full px-4 py-3.5 border border-gray-300 rounded-xl input-disabled">
                                        <option value="">Tipo de escola</option>
                                        <option value="publica" <?= $tipo_escola === 'publica' ? 'selected' : '' ?>>Escola Pública</option>
                                        <option value="privada" <?= $tipo_escola === 'privada' ? 'selected' : '' ?>>Escola Privada</option>
                                    </select>
                                    <input type="hidden" name="tipo_escola" value="<?= htmlspecialchars($tipo_escola) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="step" id="step-2">
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse compact-table text-sm">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="border border-gray-300 px-4 py-3 text-left text-sm font-medium text-gray-700">Matéria</th>
                                            <th class="border border-gray-300 px-4 py-3 text-center text-sm font-medium text-gray-700">6º Ano</th>
                                            <th class="border border-gray-300 px-4 py-3 text-center text-sm font-medium text-gray-700">7º Ano</th>
                                            <th class="border border-gray-300 px-4 py-3 text-center text-sm font-medium text-gray-700">8º Ano</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Português</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="portugues_6" placeholder="0,00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="portugues_7" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="portugues_8" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" required>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Matemática</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="matematica_6" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="matematica_7" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="matematica_8" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" required>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">História</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="historia_6" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="historia_7" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="historia_8" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" required>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Geografia</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="geografia_6" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="geografia_7" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="geografia_8" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" required>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Ciências</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="ciencias_6" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="ciencias_7" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="ciencias_8" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" required>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Inglês</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="ingles_6" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="ingles_7" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="ingles_8" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" required>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Artes</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="artes_6" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="artes_7" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="artes_8" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)">
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Educação Física</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="edfisica_6" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="edfisica_7" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="edfisica_8" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)">
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Religião</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="religiao_6" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="religiao_7" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="religiao_8" placeholder="0,00" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="step" id="step-3">
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse compact-table text-sm">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="border border-gray-300 px-4 py-3 text-left text-sm font-medium text-gray-700">Matéria</th>
                                            <th class="border border-gray-300 px-4 py-3 text-center text-sm font-medium text-gray-700">1º Bimestre</th>
                                            <th class="border border-gray-300 px-4 py-3 text-center text-sm font-medium text-gray-700">2º Bimestre</th>
                                            <th class="border border-gray-300 px-4 py-3 text-center text-sm font-medium text-gray-700">3º Bimestre</th>
                                            <th class="border border-gray-300 px-4 py-3 text-center text-sm font-medium text-gray-700">Média Geral</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Português</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="portugues_9_1" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="portugues_9_2" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="portugues_9_3" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="portugues_9_media" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus bg-yellow-50" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;">
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Matemática</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="matematica_9_1" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="matematica_9_2" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="matematica_9_3" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="matematica_9_media" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus bg-yellow-50" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;">
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">História</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="historia_9_1" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="historia_9_2" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="historia_9_3" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="historia_9_media" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus bg-yellow-50" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;">
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Geografia</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="geografia_9_1" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="geografia_9_2" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="geografia_9_3" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="geografia_9_media" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus bg-yellow-50" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;">
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Ciências</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="ciencias_9_1" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="ciencias_9_2" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="ciencias_9_3" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="ciencias_9_media" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus bg-yellow-50" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;">
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Inglês</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="ingles_9_1" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="ingles_9_2" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="ingles_9_3" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="ingles_9_media" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus bg-yellow-50" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;">
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Artes</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="artes_9_1" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="artes_9_2" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="artes_9_3" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="artes_9_media" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus bg-yellow-50" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;">
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Educação Física</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="edfisica_9_1" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="edfisica_9_2" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="edfisica_9_3" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="edfisica_9_media" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus bg-yellow-50" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;">
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Religião</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="religiao_9_1" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="religiao_9_2" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="religiao_9_3" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="religiao_9_media" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus bg-yellow-50" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-medium text-blue-800">Informação Importante</h4>
                                        <p class="text-sm text-blue-700 mt-1">Você pode preencher as notas por bimestre (1º, 2º, 3º) OU a média geral. <strong>A média geral não é obrigatória</strong> - se preencher os bimestres, a média será calculada automaticamente.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <button type="button" id="prevBtn" class="px-8 py-3 border-2 rounded-lg transition-all duration-300 font-semibold group hidden" style="border-color: <?= $curso_cor ?>; color: <?= $curso_cor ?>; --hover-bg: <?= $curso_cor ?>20;">
                                    <span class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                        Voltar
                                    </span>
                                </button>

                                <div class="flex space-x-4">
                                    <button type="button" id="nextBtn" class="px-8 py-3 text-white rounded-lg transition-all duration-300 font-semibold group" style="background-color: <?= $curso_cor ?>; --hover-bg: <?= $curso_cor ?>dd;">
                                        <span class="flex items-center">
                                            <svg class="w-5 h-5 mr-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                            Avançar
                                        </span>
                                    </button>
                                    <button type="submit" id="submitBtn" class="px-8 py-3 text-white rounded-lg transition-all duration-300 font-semibold group hidden" style="background-color: <?= $curso_cor ?>; --hover-bg: <?= $curso_cor ?>dd;">
                                        <span class="flex items-center">
                                            <svg class="w-5 h-5 mr-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Cadastrar Candidato
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
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
            let value = input.value.replace(/\D/g, '');
            if (value.length > 8) value = value.slice(0, 8);
            if (value.length > 4) {
                value = value.slice(0, 2) + '/' + value.slice(2, 4) + '/' + value.slice(4);
            } else if (value.length > 2) {
                value = value.slice(0, 2) + '/' + value.slice(2);
            }
            input.value = value;
        }
function applyGradeMask(input) {
    // Armazena a posição do cursor
    let cursorPosition = input.selectionStart;
    let value = input.value;
    
    // Remove tudo exceto dígitos e vírgula
    let cleanValue = value.replace(/[^\d,]/g, '');
    
    // Se o campo está vazio ou sendo limpo, permite
    if (cleanValue === '' || cleanValue === ',') {
        input.value = cleanValue === ',' ? '' : cleanValue;
        return;
    }
    
    // Garante apenas uma vírgula
    cleanValue = cleanValue.replace(/,+/g, ',');
    
    // Se começar com vírgula, adiciona 0
    if (cleanValue.startsWith(',')) {
        cleanValue = '0' + cleanValue;
    }
    
    // Separa partes
    let parts = cleanValue.split(',');
    let integerPart = parts[0];
    let decimalPart = parts[1] || '';
    
    // Se digitou 3+ dígitos sem vírgula, converte para 10,00
    if (parts.length === 1 && integerPart.length >= 3) {
        input.value = '10,00';
        return;
    }
    
    // Processa parte inteira
    if (integerPart.length > 2) {
        integerPart = integerPart.slice(0, 2);
    }
    
    // Se tem 2 dígitos na parte inteira e não é "10"
    if (integerPart.length === 2 && integerPart !== '10' && parts.length === 1) {
        // Move o segundo dígito para decimal
        decimalPart = integerPart[1] + decimalPart;
        integerPart = integerPart[0];
    }
    
    // Limita parte decimal a 2 dígitos
    if (decimalPart.length > 2) {
        decimalPart = decimalPart.slice(0, 2);
    }
    
    // Monta o resultado
    let result = integerPart;
    if (parts.length > 1 || decimalPart.length > 0) {
        result += ',' + decimalPart;
    }
    
    // Verifica se excede 10
    if (result.includes(',')) {
        let numValue = parseFloat(result.replace(',', '.'));
        if (numValue > 10) {
            result = '10,00';
        }
    }
    
    // Aplica o resultado
    input.value = result;
    
    // Restaura a posição do cursor (aproximadamente)
    let newCursorPos = Math.min(cursorPosition, result.length);
    setTimeout(() => {
        input.setSelectionRange(newCursorPos, newCursorPos);
    }, 0);
}

        let currentStep = 1;
        const totalSteps = 3;
        const steps = document.querySelectorAll('.step');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        const progressBarFill = document.querySelector('.progress-bar-fill');

        function updateStep() {
            steps.forEach((step, index) => {
                step.classList.toggle('active', index + 1 === currentStep);
            });
            prevBtn.classList.toggle('hidden', currentStep === 1);
            nextBtn.classList.toggle('hidden', currentStep === totalSteps);
            submitBtn.classList.toggle('hidden', currentStep !== totalSteps);
            progressBarFill.style.width = `${(currentStep / totalSteps) * 100}%`;
        }

        function validateStep(step) {
            const inputs = document.querySelectorAll(`#step-${step} input[required]`);
            const emptyFields = [];
            const subjectMap = {
                'portugues': 'Português',
                'matematica': 'Matemática',
                'historia': 'História',
                'geografia': 'Geografia',
                'ciencias': 'Ciências',
                'ingles': 'Inglês'
            };

            for (let input of inputs) {
                if (!input.disabled && !input.value.trim()) {
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
                const message = step === 2 
                    ? `Por favor, preencha todas as notas obrigatórias para as matérias: ${missingSubjects} (6º, 7º e 8º anos).`
                    : `Por favor, preencha todas as notas obrigatórias para as matérias: ${missingSubjects} (1º, 2º e 3º bimestres).`;
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

        document.getElementById('errorModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeErrorModal();
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentStep > 1) {
                currentStep--;
                updateStep();
            }
        });

        nextBtn.addEventListener('click', () => {
            if (currentStep < totalSteps && validateStep(currentStep)) {
                currentStep++;
                updateStep();
            }
        });

        submitBtn.addEventListener('click', (e) => {
            if (!validateStep(currentStep)) {
                e.preventDefault();
            }
        });

        function setupExclusiveFields() {
            const bimestreInputs = document.querySelectorAll('input[name*="_9_1"], input[name*="_9_2"], input[name*="_9_3"]');
            const mediaInputs = document.querySelectorAll('input[name*="_9_media"]');

            function disableMediaFields() {
                mediaInputs.forEach(input => {
                    input.disabled = true;
                    input.classList.add('opacity-50', 'cursor-not-allowed');
                    input.classList.remove('input-focus');
                });
            }

            function disableBimestreFields() {
                bimestreInputs.forEach(input => {
                    input.disabled = true;
                    input.classList.add('opacity-50', 'cursor-not-allowed');
                    input.classList.remove('input-focus');
                });
            }

            function enableAllFields() {
                [...bimestreInputs, ...mediaInputs].forEach(input => {
                    input.disabled = false;
                    input.classList.remove('opacity-50', 'cursor-not-allowed');
                    input.classList.add('input-focus');
                });
            }

            bimestreInputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value.trim() !== '') {
                        disableMediaFields();
                    } else {
                        const allBimestreEmpty = Array.from(bimestreInputs).every(inp => inp.value.trim() === '');
                        if (allBimestreEmpty) {
                            enableAllFields();
                        }
                    }
                });
            });

            mediaInputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value.trim() !== '') {
                        disableBimestreFields();
                    } else {
                        const allMediaEmpty = Array.from(mediaInputs).every(inp => inp.value.trim() === '');
                        if (allMediaEmpty) {
                            enableAllFields();
                        }
                    }
                });
            });
        }

        function applyModernStyles() {
            const cursoCor = '<?= $curso_cor ?>';
            const inputs = document.querySelectorAll('input:not([disabled]):not([type="checkbox"]):not([type="radio"]), select:not([disabled])');

            inputs.forEach(input => {
                input.classList.add('input-modern', 'input-focus');
                if (!input.classList.contains('rounded-xl')) {
                    input.classList.add('rounded-lg');
                }
                input.style.setProperty('--tw-ring-color', cursoCor);
                input.style.setProperty('--tw-border-opacity', '0.5');
            });

            const radios = document.querySelectorAll('input[type="radio"]');
            radios.forEach(radio => {
                radio.classList.add('input-radio');
            });

            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.classList.add('input-checkbox');
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            setupExclusiveFields();
            updateStep();
            applyModernStyles();
            const radioCards = document.querySelectorAll('.radio-card');
            const radioInputs = document.querySelectorAll('input[name="cota"]');

            function syncSelected() {
                radioCards.forEach(card => card.classList.remove('selected'));
                const checked = document.querySelector('input[name="cota"]:checked');
                if (checked) {
                    const card = checked.closest('.radio-card');
                    if (card) card.classList.add('selected');
                }
            }

            radioCards.forEach(card => {
                card.addEventListener('click', () => {
                    const input = card.querySelector('input[name="cota"]');
                    if (input) {
                        input.checked = true;
                        syncSelected();
                    }
                });
            });

            radioInputs.forEach(input => input.addEventListener('change', syncSelected));
            syncSelected();
        });
    </script>
</body>

</html>