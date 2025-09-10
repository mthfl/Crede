<?php
// Bloco específico para USUÁRIO (não altera os blocos existentes abaixo)
if (isset($_POST['form']) && $_POST['form'] === 'usuario') {
    require_once(__DIR__ . "/../models/model.admin.php");
    require_once(__DIR__ . "/../models/sessions.php");

    $session = new sessions();
    $session->autenticar_session();
    $session->tempo_session();

    $nome_completo_escola = strtolower($_SESSION['escola'] ?? '');
    $nome_array = explode(' ', $nome_completo_escola);
    if (count($nome_array) >= 3) {
        $nome_escola_banco = $nome_array[1] . '_' . $nome_array[2];
    } else {
        $nome_escola_banco = str_replace(' ', '_', $nome_completo_escola);
    }

    $admin_model = new admin($nome_escola_banco);

    $id_usuario = isset($_POST['id_usuario']) ? trim($_POST['id_usuario']) : '';
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $cpf = isset($_POST['cpf']) ? trim($_POST['cpf']) : '';
    $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';

    $cpf = preg_replace('/\D+/', '', $cpf);

    if ($id_usuario === '' && $nome !== '' && $email !== '' && $cpf !== '' && $tipo !== '') {
        $result = $admin_model->cadastrar_usuario($nome, $email, $cpf, $tipo);
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
    }

    if ($id_usuario !== '' && $nome !== '' && $email !== '' && $cpf !== '' && $tipo !== '') {
        $result = $admin_model->editar_usuario((int)$id_usuario, $nome, $email, $cpf, $tipo);
        switch ($result) {
            case 1:
                header('Location: ../views/usuario.php?editado');
                exit();
            case 2:
                header('Location: ../views/usuario.php?erro');
                exit();
            case 3:
                header('Location: ../views/usuario.php?nao_encontrado');
                exit();
            default:
                header('Location: ../views/usuario.php?falha');
                exit();
        }
    }

    if ($id_usuario !== '' && $nome === '' && $email === '' && $cpf === '' && $tipo === '') {
        $result = $admin_model->excluir_usuario((int)$id_usuario);
        switch ($result) {
            case 1:
                header('Location: ../views/usuario.php?excluido');
                exit();
            case 2:
                header('Location: ../views/usuario.php?erro');
                exit();
            case 3:
                header('Location: ../views/usuario.php?nao_encontrado');
                exit();
            default:
                header('Location: ../views/usuario.php?falha');
                exit();
        }
    }

    header('Location: ../views/usuario.php');
    exit();
}

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