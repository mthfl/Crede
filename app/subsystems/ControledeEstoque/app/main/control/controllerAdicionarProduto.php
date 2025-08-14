<?php
require("../model/model.functions.php");

if (isset($_POST['btn'])) {
    $barcode = isset($_POST['barcode']) ? $_POST['barcode'] : '';
    $nome = $_POST['nome'];
    $quantidade = $_POST['quantidade'];
    $natureza = $_POST['natureza'];
    $validade = $_POST['validade'] ?? NULL;

   

    $x = new gerenciamento();
    
    if (empty($barcode) || !is_numeric($barcode)) {
        
        $barcode = 'SCB_' . $nome;
    }
    
    $x->adcproduto($barcode, $nome, $quantidade, $natureza, $validade);
}
?>