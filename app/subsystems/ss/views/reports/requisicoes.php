<?php

require_once(__DIR__ . '/../../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../../config/connect.php');
require_once(__DIR__ . '/../../assets/libs/fpdf/fpdf.php');

$escola = $_SESSION['escola'];

// Classe FPDF customizada para suporte a UTF-8
class PDF extends FPDF
{
    function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '')
    {
        $txt = mb_convert_encoding($txt, 'ISO-8859-1', 'UTF-8');
        parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
    }

    function MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false)
    {
        $txt = mb_convert_encoding($txt, 'ISO-8859-1', 'UTF-8');
        parent::MultiCell($w, $h, $txt, $border, $align, $fill);
    }
}

class relatorios extends connect
{
    protected string $table5;
    protected string $table14;
    protected string $table1;

    function __construct($escola)
    {
        parent::__construct($escola);
        $table = require(__DIR__ . '/../../../../.env/tables.php');
        $this->table5 = $table["ss_$escola"][5]; // usuarios table
        $this->table14 = $table["ss_$escola"][14]; // requisicoes table
        $this->table1 = $table["ss_$escola"][1]; // candidatos table
    }

    public function relatorio_requisicoes()
    {

        // Fetch completed requests
        $sql_concluidas = "SELECT r.*, c.nome as nome_candidato 
                          FROM $this->table14 r 
                          INNER JOIN $this->table1 c ON r.id_candidato = c.id 
                          WHERE r.status = 'Concluido' 
                          ORDER BY r.id DESC";
        $stmtSelect_concluidas = $this->connect->query($sql_concluidas);
        $dados_concluidas = $stmtSelect_concluidas->fetchAll(PDO::FETCH_ASSOC);

        // Fetch pending requests
        $sql_pendentes = "SELECT r.*, c.nome as nome_candidato 
                         FROM $this->table14 r 
                         INNER JOIN $this->table1 c ON r.id_candidato = c.id 
                         WHERE r.status = 'Pendente' 
                         ORDER BY r.id DESC";
        $stmtSelect_pendentes = $this->connect->query($sql_pendentes);
        $dados_pendentes = $stmtSelect_pendentes->fetchAll(PDO::FETCH_ASSOC);

        // Fetch refused requests
        $sql_recusadas = "SELECT r.*, c.nome as nome_candidato 
                         FROM $this->table14 r 
                         INNER JOIN $this->table1 c ON r.id_candidato = c.id 
                         WHERE r.status = 'Recusado' 
                         ORDER BY r.id DESC";
        $stmtSelect_recusadas = $this->connect->query($sql_recusadas);
        $dados_recusadas = $stmtSelect_recusadas->fetchAll(PDO::FETCH_ASSOC);

        $pdf = new PDF('P', 'mm', 'A4');
        $pdf->AddPage();

        // Header
        $pdf->Image(__DIR__ . '/../../assets/imgs/logo.png', 8, 8, 15, 0, 'PNG');
        $pdf->SetFont('Arial', 'B', 25);
        $pdf->SetY(10);
        $pdf->SetX(55);
        $pdf->Cell(110, 8, 'RELATÓRIO DE REQUISIÇÕES', 0, 1, 'C');

        $y_position = 52;

        // COMPLETED REQUESTS SECTION
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetFillColor(93, 164, 67); // Green background
        $pdf->SetTextColor(255, 255, 255); // White text
        $pdf->SetY($y_position);
        $pdf->SetX(10);
        $pdf->Cell(190, 8, 'REQUISIÇÕES CONCLUÍDAS', 1, 1, 'C', true);
        $y_position += 8;

        // Table Header for Completed
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(93, 164, 67); // Green background
        $pdf->SetTextColor(255, 255, 255); // White text
        $pdf->SetY($y_position);
        $pdf->SetX(10);
        $pdf->Cell(40, 7, 'CANDIDATO', 1, 0, 'C', true);
        $pdf->Cell(120, 7, 'TEXTO DA REQUISIÇÃO', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'STATUS', 1, 1, 'C', true);
        $y_position += 7;

        // Reset text color to black
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);

        if (empty($dados_concluidas)) {
            $pdf->SetY($y_position);
            $pdf->SetX(10);
            $pdf->SetFillColor(93, 164, 67); // Green background for empty message
            $pdf->Cell(190, 7, 'NENHUMA REQUISIÇÃO CONCLUÍDA ENCONTRADA', 1, 1, 'C', true);
            $y_position += 7;
        } else {
            foreach ($dados_concluidas as $dado) {
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetY($y_position);
                $pdf->SetX(10);
                $pdf->Cell(40, 7, strtoupper($dado['nome_candidato']), 1, 0, 'L', true);
                
                // Truncate long text
                $texto = $dado['texto'];
                if (strlen($texto) > 60) {
                    $texto = substr($texto, 0, 57) . '...';
                }
                $pdf->Cell(120, 7, strtoupper($texto), 1, 0, 'L', true);
                $pdf->Cell(30, 7, strtoupper($dado['status']), 1, 1, 'C', true);
                $y_position += 7;

                // Add new page if needed
                if ($y_position > 270) {
                    $pdf->AddPage();
                    $y_position = 20;
                }
            }
        }

