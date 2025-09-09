<?php
require_once(__DIR__ . "/../models/model.admin.php");
print_r($_POST);

//cadastrar curso
if (
    !isset($_POST["id_curso"]) && empty($_POST["id_curso"]) &&
    isset($_POST["nome_curso"]) && !empty($_POST["nome_curso"]) &&
    isset($_POST["cor"]) && !empty($_POST["cor"])
) {
    $nome_curso = $_POST["nome_curso"];
    $cor = $_POST["cor"];

    $admin_model = new admin();
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
} else 

//editar curso
if (
    isset($_POST["id_curso"]) && !empty($_POST["id_curso"]) &&
    isset($_POST["nome_curso"]) && !empty($_POST["nome_curso"]) &&
    isset($_POST["cor"]) && !empty($_POST["cor"])
) {
    $id_curso = $_POST["id_curso"];
    $nome_curso = $_POST["nome_curso"];
    $cor = $_POST["cor"];

    $admin_model = new admin();
    $result = $admin_model->editar_curso($id_curso, $nome_curso, $cor);

    switch ($result) {
        case 1:
            header('Location: ../views/usuario.php?criado');
            exit();
        case 2:
            header('Location: ../views/usuario.php?erro');
            exit();
        case 3:
            header('Location: ../views/usuario.php?ja_existe');
            exit();
        default:
            header('Location: ../views/usuario.php?falha');
            exit();
    }
} else 

//excluir curso
if (
    isset($_POST["id_curso"]) && !empty($_POST["id_curso"]) &&
    !isset($_POST["nome_curso"]) && empty($_POST["nome_curso"]) &&
    !isset($_POST["cor"]) && empty($_POST["cor"])
) {
    $id_curso = $_POST["id_curso"];

    $admin_model = new admin();
    $result = $admin_model->excluir_curso($id_curso);

    switch ($result) {
        case 1:
            header('Location: ../views/usuario.php?criado');
            exit();
        case 2:
            header('Location: ../views/usuario.php?erro');
            exit();
        case 3:
            header('Location: ../views/usuario.php?ja_existe');
            exit();
        default:
            header('Location: ../views/usuario.php?falha');
            exit();
    }
}/* else {
    header('Location: ../index.php');
    exit();
}*/