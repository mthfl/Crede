<?php
require_once(__DIR__ . '/../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../models/model.liberador.php');
$select = new liberador();
//print_r($_POST);

if (
    isset($_GET['barcode']) && !empty($_GET['barcode'])

) {

    $barcode = $_GET['barcode'];
    $model = new liberador();

    $result = $model->verificar_produto_barcode($barcode);
    if ($result) {

        header("Location: ../views/solicitar.php?barcode=" . $barcode);
        exit();
    } else {

        header("Location: ../views/products/adc_novo_produto.php?barcode=" . $barcode);
        exit();
    }
}else {

    header('location:../views/index.php');
    exit();
}
