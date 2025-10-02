<?php

require_once(__DIR__ . '/../../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../../config/connect.php');
require_once(__DIR__ . '/../../assets/libs/fpdf/fpdf.php');

$escola = $_SESSION['escola'];

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

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        // Header
        $pdf->Image(__DIR__ . '/../../assets/imgs/logo.png', 8, 8, 15, 0, 'PNG');
        $pdf->SetFont('Arial', 'B', 25);
        $pdf->SetY(10);
        $pdf->SetX(55);
        $pdf->Cell(110, 8, utf8_decode(strtoupper('COMISSÃO DE SELEÇÃO')), 0, 1, 'C');

        // Table Header
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(93, 164, 67); // Green background
        $pdf->SetTextColor(255, 255, 255); // White text
        $pdf->SetY(50);
        $pdf->SetX(5);
        $pdf->Cell(80, 7, utf8_decode(strtoupper('NOME')), 1, 0, 'C', true);
        $pdf->Cell(30, 7, utf8_decode(strtoupper('PERFIL')), 1, 0, 'C', true);
        $pdf->Cell(30, 7, utf8_decode(strtoupper('USUÁRIO')), 1, 0, 'C', true);
        $pdf->Cell(30, 7, utf8_decode(strtoupper('DATA DE INÍCIO')), 1, 0, 'C', true);
        $pdf->Cell(30, 7, utf8_decode(strtoupper('DATA DE FIM')), 1, 1, 'C', true);

        // Reset text color to black
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);

        // Initialize Y position
        $y_position = 50;

        // Active Users Section
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetFillColor(255, 255, 255); // White background
        $pdf->SetY(40);
        $pdf->SetX(5);
        $pdf->Cell(200, 7, utf8_decode(strtoupper('USUÁRIOS ATIVOS')), 0, 1, 'C', true);
        $y_position += 7;
        $pdf->SetFont('Arial', '', 8);

        if (empty($dados_ativo)) {
            $pdf->SetY($y_position);
            $pdf->SetX(5);
            $pdf->SetFillColor(93, 164, 67); // Green background for empty message
            $pdf->Cell(200, 7, utf8_decode(strtoupper('NENHUM USUÁRIO ATIVO ENCONTRADO')), 1, 1, 'C', true);
            $y_position += 7;
        } else {
            $valor = 1;
            foreach ($dados_ativo as $dado) {
                $pdf->SetFillColor(255,255,255);
                $pdf->SetY($y_position);
                $pdf->SetX(5);
                $pdf->Cell(80, 7, utf8_decode(strtoupper($dado['nome_user'])), 1, 0, 'L', true);
                $pdf->Cell(30, 7, utf8_decode(strtoupper($dado['id_perfil'] != null ? $this->select_perfil($dado['id_perfil']) : 'SEM PERFIL')), 1, 0, 'L', true);
                $pdf->Cell(30, 7, utf8_decode(strtoupper($dado['tipo_usuario'])), 1, 0, 'L', true);
                $pdf->Cell(30, 7, utf8_decode(strtoupper($dado['data_inicio'])), 1, 0, 'L', true);
                $pdf->Cell(30, 7, utf8_decode(strtoupper($dado['data_fim'] ?? 'NÃO SE APLICA')), 1, 1, 'L', true);
                $y_position += 7;
                $valor++;
            }
        }
        $y_position += 10;

        // Deactivated Users Section
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetFillColor(255, 255, 255); // White background
        $pdf->SetTextColor(0, 0, 0); // White text
        $pdf->SetY($y_position);
        $pdf->SetX(5);
        $pdf->Cell(200, 7, utf8_decode(strtoupper('USUÁRIOS DESATIVADOS')), 0, 1, 'C', true);
        $y_position += 10;


        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetTextColor(255, 255, 255); // White text
        $pdf->SetFillColor(192,192,192); // Green background
        $pdf->SetY($y_position);
        $pdf->SetX(5);
        $pdf->Cell(80, 7, utf8_decode(strtoupper('NOME')), 1, 0, 'C', true);
        $pdf->Cell(30, 7, utf8_decode(strtoupper('PERFIL')), 1, 0, 'C', true);
        $pdf->Cell(30, 7, utf8_decode(strtoupper('USUÁRIO')), 1, 0, 'C', true);
        $pdf->Cell(30, 7, utf8_decode(strtoupper('DATA DE INÍCIO')), 1, 0, 'C', true);
        $pdf->Cell(30, 7, utf8_decode(strtoupper('DATA DE FIM')), 1, 1, 'C', true);

        $y_position += 7;
        $pdf->SetTextColor(0, 0, 0); // White text
        $pdf->SetFont('Arial', '', 8);
        if (empty($dados_desativado)) {
            $pdf->SetY($y_position);
            $pdf->SetX(5);
            $pdf->SetFillColor(93, 164, 67); // Green background for empty message
            $pdf->Cell(200, 7, utf8_decode(strtoupper('NENHUM USUÁRIO DESATIVADO ENCONTRADO')), 1, 1, 'C', true);
            $y_position += 7;
        } else {
            $valor = 1;
            foreach ($dados_desativado as $dado) {
                $cor = $valor % 2 ? 255 : 192; // Alternate row colors
                $pdf->SetFillColor($cor, $cor, $cor);
                $pdf->SetY($y_position);
                $pdf->SetX(5);
                $pdf->Cell(80, 7, utf8_decode(strtoupper($dado['nome_user'])), 1, 0, 'L', true);
                $pdf->Cell(30, 7, utf8_decode(strtoupper($dado['id_perfil'] != null ? $this->select_perfil($dado['id_perfil']) : 'SEM PERFIL')), 1, 0, 'L', true);
                $pdf->Cell(30, 7, utf8_decode(strtoupper($dado['tipo_usuario'])), 1, 0, 'L', true);
                $pdf->Cell(30, 7, utf8_decode(strtoupper($dado['data_inicio'])), 1, 0, 'L', true);
                $pdf->Cell(30, 7, utf8_decode(strtoupper($dado['data_fim'] ?? 'SEM DATA')), 1, 1, 'L', true);
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
