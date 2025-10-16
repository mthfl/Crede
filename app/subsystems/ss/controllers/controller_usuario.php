<?php
require_once(__DIR__ . '/../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . "/../models/model.admin.php");
//print_r($_POST);

//cadastrar curso
if (
    empty($_POST["id_usuario"]) &&
    isset($_POST["nome"]) && !empty($_POST["nome"]) &&
    isset($_POST["email"]) && !empty($_POST["email"]) &&
    isset($_POST["cpf"]) && !empty($_POST["cpf"]) &&
    isset($_POST["tipo"]) && !empty($_POST["tipo"]) &&
    isset($_POST["perfil"]) && !empty($_POST["perfil"])
) {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $cpf = $_POST["cpf"];
    $tipo = $_POST["tipo"];
    $perfil = $_POST["perfil"];
    $escola = $_SESSION['escola'];
    $admin_model = new admin($escola);
    $result = $admin_model->cadastrar_usuario($nome, $email, $cpf, $tipo, $perfil);

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
} else if (
    isset($_POST["id_usuario"]) && !empty($_POST["id_usuario"]) &&
    isset($_POST["nome"]) && !empty($_POST["nome"]) &&
    isset($_POST["email"]) && !empty($_POST["email"]) &&
    isset($_POST["cpf"]) && !empty($_POST["cpf"]) &&
    isset($_POST["tipo"]) && !empty($_POST["tipo"]) &&
    isset($_POST["perfil"]) && !empty($_POST["perfil"])
) {
    $id_usuario = $_POST["id_usuario"];
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $cpf = $_POST["cpf"];
    $tipo = $_POST["tipo"];
    $perfil = $_POST["perfil"];

    $escola = $_SESSION['escola'];
    $admin_model = new admin($escola);
    $result = $admin_model->editar_usuario($id_usuario, $nome, $email, $cpf, $tipo, $perfil);

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
} else if (
    isset($_POST["id_usuario"]) && !empty($_POST["id_usuario"]) &&
    isset($_POST["habilitar"]) && !empty($_POST["habilitar"]) &&
    !isset($_POST["nome"]) && !isset($_POST["email"]) && !isset($_POST["cpf"]) && !isset($_POST["tipo"]) && !isset($_POST["perfil"]) && !isset($_POST["descricao"])
) {
    $id_usuario = $_POST["id_usuario"];
    $escola = $_SESSION['escola'];
    $admin_model = new admin($escola);
    $result = $admin_model->habilitar_usuario($id_usuario);

    switch ($result) {
        case 1:
            header('Location: ../views/usuario.php?editado');
            exit();
        case 2:
            header('Location: ../views/usuario.php?erro');
            exit();
        case 3:
            header('Location: ../views/usuario.php?nao_existe');
            exit();
        default:
            header('Location: ../views/usuario.php?falha');
            exit();
    }
} else if (
    isset($_POST["id_usuario"]) && !empty($_POST["id_usuario"]) &&
    !isset($_POST["nome"]) && empty($_POST["nome"]) &&
    !isset($_POST["email"]) && empty($_POST["email"]) &&
    !isset($_POST["cpf"]) && empty($_POST["cpf"]) &&
    !isset($_POST["tipo"]) && empty($_POST["tipo"]) &&
    !isset($_POST["perfil"]) && empty($_POST["perfil"]) &&
    !isset($_POST["descricao"]) && empty($_POST["descricao"])
) {
    $id_usuario = $_POST["id_usuario"];
    $escola = $_SESSION['escola'];
    $admin_model = new admin($escola);
    $result = $admin_model->desabilitar_usuario($id_usuario);

    switch ($result) {
        case 1:
            header('Location: ../views/usuario.php?desabilitado');
            exit();
        case 2:
            header('Location: ../views/usuario.php?erro');
            exit();
        case 3:
            header('Location: ../views/usuario.php?nao_existe');
            exit();
        default:
            header('Location: ../views/usuario.php?falha');
            exit();
    }
} else if (
    isset($_POST['descricao']) && !empty($_POST['descricao']) &&
    isset($_POST['id_candidato']) && !empty($_POST['id_candidato']) &&
    isset($_POST['id_usuario']) && !empty($_POST['id_usuario'])
) {
    $escola = $_SESSION['escola'];
    $cadastrador_model = new cadastrador($escola);

    $id_candidato = $_POST['id_candidato'];
    $id_usuario = $_POST['id_usuario'];
    $descricao = $_POST['descricao'];
    $result = $cadastrador_model->requisicao_alteracao($id_usuario, $id_candidato, $descricao);
    switch ($result) {
        case 1:
            header('Location: ../views/solicitar_alteracao.php?requisitado');
            exit();
        case 2:
            header('Location: ../views/solicitar_alteracao.php?erro');
            exit();
        case 3:
            header('Location: ../views/solicitar_alteracao.php?usuario_ou_aluno_nao_existe');
            exit();
        default:
            header('Location: ../views/solicitar_alteracao.php?falha');
            exit();
    }
} else if (
    isset($_POST['novo_status']) && !empty($_POST['novo_status']) &&
    isset($_POST['id_requisicao']) && !empty($_POST['id_requisicao'])
) {
    $id_requisicao = $_POST['id_requisicao'];
    $novo_status = $_POST['novo_status'];
    $escola = $_SESSION['escola'];
    $admin_model = new admin($escola);

    if ($novo_status == 'Concluido') {
        $result = $admin_model->requisicao_alteracao_realizada($id_requisicao);
    } else if ($novo_status == 'Recusado') {
        $result = $admin_model->requisicao_alteracao_recusada($id_requisicao);
    } else if ($novo_status == 'Pendente') {
        $result = $admin_model->requisicao_alteracao_pendente($id_requisicao);
    }
    switch ($result) {
        case 1:
            header('Location: ../views/solicitar_alteracao.php?realizado#tab-concluidas');
            exit();
        case 2:
            header('Location: ../views/solicitar_alteracao.php?erro');
            exit();
        default:
            header('Location: ../views/solicitar_alteracao.php?fatal');
            exit();
    }
} else if (empty($_POST['id_perfil']) && isset($_POST['nome_perfil']) && !empty($_POST['nome_perfil'])) {

    $nome_perfil = $_POST['nome_perfil'];
    $escola = $_SESSION['escola'];

    $admin_model = new admin($escola);
    echo $result = $admin_model->cadastrar_perfil($nome_perfil);

    switch ($result) {
        case 1:
            header('Location: ../views/perfis.php?criado');
            exit();
        case 2:
            header('Location: ../views/perfis.php?erro');
            exit();
        case 3:
            header('Location: ../views/perfis.php?ja_existe');
            exit();
        default:
            header('Location: ../views/perfis.php?falha');
            exit();
    }
} else if (isset($_POST['id_perfil']) && !empty($_POST['id_perfil']) && isset($_POST['excluir']) && !empty($_POST['excluir'])) {
    $id_perfil = $_POST['id_perfil'];
    $escola = $_SESSION['escola'];
    $admin_model = new admin($escola);
    $result = $admin_model->excluir_perfil($id_perfil);
    switch ($result) {
        case 1:
            header('Location: ../views/perfis.php?excluido');
            exit();
        case 2:
            header('Location: ../views/perfis.php?erro');
            exit();
        case 3:
            header('Location: ../views/perfis.php?nao_existe');
            exit();
        default:
            header('Location: ../views/perfis.php?falha');
            exit();
    }
} else if (isset($_POST['id_perfil']) && !empty($_POST['id_perfil']) && isset($_POST['nome_perfil']) && !empty($_POST['nome_perfil'])) {
    $id_perfil = $_POST['id_perfil'];
    $nome_perfil = $_POST['nome_perfil'];
    $escola = $_SESSION['escola'];
    $admin_model = new admin($escola);
    $result = $admin_model->editar_perfil($id_perfil, $nome_perfil);
    switch ($result) {
        case 1:
            header('Location: ../views/perfis.php?editado');
            exit();
        case 2:
            header('Location: ../views/perfis.php?erro');
            exit();
        case 3:
            header('Location: ../views/perfis.php?ja_existe');
            exit();
        default:
            header('Location: ../views/perfis.php?falha');
            exit();
    }
} else {
    header('Location: ../index.php');
    exit();
}
