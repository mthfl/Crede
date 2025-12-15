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

    function Footer()
    {
        // Adicionar data/hora apenas na primeira página
        if ($this->PageNo() == 1 && !empty($this->data_hora_footer)) {
            $this->SetY(-12);
            $this->SetX(10);
            $this->SetFont('Arial', 'B', 9);
            $this->SetTextColor(0, 0, 0);
            $texto = 'Gerado em: ' . $this->data_hora_footer;
            if (mb_detect_encoding($texto, 'UTF-8', true) === 'UTF-8') {
                $texto = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $texto);
            }
            $this->Cell(0, 5, $texto, 0, 0, 'L');
        }
    }
}

class relatorios extends connect
{
    protected string $table5;
    protected string $table18;
    protected string $table1;
    protected string $escola;

    function __construct($escola)
    {
        parent::__construct($escola);
        $this->escola = $escola;
        $table = require(__DIR__ . '/../../../../.env/tables.php');
        $this->table5 = $table["ss_$escola"][5]; // usuarios table
        $this->table18 = $table["ss_$escola"][18]; // recursos table
        $this->table1 = $table["ss_$escola"][1]; // candidatos table
    }

    public function relatorio_recursos()
    {
        // Fetch deferred resources
        $sql_deferidos = "SELECT r.*, c.nome as nome_candidato, u.nome_user AS nome_usuario 
                          FROM $this->table18 r 
                          INNER JOIN $this->table1 c ON r.id_candidato = c.id
                          INNER JOIN $this->table5 u ON r.id_usuario = u.id 
                          WHERE r.status = 'DEFERIDO' 
                          ORDER BY r.id DESC";
        $stmtSelect_deferidos = $this->connect->query($sql_deferidos);
        $dados_deferidos = $stmtSelect_deferidos->fetchAll(PDO::FETCH_ASSOC);

        // Fetch pending resources
        $sql_pendentes = "SELECT r.*, c.nome as nome_candidato, u.nome_user AS nome_usuario  
                         FROM $this->table18 r 
                         INNER JOIN $this->table1 c ON r.id_candidato = c.id 
                         INNER JOIN $this->table5 u ON r.id_usuario = u.id 
                         WHERE r.status = 'PEDENTE' 
                         ORDER BY r.id DESC";
        $stmtSelect_pendentes = $this->connect->query($sql_pendentes);
        $dados_pendentes = $stmtSelect_pendentes->fetchAll(PDO::FETCH_ASSOC);

        // Fetch not deferred resources
        $sql_nao_deferidos = "SELECT r.*, c.nome as nome_candidato, u.nome_user AS nome_usuario 
                         FROM $this->table18 r 
                         INNER JOIN $this->table1 c ON r.id_candidato = c.id
                         INNER JOIN $this->table5 u ON r.id_usuario = u.id  
                         WHERE r.status = 'NÃO DEFERIDO' 
                         ORDER BY r.id DESC";
        $stmtSelect_nao_deferidos = $this->connect->query($sql_nao_deferidos);
        $dados_nao_deferidos = $stmtSelect_nao_deferidos->fetchAll(PDO::FETCH_ASSOC);

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
        $pdf->Image('../../assets/imgs/fundo5_pdf.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'png');
        // Header
        
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
        $nome_relatorio = 'RELATÓRIO DE RECURSOS';
        $count = mb_strlen($nome_relatorio);
        $pdf->Cell(55, 4, $nome_relatorio, 0, 1, 'L');
        $pdf->SetFillColor(255,165,0);
        $pdf->SetY(16);
        $pdf->SetX(9 );
        $pdf->Cell(3.9*$count, 1.2, '', 0, 1, 'L', true);

        $y_position = 32;

        // DEFERRED RESOURCES SECTION
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetFillColor(0, 90, 36); // Green background
        $pdf->SetTextColor(255, 255, 255); // White text
        $pdf->SetY($y_position);
        $pdf->SetX(10);
        $pdf->Cell(190, 8, 'RECURSOS DEFERIDOS', 1, 1, 'C', true);
        $y_position += 8;

        // Table Header for Deferred
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(0, 90, 36); // Green background
        $pdf->SetTextColor(255, 255, 255); // White text
        $pdf->SetY($y_position);
        $pdf->SetX(10);
        $pdf->Cell(40, 7, 'CANDIDATO', 1, 0, 'C', true);
        $pdf->Cell(85, 7, 'TEXTO DO RECURSO', 1, 0, 'C', true);
        $pdf->Cell(35, 7, 'USUÁRIO', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'DATA', 1, 1, 'C', true);
        $y_position += 7;

        // Reset text color to black
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);

        if (empty($dados_deferidos)) {
            $pdf->SetY($y_position);
            $pdf->SetX(10);
            $pdf->SetFillColor(0, 90, 36); // Green background for empty message
            $pdf->SetTextColor(255, 255, 255); // White text
            $pdf->Cell(190, 7, 'NENHUM RECURSO DEFERIDO ENCONTRADO', 1, 1, 'C', true);
            $y_position += 7;
        } else {
            foreach ($dados_deferidos as $dado) {
                // Break text into lines of 50 characters
                $texto_recurso = $dado['texto'];
                $linhas_texto = [];
                if (mb_strlen($texto_recurso, 'UTF-8') > 50) {
                    $texto_recurso = wordwrap($texto_recurso, 50, "\n", true);
                    $linhas_texto = explode("\n", $texto_recurso);
                } else {
                    $linhas_texto = [$texto_recurso];
                }
                
                // Calculate row height based on number of lines
                $num_linhas = count($linhas_texto);
                $altura_linha = 7;
                $altura_total = max($altura_linha, $altura_linha * $num_linhas);

                $pdf->SetFillColor(255, 255, 255);
                $x_inicial = 10;
                $y_inicial = $y_position;
                $y_atual = $y_inicial;
                
                // Prepare text content with truncation for long names
                $nome_candidato_original = mb_strtoupper($dado['nome_candidato'], 'UTF-8');
                $nome_candidato = mb_strlen($nome_candidato_original, 'UTF-8') > 18 ? mb_substr($nome_candidato_original, 0, 20, 'UTF-8') . '...' : $nome_candidato_original;
                $nome_usuario_original = mb_strtoupper($dado['nome_usuario'], 'UTF-8');
                $nome_usuario = mb_strlen($nome_usuario_original, 'UTF-8') > 18 ? mb_substr($nome_usuario_original, 0, 20, 'UTF-8') . '...' : $nome_usuario_original;
                $data = mb_strtoupper($dado['data'], 'UTF-8');
                
                // Draw candidate cell (first column) with border - centered vertically
                $pdf->SetXY($x_inicial, $y_atual);
                $pdf->Cell(40, $altura_total, $nome_candidato, 1, 0, 'L', true);
                
                // Draw text cell with MultiCell (second column) with border
                $pdf->SetXY($x_inicial + 40, $y_atual);
                $pdf->MultiCell(85, $altura_linha, $texto_recurso, 1, 'L', true);
                
                // Draw user cell (third column) with border - centered vertically
                $pdf->SetXY($x_inicial + 40 + 85, $y_atual);
                $pdf->Cell(35, $altura_total, $nome_usuario, 1, 0, 'L', true);
                
                // Draw date cell (fourth column) with border - centered vertically (recursos não tem data)
                $pdf->SetXY($x_inicial + 40 + 85 + 35, $y_atual);
                $pdf->Cell(30, $altura_total, '-', 1, 1, 'C', true);
                
                $y_position += $altura_total;

                // Add new page if needed
                if ($y_position > 270) {
                    $pdf->AddPage();
                    $y_position = 20;
                }
            }
        }

        $y_position += 10;

        // PENDING RESOURCES SECTION
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetFillColor(255, 165, 0); // Yellow background
        $pdf->SetTextColor(0, 0, 0); // Black text
        $pdf->SetY($y_position);
        $pdf->SetX(10);
        $pdf->Cell(190, 8, 'RECURSOS PENDENTES', 1, 1, 'C', true);
        $y_position += 8;

        // Table Header for Pending
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(255, 165, 0); // Yellow background
        $pdf->SetTextColor(0, 0, 0); // Black text
        $pdf->SetY($y_position);
        $pdf->SetX(10);
        $pdf->Cell(40, 7, 'CANDIDATO', 1, 0, 'C', true);
        $pdf->Cell(85, 7, 'TEXTO DO RECURSO', 1, 0, 'C', true);
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
            $pdf->Cell(190, 7, 'NENHUM RECURSO PENDENTE ENCONTRADO', 1, 1, 'C', true);
            $y_position += 7;
        } else {
            foreach ($dados_pendentes as $dado) {
                // Break text into lines of 50 characters
                $texto_recurso = $dado['texto'];
                $linhas_texto = [];
                if (mb_strlen($texto_recurso, 'UTF-8') > 50) {
                    $texto_recurso = wordwrap($texto_recurso, 50, "\n", true);
                    $linhas_texto = explode("\n", $texto_recurso);
                } else {
                    $linhas_texto = [$texto_recurso];
                }
                
                // Calculate row height based on number of lines
                $num_linhas = count($linhas_texto);
                $altura_linha = 7;
                $altura_total = max($altura_linha, $altura_linha * $num_linhas);

                $pdf->SetFillColor(255, 255, 255);
                $x_inicial = 10;
                $y_inicial = $y_position;
                $y_atual = $y_inicial;
                
                // Prepare text content with truncation for long names
                $nome_candidato_original = mb_strtoupper($dado['nome_candidato'], 'UTF-8');
                $nome_candidato = mb_strlen($nome_candidato_original, 'UTF-8') > 18 ? mb_substr($nome_candidato_original, 0, 20, 'UTF-8') . '...' : $nome_candidato_original;
                $nome_usuario_original = mb_strtoupper($dado['nome_usuario'], 'UTF-8');
                $nome_usuario = mb_strlen($nome_usuario_original, 'UTF-8') > 18 ? mb_substr($nome_usuario_original, 0, 20, 'UTF-8') . '...' : $nome_usuario_original;
                $data = mb_strtoupper($dado['data'], 'UTF-8');
                
                // Draw candidate cell (first column) with border - centered vertically
                $pdf->SetXY($x_inicial, $y_atual);
                $pdf->Cell(40, $altura_total, $nome_candidato, 1, 0, 'L', true);
                
                // Draw text cell with MultiCell (second column) with border
                $pdf->SetXY($x_inicial + 40, $y_atual);
                $pdf->MultiCell(85, $altura_linha, $texto_recurso, 1, 'L', true);
                
                // Draw user cell (third column) with border - centered vertically
                $pdf->SetXY($x_inicial + 40 + 85, $y_atual);
                $pdf->Cell(35, $altura_total, $nome_usuario, 1, 0, 'L', true);
                
                // Draw date cell (fourth column) with border - centered vertically (recursos não tem data)
                $pdf->SetXY($x_inicial + 40 + 85 + 35, $y_atual);
                $pdf->Cell(30, $altura_total, '-', 1, 1, 'C', true);
                
                $y_position += $altura_total;

                // Add new page if needed
                if ($y_position > 270) {
                    $pdf->AddPage();
                    $y_position = 20;
                }
            }
        }

