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
        if (mb_detect_encoding($txt, 'UTF-8', true) === 'UTF-8') {
            $txt = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $txt);
        }
        parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
    }

    function MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false)
    {
        if (mb_detect_encoding($txt, 'UTF-8', true) === 'UTF-8') {
            $txt = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $txt);
        }
        parent::MultiCell($w, $h, $txt, $border, $align, $fill);
    }
}

class relatorios extends connect
{
    protected string $table1;
    protected string $table2;
    protected string $table5;
    protected string $table17;

    function __construct($escola)
    {
        parent::__construct($escola);
        $table = require(__DIR__ . '/../../../../.env/tables.php');
        $this->table1 = $table["ss_$escola"][1]; // candidatos table
        $this->table2 = $table["ss_$escola"][2]; // cursos table
        $this->table5 = $table["ss_$escola"][5]; // usuarios table
        $this->table17 = $table["ss_$escola"][17]; // exclusao_candidato table
    }

    public function relatorio_candidatos_desabilitados()
    {
        // Buscar candidatos desabilitados com informações relacionadas
        $sql = "SELECT 
                    ec.id,
                    ec.id_candidato,
                    ec.motivo,
                    ec.data as data_desabilitacao,
                    c.nome,
                    c.publica,
                    c.bairro,
                    c.pcd,
                    cur.nome_curso,
                    u.nome_user as responsavel
                FROM $this->table17 ec
                INNER JOIN $this->table1 c ON ec.id_candidato = c.id
                INNER JOIN $this->table2 cur ON c.id_curso1 = cur.id
                INNER JOIN $this->table5 u ON ec.id_desabilitador = u.id
                ORDER BY ec.data DESC";
        
        $stmtSelect = $this->connect->query($sql);
        $dados = $stmtSelect->fetchAll(PDO::FETCH_ASSOC);

        $pdf = new PDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->Image('../../assets/imgs/fundo5_pdf.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'png', '', 0.1);
        
        // Cabeçalho
        date_default_timezone_set('America/Fortaleza');
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->SetY(3);
        $pdf->SetX(46);
        $pdf->Cell(40, 4, $_SESSION['nome_escola'], 0, 0, 'C');
        $pdf->SetX(155);
        $pdf->Cell(40, 4, $datatime = date('Y/m/d H:i:s'), 0, 1, 'C');

        // Título do relatório
        $pdf->SetFont('Arial', 'B', 17);
        $pdf->SetY(10);
        $pdf->SetX(8);
        $nome_relatorio = 'RELATÓRIO DE CANDIDATOS DESABILITADOS';
        $count = mb_strlen($nome_relatorio);
        $pdf->Cell(55, 4, $nome_relatorio, 0, 1, 'L');
        $pdf->SetFillColor(255,165,0);
        $pdf->SetY(16);
        $pdf->SetX(9);
        $pdf->Cell(3.9*$count, 1.2, '', 0, 1, 'L', true);

        // Posição inicial para a tabela
        $y_position = 32;

        // Cabeçalho da tabela
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(0, 90, 36); // Verde (#005A24)
        $pdf->SetTextColor(255, 255, 255); // Texto branco
        $pdf->SetY($y_position);
        $pdf->SetX(10);
        $pdf->Cell(60, 7, 'NOME', 1, 0, 'C', true);
        $pdf->Cell(15, 7, 'SEG', 1, 0, 'C', true);
        $pdf->Cell(20, 7, 'ORIGEM', 1, 0, 'C', true);
        $pdf->Cell(50, 7, 'MOTIVO', 1, 0, 'C', true);
        $pdf->Cell(17, 7, 'DATA', 1, 0, 'C', true);
        $pdf->Cell(28, 7, 'RESP.', 1, 1, 'C', true);
        $y_position += 7;

        // Resetar cor do texto para preto
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);

        if (empty($dados)) {
            $pdf->SetY($y_position);
            $pdf->SetX(10);
            $pdf->SetFillColor(0, 90, 36); // Verde para mensagem vazia
            $pdf->SetTextColor(255, 255, 255); // Texto branco
            $pdf->Cell(190, 7, 'NENHUM CANDIDATO DESABILITADO ENCONTRADO', 1, 1, 'C', true);
            $y_position += 7;
        } else {
            $linha = 0;
            foreach ($dados as $dado) {
                // Determinar segmento
                if ($dado['pcd'] == 1) {
                    $segmento = 'PCD';
                } else if ($dado['bairro'] == 1) {
                    $segmento = 'COTAS';
                } else {
                    $segmento = 'AC';
                }

                // Determinar origem
                $origem = ($dado['publica'] == 1) ? 'PÚBLICA' : 'PRIVADA';

                // Preparar dados para exibição
                $nome = mb_strtoupper($dado['nome'], 'UTF-8');
                // Truncar nome se tiver mais de 30 caracteres
                if (mb_strlen($nome, 'UTF-8') > 35) {
                    $nome = mb_substr($nome, 0, 35, 'UTF-8') . '...';
                }

                $motivo = mb_strtoupper($dado['motivo'] ?? 'NÃO INFORMADO', 'UTF-8');
                // Truncar motivo se tiver mais de 30 caracteres
                if (mb_strlen($motivo, 'UTF-8') > 30) {
                    $motivo = mb_substr($motivo, 0, 30, 'UTF-8') . '...';
                }

                $data = mb_strtoupper($dado['data_desabilitacao'] ?? '', 'UTF-8');
                // Formatar data se necessário
                if (!empty($data) && strlen($data) > 10) {
                    $data = substr($data, 0, 10); // Pegar apenas a data (YYYY/MM/DD)
                }

                $responsavel = mb_strtoupper($dado['responsavel'] ?? 'NÃO INFORMADO', 'UTF-8');
                // Truncar responsável se tiver mais de 10 caracteres
                if (mb_strlen($responsavel, 'UTF-8') > 14) {
                    $responsavel = mb_substr($responsavel, 0, 14, 'UTF-8') . '...';
                }

                // Cor alternada para as linhas
                $cor = ($linha % 2 == 0) ? 255 : 240;
                $pdf->SetFillColor($cor, $cor, $cor);

                // Verificar se precisa de nova página
                if ($y_position > 270) {
                    $pdf->AddPage();
                    $pdf->Image('../../assets/imgs/fundo5_pdf.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'png', '', 0.1);
                    $y_position = 20;

                    // Adicionar cabeçalho na nova página
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->SetFillColor(0, 90, 36);
                    $pdf->SetTextColor(255, 255, 255);
                    $pdf->SetY($y_position);
                    $pdf->SetX(10);
                    $pdf->Cell(60, 7, 'NOME', 1, 0, 'C', true);
                    $pdf->Cell(15, 7, 'SEG', 1, 0, 'C', true);
                    $pdf->Cell(20, 7, 'ORIGEM', 1, 0, 'C', true);
                    $pdf->Cell(50, 7, 'MOTIVO', 1, 0, 'C', true);
                    $pdf->Cell(17, 7, 'DATA', 1, 0, 'C', true);
                    $pdf->Cell(28, 7, 'RESP.', 1, 1, 'C', true);
                    $y_position = 27;
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetFont('Arial', '', 8);
                }

                // Imprimir linha
                $pdf->SetY($y_position);
                $pdf->SetX(10);
                $pdf->Cell(60, 7, $nome, 1, 0, 'L', true);
                $pdf->Cell(15, 7, $segmento, 1, 0, 'C', true);
                $pdf->Cell(20, 7, $origem, 1, 0, 'C', true);
                $pdf->Cell(50, 7, $motivo, 1, 0, 'L', true);
                $pdf->Cell(17, 7, $data, 1, 0, 'C', true);
                $pdf->Cell(28, 7, $responsavel, 1, 1, 'L', true);
                
                $y_position += 7;
                $linha++;
            }
        }

        $pdf->Output('relatorio_candidatos_desabilitados.pdf', 'I');
    }
}

if (isset($_GET['usuarios'])) {
    $relatorio = new relatorios($escola);
    $relatorio->relatorio_candidatos_desabilitados();
} else {
    header('location:../../index.php');
    exit();
}
?>

