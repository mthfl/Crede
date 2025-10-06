<?php

require_once(__DIR__ . "/../models/model.usuario.php");
print_r($_POST);

//cadastrar usuario
if (
    isset($_POST["nome"]) && !empty($_POST["nome"]) && is_string($_POST["nome"]) &&
    isset($_POST["email"]) && !empty($_POST["email"]) && is_string($_POST["email"]) &&
    isset($_POST["cpf"]) && !empty($_POST["cpf"]) && is_string($_POST["cpf"]) &&
    isset($_POST["escola"]) && !empty($_POST["escola"]) && is_string($_POST["escola"])
) {
    
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $cpf = $_POST["cpf"];
    $escola = $_POST["escola"];

    $model_usuario = new usuario();

    $result = $model_usuario->insert_user($escola, $nome, $email, $cpf);

    switch ($result) {

        
    }

} else {
    header('Location: ../index.php');
    exit();
}
