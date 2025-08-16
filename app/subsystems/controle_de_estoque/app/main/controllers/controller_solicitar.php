<?php
require_once('../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once('../models/model.usuario.php');
print_r($_POST);

if (
    isset( $_POST['opcao_atual']) && $_POST['opcao_atual'] == 'select' &&
    isset($_POST['produto']) && !empty($_POST['produto']) && is_numeric($_POST['produto']) &&
    isset($_POST['retirante']) && !empty($_POST['retirante']) && is_numeric($_POST['retirante']) &&
    isset($_POST['quantidade']) && !empty($_POST['quantidade']) && is_numeric($_POST['quantidade'])
) {

    $id_produto = $_POST['produto'];
    $retirante = $_POST['retirante'];
    $valor_retirada = $_POST['quantidade'];
    $usuario = $_SESSION['nome'];
    $model = new usuario();

    date_default_timezone_set('America/Fortaleza');
    $datatime = date('Y-m-d H:i:s');
    $result = $model->solicitar_produto_id($valor_retirada, $id_produto, $retirante, $datatime, $usuario);

    switch ($result) {
        case 1:
            header("Location: ../views/solicitar.php?retirado");
            break;
        case 2:
            header("Location: ../views/solicitar.php?erro");
            break;
        case 3:
            header("Location: ../views/solicitar.php?sem_produtos");
            break;
        case 4:
            header("Location: ../views/solicitar.php?numero_alto");
            break;
        default:
            header("Location: ../views/solicitar.php?fatal");
            break;
    }
} 

else if (
    isset($_POST['barcode']) && !empty($_POST['barcode']) && is_numeric($_POST['barcode']) &&
    isset($_POST['retirante']) && !empty($_POST['retirante']) && is_numeric($_POST['retirante']) &&
    isset($_POST['quantidade']) && !empty($_POST['quantidade']) && is_numeric($_POST['quantidade'])
) {

    $id_produto = $_POST['barcode'];
    $retirante = $_POST['retirante'];
    $valor_retirada = $_POST['quantidade'];
    $usuario = $_SESSION['nome'];
    $model = new usuario();

    date_default_timezone_set('America/Fortaleza');
    $datatime = date('Y-m-d H:i:s');
    $result = $model->solicitar_produto_barcode($valor_retirada, $id_produto, $retirante, $datetime, $usuario);

    switch ($result) {
        case 1:
            header("Location: ../views/solicitar.php?retirado");
            break;
        case 2:
            header("Location: ../views/solicitar.php?erro");
            break;
        case 3:
            header("Location: ../views/solicitar.php?sem_produtos");
            break;
        case 4:
            header("Location: ../views/solicitar.php?numero_alto");
            break;
        default:
            header("Location: ../views/solicitar.php?fatal");
            break;
    }
}
