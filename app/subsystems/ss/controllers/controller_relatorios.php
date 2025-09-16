<?php
require_once(__DIR__ . '/../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

print_r($_POST);
if (
    isset($_POST['tipo_relatorio']) && !empty($_POST['tipo_relatorio']) &&
    isset($_POST['curso_id']) && !empty($_POST['curso_id'])
) {

    $curso = $_POST['curso_id'];
    $tipo_relatorio = $_POST['tipo_relatorio'];
    switch ($tipo_relatorio) {
        case 'privada_ac':
            header("location:../views/reports/relatorios.php?curso=". $curso. "&tipo_relatorio=PRIVADA AC");
            exit();
        case 'privada_cotas':
            header("location:../views/reports/relatorios.php?curso=". $curso . "&tipo_relatorio=PRIVADA COTAS");
            exit();
        case 'privada_geral':
            header("location:../views/reports/relatorios.php?curso=". $curso . "&tipo_relatorio=PRIVADA GERAL");
            exit();
        case 'publica_ac':
            header("location:../views/reports/relatorios.php?curso=". $curso . "&tipo_relatorio=PÚBLICA AC");    
            exit();
        case 'publica_cotas':
            header("location:../views/reports/relatorios.php?curso=". $curso . "&tipo_relatorio=PÚBLICA COTAS");
            exit();
        case 'publica_geral':
            header("location:../views/reports/relatorios.php?curso=". $curso . "&tipo_relatorio=PÚBLICA GERAL");
            exit();
    }
}else if (
    isset($_POST['tipo_consulta']) && !empty($_POST['tipo_consulta']) &&
    isset($_POST['curso_id']) && !empty($_POST['curso_id'])
) {

    $curso = $_POST['curso_id'];
    $tipo_relatorio = $_POST['tipo_consulta'];
    switch ($tipo_relatorio) {
        case 'classificados':
            header("location:../views/reports/resultados/classificados.php?curso=". $curso);
            exit();
        case 'classificaveis':
            header("location:../views/reports/resultados/classificaveis.php?curso=". $curso);
            exit();
        case 'resultado_final':
            header("location:../views/reports/resultados/resultado_final.php?curso=". $curso);
            exit();
        case 'resultado_preliminar':
            header("location:../views/reports/resultados/resultado_preliminar.php?curso=". $curso);
            exit();
    }
}else{
   header("location: ../index.php"); 
   exit();
}
