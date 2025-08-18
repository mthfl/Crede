<?php
require_once(__DIR__.'\..\..\models\sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

return $config = [
    "local" => [
        "crede_estoque" => [
            "host" => "localhost",
            "user" => "root",
            "banco" => "crede_estoque",
            "senha" => ""
        ]
    ],
    "hospedagem" => [
        "crede_estoque" => [
            "host" => "localhost",
            "user" => "u750204740_crede_estoque",
            "banco" => "u750204740_crede_estoque",
            "senha" => "Crede1@#$"           
        ]
    ]
];