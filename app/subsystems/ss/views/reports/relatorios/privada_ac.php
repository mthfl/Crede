<?php
require_once(__DIR__ . '/../../../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();
require_once(__DIR__ . '/../../../config/connect.php');
$escola = $_SESSION['escola'];

new connect($escola);
require_once(__DIR__.'/../../../assets/libs/fpdf/fpdf.php');
require_once(__DIR__.'/../../../models/model.select.php');

class PDF extends select
{
    function __construct($escola)
    {
        parent::__construct($escola);
        $this->main();
    }
    public function main(){

        $pdf = new PDF("L", "pt", "A4");
        $dados = $this->connect->query("SELECT * FROM $this");
    }
    
}
//new PDF($escola);
