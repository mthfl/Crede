<?php
require_once(__DIR__ . '/../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();
require_once(__DIR__ . "/../models/model.cadastrador.php");
print_r($_POST);

if (
    isset($_POST["nome"]) && !empty($_POST["nome"])
    && !isset($_POST["cpf"]) && empty($_POST["cpf"])
) {
    $nome = $_POST["nome"];

    $escola = $_SESSION['escola'];
    $admin_model = new cadastrador($escola);
    $result = $admin_model->cadastrar_candidato();

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
}  /*else {
    header('Location: ../index.php');
    exit();
}*/