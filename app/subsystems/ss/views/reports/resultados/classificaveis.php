<?php
require_once(__DIR__ . '/../../../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../../../config/connect.php');
$escola = $_SESSION['escola'];

new connect($escola);
require_once(__DIR__ . '/../../../assets/libs/fpdf/fpdf.php');

class PDF extends FPDF
{
    function AddPage($orientation = '', $size = '', $rotation = 0)
    {
        parent::AddPage($orientation, $size, $rotation);
        $this->Image('../../../assets/imgs/fundo5_pdf.png', 0, 0, $this->GetPageWidth(), $this->GetPageHeight(), 'png', '', 0.1);
    }

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

        // Inicializar PDF
        $pdf = new PDF('P', 'mm', 'A4');
        $pdf->AddPage();

        date_default_timezone_set('America/Fortaleza');
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->SetY(3);
        $pdf->SetX(46);
        $pdf->Cell(40, 4, $_SESSION['nome_escola'], 0, 0, 'C');
        $pdf->SetX(155);
        $pdf->Cell(40, 4, $datatime = date('Y/m/d H:i:s'), 0, 1, 'C');
        
        // Cabeçalho (apenas na primeira página)
        $stmtSelect_curso = $this->connect->prepare(
            "SELECT * FROM $this->table2 WHERE id = :id_curso"
        );
        $stmtSelect_curso->bindValue(':id_curso', $curso);
        $stmtSelect_curso->execute();
        $curso_nome = $stmtSelect_curso->fetch(PDO::FETCH_ASSOC);

