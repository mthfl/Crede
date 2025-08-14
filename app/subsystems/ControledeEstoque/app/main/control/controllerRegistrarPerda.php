<?php
require_once('../model/sessions.php');
require_once('../model/model.functions.php');

// Verificar se o usuário está autenticado
$session = new sessions();
$session->autenticar_session();

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../view/perdas.php?error=1&message=Método não permitido');
    exit;
}

// Verificar se todos os campos obrigatórios foram enviados
$camposObrigatorios = ['produto_id', 'quantidade_perdida', 'tipo_perda', 'data_perda'];
foreach ($camposObrigatorios as $campo) {
    if (!isset($_POST[$campo]) || empty($_POST[$campo])) {
        header('Location: ../view/perdas.php?error=1&message=Campo ' . $campo . ' é obrigatório');
        exit;
    }
}

try {
    // Conectar ao banco de dados
    $env = isset($_GET['env']) ? $_GET['env'] : 'local';
    $gerenciamento = new gerenciamento($env);
    $pdo = $gerenciamento->getPdo();
    
    // Iniciar transação
    $pdo->beginTransaction();
    
    // Validar dados
    $produto_id = (int)$_POST['produto_id'];
    $quantidade_perdida = (int)$_POST['quantidade_perdida'];
    $tipo_perda = $_POST['tipo_perda'];
    $data_perda = $_POST['data_perda'];
    $observacoes = isset($_POST['observacoes']) ? trim($_POST['observacoes']) : '';
    
    // Validar tipos de perda permitidos
    $tiposPermitidos = ['dano_fisico', 'vencimento', 'desaparecimento', 'ma_conservacao'];
    if (!in_array($tipo_perda, $tiposPermitidos)) {
        throw new Exception('Tipo de perda inválido');
    }
    
    // Verificar se o produto existe e tem estoque suficiente
    $stmt = $pdo->prepare('SELECT id, nome_produto, quantidade FROM produtos WHERE id = ?');
    $stmt->execute([$produto_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$produto) {
        throw new Exception('Produto não encontrado');
    }
    
    if ($produto['quantidade'] < $quantidade_perdida) {
        throw new Exception('Quantidade em estoque insuficiente para registrar a perda');
    }
    
    // Registrar a perda
    $stmt = $pdo->prepare('INSERT INTO perdas (produto_id, quantidade_perdida, tipo_perda, data_perda, observacoes) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$produto_id, $quantidade_perdida, $tipo_perda, $data_perda, $observacoes]);
    
    // Atualizar a quantidade em estoque
    $novaQuantidade = $produto['quantidade'] - $quantidade_perdida;
    $stmt = $pdo->prepare('UPDATE produtos SET quantidade = ? WHERE id = ?');
    $stmt->execute([$novaQuantidade, $produto_id]);
    
    // Confirmar transação
    $pdo->commit();
    
    // Redirecionar com mensagem de sucesso
    $mensagem = 'Perda registrada com sucesso! ' . $quantidade_perdida . ' unidades de "' . $produto['nome_produto'] . '" foram removidas do estoque.';
    header('Location: ../view/perdas.php?success=1&message=' . urlencode($mensagem));
    exit;
    
} catch (Exception $e) {
    // Reverter transação em caso de erro
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    
    $mensagem = 'Erro ao registrar perda: ' . $e->getMessage();
    header('Location: ../view/perdas.php?error=1&message=' . urlencode($mensagem));
    exit;
}
?>
