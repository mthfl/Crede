<?php
require_once("../model/model.functions.php");

header('Content-Type: application/json');

try {
    $env = isset($_GET['env']) ? $_GET['env'] : 'local';
    $gerenciamento = new gerenciamento($env);
    
    // Verificar se a conexão está funcionando usando o método público
    $pdo = $gerenciamento->getPdo();
    
    if (!$pdo) {
        throw new Exception("Conexão com banco de dados não estabelecida");
    }
    
    // Buscar estatísticas do estoque
    $consulta = "SELECT 
        COUNT(*) as total_produtos,
        SUM(CASE WHEN quantidade <= 5 THEN 1 ELSE 0 END) as produtos_criticos,
        COUNT(DISTINCT natureza) as total_categorias,
        SUM(quantidade) as total_unidades
        FROM produtos";
    
    $query = $pdo->prepare($consulta);
    $query->execute();
    $estatisticas = $query->fetch(PDO::FETCH_ASSOC);
    
    // Buscar dados para o gráfico
    $consultaGrafico = "SELECT 
        SUM(CASE WHEN quantidade > 5 THEN 1 ELSE 0 END) as em_estoque,
        SUM(CASE WHEN quantidade <= 5 AND quantidade > 0 THEN 1 ELSE 0 END) as estoque_critico,
        SUM(CASE WHEN quantidade = 0 THEN 1 ELSE 0 END) as sem_estoque
        FROM produtos";
    
    $queryGrafico = $pdo->prepare($consultaGrafico);
    $queryGrafico->execute();
    $dadosGrafico = $queryGrafico->fetch(PDO::FETCH_ASSOC);
    
    // Verificar se os dados foram encontrados
    if ($estatisticas['total_produtos'] === null) {
        $estatisticas['total_produtos'] = 0;
    }
    if ($estatisticas['produtos_criticos'] === null) {
        $estatisticas['produtos_criticos'] = 0;
    }
    if ($estatisticas['total_categorias'] === null) {
        $estatisticas['total_categorias'] = 0;
    }
    if ($estatisticas['total_unidades'] === null) {
        $estatisticas['total_unidades'] = 0;
    }
    
    if ($dadosGrafico['em_estoque'] === null) {
        $dadosGrafico['em_estoque'] = 0;
    }
    if ($dadosGrafico['estoque_critico'] === null) {
        $dadosGrafico['estoque_critico'] = 0;
    }
    if ($dadosGrafico['sem_estoque'] === null) {
        $dadosGrafico['sem_estoque'] = 0;
    }
    
    // Calcular valor total (simulado - em produção você teria uma coluna de preço)
    $valorTotal = $estatisticas['total_unidades'] * 290; // Valor médio por unidade
    
    $response = [
        'success' => true,
        'estatisticas' => [
            'total_produtos' => (int)$estatisticas['total_produtos'],
            'produtos_criticos' => (int)$estatisticas['produtos_criticos'],
            'total_categorias' => (int)$estatisticas['total_categorias'],
            'valor_total' => 'R$ ' . number_format($valorTotal, 2, ',', '.')
        ],
        'grafico' => [
            'em_estoque' => (int)$dadosGrafico['em_estoque'],
            'estoque_critico' => (int)$dadosGrafico['estoque_critico'],
            'sem_estoque' => (int)$dadosGrafico['sem_estoque']
        ],
        'debug' => [
            'banco_conectado' => true,
            'dados_brutos' => $estatisticas,
            'dados_grafico_brutos' => $dadosGrafico
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'error' => 'Erro ao carregar estatísticas: ' . $e->getMessage(),
        'estatisticas' => [
            'total_produtos' => 0,
            'produtos_criticos' => 0,
            'total_categorias' => 0,
            'valor_total' => 'R$ 0,00'
        ],
        'grafico' => [
            'em_estoque' => 0,
            'estoque_critico' => 0,
            'sem_estoque' => 0
        ],
        'debug' => [
            'banco_conectado' => false,
            'erro' => $e->getMessage()
        ]
    ];
    
    echo json_encode($response);
}
?>