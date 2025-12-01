<?php

session_start();

require_once __DIR__ . '/../models/Escolas.php';

// Obter lista de escolas (abreviação + nome completo) a partir do banco crede_users
$escolasModel  = new Escolas();
$schoolsConfig = $escolasModel->listarEscolas();

// Define escola selecionada: prioridade para GET, depois sessão
$escola = isset($_GET['escola']) && $_GET['escola'] !== ''
    ? $_GET['escola']
    : ($_SESSION['escola'] ?? null);

if ($escola) {
    $_SESSION['escola'] = $escola;
}

require_once __DIR__ . '/../models/AdminDashboard.php';

$cursosChartData = [];
$cotasPorCurso   = [];
$usuariosEscola  = [];
$quickStats      = [
    'totalAlunos'   => 0,
    'totalPublicos' => 0,
    'totalPrivados' => 0,
    'totalPCDs'     => 0,
];
$candidatos      = [];

if ($escola) {
    $dashboard = new AdminDashboard($escola);
    $cursosChartData = $dashboard->getCursosChartData();
    $cotasPorCurso   = $dashboard->getCotasPorCurso();
    $usuariosEscola  = $dashboard->getUsuariosEscola();
    $quickStats      = $dashboard->getQuickStats();
    $candidatos      = $dashboard->getCandidatos();
}
