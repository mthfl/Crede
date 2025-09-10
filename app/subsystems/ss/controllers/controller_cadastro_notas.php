<?php
require_once(__DIR__ . '/../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../config/connect.php');
$escola = $_SESSION['escola'];

require_once(__DIR__ . "/../models/model.cadastrador.php");
print_r($_POST);

if (
    isset($_POST["user_id"]) && !empty($_POST["user_id"]) &&
    isset($_POST["nome"]) && !empty($_POST["nome"]) &&
    isset($_POST["email"]) && !empty($_POST["email"]) &&
    isset($_POST["cpf"]) && !empty($_POST["cpf"]) &&
    isset($_POST["setor"]) && !empty($_POST["setor"])
) {
    $id_usuario = $_POST["user_id"];
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $cpf = $_POST["cpf"];
    $id_setor = $_POST["setor"];

    $admin_model = new admin($escola);
    $result = $admin_model->cadastrar_candidato($id_usuario, $nome, $email, $cpf, $id_setor);

    switch ($result) {
        case 1:
            header('Location: ../views/usuario.php?editado');
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