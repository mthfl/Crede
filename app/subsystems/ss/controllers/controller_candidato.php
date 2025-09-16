<?php
require_once(__DIR__ . '/../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();
require_once(__DIR__ . "/../models/model.admin.php");
require_once(__DIR__ . "/../models/model.cadastrador.php");
require_once(__DIR__ . "/../models/model.select.php");
//print_r($_POST);

if (
    isset($_POST["nome"]) && !empty($_POST["nome"]) &&
    isset($_POST["data_nascimento"]) && !empty($_POST["data_nascimento"]) &&
    isset($_POST["curso_id"]) && !empty($_POST["curso_id"]) &&
    isset($_POST["tipo_escola"]) && !empty($_POST["tipo_escola"]) &&
    isset($_POST["portugues_6"]) && !empty($_POST["portugues_6"]) &&
    isset($_POST["portugues_7"]) && !empty($_POST["portugues_7"]) &&
    isset($_POST["portugues_8"]) && !empty($_POST["portugues_8"]) &&
    isset($_POST["matematica_6"]) && !empty($_POST["matematica_6"]) &&
    isset($_POST["matematica_7"]) && !empty($_POST["matematica_7"]) &&
    isset($_POST["matematica_8"]) && !empty($_POST["matematica_8"]) &&
    isset($_POST["historia_6"]) && !empty($_POST["historia_6"]) &&
    isset($_POST["historia_7"]) && !empty($_POST["historia_7"]) &&
    isset($_POST["historia_8"]) && !empty($_POST["historia_8"]) &&
    isset($_POST["geografia_6"]) && !empty($_POST["geografia_6"]) &&
    isset($_POST["geografia_7"]) && !empty($_POST["geografia_7"]) &&
    isset($_POST["geografia_8"]) && !empty($_POST["geografia_8"]) &&
    isset($_POST["ciencias_6"]) && !empty($_POST["ciencias_6"]) &&
    isset($_POST["ciencias_7"]) && !empty($_POST["ciencias_7"]) &&
    isset($_POST["ciencias_8"]) && !empty($_POST["ciencias_8"]) &&
    isset($_POST["ingles_6"]) && !empty($_POST["ingles_6"]) &&
    isset($_POST["ingles_7"]) && !empty($_POST["ingles_7"]) &&
    isset($_POST["ingles_8"]) && !empty($_POST["ingles_8"])
) {
    $nome = $_POST["nome"];
    $data_nascimento = $_POST["data_nascimento"];
    if (isset($_POST['ampla'])) {
        $pcd = isset($_POST["pcd"]) && $_POST["pcd"] == 'on' ? 1 : 0;
        $bairro = isset($_POST["bairro"]) && $_POST["bairro"] == 'on' ? 1 : 0;
    } else {
        $pcd = 0;
        $bairro = 0;
    }
    $id_curso1 = (int)$_POST["curso_id"];
    $publica = $_POST["tipo_escola"] == 'publica' ? 1 : 0;
    $id_cadastrador = $_SESSION['id'];
    $lp_6ano = (int)str_replace(',', '.', $_POST["portugues_6"]);
    $lp_7ano = (int)str_replace(',', '.', $_POST["portugues_7"]);
    $lp_8ano = (int)str_replace(',', '.', $_POST["portugues_8"]);
    $mate_6ano = (int)str_replace(',', '.', $_POST["matematica_6"]);
    $mate_7ano = (int)str_replace(',', '.', $_POST["matematica_7"]);
    $mate_8ano = (int)str_replace(',', '.', $_POST["matematica_8"]);
    $hist_6ano = (int)str_replace(',', '.', $_POST["historia_6"]);
    $hist_7ano = (int)str_replace(',', '.', $_POST["historia_7"]);
    $hist_8ano = (int)str_replace(',', '.', $_POST["historia_8"]);
    $geo_6ano = (int)str_replace(',', '.', $_POST["geografia_6"]);
    $geo_7ano = (int)str_replace(',', '.', $_POST["geografia_7"]);
    $geo_8ano = (int)str_replace(',', '.', $_POST["geografia_8"]);
    $cien_6ano = (int)str_replace(',', '.', $_POST["ciencias_6"]);
    $cien_7ano = (int)str_replace(',', '.', $_POST["ciencias_7"]);
    $cien_8ano = (int)str_replace(',', '.', $_POST["ciencias_8"]);
    $li_6ano = (int)str_replace(',', '.', $_POST["ingles_6"]);
    $li_7ano = (int)str_replace(',', '.', $_POST["ingles_7"]);
    $li_8ano = (int)str_replace(',', '.', $_POST["ingles_8"]);
    $artes_6ano = (int)str_replace(',', '.', ($_POST["artes_6"] ?? 0));
    $artes_7ano = (int)str_replace(',', '.', ($_POST["artes_7"] ?? 0));
    $artes_8ano = (int)str_replace(',', '.', ($_POST["artes_8"] ?? 0));
    $ef_6ano = (int)str_replace(',', '.', ($_POST["edfisica_6"] ?? 0));
    $ef_7ano = (int)str_replace(',', '.', ($_POST["edfisica_7"] ?? 0));
    $ef_8ano = (int)str_replace(',', '.', ($_POST["edfisica_8"] ?? 0));
    $reli_6ano = (int)str_replace(',', '.', ($_POST["religiao_6"] ?? 0));
    $reli_7ano = (int)str_replace(',', '.', ($_POST["religiao_7"] ?? 0));
    $reli_8ano = (int)str_replace(',', '.', ($_POST["religiao_8"] ?? 0));
    $lp_9ano = (int)str_replace(',', '.', ($_POST["portugues_9_media"] ?? 0));
    $mate_9ano = (int)str_replace(',', '.', ($_POST["matematica_9_media"] ?? 0));
    $hist_9ano = (int)str_replace(',', '.', ($_POST["historia_9_media"] ?? 0));
    $geo_9ano = (int)str_replace(',', '.', ($_POST["geografia_9_media"] ?? 0));
    $cien_9ano = (int)str_replace(',', '.', ($_POST["ciencias_9_media"] ?? 0));
    $li_9ano = (int)str_replace(',', '.', ($_POST["ingles_9_media"] ?? 0));
    $artes_9ano = (int)str_replace(',', '.', ($_POST["artes_9_media"] ?? 0));
    $ef_9ano = (int)str_replace(',', '.', ($_POST["edfisica_9_media"] ?? 0));
    $reli_9ano = (int)str_replace(',', '.', ($_POST["religiao_9_media"] ?? 0));
    $lp_1bim_9ano = (int)str_replace(',', '.', ($_POST["portugues_9_1"] ?? 0));
    $lp_2bim_9ano = (int)str_replace(',', '.', ($_POST["portugues_9_2"] ?? 0));
    $lp_3bim_9ano = (int)str_replace(',', '.', ($_POST["portugues_9_3"] ?? 0));
    $mate_1bim_9ano = (int)str_replace(',', '.', ($_POST["matematica_9_1"] ?? 0));
    $mate_2bim_9ano = (int)str_replace(',', '.', ($_POST["matematica_9_2"] ?? 0));
    $mate_3bim_9ano = (int)str_replace(',', '.', ($_POST["matematica_9_3"] ?? 0));
    $hist_1bim_9ano = (int)str_replace(',', '.', ($_POST["historia_9_1"] ?? 0));
    $hist_2bim_9ano = (int)str_replace(',', '.', ($_POST["historia_9_2"] ?? 0));
    $hist_3bim_9ano = (int)str_replace(',', '.', ($_POST["historia_9_3"] ?? 0));
    $geo_1bim_9ano = (int)str_replace(',', '.', ($_POST["geografia_9_1"] ?? 0));
    $geo_2bim_9ano = (int)str_replace(',', '.', ($_POST["geografia_9_2"] ?? 0));
    $geo_3bim_9ano = (int)str_replace(',', '.', ($_POST["geografia_9_3"] ?? 0));
    $cien_1bim_9ano = (int)str_replace(',', '.', ($_POST["ciencias_9_1"] ?? 0));
    $cien_2bim_9ano = (int)str_replace(',', '.', ($_POST["ciencias_9_2"] ?? 0));
    $cien_3bim_9ano = (int)str_replace(',', '.', ($_POST["ciencias_9_3"] ?? 0));
    $li_1bim_9ano = (int)str_replace(',', '.', ($_POST["ingles_9_1"] ?? 0));
    $li_2bim_9ano = (int)str_replace(',', '.', ($_POST["ingles_9_2"] ?? 0));
    $li_3bim_9ano = (int)str_replace(',', '.', ($_POST["ingles_9_3"] ?? 0));
    $artes_1bim_9ano = (int)str_replace(',', '.', ($_POST["artes_9_1"] ?? 0));
    $artes_2bim_9ano = (int)str_replace(',', '.', ($_POST["artes_9_2"] ?? 0));
    $artes_3bim_9ano = (int)str_replace(',', '.', ($_POST["artes_9_3"] ?? 0));
    $ef_1bim_9ano = (int)str_replace(',', '.', ($_POST["edfisica_9_1"] ?? 0));
    $ef_2bim_9ano = (int)str_replace(',', '.', ($_POST["edfisica_9_2"] ?? 0));
    $ef_3bim_9ano = (int)str_replace(',', '.', ($_POST["edfisica_9_3"] ?? 0));
    $reli_1bim_9ano = (int)str_replace(',', '.', ($_POST["religiao_9_1"] ?? 0));
    $reli_2bim_9ano = (int)str_replace(',', '.', ($_POST["religiao_9_2"] ?? 0));
    $reli_3bim_9ano = (int)str_replace(',', '.', ($_POST["religiao_9_3"] ?? 0));

    $escola = $_SESSION['escola'];
    $admin_model = new cadastrador($escola);
    $result = $admin_model->cadastrar_candidato(
        $nome,
        $id_curso1,
        $data_nascimento,
        $bairro,
        $publica,
        $pcd,
        $id_cadastrador,
        $lp_6ano,
        $artes_6ano,
        $ef_6ano,
        $li_6ano,
        $mate_6ano,
        $cien_6ano,
        $geo_6ano,
        $hist_6ano,
        $reli_6ano,
        $lp_7ano,
        $artes_7ano,
        $ef_7ano,
        $li_7ano,
        $mate_7ano,
        $cien_7ano,
        $geo_7ano,
        $hist_7ano,
        $reli_7ano,
        $lp_8ano,
        $artes_8ano,
        $ef_8ano,
        $li_8ano,
        $mate_8ano,
        $cien_8ano,
        $geo_8ano,
        $hist_8ano,
        $reli_8ano,
        $lp_9ano,
        $artes_9ano,
        $ef_9ano,
        $li_9ano,
        $mate_9ano,
        $cien_9ano,
        $geo_9ano,
        $hist_9ano,
        $reli_9ano,
        $lp_1bim_9ano,
        $artes_1bim_9ano,
        $ef_1bim_9ano,
        $li_1bim_9ano,
        $mate_1bim_9ano,
        $cien_1bim_9ano,
        $geo_1bim_9ano,
        $hist_1bim_9ano,
        $reli_1bim_9ano,
        $lp_2bim_9ano,
        $artes_2bim_9ano,
        $ef_2bim_9ano,
        $li_2bim_9ano,
        $mate_2bim_9ano,
        $cien_2bim_9ano,
        $geo_2bim_9ano,
        $hist_2bim_9ano,
        $reli_2bim_9ano,
        $lp_3bim_9ano,
        $artes_3bim_9ano,
        $ef_3bim_9ano,
        $li_3bim_9ano,
        $mate_3bim_9ano,
        $cien_3bim_9ano,
        $geo_3bim_9ano,
        $hist_3bim_9ano,
        $reli_3bim_9ano
    );

    switch ($result) {
        case 1:
            header('Location: ../views/windows/success.php?criado');
            exit();
        case 2:
            header('Location: ../views/cadastro.php?erro');
            exit();
        case 3:
            header('Location: ../views/cadastro.php?ja_existe');
            exit();
        default:
            header('Location: ../views/cadastro.php?falha');
            exit();
    }
} else if(isset($_POST['id_candidato']) && !empty($_POST['id_candidato'])){
    $escola = $_SESSION['escola'];
    $admin_model = new admin($escola);
    
    $id_candidato = $_POST['id_candidato'];
    echo $result = $admin_model->excluir_candidato($id_candidato);
    switch ($result) {
        case 1:
            header('Location: ../views/candidatos.php?deletado');
            exit();
        case 2:
            header('Location: ../views/candidatos.php?erro');
            exit();
        case 3:
            header('Location: ../views/candidatos.php?nao_existe');
            exit();
        default:
            header('Location: ../views/candidatos.php?falha');
            exit();
    }

}else if(isset($_GET['id_candidato']) && !empty($_GET['id_candidato'])){
    $escola = $_SESSION['escola'];
    $admin_model = new select($escola);
    
    $id_candidato = $_GET['id_candidato'];
    $result = $admin_model->select_candidato_notas($id_candidato);
    switch ($result) {
        case 1:
            header('Location: ../views/candidatos.php?deletado');
            exit();
        case 2:
            header('Location: ../views/candidatos.php?erro');
            exit();
        case 3:
            header('Location: ../views/candidatos.php?nao_existe');
            exit();
        default:
            header('Location: ../views/candidatos.php?falha');
            exit();
    }

}/*else{
    header("location:../index.php");
    exit();
}*/
