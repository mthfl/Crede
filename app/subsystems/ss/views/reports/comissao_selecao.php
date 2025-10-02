
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
        $pdf->Cell(110, 8, mb_convert_encoding('COMISSÃO DE SELEÇÃO', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetY(20);
        $pdf->SetX(55);
        $pdf->Cell(110, 8, mb_convert_encoding('Relatório de Usuários', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // Table Header
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(93, 164, 67); // Green background
        $pdf->SetTextColor(255, 255, 255); // White text
        $pdf->SetY(40);
        $pdf->SetX(5);
        $pdf->Cell(80, 7, mb_convert_encoding('Nome', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
        $pdf->Cell(30, 7, mb_convert_encoding('Perfil', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
        $pdf->Cell(30, 7, mb_convert_encoding('Tipo', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
        $pdf->Cell(30, 7, mb_convert_encoding('Data Início', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
        $pdf->Cell(30, 7, mb_convert_encoding('Data Fim', 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', true);

        // Reset text color to black
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);

        // Initialize Y position
        $y_position = 47;
        $pdf->SetY($y_position);

        // Active Users Section
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetX(5);
        $pdf->Cell(200, 7, mb_convert_encoding('Usuários Ativos', 'ISO-8859-1', 'UTF-8'), 1, 1, 'L', true);
        $y_position += 7;

        if (empty($dados_ativo)) {
            $pdf->SetY($y_position);
            $pdf->SetX(5);
            $pdf->Cell(200, 7, mb_convert_encoding('Nenhum usuário ativo encontrado', 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', true);
            $y_position += 7;
        } else {
            $valor = 1;
            foreach ($dados_ativo as $dado) {
                $cor = $valor % 2 ? 255 : 192; // Alternate row colors
                $pdf->SetFillColor($cor, $cor, $cor);
                $pdf->SetY($y_position);
                $pdf->SetX(5);
                $pdf->Cell(80, 7, mb_convert_encoding(strtoupper($dado['nome_user']), 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
                $pdf->Cell(30, 7, mb_convert_encoding(strtoupper($dado['id_perfil'] != null ? $this->select_perfil($dado['id_perfil']) : 'Sem perfil'), 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
                $pdf->Cell(30, 7, mb_convert_encoding(strtoupper($dado['tipo_usuario']), 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
                $pdf->Cell(30, 7, mb_convert_encoding(strtoupper($dado['data_inicio']), 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
                $pdf->Cell(30, 7, mb_convert_encoding(strtoupper($dado['data_fim'] ?? 'Sem data'), 'ISO-8859-1', 'UTF-8'), 1, 1, 'L', true);
                $y_position += 7;
                $valor++;
            }
        }

        // Deactivated Users Section
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetY($y_position);
        $pdf->SetX(5);
        $pdf->Cell(200, 7, mb_convert_encoding('Usuários Desativados', 'ISO-8859-1', 'UTF-8'), 1, 1, 'L', true);
        $y_position += 7;

        if (empty($dados_desativado)) {
            $pdf->SetY($y_position);
            $pdf->SetX(5);
            $pdf->Cell(200, 7, mb_convert_encoding('Nenhum usuário desativado encontrado', 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', true);
        } else {
            $valor = 1;
            foreach ($dados_desativado as $dado) {
                $cor = $valor % 2 ? 255 : 192; // Alternate row colors
                $pdf->SetFillColor($cor, $cor, $cor);
                $pdf->SetY($y_position);
                $pdf->SetX(5);
                $pdf->Cell(80, 7, mb_convert_encoding(strtoupper($dado['nome_user']), 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
                $pdf->Cell(30, 7, mb_convert_encoding(strtoupper($dado['id_perfil'] != null ? $this->select_perfil($dado['id_perfil']) : 'Sem perfil'), 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
                $pdf->Cell(30, 7, mb_convert_encoding(strtoupper($dado['tipo_usuario']), 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
                $pdf->Cell(30, 7, mb_convert_encoding(strtoupper($dado['data_inicio']), 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
                $pdf->Cell(30, 7, mb_convert_encoding(strtoupper($dado['data_fim'] ?? 'Sem data'), 'ISO-8859-1', 'UTF-8'), 1, 1, 'L', true);
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
        return $dados['nome_perfil'] ?? 'Sem perfil';
    }
}

if (isset($_GET['usuarios'])) {
    $relatorio = new relatorios($escola);
    $relatorio->comissao_selecao();
} else {
    header('location:../../index.php');
    exit();
}
?>
