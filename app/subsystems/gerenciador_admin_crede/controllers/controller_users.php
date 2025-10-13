<?php

require_once(__DIR__ . "/../models/model.usuario.php");

// Remover print_r em produção
// print_r($_POST);

// Cadastrar usuário
if (
    (!isset($_POST["action"]) || $_POST["action"] == "create") &&
    isset($_POST["nome"]) && !empty($_POST["nome"]) && is_string($_POST["nome"]) &&
    isset($_POST["email"]) && !empty($_POST["email"]) && is_string($_POST["email"]) &&
    isset($_POST["cpf"]) && !empty($_POST["cpf"]) && is_string($_POST["cpf"]) &&
    isset($_POST["escola"]) && !empty($_POST["escola"]) && is_string($_POST["escola"])
) {
    
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $cpf = $_POST["cpf"];
    $escola = $_POST["escola"];
    
    // Aplicar máscara no CPF se não estiver formatado
    if (!preg_match('/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $cpf)) {
        // Remove caracteres não numéricos
        $cpf_numeros = preg_replace('/\D/', '', $cpf);
        // Aplica a máscara se tiver 11 dígitos
        if (strlen($cpf_numeros) === 11) {
            $cpf = substr($cpf_numeros, 0, 3) . '.' . substr($cpf_numeros, 3, 3) . '.' . substr($cpf_numeros, 6, 3) . '-' . substr($cpf_numeros, 9, 2);
        }
    }

    $model_usuario = new usuario();

    $result = $model_usuario->insert_user($escola, $nome, $email, $cpf);

    switch ($result) {
        case 1:
            // Sucesso
            header('Location: ../index.php?success=1');
            exit();
        case 2:
            // Erro ao inserir
            header('Location: ../index.php?error=2');
            exit();
        case 3:
            // Usuário já existe
            header('Location: ../index.php?error=3');
            exit();
        default:
            // Erro desconhecido
            header('Location: ../index.php?error=0');
            exit();
    }
} 
// Editar usuário
else if (
    isset($_POST["action"]) && $_POST["action"] == "update" &&
    isset($_POST["id"]) && !empty($_POST["id"]) && 
    isset($_POST["nome"]) && !empty($_POST["nome"]) && is_string($_POST["nome"]) &&
    isset($_POST["email"]) && !empty($_POST["email"]) && is_string($_POST["email"]) &&
    isset($_POST["cpf"]) && !empty($_POST["cpf"]) && is_string($_POST["cpf"]) &&
    isset($_POST["escola"]) && !empty($_POST["escola"]) && is_string($_POST["escola"])
) {
    $id = intval($_POST["id"]);
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $cpf = $_POST["cpf"];
    $escola = $_POST["escola"];
    
    // Aplicar máscara no CPF se não estiver formatado
    if (!preg_match('/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $cpf)) {
        // Remove caracteres não numéricos
        $cpf_numeros = preg_replace('/\D/', '', $cpf);
        // Aplica a máscara se tiver 11 dígitos
        if (strlen($cpf_numeros) === 11) {
            $cpf = substr($cpf_numeros, 0, 3) . '.' . substr($cpf_numeros, 3, 3) . '.' . substr($cpf_numeros, 6, 3) . '-' . substr($cpf_numeros, 9, 2);
        }
    }

    $model_usuario = new usuario();
    $result = $model_usuario->update_user($escola, $id, $nome, $email, $cpf);

    if ($result) {
        header('Location: ../index.php?success=2');
    } else {
        header('Location: ../index.php?error=4');
    }
    exit();
}
// Desativar usuário
else if (
    isset($_POST["action"]) && $_POST["action"] == "deactivate" &&
    isset($_POST["id"]) && !empty($_POST["id"]) &&
    isset($_POST["escola"]) && !empty($_POST["escola"])
) {
    $id = intval($_POST["id"]);
    $escola = $_POST["escola"];

    $model_usuario = new usuario();
    $result = $model_usuario->delete_user($escola, $id);

    if ($result) {
        header('Location: ../index.php?success=3');
    } else {
        header('Location: ../index.php?error=5');
    }
    exit();
}
// Ativar usuário
else if (
    isset($_POST["action"]) && $_POST["action"] == "activate" &&
    isset($_POST["id"]) && !empty($_POST["id"]) &&
    isset($_POST["escola"]) && !empty($_POST["escola"])
) {
    $id = intval($_POST["id"]);
    $escola = $_POST["escola"];

    $model_usuario = new usuario();
    $result = $model_usuario->activate_user($escola, $id);

    if ($result) {
        header('Location: ../index.php?success=4');
    } else {
        header('Location: ../index.php?error=6');
    }
    exit();
} 
else {
    header('Location: ../index.php?error=1');
    exit();
}