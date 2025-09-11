<?php
require_once(__DIR__ . '/../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . "/../models/model.admin.php");
print_r($_POST);

//cadastrar bairro
if (
    empty($_POST["id_bairro"]) &&
    isset($_POST["nome"]) && !empty($_POST["nome"])
) {
    $nome = $_POST["nome"];

    $escola = $_SESSION['escola'];
    $admin_model = new admin($escola);
    $result = $admin_model->cadastrar_bairro($nome);

    switch ($result) {
        case 1:
            header('Location: ../views/cotas.php?criado');
            exit();
        case 2:
            header('Location: ../views/cotas.php?erro');
            exit();
        case 3:
            header('Location: ../views/cotas.php?ja_existe');
            exit();
        default:
            header('Location: ../views/cotas.php?falha');
            exit();
    }
} else

    //editar curso
    if (
        isset($_POST["id_bairro"]) && !empty($_POST["id_bairro"]) &&
        isset($_POST["nome"]) && !empty($_POST["nome"])
    ) {
        $id_bairro = $_POST["id_bairro"];
        $nome = $_POST["nome"];

        $escola = $_SESSION['escola'];
        $admin_model = new admin($escola);
        $result = $admin_model->editar_bairro($id_bairro, $nome);

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

        //excluir curso
        if (
            isset($_POST["id_bairro"]) && !empty($_POST["id_bairro"]) &&
            !isset($_POST["nome"]) && empty($_POST["nome"])
        ) {
            $id_bairro = $_POST["id_bairro"];

            $escola = $_SESSION['escola'];
            $admin_model = new admin($escola);
            $result = $admin_model->excluir_curso($id_bairro);

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