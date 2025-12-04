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
    public $data_hora_footer = '';

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

    function Footer()
    {
        // Adicionar data/hora apenas na primeira página
        if ($this->PageNo() == 1 && !empty($this->data_hora_footer)) {
            $this->SetY(-12);
            $this->SetX(10);
            $this->SetFont('Arial', 'B', 9);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(0, 5, mb_convert_encoding('Gerado em: ' . $this->data_hora_footer, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
        }
    }
}

class relatorios extends connect
{
    protected string $table1;
    protected string $table15;
    protected string $escola;

    function __construct($escola)
    {
        parent::__construct($escola);
        $this->escola = $escola;
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

        // Buscar logo da escola
        $logo_escola = null;
        $stmt_logo = $this->connect_users->prepare("SELECT foto_perfil FROM escolas WHERE escola_banco = :escola_banco LIMIT 1");
        $stmt_logo->bindValue(':escola_banco', $this->escola);
        $stmt_logo->execute();
        $dados_logo = $stmt_logo->fetch();
        if ($dados_logo && !empty($dados_logo['foto_perfil'])) {
            $logo_path = __DIR__ . '/../../assets/fotos_escola/' . $dados_logo['foto_perfil'];
            if (file_exists($logo_path)) {
                $logo_escola = $logo_path;
            }
        }

        // Data e hora para exibir no rodapé
        date_default_timezone_set('America/Fortaleza');
        $data_hora_pdf = date('d/m/Y H:i:s');

        $pdf = new PDF('P', 'mm', 'A4');
        $pdf->data_hora_footer = $data_hora_pdf;
        $pdf->AddPage();
        $pdf->Image('../../assets/imgs/fundo5_pdf.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'png', '', 0.1);

        $pdf->SetFont('Arial', 'B', 13);
        $pdf->SetY(3);
        $pdf->SetX(46);
        $pdf->Cell(40, 4, $_SESSION['nome_escola'], 0, 0, 'C');
        
        // Logo da escola no lugar da data/hora (adicionar por último para ficar por cima)
        if ($logo_escola && file_exists($logo_escola)) {
            $pdf->Image($logo_escola, 170, 3, 22);
        }

        $pdf->SetFont('Arial', 'B', 17);
        $pdf->SetY(10);
        $pdf->SetX(8);
        $nome_relatorio = 'COMISSÃO DE SELEÇÃO';
        $count = mb_strlen($nome_relatorio);
        $pdf->Cell(55, 4, $nome_relatorio, 0, 1, 'L');
        $pdf->SetFillColor(255, 165, 0);
        $pdf->SetY(16);
        $pdf->SetX(9);
        $pdf->Cell(3.9 * $count, 1.2, '', 0, 1, 'L', true);

        // Table Header
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(0, 90, 36); // Green background
        $pdf->SetTextColor(255, 255, 255); // White text
        $pdf->SetY(40);
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
        $y_position = 40;

        // Active Users Section
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetFillColor(255, 255, 255); // White background
        $pdf->SetY(30);
        $pdf->SetX(8);
        $pdf->Cell(190, 7, strtoupper('USUÁRIOS ATIVOS'), 0, 1, 'C', true);
        $y_position += 7;
        $pdf->SetFont('Arial', '', 8);

        if (!empty($dados_ativo)) {

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
        if (!empty($dados_desativado)) {

            $valor = 1;
            foreach ($dados_desativado as $dado) {
                $cor = $valor % 2 ? 255 : 192; // Alternate row colors
                $pdf->SetFillColor($cor, $cor, $cor);
                $pdf->SetY($y_position);
                $pdf->SetX(8);
                $pdf->Cell(75, 7, strtoupper($dado['nome_user']), 1, 0, 'L', true);
                $pdf->Cell(30, 7, strtoupper($dado['id_perfil'] != null ? $this->select_perfil($dado['id_perfil']) : 'SEM PERFIL'), 1, 0, 'L', true);
                $pdf->Cell(25, 7, strtoupper($dado['tipo_usuario']), 1, 0, 'L', true);
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
