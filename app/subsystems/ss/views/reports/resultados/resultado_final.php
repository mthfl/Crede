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
    protected string $table6;
    protected string $table7;
    protected string $table8;
    protected string $table9;
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
        $this->table6 = $table["ss_$escola"][6];
        $this->table7 = $table["ss_$escola"][7];
        $this->table8 = $table["ss_$escola"][8];
        $this->table9 = $table["ss_$escola"][9];
        $this->table13 = $table["ss_$escola"][13];
    }

    /**
     * Calcula a média final baseada nas notas dos 4 anos (6º, 7º, 8º, 9º)
     * Usa a mesma lógica do model.cadastrador.php
     */
    private function calcularMediaFinal($notas_6ano, $notas_7ano, $notas_8ano, $notas_9ano)
    {
        // Extrair valores das notas
        $lp_6ano = floatval($notas_6ano['l_portuguesa'] ?? 0);
        $artes_6ano = floatval($notas_6ano['artes'] ?? 0);
        $ef_6ano = floatval($notas_6ano['educacao_fisica'] ?? 0);
        $li_6ano = floatval($notas_6ano['l_inglesa'] ?? 0);
        $mate_6ano = floatval($notas_6ano['matematica'] ?? 0);
        $cien_6ano = floatval($notas_6ano['ciencias'] ?? 0);
        $geo_6ano = floatval($notas_6ano['geografia'] ?? 0);
        $hist_6ano = floatval($notas_6ano['historia'] ?? 0);
        $reli_6ano = floatval($notas_6ano['religiao'] ?? 0);

        $lp_7ano = floatval($notas_7ano['l_portuguesa'] ?? 0);
        $artes_7ano = floatval($notas_7ano['artes'] ?? 0);
        $ef_7ano = floatval($notas_7ano['educacao_fisica'] ?? 0);
        $li_7ano = floatval($notas_7ano['l_inglesa'] ?? 0);
        $mate_7ano = floatval($notas_7ano['matematica'] ?? 0);
        $cien_7ano = floatval($notas_7ano['ciencias'] ?? 0);
        $geo_7ano = floatval($notas_7ano['geografia'] ?? 0);
        $hist_7ano = floatval($notas_7ano['historia'] ?? 0);
        $reli_7ano = floatval($notas_7ano['religiao'] ?? 0);

        $lp_8ano = floatval($notas_8ano['l_portuguesa'] ?? 0);
        $artes_8ano = floatval($notas_8ano['artes'] ?? 0);
        $ef_8ano = floatval($notas_8ano['educacao_fisica'] ?? 0);
        $li_8ano = floatval($notas_8ano['l_inglesa'] ?? 0);
        $mate_8ano = floatval($notas_8ano['matematica'] ?? 0);
        $cien_8ano = floatval($notas_8ano['ciencias'] ?? 0);
        $geo_8ano = floatval($notas_8ano['geografia'] ?? 0);
        $hist_8ano = floatval($notas_8ano['historia'] ?? 0);
        $reli_8ano = floatval($notas_8ano['religiao'] ?? 0);

        $lp_9ano = floatval($notas_9ano['l_portuguesa'] ?? 0);
        $artes_9ano = floatval($notas_9ano['artes'] ?? 0);
        $ef_9ano = floatval($notas_9ano['educacao_fisica'] ?? 0);
        $li_9ano = floatval($notas_9ano['l_inglesa'] ?? 0);
        $mate_9ano = floatval($notas_9ano['matematica'] ?? 0);
        $cien_9ano = floatval($notas_9ano['ciencias'] ?? 0);
        $geo_9ano = floatval($notas_9ano['geografia'] ?? 0);
        $hist_9ano = floatval($notas_9ano['historia'] ?? 0);
        $reli_9ano = floatval($notas_9ano['religiao'] ?? 0);

        // Calcular médias por matéria (soma dos 4 anos / 4)
        $l_portuguesa_media = ($lp_6ano + $lp_7ano + $lp_8ano + $lp_9ano) / 4;
        $l_inglesa_media = ($li_6ano + $li_7ano + $li_8ano + $li_9ano) / 4;
        $matematica_media = ($mate_6ano + $mate_7ano + $mate_8ano + $mate_9ano) / 4;
        $ciencias_media = ($cien_6ano + $cien_7ano + $cien_8ano + $cien_9ano) / 4;
        $geografia_media = ($geo_6ano + $geo_7ano + $geo_8ano + $geo_9ano) / 4;
        $historia_media = ($hist_6ano + $hist_7ano + $hist_8ano + $hist_9ano) / 4;

        // Calcular média de artes (considerando zeros)
        if ($artes_6ano == 0 && $artes_7ano == 0 && $artes_8ano == 0 && $artes_9ano == 0) {
            $artes_media = 0;
        } else {
            $d_media = 4;
            if ($artes_6ano == 0) $d_media -= 1;
            if ($artes_7ano == 0) $d_media -= 1;
            if ($artes_8ano == 0) $d_media -= 1;
            if ($artes_9ano == 0) $d_media -= 1;
            $artes_media = ($artes_6ano + $artes_7ano + $artes_8ano + $artes_9ano) / $d_media;
        }

        // Calcular média de educação física (considerando zeros)
        if ($ef_6ano == 0 && $ef_7ano == 0 && $ef_8ano == 0 && $ef_9ano == 0) {
            $ef_media = 0;
        } else {
            $d_media = 4;
            if ($ef_6ano == 0) $d_media -= 1;
            if ($ef_7ano == 0) $d_media -= 1;
            if ($ef_8ano == 0) $d_media -= 1;
            if ($ef_9ano == 0) $d_media -= 1;
            $ef_media = ($ef_6ano + $ef_7ano + $ef_8ano + $ef_9ano) / $d_media;
        }

        // Calcular média de religião (considerando zeros)
        if ($reli_6ano == 0 && $reli_7ano == 0 && $reli_8ano == 0 && $reli_9ano == 0) {
            $reli_media = 0;
        } else {
            $d_media = 4;
            if ($reli_6ano == 0) $d_media -= 1;
            if ($reli_7ano == 0) $d_media -= 1;
            if ($reli_8ano == 0) $d_media -= 1;
            if ($reli_9ano == 0) $d_media -= 1;
            $reli_media = ($reli_6ano + $reli_7ano + $reli_8ano + $reli_9ano) / $d_media;
        }

        // Calcular média final (soma de todas as médias / quantidade de matérias)
        $d_media_final = 9;
        if ($artes_media == 0) $d_media_final -= 1;
        if ($ef_media == 0) $d_media_final -= 1;
        if ($reli_media == 0) $d_media_final -= 1;
        
        $media_final = ($l_portuguesa_media + $artes_media + $ef_media + $l_inglesa_media + 
                       $matematica_media + $ciencias_media + $geografia_media + $historia_media + 
                       $reli_media) / $d_media_final;

        return $media_final;
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
            'publica_ac' => "SELECT can.id, can.nome, can.data_nascimento, cur.nome_curso, can.publica, can.bairro, can.pcd
                             FROM $this->table1 can
                             INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id
                             WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.pcd = 0 AND can.bairro = 0 AND status = 1",
            'publica_cotas' => "SELECT can.id, can.nome, can.data_nascimento, cur.nome_curso, can.publica, can.bairro, can.pcd
                                FROM $this->table1 can
                                INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id
                                WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.pcd = 0 AND can.bairro = 1 AND status = 1",
            'pcd_publica' => "SELECT can.id, can.nome, can.data_nascimento, cur.nome_curso, can.publica, can.bairro, can.pcd
                              FROM $this->table1 can
                              INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id
                              WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.pcd = 1 AND status = 1",
            'privada_ac' => "SELECT can.id, can.nome, can.data_nascimento, cur.nome_curso, can.publica, can.bairro, can.pcd
                             FROM $this->table1 can
                             INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id
                             WHERE can.id_curso1 = :curso AND can.publica = 0 AND can.pcd = 0 AND can.bairro = 0 AND status = 1",
            'privada_cotas' => "SELECT can.id, can.nome, can.data_nascimento, cur.nome_curso, can.publica, can.bairro, can.pcd
                                 FROM $this->table1 can
                                 INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id
                                 WHERE can.id_curso1 = :curso AND can.publica = 0 AND can.pcd = 0 AND can.bairro = 1 AND status = 1",
            'pcd_privada' => "SELECT can.id, can.nome, can.data_nascimento, cur.nome_curso, can.publica, can.bairro, can.pcd
                              FROM $this->table1 can
                              INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id
                              WHERE can.id_curso1 = :curso AND can.publica = 0 AND can.pcd = 1 AND status = 1"
        ];

        foreach ($queries as $key => $sql) {
            $stmt = $this->connect->prepare($sql);
            $stmt->bindValue(':curso', $curso);
            $stmt->execute();
            $candidatos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Para cada candidato, buscar notas e calcular média final
            foreach ($candidatos as &$candidato) {
                // Buscar notas dos 4 anos
                $stmt_6ano = $this->connect->prepare("SELECT * FROM $this->table6 WHERE id_candidato = :id LIMIT 1");
                $stmt_6ano->bindValue(':id', $candidato['id']);
                $stmt_6ano->execute();
                $notas_6ano = $stmt_6ano->fetch(PDO::FETCH_ASSOC) ?: [];

                $stmt_7ano = $this->connect->prepare("SELECT * FROM $this->table7 WHERE id_candidato = :id LIMIT 1");
                $stmt_7ano->bindValue(':id', $candidato['id']);
                $stmt_7ano->execute();
                $notas_7ano = $stmt_7ano->fetch(PDO::FETCH_ASSOC) ?: [];

                $stmt_8ano = $this->connect->prepare("SELECT * FROM $this->table8 WHERE id_candidato = :id LIMIT 1");
                $stmt_8ano->bindValue(':id', $candidato['id']);
                $stmt_8ano->execute();
                $notas_8ano = $stmt_8ano->fetch(PDO::FETCH_ASSOC) ?: [];

                $stmt_9ano = $this->connect->prepare("SELECT * FROM $this->table9 WHERE id_candidato = :id LIMIT 1");
                $stmt_9ano->bindValue(':id', $candidato['id']);
                $stmt_9ano->execute();
                $notas_9ano = $stmt_9ano->fetch(PDO::FETCH_ASSOC) ?: [];

                // Calcular média final
                $candidato['media_final'] = $this->calcularMediaFinal($notas_6ano, $notas_7ano, $notas_8ano, $notas_9ano);
                
                // Calcular médias de português e matemática para ordenação
                $lp_6ano = floatval($notas_6ano['l_portuguesa'] ?? 0);
                $lp_7ano = floatval($notas_7ano['l_portuguesa'] ?? 0);
                $lp_8ano = floatval($notas_8ano['l_portuguesa'] ?? 0);
                $lp_9ano = floatval($notas_9ano['l_portuguesa'] ?? 0);
                $candidato['l_portuguesa_media'] = ($lp_6ano + $lp_7ano + $lp_8ano + $lp_9ano) / 4;

                $mate_6ano = floatval($notas_6ano['matematica'] ?? 0);
                $mate_7ano = floatval($notas_7ano['matematica'] ?? 0);
                $mate_8ano = floatval($notas_8ano['matematica'] ?? 0);
                $mate_9ano = floatval($notas_9ano['matematica'] ?? 0);
                $candidato['matematica_media'] = ($mate_6ano + $mate_7ano + $mate_8ano + $mate_9ano) / 4;
            }
            unset($candidato);

            // Ordenar por média final, data de nascimento, português e matemática
            usort($candidatos, function($a, $b) {
                if ($b['media_final'] != $a['media_final']) {
                    return $b['media_final'] <=> $a['media_final'];
                }
                if ($a['data_nascimento'] != $b['data_nascimento']) {
                    return $a['data_nascimento'] <=> $b['data_nascimento'];
                }
                if ($b['l_portuguesa_media'] != $a['l_portuguesa_media']) {
                    return $b['l_portuguesa_media'] <=> $a['l_portuguesa_media'];
                }
                return $b['matematica_media'] <=> $a['matematica_media'];
            });

            $todos_candidatos[$key] = $candidatos;
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

        // Primeiro preencher privada_ac para calcular vagas restantes
        $limite_privada_ac = $privada_ac + ($privada_cotas - count($vagas_ocupadas['privada_cotas']));
        $vagas_ocupadas['privada_ac'] = array_slice($todos_candidatos['privada_ac'], 0, $limite_privada_ac);
        foreach ($vagas_ocupadas['privada_ac'] as $cand) $ids_classificados[] = $cand['id'];
        
        // Calcular vagas restantes de privada_ac que não foram preenchidas
        $vagas_restantes_privada_ac = $limite_privada_ac - count($vagas_ocupadas['privada_ac']);
        
        // Adicionar vagas restantes de privada_ac para publica_ac
        $limite_publica_ac = $publica_ac + ($publica_cotas - count($vagas_ocupadas['publica_cotas'])) + ($vagas_pcd - count($vagas_ocupadas['pcd'])) + $vagas_restantes_privada_ac;
        $vagas_ocupadas['publica_ac'] = array_slice($todos_candidatos['publica_ac'], 0, $limite_publica_ac);
        foreach ($vagas_ocupadas['publica_ac'] as $cand) $ids_classificados[] = $cand['id'];

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
        $pdf->Line(10, $pdf->GetY() + 3, 197.55, $pdf->GetY() + 3);
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