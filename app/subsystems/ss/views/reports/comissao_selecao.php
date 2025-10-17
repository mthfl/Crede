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
    protected string $table1;
    protected string $table15;

    function __construct($escola)
    {
        parent::__construct($escola);
        $table = require(__DIR__ . '/../../../../.env/tables.php');
        $this->table1 = $table["ss_$escola"][5]; // usuarios table
        $this->table15 = $table["ss_$escola"][15]; // perfis_usuarios table
    }

    public function comissao_selecao()
    {
        // Fetch active users (status = 1)
        $sql_ativo = "SELECT * FROM $this->table1 WHERE status = 1";
        $stmtSelect_ativo = $this->connect->query($sql_ativo);
        $dados_ativo = $stmtSelect_ativo->fetchAll(PDO::FETCH_ASSOC);

        // Fetch deactivated users (status = 0)
        $sql_desativado = "SELECT * FROM $this->table1 WHERE status = 0";
        $stmtSelect_desativado = $this->connect->query($sql_desativado);
        $dados_desativado = $stmtSelect_desativado->fetchAll(PDO::FETCH_ASSOC);

        $pdf = new PDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->Image('../../assets/imgs/fundo_pdf.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'png', '', 0.1);

        // Header
        $pdf->SetFont('Arial', 'B', 25);
        $pdf->SetY(10);
        $pdf->SetX(55);
        $pdf->Cell(110, 8, strtoupper('COMISSÃO DE SELEÇÃO'), 0, 1, 'C');

        // Table Header
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(0, 90, 36); // Green background
        $pdf->SetTextColor(255, 255, 255); // White text
        $pdf->SetY(50);
        $pdf->SetX(8);
        $pdf->Cell(75, 7, strtoupper('NOME'), 1, 0, 'C', true);
        $pdf->Cell(30, 7, strtoupper('PERFIL'), 1, 0, 'C', true);
        $pdf->Cell(25, 7, strtoupper('USUÁRIO'), 1, 0, 'C', true);
        $pdf->Cell(30, 7, strtoupper('DATA DE INÍCIO'), 1, 0, 'C', true);
        $pdf->Cell(30, 7, strtoupper('DATA DE FIM'), 1, 1, 'C', true);

        // Reset text color to black
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);

        // Initialize Y position
        $y_position = 50;

        // Active Users Section
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetFillColor(255, 255, 255); // White background
        $pdf->SetY(40);
        $pdf->SetX(8);
        $pdf->Cell(190, 7, strtoupper('USUÁRIOS ATIVOS'), 0, 1, 'C', true);
        $y_position += 7;
        $pdf->SetFont('Arial', '', 8);

        if (empty($dados_ativo)) {
            $pdf->SetY($y_position);
            $pdf->SetX(8);
            $pdf->SetFillColor(0, 90, 36); // Green background for empty message
            $pdf->Cell(200, 7, strtoupper('NENHUM USUÁRIO ATIVO ENCONTRADO'), 1, 1, 'C', true);
            $y_position += 7;
        } else {
            $valor = 1;
            foreach ($dados_ativo as $dado) {
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetY($y_position);
                $pdf->SetX(8);
                $pdf->Cell(75, 7, strtoupper($dado['nome_user']), 1, 0, 'L', true);
                $pdf->Cell(30, 7, strtoupper($dado['id_perfil'] != null ? $this->select_perfil($dado['id_perfil']) : 'SEM PERFIL'), 1, 0, 'L', true);
                $pdf->Cell(25, 7, strtoupper($dado['tipo_usuario']), 1, 0, 'L', true);
                $pdf->Cell(30, 7, strtoupper($dado['data_inicio']), 1, 0, 'L', true);
                $pdf->Cell(30, 7, strtoupper($dado['data_fim'] ?? 'NÃO SE APLICA'), 1, 1, 'L', true);
                $y_position += 7;
                $valor++;
            }
        }
        $y_position += 10;

        // Deactivated Users Section
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetFillColor(255, 255, 255); // White background
        $pdf->SetTextColor(0, 0, 0); // Black text
        $pdf->SetY($y_position);
        $pdf->SetX(8);
        $pdf->Cell(180, 7, strtoupper('USUÁRIOS DESATIVADOS'), 0, 1, 'C', true);
        $y_position += 10;

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetTextColor(255, 255, 255); // White text
        $pdf->SetFillColor(0, 90, 36); // Green background
        $pdf->SetY($y_position);
        $pdf->SetX(8);
        $pdf->Cell(75, 7, strtoupper('NOME'), 1, 0, 'C', true);
        $pdf->Cell(30, 7, strtoupper('PERFIL'), 1, 0, 'C', true);
        $pdf->Cell(25, 7, strtoupper('USUÁRIO'), 1, 0, 'C', true);
        $pdf->Cell(30, 7, strtoupper('DATA DE INÍCIO'), 1, 0, 'C', true);
        $pdf->Cell(30, 7, strtoupper('DATA DE FIM'), 1, 1, 'C', true);

        $y_position += 7;
        $pdf->SetTextColor(0, 0, 0); // Black text
        $pdf->SetFont('Arial', '', 8);
        if (empty($dados_desativado)) {
            $pdf->SetY($y_position);
            $pdf->SetX(8);
            $pdf->SetFillColor(0, 90, 36); // Green background for empty message
            $pdf->Cell(190, 7, strtoupper('NENHUM USUÁRIO DESATIVADO ENCONTRADO'), 1, 1, 'C', true);
            $y_position += 7;
        } else {
            $valor = 1;
            foreach ($dados_desativado as $dado) {
                $cor = $valor % 2 ? 255 : 192; // Alternate row colors
                $pdf->SetFillColor($cor, $cor, $cor);
                $pdf->SetY($y_position);
                $pdf->SetX(5);
                $pdf->Cell(75, 7, strtoupper($dado['nome_user']), 1, 0, 'L', true);
                $pdf->Cell(30, 7, strtoupper($dado['id_perfil'] != null ? $this->select_perfil($dado['id_perfil']) : 'SEM PERFIL'), 1, 0, 'L', true);
                $pdf->Cell(30, 7, strtoupper($dado['tipo_usuario']), 1, 0, 'L', true);
                $pdf->Cell(30, 7, strtoupper($dado['data_inicio']), 1, 0, 'L', true);
                $pdf->Cell(30, 7, strtoupper($dado['data_fim'] ?? 'SEM DATA'), 1, 1, 'L', true);
                $y_position += 7;
                $valor++;
            }
        }

        $pdf->Output('relatorio_usuarios.pdf', 'I');
    }

    private function select_perfil($id_perfil)
    {
        $sql = "SELECT nome_perfil FROM $this->table15 WHERE id = :id_perfil";
        $stmtSelect = $this->connect->prepare($sql);
        $stmtSelect->execute(['id_perfil' => $id_perfil]);
        $dados = $stmtSelect->fetch(PDO::FETCH_ASSOC);
        return $dados['nome_perfil'] ?? 'SEM PERFIL';
    }
}

if (isset($_GET['usuarios'])) {
    $relatorio = new relatorios($escola);
    $relatorio->comissao_selecao();
} else {
    header('location:../../index.php');
    exit();
}