<?php
require_once(__DIR__ . '/../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();
print_r($_POST);
print_r($_GET);

if (
    (isset($_GET['tipo_relatorio']) && !empty($_GET['tipo_relatorio']) || isset($_POST['tipo_relatorio']) && !empty($_POST['tipo_relatorio']))
) {
    echo $tipo_relatorio = $_GET['tipo_relatorio'] ?? $_POST['tipo_relatorio'];
    echo $id_usuario = $_GET['id_usuario'] ?? $_POST['id_usuario'];
    echo $id_curso = $_GET['curso'] ?? $_POST['curso'];

    switch ($tipo_relatorio) {
        case 'Resultado Final':
            header("location:../views/reports/resultados/resultado_final.php?curso=" . $id_curso);
            exit();
        case 'Resultado Preliminar':
            header("location:../views/reports/resultados/resultado_preliminar.php?curso=" . $id_curso);
            exit();
        case 'Resultado':
            header("location:../views/reports/resultados/resultado.php?curso=" . $id_curso);
            exit();
        case 'comissao_selecao':
            header("location:../views/reports/comissao_selecao.php?usuarios");
            exit();
        case 'movimentacoes':
            header("location:../views/reports/movimentacoes.php?id_usuario=" . $id_usuario);
            exit();
        case 'requisicoes':
            header("location:../views/reports/requisicoes.php?usuarios");
            exit();
        case 'recursos':
            header("location:../views/reports/recursos.php?usuarios");
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
