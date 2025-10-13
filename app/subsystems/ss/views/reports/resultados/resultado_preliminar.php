<?php
require_once(__DIR__ . '/../../../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../../../config/connect.php');
$escola = $_SESSION['escola'];

new connect($escola);
require_once(__DIR__ . '/../../../assets/libs/fpdf/fpdf.php');

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
    protected string $table2;
    protected string $table3;
    protected string $table4;
    protected string $table5;
    protected string $table13;

    function __construct($escola)
    {
        parent::__construct($escola);
        $table = require(__DIR__ . '/../../../../../.env/tables.php');
        $this->table1 = $table["ss_$escola"][1];
        $this->table2 = $table["ss_$escola"][2];
        $this->table3 = $table["ss_$escola"][3];
        $this->table4 = $table["ss_$escola"][4];
        $this->table5 = $table["ss_$escola"][5];
        $this->table13 = $table["ss_$escola"][13];
    }

    public function private_ac($curso)
    {
        require_once(__DIR__ . '/../../../config/connect.php');
        $escola = $_SESSION['escola'];
        require_once(__DIR__ . '/../../../models/model.select.php');
        $select = new select($escola);
        
        // Obter total de vagas do curso
        $stmtSelect_vagas = $this->connect->prepare(
            "SELECT quantidade_alunos FROM $this->table2 WHERE id = :id_curso"
        );
        $stmtSelect_vagas->bindValue(':id_curso', $curso);
        $stmtSelect_vagas->execute();
        $vagas_curso = $stmtSelect_vagas->fetch(PDO::FETCH_ASSOC);
        $total_vagas = $vagas_curso['quantidade_alunos'];

        // Cálculo das vagas por segmento
        $vagas_pcd = 2; // Vagas reservadas para PCD
        $vagas_restantes = $total_vagas - $vagas_pcd;
        
        $total_publica = floor($vagas_restantes * (80 / 100));
        $total_privada = $vagas_restantes - $total_publica;
        
        $publica_cotas = floor($total_publica * (30 / 100));
        $publica_ac = $total_publica - $publica_cotas;
        
        $privada_cotas = floor($total_privada * (30 / 100));
        $privada_ac = $total_privada - $privada_cotas;

        $pdf = new PDF();
        $pdf->AddPage();

        $stmtSelect_curso = $this->connect->prepare(
            "SELECT * FROM $this->table2 WHERE id = :id_curso"
        );
        $stmtSelect_curso->bindValue(':id_curso', $curso);
        $stmtSelect_curso->execute();
        $curso_nome = $stmtSelect_curso->fetch(PDO::FETCH_ASSOC);
        
        // Cabeçalho com larguras ajustadas
        $pdf->Image('../../../assets/imgs/logo.png', 8, 8, 15, 0, 'PNG');
        $pdf->SetFont('Arial', 'B', 25);
        $pdf->Cell(185, 10, 'RESULTADO PRELIMINAR', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(185, 5, " - " . mb_strtoupper($curso_nome['nome_curso'], 'UTF-8') . " - ", 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 10, ('PCD = PESSOA COM DEFICIENCIA | COTISTA = INCLUSO NA COTA DO BAIRRO | AC = AMPLA CONCORRENCIA'), 0, 1, 'C');
        $pdf->SetFont('Arial', 'b', 12);
        $pdf->Cell(185, 10, '', 0, 1, 'C');

        // Array para armazenar os classificados de cada segmento
        $classificados = [];

        // PÚBLICA - AC
        $stmtSelect_ac_publica = $this->connect->prepare(
            "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
            FROM $this->table1 can    
            INNER JOIN $this->table4 m ON m.id_candidato = can.id 
            INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
            WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.pcd = 0 AND can.bairro = 0 
            ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC"
        );
        $stmtSelect_ac_publica->bindValue(':curso', $curso);
        $stmtSelect_ac_publica->execute();
        $classificados['publica_ac'] = $stmtSelect_ac_publica->fetchAll(PDO::FETCH_ASSOC);

        // PÚBLICA - COTA
        $stmtSelect_bairro_publica = $this->connect->prepare(
            "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
            FROM $this->table1 can    
            INNER JOIN $this->table4 m ON m.id_candidato = can.id 
            INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
            WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.pcd = 0 AND can.bairro = 1 
            ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC"
        );
        $stmtSelect_bairro_publica->bindValue(':curso', $curso);
        $stmtSelect_bairro_publica->execute();
        $classificados['publica_cotas'] = $stmtSelect_bairro_publica->fetchAll(PDO::FETCH_ASSOC);

        // PCD - PÚBLICA
        $stmtSelect_pcd_publica = $this->connect->prepare(
            "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
            FROM $this->table1 can    
            INNER JOIN $this->table4 m ON m.id_candidato = can.id 
            INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
            WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.pcd = 1 AND can.bairro = 0 
            ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC"
        );
        $stmtSelect_pcd_publica->bindValue(':curso', $curso);
        $stmtSelect_pcd_publica->execute();
        $classificados['pcd_publica'] = $stmtSelect_pcd_publica->fetchAll(PDO::FETCH_ASSOC);

        // PRIVADA - AC
        $stmtSelect_ac_privada = $this->connect->prepare(
            "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
            FROM $this->table1 can    
            INNER JOIN $this->table4 m ON m.id_candidato = can.id 
            INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
            WHERE can.id_curso1 = :curso AND can.publica = 0 AND can.pcd = 0 AND can.bairro = 0 
            ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC"
        );
        $stmtSelect_ac_privada->bindValue(':curso', $curso);
        $stmtSelect_ac_privada->execute();
        $classificados['privada_ac'] = $stmtSelect_ac_privada->fetchAll(PDO::FETCH_ASSOC);

        // PRIVADA - COTA
        $stmtSelect_bairro_privada = $this->connect->prepare(
            "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
            FROM $this->table1 can    
            INNER JOIN $this->table4 m ON m.id_candidato = can.id 
            INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
            WHERE can.id_curso1 = :curso AND can.publica = 0 AND can.pcd = 0 AND can.bairro = 1 
            ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC"
        );
        $stmtSelect_bairro_privada->bindValue(':curso', $curso);
        $stmtSelect_bairro_privada->execute();
        $classificados['privada_cotas'] = $stmtSelect_bairro_privada->fetchAll(PDO::FETCH_ASSOC);

        // PCD - PRIVADA
        $stmtSelect_pcd_privada = $this->connect->prepare(
            "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
            FROM $this->table1 can    
            INNER JOIN $this->table4 m ON m.id_candidato = can.id 
            INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
            WHERE can.id_curso1 = :curso AND can.publica = 0 AND can.pcd = 1 AND can.bairro = 0 
            ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC"
        );
        $stmtSelect_pcd_privada->bindValue(':curso', $curso);
        $stmtSelect_pcd_privada->execute();
        $classificados['pcd_privada'] = $stmtSelect_pcd_privada->fetchAll(PDO::FETCH_ASSOC);

        // LÓGICA DE REDISTRIBUIÇÃO DE VAGAS
        $vagas_ocupadas = [];

        // PCD - Total (2 vagas para PCD independente de rede)
        $total_pcd = array_merge($classificados['pcd_publica'], $classificados['pcd_privada']);
        usort($total_pcd, function($a, $b) {
            return $b['media_final'] <=> $a['media_final'];
        });
        $vagas_ocupadas['pcd'] = array_slice($total_pcd, 0, $vagas_pcd);
        $vagas_sobra_pcd = $vagas_pcd - count($vagas_ocupadas['pcd']);

        // PÚBLICA COTAS - Verificar se há vagas não preenchidas
        $vagas_ocupadas['publica_cotas'] = array_slice($classificados['publica_cotas'], 0, $publica_cotas);
        $vagas_sobra_publica_cotas = $publica_cotas - count($vagas_ocupadas['publica_cotas']);

        // PRIVADA COTAS - Verificar se há vagas não preenchidas
        $vagas_ocupadas['privada_cotas'] = array_slice($classificados['privada_cotas'], 0, $privada_cotas);
        $vagas_sobra_privada_cotas = $privada_cotas - count($vagas_ocupadas['privada_cotas']);

        // PÚBLICA AC - Adicionar vagas que sobraram das cotas públicas
        $limite_publica_ac = $publica_ac + $vagas_sobra_publica_cotas + $vagas_sobra_pcd;
        $vagas_ocupadas['publica_ac'] = array_slice($classificados['publica_ac'], 0, $limite_publica_ac);

        // PRIVADA AC - Adicionar vagas que sobraram das cotas privadas
        $limite_privada_ac = $privada_ac + $vagas_sobra_privada_cotas;
        $vagas_ocupadas['privada_ac'] = array_slice($classificados['privada_ac'], 0, $limite_privada_ac);

        // IMPRIMIR RELATÓRIO

        // PÚBLICA - AC
        $this->imprimirSegmento($pdf, "REDE PUBLICA - AC", $vagas_ocupadas['publica_ac']);

        // PÚBLICA - COTA
        $this->imprimirSegmento($pdf, "REDE PUBLICA - COTA", $vagas_ocupadas['publica_cotas']);

        // PCD
        $this->imprimirSegmento($pdf, "PCD", $vagas_ocupadas['pcd']);

        // PRIVADA - AC
        $this->imprimirSegmento($pdf, "REDE PRIVADA - AC", $vagas_ocupadas['privada_ac']);

        // PRIVADA - COTA
        $this->imprimirSegmento($pdf, "REDE PRIVADA - COTA", $vagas_ocupadas['privada_cotas']);

        $pdf->Output('classificados.pdf', 'I');
    }

    private function imprimirSegmento($pdf, $titulo, $dados)
    {
        if (empty($dados)) {
            return;
        }

        // Fonte do cabeçalho
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(93, 164, 67); //fundo verde
        $pdf->SetTextColor(255, 255, 255);  //texto branco
        $pdf->Cell(191, -8, $titulo, 1, 0, 'C', true);
        $pdf->Ln(0);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(93, 164, 67); //fundo verde
        $pdf->SetTextColor(255, 255, 255);  //texto branco
        $pdf->Cell(10, 7, 'CH', 1, 0, 'C', true);
        $pdf->Cell(90, 7, 'NOME', 1, 0, 'C', true);
        $pdf->Cell(32, 7, 'CURSO', 1, 0, 'C', true);
        $pdf->Cell(18, 7, 'ORIGEM', 1, 0, 'C', true);
        $pdf->Cell(26, 7, 'SEGMENTO', 1, 0, 'C', true);
        $pdf->Cell(15, 7, 'MEDIA', 1, 1, 'C', true);

        // Resetar cor do texto para preto
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 10);

        // Dados com cores alternadas
        $classificacao = 1;

        foreach ($dados as $row) {
            // Definir escola
            $escola = ($row['publica'] == 1) ? ('PUBLICA') : ('PRIVADA');

            // Definir cota
            if ($row['pcd'] == 1) {
                $cota = 'PCD';
            } else if ($row['bairro'] == 1) {
                $cota = 'COTISTA';
            } else {
                $cota = 'AC';
            }

            // Definir cor da linha
            $cor = $classificacao % 2 ? 255 : 192;
            $pdf->SetFillColor($cor, $cor, $cor);

            // Imprimir linha no PDF - TUDO EM CAIXA ALTA
            $pdf->Cell(10, 7, sprintf("%03d", $classificacao), 1, 0, 'C', true);
            $pdf->Cell(90, 7, mb_strtoupper($row['nome'], 'UTF-8'), 1, 0, 'L', true);
            $pdf->Cell(32, 7, mb_strtoupper($row['nome_curso'], 'UTF-8'), 1, 0, 'L', true);
            $pdf->Cell(18, 7, $escola, 1, 0, 'L', true);
            $pdf->Cell(26, 7, $cota, 1, 0, 'L', true);
            $pdf->Cell(15, 7, number_format($row['media_final'], 2), 1, 1, 'C', true);

            $classificacao++;
        }
        $pdf->Ln(10);
    }
}

if (isset($_GET['curso']) && !empty($_GET['curso'])) {
    $relatorios = new relatorios($escola);
    $curso = $_GET['curso'];
    $relatorios->private_ac($curso);
} else {
    header('location:../../../index.php');
    exit();
}