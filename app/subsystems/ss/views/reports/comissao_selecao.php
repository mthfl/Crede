<?php

require_once(__DIR__ . '/../../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../../config/connect.php');
$escola = $_SESSION['escola'];

new connect($escola);
require_once(__DIR__ . '/../../assets/libs/fpdf/fpdf.php');

class relatorios extends connect
{
    protected string $table1;
    protected string $table15;
    function __construct($escola)
    {
        parent::__construct($escola);
        $table = require(__DIR__ . '/../../../../.env/tables.php');
        $this->table1 = $table["ss_$escola"][5];
        $this->table15 = $table["ss_$escola"][15];
    }
    public function comissao_selecao()
    {
        // Construir a consulta SQL com base nos parâmetros
        $sql = "SELECT * FROM $this->table1";

        $stmtSelect = $this->connect->query($sql);
        $dados = $stmtSelect->fetchAll(PDO::FETCH_ASSOC);

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        // Cabeçalho com larguras ajustadas
        $pdf->Image(__DIR__ . '/../../assets/imgs/logo.png', 8, 8, 15, 0, 'PNG');
        $pdf->SetFont('Arial', 'B', 25);
        $pdf->SetY(10);
        $pdf->SetX(55);
        $pdf->Cell(110, 8, mb_convert_encoding('COMISSÃO DE SELEÇÃO', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetY(16);
        $pdf->SetX(55);
        $pdf->SetFont('Arial', 'b', 12);
        $pdf->Cell(185, 10, '', 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetY(16);
        $pdf->SetX(55);

        $pdf->SetFont('Arial', 'b', 12);
        $pdf->Cell(185, 10, '', 0, 1, 'C');
        // Fonte do cabeçalho
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(93, 164, 67); //fundo verde
        $pdf->SetTextColor(255, 255, 255);  //texto branco
        $pdf->SetY(40);
        $pdf->SetX(20);
        $pdf->Cell(60, 7, 'Nome', 1, 0, 'C', true);
        $pdf->Cell(60, 7, 'Perfil', 1, 0, 'C', true);
        $pdf->Cell(60, 7, 'Tipo', 1, 1, 'C', true);

        // Resetar cor do texto para preto
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);

        // Dados com cores alternadas
        $valor = 001;

        foreach ($dados as $dado) {

            // Definir cor da linha
            $cor = $valor % 2 ? 255 : 192;
            $pdf->SetFillColor($cor, $cor, $cor);

            // Imprimir linha no PDF
            $pdf->SetY(47);
            $pdf->SetX(20);
            $pdf->Cell(60, 7, strToUpper($dado['nome_user']), 1, 0, 'L', true);
            $pdf->Cell(60, 7, $dado['id_perfil'] != null ? $this->select_perfil($dado['id_perfil']) : 'Sem perfil', 1, 0, 'L', true);
            $pdf->Cell(60, 7, strToUpper($dado['tipo_usuario']), 1, 1, 'L', true);

            $valor++;
        }
        $pdf->Output('classificacao.pdf', 'I');
    }
    private function select_perfil($id_perfil)
    {
        $sql = "SELECT * FROM $this->table15 WHERE id = '$id_perfil'";
        $stmtSelect = $this->connect->query($sql);
        $dados = $stmtSelect->fetch(PDO::FETCH_ASSOC);
        return $dados['nome_perfil'];
    }
}
if (isset($_GET['usuarios'])) {

    $relatorio = new relatorios($escola);
    $relatorio->comissao_selecao();
} else {
    header('location:../../index.php');
    exit();
}
