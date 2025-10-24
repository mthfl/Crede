<?php
require_once(__DIR__ . '/../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . "/../models/model.admin.php");
//print_r($_POST);

//cadastrar curso
if (
    empty($_POST["curso_id"]) &&
    isset($_POST["nome_curso"]) && !empty($_POST["nome_curso"]) &&
    isset($_POST["cor_curso"]) && !empty($_POST["cor_curso"])
) {
    strtoupper($nome_curso = $_POST["nome_curso"]);
    $cor = $_POST["cor_curso"];

    $escola = $_SESSION['escola'];
    $admin_model = new admin($escola);
    $result = $admin_model->cadastrar_curso($nome_curso, $cor);

    switch ($result) {
        case 1:
            header('Location: ../views/cursos.php?criado');
            exit();
        case 2:
            header('Location: ../views/cursos.php?erro');
            exit();
        case 3:
            header('Location: ../views/cursos.php?ja_existe');
            exit();
        default:
            header('Location: ../views/cursos.php?falha');
            exit();
    }
}
//editar curso
else if (
    isset($_POST["curso_id"]) && !empty($_POST["curso_id"]) &&
    isset($_POST["nome_curso"]) && !empty($_POST["nome_curso"]) &&
    isset($_POST["cor_curso"]) && !empty($_POST["cor_curso"])
) {
    $id_curso = $_POST["curso_id"];
    strtoupper($nome_curso = $_POST["nome_curso"]);
    $cor = $_POST["cor_curso"];

    $escola = $_SESSION['escola'];
    $admin_model = new admin($escola);
    $result = $admin_model->editar_curso($id_curso, $nome_curso, $cor);

    switch ($result) {
        case 1:
            header('Location: ../views/cursos.php?editado');
            exit();
        case 2:
            header('Location: ../views/cursos.php?erro');
            exit();
        case 3:
            header('Location: ../views/cursos.php?ja_existe');
            exit();
        default:
            header('Location: ../views/cursos.php?falha');
            exit();
    }
} //excluir curso
else if (
    isset($_POST["id_curso"]) && !empty($_POST["id_curso"]) &&
    !isset($_POST["nome_curso"]) && empty($_POST["nome_curso"]) &&
    !isset($_POST["cor"]) && empty($_POST["cor"])
) {
    $id_curso = $_POST["id_curso"];

    $escola = $_SESSION['escola'];
    $admin_model = new admin($escola);
    $result = $admin_model->excluir_curso($id_curso);

    switch ($result) {
        case 1:
            header('Location: ../views/cursos.php?excluido');
            exit();
        case 2:
            header('Location: ../views/cursos.php?erro');
            exit();
        case 3:
            header('Location: ../views/cursos.php?nao_existe');
            exit();
        case 4:
            header('Location: ../views/cursos.php?candidato_associado');
            exit();
        default:
            header('Location: ../views/cursos.php?falha');
            exit();
    }
} else {
    header('Location: ../index.php');
    exit();
}
