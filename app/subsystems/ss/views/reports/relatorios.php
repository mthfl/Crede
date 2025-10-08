<?php
require_once(__DIR__ . '/../../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../../config/connect.php');
$escola = $_SESSION['escola'];

new connect($escola);
require_once(__DIR__ . '/../../assets/libs/fpdf/fpdf.php');

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
            $n = 102;
            $p = 0; // ñ quebra a linha caso seja cliente 
            $orientacao = 'L';
        } else if ((isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'cadastrador')) {
            //CLIENTE
            $n = 105;
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
                $publica = 0;
                $pcd = 0;
                $bairro = 0;
                break;
            case 'PRIVADA COTAS':
                $publica = 0;
                $pcd = 0;
                $bairro = 1;
                break;
            case 'PRIVADA GERAL':
                $publica = 0;
                $pcd = 0;
                $bairro = null; // Não filtrar por bairro
                break;
            case 'PÚBLICA AC':
                $publica = 1;
                $pcd = 0;
                $bairro = 0;
                break;
            case 'PÚBLICA COTAS':
                $publica = 1;
                $pcd = 0;
                $bairro = 1;
                break;
            case 'PÚBLICA GERAL':
                $publica = 1;
                $pcd = 0;
                $bairro = null; // Não filtrar por bairro
                break;
            default:
                $publica = 0;
                $pcd = 0;
                $bairro = 0;
                $tipo_relatorio = 'PRIVADA AC';
                break;
        }

        // Construir a consulta SQL com base nos parâmetros
        $sql = "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final, u.nome_user 
                FROM $this->table1 can    
                INNER JOIN $this->table4 m ON m.id_candidato = can.id 
                INNER JOIN $this->table5 u ON can.id_cadastrador = u.id 
                INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
                WHERE can.id_curso1 = :curso AND can.publica = :publica AND can.pcd = :pcd";
        
        // Adicionar filtro de bairro apenas se necessário
        if ($bairro !== null) {
            $sql .= " AND can.bairro = :bairro";
        }
        
        $sql .= " ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC;";
        
        $stmtSelect = $this->connect->prepare($sql);
        $stmtSelect->BindValue(':curso', $curso);
        $stmtSelect->BindValue(':publica', $publica);
        $stmtSelect->BindValue(':pcd', $pcd);
        
        // Vincular parâmetro de bairro apenas se necessário
        if ($bairro !== null) {
            $stmtSelect->BindValue(':bairro', $bairro);
        }
        
        $stmtSelect->execute();
        $dados = $stmtSelect->fetchAll(PDO::FETCH_ASSOC);

        $pdf = new FPDF($orientacao, 'mm', 'A4');
        $pdf->AddPage();

        // Cabeçalho com larguras ajustadas
        $pdf->Image(__DIR__ . '/../../assets/imgs/logo.png', 8, 8, 15, 0, 'PNG');
        $pdf->SetFont('Arial', 'B', 25);
        $pdf->SetY(8);
        $pdf->SetX(20);
        $pdf->Cell(90, 8, utf8_decode($tipo_relatorio), 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetY(16);
        $pdf->SetX(11);
        $pdf->Cell(188, 6, ('PCD = PESSOA COM DEFICIENCIA | COTISTA = INCLUSO NA COTA DO BAIRRO | AC = AMPLA CONCORRENCIA'), 0, 1, 'C');
        $pdf->SetFont('Arial', 'b', 12);
        $pdf->Cell(185, 10, '', 0, 1, 'C');

        $stmt_bairros = $this->connect->query("SELECT * FROM $this->table13");
        $dados_bairros = $stmt_bairros->fetchAll(PDO::FETCH_ASSOC);
        $bairros_para_mostrar = array_slice($dados_bairros, 0, 5);
       
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetY(20);
        $pdf->SetX(17);
        // Título e bairros alinhados na mesma linha, exceto para PRIVADA AC e PÚBLICA AC
        if ($tipo_relatorio !== 'PRIVADA AC' && $tipo_relatorio !== 'PÚBLICA AC') {
            $pdf->Cell(50, 6, 'BAIRROS DE COTA:', 0, 0, 'C');
            $pdf->SetFont('Arial', '', 8);
            $x_pos = 55; // Inicia logo após o título
            $item_width = 25; // Ajustado para caber 2 bairros com separador
            foreach ($bairros_para_mostrar as $index => $dado) {
                $pdf->SetX($x_pos);
                $bairro = strtoupper(utf8_decode($dado['bairros']));
                // Adiciona vírgula e espaço, exceto no último bairro
                $texto = ($index < count($bairros_para_mostrar) - 1) ? $bairro . ', ' : $bairro;
                $pdf->Cell($item_width, 6, $texto, 0, 0, 'L');
                $x_pos += $item_width;
            }
        }

        $pdf->SetFont('Arial', 'b', 12);
        $pdf->Cell(185, 10, '', 0, 1, 'C');
        // Fonte do cabeçalho
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(93, 164, 67); //fundo verde
        $pdf->SetTextColor(255, 255, 255);  //texto branco
        $pdf->Cell(10, 7, 'CH', 1, 0, 'C', true);
        $pdf->Cell($n, 7, 'Nome', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Curso', 1, 0, 'C', true);
        $pdf->Cell(20, 7, 'Origem', 1, 0, 'C', true);
        if ((isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin')) {
            $pdf->Cell(24, 7, 'Segmento', 1, 0, 'C', true);
            $pdf->Cell(17, 7, 'Media', 1, 0, 'C', true);
            $pdf->Cell(60, 7, 'Resp. Cadastro', 1, 1, 'C', true);
        } else {
            $pdf->Cell(26, 7, 'Segmento', 1, 1, 'C', true);
        }
        // Resetar cor do texto para preto
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);

        // Dados com cores alternadas
        $classificacao = 001;

        foreach ($dados as $dado) {

            // Definir escola
            $escola = ($dado['publica'] == 1) ? ('PUBLICA') : ('PRIVADA');

            // Definir cota
            if ($dado['pcd'] == 1) {
                $cota = 'PCD';
            } else if ($dado['publica'] == 0 && $dado['bairro'] == 1) {
                $cota = 'COTAS';
            } else if ($dado['publica'] == 1 && $dado['bairro'] == 1) {
                    $cota = 'COTAS';
            } else {
                $cota = 'AC';
            }

            // Definir cor da linha
            $cor = $classificacao % 2 ? 255 : 192;
            $pdf->SetFillColor($cor, $cor, $cor);

            // Imprimir linha no PDF
            $pdf->Cell(10, 7, sprintf("%03d", $classificacao), 1, 0, 'C', true);
            $pdf->Cell($n, 7, strToUpper(utf8_decode($dado['nome'])), 1, 0, 'L', true);
            $pdf->Cell(30, 7, strToUpper(utf8_decode($dado['nome_curso'])), 1, 0, 'L', true);
            $pdf->Cell(20, 7, $escola, 1, 0, 'L', true);
            $pdf->Cell(24, 7, $cota, 1, $p, 'C', true); // verificar parâmetro 'p' na parte superior do relatório
            if ((isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin')) {
                $pdf->Cell(17, 7, number_format($dado['media_final'], 2), 1, 0, 'C', true);
                $pdf->Cell(60, 7, strtoupper(utf8_decode($dado['nome_user'])), 1, 1, 'L', true);
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
