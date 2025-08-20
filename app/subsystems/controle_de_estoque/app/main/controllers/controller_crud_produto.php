<?php
require_once(__DIR__ . '/../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../models/model.usuario.php');
$select = new usuario();
//print_r($_POST);

//cadastrar produto com codigo de barra
if (
    isset($_POST['nome_produto']) && !empty($_POST['nome_produto']) && is_string($_POST['nome_produto']) &&
    isset($_POST['quantidade']) && !empty($_POST['quantidade']) && is_numeric($_POST['quantidade']) &&
    isset($_POST['id_categoria']) && !empty($_POST['id_categoria']) && is_numeric($_POST['id_categoria'])
) {

    $barcode = $_POST['barcode'] ?? null;
    $nome = $_POST['nome_produto'];
    $quantidade = $_POST['quantidade'];
    $validade = $_POST['validade'] ?? null;
    $id_categoria = $_POST['id_categoria'];

    $obj = new usuario();
    $result = $obj->cadastrar_produto($barcode, $nome, $quantidade, $id_categoria, $validade);

    switch ($result) {
        case 1:
            header('Location: ../views/estoque.php?cadastrado');
            exit();
        case 2:
            header('Location: ../views/estoque.php?erro');
            exit();
        case 3:
            header('Location: ../views/products/adc_novo_produto.php?ja_cadastrado');
            exit();
        default:
            header('Location: ../views/estoque.php?falha');
            exit();
    }
}

//verificar o tipo de cadastro de produto
else if (
    isset($_POST['btn']) && !empty($_POST['btn']) && is_string($_POST['btn']) &&
    isset($_POST['tipo_produto']) && !empty($_POST['tipo_produto']) && is_string($_POST['tipo_produto'])
) {

    $tipo_produto = $_POST['tipo_produto'];
    $barcode = $_POST['barcode'];

    if ($tipo_produto === 'com_codigo') {

        $obj = new usuario();
        $result = $obj->verificar_produto_barcode($barcode);

        if ($result) {

            header('Location: ../views/products/adc_produto_existente.php?barcode=' . $barcode);
            exit();
        } else {

            header('Location: ../views/products/adc_novo_produto.php?barcode=' . $barcode);
            exit();
        }
    } else  if ($tipo_produto === 'sem_codigo') {

        header('Location: ../views/products/adc_novo_produto.php');
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

    $obj = new usuario();
    $result = $obj->registrar_perda(
        $id_produto,
        $quantidade,
        $tipo_perda,
        $data_perda
    );

    switch ($result) {
        case 1:
            header('Location: ../views/estoque.php?registrado');
            exit();
        case 2:
            header('Location: ../views/perdas.php?erro');
            exit();
        case 3:
            header('Location: ../views/perdas.php?nao_existe');
            exit();
        default:
            header('Location: ../views/perdas.php?falha');
            exit();
    }
}

//adicionar categoria
else if (isset($_POST['categoria']) && !empty($_POST['categoria'])) {

    $categoria = $_POST['categoria'];

    $obj = new usuario();
    $result = $obj->cadastrar_categoria($categoria);

    switch ($result) {
        case 1:
            header('Location: ../views/estoque.php?cadastrado');
            exit();
        case 2:
            header('Location: ../views/estoque.php?erro');
            exit();
        case 3:
            header('Location: ../views/estoque.php?ja_cadastrado');
            exit();
        default:
            header('Location: ../views/estoque.php?falha');
            exit();
    }
}
//adicionar mais produto existente ao estoque
else if (
    isset($_POST['barcode']) && !empty($_POST['barcode']) &&
    isset($_POST['quantidade_adicionar']) && !empty($_POST['quantidade_adicionar']) && is_numeric($_POST['quantidade_adicionar'])
) {
    $barcode = $_POST['barcode'];
    $quantidade = $_POST['quantidade_adicionar'];

    $obj = new usuario();

    $result = $obj->adicionar_produto($barcode, $quantidade);

    switch ($result) {
        case 1:
            header('Location: ../views/estoque.php?excluido');
            exit();
        case 2:
            header('Location: ../views/estoque.php?erro');
            exit();
        default:
            header('Location: ../views/estoque.php?falha');
            exit();
    }
} else {

    header('location:../views/index.php');
    exit();
}
