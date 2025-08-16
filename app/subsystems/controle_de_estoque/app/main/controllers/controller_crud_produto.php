<?php
require_once('../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__.'\..\models\model.usuario.php');
print_r($_POST);

if(
    isset($_POST['barcode']) && !empty($_POST['barcode']) && 
    isset($_POST['nome_produto']) && !empty($_POST['nome_produto']) &&  
    isset($_POST['quantidade']) && !empty($_POST['quantidade']) &&
    isset($_POST['validade']) &&
    isset($_POST['id_categoria']) && !empty($_POST['id_categoria'])
){

    $barcode = $_POST['barcode'];
    $nome = $_POST['nome_produto']; 
    $quantidade = $_POST['quantidade'];
    $validade = $_POST['validade'] ?? null;
    $id_categoria = $_POST['id_categoria'];

    $obj = new usuario();
    $result = $obj->cadastrar_produto($barcode, $nome,$quantidade, $id_categoria, $validade);

    switch ($result) {
        case 1:
            header('Location: ../views/estoque.php?cadastrado');
            exit();
        case 2:
            header('Location: ../views/estoque.php?erro');
            exit();
        case 3:
            header('Location: ../views/estoque.php?ja_cadastrado');
            exit();
        default:
            header('Location: ../views/estoque.php?falha');
            exit();
    }
}
else if (
    isset($_POST['btn']) && !empty($_POST['btn']) && is_string($_POST['btn']) &&
    isset($_POST['tipo_produto']) && !empty($_POST['tipo_produto']) && is_string($_POST['tipo_produto']) &&
    isset($_POST['barcode']) && !empty($_POST['barcode']) && (is_string($_POST['barcode']) || is_numeric($POST['barcode']))
) {

    $tipo_produto = $_POST['tipo_produto'];
    $barcode = $_POST['barcode'];

    if($tipo_produto = 'com_codigo'){

        $obj = new usuario();
        $result = $obj->verificar_produto_barcode($barcode);
    
        if($result) {

            header('Location: ../views/products/adc_produto_existente.php?barcode='.$barcode);
            exit();
        }else{

            header('Location: ../views/products/adc_novo_produto.php?barcode='.$barcode);
            exit();
        }
    }else{

        $obj = new usuario();
        $result = $obj->verificar_produto_nome($barcode);
    
        switch ($result) {
            case 1:
                header('Location: ../view/perdas.php?registrado');
                exit();
            case 2:
                header('Location: ../view/perdas.php?erro');
                exit();
            case 3:
                header('Location: ../view/perdas.php?nao_existe');
                exit();
            default:
                header('Location: ../view/perdas.php?falha');
                exit();
        }
    }
} 

//registrar perda
else if (
    isset($_POST['id_produto']) && !empty(trim($_POST['id_produto'])) &&
    isset($_POST['quantidade_perdida']) && !empty(trim($_POST['quantidade_perdida'])) &&
    isset($_POST['tipo_perda']) && !empty(trim($_POST['tipo_perda'])) &&
    isset($_POST['data_perda']) && !empty(trim($_POST['data_perda']))
) {

    $id_produto = trim($_POST['id_produto']);
    $quantidade = trim($_POST['quantidade_perdida']);
    $tipo_perda = trim($_POST['tipo_perda']);
    $data_perda = trim($_POST['data_perda']);

    $obj = new usuario();
    $result = $obj->registrar_perda(
        $id_produto,
        $quantidade,
        $tipo_perda,
        $data_perda
    );

    switch ($result) {
        case 1:
            header('Location: ../views/estoque.php?registrado');
            exit();
        case 2:
            header('Location: ../views/perdas.php?erro');
            exit();
        case 3:
            header('Location: ../views/perdas.php?nao_existe');
            exit();
        default:
            header('Location: ../views/perdas.php?falha');
            exit();
    }
} 
//adicionar categoria
if(isset($_POST['categoria']) && !empty($_POST['categoria']) && is_numeric($_POST['categoria'])){

    $categoria = $_POST['categoria'];

    $obj = new usuario();
    $result = $obj->cadastrar_categoria($categoria);

    switch ($result) {
        case 1:
            header('Location: ../views/estoque.php?cadastrado');
            exit();
        case 2:
            header('Location: ../views/estoque.php?erro');
            exit();
        case 3:
            header('Location: ../views/estoque.php?ja_cadastrado');
            exit();
        default:
            header('Location: ../views/estoque.php?falha');
            exit();
    }
} 
//adicionar produto existente ao estoque
else if (
    isset($_POST['barcode']) && !empty($_POST['barcode']) &&
    isset($_POST['quantidade_adicionar']) && !empty($_POST['quantidade_adicionar']) && is_numeric($_POST['quantidade_adicionar'])
) {
    $barcode = $_POST['barcode'];
    $quantidade = $_POST['quantidade_adicionar'];

    $obj = new usuario();
    
    $result = $obj->adicionar_produto($barcode, $quantidade);

    switch ($result) {
        case 1:
            header('Location: ../views/estoque.php?adicionado');
            exit();
        case 2:
            header('Location: ../views/estoque.php?erro');
            exit();
        default:
            header('Location: ../views/estoque.php?falha');
            exit();
    }
} else 
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
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
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
} else if (isset($_POST['editar_id'])) {
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
} else if (isset($_POST['btn'])) {
    $retirante = $_POST['retirante'];
    $valor_retirada = $_POST['quantidade'];

    $x = new gerenciamento();

    if (!empty($_POST['barcode']) && trim($_POST['barcode']) !== '') {
        $barcode = $_POST['barcode'];

        $produtoEncontrado = $x->buscarProdutoPorBarcode($barcode);
        if ($produtoEncontrado) {

            date_default_timezone_set('America/Fortaleza');
            $datatime = date('Y-m-d H:i:s');
            $x->solicitarproduto($valor_retirada, $produtoEncontrado['barcode'], $retirante, $datetime);
        } else {

            header("Location: ../view/solicitar.php?error=1&message=" . urlencode("Produto não encontrado com o código de barras informado!"));
            exit;
        }
    } elseif (!empty($_POST['produto']) && $_POST['produto'] !== '') {
        $produto_id = $_POST['produto'];

        $produtoEncontrado = $x->buscarProdutoPorId($produto_id);

        if ($produtoEncontrado) {

            date_default_timezone_set('America/Fortaleza');
            $datatime = date('Y-m-d H:i:s');
            $x->solicitarproduto($valor_retirada, $produtoEncontrado['barcode'], $retirante, $datetime);
        } else {
            header("Location: ../view/solicitar.php?error=1&message=" . urlencode("Produto não encontrado pelo ID informado."));
            exit;
        }
    }
} /*else {

    header('location:../views/index.php');
    exit();
}*/
