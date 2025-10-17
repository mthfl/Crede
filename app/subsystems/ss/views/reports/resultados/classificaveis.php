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

    public function classificaveis($curso)
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

        $pdf = new PDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->Image('../../../assets/imgs/fundo_pdf.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'png', '', 0.1);
        $stmtSelect_curso = $this->connect->prepare(
            "SELECT * FROM $this->table2 WHERE id = :id_curso"
        );
        $stmtSelect_curso->bindValue(':id_curso', $curso);
        $stmtSelect_curso->execute();
        $curso_nome = $stmtSelect_curso->fetch(PDO::FETCH_ASSOC);
        
        // Styling adjusted to match the base code
        $pdf->SetFont('Arial', 'B', 25);
        $pdf->SetY(8);
        $pdf->SetX(60);
        $pdf->Cell(90, 8, 'CLASSIFICÁVEIS', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetY(16);
        $pdf->SetX(11);
        $pdf->Cell(188, 6, " - " . mb_strtoupper($curso_nome['nome_curso'], 'UTF-8') . " - ", 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(188, 6, 'PCD = PESSOA COM DEFICIÊNCIA | COTISTA = INCLUSO NA COTA DO BAIRRO | AC = AMPLA CONCORRÊNCIA', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(185, 10, '', 0, 1, 'C');

        // Array para armazenar os classificáveis de cada segmento
        $classificaveis = [];

        // PÚBLICA - AC (a partir da vaga publica_ac + 1)
        $stmtSelect_ac_publica = $this->connect->prepare(
            "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
            FROM $this->table1 can    
            INNER JOIN $this->table4 m ON m.id_candidato = can.id 
            INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
            WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.pcd = 0 AND can.bairro = 0 
            ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC
            LIMIT 1000 OFFSET :offset"
        );
        $stmtSelect_ac_publica->bindValue(':curso', $curso);
        $stmtSelect_ac_publica->bindValue(':offset', $publica_ac, PDO::PARAM_INT);
        $stmtSelect_ac_publica->execute();
        $classificaveis['publica_ac'] = $stmtSelect_ac_publica->fetchAll(PDO::FETCH_ASSOC);

        // PÚBLICA - COTA (a partir da vaga publica_cotas + 1)
        $stmtSelect_bairro_publica = $this->connect->prepare(
            "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
            FROM $this->table1 can    
            INNER JOIN $this->table4 m ON m.id_candidato = can.id 
            INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
            WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.pcd = 0 AND can.bairro = 1 
            ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC
            LIMIT 1000 OFFSET :offset"
        );
        $stmtSelect_bairro_publica->bindValue(':curso', $curso);
        $stmtSelect_bairro_publica->bindValue(':offset', $publica_cotas, PDO::PARAM_INT);
        $stmtSelect_bairro_publica->execute();
        $classificaveis['publica_cotas'] = $stmtSelect_bairro_publica->fetchAll(PDO::FETCH_ASSOC);

        // PCD - Total (a partir da 3ª vaga)
        $stmtSelect_pcd_total = $this->connect->prepare(
            "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
            FROM $this->table1 can    
            INNER JOIN $this->table4 m ON m.id_candidato = can.id 
            INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
            WHERE can.id_curso1 = :curso AND can.pcd = 1 
            ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC
            LIMIT 1000 OFFSET :offset"
        );
        $stmtSelect_pcd_total->bindValue(':curso', $curso);
        $stmtSelect_pcd_total->bindValue(':offset', $vagas_pcd, PDO::PARAM_INT);
        $stmtSelect_pcd_total->execute();
        $classificaveis['pcd'] = $stmtSelect_pcd_total->fetchAll(PDO::FETCH_ASSOC);

        // PRIVADA - AC (a partir da vaga privada_ac + 1)
        $stmtSelect_ac_privada = $this->connect->prepare(
            "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
            FROM $this->table1 can    
            INNER JOIN $this->table4 m ON m.id_candidato = can.id 
            INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
            WHERE can.id_curso1 = :curso AND can.publica = 0 AND can.pcd = 0 AND can.bairro = 0 
            ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC
            LIMIT 1000 OFFSET :offset"
        );
        $stmtSelect_ac_privada->bindValue(':curso', $curso);
        $stmtSelect_ac_privada->bindValue(':offset', $privada_ac, PDO::PARAM_INT);
        $stmtSelect_ac_privada->execute();
        $classificaveis['privada_ac'] = $stmtSelect_ac_privada->fetchAll(PDO::FETCH_ASSOC);

        // PRIVADA - COTA (a partir da vaga privada_cotas + 1)
        $stmtSelect_bairro_privada = $this->connect->prepare(
            "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
            FROM $this->table1 can    
            INNER JOIN $this->table4 m ON m.id_candidato = can.id 
            INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
            WHERE can.id_curso1 = :curso AND can.publica = 0 AND can.pcd = 0 AND can.bairro = 1 
            ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC
            LIMIT 1000 OFFSET :offset"
        );
        $stmtSelect_bairro_privada->bindValue(':curso', $curso);
        $stmtSelect_bairro_privada->bindValue(':offset', $privada_cotas, PDO::PARAM_INT);
        $stmtSelect_bairro_privada->execute();
        $classificaveis['privada_cotas'] = $stmtSelect_bairro_privada->fetchAll(PDO::FETCH_ASSOC);

        // IMPRIMIR RELATÓRIO DOS CLASSIFICÁVEIS

        // PÚBLICA - AC
        $this->imprimirSegmentoClassificaveis($pdf, "REDE PUBLICA - AC (LISTA DE ESPERA)", $classificaveis['publica_ac']);

        // PÚBLICA - COTA
        $this->imprimirSegmentoClassificaveis($pdf, "REDE PUBLICA - COTA (LISTA DE ESPERA)", $classificaveis['publica_cotas']);

        // PCD
        $this->imprimirSegmentoClassificaveis($pdf, "PCD (LISTA DE ESPERA)", $classificaveis['pcd']);

        // PRIVADA - AC
        $this->imprimirSegmentoClassificaveis($pdf, "REDE PRIVADA - AC (LISTA DE ESPERA)", $classificaveis['privada_ac']);

        // PRIVADA - COTA
        $this->imprimirSegmentoClassificaveis($pdf, "REDE PRIVADA - COTA (LISTA DE ESPERA)", $classificaveis['privada_cotas']);

        // Verificar se há classificáveis em algum segmento
        $tem_classificaveis = false;
        foreach ($classificaveis as $segmento) {
            if (!empty($segmento)) {
                $tem_classificaveis = true;
                break;
            }
        }

        if (!$tem_classificaveis) {
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(185, 10, 'NÃO HÁ CANDIDATOS NA LISTA DE ESPERA', 0, 1, 'C');
        }

        $pdf->Output('classificaveis.pdf', 'I');
    }

    private function imprimirSegmentoClassificaveis($pdf, $titulo, $dados)
    {
        if (empty($dados)) {
            return;
        }

        // Fonte do cabeçalho
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(0, 90, 36); // fundo verde (#005A24)
        $pdf->SetTextColor(255, 255, 255); // texto branco
        $pdf->Cell(188, 5, $titulo, 1, 1, 'C', true);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(0, 90, 36); // fundo verde (#005A24)
        $pdf->SetTextColor(255, 255, 255); // texto branco
        $pdf->Cell(10, 5, 'CH', 1, 0, 'C', true);
        $pdf->Cell(93, 5, 'NOME', 1, 0, 'C', true);
        $pdf->Cell(30, 5, 'CURSO', 1, 0, 'C', true);
        $pdf->Cell(20, 5, 'ORIGEM', 1, 0, 'C', true);
        $pdf->Cell(20, 5, 'SEGM.', 1, 0, 'C', true);
        $pdf->Cell(15, 5, 'MEDIA', 1, 1, 'C', true);

        // Resetar cor do texto para preto
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);

        // Dados com cores alternadas
        $posicao = 1;

        foreach ($dados as $row) {
            // Definir escola
            $escola = ($row['publica'] == 1) ? 'PÚBLICA' : 'PRIVADA';

            // Definir cota
            if ($row['pcd'] == 1) {
                $cota = 'PCD';
            } else if ($row['bairro'] == 1) {
                $cota = 'COTISTA';
            } else {
                $cota = 'AC';
            }

            // Definir cor da linha
            $cor = $posicao % 2 ? 255 : 192;
            $pdf->SetFillColor($cor, $cor, $cor);

            // Imprimir linha no PDF - TUDO EM CAIXA ALTA
            $pdf->Cell(10, 5, sprintf("%03d", $posicao), 1, 0, 'C', true);
            $pdf->Cell(93, 5, mb_strtoupper($row['nome'], 'UTF-8'), 1, 0, 'L', true);
            $pdf->Cell(30, 5, mb_strtoupper($row['nome_curso'], 'UTF-8'), 1, 0, 'L', true);
            $pdf->Cell(20, 5, $escola, 1, 0, 'L', true);
            $pdf->Cell(20, 5, $cota, 1, 0, 'C', true);
            $pdf->Cell(15, 5, number_format($row['media_final'], 2), 1, 1, 'C', true);

            $posicao++;
        }
        $pdf->Ln(10);
    }
}

if (isset($_GET['curso']) && !empty($_GET['curso'])) {
    $relatorios = new relatorios($escola);
    $curso = $_GET['curso'];
    $relatorios->classificaveis($curso);
} else {
    header('location:../../../index.php');
    exit();
}