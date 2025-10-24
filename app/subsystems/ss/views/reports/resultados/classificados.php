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

    public function gerarRelatorio($curso, $tipo_relatorio = 'TODOS')
    {
        if ((isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin')) {
            // ADMIN
            $celula_ch = 10;
            $celula_nome = 93;
            $celula_curso = 30;
            $celula_origem = 20;
            $celula_segmento = 20;
            $celula_media = 15;
            $altura_celula = 5;
            $p = 0; // Não quebra a linha caso seja admin
            $orientacao = 'P';
        } else if ((isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'cadastrador')) {
            // CLIENTE
            $celula_ch = 10;
            $celula_nome = 93;
            $celula_curso = 30;
            $celula_origem = 20;
            $celula_segmento = 20;
            $celula_media = 15;
            $altura_celula = 5;
            $p = 1; // Quebra a linha caso seja cliente
            $orientacao = 'P';
        }

        // Cálculo das vagas
        $stmtSelect_vagas = $this->connect->prepare(
            "SELECT quantidade_alunos FROM $this->table2 WHERE id = :id_curso"
        );
        $stmtSelect_vagas->bindValue(':id_curso', $curso);
        $stmtSelect_vagas->execute();
        $vagas_curso = $stmtSelect_vagas->fetch(PDO::FETCH_ASSOC);
        $total_vagas = $vagas_curso['quantidade_alunos'];

        $vagas_pcd = 2; 
        $vagas_restantes = $total_vagas - $vagas_pcd;

        round($total_publica = $vagas_restantes * (80 / 100))."<br>";
        round($total_privada = $vagas_restantes * (20 / 100))."<br>";

        round($publica_cotas = $total_publica * (30 / 100))."<br>";
        round($privada_cotas = $total_privada * (30 / 100))."<br>";

        round($publica_ac = $total_publica * (70 / 100))."<br>";
        round($privada_ac = $total_privada * (70 / 100))."<br>";

        // Array para armazenar os classificados de cada segmento
        $classificados = [];

        // PÚBLICA - AC
        $stmtSelect_ac_publica = $this->connect->prepare(
            "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
            FROM $this->table1 can    
            INNER JOIN $this->table4 m ON m.id_candidato = can.id 
            INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
            WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.pcd = 0 AND can.bairro = 0 AND status = 1 
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
            WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.pcd = 0 AND can.bairro = 1 AND status = 1 
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
            WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.pcd = 1 AND can.bairro = 0 AND status = 1 
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
            WHERE can.id_curso1 = :curso AND can.publica = 0 AND can.pcd = 0 AND can.bairro = 0 AND status = 1 
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
            WHERE can.id_curso1 = :curso AND can.publica = 0 AND can.pcd = 0 AND can.bairro = 1 AND status = 1 
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
            WHERE can.id_curso1 = :curso AND can.publica = 0 AND can.pcd = 1 AND can.bairro = 0 AND status = 1 
            ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC"
        );
        $stmtSelect_pcd_privada->bindValue(':curso', $curso);
        $stmtSelect_pcd_privada->execute();
        $classificados['pcd_privada'] = $stmtSelect_pcd_privada->fetchAll(PDO::FETCH_ASSOC);

        // LÓGICA DE REDISTRIBUIÇÃO DE VAGAS
        $vagas_ocupadas = [];

        // PCD - Total (2 vagas para PCD independente de rede)
        $total_pcd = array_merge($classificados['pcd_publica'], $classificados['pcd_privada']);
        usort($total_pcd, function ($a, $b) {
            return $b['media_final'] <=> $a['media_final'];
        });
        $vagas_ocupadas['pcd'] = array_slice($total_pcd, 0, $vagas_pcd);
        $vagas_sobra_pcd = $vagas_pcd - count($vagas_ocupadas['pcd']);

        // PÚBLICA COTAS - Verificar se há vagas não preenchidas
        $vagas_ocupadas['publica_cotas'] = array_slice($classificados['publica_cotas'], 0, round($publica_cotas));
        $vagas_sobra_publica_cotas = $publica_cotas - count($vagas_ocupadas['publica_cotas']);

        // PRIVADA COTAS - Verificar se há vagas não preenchidas
        $vagas_ocupadas['privada_cotas'] = array_slice($classificados['privada_cotas'], 0, round($privada_cotas));
        $vagas_sobra_privada_cotas = $privada_cotas - count($vagas_ocupadas['privada_cotas']);

        // PÚBLICA AC - Adicionar vagas que sobraram das cotas públicas
        $limite_publica_ac = $publica_ac + $vagas_sobra_publica_cotas + $vagas_sobra_pcd;
        $vagas_ocupadas['publica_ac'] = array_slice($classificados['publica_ac'], 0, round($limite_publica_ac));

        // PRIVADA AC - Adicionar vagas que sobraram das cotas privadas
        $limite_privada_ac = $privada_ac + $vagas_sobra_privada_cotas;
        $vagas_ocupadas['privada_ac'] = array_slice($classificados['privada_ac'], 0, round($limite_privada_ac));

        // Inicializar PDF
        $pdf = new PDF($orientacao, 'mm', 'A4');
        $pdf->AddPage();

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
        $pdf->Cell(22, 8, mb_convert_encoding('CLASSIFICADOS', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetY(16);
        $pdf->SetX(11);
        $pdf->Cell(188, 6, mb_convert_encoding(" - " . mb_strtoupper($curso_nome['nome_curso'], 'UTF-8') . " - ", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // LEGENDAS PCD | COTISTAS | AC (apenas na primeira página)
        $pdf->SetLeftMargin(138);
        $pdf->SetY(8);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(255, 174, 25); // texto amarelo
        $pdf->Write(5, mb_convert_encoding('PCD', 'ISO-8859-1', 'UTF-8'));
        $pdf->SetTextColor(0, 90, 36); // texto verde
        $pdf->SetFont('Arial', '', 8);
        $pdf->Write(5, mb_convert_encoding('  PESSOA COM DEFICIÊNCIA', 'ISO-8859-1', 'UTF-8'));

        $pdf->SetY(12);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(255, 174, 25);
        $pdf->Write(5, mb_convert_encoding('COTISTA', 'ISO-8859-1', 'UTF-8'));
        $pdf->SetTextColor(0, 90, 36);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Write(5, mb_convert_encoding('  COTA DO BAIRRO', 'ISO-8859-1', 'UTF-8'));

        $pdf->SetY(16);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(255, 174, 25);
        $pdf->Write(5, mb_convert_encoding('AC', 'ISO-8859-1', 'UTF-8'));
        $pdf->SetTextColor(0, 90, 36);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Write(5, mb_convert_encoding('  AMPLA CONCORRÊNCIA', 'ISO-8859-1', 'UTF-8'));

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
        $pdf->Cell(28, 6, mb_convert_encoding('BAIRROS DE COTA:', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
        $pdf->SetTextColor(0, 90, 36);
        $pdf->SetFont('Arial', '', 8);
        $bairros_texto = '';
        foreach ($bairros_para_mostrar as $index => $dado) {
            $bairro = strtoupper(mb_convert_encoding($dado['bairros'], 'ISO-8859-1', 'UTF-8'));
            $bairros_texto .= ($index < count($bairros_para_mostrar) - 1) ? $bairro . ' | ' : $bairro;
        }
        $pdf->Cell(0, 6, $bairros_texto, 0, 1, 'L');
        $pdf->SetY(30); // Ajustar posição Y após o cabeçalho

        // IMPRIMIR RELATÓRIO PARA TODOS OS SEGMENTOS
        $segmentos = [
            ['titulo' => 'PÚBLICA - AC', 'dados' => $vagas_ocupadas['publica_ac']],
            ['titulo' => 'PÚBLICA - COTA', 'dados' => $vagas_ocupadas['publica_cotas']],
            ['titulo' => 'PCD', 'dados' => $vagas_ocupadas['pcd']],
            ['titulo' => 'PRIVADA - AC', 'dados' => $vagas_ocupadas['privada_ac']],
            ['titulo' => 'PRIVADA - COTA', 'dados' => $vagas_ocupadas['privada_cotas']]
        ];

        foreach ($segmentos as $segmento) {
            $titulo = $segmento['titulo'];
            $dados = $segmento['dados'];

            if (empty($dados)) {
                continue;
            }

            // Verificar se há espaço suficiente na página para o segmento
            $linhas_necessarias = count($dados) + 2; // +2 para o título e cabeçalho da tabela
            $espaco_por_linha = $altura_celula;
            $espaco_total = $linhas_necessarias * $espaco_por_linha + 10; // +10 para o espaço após o segmento
            $espaco_disponivel = $pdf->GetPageHeight() - $pdf->GetY() - 10; // Margem inferior

            if ($espaco_total > $espaco_disponivel) {
                $pdf->AddPage();
                $pdf->SetY(10); // Iniciar no topo da nova página, sem repetir o cabeçalho
            }

            // Imprimir título do segmento em uma célula fixa
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetFillColor(0, 90, 36); // fundo verde (#005A24)
            $pdf->SetTextColor(255, 255, 255); // texto branco
            $pdf->Cell(188, 5, mb_convert_encoding($titulo, 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', true);

            // Cabeçalho da tabela
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetFillColor(0, 90, 36); // fundo verde (#005A24)
            $pdf->SetTextColor(255, 255, 255); // texto branco
            $pdf->Cell($celula_ch, $altura_celula, mb_convert_encoding('CH', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
            $pdf->Cell($celula_nome, $altura_celula, mb_convert_encoding('NOME', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
            $pdf->Cell($celula_curso, $altura_celula, mb_convert_encoding('CURSO', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
            $pdf->Cell($celula_origem, $altura_celula, mb_convert_encoding('ORIGEM', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
            $pdf->Cell($celula_segmento, $altura_celula, mb_convert_encoding('SEGM.', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
            $pdf->Cell($celula_media, $altura_celula, mb_convert_encoding('MEDIA', 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', true);

            // Resetar cor do texto para preto
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', '', 8);

            // Dados com cores alternadas
            $classificacao = 1;

            foreach ($dados as $row) {
                // Definir escola
                $escola = ($row['publica'] == 1) ? mb_convert_encoding('PÚBLICA', 'ISO-8859-1', 'UTF-8') : mb_convert_encoding('PRIVADA', 'ISO-8859-1', 'UTF-8');

                // Definir cota
                if ($row['pcd'] == 1) {
                    $cota = mb_convert_encoding('PCD', 'ISO-8859-1', 'UTF-8');
                } else if ($row['bairro'] == 1) {
                    $cota = mb_convert_encoding('COTISTA', 'ISO-8859-1', 'UTF-8');
                } else {
                    $cota = mb_convert_encoding('AC', 'ISO-8859-1', 'UTF-8');
                }

                // Definir cor da linha
                $cor = $classificacao % 2 ? 255 : 192;
                $pdf->SetFillColor($cor, $cor, $cor);

                // Imprimir linha no PDF - TUDO EM CAIXA ALTA
                $pdf->Cell($celula_ch, $altura_celula, sprintf("%03d", $classificacao), 1, 0, 'C', true);
                $pdf->Cell($celula_nome, $altura_celula, mb_convert_encoding(mb_strtoupper($row['nome'], 'UTF-8'), 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
                $pdf->Cell($celula_curso, $altura_celula, mb_convert_encoding(mb_strtoupper($row['nome_curso'], 'UTF-8'), 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
                $pdf->Cell($celula_origem, $altura_celula, $escola, 1, 0, 'L', true);
                $pdf->Cell($celula_segmento, $altura_celula, $cota, 1, 0, 'C', true);
                $pdf->Cell($celula_media, $altura_celula, number_format($row['media_final'], 2), 1, 1, 'C', true);

                $classificacao++;
            }
            $pdf->Ln(10);
        }

        $pdf->Output('classificados.pdf', 'I');
    }
}

if (isset($_GET['curso']) && !empty($_GET['curso'])) {
    $relatorios = new relatorios($escola);
    $curso = $_GET['curso'];
    $tipo_relatorio = isset($_GET['tipo_relatorio']) ? $_GET['tipo_relatorio'] : 'TODOS';
    $relatorios->gerarRelatorio($curso, $tipo_relatorio);
} else {
    header('location:../../../index.php');
    exit();
}