<?php
require_once(__DIR__ . '/../../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../../config/connect.php');
$escola = $_SESSION['escola'];

new connect($escola);
require_once(__DIR__ . '/../../assets/libs/fpdf/fpdf.php');

class PDF extends FPDF
{
    function AddPage($orientation = '', $size = '', $rotation = 0)
    {
        parent::AddPage($orientation, $size, $rotation);
        $this->Image('../../assets/imgs/fundo5_pdf.png', 0, 0, $this->GetPageWidth(), $this->GetPageHeight(), 'png', '', 0.1);
    }
}

class relatorios extends connect
{
    protected string $table1;
    protected string $table2;
    protected string $table3;
    protected string $table4;
    protected string $table5;

    protected string $table13;

    function __construct($escola)
    {
        parent::__construct($escola);
        $table = require(__DIR__ . '/../../../../.env/tables.php');
        $this->table1 = $table["ss_$escola"][1];
        $this->table2 = $table["ss_$escola"][2];
        $this->table3 = $table["ss_$escola"][3];
        $this->table4 = $table["ss_$escola"][4];
        $this->table5 = $table["ss_$escola"][5];
        $this->table13 = $table["ss_$escola"][13];
    }

    public function gerarRelatorio($curso, $tipo_relatorio = 'PRIVADA AC')
    {
        if ((isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin')) {
            //ADMIN
            $celula_nome = 80;
            $celula_curso = 23;
            $celula_origem = 15;
            $celula_segmento = 15;
            $celula_cadastrador = 30;
            $altura_celula = 5;
            $p = 0; // ñ quebra a linha caso seja cliente 
            $orientacao = 'P';
        } else if ((isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'cadastrador')) {
            //CLIENTE
            $celula_nome = 80;
            $celula_curso = 23;
            $celula_origem = 15;
            $celula_segmento = 15;
            $celula_cadastrador = 30;
            $altura_celula = 5;
            $p = 1; // quebra a linha caso seja cliente 
            $orientacao = 'P';
        }

        // Definir a consulta SQL com base no tipo de relatório
        $sql = "";
        $publica = 0;
        $pcd = 0;
        $bairro = 0;

        // Determinar os parâmetros da consulta com base no tipo de relatório
        switch ($tipo_relatorio) {
            case 'PRIVADA AC':
                $sql = "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final, u.nome_user 
                FROM $this->table1 can    
                INNER JOIN $this->table4 m ON m.id_candidato = can.id 
                INNER JOIN $this->table5 u ON can.id_cadastrador = u.id 
                INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
                WHERE can.id_curso1 = :curso AND can.publica = 0 AND can.pcd = 0 AND can.bairro = 0 AND can.status = 1
                ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC";
                break;
            case 'PRIVADA COTAS':
                $sql = "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final, u.nome_user 
                FROM $this->table1 can    
                INNER JOIN $this->table4 m ON m.id_candidato = can.id 
                INNER JOIN $this->table5 u ON can.id_cadastrador = u.id 
                INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
                WHERE can.id_curso1 = :curso AND can.publica = 0 AND (can.pcd = 1 OR can.bairro = 1) AND can.status = 1
                ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC";
                break;
            case 'PRIVADA GERAL':
                $sql = "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final, u.nome_user 
                FROM $this->table1 can    
                INNER JOIN $this->table4 m ON m.id_candidato = can.id 
                INNER JOIN $this->table5 u ON can.id_cadastrador = u.id 
                INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
                WHERE can.id_curso1 = :curso AND can.publica = 0 AND can.status = 1
                ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC";
                break;
            case 'PÚBLICA AC':
                $sql = "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final, u.nome_user 
                FROM $this->table1 can    
                INNER JOIN $this->table4 m ON m.id_candidato = can.id 
                INNER JOIN $this->table5 u ON can.id_cadastrador = u.id 
                INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
                WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.pcd = 0 AND can.bairro = 0 AND can.status = 1
                ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC";
                break;
            case 'PÚBLICA COTAS':
                $sql = "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final, u.nome_user 
                FROM $this->table1 can    
                INNER JOIN $this->table4 m ON m.id_candidato = can.id 
                INNER JOIN $this->table5 u ON can.id_cadastrador = u.id 
                INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
                WHERE can.id_curso1 = :curso AND can.publica = 1 AND (can.pcd = 1 OR can.bairro = 1) AND can.status = 1
                ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC";
                break;
            case 'PÚBLICA GERAL':
                $sql = "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final, u.nome_user 
                FROM $this->table1 can    
                INNER JOIN $this->table4 m ON m.id_candidato = can.id 
                INNER JOIN $this->table5 u ON can.id_cadastrador = u.id 
                INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
                WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.status = 1
                ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC";
                break;
            default:
                header("location:../relatorios.php");
                exit();
        }

        $stmtSelect = $this->connect->prepare($sql);
        $stmtSelect->BindValue(':curso', $curso);
        $stmtSelect->execute();
        $dados = $stmtSelect->fetchAll(PDO::FETCH_ASSOC);

        $pdf = new PDF($orientacao, 'mm', 'A4');
        $pdf->AddPage();
        // Cabeçalho com larguras ajustadas
        date_default_timezone_set('America/Fortaleza');
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->SetY(3);
        $pdf->SetX(46);
        $pdf->Cell(40, 4, $_SESSION['nome_escola'], 0, 0, 'C');
        $pdf->SetX(155);
        $pdf->Cell(40, 4, $datatime = date('Y/m/d H:i:s'), 0, 1, 'C');
        
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->SetY(8);
        $pdf->SetX(8.50);
        $pdf->Cell(22, 8, mb_convert_encoding($tipo_relatorio, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 8);

        //LEGENDAS PCD  |  COTISTAS  |  AC  ///////////////////////////////////////////////////////////////////////////////
        $pdf->SetLeftMargin(138);
        // Linha 1
        $pdf->SetY(8);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(255, 174, 25); // texto amarelo
        $pdf->Write(5, mb_convert_encoding('PCD', 'ISO-8859-1', 'UTF-8'));
        $pdf->SetTextColor(0, 90, 36); // volta pro preto
        $pdf->SetFont('Arial', '', 8);
        $pdf->Write(5, mb_convert_encoding('  PESSOA COM DEFICIÊNCIA', 'ISO-8859-1', 'UTF-8'));

        // Linha 2
        $pdf->SetY(12);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(255, 174, 25); // texto amarelo
        $pdf->Write(5, mb_convert_encoding('COTISTA', 'ISO-8859-1', 'UTF-8'));
        $pdf->SetTextColor(0, 90, 36);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Write(5, mb_convert_encoding('  COTA DO BAIRRO', 'ISO-8859-1', 'UTF-8'));

        // Linha 3
        $pdf->SetY(16);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(255, 174, 25); // texto amarelo
        $pdf->Write(5, mb_convert_encoding('AC', 'ISO-8859-1', 'UTF-8'));
        $pdf->SetTextColor(0, 90, 36);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Write(5, mb_convert_encoding('  AMPLA CONCORRÊNCIA', 'ISO-8859-1', 'UTF-8'));
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $pdf->SetLeftMargin(10);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(185, 10, '', 0, 1, 'C');

        $stmt_bairros = $this->connect->query("SELECT * FROM $this->table13");
        $dados_bairros = $stmt_bairros->fetchAll(PDO::FETCH_ASSOC);
        $bairros_para_mostrar = array_slice($dados_bairros, 0, 5);

        $pdf->SetFont('Arial', '', 8);

        // Título e bairros alinhados na mesma linha, exceto para PRIVADA AC e PÚBLICA AC
        if ($tipo_relatorio !== 'PRIVADA AC' && $tipo_relatorio !== 'PÚBLICA AC') {
            // mesmo alinhamento do tipo_relatorio
            $pdf->SetY(20);
            $pdf->SetX(8.50);

            // título em amarelo (mesma cor usada em PCD, COTISTA e AC)
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetTextColor(255, 174, 25);
            $pdf->Cell(28, 6, mb_convert_encoding('BAIRROS DE COTA:', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

            // texto dos bairros em verde-escuro
            $pdf->SetTextColor(0, 90, 36);
            $pdf->SetFont('Arial', '', 8);

            // monta a string dos bairros separados por " | "
            $bairros_texto = '';
            foreach ($bairros_para_mostrar as $index => $dado) {
                $bairro = strtoupper(mb_convert_encoding($dado['bairros'], 'ISO-8859-1', 'UTF-8'));
                $bairros_texto .= ($index < count($bairros_para_mostrar) - 1) ? $bairro . ' | ' : $bairro;
            }

            // imprime os bairros na mesma linha
            $pdf->Cell(0, 6, $bairros_texto, 0, 1, 'L');
            $pdf->SetY(16);
        } else {
            // mesmo espaçamento vertical quando não há seção de bairros
            $pdf->SetY(16); // posição equivalente à parte inferior da seção de bairros
        }

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(185, 10, '', 0, 1, 'C');
        // Fonte do cabeçalho
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetFillColor(0, 90, 36); //fundo verde
        $pdf->SetTextColor(255, 255, 255);  //texto branco
        $pdf->Cell(10, $altura_celula, mb_convert_encoding('CL', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
        $pdf->Cell($celula_nome, $altura_celula, mb_convert_encoding('NOME', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
        $pdf->Cell($celula_curso, $altura_celula, mb_convert_encoding('CURSO', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
        $pdf->Cell($celula_origem, $altura_celula, mb_convert_encoding('ORIGEM', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
        if ((isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin')) {
            $pdf->Cell($celula_segmento, $altura_celula, mb_convert_encoding('SEG', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
            $pdf->Cell(17, $altura_celula, mb_convert_encoding('MEDIA', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
            $pdf->Cell($celula_cadastrador, $altura_celula, mb_convert_encoding('CADASTRADOR(A)', 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', true);
        } else {
            $pdf->Cell(26, $altura_celula, mb_convert_encoding('Segmento', 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', true);
        }
        // Resetar cor do texto para preto
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);

        // Dados com cores alternadas
        $classificacao = 001;

        foreach ($dados as $dado) {
            // Definir escola
            $escola = ($dado['publica'] == 1) ? mb_convert_encoding('PÚBLICA', 'ISO-8859-1', 'UTF-8') : mb_convert_encoding('PRIVADA', 'ISO-8859-1', 'UTF-8');

            // Definir cota
            if ($dado['pcd'] == 1) {
                $cota = mb_convert_encoding('PCD', 'ISO-8859-1', 'UTF-8');
            } else if ($dado['publica'] == 0 && $dado['bairro'] == 1) {
                $cota = mb_convert_encoding('COTAS', 'ISO-8859-1', 'UTF-8');
            } else if ($dado['publica'] == 1 && $dado['bairro'] == 1) {
                $cota = mb_convert_encoding('COTAS', 'ISO-8859-1', 'UTF-8');
            } else {
                $cota = mb_convert_encoding('AC', 'ISO-8859-1', 'UTF-8');
            }

            // Definir cor da linha
            $cor = $classificacao % 2 ? 255 : 192;
            $pdf->SetFillColor($cor, $cor, $cor);

            // Imprimir linha no PDF
            $pdf->Cell(10, $altura_celula, sprintf("%03d", $classificacao), 1, 0, 'C', true);
            $pdf->Cell($celula_nome, $altura_celula, strtoupper(mb_convert_encoding($dado['nome'], 'ISO-8859-1', 'UTF-8')), 1, 0, 'L', true);
            $pdf->Cell($celula_curso, $altura_celula, strtoupper(mb_convert_encoding($dado['nome_curso'], 'ISO-8859-1', 'UTF-8')), 1, 0, 'L', true);
            $pdf->Cell($celula_origem, $altura_celula, $escola, 1, 0, 'L', true);
            $pdf->Cell($celula_segmento, $altura_celula, $cota, 1, $p, 'C', true); // verificar parâmetro 'p' na parte superior do relatório
            if ((isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin')) {
                $pdf->Cell(17, $altura_celula, number_format($dado['media_final'], 2), 1, 0, 'C', true);
                $cadastrador_text = strtoupper(mb_convert_encoding($dado['nome_user'], 'ISO-8859-1', 'UTF-8'));
                $text_width = $pdf->GetStringWidth($cadastrador_text);
                if ($text_width > $celula_cadastrador) {
                    $max_chars = floor(($celula_cadastrador / $text_width) * strlen($cadastrador_text)) - 3;
                    $cadastrador_text = substr($cadastrador_text, 0, $max_chars) . '...';
                }
                $pdf->Cell($celula_cadastrador, $altura_celula, $cadastrador_text, 1, 1, 'L', true);
            }
            $classificacao++;
        }
        $pdf->Output('classificacao.pdf', 'I');
    }
}

if (isset($_GET['curso']) && !empty($_GET['curso'])) {
    $relatorios = new relatorios($escola);
    $curso = $_GET['curso'];
    $tipo_relatorio = isset($_GET['tipo_relatorio']) ? $_GET['tipo_relatorio'] : 'PRIVADA AC';
    $relatorios->gerarRelatorio($curso, $tipo_relatorio);
} else {
    header('location:../../index.php');
    exit();
}