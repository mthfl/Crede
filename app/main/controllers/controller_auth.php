<?php
require_once(__DIR__."/../models/model.usuario.php");

//pre-cadastro
if (
    isset($_POST['CPF']) && !empty($_POST['CPF']) && is_string($_POST['CPF']) &&
    isset($_POST['email']) && !empty($_POST['email']) && is_string($_POST['email'])
) {

    $email = $_POST['email'];
    $cpf = $_POST['CPF'];

    $model_usuario = new model_usuario();
    $result = $model_usuario->pre_cadastro($cpf, $email);

    switch ($result) {
        case 1:
            header('Location: ../views/primeiro_acesso.php');
            exit();
        case 2:
            header('Location: ../login.php?erro');
            exit();
        case 3:
            header('Location: ../login.php?nao_existe');
            exit();
        default:
            header('Location: ../windows/fatal_erro.php');
            exit();
    }
}
//primeiro acesso
else if (
    isset($_POST['senha']) && !empty($_POST['senha']) && is_string($_POST['senha']) &&
    isset($_POST['confirmar_senha']) && !empty($_POST['confirmar_senha']) && is_string($_POST['confirmar_senha']) &&
    isset($_POST['cpf']) && !empty($_POST['cpf']) && is_string($_POST['cpf']) &&
    isset($_POST['email']) && !empty($_POST['email']) && is_string($_POST['email'])
) {

    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];

    if ($senha !== $confirmar_senha) {

        header('location:../views/primeiro_acesso.php?senhas_nao_condizem');
        exit();
    }

    $model_usuario = new model_usuario();
    $result = $model_usuario->primeiro_acesso($cpf, $email, $senha);

    switch ($result) {
        case 1:
            header('Location: ../login.php');
            exit();
        case 2:
            header('Location: ../views/primeiro_acesso.php?erro');
            exit();
        case 3:
            header('Location: ../login.php?nao_existe');
            exit();
        default:
            header('Location: ../login.php?falha');
            exit();
    }
}

//login
else if (
    isset($_POST['senha']) && !empty($_POST['senha']) && is_string($_POST['senha']) &&
    isset($_POST['email']) && !empty($_POST['email']) && is_string($_POST['email'])
) {

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $model_usuario = new model_usuario();
    $result = $model_usuario->login($email, $senha);

    switch ($result) {
        case 1:
            header('Location: ../views/subsystems.php');
            exit();
        case 2:
            header('Location: ../login.php?erro');
            exit();
        case 3:
            header('Location: ../login.php?erro_email');
            exit();
        case 4:
            header('Location: ../login.php?erro_senha');
            exit();
        default:
            header('Location: ../login.php?falha');
            exit();
    }
}else{

    header('location:../login.php');
    exit();
}
