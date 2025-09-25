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

    protected string $table13;

    function __construct($escola)
    {
        parent::__construct($escola);
        $table = require(__DIR__ . '/../../../../.env/tables.php');
        $this->table1 = $table["ss_$escola"][5];
    }
    public function comissao_selecao()
    {
        // Construir a consulta SQL com base nos parâmetros
        $sql = "SELECT * FROM $this->table1";
        $stmtSelect = $this->connect->query($sql);
        $dados = $stmtSelect->fetchAll(PDO::FETCH_ASSOC);

        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();

        // Cabeçalho com larguras ajustadas
        $pdf->Image(__DIR__ . '/../../assets/imgs/logo.png', 8, 8, 15, 0, 'PNG');
        $pdf->SetFont('Arial', 'B', 25);
        $pdf->SetY(8);
        $pdf->SetX(20);
        $pdf->Cell(90, 8, utf8_decode('COMISSÃO DE SELEÇÃO'), 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetY(16);
        $pdf->SetX(11);
        $pdf->Cell(188, 6, ('PCD = PESSOA COM DEFICIENCIA | COTISTA = INCLUSO NA COTA DO BAIRRO | AC = AMPLA CONCORRENCIA'), 0, 1, 'C');
        $pdf->SetFont('Arial', 'b', 12);
        $pdf->Cell(185, 10, '', 0, 1, 'C');

        $stmt_bairros = $this->connect->query("SELECT * FROM $this->table13");
        $dados_bairros = $stmt_bairros->fetchAll(PDO::FETCH_ASSOC);
        $bairros_para_mostrar = array_slice($dados_bairros, 0, 5);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetY(16);
        $pdf->SetX(190);

        $pdf->SetFont('Arial', 'b', 12);
        $pdf->Cell(185, 10, '', 0, 1, 'C');
        // Fonte do cabeçalho
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(93, 164, 67); //fundo verde
        $pdf->SetTextColor(255, 255, 255);  //texto branco
        $pdf->Cell(10, 7, 'CH', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Nome', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Curso', 1, 0, 'C', true);
        $pdf->Cell(20, 7, 'Origem', 1, 0, 'C', true);
        if ((isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin')) {
            $pdf->Cell(24, 7, 'Segmento', 1, 0, 'C', true);
            $pdf->Cell(17, 7, 'Media', 1, 0, 'C', true);
            $pdf->Cell(60, 7, 'Resp. Cadastro', 1, 1, 'C', true);
        } else {
            $pdf->Cell(26, 7, 'Segmento', 1, 1, 'C', true);
        }
        // Resetar cor do texto para preto
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);

        // Dados com cores alternadas
        $classificacao = 001;

        foreach ($dados as $dado) {

            // Definir cor da linha
            $cor = $classificacao % 2 ? 255 : 192;
            $pdf->SetFillColor($cor, $cor, $cor);

            // Imprimir linha no PDF
            $pdf->Cell(10, 7, sprintf("%03d", $classificacao), 1, 0, 'C', true);
            $pdf->Cell(30, 7, strToUpper(utf8_decode($dado['nome'])), 1, 0, 'L', true);
            $pdf->Cell(30, 7, strToUpper(utf8_decode($dado['nome_curso'])), 1, 0, 'L', true);

            $classificacao++;
        }
        $pdf->Output('classificacao.pdf', 'I');
    }
}
if(isset($_GET['usuarios'])){

    $relatorio = new relatorios($escolas);
    $relatorio->comissao_selecao();
} else {
    header('location:../../index.php');
    exit();
}
