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
function hex2rgba($hex, $alpha = 0.2)
{
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
    <link rel="icon" type="image/png" href="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png">
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

        .calculator-icon-btn {
            position: relative;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            cursor: pointer;
            background: white;
            border: 2px solid <?= $curso_cor ?>30;
        }

        .calculator-icon-btn:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 20px <?= $curso_cor ?>30;
            border-color: <?= $curso_cor ?>;
        }

        .calculator-icon-btn:active {
            transform: translateY(0) scale(0.98);
        }

        .calculator-icon-btn svg {
            transition: all 0.3s ease;
        }

        .calculator-icon-btn:hover svg {
            transform: scale(1.1);
        }

        .tooltip {
            position: absolute;
            bottom: -35px;
            left: 50%;
            transform: translateX(-50%) scale(0.8);
            background: #1f2937;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            pointer-events: none;
            z-index: 10;
        }

        .tooltip::before {
            content: '';
            position: absolute;
            top: -4px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-bottom: 5px solid #1f2937;
        }

        .calculator-icon-btn:hover .tooltip {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) scale(1);
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
            border: 2px solid #cbd5e1;
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
            border: 2px solid #cbd5e1;
            border-radius: 0.25rem;
            background-color: #fff;
            position: relative;
        }

        .input-checkbox:hover {
            transform: scale(1.05);
        }

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
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
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

        input[type="checkbox"],
        input[type="radio"] {
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

        #calculatorForm select,
        #calculatorForm input {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #calculatorForm input[readonly] {
            background-color: #fefcbf;
            cursor: default;
        }

        .subject-row {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .subject-row select,
        .subject-row input {
            flex: 1;
        }

        .subject-row button {
            padding: 0.5rem;
            background: #ef4444;
            color: white;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .subject-row button:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .add-subject-btn {
            background: #10b981;
        }

        .add-subject-btn:hover {
            background: #059669;
        }

        /* Estilos específicos para o modal da calculadora */
        #calculatorModal .modal-content {
            max-width: 48rem;
            width: 95%;
        }

        #subjectsList::-webkit-scrollbar {
            width: 6px;
        }

        #subjectsList::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        #subjectsList::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        #subjectsList::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .subject-row input:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .calculator-result-input {
            font-family: 'Inter', monospace;
            letter-spacing: 0.05em;
        }

        /* Sistema de Notificação Toast */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            pointer-events: none;
        }

        .toast {
            background: white;
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            pointer-events: auto;
            min-width: 280px;
        }

        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast.success {
            border-left: 4px solid #10b981;
        }

        .toast.error {
            border-left: 4px solid #ef4444;
        }

        .toast.warning {
            border-left: 4px solid #f59e0b;
        }

        .toast-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .toast.success .toast-icon {
            background: #10b981;
        }

        .toast.error .toast-icon {
            background: #ef4444;
        }

        .toast.warning .toast-icon {
            background: #f59e0b;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 600;
            font-size: 14px;
            color: #1f2937;
            margin: 0;
        }

        .toast-message {
            font-size: 13px;
            color: #6b7280;
            margin: 2px 0 0 0;
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
                    <span class="ml-3 font-medium">Página Inicial</span>
                </button>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                <div class="text-white p-6" style="background: linear-gradient(135deg, <?= $curso_cor ?>, #1A3C34);">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
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

                        <!-- Calculadora de Médias -->
                        <div class="w-full sm:w-auto flex items-center gap-4 bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 border border-white/20 shadow-lg hover:bg-white/15 transition-all duration-300 group cursor-pointer hidden" id="openCalculatorBtn">
                            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-all duration-300">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-white">Calculadora de Médias</span>
                                <span class="text-xs text-white/70">Calcule suas médias facilmente</span>
                            </div>
                            <svg class="w-4 h-4 text-white/60 group-hover:text-white group-hover:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
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
                                    <input type="text" name="nome" required placeholder="Digite seu nome completo" class="w-full px-4 py-3.5 border border-gray-300 rounded-xl input-modern input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="this.value = this.value.toUpperCase()">
                                </div>
                                <div>
                                    <input type="text" name="data_nascimento" required placeholder="DD/MM/AAAA" class="w-full px-4 py-3.5 border border-gray-300 rounded-xl input-modern input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyDateMask(this)">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-6 mb-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div class="w-full">
                                        <div class="flex items-center px-4 py-3.5 border border-gray-300 rounded-xl input-modern radio-card">
                                            <input type="radio" name="cota" value="ampla" id="ampla" class="w-5 h-5 text-primary border-gray-300 rounded input-radio focus:ring-2 focus:ring-primary focus:ring-opacity-50" checked>
                                            <label for="ampla" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer">Ampla</label>
                                        </div>
                                    </div>
                                    <div class="w-full">
                                        <div class="flex items-center px-4 py-3.5 border border-gray-300 rounded-xl input-modern radio-card">
                                            <input type="radio" name="cota" value="pcd" id="pcd" class="w-5 h-5 text-primary border-gray-300 rounded input-radio focus:ring-2 focus:ring-primary focus:ring-opacity-50">
                                            <label for="pcd" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer">Pessoa com Deficiência (PCD)</label>
                                        </div>
                                    </div>
                                    <div class="w-full">
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
                                                <input type="text" name="artes_6" placeholder="0,00" class="optional-subject-field w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" data-subject="artes">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="artes_7" placeholder="0,00" class="optional-subject-field w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" data-subject="artes">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="artes_8" placeholder="0,00" class="optional-subject-field w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" data-subject="artes">
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Educação Física</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="edfisica_6" placeholder="0,00" class="optional-subject-field w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" data-subject="edfisica">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="edfisica_7" placeholder="0,00" class="optional-subject-field w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" data-subject="edfisica">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="edfisica_8" placeholder="0,00" class="optional-subject-field w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" data-subject="edfisica">
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Religião</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="religiao_6" placeholder="0,00" class="optional-subject-field w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" data-subject="religiao">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="religiao_7" placeholder="0,00" class="optional-subject-field w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" data-subject="religiao">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="religiao_8" placeholder="0,00" class="optional-subject-field w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" oninput="applyGradeMask(this)" data-subject="religiao">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="step" id="step-3">
                            <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                Notas 9º Ano
                            </h3>
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
                                                <div class="p-2 rounded-lg" style="background: <?= $primary_rgba_01 ?>; border: 1px solid <?= $primary_rgba_02 ?>;">
                                                    <input type="text" name="portugues_9_media" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-transparent rounded-md input-modern text-center text-sm input-focus" style="background: rgba(255,255,255,0.92); --tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                                </div>
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
                                                <div class="p-2 rounded-lg" style="background: <?= $primary_rgba_01 ?>; border: 1px solid <?= $primary_rgba_02 ?>;">
                                                    <input type="text" name="matematica_9_media" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-transparent rounded-md input-modern text-center text-sm input-focus" style="background: rgba(255,255,255,0.92); --tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                                </div>
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
                                                <div class="p-2 rounded-lg" style="background: <?= $primary_rgba_01 ?>; border: 1px solid <?= $primary_rgba_02 ?>;">
                                                    <input type="text" name="historia_9_media" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-transparent rounded-md input-modern text-center text-sm input-focus" style="background: rgba(255,255,255,0.92); --tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                                </div>
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
                                                <div class="p-2 rounded-lg" style="background: <?= $primary_rgba_01 ?>; border: 1px solid <?= $primary_rgba_02 ?>;">
                                                    <input type="text" name="geografia_9_media" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-transparent rounded-md input-modern text-center text-sm input-focus" style="background: rgba(255,255,255,0.92); --tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                                </div>
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
                                                <div class="p-2 rounded-lg" style="background: <?= $primary_rgba_01 ?>; border: 1px solid <?= $primary_rgba_02 ?>;">
                                                    <input type="text" name="ciencias_9_media" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-transparent rounded-md input-modern text-center text-sm input-focus" style="background: rgba(255,255,255,0.92); --tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                                </div>
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
                                                <div class="p-2 rounded-lg" style="background: <?= $primary_rgba_01 ?>; border: 1px solid <?= $primary_rgba_02 ?>;">
                                                    <input type="text" name="ingles_9_media" placeholder="0,00" oninput="applyGradeMask(this)" class="w-full px-3 py-2 border border-transparent rounded-md input-modern text-center text-sm input-focus" style="background: rgba(255,255,255,0.92); --tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" required>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Artes</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="artes_9_1" placeholder="0,00" oninput="applyGradeMask(this)" class="optional-subject-field w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" data-subject="artes">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="artes_9_2" placeholder="0,00" oninput="applyGradeMask(this)" class="optional-subject-field w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" data-subject="artes">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="artes_9_3" placeholder="0,00" oninput="applyGradeMask(this)" class="optional-subject-field w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" data-subject="artes">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <div class="p-2 rounded-lg" style="background: <?= $primary_rgba_01 ?>; border: 1px solid <?= $primary_rgba_02 ?>;">
                                                    <input type="text" name="artes_9_media" placeholder="0,00" oninput="applyGradeMask(this)" class="optional-subject-field w-full px-3 py-2 border border-transparent rounded-md input-modern text-center text-sm input-focus" style="background: rgba(255,255,255,0.92); --tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" data-subject="artes">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Educação Física</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="edfisica_9_1" placeholder="0,00" oninput="applyGradeMask(this)" class="optional-subject-field w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" data-subject="edfisica">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="edfisica_9_2" placeholder="0,00" oninput="applyGradeMask(this)" class="optional-subject-field w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" data-subject="edfisica">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="edfisica_9_3" placeholder="0,00" oninput="applyGradeMask(this)" class="optional-subject-field w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" data-subject="edfisica">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <div class="p-2 rounded-lg" style="background: <?= $primary_rgba_01 ?>; border: 1px solid <?= $primary_rgba_02 ?>;">
                                                    <input type="text" name="edfisica_9_media" placeholder="0,00" oninput="applyGradeMask(this)" class="optional-subject-field w-full px-3 py-2 border border-transparent rounded-md input-modern text-center text-sm input-focus" style="background: rgba(255,255,255,0.92); --tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" data-subject="edfisica">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Religião</td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="religiao_9_1" placeholder="0,00" oninput="applyGradeMask(this)" class="optional-subject-field w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" data-subject="religiao">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="religiao_9_2" placeholder="0,00" oninput="applyGradeMask(this)" class="optional-subject-field w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" data-subject="religiao">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <input type="text" name="religiao_9_3" placeholder="0,00" oninput="applyGradeMask(this)" class="optional-subject-field w-full px-3 py-2 border border-gray-300 rounded-lg input-modern text-center text-sm input-focus" style="--tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" data-subject="religiao">
                                            </td>
                                            <td class="border border-gray-300 px-4 py-3">
                                                <div class="p-2 rounded-lg" style="background: <?= $primary_rgba_01 ?>; border: 1px solid <?= $primary_rgba_02 ?>;">
                                                    <input type="text" name="religiao_9_media" placeholder="0,00" oninput="applyGradeMask(this)" class="optional-subject-field w-full px-3 py-2 border border-transparent rounded-md input-modern text-center text-sm input-focus" style="background: rgba(255,255,255,0.92); --tw-ring-color: <?= $curso_cor ?>; --tw-border-opacity: 0.5;" data-subject="religiao">
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Informação Importante Dinâmica -->
                            <div class="mt-4 p-4 rounded-lg" style="background: <?= $primary_rgba_01 ?>; border: 1px solid <?= $primary_rgba_02 ?>;">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 mt-0.5 mr-3 flex-shrink-0" style="color: <?= $curso_cor ?>;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-medium" style="color: <?= $curso_cor ?>;">Informação Importante</h4>
                                        <p class="text-sm mt-1" style="color: <?= $curso_cor ?>dd;">Você pode preencher as notas por bimestre (1º, 2º, 3º) OU a média geral. <strong>A média geral não é obrigatória</strong> - se preencher os bimestres, a média será calculada automaticamente.</p>
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

    <!-- Modal de Confirmação para Notas em Branco -->
    <div id="confirmBlankModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-icon" style="background: linear-gradient(135deg, #f59e0b, #f97316);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="modal-title">Deixar Notas em Branco?</h3>
            <p id="confirmBlankMessage" class="modal-message">Você tem certeza que deseja deixar as notas de <strong id="subjectName"></strong> em branco?</p>
            <div class="flex gap-3">
                <button class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition-all duration-200" onclick="closeConfirmBlankModal()">
                    Cancelar
                </button>
                <button class="flex-1 px-4 py-2.5 text-white rounded-lg font-semibold transition-all duration-200" style="background: linear-gradient(135deg, #ef4444, #dc2626);" onclick="confirmBlank()">
                    Deixar em Branco
                </button>
            </div>
        </div>
    </div>

    <!-- Modal da Calculadora -->
<div id="calculatorModal" class="modal-overlay">
    <div class="modal-content bg-white rounded-xl p-0 max-w-2xl w-full mx-auto shadow-2xl transform transition-all duration-300 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 text-white" style="background: linear-gradient(135deg, <?= $curso_cor ?>, #1A3C34);">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Calculadora de Médias</h3>
                        <p class="text-white/80 text-sm">Adicione suas notas e calcule a média automaticamente</p>
                    </div>
                </div>
                <button type="button" onclick="closeCalculator()" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-all duration-200">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Conteúdo -->
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Seção de Notas -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-lg font-semibold text-gray-800">Suas Notas</h4>
                        <button type="button" onclick="addSingleField()" class="px-3 py-1.5 text-white rounded-lg transition-all duration-200 font-medium text-sm flex items-center gap-1.5 hover:opacity-90" style="background: linear-gradient(135deg, <?= $curso_cor ?>, #1A3C34);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Adicionar
                        </button>
                    </div>
                    <div id="subjectsList" class="space-y-3 max-h-80 overflow-y-auto pr-2 border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <!-- As linhas serão adicionadas dinamicamente -->
                    </div>
                </div>

                <!-- Seção de Resultado -->
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-gray-800">Resultado</h4>
                    
                    <!-- Resultado da Média -->
                    <div class="p-4 rounded-xl border-2 border-dashed" style="background: linear-gradient(135deg, <?= $primary_rgba_01 ?>, <?= $primary_rgba_015 ?>); border-color: <?= $primary_rgba_02 ?>;">
                        <div class="text-center space-y-3">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" style="color: <?= $curso_cor ?>;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <label class="text-sm font-semibold" style="color: <?= $curso_cor ?>;">Média Calculada</label>
                            </div>
                            <input type="text" id="calculatedAverage" placeholder="0.00" class="w-full text-center text-3xl font-bold border-0 rounded-lg px-4 py-3 bg-white shadow-inner focus:outline-none focus:ring-2 transition-all duration-200 calculator-result-input" style="color: <?= $curso_cor ?>; --tw-ring-color: <?= $curso_cor ?>;" readonly>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex gap-3 pt-2">
                        <button type="button" id="closeCalculatorBtn" class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-all duration-200 font-medium text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Container de Notificações Toast -->
    <div id="toastContainer" class="toast-container"></div>

    <script>
        // Sistema de Notificações Toast
        function showToast(title, message, type = 'success', duration = 3000) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            // Ícones diferentes para cada tipo
            let iconSvg = '';
            switch(type) {
                case 'success':
                    iconSvg = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>`;
                    break;
                case 'error':
                    iconSvg = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>`;
                    break;
                case 'warning':
                    iconSvg = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>`;
                    break;
                default:
                    iconSvg = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>`;
            }
            
            toast.innerHTML = `
                <div class="toast-icon">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${iconSvg}
                    </svg>
                </div>
                <div class="toast-content">
                    <h4 class="toast-title">${title}</h4>
                    <p class="toast-message">${message}</p>
                </div>
            `;
            
            container.appendChild(toast);
            
            // Trigger animation
            setTimeout(() => toast.classList.add('show'), 100);
            
            // Auto remove
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 400);
            }, duration);
        }

        function applyUppercase(input) {
            input.value = input.value.toUpperCase();
        }

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
            let value = input.value;
            let cleanValue = value.replace(/[^\d.]/g, '');

            if (cleanValue === '' || cleanValue === '.') {
                input.value = cleanValue === '.' ? '' : cleanValue;
                setTimeout(() => input.setSelectionRange(input.value.length, input.value.length), 0);
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
                setTimeout(() => input.setSelectionRange(input.value.length, input.value.length), 0);
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
            setTimeout(() => input.setSelectionRange(input.value.length, input.value.length), 0);
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
            const calcBtn = document.getElementById('openCalculatorBtn');
            if (calcBtn) {
                calcBtn.classList.toggle('hidden', currentStep !== 2 && currentStep !== 3);
            }
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
                'ingles': 'Inglês',
                'artes': 'Artes',
                'edfisica': 'Educação Física',
                'religiao': 'Religião'
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
                const message = step === 2 ?
                    `Por favor, preencha todas as notas obrigatórias para as matérias: ${missingSubjects} (6º, 7º e 8º anos).` :
                    `Por favor, preencha todas as notas obrigatórias para as matérias: ${missingSubjects} (1º, 2º e 3º bimestres).`;
                showErrorModal(message);
                return false;
            }

            // Verifica se há campos opcionais (Artes, Educação Física, Religião) vazios
            const optionalFields = document.querySelectorAll(`#step-${step} .optional-subject-field:not([required])`);
            const blankOptionalFields = [];
            const optionalSubjects = new Set();

            for (let input of optionalFields) {
                if (!input.disabled && !input.value.trim()) {
                    blankOptionalFields.push(input);
                    optionalSubjects.add(input.dataset.subject);
                }
            }

            if (blankOptionalFields.length > 0 && optionalSubjects.size > 0) {
                const subjectNames = [...optionalSubjects].map(subject => subjectMap[subject] || subject).join(', ');
                showConfirmBlankSubmit(step, subjectNames);
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

        // Sistema de confirmação para notas em branco nas matérias opcionais
        let currentStepOnBlank = null;
        let subjectNamesOnBlank = null;

        function showConfirmBlankSubmit(step, subjectNames) {
            const modal = document.getElementById('confirmBlankModal');
            const subjectNameEl = document.getElementById('subjectName');
            subjectNameEl.textContent = subjectNames;
            
            currentStepOnBlank = step;
            subjectNamesOnBlank = subjectNames;
            
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeConfirmBlankModal() {
            const modal = document.getElementById('confirmBlankModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
            
            currentStepOnBlank = null;
            subjectNamesOnBlank = null;
        }

        function confirmBlank() {
            const modal = document.getElementById('confirmBlankModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
            
            // Mostra notificação
            showToast(
                'Notas em Branco',
                'As notas foram deixadas em branco conforme solicitado.',
                'warning',
                2000
            );
            
            // Permite o avanço/envio
            if (currentStepOnBlank < totalSteps) {
                currentStep = currentStepOnBlank + 1;
                updateStep();
            } else {
                // Se está no último passo, envia o formulário
                document.getElementById('cadastroForm').submit();
            }
            
            currentStepOnBlank = null;
            subjectNamesOnBlank = null;
        }

        document.getElementById('confirmBlankModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeConfirmBlankModal();
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

            // Registrar quais campos eram required originalmente para restaurar corretamente
            [...bimestreInputs, ...mediaInputs].forEach(input => {
                if (input.dataset.wasRequired === undefined) {
                    input.dataset.wasRequired = input.hasAttribute('required') ? 'true' : 'false';
                }
            });

            function disableMediaFields() {
                mediaInputs.forEach(input => {
                    input.disabled = true;
                    input.classList.add('opacity-50', 'cursor-not-allowed');
                    input.classList.remove('input-focus');
                    // Remover required quando desabilitar
                    input.removeAttribute('required');
                });
            }

            function disableBimestreFields() {
                bimestreInputs.forEach(input => {
                    input.disabled = true;
                    input.classList.add('opacity-50', 'cursor-not-allowed');
                    input.classList.remove('input-focus');
                    // Remover required quando desabilitar
                    input.removeAttribute('required');
                });
            }

            function enableAllFields() {
                [...bimestreInputs, ...mediaInputs].forEach(input => {
                    input.disabled = false;
                    input.classList.remove('opacity-50', 'cursor-not-allowed');
                    input.classList.add('input-focus');
                    // Restaurar required apenas para quem era originalmente required
                    if (input.dataset.wasRequired === 'true') {
                        input.setAttribute('required', '');
                    } else {
                        input.removeAttribute('required');
                    }
                });
            }

            bimestreInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const allBimestresEmpty = Array.from(bimestreInputs).every(input => input.value.trim() === '');
                    if (this.value.trim() !== '') {
                        disableMediaFields();
                    } else if (allBimestresEmpty) {
                        enableAllFields();
                    }
                });
            });

            mediaInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const allMediasEmpty = Array.from(mediaInputs).every(input => input.value.trim() === '');
                    if (this.value.trim() !== '') {
                        disableBimestreFields();
                    } else if (allMediasEmpty) {
                        enableAllFields();
                    }
                });
            });

            const allBimestresEmpty = Array.from(bimestreInputs).every(input => input.value.trim() === '');
            const allMediasEmpty = Array.from(mediaInputs).every(input => input.value.trim() === '');
            if (!allBimestresEmpty) {
                disableMediaFields();
            } else if (!allMediasEmpty) {
                disableBimestreFields();
            }
        }

        // Configuração da Calculadora de Médias
        const calculatorModal = document.getElementById('calculatorModal');
        const openCalculatorBtn = document.getElementById('openCalculatorBtn');
        const closeCalculatorBtn = document.getElementById('closeCalculatorBtn');
        const subjectsList = document.getElementById('subjectsList');
        const calculatedAverage = document.getElementById('calculatedAverage');
        const primaryColor = '<?= $curso_cor ?>';

   function addSingleField() {
    const fieldNumber = subjectsList.children.length + 1;
    const row = document.createElement('div');
    row.className = 'subject-row flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200 hover:border-gray-300 hover:shadow-sm transition-all duration-200';
    
    row.innerHTML = `
        <div class="flex items-center justify-center w-7 h-7 bg-gray-100 rounded-lg border border-gray-300 text-sm font-semibold text-gray-600 flex-shrink-0">
            ${fieldNumber}
        </div>
        <input type="text" placeholder="Digite a nota (ex: 8.5)" class="flex-1 border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-center transition-all duration-200 focus:border-transparent focus:ring-2 focus:ring-opacity-50 focus:outline-none" style="--tw-ring-color: ${primaryColor};" oninput="applyGradeMask(this); calculateAverage()">
        <button type="button" class="p-2 rounded-lg bg-red-500 text-white hover:bg-red-600 transition-all duration-200 hover:scale-105 flex-shrink-0" onclick="removeSubjectRow(this)" title="Remover nota">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    
    subjectsList.appendChild(row);
    updateFieldNumbers();
    // Focar no novo campo adicionado
    const newInput = row.querySelector('input');
    setTimeout(() => newInput.focus(), 100);
}

        function updateFieldNumbers() {
            const rows = subjectsList.querySelectorAll('.subject-row');
            rows.forEach((row, index) => {
                const numberDiv = row.querySelector('div:first-child');
                if (numberDiv) {
                    numberDiv.textContent = index + 1;
                }
            });
        }

        function removeSubjectRow(button) {
            if (subjectsList.children.length > 1) {
                button.parentElement.remove();
                updateFieldNumbers();
                calculateAverage();
            }
        }

        function calculateAverage() {
            const inputs = subjectsList.querySelectorAll('input');
            let totalSum = 0;
            let totalGrades = 0;

            inputs.forEach(input => {
                const value = parseFloat(input.value);
                if (!isNaN(value) && value > 0) {
                    totalSum += value;
                    totalGrades++;
                }
            });

            const average = totalGrades > 0 ? (totalSum / totalGrades).toFixed(2) : '0.00';
            calculatedAverage.value = average;
        }

        function copyAverage() {
            const average = calculatedAverage.value;
            if (average !== '0.00') {
                navigator.clipboard.writeText(average).then(() => {
                    // Mostrar notificação toast
                    showToast(
                        'Média Copiada!', 
                        `A média ${average} foi copiada para a área de transferência.`, 
                        'success', 
                        4000
                    );
                    
                    // Fechar calculadora após um pequeno delay
                    setTimeout(() => {
                        closeCalculator();
                    }, 1000);
                }).catch(() => {
                    // Fallback caso a API de clipboard falhe
                    showToast(
                        'Erro ao Copiar', 
                        'Não foi possível copiar a média. Tente novamente.', 
                        'error', 
                        4000
                    );
                });
            } else {
                showToast(
                    'Nenhuma Média', 
                    'Adicione pelo menos uma nota para calcular a média.', 
                    'warning', 
                    3000
                );
            }
        }

        function closeCalculator() {
            calculatorModal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        openCalculatorBtn.addEventListener('click', () => {
            subjectsList.innerHTML = '';
            // Adicionar 3 campos iniciais para uma melhor experiência
            addSingleField();
            addSingleField();
            addSingleField();
            calculateAverage();
            calculatorModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });

        closeCalculatorBtn.addEventListener('click', closeCalculator);

        calculatorModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCalculator();
            }
        });

        // Radio card selection
        document.querySelectorAll('.radio-card').forEach(card => {
            card.addEventListener('click', function() {
                const input = this.querySelector('input[type="radio"]');
                if (input) {
                    document.querySelectorAll('.radio-card').forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');
                    input.checked = true;
                }
            });
        });

        setupExclusiveFields();
        updateStep();
    </script>
</body>

</html>



