<?php
require_once(__DIR__ . '/../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../models/model.liberador.php');
require_once(__DIR__ . '/../models/model.select.php');
$select = new liberador();
$selectModel = new select();
print_r($_POST);

if (
    isset($_POST['opcao_atual']) && $_POST['opcao_atual'] == 'select' &&
    isset($_POST['produto']) && !empty($_POST['produto']) && is_numeric($_POST['produto']) &&
    isset($_POST['quantidade']) && !empty($_POST['quantidade']) && is_numeric($_POST['quantidade']) &&
    isset($_POST['retirante']) && !empty($_POST['retirante'])
) {

    $id_produto = $_POST['produto'];
    $solicitador = $_POST['retirante'];
    $valor_retirado = $_POST['quantidade'];
    $liberador = $_SESSION['nome'];
    $model = new liberador();

    date_default_timezone_set('America/Fortaleza');
    $datatime = date('Y-m-d H:i:s');
    $result = $model->solicitar_produto_id($valor_retirado, $id_produto, $solicitador, $datatime, $liberador);

    switch ($result) {
        case 1:
            header("Location: ../views/solicitar.php?retirado");
            break;
        case 2:
            header("Location: ../views/solicitar.php?erro");
            break;
        case 3:
            header("Location: ../views/solicitar.php?sem_produtos");
            break;
        case 4:
            header("Location: ../views/solicitar.php?numero_alto");
            break;
        default:
            header("Location: ../views/solicitar.php?fatal");
            break;
    }
} else if (
    isset($_POST['barcode']) && !empty($_POST['barcode']) &&
    isset($_POST['quantidade']) && !empty($_POST['quantidade']) && is_numeric($_POST['quantidade']) && 
    isset($_POST['retirante']) && !empty($_POST['retirante'])
) {

    $barcode = $_POST['barcode'];
    $retirante = $_POST['retirante'];
    $valor_retirada = $_POST['quantidade'];
    $liberador = $_SESSION['nome'];
    $model = new liberador();

    date_default_timezone_set('America/Fortaleza');
    $datatime = date('Y-m-d H:i:s');
    $result = $model->solicitar_produto_barcode($valor_retirada, $barcode, $retirante, $datatime, $liberador);

    switch ($result) {
        case 1:
            header("Location: ../views/solicitar.php?retirado");
            break;
        case 2:
            header("Location: ../views/solicitar.php?erro");
            break;
        case 3:
            header("Location: ../views/solicitar.php?sem_produtos");
            break;
        case 4:
            header("Location: ../views/solicitar.php?numero_alto");
            break;
        default:
            header("Location: ../views/solicitar.php?fatal");
            break;
    }
} else if (isset($_POST['barcode']) && !empty($_POST['barcode']) && isset($_POST['action']) && $_POST['action'] === 'buscar') {
    // Buscar produto por código de barras via AJAX
    try {
        // Garantir que não há saída antes do JSON
        ob_clean();
        
        $barcode = trim($_POST['barcode']);
        
        // Debug: verificar se o barcode está sendo recebido corretamente
        error_log("Barcode recebido: " . $barcode);
        
        $produtos = $selectModel->select_produto_nome($barcode);
        
        // Debug: verificar o resultado da consulta
        error_log("Resultado da consulta: " . print_r($produtos, true));
        
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        if ($produtos && count($produtos) > 0) {
            $produto = $produtos[0]; // Pega o primeiro resultado
            echo json_encode([
                'success' => true,
                'produto' => [
                    'id' => $produto['id'],
                    'nome_produto' => $produto['nome_produto'],
                    'quantidade' => $produto['quantidade'],
                    'barcode' => $produto['barcode'],
                    'categoria' => $produto['categoria'] ?? ''
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Produto não encontrado para o código: ' . $barcode,
                'debug' => [
                    'barcode_recebido' => $barcode,
                    'tipo_barcode' => gettype($barcode),
                    'comprimento' => strlen($barcode)
                ]
            ]);
        }
    } catch (Exception $e) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'Erro interno: ' . $e->getMessage(),
            'debug' => [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]
        ]);
    }
    exit();
} else if (isset($_GET['teste']) && $_GET['teste'] === 'barcode') {
    // Endpoint de teste para verificar a busca
    header('Content-Type: application/json');
    
    // Testar com o código de barras "123" que está no banco
    $barcode = "123";
    $produtos = $selectModel->select_produto_nome($barcode);
    
    echo json_encode([
        'teste' => true,
        'barcode_teste' => $barcode,
        'resultado' => $produtos,
        'count' => $produtos ? count($produtos) : 0
    ]);
    exit();
} /*else {

    header('location:../views/index.php');
    exit();
}*/
