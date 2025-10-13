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
    protected string $table16;

    function __construct($escola)
    {
        parent::__construct($escola);
        $table = require(__DIR__ . '/../../../../.env/tables.php');
        $this->table5 = $table["ss_$escola"][5]; // usuarios table
        $this->table16 = $table["ss_$escola"][16]; // movimentacoes table
    }

    public function comissao_selecao($id_usuario)
    {
        // Fetch user movements
        $sql_movimentacoes = "SELECT * FROM $this->table16 WHERE id_usuario = '$id_usuario' ORDER BY data DESC";
        $stmtSelect_movimentacoes = $this->connect->query($sql_movimentacoes);
        $dados_movimentacoes = $stmtSelect_movimentacoes->fetchAll(PDO::FETCH_ASSOC);

        // Fetch user info
        $sql_usuario = "SELECT * FROM $this->table5 WHERE id = '$id_usuario'";
        $stmtSelect_usuario = $this->connect->query($sql_usuario);
        $dados_usuario = $stmtSelect_usuario->fetch(PDO::FETCH_ASSOC);

        $pdf = new PDF('P', 'mm', 'A4');
        $pdf->AddPage();

        // Header
        $pdf->Image(__DIR__ . '/../../assets/imgs/logo.png', 8, 8, 15, 0, 'PNG');
        $pdf->SetFont('Arial', 'B', 25);
        $pdf->SetY(10);
        $pdf->SetX(55);
        $pdf->Cell(110, 8, 'RELATÓRIO DE MOVIMENTAÇÕES', 0, 1, 'C');

        // User info
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetY(38);
        $pdf->SetX(10);
        $pdf->Cell(100, 7, 'Usuário: ' . $dados_usuario['nome_user'], 0, 1, 'L');

        // Table Header
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(93, 164, 67); // Green background
        $pdf->SetTextColor(255, 255, 255); // White text
        $pdf->SetY(45);
        $pdf->SetX(10);
        $pdf->Cell(30, 7, 'Data', 1, 0, 'C', true);
        $pdf->Cell(60, 7, 'Tipo de Movimentação', 1, 0, 'C', true);
        $pdf->Cell(100, 7, 'Descrição', 1, 1, 'C', true);

        // Reset text color to black
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);

        // Initialize Y position
        $y_position = 52;

        // Movements Section
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetFillColor(255, 255, 255); // White background
        $pdf->SetY(50);
        $pdf->SetX(10);
        $y_position += 0.1;
        $pdf->SetFont('Arial', '', 8);

        if (empty($dados_movimentacoes)) {
            $pdf->SetY($y_position);
            $pdf->SetX(10);
            $pdf->SetFillColor(93, 164, 67); // Green background for empty message
            $pdf->Cell(190, 7, 'NENHUMA MOVIMENTAÇÃO ENCONTRADA', 1, 1, 'C', true);
            $y_position += 7;
        } else {
            foreach ($dados_movimentacoes as $dado) {
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetY($y_position);
                $pdf->SetX(10);
                $pdf->Cell(30, 7, $dado['data'], 1, 0, 'L', true);
                $pdf->Cell(60, 7, $dado['tipo_movimentacao'], 1, 0, 'L', true);

                // Tratar descrição nula ou vazia
                $descricao = $dado['descricao'] ?? 'NÃO INFORMADA';
                if (empty(trim($descricao))) {
                    $descricao = 'NÃO INFORMADA';
                }
                $pdf->Cell(100, 7, $descricao, 1, 1, 'L', true);
                $y_position += 7;

                // Add new page if needed
                if ($y_position > 270) {
                    $pdf->AddPage();
                    $y_position = 20;

                    // Add header to new page
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->SetFillColor(93, 164, 67);
                    $pdf->SetTextColor(255, 255, 255);
                    $pdf->SetY(10);
                    $pdf->SetX(5);
                    $pdf->Cell(30, 7, 'Data', 1, 0, 'C', true);
                    $pdf->Cell(60, 7, 'Tipo de Movimentação', 1, 0, 'C', true);
                    $pdf->Cell(100, 7, 'Descrição', 1, 1, 'C', true);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetFont('Arial', '', 8);
                    $y_position = 17;
                }
            }
        }

        $pdf->Output('relatorio_movimentacoes.pdf', 'I');
    }
}

if (isset($_GET['id_usuario']) && !empty($_GET['id_usuario'])) {
    $id_usuario = $_GET['id_usuario'];
    $relatorio = new relatorios($escola);
    $relatorio->comissao_selecao($id_usuario);
} else {
    header('location:../../index.php');
    exit();
}
