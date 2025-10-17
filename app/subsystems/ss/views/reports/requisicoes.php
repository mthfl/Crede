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

    // Method to estimate number of lines for MultiCell
    function NbLines($width, $text)
    {
        $texto = mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
        $cw = &$this->CurrentFont['cw'];
        if ($width == 0) {
            $width = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($width - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $texto);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += isset($cw[$c]) ? $cw[$c] : 0; // Check if character exists in font
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
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
        $sql_concluidas = "SELECT r.*, c.nome as nome_candidato, u.nome_user AS nome_usuario 
                          FROM $this->table14 r 
                          INNER JOIN $this->table1 c ON r.id_candidato = c.id
                          INNER JOIN $this->table5 u ON r.id_usuario = u.id 
                          WHERE r.status = 'Concluido' 
                          ORDER BY r.id DESC";
        $stmtSelect_concluidas = $this->connect->query($sql_concluidas);
        $dados_concluidas = $stmtSelect_concluidas->fetchAll(PDO::FETCH_ASSOC);

        // Fetch pending requests
        $sql_pendentes = "SELECT r.*, c.nome as nome_candidato, u.nome_user AS nome_usuario  
                         FROM $this->table14 r 
                         INNER JOIN $this->table1 c ON r.id_candidato = c.id 
                         INNER JOIN $this->table5 u ON r.id_usuario = u.id 
                         WHERE r.status = 'Pendente' 
                         ORDER BY r.id DESC";
        $stmtSelect_pendentes = $this->connect->query($sql_pendentes);
        $dados_pendentes = $stmtSelect_pendentes->fetchAll(PDO::FETCH_ASSOC);

        // Fetch refused requests
        $sql_recusadas = "SELECT r.*, c.nome as nome_candidato, u.nome_user AS nome_usuario 
                         FROM $this->table14 r 
                         INNER JOIN $this->table1 c ON r.id_candidato = c.id
                         INNER JOIN $this->table5 u ON r.id_usuario = u.id  
                         WHERE r.status = 'Recusado' 
                         ORDER BY r.id DESC";
        $stmtSelect_recusadas = $this->connect->query($sql_recusadas);
        $dados_recusadas = $stmtSelect_recusadas->fetchAll(PDO::FETCH_ASSOC);

        $pdf = new PDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->Image('../../assets/imgs/fundo_pdf.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'png', '', 0.1);
        // Header
        
        $pdf->SetFont('Arial', 'B', 25);
        $pdf->SetY(10);
        $pdf->SetX(55);
        $pdf->Cell(110, 8, 'RELATÓRIO DE REQUISIÇÕES', 0, 1, 'C');

        $y_position = 32;

        // COMPLETED REQUESTS SECTION
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetFillColor(0, 90, 36); // Green background
        $pdf->SetTextColor(255, 255, 255); // White text
        $pdf->SetY($y_position);
        $pdf->SetX(10);
        $pdf->Cell(190, 8, 'REQUISIÇÕES CONCLUÍDAS', 1, 1, 'C', true);
        $y_position += 8;

        // Table Header for Completed
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(0, 90, 36); // Green background
        $pdf->SetTextColor(255, 255, 255); // White text
        $pdf->SetY($y_position);
        $pdf->SetX(10);
        $pdf->Cell(40, 7, 'CANDIDATO', 1, 0, 'C', true);
        $pdf->Cell(85, 7, 'TEXTO DA REQUISIÇÃO', 1, 0, 'C', true);
        $pdf->Cell(35, 7, 'USUÁRIO', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'DATA', 1, 1, 'C', true);
        $y_position += 7;

        // Reset text color to black
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);

        if (empty($dados_concluidas)) {
            $pdf->SetY($y_position);
            $pdf->SetX(10);
            $pdf->SetFillColor(0, 90, 36); // Green background for empty message
            $pdf->SetTextColor(255, 255, 255); // White text
            $pdf->Cell(190, 7, 'NENHUMA REQUISIÇÃO CONCLUÍDA ENCONTRADA', 1, 1, 'C', true);
            $y_position += 7;
        } else {
            foreach ($dados_concluidas as $dado) {
                // Limit candidate and user names to 30 characters
                $nome_candidato = strlen($dado['nome_candidato']) > 20 ? substr($dado['nome_candidato'], 0, 20) . '...' : $dado['nome_candidato'];
                $nome_usuario = strlen($dado['nome_usuario']) > 17 ? substr($dado['nome_usuario'], 0, 17) . '...' : $dado['nome_usuario'];
                
                // Calculate height for MultiCell based on text length
                $texto = strtoupper($dado['texto']);
                $num_lines = $pdf->NbLines(90, $texto);
                $cell_height = 7 * max(1, $num_lines);

                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetY($y_position);
                $pdf->SetX(10);
                
                // Draw cells with dynamic height
                $pdf->Cell(35, $cell_height, strtoupper($nome_candidato), 1, 0, 'L', true);
                $pdf->MultiCell(85, 7, $texto, 1, 'L', true);
                $pdf->SetXY(135, $y_position);
                $pdf->Cell(35, $cell_height, strtoupper($nome_usuario), 1, 0, 'L', true);
                $pdf->Cell(30, $cell_height, strtoupper($dado['data']), 1, 1, 'C', true);
                $y_position += $cell_height;

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
        $pdf->SetFillColor(255, 165, 0); // Yellow background
        $pdf->SetTextColor(0, 0, 0); // Black text
        $pdf->SetY($y_position);
        $pdf->SetX(10);
        $pdf->Cell(190, 8, 'REQUISIÇÕES PENDENTES', 1, 1, 'C', true);
        $y_position += 8;

        // Table Header for Pending
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(255, 165, 0); // Yellow background
        $pdf->SetTextColor(0, 0, 0); // Black text
        $pdf->SetY($y_position);
        $pdf->SetX(10);
        $pdf->Cell(40, 7, 'CANDIDATO', 1, 0, 'C', true);
        $pdf->Cell(85, 7, 'TEXTO DA REQUISIÇÃO', 1, 0, 'C', true);
        $pdf->Cell(35, 7, 'USUÁRIO', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'DATA', 1, 1, 'C', true);
        $y_position += 7;

        // Reset text color to black
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);

        if (empty($dados_pendentes)) {
            $pdf->SetY($y_position);
            $pdf->SetX(10);
            $pdf->SetFillColor(255, 165, 0); // Yellow background for empty message
            $pdf->Cell(190, 7, 'NENHUMA REQUISIÇÃO PENDENTE ENCONTRADA', 1, 1, 'C', true);
            $y_position += 7;
        } else {
            foreach ($dados_pendentes as $dado) {
                // Limit candidate and user names to 30 characters
                $nome_candidato = strlen($dado['nome_candidato']) > 20 ? substr($dado['nome_candidato'], 0, 20) . '...' : $dado['nome_candidato'];
                $nome_usuario = strlen($dado['nome_usuario']) > 17 ? substr($dado['nome_usuario'], 0, 17) . '...' : $dado['nome_usuario'];
                
                // Calculate height for MultiCell based on text length
                $texto = strtoupper($dado['texto']);
                $num_lines = $pdf->NbLines(90, $texto);
                $cell_height = 7 * max(1, $num_lines);

                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetY($y_position);
                $pdf->SetX(10);
                
                // Draw cells with dynamic height
                $pdf->Cell(40, $cell_height, strtoupper($nome_candidato), 1, 0, 'L', true);
                $pdf->MultiCell(85, 7, $texto, 1, 'L', true);
                $pdf->SetXY(135, $y_position);
                $pdf->Cell(35, $cell_height, strtoupper($nome_usuario), 1, 0, 'L', true);
                $pdf->Cell(30, $cell_height, strtoupper($dado['data']), 1, 1, 'C', true);
                $y_position += $cell_height;

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
        $pdf->Cell(85, 7, 'TEXTO DA REQUISIÇÃO', 1, 0, 'C', true);
        $pdf->Cell(35, 7, 'USUÁRIO', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'DATA', 1, 1, 'C', true);
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
                // Limit candidate and user names to 30 characters
                $nome_candidato = strlen($dado['nome_candidato']) > 20 ? substr($dado['nome_candidato'], 0, 20) . '...' : $dado['nome_candidato'];
                $nome_usuario = strlen($dado['nome_usuario']) > 17 ? substr($dado['nome_usuario'], 0, 17) . '...' : $dado['nome_usuario'];
                
                // Calculate height for MultiCell based on text length
                $texto = strtoupper($dado['texto']);
                $num_lines = $pdf->NbLines(90, $texto);
                $cell_height = 7 * max(1, $num_lines);

                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetY($y_position);
                $pdf->SetX(10);
                
                // Draw cells with dynamic height
                $pdf->Cell(35, $cell_height, strtoupper($nome_candidato), 1, 0, 'L', true);
                $pdf->MultiCell(85, 7, $texto, 1, 'L', true);
                $pdf->SetXY(105, $y_position);
                $pdf->Cell(35, $cell_height, strtoupper($nome_usuario), 1, 0, 'L', true);
                $pdf->Cell(30, $cell_height, strtoupper($dado['data']), 1, 1, 'C', true);
                $y_position += $cell_height;

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
?>