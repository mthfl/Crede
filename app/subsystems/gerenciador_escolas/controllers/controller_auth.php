<?php
require_once(__DIR__ . "/../models/model.usuario.php");
require_once(__DIR__ . "/../config/connect_escolas.php");
print_r($_POST);
//pre-cadastro
if (
    isset($_POST['escola']) && !empty($_POST['escola']) && is_string($_POST['escola']) &&
    isset($_POST['CPF']) && !empty($_POST['CPF']) && is_string($_POST['CPF']) &&
    isset($_POST['email']) && !empty($_POST['email']) && is_string($_POST['email']) &&
    isset($_POST['escola_banco']) && !empty($_POST['escola_banco']) && is_string($_POST['escola_banco'])
) {

    $escola = $_POST['escola'];
    $email = $_POST['email'];
    $cpf = $_POST['CPF'];
    $nome_escola_banco = $_POST['escola_banco'];

    new connect_escolas($nome_escola_banco);
    $model_usuario = new model_usuario($nome_escola_banco);
    $result = $model_usuario->pre_cadastro($cpf, $email);

    switch ($result) {
        case 1:
            header("Location: ../views/primeiro_acesso.php?escola=$escola&banco=$nome_escola_banco");
            exit();
        case 2:
            header("Location: ../views/login.php?escola=$escola&erro");
            exit();
        case 3:
            header("Location: ../views/login.php?escola=$escola&ja_tem_primeiro_acesso_ou_erro_senha_email");
            exit();
        case 4:
            header("Location: ../views/login.php?escola=$escola&usuario_desativado");
            exit();
        default:
            header("Location: ../windows/fatal_erro.php");
            exit();
    }
}
//primeiro acesso
else if (
    isset($_POST['escola']) && !empty($_POST['escola']) && is_string($_POST['escola']) &&
    isset($_POST['senha']) && !empty($_POST['senha']) && is_string($_POST['senha']) &&
    isset($_POST['confirmar_senha']) && !empty($_POST['confirmar_senha']) && is_string($_POST['confirmar_senha']) &&
    isset($_POST['cpf']) && !empty($_POST['cpf']) && is_string($_POST['cpf']) &&
    isset($_POST['email']) && !empty($_POST['email']) && is_string($_POST['email']) &&
    isset($_POST['banco']) && !empty($_POST['banco']) && is_string($_POST['banco'])
) {

    $escola = $_POST['escola'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];
    $nome_escola_banco = $_POST['banco'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if ($senha !== $confirmar_senha) {

        header("location:../views/primeiro_acesso.php?senhas_nao_condizem");
        exit();
    }

    new connect_escolas($nome_escola_banco);
    $model_usuario = new model_usuario($nome_escola_banco);
    $result = $model_usuario->primeiro_acesso($cpf, $email, $senha);

    switch ($result) {
        case 1:
            header("Location: ../views/login.php?escola=$escola");
            exit();
        case 2:
            header("Location: ../views/primeiro_acesso.php?escola=$escola&erro");
            exit();
        case 3:
            header("Location: ../views/login.php?escola=$escola&nao_existe");
            exit();
        case 4:
            header("Location: ../views/login.php?escola=$escola&usuario_desativado");
            exit();
        default:
            header("Location: ../windows/fatal_erro.php");
            exit();
    }
}

//login
else if (
    isset($_POST['escola']) && !empty($_POST['escola']) && is_string($_POST['escola']) &&
    isset($_POST['senha']) && !empty($_POST['senha']) && is_string($_POST['senha']) &&
    isset($_POST['email']) && !empty($_POST['email']) && is_string($_POST['email']) &&
    isset($_POST['escola_banco']) && !empty($_POST['escola_banco']) && is_string($_POST['escola_banco'])
) {

    $escola = $_POST['escola'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $nome_escola_banco = $_POST['escola_banco'];

    
    new connect_escolas($nome_escola_banco);
    $model_usuario = new model_usuario($nome_escola_banco);
    $result = $model_usuario->login($email, $senha, $nome_escola_banco);

    switch ($result) {
        case 1:
            header("Location: ../../ss/index.php");
            exit();
        case 2:
            header("Location: ../views/login.php?escola=$escola&erro");
            exit();
        case 3:
            header("Location: ../views/login.php?escola=$escola&erro_email_senha");
            exit();
        case 4:
            header("Location: ../views/login.php?escola=$escola&usuario_desativado");
            exit();
        default:
            header("Location: ../views/login.php?escola=$escola&falha");
            exit();
    }
}else{

    header('location:../login.php');
    exit();
}
