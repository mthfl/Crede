<?php
require("../model/model.functions.php");

// Verificar se recebeu nome do produto via POST (produto sem código)
if (isset($_POST['nome_produto'])) {
    $nome_produto = $_POST['nome_produto'];

    // Adicionar SCB_ apenas para o gerador de código de barras
header('Location: https://barcode.orcascan.com/?type=code128&data=SCB_'.$nome_produto);
    exit();
}

// Verificar se recebeu dados do formulário normal (produto com código)
if (isset($_POST['btn'])) {
    $identificador = $_POST['barcode'];
    $quantidade = $_POST['quantidade'];

    $x = new gerenciamento();    
    $x->adcaoestoque($identificador, $quantidade);
   
}


?>