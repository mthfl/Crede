<?php
require_once('../model/model.main.php');
print_r($_POST);

//verificação de codigo de barra
if (isset($_GET['barcode']) && !empty(trim($_GET['barcode']))) {

    $barcode = $_GET['barcode'];

    $obj = new MainModel();
    $result = $obj->verificar_produto($barcode);

    if ($result) {
        header('Location: ../view/solicitar.php?barcode=' . $barcode);
        exit();
    } else {
        header('Location: ../view/adcnovoproduto.php?barcode=' . $barcode);
        exit();
    }
}

//registrar perda
else if (
    isset($_POST['id_produto']) && !empty(trim($_POST['id_produto'])) &&
    isset($_POST['quantidade_perdida']) && !empty(trim($_POST['quantidade_perdida'])) &&
    isset($_POST['tipo_perda']) && !empty(trim($_POST['tipo_perda'])) &&
    isset($_POST['data_perda']) && !empty(trim($_POST['data_perda']))
) {

    $id_produto = trim($_POST['id_produto']);
    $quantidade = trim($_POST['quantidade_perdida']);
    $tipo_perda = trim($_POST['tipo_perda']);
    $data_perda = trim($_POST['data_perda']);

    $obj = new MainModel();
    $result = $obj->registrar_perda(
        $id_produto,
        $quantidade,
        $tipo_perda,
        $data_perda
    );

    switch ($result) {
        case 1:
            header('Location: ../view/perdas.php?registrado');
            exit();
        case 2:
            header('Location: ../view/perdas.php?erro');
            exit();
        case 3:
            header('Location: ../view/perdas.php?nao_existe');
            exit();
        default:
            header('Location: ../view/perdas.php?falha');
            exit();
    }
} else {
    header('Location: ../view/paginaincial.php');
    exit();
}
