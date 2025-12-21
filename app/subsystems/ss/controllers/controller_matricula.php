<?php
require_once(__DIR__ . '/../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . "/../models/model.admin.php");
require_once(__DIR__ . "/../models/model.select.php");

$select = new select($_SESSION['escola']);
$cursos = $select->select_cursos();
print_r($_POST);
if (
    isset($_POST["acao"]) && $_POST["acao"] === "excluir_matricula" &&
    isset($_POST["id_matricula"]) && !empty($_POST["id_matricula"])
) {
    $id_matricula = (int) $_POST["id_matricula"];
    $escola = $_SESSION['escola'];
    $admin_model = new admin($escola);
    $result = $admin_model->excluir_matricula($id_matricula);

    switch ($result) {
        case 1:
            header('Location: ../views/matriculas.php?excluido');
            exit();
        case 2:
            header('Location: ../views/matriculas.php?erro');
            exit();
        default:
            header('Location: ../views/matriculas.php?falha');
            exit();
    }
}
//cadastrar bairro
if (
   
    isset($_POST["dias_matricula"]) && !empty($_POST["dias_matricula"])
  
) {
    
    $escola = $_SESSION['escola'];
    $admin_model = new admin($escola);

    $dias_matricula = $_POST["dias_matricula"];
    foreach ($dias_matricula as $dia) {
        $curso_id = $dia["curso_id"];
        echo $data = $dia["data"];
        echo $hora_inicio = $dia["hora_inicio"];
        echo $hora_fim = $dia["hora_fim"];
        $result = $admin_model->cadastrar_matricula($curso_id, $data, $hora_inicio, $hora_fim);
    }

    switch ($result) {
        case 1:
            header('Location: ../views/matriculas.php?criado');
            exit();
        case 2:
            header('Location: ../views/matriculas.php?erro');
            exit();
        case 3:
            header('Location: ../views/matriculas.php?ja_existe');
            exit();
        default:
            header('Location: ../views/matriculas.php?falha');
            exit();
    }
}
//editar bairro 
else if (
    isset($_POST["id_matricula"]) && !empty($_POST["id_matricula"]) &&
    isset($_POST["data"]) && !empty($_POST["data"]) &&
    isset($_POST["hora"]) && !empty($_POST["hora"]) &&
    isset($_POST["acao"]) && $_POST["acao"] === "edit"
) {
    $id_matricula = $_POST["id_matricula"];
    $data = $_POST["data"];
    $hora = $_POST["hora"];

    $escola = $_SESSION['escola'];
    $admin_model = new admin($escola);
    $result = $admin_model->excluir_matricula($id_matricula, $data, $hora);

    switch ($result) {
        case 1:
            header('Location: ../views/matriculas.php?editado');
            exit();
        case 2:
            header('Location: ../views/matriculas.php?erro');
            exit();
        case 3:
            header('Location: ../views/matriculas.php?ja_existe');
            exit();
        default:
            header('Location: ../views/matriculas.php?falha');
            exit();
    }
}
//editar quantidade de vagas
else if (
    isset($_POST["acao"]) && $_POST["acao"] === "editar_vagas" &&
    isset($_POST["quantidades"]) && !empty($_POST["quantidades"])
) {
    $quantidade = (int) $_POST["quantidades"];
    $escola = $_SESSION['escola'];
    $admin_model = new admin($escola);
    $result = $admin_model->editar_quantidade_vaga($quantidade);

    switch ($result) {
        case 1:
            header('Location: ../views/cotas.php?vagas_editadas');
            exit();
        case 2:
            header('Location: ../views/cotas.php?erro');
            exit();
        case 3:
            header('Location: ../views/cotas.php?ja_existe');
            exit();
        default:
            header('Location: ../views/cotas.php?falha');
            exit();
    }
}
//excluir bairro 
else if (
    isset($_POST["id_bairro"]) && !empty($_POST["id_bairro"]) &&
    !isset($_POST["nome"]) && empty($_POST["nome"])
) {
    $id_bairro = $_POST["id_bairro"];

    $escola = $_SESSION['escola'];
    $admin_model = new admin($escola);
    $result = $admin_model->excluir_bairro($id_bairro);

    switch ($result) {
        case 1:
            header('Location: ../views/cotas.php?excluido');
            exit();
        case 2:
            header('Location: ../views/cotas.php?erro');
            exit();
        case 3:
            header('Location: ../views/cotas.php?nao_existe');
            exit();
        default:
            header('Location: ../views/cotas.php?falha');
            exit();
    }
} /*else {
    header('Location: ../index.php');
    exit();
}*/
