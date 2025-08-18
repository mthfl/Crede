<?php
require_once(__DIR__.'\..\..\models\sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

return $table = [
    'crede_estoque' => [
        1 => 'categorias',
        2 => 'movimentacao',
        3 => 'perdas_produtos',
        4 => 'produtos',
        5 => 'responsaveis',
    ]
];
