<?php
require_once "../model/model.functions.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['barcode'])) {
    $barcode = $_GET['barcode'];
    
    try {
        $gerenciamento = new gerenciamento();
        $produto = $gerenciamento->buscarProdutoPorBarcode($barcode);
        
        header('Content-Type: application/json');
        
        if ($produto) {
            echo json_encode([
                'success' => true,
                'produto' => $produto
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Produto não encontrado'
            ]);
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Erro ao buscar produto: ' . $e->getMessage()
        ]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Parâmetros inválidos'
    ]);
}
?> 