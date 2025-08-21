<?php 
require_once(__DIR__ . "/../models/model.admin.php");
print_r($_POST);

if (
    !isset($_POST["perm_id"]) && empty($_POST["perm_id"]) &&
    isset($_POST["sistema"]) && !empty($_POST["sistema"]) &&
    isset($_POST["user_id"]) && !empty($_POST["user_id"]) &&
    isset($_POST["tipo_permissao"]) && !empty($_POST["tipo_permissao"])
) {
    $id_tipo_usuario = $_POST["tipo_permissao"];
    $id_sistema = $_POST["sistema"];
    $id_usuairo = $_POST["user_id"];

    $admin_model = new admin();
    $result = $admin_model->adicionar_permissao($id_usuairo, $id_tipo_usuario,$id_sistema);

    switch ($result) {
        case 1:
            header('Location: ../views/permissoes.php?criado');
            exit();
        case 2:
            header('Location: ../views/permissoes.php?erro');
            exit();
        case 3:
            header('Location: ../views/permissoes.php?ja_existe');
            exit();
        default:
            header('Location: ../views/permissoes.php?falha');
            exit();
    }
}
else if (
    isset($_POST["perm_id"]) && !empty($_POST["perm_id"]) &&
    !isset($_POST["sistema"]) && empty($_POST["sistema"])
) {
    $perm_id = $_POST["perm_id"];

    $admin_model = new admin();
    $result = $admin_model->excluir_permissao($perm_id);

    switch ($result) {
        case 1:
            header('Location: ../views/permissoes.php?excluido');
            exit();
        case 2:
            header('Location: ../views/permissoes.php?erro');
            exit();
        default:
            header('Location: ../views/permissoes.php?falha');
            exit();
    }
} else if (
    isset($_POST["perm_id"]) && !empty($_POST["perm_id"]) &&
    isset($_POST["sistema"]) && !empty($_POST["sistema"]) &&
    isset($_POST["user_id"]) && !empty($_POST["user_id"]) &&
    isset($_POST["tipo_permissao"]) && !empty($_POST["tipo_permissao"])
) {
    $perm_id = $_POST["perm_id"];
    $id_tipo_usuario = $_POST["tipo_permissao"];
    $id_sistema = $_POST["sistema"];
    $id_usuairo = $_POST["user_id"];

    $admin_model = new admin();
    $result = $admin_model->editar_permissao($perm_id, $id_usuairo, $id_tipo_usuario,$id_sistema);

    switch ($result) {
        case 1:
            header('Location: ../views/permissoes.php?editado');
            exit();
        case 2:
            header('Location: ../views/permissoes.php?erro');
            exit();
        case 3:
            header('Location: ../views/permissoes.php?ja_existe');
            exit();
        default:
            header('Location: ../views/permissoes.php?falha');
            exit();
    }
}
?>