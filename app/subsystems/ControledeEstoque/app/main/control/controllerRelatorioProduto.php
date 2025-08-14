<?php
require "../model/model.functions.php";

// Handle the request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['produto']) && isset($_GET['data_inicio']) && isset($_GET['data_fim'])) {
    $controller = new relatorios();
    $controller->relatorioEstoqueProduto($_GET['data_inicio'], $_GET['data_fim'], $_GET['produto']);
} else {
    echo "Erro: Parâmetros de data não fornecidos.";
    exit;
}
?>