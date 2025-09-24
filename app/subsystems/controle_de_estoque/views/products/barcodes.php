<?php 
// Verificar se recebeu nome do produto via POST (produto sem código)
if (isset($_POST['nome_produto'])) {
    $nome_produto = $_POST['nome_produto'];

    // Adicionar SCB_ apenas para o gerador de código de barras
header('Location: https://barcode.orcascan.com/?type=code128&data=SCB_'.$nome_produto);
    exit();
}
?>