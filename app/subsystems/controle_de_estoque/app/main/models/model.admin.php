<?php 
require_once('sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__.'\..\config\connect.php');

?>