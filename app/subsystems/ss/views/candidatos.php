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

$candidatos_ativos = $select->select_candidatos_ativos();
$cursos = $select->select_cursos();
$usuarios = $select->select_usuarios();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Candidatos</title>
    <link rel="icon" type="image/png" href="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slim-select@2.8.1/dist/slimselect.css">
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

        .card-hover {
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            overflow: hidden;
        }

        .card-hover::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }

        .card-hover:hover::before {
            left: 100%;
        }

        .card-hover:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 90, 36, 0.25), 0 0 0 1px rgba(0, 90, 36, 0.05);
        }

        .grid-item {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }

        .grid-item:nth-child(1) {
            animation-delay: 0.1s;
        }

        .grid-item:nth-child(2) {
            animation-delay: 0.2s;
        }

        .grid-item:nth-child(3) {
            animation-delay: 0.3s;
        }

        .grid-item:nth-child(4) {
            animation-delay: 0.4s;
        }

        .grid-item:nth-child(5) {
            animation-delay: 0.5s;
        }

        .grid-item:nth-child(6) {
            animation-delay: 0.6s;
        }

        @media (max-width: 768px) {
            .card-hover:hover {
                transform: none;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }
        }
    </style>
</head>

<body class="bg-white min-h-screen font-body">
    <div class="flex h-screen bg-gray-50 overflow-hidden">
        <?php include __DIR__ . '/partials/sidebar.php'; ?>
        <div class="main-content flex-1 h-screen overflow-y-auto custom-scrollbar bg-white">
            <header class="bg-white shadow-sm border-b border-gray-200 z-30 sticky top-0">
                <div class="px-3 sm:px-4 md:px-6 lg:px-8 py-3 sm:py-4">
                    <div class="flex items-center justify-between">
                        <button id="openSidebar" class="text-primary lg:hidden btn-animate p-2 sm:p-3 rounded-xl hover:bg-accent focus-ring">
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
                            <a href="../models/sessions.php?sair" class="bg-primary text-white px-3 sm:px-4 md:px-6 py-2 sm:py-3 rounded-xl hover:bg-dark btn-animate font-semibold shadow-lg focus-ring text-xs sm:text-sm">
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
                <div class="flex items-center justify-between mb-6">
                    <div class="text-lg font-semibold text-gray-800">Lista de Candidatos</div>
                </div>

                <!-- Barra de Pesquisa e Filtros -->
                <div class="mb-6">
                    <div class="bg-accent/40 border border-primary/10 rounded-2xl p-4 sm:p-5 shadow-sm">
                        <div class="flex flex-wrap lg:flex-nowrap items-center gap-3 lg:gap-4">
                            <div class="relative w-full lg:w-80 xl:w-96">
                                <input type="text" id="searchInput" placeholder="Pesquisar por nome do candidato..." class="w-full px-4 py-3 pr-10 border border-primary/30 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm bg-white" onkeyup="filterCandidates()">
                                <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>

                            <!-- Select único de filtros -->
                            <div class="relative min-w-[200px]">
                                <select id="filterPicker" onchange="addFilterChip()">
                                    <option value="">Adicionar filtro...</option>
                                    <optgroup label="Curso">
                                        <?php foreach ($cursos as $curso) { 
                                            $nomeCursoOriginal = $curso['nome_curso'] ?? '';
                                            $nomeCursoNormalizado = ucfirst(mb_strtolower($nomeCursoOriginal, 'UTF-8'));
                                        ?>
                                            <option value="curso:<?= htmlspecialchars($nomeCursoOriginal) ?>">Curso: <?= htmlspecialchars($nomeCursoNormalizado) ?></option>
                                        <?php } ?>
                                    </optgroup>
                                    <optgroup label="Segmento">
                                        <option value="segmento:AMPLA">Segmento: Ampla</option>
                                        <option value="segmento:BAIRRO">Segmento: Bairro</option>
                                        <option value="segmento:PCD">Segmento: Pcd</option>
                                    </optgroup>
                                    <optgroup label="Origem">
                                        <option value="origem:Pública">Origem: Pública</option>
                                        <option value="origem:Privada">Origem: Privada</option>
                                    </optgroup>
                                </select>
                            </div>

                            <!-- Chips de filtros ativos -->
                            <div id="activeFilters" class="flex flex-wrap gap-2 flex-1"></div>

                            <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                                <div class="ml-0 lg:ml-2">
                                    <a href="candidatos_excluidos.php" class="bg-primary text-white px-4 sm:px-5 py-2.5 sm:py-3 rounded-xl hover:bg-dark transition-all duration-300 font-medium text-xs sm:text-sm btn-animate focus-ring flex items-center shadow hover:shadow-lg whitespace-nowrap">
                                        <svg class="w-4 h-4 mr-1.5 sm:mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        <span>Candidatos Inativos</span>
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <style>
                    input:focus,
                    select:focus,
                    button:focus,
                    a:focus {
                        outline: 2px solid var(--primary);
                        outline-offset: 2px;
                    }

                    /* Slim Select - estilização alinhada à paleta do sistema */
                    #filterPicker {
                        display: block;
                        width: 100%;
                        border-radius: 0.75rem;
                        border: 1px solid rgba(0, 90, 36, 0.6);
                        min-height: 2.5rem;
                        padding: 0.45rem 0.75rem;
                        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
                        background-color: #ffffff;
                        font-size: 0.9rem;
                        color: #374151;
                    }

                    .ss-main {
                        border-radius: 0.75rem;
                        border: 1px solid rgba(0, 90, 36, 0.6);
                        min-height: 2.5rem;
                        padding: 0.15rem 0.6rem;
                        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
                        background-color: #ffffff;
                        font-size: 0.9rem;
                        color: #374151;
                        outline: none;
                    }

                    .ss-main.ss-open,
                    .ss-main.ss-focus,
                    .ss-main:focus,
                    .ss-main:focus-visible {
                        border-color: var(--primary) !important;
                        box-shadow: 0 0 0 3px rgba(0, 90, 36, 0.18) !important;
                        outline: none !important;
                    }

                    .ss-main .ss-values .ss-single {
                        color: #4b5563;
                    }

                    .ss-main .ss-arrow path {
                        stroke: var(--primary);
                    }

                    .ss-content {
                        border-radius: 0.75rem;
                        border-color: rgba(0, 90, 36, 0.35);
                        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
                        overflow: hidden;
                        outline: none;
                    }

                    .ss-content .ss-list .ss-option {
                        font-size: 0.8rem;
                        padding: 0.5rem 0.75rem;
                        color: var(--dark);
                    }

                    .ss-content .ss-list .ss-option.ss-highlighted,
                    .ss-content .ss-list .ss-option.ss-selected,
                    .ss-content .ss-list .ss-option:hover {
                        background-color: rgba(0, 90, 36, 0.08) !important;
                        color: var(--primary) !important;
                    }

                    .ss-content .ss-list .ss-group {
                        font-size: 0.75rem;
                        font-weight: 600;
                        text-transform: uppercase;
                        color: var(--primary);
                        background-color: rgba(0, 90, 36, 0.06);
                        padding: 0.4rem 0.75rem;
                        border-top: 1px solid rgba(0, 90, 36, 0.08);
                    }
                </style>

                <script>
                    function getActiveFilterValues() {
                        const chips = document.querySelectorAll('#activeFilters .filter-chip');
                        const filtros = {
                            cursos: [],
                            segmentos: [],
                            origens: []
                        };

                        chips.forEach(chip => {
                            const tipo = chip.getAttribute('data-tipo');
                            const valor = chip.getAttribute('data-valor') || '';
                            if (!valor) return;

                            if (tipo === 'curso') filtros.cursos.push(valor);
                            if (tipo === 'segmento') filtros.segmentos.push(valor);
                            if (tipo === 'origem') filtros.origens.push(valor);
                        });

                        return filtros;
                    }

                    function addFilterChip() {
                        const select = document.getElementById('filterPicker');
                        if (!select) return;

                        const value = select.value;
                        if (!value) return;

                        const [tipo, raw] = value.split(':');
                        const texto = select.options[select.selectedIndex].text;
                        const container = document.getElementById('activeFilters');
                        if (!container || !tipo || !raw) return;

                        const valor = raw;

                        // Regras por ramo:
                        // - curso: apenas 1 ativo por vez
                        // - segmento / origem: apenas 1 ativo por vez

                        // Evitar chips duplicados (mesmo tipo + valor)
                        const existingSame = container.querySelector(`.filter-chip[data-tipo="${tipo}"][data-valor="${valor}"]`);
                        if (existingSame) {
                            select.value = '';
                            return;
                        }

                        // Para curso, segmento e origem, remove qualquer chip anterior do mesmo tipo
                        if (tipo === 'curso' || tipo === 'segmento' || tipo === 'origem') {
                            const previousOfType = container.querySelectorAll(`.filter-chip[data-tipo="${tipo}"]`);
                            previousOfType.forEach(chip => chip.remove());
                        }

                        const chip = document.createElement('div');
                        chip.className = 'filter-chip inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary border border-primary/30';
                        chip.setAttribute('data-tipo', tipo);
                        chip.setAttribute('data-valor', valor);

                        const span = document.createElement('span');
                        span.textContent = texto;

                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'ml-1 text-primary/70 hover:text-primary focus:outline-none';
                        btn.innerHTML = '&times;';
                        btn.onclick = function() {
                            chip.remove();
                            filterCandidates();
                        };

                        chip.appendChild(span);
                        chip.appendChild(btn);
                        container.appendChild(chip);

                        // Reset select e aplicar filtro
                        select.value = '';
                        filterCandidates();
                    }

                    function filterCandidates() {
                        const searchInput = (document.getElementById('searchInput')?.value || '').toLowerCase();
                        const { cursos, segmentos, origens } = getActiveFilterValues();

                        const tableRows = document.querySelectorAll('tbody tr');
                        const candidateCards = document.querySelectorAll('.candidate-card');

                        // Desktop (tabela)
                        tableRows.forEach(row => {
                            const nomeCell = row.querySelector('td:first-child');
                            const nome = nomeCell ? nomeCell.textContent.toLowerCase() : '';

                            const rowCurso = row.getAttribute('data-curso') || '';
                            const rowSegmento = row.getAttribute('data-segmento') || '';
                            const rowOrigem = row.getAttribute('data-origem') || '';

                            const matchNome = nome.includes(searchInput);
                            const matchCurso = cursos.length === 0 || cursos.includes(rowCurso);
                            const matchSegmento = segmentos.length === 0 || segmentos.includes(rowSegmento);
                            const matchOrigem = origens.length === 0 || origens.includes(rowOrigem);

                            row.style.display = (matchNome && matchCurso && matchSegmento && matchOrigem) ? '' : 'none';
                        });

                        // Mobile (cards)
                        candidateCards.forEach(card => {
                            const nomeAttr = card.getAttribute('data-nome') || '';
                            const nomeH3 = card.querySelector('h3') ? card.querySelector('h3').textContent : '';
                            const nome = (nomeAttr || nomeH3).toLowerCase();

                            const cardCurso = card.getAttribute('data-curso') || '';
                            const cardSegmento = card.getAttribute('data-segmento') || '';
                            const cardOrigem = card.getAttribute('data-origem') || '';

                            const matchNome = nome.includes(searchInput);
                            const matchCurso = cursos.length === 0 || cursos.includes(cardCurso);
                            const matchSegmento = segmentos.length === 0 || segmentos.includes(cardSegmento);
                            const matchOrigem = origens.length === 0 || origens.includes(cardOrigem);

                            card.style.display = (matchNome && matchCurso && matchSegmento && matchOrigem) ? '' : 'none';
                        });
                    }
                </script>

                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <!-- Table for desktop (sm and above) -->
                        <table class="min-w-full text-sm hidden sm:table">
                            <thead>
                                <tr class="bg-gradient-to-r from-primary to-dark text-white">
                                    <th class="px-6 py-4 text-left text-sm font-semibold font-display">Nome</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold font-display">Curso</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold font-display">Segmento</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold font-display">Origem</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold font-display">Data</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold font-display">Cadastrador</th>
                                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                                        <th class="px-6 py-4 text-center text-sm font-semibold font-display">Ações</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">

                                <?php


                                foreach ($candidatos_ativos as $cand) {
                                    $id = $cand['id'] ?? '-';
                                    $nome = $cand['nome'] ?? '-';
                                    $cursoNome = $cand['nome_curso'] ?? '-';
                                    $origem = (isset($cand['publica']) && (int)$cand['publica'] === 1) ? 'Pública' : 'Privada';
                                    $data = $cand['data'] ?? '-';
                                    $cadastradorNome = $cand['nome_user'] ?? '-';

                                    if ($cand['bairro'] == 1) {
                                        $cota = 'BAIRRO';
                                    } else if ($cand['pcd'] == 1) {
                                        $cota = 'PCD';
                                    } else {
                                        $cota = 'AMPLA';
                                    }
                                ?>
                                    <tr class="hover:bg-gradient-to-r hover:from-primary/5 hover:to-accent/10 transition-all duration-200 <?= $cand['status'] == 0 ? 'bg-gray-50 opacity-75' : 'bg-white' ?> group" data-curso="<?= htmlspecialchars((string)$cursoNome) ?>" data-segmento="<?= htmlspecialchars((string)$cota) ?>" data-origem="<?= htmlspecialchars((string)$origem) ?>">
                                        <td class="px-6 py-4">
                                            <div>
                                                <div class="text-sm font-semibold <?= $cand['status'] == 0 ? 'text-gray-400' : 'text-gray-900' ?>"><?= htmlspecialchars((string)$nome) ?></div>
                                                <?php if ($cand['status'] == 0) { ?>
                                                    <div class="text-xs text-gray-500 font-medium">Desativado</div>
                                                <?php } ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm <?= $cand['status'] == 0 ? 'text-gray-400' : 'text-gray-600' ?>">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                                <?= htmlspecialchars((string)$cursoNome) ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?= $cand['status'] == 0 ? 'bg-gray-100 text-gray-500 border border-gray-200' : 'bg-primary/10 text-primary border border-primary/20' ?>">
                                                <?= htmlspecialchars((string)$cota) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?= $origem === 'Pública' ? ($cand['status'] == 0 ? 'bg-gray-200 text-gray-600 border border-gray-300' : 'bg-green-100 text-green-800 border border-green-200') : ($cand['status'] == 0 ? 'bg-gray-100 text-gray-500 border border-gray-200' : 'bg-gray-100 text-gray-600 border border-gray-200') ?>">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                <?= $origem ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm <?= $cand['status'] == 0 ? 'text-gray-400' : 'text-gray-700' ?>">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <?= htmlspecialchars((string)$data) ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm <?= $cand['status'] == 0 ? 'text-gray-400' : 'text-gray-700' ?>">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <?= htmlspecialchars((string)$cadastradorNome) ?>
                                            </div>
                                        </td>
                                        <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                                            <td class="px-6 py-4">
                                                <div class="flex space-x-2 justify-center">
                                                    <a href="../views/editar_candidato.php?id=<?= $id ?>" class="inline-flex items-center <?= $cand['status'] == 0 ? 'bg-gray-700 text-white hover:bg-gray-800' : 'bg-primary text-white hover:bg-dark' ?> px-4 py-2 rounded-lg transition-all duration-300 font-medium text-sm btn-animate focus-ring">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        Editar
                                                    </a>
                                                    <?php if ($cand['status'] == 1) { ?>
                                                        <button type="button" onclick="openInactivateModal(<?= $id ?>, '<?= htmlspecialchars($nome) ?>')" class="inline-flex items-center bg-secondary text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-all duration-300 font-medium text-sm btn-animate focus-ring">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                            Desativar
                                                        </button>
                                                    <?php } else { ?>
                                                        <button type="button" onclick="openActivateModal(<?= $id ?>, '<?= htmlspecialchars($nome) ?>')" class="inline-flex items-center <?= $cand['status'] == 0 ? 'bg-gray-500 text-white hover:bg-gray-600' : 'bg-green-600 text-white hover:bg-green-700' ?> px-4 py-2 rounded-lg transition-all duration-300 font-medium text-sm btn-animate focus-ring">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            Ativar
                                                        </button>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                                <?php if (count($candidatos_ativos) === 0) { ?>
                                    <tr>
                                        <td colspan="<?= isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin' ? '7' : '6' ?>" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                                    </svg>
                                                </div>
                                                <h3 class="text-lg font-semibold text-gray-500 mb-2">Nenhum candidato encontrado</h3>
                                                <p class="text-sm text-gray-400">Adicione candidatos para começar</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <!-- Cards for mobile (below sm) -->
                        <div class="sm:hidden grid gap-6 p-4">
                            <?php foreach ($candidatos_ativos as $cand) {
                                $id = $cand['id'] ?? '-';
                                $nome = $cand['nome'] ?? '-';
                                $cursoNome = $cand['nome_curso'] ?? '-';
                                $origem = (isset($cand['publica']) && (int)$cand['publica'] === 1) ? 'Pública' : 'Privada';
                                $data = $cand['data'] ?? '-';
                                $cadastradorNome = $cand['nome_user'] ?? '-';

                                if ($cand['bairro'] == 1) {
                                    $cota = 'BAIRRO';
                                } else if ($cand['pcd'] == 1) {
                                    $cota = 'PCD';
                                } else {
                                    $cota = 'AMPLA';
                                }
                            ?>
                                <article class="grid-item card-hover candidate-card bg-white rounded-2xl shadow-xl border-0 overflow-hidden group relative<?= (isset($cand['status']) && (int)$cand['status'] === 0 ? ' opacity-80 grayscale' : '') ?>" data-nome="<?= htmlspecialchars($nome) ?>" data-curso="<?= htmlspecialchars($cursoNome) ?>" data-segmento="<?= htmlspecialchars($cota) ?>" data-origem="<?= htmlspecialchars($origem) ?>">
                                    <div class="h-2 w-full bg-gradient-to-r <?= (isset($cand['status']) && (int)$cand['status'] === 0 ? 'from-red-400 to-red-600' : 'from-primary to-secondary') ?>"></div>
                                    <?php if (isset($cand['status']) && (int)$cand['status'] === 0) { ?>
                                        <span class="absolute top-3 right-3 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200 shadow-sm">Desativado</span>
                                    <?php } ?>
                                    <div class="p-8">
                                        <div class="text-center mb-8">
                                            <div class="w-16 h-16 bg-gradient-to-br from-primary to-dark rounded-full flex items-center justify-center mx-auto mb-4">
                                                <span class="text-white font-bold text-xl"><?= strtoupper(substr($nome, 0, 1)) ?></span>
                                            </div>
                                            <h3 class="text-xl font-bold leading-tight font-display group-hover:scale-105 transition-all duration-300 text-primary"><?= htmlspecialchars((string)$nome) ?></h3>
                                            <div class="w-16 h-0.5 mx-auto mt-3 rounded-full bg-primary/40"></div>
                                        </div>
                                        <div class="space-y-3 mb-6">
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <span class="font-medium">ID:</span>
                                                <span class="ml-2"><?= htmlspecialchars((string)$id) ?></span>
                                            </div>
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                                <span class="font-medium">Curso:</span>
                                                <span class="ml-2 truncate"><?= htmlspecialchars((string)$cursoNome) ?></span>
                                            </div>
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                </svg>
                                                <span class="font-medium">Segmento:</span>
                                                <span class="ml-2 px-2 py-1 bg-primary/10 text-primary rounded-full text-xs font-medium"><?= htmlspecialchars((string)$cota) ?></span>
                                            </div>
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                                                </svg>
                                                <span class="font-medium">Origem:</span>
                                                <span class="ml-2 px-2 py-1 bg-primary/10 text-primary rounded-full text-xs font-medium"><?= $origem ?></span>
                                            </div>
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <span class="font-medium">Data:</span>
                                                <span class="ml-2"><?= htmlspecialchars((string)$data) ?></span>
                                            </div>
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <span class="font-medium">Cadastrador:</span>
                                                <span class="ml-2 truncate"><?= htmlspecialchars((string)$cadastradorNome) ?></span>
                                            </div>
                                        </div>
                                        <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') { ?>
                                            <div class="flex space-x-2">
                                                <a href="../views/editar_candidato.php?id=<?= $id ?>" class="flex-1 bg-primary text-white py-2 px-4 rounded-lg hover:bg-dark transition-all duration-300 font-medium text-sm btn-animate focus-ring">
                                                    <span class="flex items-center justify-center">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        Editar
                                                    </span>
                                                </a>
                                                <?php if (!isset($cand['status']) || (int)$cand['status'] === 1) { ?>
                                                    <button onclick="openInactivateModal(<?= $id ?>, '<?= htmlspecialchars($nome) ?>')" class="flex-1 bg-secondary text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition-all duration-300 font-medium text-sm btn-animate focus-ring">
                                                        <span class="flex items-center justify-center">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                            Desativar
                                                        </span>
                                                    </button>
                                                <?php } else { ?>
                                                    <button onclick="openActivateModal(<?= $id ?>, '<?= htmlspecialchars($nome) ?>')" class="flex-1 bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-all duration-300 font-medium text-sm btn-animate focus-ring">
                                                        <span class="flex items-center justify-center">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            Ativar
                                                        </span>
                                                    </button>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </article>
                            <?php } ?>
                            <?php if (count($candidatos_ativos) === 0) { ?>
                                <div class="text-center text-gray-500 py-8">Nenhum candidato encontrado.</div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Confirmar Desativação -->
    <div id="modalInactivateConfirm" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modalInactivateConfirmContent">
            <form id="inactivateForm" method="POST" action="../controllers/controller_candidato.php" class="p-6 sm:p-8">
                <input type="hidden" name="tipo" value="desabilitar">
                <input type="hidden" name="id_excluir" id="inactivateCandidatoId" value="">
                <div class="text-center">
                    <div class="w-20 h-20 rounded-full bg-secondary/20 flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M4.93 4.93l14.14 14.14"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-dark font-heading mb-4">Confirmar Desativação</h3>
                    <p class="text-gray-600 text-base mb-4 leading-relaxed">
                        Tem certeza que deseja desativar o candidato <span class="font-semibold text-dark" id="inactivateCandidatoName"></span>?
                    </p>
                </div>
                <div class="mb-4 text-left">
                    <label for="inactivateReason" class="block text-sm font-semibold text-gray-700 mb-2">Motivo da desativação *</label>
                    <div class="relative">
                        <select id="inactivateReason" name="motivo" required class="w-full appearance-none px-4 py-3.5 border border-gray-300 rounded-xl text-xs sm:text-sm bg-white text-dark shadow-sm hover:border-primary hover:shadow focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                            <option value="" selected disabled>SELECIONE UM MOTIVO</option>
                            <option value="DESISTÊNCIA VOLUNTÁRIA">DESISTÊNCIA VOLUNTÁRIA</option>
                            <option value="DOCUMENTO INVÁLIDO">DOCUMENTO INVÁLIDO</option>
                            <option value="INFORMAÇÃO INCONSISTENTE">INFORMAÇÃO INCONSISTENTE</option>
                            <option value="TRANSFERÊNCIA ESCOLAR">TRANSFERÊNCIA ESCOLAR</option>
                            <option value="SOLICITAÇÃO DA FAMÍLIA">SOLICITAÇÃO DA FAMÍLIA</option>
                            <option value="ERRO DE CADASTRO">ERRO DE CADASTRO</option>
                            <option value="VAGA DUPLICADA">VAGA DUPLICADA</option>
                            <option value="outros">OUTROS (ESPECIFIQUE)</option>
                        </select>
                        <svg class="w-5 h-5 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 8l4 4 4-4" />
                        </svg>
                    </div>
                    <div id="inactivateReasonOtherContainer" class="mt-3 hidden">
                        <label for="inactivateReasonOther" class="block text-sm font-semibold text-gray-700 mb-2">Descreva o motivo *</label>
                        <input id="inactivateReasonOther" name="motivo_outros" type="text" placeholder="Descreva o motivo" class="w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 text-base" />
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <button type="button" class="px-6 py-3 rounded-xl border-2 border-secondary font-semibold text-secondary hover:bg-accent hover:border-secondary transition-all text-base focus-ring" onclick="closeModal('modalInactivateConfirm')">Cancelar</button>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-secondary to-orange-600 text-white font-semibold rounded-xl hover:from-secondary/90 hover:to-orange-700 transition-all text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus-ring">Confirmar Desativação</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Confirmar Ativação -->
    <div id="modalActivateConfirm" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modalActivateConfirmContent">
            <div class="p-6 sm:p-8 text-center">
                <div class="w-20 h-20 rounded-full bg-secondary/20 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M4.93 4.93l14.14 14.14"></path>
                    </svg>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-dark font-heading mb-4">Confirmar Ativação</h3>
                <p class="text-gray-600 text-base mb-6 leading-relaxed">
                    Tem certeza que deseja ativar o candidato <span class="font-semibold text-dark" id="activateCandidatoName"></span>?
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <button type="button" class="px-6 py-3 rounded-xl border-2 border-secondary font-semibold text-secondary hover:bg-accent hover:border-secondary transition-all text-base focus-ring" onclick="closeModal('modalActivateConfirm')">Cancelar</button>
                    <a id="activateLink" href="#" class="px-6 py-3 bg-gradient-to-r from-secondary to-orange-600 text-white font-semibold rounded-xl hover:from-secondary/90 hover:to-orange-700 transition-all text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus-ring text-center">Confirmar Ativação</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Exclusão -->
    <div id="modalDeleteCandidato" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modalDeleteCandidatoContent">
            <div class="p-6 sm:p-8 text-center">
                <div class="w-20 h-20 rounded-full bg-secondary/20 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M4.93 4.93l14.14 14.14"></path>
                    </svg>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-dark font-heading mb-4">Confirmar Exclusão</h3>
                <p class="text-gray-600 text-base mb-6 leading-relaxed">
                    Tem certeza que deseja excluir o candidato <span class="font-semibold text-dark" id="deleteCandidatoName"></span>?
                </p>
                <p class="text-sm text-secondary bg-secondary/10 px-4 py-3 rounded-lg border border-secondary/20 mb-6">
                    Esta ação não pode ser desfeita.
                </p>
                <form id="deleteCandidatoForm" action="../controllers/controller_candidato.php" method="POST">

                    <input type="hidden" name="form" value="candidato">
                    <input type="hidden" id="deleteCandidatoId" name="id_candidato" value="">
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button type="button" class="px-6 py-3 rounded-xl border-2 border-secondary font-semibold text-secondary hover:bg-accent hover:border-secondary transition-all text-base focus-ring" onclick="closeModal('modalDeleteCandidato')">Cancelar</button>
                        <button type="submit" name="acao" value="delete" class="px-6 py-3 bg-gradient-to-r from-secondary to-orange-600 text-white font-semibold rounded-xl hover:from-secondary/90 hover:to-orange-700 transition-all text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus-ring">Excluir Candidato</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Relatórios -->
    <div id="modalRelatorios" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="modalRelatoriosContent">
            <div class="bg-gradient-to-r from-primary to-dark text-white p-6">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/30 shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold font-display">Gerar Relatórios PDF</h2>
                        <p class="text-white/90 text-sm mt-1">Crie documentos em PDF com dados do sistema</p>
                    </div>
                </div>
                <button class="absolute top-6 right-6 p-2 rounded-xl hover:bg-white/10 transition-all group" onclick="closeModal('modalRelatorios')">
                    <svg class="w-5 h-5 text-white group-hover:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <form action="../controllers/controller_relatorios.php" method="POST" class="space-y-6">
                    <input type="hidden" name="form" value="relatorio_pdf">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Tipo de Relatório *</label>
                        <select name="tipo_relatorio" id="tipo_relatorio" required class="w-full px-4 py-3.5 border border-gray-300 rounded-xl input-modern focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-base">
                            <option value="" selected disabled>Selecione o tipo de relatório</option>
                            <option value="privada_ac">Privada AC</option>
                            <option value="privada_cotas">Privada Cotas</option>
                            <option value="privada_geral">Privada Geral</option>
                            <option value="publica_ac">Pública AC</option>
                            <option value="publica_cotas">Publica Cotas</option>
                            <option value="publica_geral">Pública Geral</option>
                            <option value="comissao_selecao">Comissão de Seleção</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3" id="label_curso">Curso (Opcional)</label>
                        <select name="curso_id" id="curso_id" class="w-full px-4 py-3.5 border border-gray-300 rounded-xl input-modern focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-base">
                            <option value="">Todos os cursos</option>
                            <?php foreach ($cursos as $curso) { ?>
                                <option value="<?= htmlspecialchars($curso['id']) ?>"><?= htmlspecialchars($curso['nome_curso']) ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <button type="submit" class="w-full bg-gradient-to-r from-primary to-dark text-white px-6 py-3.5 rounded-xl hover:from-dark hover:to-primary btn-animate font-semibold shadow-lg focus-ring transition-all text-base">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Gerar PDF
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Resultados -->
    <div id="modalResultados" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="modalRelatoriosContent">
            <div class="bg-gradient-to-r from-primary to-dark text-white p-6">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/30 shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0h2a2 2 0 002 2v2a2 2 0 00-2 2H9a2 2 0 00-2-2v-2a2 2 0 00-2-2zm2 0h2v2a2 2 0 002 2H9a2 2 0 00-2-2v-2a2 2 0 002-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold font-display">Consultar Resultados</h2>
                        <p class="text-white/90 text-sm mt-1">Visualize rankings e estatísticas do sistema</p>
                    </div>
                </div>
                <button class="absolute top-6 right-6 p-2 rounded-xl hover:bg-white/10 transition-all group" onclick="closeModal('modalResultados')">
                    <svg class="w-5 h-5 text-white group-hover:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <form action="../controllers/controller_relatorios.php" method="POST" class="space-y-6">
                    <input type="hidden" name="form" value="resultados">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Tipo de Consulta *</label>
                        <select name="tipo_consulta" required class="w-full px-4 py-3.5 border border-gray-300 rounded-xl input-modern focus:border-secondary focus:ring-4 focus:ring-secondary/10 transition-all text-base">
                            <option value="" selected disabled>Selecione o tipo de consulta</option>
                            <option value="classificados">Classificados</option>
                            <option value="classificaveis">Classificáveis</option>
                            <option value="resultado_final">Resultado Final</option>
                            <option value="resultado_preliminar">Resultado Preliminar</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Curso (Opcional)</label>
                        <select name="curso_id" class="w-full px-4 py-3.5 border border-gray-300 rounded-xl input-modern focus:border-secondary focus:ring-4 focus:ring-secondary/10 transition-all text-base">
                            <option value="">Todos os cursos</option>
                            <?php foreach ($cursos as $curso) { ?>
                                <option value="<?= htmlspecialchars($curso['id']) ?>"><?= htmlspecialchars($curso['nome_curso']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="pt-4 border-t border-gray-200">
                        <button type="submit" class="w-full bg-gradient-to-r from-primary to-dark text-white px-6 py-3.5 rounded-xl hover:from-dark hover:to-primary btn-animate font-semibold shadow-lg focus-ring transition-all text-base">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Consultar Resultados
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/slim-select@2.8.1/dist/slimselect.min.js"></script>
    <script>
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

        function openInactivateModal(id, nome) {
            document.getElementById('inactivateCandidatoName').textContent = nome;
            document.getElementById('inactivateCandidatoId').value = id;
            // Reset form
            document.getElementById('inactivateForm').reset();
            document.getElementById('inactivateCandidatoId').value = id;
            document.getElementById('inactivateReasonOtherContainer').classList.add('hidden');
            document.getElementById('inactivateReasonOther').removeAttribute('required');
            openModal('modalInactivateConfirm');
        }

        function openActivateModal(id, nome) {
            document.getElementById('activateCandidatoName').textContent = nome;
            document.getElementById('activateLink').href = `../controllers/controller_candidato.php?id_excluir=${id}&tipo=ativar`;
            openModal('modalActivateConfirm');
        }

        // Event delegation for edit and delete buttons
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.bg-white.rounded-2xl');

            container.addEventListener('click', function(e) {
                const target = e.target;

                // Handle Delete button
                if (target.classList.contains('delete-candidato')) {
                    const id = target.dataset.id || '';
                    const nome = target.dataset.nome || '';
                    document.getElementById('deleteCandidatoName').textContent = nome;
                    document.getElementById('deleteCandidatoId').value = id;
                    openModal('modalDeleteCandidato');
                }
            });

            // Form validation
            const form = document.getElementById('candidatoForm');
            form.addEventListener('submit', function(e) {
                const nome = document.getElementById('inpCandidatoNome');
                const curso = document.getElementById('inpCandidatoCurso');
                if (!nome.value.trim() || !curso.value) {
                    e.preventDefault();
                    alert('Por favor, preencha todos os campos obrigatórios.');
                }
            });
        });

        // Toggle 'Outros' reason text field in desativation modal
        document.addEventListener('DOMContentLoaded', function() {
            const reasonSelect = document.getElementById('inactivateReason');
            const otherContainer = document.getElementById('inactivateReasonOtherContainer');
            const otherInput = document.getElementById('inactivateReasonOther');
            const inactivateForm = document.getElementById('inactivateForm');

            if (reasonSelect && otherContainer && otherInput) {
                reasonSelect.addEventListener('change', function() {
                    if (this.value === 'outros') {
                        otherContainer.classList.remove('hidden');
                        otherInput.setAttribute('required', 'required');
                        otherInput.focus();
                    } else {
                        otherContainer.classList.add('hidden');
                        otherInput.removeAttribute('required');
                        otherInput.value = '';
                    }
                });
            }

            // Validação do formulário de desativação
            if (inactivateForm) {
                inactivateForm.addEventListener('submit', function(e) {
                    const motivo = document.getElementById('inactivateReason').value;
                    const motivoOutros = document.getElementById('inactivateReasonOther').value;

                    if (!motivo) {
                        e.preventDefault();
                        alert('Por favor, selecione um motivo para a desativação.');
                        return false;
                    }

                    if (motivo === 'outros' && !motivoOutros.trim()) {
                        e.preventDefault();
                        alert('Por favor, descreva o motivo da desativação.');
                        document.getElementById('inactivateReasonOther').focus();
                        return false;
                    }
                });
            }
        });

        // Controle do select de cursos baseado no tipo de relatório
        document.addEventListener('DOMContentLoaded', function() {
            const tipoRelatorio = document.getElementById('tipo_relatorio');
            const cursoSelect = document.getElementById('curso_id');
            const labelCurso = document.getElementById('label_curso');

            if (tipoRelatorio && cursoSelect && labelCurso) {
                tipoRelatorio.addEventListener('change', function() {
                    if (this.value === 'comissao_selecao') {
                        // Desabilitar e limpar o select de cursos
                        cursoSelect.disabled = true;
                        cursoSelect.value = '';
                        cursoSelect.classList.add('input-disabled');
                        labelCurso.textContent = 'Curso (Não aplicável)';
                        labelCurso.classList.add('text-gray-400');
                    } else {
                        // Habilitar o select de cursos
                        cursoSelect.disabled = false;
                        cursoSelect.classList.remove('input-disabled');
                        labelCurso.textContent = 'Curso (Obrigatório)';
                        labelCurso.classList.remove('text-gray-400');
                        labelCurso.classList.add('text-red-600');
                    }
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const filterPicker = document.getElementById('filterPicker');
            if (filterPicker && typeof SlimSelect !== 'undefined') {
                new SlimSelect({
                    select: filterPicker,
                    settings: {
                        showSearch: false
                    }
                });
            }
        });
    </script>
</body>

</html>