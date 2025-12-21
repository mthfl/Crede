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

$matriculas = $select->select_matriculas(); // Deve retornar: id, curso_id, nome_curso, data, hora_inicio, hora_fim
$cursos = $select->select_cursos(); // Para o select do formulário
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
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        /* (Mantive todos os estilos anteriores - omitidos aqui por brevidade, mas estão iguais ao seu código original) */
        /* Inclua aqui todos os <style> que você já tinha (sidebar, select2, animações, etc.) */
        /* Para não repetir tudo, assumo que você vai colar o bloco <style> completo do seu código original */

        .grid-responsive {
            grid-template-columns: 1fr;
        }

        @media (min-width: 768px) {
            .grid-responsive {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .grid-responsive {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Estilização do Select2 para o select de Curso */
        .select2-curso + .select2-container .select2-selection--single {
            height: 3rem;
            min-height: 3rem;
            border-radius: 0.75rem;
            border: 1px solid #d1d5db; /* border-gray-300 */
            padding: 0.5rem 0.75rem;
            display: flex;
            align-items: center;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
            background-color: #ffffff;
        }

        .select2-curso + .select2-container .select2-selection--single .select2-selection__rendered {
            padding-left: 0;
            color: #111827; /* text-gray-900 */
            font-size: 0.95rem;
        }

        .select2-curso + .select2-container .select2-selection--single .select2-selection__placeholder {
            color: #6b7280; /* text-gray-500 */
        }

        .select2-curso + .select2-container .select2-selection--single .select2-selection__arrow {
            height: 100%;
            right: 0.75rem;
        }

        .select2-curso + .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-curso + .select2-container .select2-selection--single:focus {
            border-color: #005A24; /* primary */
            box-shadow: 0 0 0 4px rgba(0, 90, 36, 0.12);
            outline: none;
        }

        .select2-dropdown {
            border-radius: 0.75rem;
            border-color: #d1d5db;
            overflow: hidden;
        }

        .select2-results__option {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
            color: #111827; /* text-gray-900 */
        }

        /* Item selecionado na lista */
        .select2-container--default .select2-results__option--selected {
            background-color: #E6F4EA !important; /* accent bem claro em vez do azul padrão */
            color: #005A24 !important; /* primary */
        }

        /* Hover / highlight do item (seta/teclado ou mouse) */
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #005A24 !important; /* primary */
            color: #ffffff !important;
        }
    </style>
</head>

<body class="bg-white min-h-screen font-body">
    <div id="overlay" class="overlay fixed inset-0 bg-black/30 z-40 lg:hidden"></div>
    <div class="flex h-screen bg-gray-50 overflow-hidden">
        <?php include __DIR__ . '/partials/sidebar.php'; ?>
        <div class="main-content flex-1 h-screen overflow-y-auto custom-scrollbar bg-white">
            <header class="bg-white shadow-sm border-b border-gray-200 z-30 sticky top-0">
                <!-- Header mantido igual -->
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
                <div class="mb-8 text-center">
                    <h1 class="text-primary text-3xl md:text-4xl font-bold tracking-tight font-heading">
                        <i class="fas fa-calendar-alt mr-3 text-secondary"></i>
                        Gerenciar Matrículas
                    </h1>
                    <p class="text-gray-600 text-base md:text-lg mt-2 max-w-2xl mx-auto">
                        Configure os dias e períodos de abertura para matrículas por curso
                    </p>
                </div>

                <!-- Mensagens de feedback -->
                <?php if (isset($_GET['sucesso'])): ?>
                    <div class="max-w-5xl mx-auto mb-6 rounded-2xl border border-green-200 bg-green-50 px-6 py-4 text-green-800 shadow-sm">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                                <i class="fas fa-check text-green-700"></i>
                            </div>
                            <div>Matrícula(s) adicionada(s) com sucesso!</div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['erro'])): ?>
                    <div class="max-w-5xl mx-auto mb-6 rounded-2xl border border-red-200 bg-red-50 px-6 py-4 text-red-800 shadow-sm">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                                <i class="fas fa-triangle-exclamation text-red-700"></i>
                            </div>
                            <div>Erro ao processar matrícula(s). Revise os campos.</div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['excluido'])): ?>
                    <div class="max-w-5xl mx-auto mb-6 rounded-2xl border border-green-200 bg-green-50 px-6 py-4 text-green-800 shadow-sm">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                                <i class="fas fa-check text-green-700"></i>
                            </div>
                            <div>Matrícula excluída com sucesso!</div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="max-w-5xl mx-auto space-y-8">

                    <!-- Formulário de Nova Matrícula -->
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200/70 overflow-hidden">
                        <div class="px-6 sm:px-8 py-6 border-b border-gray-100">
                            <h2 class="text-lg sm:text-xl font-bold text-primary">
                                <i class="fas fa-plus-circle mr-2 text-secondary"></i> Adicionar Nova Configuração
                            </h2>
                            <p class="text-sm text-gray-600 mt-2">Defina curso, data e período de abertura das matrículas.</p>
                        </div>

                        <form action="../controllers/controller_matricula.php" method="POST" class="p-6 sm:p-8">
                            <div id="dias-matricula-container" class="space-y-6">
                                <div class="dia-matricula-item bg-gray-50 p-6 rounded-2xl border border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-5">Dia de Matrícula 1</h3>
                                    <div class="grid grid-responsive gap-6">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-book mr-2 text-primary"></i> Curso
                                            </label>
                                            <select name="dias_matricula[0][curso_id]" class="select2-curso w-full" required>
                                                <option value="">Selecione um curso</option>
                                                <option value="0">TODOS OS CURSOS</option>
                                                <?php foreach ($cursos as $curso): ?>
                                                    <option value="<?= htmlspecialchars($curso["id"]) ?>">
                                                        <?= htmlspecialchars($curso["nome_curso"]) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-calendar mr-2 text-primary"></i> Data
                                            </label>
                                            <input type="date" name="dias_matricula[0][data]" required min="<?= date('Y-m-d') ?>"
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-primary focus:ring-4 focus:ring-[rgba(0,90,36,0.12)]">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-clock mr-2 text-primary"></i> Hora Início
                                            </label>
                                            <input type="time" name="dias_matricula[0][hora_inicio]" required
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-primary focus:ring-4 focus:ring-[rgba(0,90,36,0.12)]">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-clock mr-2 text-primary"></i> Hora Fim
                                            </label>
                                            <input type="time" name="dias_matricula[0][hora_fim]" required
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-primary focus:ring-4 focus:ring-[rgba(0,90,36,0.12)]">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex flex-col sm:flex-row justify-start gap-4">
                                <button type="submit" name="adicionar_matricula"
                                        class="w-full sm:w-auto bg-gradient-to-r from-primary to-dark text-white py-3 px-8 rounded-xl hover:from-dark hover:to-primary font-semibold shadow-lg order-2 sm:order-1">
                                    <i class="fas fa-save mr-2"></i> Salvar Configurações
                                </button>
                                <button type="reset"
                                        class="w-full sm:w-auto px-6 py-3 rounded-xl border border-secondary bg-secondary text-white hover:bg-orange-500 hover:border-orange-500 font-semibold order-1 sm:order-2">
                                    <i class="fas fa-redo mr-2"></i> Limpar
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Lista de Matrículas Cadastradas -->
                    <?php if (!empty($matriculas)): ?>
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-200/70 overflow-hidden">
                            <div class="px-6 sm:px-8 py-6 border-b border-gray-100 flex items-center justify-between">
                                <h2 class="text-lg sm:text-xl font-bold text-primary">
                                    <i class="fas fa-list mr-2 text-secondary"></i> Matrículas Cadastradas
                                </h2>
                                <span class="text-sm text-gray-500"><?= count($matriculas) ?> registro(s)</span>
                            </div>
                            <div class="divide-y divide-gray-100">
                                <?php foreach ($matriculas as $matricula): ?>
                                    <div class="px-6 sm:px-8 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                        <div class="min-w-0 flex-1">
                                            <p class="font-semibold text-gray-900 truncate">
                                                <?php if ($matricula['nome_curso']): ?>
                                                    <?= htmlspecialchars($matricula['nome_curso']) ?>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center rounded-full bg-secondary/10 text-secondary px-3 py-1 text-xs font-semibold">
                                                        TODOS OS CURSOS
                                                    </span>
                                                <?php endif; ?>
                                            </p>
                                            <div class="mt-3 flex flex-wrap gap-3 text-sm text-gray-600">
                                                <span class="inline-flex items-center gap-2 rounded-lg bg-gray-50 border border-gray-200 px-3 py-1.5">
                                                    <i class="fas fa-calendar text-primary"></i>
                                                    <?= date('d/m/Y', strtotime($matricula['data'])) ?>
                                                </span>
                                                <span class="inline-flex items-center gap-2 rounded-lg bg-gray-50 border border-gray-200 px-3 py-1.5">
                                                    <i class="fas fa-clock text-primary"></i>
                                                    das <?= date('H:i', strtotime($matricula['hora_inicio'])) ?>
                                                    às <?= date('H:i', strtotime($matricula['hora_fim'])) ?>
                                                </span>
                                            </div>
                                        </div>
                                        <button type="button" onclick="openDeleteMatriculaModal(<?= (int)$matricula['id'] ?>)"
                                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 text-sm font-semibold">
                                            <i class="fas fa-trash"></i> Excluir
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-200/70 overflow-hidden text-center p-12">
                            <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
                            <p class="text-xl font-semibold text-gray-800">Nenhuma matrícula cadastrada</p>
                            <p class="text-gray-600 mt-2">Adicione as primeiras configurações acima.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <script>
        let diaMatriculaCount = 1;

        $(document).ready(function() {
            // Inicializa Select2 nos selects de curso
            $('.select2-curso').select2({
                placeholder: 'Selecione um curso',
                allowClear: true
            });

            // Garante que o botão "Limpar" reseta também o Select2
            const formMatricula = document.querySelector('form[action="../controllers/controller_matricula.php"]');
            if (formMatricula) {
                formMatricula.addEventListener('reset', function() {
                    // dá um pequeno delay para garantir que o reset padrão rode antes
                    setTimeout(function() {
                        $('.select2-curso').val(null).trigger('change');
                    }, 0);
                });
            }
        });

        function adicionarDiaMatricula() {
            const container = document.getElementById('dias-matricula-container');
            const novoDia = document.createElement('div');
            novoDia.className = 'dia-matricula-item bg-gray-50 p-6 rounded-2xl border border-gray-200 horario-item';

            novoDia.innerHTML = `
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-semibold text-gray-900">Dia de Matrícula ${diaMatriculaCount + 1}</h3>
                    <button type="button" onclick="removerDiaMatricula(this)" 
                            class="px-4 py-2 rounded-xl border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 text-sm font-semibold">
                        <i class="fas fa-trash mr-1"></i> Remover
                    </button>
                </div>
                <div class="grid grid-responsive gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-book mr-2 text-primary"></i> Curso
                        </label>
                        <select name="dias_matricula[${diaMatriculaCount}][curso_id]" class="select2-curso w-full" required>
                            <option value="">Selecione um curso</option>
                            <option value="0">TODOS OS CURSOS</option>
                            <?php foreach ($cursos as $curso): ?>
                                <option value="<?= htmlspecialchars($curso["id"]) ?>"><?= htmlspecialchars($curso["nome_curso"]) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar mr-2 text-primary"></i> Data
                        </label>
                        <input type="date" name="dias_matricula[${diaMatriculaCount}][data]" required min="<?= date('Y-m-d') ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-primary focus:ring-4 focus:ring-[rgba(0,90,36,0.12)]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-clock mr-2 text-primary"></i> Hora Início
                        </label>
                        <input type="time" name="dias_matricula[${diaMatriculaCount}][hora_inicio]" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-primary focus:ring-4 focus:ring-[rgba(0,90,36,0.12)]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-clock mr-2 text-primary"></i> Hora Fim
                        </label>
                        <input type="time" name="dias_matricula[${diaMatriculaCount}][hora_fim]" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-primary focus:ring-4 focus:ring-[rgba(0,90,36,0.12)]">
                    </div>
                </div>
            `;

            container.appendChild(novoDia);
            diaMatriculaCount++;

            $(novoDia).find('.select2-curso').select2({
                placeholder: 'Selecione um curso',
                allowClear: true
            });
        }

        function removerDiaMatricula(button) {
            const container = document.getElementById('dias-matricula-container');
            if (container.children.length > 1) {
                const item = button.closest('.dia-matricula-item');
                $(item).find('.select2-curso').select2('destroy');
                item.remove();

                container.querySelectorAll('.dia-matricula-item').forEach((el, i) => {
                    el.querySelector('h3').textContent = `Dia de Matrícula ${i + 1}`;
                });
            } else {
                alert('É obrigatório manter pelo menos um dia de matrícula.');
            }
        }

        // Sidebar e modal (mantidos iguais)
        const openSidebar = document.getElementById('openSidebar');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const closeSidebar = document.getElementById('closeSidebar');

        if (openSidebar) openSidebar.onclick = () => { sidebar.classList.add('open'); overlay.classList.add('show'); };
        if (closeSidebar) closeSidebar.onclick = () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); };
        if (overlay) overlay.onclick = () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); };

        function openDeleteMatriculaModal(id) {
            document.getElementById('deleteMatriculaId').value = id;
            document.getElementById('deleteMatriculaModal').classList.remove('hidden');
            document.getElementById('deleteMatriculaModal').classList.add('flex');
        }

        function closeDeleteMatriculaModal() {
            document.getElementById('deleteMatriculaModal').classList.add('hidden');
            document.getElementById('deleteMatriculaModal').classList.remove('flex');
        }

        function confirmDeleteMatricula() {
            document.getElementById('deleteMatriculaForm').submit();
        }
    </script>

    <!-- Modal de exclusão -->
    <div id="deleteMatriculaModal" class="hidden fixed inset-0 z-50 items-center justify-center">
        <div class="absolute inset-0 bg-black/40" onclick="closeDeleteMatriculaModal()"></div>
        <div class="relative w-[92%] max-w-md rounded-2xl bg-white shadow-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b flex justify-between items-center">
                <h3 class="text-lg font-bold">Confirmar exclusão</h3>
                <button onclick="closeDeleteMatriculaModal()" class="w-10 h-10 rounded-xl border hover:bg-gray-50">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            <div class="px-6 py-6 text-center">
                <p class="text-gray-800 font-semibold">Tem certeza que deseja excluir esta configuração de matrícula?</p>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3">
                <button onclick="closeDeleteMatriculaModal()" class="px-5 py-2.5 rounded-xl border bg-white hover:bg-gray-100 font-semibold">
                    Cancelar
                </button>
                <form id="deleteMatriculaForm" action="../controllers/controller_matricula.php" method="POST" class="inline">
                    <input type="hidden" name="acao" value="excluir_matricula">
                    <input type="hidden" id="deleteMatriculaId" name="id_matricula" value="">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-600 text-white hover:bg-red-700 font-semibold">
                        <i class="fas fa-trash mr-2"></i> Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>