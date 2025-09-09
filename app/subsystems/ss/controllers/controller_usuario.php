<?php
require_once(__DIR__ . "/../models/model.admin.php");
print_r($_POST);

//cadastrar curso
if (
    !isset($_POST["id_usuario"]) && empty($_POST["id_usuario"]) &&
    isset($_POST["nome"]) && !empty($_POST["nome"]) &&
    isset($_POST["email"]) && !empty($_POST["email"]) &&
    isset($_POST["cpf"]) && !empty($_POST["cpf"]) &&
    isset($_POST["tipo"]) && !empty($_POST["tipo"])
) {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $cpf = $_POST["cpf"];
    $tipo = $_POST["tipo"];

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
    isset($_POST["id_usuario"]) && !empty($_POST["id_usuario"]) &&
    isset($_POST["nome"]) && !empty($_POST["nome"]) &&
    isset($_POST["email"]) && !empty($_POST["email"]) &&
    isset($_POST["cpf"]) && !empty($_POST["cpf"]) &&
    isset($_POST["tipo"]) && !empty($_POST["tipo"])
) {
    $id_usuario = $_POST["id_usuario"];
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $cpf = $_POST["cpf"];
    $tipo = $_POST["tipo"];

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
    isset($_POST["id_usuario"]) && !empty($_POST["id_usuario"]) &&
    !isset($_POST["nome"]) && empty($_POST["nome"]) &&
    !isset($_POST["email"]) && empty($_POST["email"]) &&
    !isset($_POST["cpf"]) && empty($_POST["cpf"]) &&
    !isset($_POST["tipo"]) && empty($_POST["tipo"])
) {
    $id_usuario = $_POST["id_usuario"];

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
} /*else {
    header('Location: ../index.php');
    exit();
}*/