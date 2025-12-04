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
    public $data_hora_footer = '';

    function AddPage($orientation = '', $size = '', $rotation = 0)
    {
        parent::AddPage($orientation, $size, $rotation);
        $this->Image(
            '../../../assets/imgs/fundo5_pdf.png',
            0,
            0,
            $this->GetPageWidth(),
            $this->GetPageHeight(),
            'png'
        );
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
    protected string $table2;
    protected string $table3;
    protected string $table4;
    protected string $table5;
    protected string $table13;
    protected string $escola;

    public $bairros_texto_pdf;

    function __construct($escola)
    {
        parent::__construct($escola);
        $this->escola = $escola;
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
            $celula_cl = 10;
            $celula_nome = 70;
            $celula_curso = 30;
            $celula_origem = 18;
            $celula_segmento = 18;
            $celula_media = 15;
            $celula_status = 27;
            $altura_celula = 5;
            $orientacao = 'P';
        } else if ((isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'cadastrador')) {
            $celula_cl = 10;
            $celula_nome = 70;
            $celula_curso = 25;
            $celula_origem = 18;
            $celula_segmento = 18;
            $celula_media = 15;
            $celula_status = 32;
            $altura_celula = 5;
            $orientacao = 'P';
        }

        // ---------- CÁLCULO DE VAGAS ----------
        $stmtSelect_vagas = $this->connect->prepare(
            "SELECT quantidade_alunos FROM $this->table2 WHERE id = :id_curso"
        );
        $stmtSelect_vagas->bindValue(':id_curso', $curso);
        $stmtSelect_vagas->execute();
        $vagas_curso = $stmtSelect_vagas->fetch(PDO::FETCH_ASSOC);
        $total_vagas = $vagas_curso['quantidade_alunos'];

        $vagas_pcd = 2;
        $vagas_restantes = $total_vagas - $vagas_pcd;

        $total_publica = round($vagas_restantes * 0.8);
        $total_privada = round($vagas_restantes * 0.2);

        $publica_cotas = round($total_publica * 0.3);
        $privada_cotas = round($total_privada * 0.3);

        $publica_ac = round($total_publica * 0.7);
        $privada_ac = round($total_privada * 0.7);

        // ---------- CONSULTAS POR SEGMENTO ----------
        $todos_candidatos = [];

        $queries = [
            'publica_ac' => "SELECT can.id, can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
                             FROM $this->table1 can
                             INNER JOIN $this->table4 m ON m.id_candidato = can.id
                             INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id
                             WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.pcd = 0 AND can.bairro = 0 AND status = 1
                             ORDER BY m.media_final DESC, can.data_nascimento ASC, m.l_portuguesa_media DESC, m.matematica_media DESC",
            'publica_cotas' => "SELECT can.id, can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
                                FROM $this->table1 can
                                INNER JOIN $this->table4 m ON m.id_candidato = can.id
                                INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id
                                WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.pcd = 0 AND can.bairro = 1 AND status = 1
                                ORDER BY m.media_final DESC, can.data_nascimento ASC, m.l_portuguesa_media DESC, m.matematica_media DESC",
            'pcd_publica' => "SELECT can.id, can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
                              FROM $this->table1 can
                              INNER JOIN $this->table4 m ON m.id_candidato = can.id
                              INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id
                              WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.pcd = 1 AND status = 1
                              ORDER BY m.media_final DESC, can.data_nascimento ASC, m.l_portuguesa_media DESC, m.matematica_media DESC",
            'privada_ac' => "SELECT can.id, can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
                             FROM $this->table1 can
                             INNER JOIN $this->table4 m ON m.id_candidato = can.id
                             INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id
                             WHERE can.id_curso1 = :curso AND can.publica = 0 AND can.pcd = 0 AND can.bairro = 0 AND status = 1
                             ORDER BY m.media_final DESC, can.data_nascimento ASC, m.l_portuguesa_media DESC, m.matematica_media DESC",
            'privada_cotas' => "SELECT can.id, can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
                                 FROM $this->table1 can
                                 INNER JOIN $this->table4 m ON m.id_candidato = can.id
                                 INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id
                                 WHERE can.id_curso1 = :curso AND can.publica = 0 AND can.pcd = 0 AND can.bairro = 1 AND status = 1
                                 ORDER BY m.media_final DESC, can.data_nascimento ASC, m.l_portuguesa_media DESC, m.matematica_media DESC",
            'pcd_privada' => "SELECT can.id, can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
                              FROM $this->table1 can
                              INNER JOIN $this->table4 m ON m.id_candidato = can.id
                              INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id
                              WHERE can.id_curso1 = :curso AND can.publica = 0 AND can.pcd = 1 AND status = 1
                              ORDER BY m.media_final DESC, can.data_nascimento ASC, m.l_portuguesa_media DESC, m.matematica_media DESC"
        ];

        foreach ($queries as $key => $sql) {
            $stmt = $this->connect->prepare($sql);
            $stmt->bindValue(':curso', $curso);
            $stmt->execute();
            $todos_candidatos[$key] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // ---------- REDISTRIBUIÇÃO DE VAGAS ----------
        $vagas_ocupadas = [];
        $ids_classificados = [];

        $total_pcd = array_merge($todos_candidatos['pcd_publica'], $todos_candidatos['pcd_privada']);
        usort($total_pcd, fn($a, $b) => $b['media_final'] <=> $a['media_final']);
        $vagas_ocupadas['pcd'] = array_slice($total_pcd, 0, $vagas_pcd);
        foreach ($vagas_ocupadas['pcd'] as $cand) $ids_classificados[] = $cand['id'];

        $vagas_ocupadas['publica_cotas'] = array_slice($todos_candidatos['publica_cotas'], 0, $publica_cotas);
        foreach ($vagas_ocupadas['publica_cotas'] as $cand) $ids_classificados[] = $cand['id'];

        $vagas_ocupadas['privada_cotas'] = array_slice($todos_candidatos['privada_cotas'], 0, $privada_cotas);
        foreach ($vagas_ocupadas['privada_cotas'] as $cand) $ids_classificados[] = $cand['id'];

        $limite_publica_ac = $publica_ac + ($publica_cotas - count($vagas_ocupadas['publica_cotas'])) + ($vagas_pcd - count($vagas_ocupadas['pcd']));
        $vagas_ocupadas['publica_ac'] = array_slice($todos_candidatos['publica_ac'], 0, $limite_publica_ac);
        foreach ($vagas_ocupadas['publica_ac'] as $cand) $ids_classificados[] = $cand['id'];

        $limite_privada_ac = $privada_ac + ($privada_cotas - count($vagas_ocupadas['privada_cotas']));
        $vagas_ocupadas['privada_ac'] = array_slice($todos_candidatos['privada_ac'], 0, $limite_privada_ac);
        foreach ($vagas_ocupadas['privada_ac'] as $cand) $ids_classificados[] = $cand['id'];

        // ---------- LOGO DA ESCOLA E BAIRROS ----------
        // Buscar logo da escola
        $logo_escola = null;
        $stmt_logo = $this->connect_users->prepare("SELECT foto_perfil FROM escolas WHERE escola_banco = :escola_banco LIMIT 1");
        $stmt_logo->bindValue(':escola_banco', $this->escola);
        $stmt_logo->execute();
        $dados_logo = $stmt_logo->fetch();
        if ($dados_logo && !empty($dados_logo['foto_perfil'])) {
            $logo_path = __DIR__ . '/../../../assets/fotos_escola/' . $dados_logo['foto_perfil'];
            if (file_exists($logo_path)) {
                $logo_escola = $logo_path;
            }
        }

        // Data e hora para exibir no rodapé
        date_default_timezone_set('America/Fortaleza');
        $data_hora_pdf = date('d/m/Y H:i:s');

        $stmt_bairros = $this->connect->query("SELECT bairros FROM $this->table13 ORDER BY id LIMIT 5");
        $bairros = $stmt_bairros->fetchAll(PDO::FETCH_COLUMN);
        $this->bairros_texto_pdf = implode(' | ', array_map('strtoupper', $bairros));

        // ---------- INÍCIO DO PDF ----------
        $pdf = new PDF($orientacao, 'mm', 'A4');
        $pdf->data_hora_footer = $data_hora_pdf;
        $pdf->AddPage();

        // Nome da escola
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->SetY(3);
        $pdf->SetX(8.5);
        $pdf->Cell(0, 6, mb_convert_encoding($_SESSION['nome_escola'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');

        // Título principal
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->SetY(8);
        $pdf->SetX(8.5);
        $pdf->Cell(0, 8, mb_convert_encoding('RESULTADO FINAL', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');

        // Logo da escola
        if ($logo_escola) {
            $pdf->Image($logo_escola, 170, 3, 22, 0, '', '');
        }

        // Legendas
        $pdf->SetY(18);
        $pdf->SetX(8.5);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(255, 174, 25);
        $pdf->Cell(20, 6, 'PCD:', 0, 0, 'L');
        $pdf->SetTextColor(0, 90, 36);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(16);
        $pdf->Cell(70, 6, mb_convert_encoding('PESSOA COM DEFICIÊNCIA  |', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

        $pdf->SetX(58);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(255, 174, 25);
        $pdf->Cell(25, 6, 'COTISTA:', 0, 0, 'L');
        $pdf->SetTextColor(0, 90, 36);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(72);
        $pdf->Cell(70, 6, mb_convert_encoding('COTA DO BAIRRO  |', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

        $pdf->SetX(101);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(255, 174, 25);
        $pdf->Cell(15, 6, 'AC:', 0, 0, 'L');
        $pdf->SetTextColor(0, 90, 36);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(107);
        $pdf->Cell(0, 6, mb_convert_encoding('AMPLA CONCORRÊNCIA', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');

        // Bairros da cota
        $pdf->SetX(8.5);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(255, 174, 25);
        $pdf->Cell(35, 5, mb_convert_encoding('BAIRROS DA COTA:', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
        $pdf->SetTextColor(0, 90, 36);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(39);
        $pdf->Cell(0, 5, mb_convert_encoding($this->bairros_texto_pdf, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');

        // Linha separadora verde
        $pdf->SetDrawColor(0, 90, 36);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(10, $pdf->GetY() + 3, 200, $pdf->GetY() + 3);
        $pdf->SetLineWidth(0.2);
        $pdf->Ln(8);

        // Bordas sempre pretas
        $pdf->SetDrawColor(0, 0, 0);

        // ---------- IMPRESSÃO DOS SEGMENTOS ----------
        $segmentos = [
            ['titulo' => 'PÚBLICA - AC',      'dados' => $todos_candidatos['publica_ac']],
            ['titulo' => 'PÚBLICA - COTA',    'dados' => $todos_candidatos['publica_cotas']],
            ['titulo' => 'PCD',               'dados' => $total_pcd],
            ['titulo' => 'PRIVADA - AC',      'dados' => $todos_candidatos['privada_ac']],
            ['titulo' => 'PRIVADA - COTA',    'dados' => $todos_candidatos['privada_cotas']]
        ];

        $pdf->SetLeftMargin(10);
        $pdf->SetTextColor(0, 0, 0);

        foreach ($segmentos as $seg) {
            if (empty($seg['dados'])) continue;

            // Título do segmento - Verde com texto branco
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetFillColor(0, 90, 36);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(188, 7, mb_convert_encoding($seg['titulo'], 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', true);

            // Cabeçalho da tabela - Verde com texto branco
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetFillColor(0, 90, 36);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell($celula_cl, $altura_celula + 1, 'CL', 1, 0, 'C', true);
            $pdf->Cell($celula_nome, $altura_celula + 1, 'NOME', 1, 0, 'C', true);
            $pdf->Cell($celula_curso, $altura_celula + 1, 'CURSO', 1, 0, 'C', true);
            $pdf->Cell($celula_origem, $altura_celula + 1, 'SEGM.', 1, 0, 'C', true);
            $pdf->Cell($celula_segmento, $altura_celula + 1, 'ORIGEM', 1, 0, 'C', true);
            $pdf->Cell($celula_media, $altura_celula + 1, mb_convert_encoding('MÉDIA', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
            $pdf->Cell($celula_status, $altura_celula + 1, 'STATUS', 1, 1, 'C', true);

            // Linhas de dados - Zebrado cinza claro / branco
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', '', 8);
            $class = 1;

            foreach ($seg['dados'] as $row) {
                $origem = $row['publica'] ? 'PÚBLICA' : 'PRIVADA';
                $cota   = $row['pcd'] ? 'PCD' : ($row['bairro'] ? 'COTISTA' : 'AC');
                $isClassificado = in_array($row['id'], $ids_classificados);
                $status = $isClassificado ? 'CLASSIFICADO' : 'LISTA DE ESPERA';

                // destaques visuais para classificados
                $pdf->SetFont('Arial', $isClassificado ? 'B' : '', 8);

                // Zebrado: linha par = cinza claro, ímpar = branco
                if ($class % 2 == 0) {
                    $pdf->SetFillColor(240, 240, 240); // cinza claro
                } else {
                    $pdf->SetFillColor(255, 255, 255); // branco
                }

                $pdf->Cell($celula_cl, $altura_celula, sprintf('%03d', $class), 1, 0, 'C', true);
                $pdf->Cell($celula_nome, $altura_celula, mb_convert_encoding(mb_strtoupper($row['nome']), 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
                $pdf->Cell($celula_curso, $altura_celula, mb_convert_encoding(mb_strtoupper($row['nome_curso']), 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
                $pdf->Cell($celula_origem, $altura_celula, mb_convert_encoding($cota, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
                $pdf->Cell($celula_segmento, $altura_celula, mb_convert_encoding($origem, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
                $pdf->Cell($celula_media, $altura_celula, number_format($row['media_final'], 2), 1, 0, 'C', true);
                $pdf->Cell($celula_status, $altura_celula, mb_convert_encoding($status, 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', true);

                $class++;
            }
            $pdf->Ln(5);
        }

        $pdf->Output('I', 'resultado_final.pdf');
    }
}

if (isset($_GET['curso']) && !empty($_GET['curso'])) {
    $relatorios = new relatorios($escola);
    $curso = $_GET['curso'];
    $tipo_relatorio = $_GET['tipo_relatorio'] ?? 'TODOS';
    $relatorios->gerarRelatorio($curso, $tipo_relatorio);
} else {
    header('Location: ../../../index.php');
    exit();
}