        $y_position += 10;

        // NOT DEFERRED RESOURCES SECTION
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetFillColor(220, 53, 69); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        $pdf->SetY($y_position);
        $pdf->SetX(10);
        $pdf->Cell(190, 8, 'RECURSOS NÃO DEFERIDOS', 1, 1, 'C', true);
        $y_position += 8;

        // Table Header for Not Deferred
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(220, 53, 69); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        $pdf->SetY($y_position);
        $pdf->SetX(10);
        $pdf->Cell(40, 7, 'CANDIDATO', 1, 0, 'C', true);
        $pdf->Cell(85, 7, 'TEXTO DO RECURSO', 1, 0, 'C', true);
        $pdf->Cell(35, 7, 'USUÁRIO', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'DATA', 1, 1, 'C', true);
        $y_position += 7;

        // Reset text color to black
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);

        if (empty($dados_nao_deferidos)) {
            $pdf->SetY($y_position);
            $pdf->SetX(10);
            $pdf->SetFillColor(220, 53, 69); // Red background for empty message
            $pdf->Cell(190, 7, 'NENHUM RECURSO NÃO DEFERIDO ENCONTRADO', 1, 1, 'C', true);
            $y_position += 7;
        } else {
            foreach ($dados_nao_deferidos as $dado) {
                // Break text into lines of 50 characters
                $texto_recurso = $dado['texto'];
                $linhas_texto = [];
                if (mb_strlen($texto_recurso, 'UTF-8') > 50) {
                    $texto_recurso = wordwrap($texto_recurso, 50, "\n", true);
                    $linhas_texto = explode("\n", $texto_recurso);
                } else {
                    $linhas_texto = [$texto_recurso];
                }
                
                // Calculate row height based on number of lines
                $num_linhas = count($linhas_texto);
                $altura_linha = 7;
                $altura_total = max($altura_linha, $altura_linha * $num_linhas);

                $pdf->SetFillColor(255, 255, 255);
                $x_inicial = 10;
                $y_inicial = $y_position;
                $y_atual = $y_inicial;
                
                // Prepare text content with truncation for long names
                $nome_candidato_original = mb_strtoupper($dado['nome_candidato'], 'UTF-8');
                $nome_candidato = mb_strlen($nome_candidato_original, 'UTF-8') > 18 ? mb_substr($nome_candidato_original, 0, 20, 'UTF-8') . '...' : $nome_candidato_original;
                $nome_usuario_original = mb_strtoupper($dado['nome_usuario'], 'UTF-8');
                $nome_usuario = mb_strlen($nome_usuario_original, 'UTF-8') > 18 ? mb_substr($nome_usuario_original, 0, 20, 'UTF-8') . '...' : $nome_usuario_original;
                $data = mb_strtoupper($dado['data'], 'UTF-8');
                
                // Draw candidate cell (first column) with border - centered vertically
                $pdf->SetXY($x_inicial, $y_atual);
                $pdf->Cell(40, $altura_total, $nome_candidato, 1, 0, 'L', true);
                
                // Draw text cell with MultiCell (second column) with border
                $pdf->SetXY($x_inicial + 40, $y_atual);
                $pdf->MultiCell(85, $altura_linha, $texto_recurso, 1, 'L', true);
                
                // Draw user cell (third column) with border - centered vertically
                $pdf->SetXY($x_inicial + 40 + 85, $y_atual);
                $pdf->Cell(35, $altura_total, $nome_usuario, 1, 0, 'L', true);
                
                // Draw date cell (fourth column) with border - centered vertically (recursos não tem data)
                $pdf->SetXY($x_inicial + 40 + 85 + 35, $y_atual);
                $pdf->Cell(30, $altura_total, '-', 1, 1, 'C', true);
                
                $y_position += $altura_total;

                // Add new page if needed
                if ($y_position > 270) {
                    $pdf->AddPage();
                    $y_position = 20;
                }
            }
        }

        $pdf->Output('relatorio_recursos.pdf', 'I');
    }
}

if (isset($_GET['usuarios'])) {
    $relatorio = new relatorios($escola);
    $relatorio->relatorio_recursos();
} else {
    header('location:../../index.php');
    exit();
}
?>

