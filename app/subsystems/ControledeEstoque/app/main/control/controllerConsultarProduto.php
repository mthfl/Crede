<?php
require("../model/model.functions.php");

if (isset($_POST['btn'])) {
    $tipo_produto = $_POST['tipo_produto'];
    
    if ($tipo_produto === 'com_codigo') {
        // Produto com código
        $barcode = $_POST['barcode'];
        $x = new gerenciamento();
        $x->consultarestoque($barcode);
    } else {
        // Produto sem código - apenas armazenar a variável
        $nome_produto = $_POST['nome_produto'];
        
        // Redirecionar para controllerAdicionarAoEstoque.php com o nome do produto via POST
        echo '<form id="redirectForm" method="POST" action="../control/controllerAdicionarAoEstoque.php">';
        echo '<input type="hidden" name="nome_produto" value="' . htmlspecialchars($nome_produto) . '">';
        echo '</form>';
        echo '<script>document.getElementById("redirectForm").submit();</script>';
    }
}
?>