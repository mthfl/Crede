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

function fmt($v) { return htmlspecialchars((string)$v); }
function simnao($v) { return ((int)$v) === 1 ? 'Sim' : 'Não'; }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Editar Candidato</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<style>
		:root { --primary: <?= $cursoCor ?>; --secondary: #FFA500; }
		* { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
		.focus-ring:focus { outline: 2px solid var(--secondary); outline-offset: 2px; }
		.input-modern { transition: all .2s ease; }
		.input-modern:focus { box-shadow: 0 0 0 3px <?= "rgba(0,0,0,0.08)" ?>; }
		.compact-table th, .compact-table td { padding: 0.5rem 0.5rem !important; }
	</style>
</head>
<body class="bg-gray-50 min-h-screen">
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
								<?= $cursoNome ? 'Curso: '.fmt($cursoNome) : 'Sistema de Seleção Escolar' ?>
							</p>
						</div>
					</div>
				</div>

				<form action="../controllers/controller_candidato.php" method="post" class="p-6 space-y-8">
					<input type="hidden" name="form" value="candidato">
					<input type="hidden" name="acao" value="update">
					<input type="hidden" name="id_candidato" value="<?= fmt($id) ?>">
					<!-- Dados do candidato -->
					<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
						<div>
							<label class="block text-sm font-medium text-gray-600 mb-1">Nome</label>
							<input type="text" name="nome" value="<?= fmt($candidato['nome'] ?? '') ?>" class="w-full px-4 py-3.5 border border-gray-200 rounded-xl bg-white text-gray-800 input-modern">
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-600 mb-1">Data de Nascimento</label>
							<input type="text" name="data_nascimento" value="<?= fmt($candidato['data_nascimento'] ?? '') ?>" class="w-full px-4 py-3.5 border border-gray-200 rounded-xl bg-white text-gray-800 input-modern">
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-600 mb-1">Tipo de Escola</label>
							<select name="tipo_escola" class="w-full px-4 py-3.5 border border-gray-200 rounded-xl bg-white text-gray-800 input-modern">
								<?php $isPublica = (int)($candidato['publica'] ?? 0) === 1; ?>
								<option value="publica" <?= $isPublica ? 'selected' : '' ?>>Escola Pública</option>
								<option value="privada" <?= !$isPublica ? 'selected' : '' ?>>Escola Privada</option>
							</select>
						</div>
						<div class="flex items-center gap-3">
							<input type="checkbox" id="pcd" name="pcd" class="w-5 h-5 text-emerald-600 border-gray-300 rounded input-modern" <?= ((int)($candidato['pcd'] ?? 0) === 1) ? 'checked' : '' ?>>
							<label for="pcd" class="text-sm font-medium text-gray-700">PCD</label>
						</div>
						<div class="flex items-center gap-3">
							<input type="checkbox" id="cotas" name="cotas" class="w-5 h-5 text-emerald-600 border-gray-300 rounded input-modern" <?= ((int)($candidato['bairro'] ?? 0) === 1) ? 'checked' : '' ?>>
							<label for="pcd" class="text-sm font-medium text-gray-700">Cotas</label>
						</div>
					</div>

					<!-- Tabela de notas 6º-8º -->
					<div>
						<h3 class="text-lg font-semibold text-gray-800 mb-4">Notas 6º ao 8º Ano</h3>
						<div class="overflow-x-auto">
							<table class="w-full border-collapse compact-table text-sm">
								<thead>
									<tr class="bg-gray-100">
										<th class="border border-gray-300 px-4 py-3 text-left">Matéria</th>
										<th class="border border-gray-300 px-4 py-3 text-center">6º Ano</th>
										<th class="border border-gray-300 px-4 py-3 text-center">7º Ano</th>
										<th class="border border-gray-300 px-4 py-3 text-center">8º Ano</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$map = [
										['Português','n6_portuguesa','n7_portuguesa','n8_portuguesa','portugues_6','portugues_7','portugues_8'],
										['Matemática','n6_matematica','n7_matematica','n8_matematica','matematica_6','matematica_7','matematica_8'],
										['História','n6_historia','n7_historia','n8_historia','historia_6','historia_7','historia_8'],
										['Geografia','n6_geografia','n7_geografia','n8_geografia','geografia_6','geografia_7','geografia_8'],
										['Ciências','n6_ciencias','n7_ciencias','n8_ciencias','ciencias_6','ciencias_7','ciencias_8'],
										['Inglês','n6_inglesa','n7_inglesa','n8_inglesa','ingles_6','ingles_7','ingles_8'],
										['Artes','n6_artes','n7_artes','n8_artes','artes_6','artes_7','artes_8'],
										['Educação Física','n6_educacao_fisica','n7_educacao_fisica','n8_educacao_fisica','edfisica_6','edfisica_7','edfisica_8'],
										['Religião','n6_religiao','n7_religiao','n8_religiao','religiao_6','religiao_7','religiao_8'],
									];
									foreach ($map as $row) {
										[$label,$a6,$a7,$a8,$n6,$n7,$n8] = $row;
										echo '<tr class="hover:bg-gray-50">';
										echo '<td class="border border-gray-300 px-4 py-3">'.fmt($label).'</td>';
										echo '<td class="border border-gray-300 px-2 py-2 text-center">';
										echo '<input type="text" name="'.$n6.'" value="'.fmt($candidato[$a6] ?? '').'" class="w-24 px-2 py-1.5 border border-gray-300 rounded-lg input-modern text-center">';
										echo '</td>';
										echo '<td class="border border-gray-300 px-2 py-2 text-center">';
										echo '<input type="text" name="'.$n7.'" value="'.fmt($candidato[$a7] ?? '').'" class="w-24 px-2 py-1.5 border border-gray-300 rounded-lg input-modern text-center">';
										echo '</td>';
										echo '<td class="border border-gray-300 px-2 py-2 text-center">';
										echo '<input type="text" name="'.$n8.'" value="'.fmt($candidato[$a8] ?? '').'" class="w-24 px-2 py-1.5 border border-gray-300 rounded-lg input-modern text-center">';
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
						<h3 class="text-lg font-semibold text-gray-800 mb-4">Notas 9º Ano</h3>
						<div class="overflow-x-auto">
							<table class="w-full border-collapse compact-table text-sm">
								<thead>
									<tr class="bg-gray-100">
										<th class="border border-gray-300 px-4 py-3 text-left">Matéria</th>
										<th class="border border-gray-300 px-4 py-3 text-center">1º Bim</th>
										<th class="border border-gray-300 px-4 py-3 text-center">2º Bim</th>
										<th class="border border-gray-300 px-4 py-3 text-center">3º Bim</th>
										<th class="border border-gray-300 px-4 py-3 text-center">Média</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$map9 = [
										['Português','n1b_portuguesa','n2b_portuguesa','n3b_portuguesa','n9_portuguesa','portugues_9_1','portugues_9_2','portugues_9_3','portugues_9_media'],
										['Matemática','n1b_matematica','n2b_matematica','n3b_matematica','n9_matematica','matematica_9_1','matematica_9_2','matematica_9_3','matematica_9_media'],
										['História','n1b_historia','n2b_historia','n3b_historia','n9_historia','historia_9_1','historia_9_2','historia_9_3','historia_9_media'],
										['Geografia','n1b_geografia','n2b_geografia','n3b_geografia','n9_geografia','geografia_9_1','geografia_9_2','geografia_9_3','geografia_9_media'],
										['Ciências','n1b_ciencias','n2b_ciencias','n3b_ciencias','n9_ciencias','ciencias_9_1','ciencias_9_2','ciencias_9_3','ciencias_9_media'],
										['Inglês','n1b_inglesa','n2b_inglesa','n3b_inglesa','n9_inglesa','ingles_9_1','ingles_9_2','ingles_9_3','ingles_9_media'],
										['Artes','n1b_artes','n2b_artes','n3b_artes','n9_artes','artes_9_1','artes_9_2','artes_9_3','artes_9_media'],
										['Educação Física','n1b_educacao_fisica','n2b_educacao_fisica','n3b_educacao_fisica','n9_educacao_fisica','edfisica_9_1','edfisica_9_2','edfisica_9_3','edfisica_9_media'],
										['Religião','n1b_religiao','n2b_religiao','n3b_religiao','n9_religiao','religiao_9_1','religiao_9_2','religiao_9_3','religiao_9_media'],
									];
									foreach ($map9 as $row) {
										[$label,$b1,$b2,$b3,$med,$n1,$n2,$n3,$nMed] = $row;
										echo '<tr class="hover:bg-gray-50">';
										echo '<td class="border border-gray-300 px-4 py-3">'.fmt($label).'</td>';
										echo '<td class="border border-gray-300 px-2 py-2 text-center">';
										echo '<input type="text" name="'.$n1.'" value="'.fmt($candidato[$b1] ?? '').'" class="w-24 px-2 py-1.5 border border-gray-300 rounded-lg input-modern text-center">';
										echo '</td>';
										echo '<td class="border border-gray-300 px-2 py-2 text-center">';
										echo '<input type="text" name="'.$n2.'" value="'.fmt($candidato[$b2] ?? '').'" class="w-24 px-2 py-1.5 border border-gray-300 rounded-lg input-modern text-center">';
										echo '</td>';
										echo '<td class="border border-gray-300 px-2 py-2 text-center">';
										echo '<input type="text" name="'.$n3.'" value="'.fmt($candidato[$b3] ?? '').'" class="w-24 px-2 py-1.5 border border-gray-300 rounded-lg input-modern text-center">';
										echo '</td>';
										echo '<td class="border border-gray-300 px-2 py-2 text-center">';
										echo '<input type="text" name="'.$nMed.'" value="'.fmt($candidato[$med] ?? '').'" class="w-24 px-2 py-1.5 border border-gray-300 rounded-lg input-modern text-center bg-yellow-50">';
										echo '</td>';
										echo '</tr>';
									}
									?>
								</tbody>
							</table>
						</div>
					</div>

					<?php if (!empty($candidato)) { ?>
					<div>
						<h3 class="text-lg font-semibold text-gray-800 mb-4">Médias Calculadas</h3>
						<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
							<?php
							$medias = [
								['Língua Portuguesa','med_portuguesa'],
								['Matemática','med_matematica'],
								['História','med_historia'],
								['Geografia','med_geografia'],
								['Ciências','med_ciencias'],
								['Língua Inglesa','med_inglesa'],
								['Artes','med_artes'],
								['Educação Física','med_educacao_fisica'],
								['Religião','med_religiao'],
							];
							foreach ($medias as [$label,$key]) {
								$value = $candidato[$key] ?? '';
								echo '<div>';
								echo '<label class="block text-sm font-medium text-gray-600 mb-1">'.fmt($label).'</label>';
								echo '<input type="text" value="'.fmt($value).'" class="w-full px-4 py-3.5 border border-gray-200 rounded-xl bg-gray-50 text-gray-800" disabled>';
								echo '</div>';
							}
							// Média final
							$value = $candidato['media_final'] ?? '';
							echo '<div class="md:col-span-3">';
							echo '<label class="block text-sm font-semibold text-gray-700 mb-1">Média Final</label>';
							echo '<input type="text" value="'.fmt($value).'" class="w-full px-4 py-3.5 border border-gray-300 rounded-xl bg-yellow-50 text-gray-900 font-semibold" disabled>';
							echo '</div>';
							?>
						</div>
					</div>
					<?php } ?>

					<div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-3">
						<button type="button" onclick="window.history.back()" class="px-6 py-3 rounded-xl border-2 border-[<?= $cursoCor ?>] text-[<?= $cursoCor ?>] font-semibold hover:bg-[<?= $cursoCor ?>] hover:text-white transition">Cancelar</button>
						<button type="submit" class="px-6 py-3 rounded-xl bg-[<?= $cursoCor ?>] text-white font-semibold">Salvar</button>
					</div>
				</form>
			</div>
		</div>
	</main>
</body>
</html>
