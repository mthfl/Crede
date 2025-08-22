<?php
require_once(__DIR__ . '/models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

if (isset($_SESSION['Comum_estoque'])) {

    header("location:views/estoque.php");
    exit();
} else {

    header("location:views/index.php");
    exit();
}