        $pdf->SetFont('Arial', 'B', 20);
        $pdf->SetY(8);
        $pdf->SetX(8.50);
        $pdf->Cell(22, 8, 'CLASSIFICÁVEIS', 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetY(16);
        $pdf->SetX(11);
        $pdf->Cell(188, 6, " - " . mb_strtoupper($curso_nome['nome_curso'], 'UTF-8') . " - ", 0, 1, 'C');

        // LEGENDAS PCD | COTISTAS | AC (apenas na primeira página)
        $pdf->SetLeftMargin(138);
        $pdf->SetY(8);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(255, 174, 25); // texto amarelo
        $pdf->Write(5, 'PCD');
        $pdf->SetTextColor(0, 90, 36); // texto verde
        $pdf->SetFont('Arial', '', 8);
        $pdf->Write(5, '  PESSOA COM DEFICIÊNCIA');

        $pdf->SetY(12);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(255, 174, 25);
        $pdf->Write(5, 'COTISTA');
        $pdf->SetTextColor(0, 90, 36);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Write(5, '  COTA DO BAIRRO');

        $pdf->SetY(16);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(255, 174, 25);
        $pdf->Write(5, 'AC');
        $pdf->SetTextColor(0, 90, 36);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Write(5, '  AMPLA CONCORRÊNCIA');

        $pdf->SetLeftMargin(10);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(185, 10, '', 0, 1, 'C');

        // Bairros (apenas na primeira página)
        $stmt_bairros = $this->connect->query("SELECT * FROM $this->table13");
        $dados_bairros = $stmt_bairros->fetchAll(PDO::FETCH_ASSOC);
        $bairros_para_mostrar = array_slice($dados_bairros, 0, 5);

        $pdf->SetY(20);
        $pdf->SetX(8.50);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(255, 174, 25);
        $pdf->Cell(28, 6, 'BAIRROS DA COTA:', 0, 0, 'L');
        $pdf->SetTextColor(0, 90, 36);
        $pdf->SetFont('Arial', '', 8);
        $bairros_texto = '';
        foreach ($bairros_para_mostrar as $index => $dado) {
            $bairro = strtoupper($dado['bairros']);
            $bairros_texto .= ($index < count($bairros_para_mostrar) - 1) ? $bairro . ' | ' : $bairro;
        }
        $pdf->Cell(0, 6, $bairros_texto, 0, 1, 'L');
        $pdf->SetY(30); // Ajustar posição Y após o cabeçalho

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
        $segmentos = [
            ['titulo' => 'REDE PÚBLICA - AC (LISTA DE ESPERA)', 'dados' => $classificaveis['publica_ac']],
            ['titulo' => 'REDE PÚBLICA - COTA (LISTA DE ESPERA)', 'dados' => $classificaveis['publica_cotas']],
            ['titulo' => 'PCD (LISTA DE ESPERA)', 'dados' => $classificaveis['pcd']],
            ['titulo' => 'REDE PRIVADA - AC (LISTA DE ESPERA)', 'dados' => $classificaveis['privada_ac']],
            ['titulo' => 'REDE PRIVADA - COTA (LISTA DE ESPERA)', 'dados' => $classificaveis['privada_cotas']]
        ];

        $tem_classificaveis = false;
        foreach ($segmentos as $segmento) {
            if (!empty($segmento['dados'])) {
                $tem_classificaveis = true;
                break;
            }
        }

        if (!$tem_classificaveis) {
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(185, 10, 'NÃO HÁ CANDIDATOS NA LISTA DE ESPERA', 0, 1, 'C');
        } else {
            $primeiro_segmento = true;
            foreach ($segmentos as $segmento) {
                $titulo = $segmento['titulo'];
                $dados = $segmento['dados'];

                if (empty($dados)) {
                    continue;
                }

                // Se for o primeiro segmento, garantir que estamos na posição correta após o cabeçalho
                if ($primeiro_segmento) {
                    $pdf->SetY(30);
                    $primeiro_segmento = false;
                } else {
                    // Adicionar espaçamento de 10mm entre segmentos
                    $espacamento_segmento = 10;
                    $y_atual = $pdf->GetY();
                    $altura_pagina = $pdf->GetPageHeight();
                    $y_com_espacamento = $y_atual + $espacamento_segmento;

                    // Se o espaçamento ultrapassar a página, criar nova página
                    if ($y_com_espacamento > $altura_pagina - 10) {
                        $pdf->AddPage();
                        $pdf->SetY(10);
                    } else {
                        $pdf->SetY($y_com_espacamento);
                    }
                }

                // Verificar se há espaço suficiente na página para o segmento completo
                $linhas_necessarias = count($dados) + 2; // +2 para o título e cabeçalho da tabela
                $espaco_por_linha = 5; // Altura da célula
                $espaco_total = $linhas_necessarias * $espaco_por_linha; // Espaço necessário para o segmento
                $y_atual = $pdf->GetY();
                $altura_pagina = $pdf->GetPageHeight();
                $espaco_disponivel = $altura_pagina - $y_atual - 10; // Margem inferior

                // Só adiciona nova página se realmente não houver espaço
                if ($espaco_total > $espaco_disponivel && $y_atual > 35) {
                    $pdf->AddPage();
                    $pdf->SetY(10); // Iniciar no topo da nova página, sem repetir o cabeçalho
                }

                // Imprimir título do segmento em uma célula fixa
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->SetFillColor(0, 90, 36); // fundo verde (#005A24)
                $pdf->SetTextColor(255, 255, 255); // texto branco
                $pdf->Cell(188, 5, $titulo, 1, 1, 'C', true);

                // Cabeçalho da tabela
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetFillColor(0, 90, 36); // fundo verde (#005A24)
                $pdf->SetTextColor(255, 255, 255); // texto branco
                $pdf->Cell(10, 5, 'CL', 1, 0, 'C', true);
                $pdf->Cell(93, 5, 'NOME', 1, 0, 'C', true);
                $pdf->Cell(30, 5, 'CURSO', 1, 0, 'C', true);
                $pdf->Cell(20, 5, 'SEGM.', 1, 0, 'C', true);
                $pdf->Cell(20, 5, 'ORIGEM', 1, 0, 'C', true);
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
                    $pdf->Cell(20, 5, $cota, 1, 0, 'C', true);
                    $pdf->Cell(20, 5, $escola, 1, 0, 'L', true);
                    $pdf->Cell(15, 5, number_format($row['media_final'], 2), 1, 1, 'C', true);

                    $posicao++;
                }
            }
        }

        $pdf->Output('classificaveis.pdf', 'I');
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