<?php
require_once("../model/model.functions.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    error_log("=== INICIANDO PROCESSAMENTO DE EXCLUSÃO ===");
    error_log("ID recebido: " . $id);
    
    try {
        $gerenciamento = new gerenciamento();
        $resultado = $gerenciamento->apagarProduto($id);
        
        error_log("Resultado da exclusão: " . ($resultado ? "SUCESSO" : "FALHA"));
        
        if ($resultado) {
            // Sucesso - redirecionar com mensagem de sucesso
            error_log("Redirecionando para sucesso");
            header("Location: ../view/estoque.php?success=1&message=Produto excluído com sucesso!");
            exit;
        } else {
            // Erro - redirecionar com mensagem de erro
            error_log("Redirecionando para erro");
            header("Location: ../view/estoque.php?error=1&message=Erro ao excluir produto!");
            exit;
        }
    } catch (Exception $e) {
        // Exceção - redirecionar com mensagem de erro
        header("Location: ../view/estoque.php?error=1&message=Erro: " . $e->getMessage());
        exit;
    }
} else {
    // Parâmetros inválidos
    header("Location: ../view/estoque.php?error=1&message=Parâmetros inválidos!");
    exit;
}
?>