        $y_position += 10;

        // PENDING REQUESTS SECTION
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetFillColor(255, 193, 7); // Yellow background
        $pdf->SetTextColor(0, 0, 0); // Black text
        $pdf->SetY($y_position);
        $pdf->SetX(10);
        $pdf->Cell(190, 8, 'REQUISIÇÕES PENDENTES', 1, 1, 'C', true);
        $y_position += 8;

        // Table Header for Pending
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(255, 193, 7); // Yellow background
        $pdf->SetTextColor(0, 0, 0); // Black text
        $pdf->SetY($y_position);
        $pdf->SetX(10);
        $pdf->Cell(40, 7, 'CANDIDATO', 1, 0, 'C', true);
        $pdf->Cell(120, 7, 'TEXTO DA REQUISIÇÃO', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'STATUS', 1, 1, 'C', true);
        $y_position += 7;

        // Reset text color to black
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);

        if (empty($dados_pendentes)) {
            $pdf->SetY($y_position);
            $pdf->SetX(10);
            $pdf->SetFillColor(255, 193, 7); // Yellow background for empty message
            $pdf->Cell(190, 7, 'NENHUMA REQUISIÇÃO PENDENTE ENCONTRADA', 1, 1, 'C', true);
            $y_position += 7;
        } else {
            foreach ($dados_pendentes as $dado) {
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetY($y_position);
                $pdf->SetX(10);
                $pdf->Cell(40, 7, strtoupper($dado['nome_candidato']), 1, 0, 'L', true);
                
                // Truncate long text
                $texto = $dado['texto'];
                if (strlen($texto) > 60) {
                    $texto = substr($texto, 0, 57) . '...';
                }
                $pdf->Cell(120, 7, strtoupper($texto), 1, 0, 'L', true);
                $pdf->Cell(30, 7, strtoupper($dado['status']), 1, 1, 'C', true);
                $y_position += 7;

                // Add new page if needed
                if ($y_position > 270) {
                    $pdf->AddPage();
                    $y_position = 20;
                }
            }
        }

        $y_position += 10;

        // REFUSED REQUESTS SECTION
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetFillColor(220, 53, 69); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        $pdf->SetY($y_position);
        $pdf->SetX(10);
        $pdf->Cell(190, 8, 'REQUISIÇÕES RECUSADAS', 1, 1, 'C', true);
        $y_position += 8;

        // Table Header for Refused
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(220, 53, 69); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        $pdf->SetY($y_position);
        $pdf->SetX(10);
        $pdf->Cell(40, 7, 'CANDIDATO', 1, 0, 'C', true);
        $pdf->Cell(120, 7, 'TEXTO DA REQUISIÇÃO', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'STATUS', 1, 1, 'C', true);
        $y_position += 7;

        // Reset text color to black
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);

        if (empty($dados_recusadas)) {
            $pdf->SetY($y_position);
            $pdf->SetX(10);
            $pdf->SetFillColor(220, 53, 69); // Red background for empty message
            $pdf->Cell(190, 7, 'NENHUMA REQUISIÇÃO RECUSADA ENCONTRADA', 1, 1, 'C', true);
            $y_position += 7;
        } else {
            foreach ($dados_recusadas as $dado) {
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetY($y_position);
                $pdf->SetX(10);
                $pdf->Cell(40, 7, strtoupper($dado['nome_candidato']), 1, 0, 'L', true);
                
                // Truncate long text
                $texto = $dado['texto'];
                if (strlen($texto) > 60) {
                    $texto = substr($texto, 0, 57) . '...';
                }
                $pdf->Cell(120, 7, strtoupper($texto), 1, 0, 'L', true);
                $pdf->Cell(30, 7, strtoupper($dado['status']), 1, 1, 'C', true);
                $y_position += 7;

                // Add new page if needed
                if ($y_position > 270) {
                    $pdf->AddPage();
                    $y_position = 20;
                }
            }
        }

        $pdf->Output('relatorio_requisicoes.pdf', 'I');
    }
}

if (isset($_GET['usuarios'])) {
    $relatorio = new relatorios($escola);
    $relatorio->relatorio_requisicoes();
} else {
    header('location:../../index.php');
    exit();
}