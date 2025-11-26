<?php
require_once(__DIR__ . '/../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();
print_r($_POST);
print_r($_GET);

if (
    isset($_POST['tipo_relatorio']) && !empty($_POST['tipo_relatorio']) &&
    isset($_POST['curso']) && !empty($_POST['curso'])
) {

    $curso = $_POST['curso'];
    $tipo_relatorio = $_POST['tipo_relatorio'];
    switch ($tipo_relatorio) {
        case 'privada_ac':
            header("location:../views/reports/relatorios.php?curso=" . $curso . "&tipo_relatorio=PRIVADA AC");
            exit();
        case 'privada_cotas':
            header("location:../views/reports/relatorios.php?curso=" . $curso . "&tipo_relatorio=PRIVADA COTAS");
            exit();
        case 'privada_geral':
            header("location:../views/reports/relatorios.php?curso=" . $curso . "&tipo_relatorio=PRIVADA GERAL");
            exit();
        case 'publica_ac':
            header("location:../views/reports/relatorios.php?curso=" . $curso . "&tipo_relatorio=PÚBLICA AC");
            exit();
        case 'publica_cotas':
            header("location:../views/reports/relatorios.php?curso=" . $curso . "&tipo_relatorio=PÚBLICA COTAS");
            exit();
        case 'publica_geral':
            header("location:../views/reports/relatorios.php?curso=" . $curso . "&tipo_relatorio=PÚBLICA GERAL");
            exit();
        case 'Classificados':
            header("location:../views/reports/resultados/classificados.php?curso=" . $curso);
            exit();
        case 'Classificaveis':
            header("location:../views/reports/resultados/classificaveis.php?curso=" . $curso);
            exit();
        case 'Resultado Final':
            header("location:../views/reports/resultados/resultado_final.php?curso=" . $curso);
            exit();
        case 'Resultado pré-liminar':
            header("location:../views/reports/resultados/resultado_preliminar.php?curso=" . $curso);
            exit();
        default:
            header("location: ../index.php");
            exit();
    }
}
if (
    (isset($_POST['tipo_relatorio']) && !empty($_POST['tipo_relatorio']))
) {
    $tipo_relatorio = $_POST['tipo_relatorio'];
    $id_usuario = $_POST['user_id'] ?? '';

    switch ($tipo_relatorio) {
        case 'comissao_selecao':
            header("location:../views/reports/comissao_selecao.php?usuarios");
            exit();
        case 'movimentacoes':
            header("location:../views/reports/movimentacoes.php?id_usuario=" . $id_usuario);
            exit();
        case 'requisicoes':
            header("location:../views/reports/requisicoes.php?usuarios");
            exit();
        case 'can_desabilitados':
            header("location:../views/reports/candidatos_desabilitados.php?usuarios");
            exit();
        default:
            header("location: ../index.php");
            exit();
    }
} else {
    header("location: ../index.php");
    exit();
}
