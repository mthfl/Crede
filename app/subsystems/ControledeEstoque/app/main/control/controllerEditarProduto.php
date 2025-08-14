<?php
require("../model/model.functions.php");

// Verificar se a requisição é para carregar o produto para edição
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $gerenciamento = new gerenciamento();
    $produto = $gerenciamento->buscarProdutoPorId($id);

    if ($produto) {
        // Retorna os dados do produto em formato JSON
        header('Content-Type: application/json');
        echo json_encode($produto);
        exit;
    } else {
        // Produto não encontrado
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Produto não encontrado']);
        exit;
    }
}

// Verificar se a requisição é para salvar as alterações
if (isset($_POST['editar_id'])) {
    error_log("=== INICIANDO PROCESSAMENTO DE EDIÇÃO ===");
    error_log("POST data: " . json_encode($_POST));
    
    $id = $_POST['editar_id'];
    $nome = $_POST['editar_nome'];
    $barcode = $_POST['editar_barcode'];
    $quantidade = $_POST['editar_quantidade'];
    $natureza = $_POST['editar_natureza'];

    error_log("Dados extraídos:");
    error_log("ID: " . $id);
    error_log("Nome: " . $nome);
    error_log("Barcode: " . $barcode);
    error_log("Quantidade: " . $quantidade);
    error_log("Natureza: " . $natureza);

    $gerenciamento = new gerenciamento();
    $resultado = $gerenciamento->editarProduto($id, $nome, $barcode, $quantidade, $natureza);

    error_log("Resultado da edição: " . ($resultado ? "SUCESSO" : "FALHA"));

    // Retornar resposta JSON para AJAX
    header('Content-Type: application/json');
    
    if ($resultado) {
        echo json_encode(['success' => true, 'message' => 'Produto atualizado com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar produto.']);
    }
}
?>