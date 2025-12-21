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

if (
    (isset($_GET['tipo_relatorio']) && !empty($_GET['tipo_relatorio']) || isset($_POST['tipo_relatorio']) && !empty($_POST['tipo_relatorio']))
) {
    $tipo_relatorio = $_GET['tipo_relatorio'] ?? $_POST['tipo_relatorio'];
    $id_usuario = $_GET['id_usuario'] ?? $_POST['id_usuario'];
    $id_curso = $_GET['curso'] ?? $_POST['curso'];
    
    switch ($tipo_relatorio) {
        case 'Resultado Final':
            $recursos_pendentes = $select->select_recursos_pendentes();
            if (!empty($recursos_pendentes)) {
                header('Location: ../views/relatorios.php?erro_recursos_pendentes=1');
                exit();
            }

            header('Location: ../views/reports/resultados/resultado_final.php?curso=' . $id_curso);
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
