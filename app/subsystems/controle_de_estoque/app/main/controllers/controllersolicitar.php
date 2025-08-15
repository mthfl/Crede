<?php

require_once('../model/model.functions.php');
print_r($_POST);

if (isset($_POST['btn'])) {
    $retirante = $_POST['retirante'];
    $valor_retirada = $_POST['quantidade'];

    $x = new gerenciamento();

    if (!empty($_POST['barcode']) && trim($_POST['barcode']) !== '') {
        $barcode = $_POST['barcode'];

        $produtoEncontrado = $x->buscarProdutoPorBarcode($barcode);
        if ($produtoEncontrado) {

            date_default_timezone_set('America/Fortaleza');
        $datatime = date('Y-m-d H:i:s');
            $x->solicitarproduto($valor_retirada, $produtoEncontrado['barcode'], $retirante, $datetime);
        } else {

            header("Location: ../view/solicitar.php?error=1&message=" . urlencode("Produto não encontrado com o código de barras informado!"));
            exit;
        }
    }

    elseif (!empty($_POST['produto']) && $_POST['produto'] !== '') {
        $produto_id = $_POST['produto'];

        $produtoEncontrado = $x->buscarProdutoPorId($produto_id); 

        if ($produtoEncontrado) {
 
            date_default_timezone_set('America/Fortaleza');
        $datatime = date('Y-m-d H:i:s');
            $x->solicitarproduto($valor_retirada, $produtoEncontrado['barcode'], $retirante, $datetime); 
        } else {
            header("Location: ../view/solicitar.php?error=1&message=" . urlencode("Produto não encontrado pelo ID informado."));
            exit;
        }
    }

    else {

        header("Location: ../view/solicitar.php?error=1&message=" . urlencode("Por favor, selecione um produto ou insira o código de barras!"));
        exit;
    }
}
