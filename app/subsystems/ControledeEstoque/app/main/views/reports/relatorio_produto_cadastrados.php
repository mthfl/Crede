<?php
require "../model/model.functions.php";

// Handle the request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['data_inicio']) && isset($_GET['data_fim'])) {
    $data_inicio = $_GET['data_inicio'];
    $data_fim = $_GET['data_fim'];
    
    $relatorios = new relatorios();
    
    // Se for uma requisição para PDF
    if (isset($_GET['pdf']) && $_GET['pdf'] == '1') {
        $relatorios->exportarRelatorioProdutosPorData($data_inicio, $data_fim);
    }
    // Se for uma requisição para JSON (visualização)
    else if (isset($_GET['format']) && $_GET['format'] == 'json') {
        header('Content-Type: application/json');
        
        try {
            $produtos = $relatorios->buscarProdutosPorData($data_inicio, $data_fim);
            
            echo json_encode([
                'success' => true,
                'produtos' => $produtos,
                'total' => count($produtos)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    // Se for uma requisição normal (visualização HTML)
    else {
        header("Location: ../view/relatorio_produtos_cadastrados.php?data_inicio=" . urlencode($data_inicio) . "&data_fim=" . urlencode($data_fim));
        exit;
    }
} else {
    echo "Erro: Parâmetros de data não fornecidos.";
    exit;
}
?> 