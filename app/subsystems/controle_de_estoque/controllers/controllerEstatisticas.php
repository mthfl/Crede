<?php
// Garantir que apenas JSON seja retornado
header('Content-Type: application/json; charset=utf-8');
// Evitar que notices/warnings quebrem o JSON
@ini_set('display_errors', '0');
@error_reporting(0);
// Capturar qualquer saída anterior e limpá-la antes de enviar o JSON
if (function_exists('ob_start')) {
    @ob_start();
}

require_once(__DIR__ . '/../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../models/model.select.php');

try {
    $select = new select();
    
    // Obter estatísticas gerais
    $total_produtos = $select->select_produtos_total()[0]['total'];
    $produtos_criticos = $select->select_produtos_critico()[0]['total'];
    $total_categorias = $select->select_total_categorias()[0]['total'];
    
    // Obter dados para o gráfico
    $em_estoque = $select->select_produtos_em_estoque();
    $estoque_critico = $select->select_produtos_estoque_critico();
    $sem_estoque = $select->select_produtos_sem_estoque();
    
    $response = [
        'success' => true,
        'estatisticas' => [
            'total_produtos' => (int)$total_produtos,
            'produtos_criticos' => (int)$produtos_criticos,
            'total_categorias' => (int)$total_categorias
        ],
        'grafico' => [
            'em_estoque' => (int)$em_estoque,
            'estoque_critico' => (int)$estoque_critico,
            'sem_estoque' => (int)$sem_estoque
        ]
    ];
    
    if (function_exists('ob_get_length') && @ob_get_length()) {
        @ob_clean();
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'error' => 'Erro ao carregar estatísticas: ' . $e->getMessage(),
        'estatisticas' => [
            'total_produtos' => 0,
            'produtos_criticos' => 0,
            'total_categorias' => 0
        ],
        'grafico' => [
            'em_estoque' => 0,
            'estoque_critico' => 0,
            'sem_estoque' => 0
        ]
    ];
    if (function_exists('ob_get_length') && @ob_get_length()) {
        @ob_clean();
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}
?>
