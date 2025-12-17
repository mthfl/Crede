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
    protected string $table18;
    protected string $table19;
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
        $this->table18 = $table["ss_$escola"][18];
        $this->table19 = $table["ss_$escola"][19];
    }

    /**
     * Calcula a média final baseada nas notas dos 4 anos (6º, 7º, 8º, 9º)
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

    /**
     * Busca todos os cursos disponíveis
     */
    private function buscarCursos()
    {
        $stmt = $this->connect->prepare("SELECT id, nome_curso FROM $this->table2 ORDER BY id");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca datas de matrícula dos cursos do banco de dados
     */
    private function buscarDatasMatricula()
    {
        $stmt = $this->connect->prepare("SELECT 
    CASE 
        WHEN m.id_curso IS NULL THEN 'TODOS OS CURSOS'
        ELSE c.nome_curso 
    END AS nome_curso,
    m.id_curso,
    m.data,
    m.hora
FROM matriculas m
LEFT JOIN cursos c ON m.id_curso = c.id
ORDER BY 
    m.data ASC,
    nome_curso ASC;");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Conta candidatos por segmento para um curso específico
     */
    private function contarCandidatosPorSegmento($curso_id)
    {
        $counts = [
            'total' => 0,
            'publica_total' => 0,
            'publica_ac' => 0,
            'publica_cotas' => 0,
            'pcd_publica' => 0,
            'privada_total' => 0,
            'privada_ac' => 0,
            'privada_cotas' => 0,
            'pcd_privada' => 0,
            'pcd_total' => 0
        ];

        $queries = [
            'publica_ac' => "SELECT COUNT(*) as count FROM $this->table1 
                             WHERE id_curso1 = :curso AND publica = 1 AND pcd = 0 AND bairro = 0 AND status = 1",
            'publica_cotas' => "SELECT COUNT(*) as count FROM $this->table1 
                                WHERE id_curso1 = :curso AND publica = 1 AND pcd = 0 AND bairro = 1 AND status = 1",
            'pcd_publica' => "SELECT COUNT(*) as count FROM $this->table1 
                              WHERE id_curso1 = :curso AND publica = 1 AND pcd = 1 AND status = 1",
            'privada_ac' => "SELECT COUNT(*) as count FROM $this->table1 
                             WHERE id_curso1 = :curso AND publica = 0 AND pcd = 0 AND bairro = 0 AND status = 1",
            'privada_cotas' => "SELECT COUNT(*) as count FROM $this->table1 
                                WHERE id_curso1 = :curso AND publica = 0 AND pcd = 0 AND bairro = 1 AND status = 1",
            'pcd_privada' => "SELECT COUNT(*) as count FROM $this->table1 
                              WHERE id_curso1 = :curso AND publica = 0 AND pcd = 1 AND status = 1"
        ];

        foreach ($queries as $key => $sql) {
            $stmt = $this->connect->prepare($sql);
            $stmt->bindValue(':curso', $curso_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $result['count'] ?? 0;

            switch ($key) {
                case 'publica_ac':
                    $counts['publica_ac'] = $count;
                    $counts['publica_total'] += $count;
                    $counts['total'] += $count;
                    break;
                case 'publica_cotas':
                    $counts['publica_cotas'] = $count;
                    $counts['publica_total'] += $count;
                    $counts['total'] += $count;
                    break;
                case 'pcd_publica':
                    $counts['pcd_publica'] = $count;
                    $counts['publica_total'] += $count;
                    $counts['pcd_total'] += $count;
                    $counts['total'] += $count;
                    break;
                case 'privada_ac':
                    $counts['privada_ac'] = $count;
                    $counts['privada_total'] += $count;
                    $counts['total'] += $count;
                    break;
                case 'privada_cotas':
                    $counts['privada_cotas'] = $count;
                    $counts['privada_total'] += $count;
                    $counts['total'] += $count;
                    break;
                case 'pcd_privada':
                    $counts['pcd_privada'] = $count;
                    $counts['privada_total'] += $count;
                    $counts['pcd_total'] += $count;
                    $counts['total'] += $count;
                    break;
            }
        }

        return $counts;
    }

    /**
     * Cria a capa do relatório final
     */
    private function criarCapa($pdf, $logo_escola, $data_hora_pdf)
    {
        // ---------- PÁGINA DE CAPA ----------
        $pdf->AddPage();

        // Logotipo da escola (se existir)
        if ($logo_escola) {
            $pdf->Image($logo_escola, 10, 10, 30, 0, '', '');
        }

        // Nome da escola
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetY(2);
        $pdf->SetX(9);
        $pdf->Cell(0, 10, mb_convert_encoding(strtoupper($_SESSION['nome_escola']), 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');

        // INEP e local
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetY(10);
        $pdf->SetX(9);
        $pdf->Cell(0, 6, mb_convert_encoding('CONFORME A PORTARIA Nº2278/2025 - GAB; PARECER Nº 010690/2025/SEDUC/ASJUR;', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');


        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(255, 174, 25);
        $pdf->SetTextColor(255, 174, 25);
        $pdf->Cell(148, 1.3, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C', true);

        $pdf->SetTextColor(0, 0, 0);
        // Coordenadoria Regional
        $pdf->SetFont('Arial', 'B', 14.5);
        $pdf->SetY($pdf->GetY() + 15);
        $pdf->SetX(10);
        $pdf->Cell(0, 8, mb_convert_encoding('1ª COORDENADORIA REGIONAL DE DESENVOLVIMENTO DA EDUCAÇÃO', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // Título principal
        $pdf->SetFont('Arial', 'B', 30);
        $pdf->SetY($pdf->GetY() + 10);
        $pdf->SetX(10);
        $pdf->Cell(0, 15, mb_convert_encoding('SELEÇÃO DE ALUNOS - 2026', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // Subtítulo
        $pdf->SetFont('Arial', 'B', 32);
        $pdf->SetTextColor(0, 90, 36); // Verde
        $pdf->SetY($pdf->GetY() + 5);
        $pdf->SetX(10);
        $pdf->Cell(0, 20, mb_convert_encoding('RESULTADO FINAL', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0); // Volta ao preto

        // ---------- DATAS DE MATRÍCULA ----------
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->SetY($pdf->GetY() + 5);
        $pdf->SetX(14);
        $pdf->Cell(0, 10, mb_convert_encoding('CRONOGRAMA DE MATRÍCULA PARA CANDIDATOS CLASSIFICADOS 2026', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // Buscar datas de matrícula do banco
        $datas_matricula = $this->buscarDatasMatricula();

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(0, 90, 36); // Verde
        $pdf->SetTextColor(255, 255, 255); // Texto branco
        $pdf->SetX(22);
        $pdf->Cell(100, 8, mb_convert_encoding('CURSOS', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
        $pdf->Cell(70, 8, mb_convert_encoding('DATA / HORA', 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', true);

        // Dados do cronograma vindos do banco
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        foreach ($datas_matricula as $curso) {
            $pdf->SetX(22);

                $pdf->Cell(100, 7, mb_convert_encoding($curso['nome_curso'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'L');

            $pdf->Cell(70, 7, mb_convert_encoding(date('d/m/Y', strtotime($curso['data'])) . ' às ' . date('H:i', strtotime($curso['hora'])), 'ISO-8859-1', 'UTF-8'), 1, 1, 'C');
        }

        $pdf->Ln(5);

        // ---------- DOCUMENTOS NECESSÁRIOS ----------
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetX(17);
        $pdf->Cell(0, 10, mb_convert_encoding('DOCUMENTOS NECESSÁRIOS', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // Tabela de documentos
        $documentos = [
            'A' => 'Declaração de CONCLUSÃO 9° ANO ou Certificado e Histórico Escolar de Conclusão do Ensino Fundamental (esses dois últimos, caso a escola já possa emitir)',
            'B' => 'Cópia da Certidão de Nascimento do(a) Aluno(a)',
            'C' => 'Cópia do comprovante do Cadastro de Pessoa Física (CPF) do(a) Aluno(a)',
            'D' => '2 (duas) Fotos 3x4 do(a) Aluno(a)',
            'E' => 'Cópia do comprovante de endereço',
            'F' => 'Cópia do Cartão de Vacinação, conforme Lei Estadual Nº 16.929, de 09/07/2019, para Estudantes com até 18 (dezoito) anos de idade do(a) Aluno(a)',
            'G' => 'Cópia do Cartão de Vacinação contra Covid-19 do(a) Aluno(a) (ATUALIZADO)',
            'H' => 'Cópia do Registro Geral (RG) ou da Carteira de Identidade Nacional (CIN) do(a) aluno(a)',
            'I' => 'Cópia do comprovante de Identificação Social (NIS DO(A) ALUNO(A) para as famílias cadastradas no Cadastro Único para Programas Sociais do Governo Federal',
            'J' => 'Laudo, relatório ou atestado que comprovem alergia alimentares, doenças, transtornos e/ou deficiência, caso possua',
            'K' => 'Cópia do RG e CPF do pai, da mãe ou do(a) responsável legal'
        ];

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetLeftMargin(20);

        foreach ($documentos as $letra => $descricao) {
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(10, 6, $letra . ' -', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->MultiCell(0, 6, mb_convert_encoding($descricao, 'ISO-8859-1', 'UTF-8'), 0, 'L');
            $pdf->Ln(2);
        }

        $pdf->SetLeftMargin(10);
        $pdf->Ln(10);

        // Rodapé da capa
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetY(-20);
        $pdf->SetX(20);
        $pdf->Cell(0, 6, '', 0, 1, 'L');
    }

    /**
     * Cria seção de deferimentos no final do relatório
     */
    /**
     * Cria seção de deferimentos no final do relatório
     */
    private function criarSecaoDeferimentos($pdf)
    {
        $pdf->AddPage();

        // Título da seção de deferimentos
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetY(20);
        $pdf->SetX(10);
        $pdf->Cell(0, 10, mb_convert_encoding('RESULTADO DOS DEFERIMENTOS', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $pdf->Ln(5);

        // Buscar recursos do banco
        $stmt_recursos = $this->connect->prepare("
        SELECT r.*, c.nome, c.publica, c.pcd, c.bairro, cur.nome_curso 
        FROM $this->table19 r 
        INNER JOIN $this->table1 c ON r.id_candidato = c.id 
        INNER JOIN $this->table2 cur ON c.id_curso1 = cur.id 
        WHERE r.status != 'PENDENTE'
        ORDER BY c.id_curso1, r.status DESC, c.nome
    ");
        $stmt_recursos->execute();
        $recursos = $stmt_recursos->fetchAll(PDO::FETCH_ASSOC);

        if (empty($recursos)) {
            // Se não houver recursos analisados
            $pdf->SetFont('Arial', 'I', 12);
            $pdf->SetY(40);
            $pdf->SetX(10);
            $pdf->Cell(0, 10, mb_convert_encoding('Nenhum recurso analisado encontrado.', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
            return;
        }

        // Agrupar recursos por curso
        $recursos_por_curso = [];
        foreach ($recursos as $recurso) {
            $curso_id = $recurso['id_curso1'] ?? 0;
            if (!isset($recursos_por_curso[$curso_id])) {
                $recursos_por_curso[$curso_id] = [
                    'nome_curso' => $recurso['nome_curso'],
                    'deferidos' => 0,
                    'indeferidos' => 0,
                    'recursos' => []
                ];
            }

            // Determinar segmento do candidato
            $segmento = $this->determinarSegmento($recurso['publica'], $recurso['pcd'], $recurso['bairro']);

            // Adicionar recurso à lista do curso
            $recursos_por_curso[$curso_id]['recursos'][] = [
                'nome' => $recurso['nome'],
                'segmento' => $segmento,
                'motivo' => $recurso['texto'],
                'status' => $recurso['status'],
                'resposta' => $recurso['resposta'] ?? ''
            ];

            // Contar por status
            if (strtoupper($recurso['status']) == 'DEFERIDO') {
                $recursos_por_curso[$curso_id]['deferidos']++;
            } elseif (strtoupper($recurso['status']) == 'INDEFERIDO') {
                $recursos_por_curso[$curso_id]['indeferidos']++;
            }
        }

        // Detalhes dos recursos
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetX(10);
        $pdf->Cell(0, 10, mb_convert_encoding('DETALHES DOS RECURSOS ANALISADOS', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $pdf->Ln(5);

        // Exibir detalhes por curso
        foreach ($recursos_por_curso as $curso_id => $dados_curso) {
            if (empty($dados_curso['recursos'])) continue;

            // Cabeçalho da tabela
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetTextColor(255,255,255);
            $pdf->SetFillColor(0, 90, 36);
            $pdf->SetX(10);
            $pdf->Cell(60, 8, mb_convert_encoding('CANDIDATO', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
            $pdf->Cell(25, 8, mb_convert_encoding('SEGMENTO', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
            $pdf->Cell(72, 8, mb_convert_encoding('MOTIVO DO RECURSO', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
            $pdf->Cell(30, 8, mb_convert_encoding('STATUS', 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', true);

            $pdf->SetFont('Arial', '', 8);
            $linha = 0;

            foreach ($dados_curso['recursos'] as $recurso) {
                // Alternar cores das linhas
                if ($linha % 2 == 0) {
                    $pdf->SetFillColor(245, 245, 245);
                } else {
                    $pdf->SetFillColor(255, 255, 255);
                }

                $status = strtoupper($recurso['status']);

                // Definir cor do status
                if ($status == 'DEFERIDO') {
                    $pdf->SetTextColor(0, 100, 0); // Verde
                } elseif ($status == 'INDEFERIDO') {
                    $pdf->SetTextColor(200, 0, 0); // Vermelho
                } else {
                    $pdf->SetTextColor(0, 0, 0); // Preto
                }

                // Nome do candidato (truncado se necessário)
                $nome = mb_strimwidth($recurso['nome'], 0, 30, '...');

                $pdf->SetX(10);
                $pdf->Cell(60, 7, mb_convert_encoding($nome, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
                $pdf->Cell(25, 7, mb_convert_encoding($recurso['segmento'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);

                // Motivo do recurso (truncado)
                $motivo = mb_strimwidth($recurso['motivo'], 0, 40, '...');
                $pdf->Cell(72, 7, mb_convert_encoding($motivo, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);

                // Status
                $pdf->Cell(30, 7, mb_convert_encoding($status, 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', true);

                $pdf->SetTextColor(0, 0, 0); // Voltar ao preto
                $linha++;

                // Verificar se precisa de nova página
                if ($pdf->GetY() > 270) {
                    $pdf->AddPage();
                    $this->recriarCabecalhoRecursos($pdf);
                    $pdf->Ln(5);
                }
            }

            $pdf->Ln(10);
        }
    }

    /**
     * Determina o segmento do candidato
     */
    private function determinarSegmento($publica, $pcd, $bairro)
    {
        if ($pcd == 1) {
            return 'PCD';
        } elseif ($bairro == 1) {
            return 'COTAS';
        } else {
            return 'AC';
        }
    }

    /**
     * Recria cabeçalho básico nas páginas da seção de recursos
     */
    private function recriarCabecalhoRecursos($pdf)
    {
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->SetY(2);
        $pdf->SetX(9);
        $pdf->Cell(0, 6, mb_convert_encoding($_SESSION['nome_escola'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetY(8);
        $pdf->SetX(9);
        $pdf->Cell(0, 8, mb_convert_encoding('RESULTADO DOS DEFERIMENTOS', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');

        $pdf->SetFont('Arial', 'I', 9);
        $pdf->SetY(14);
        $pdf->SetX(9);
        $pdf->Cell(0, 6, mb_convert_encoding('Relatório de Análise de Recursos - Resultado Final', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');

        $pdf->SetDrawColor(0, 90, 36);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(10, 20, 197.55, 20);
        $pdf->SetLineWidth(0.2);
        $pdf->Ln(10);

        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLeftMargin(10);
        $pdf->SetTextColor(0, 0, 0);
    }

    public function gerarRelatorioFinal()
    {
        // Configurações do PDF - AJUSTADAS
        $celula_cl = 10;       // CH
        $celula_nome = 72;     // Nome (aumentado)
        $celula_curso = 28;    // Curso (reduzido)
        $celula_segmento = 15; // Segm. (reduzido para 15)
        $celula_origem = 20;   // Origem
        $celula_media = 15;    // Média
        $celula_status = 28;   // Status (aumentado para 28)
        $altura_celula = 5;
        $orientacao = 'P';

        // Buscar todos os cursos
        $cursos = $this->buscarCursos();

        if (empty($cursos)) {
            echo "Nenhum curso encontrado!";
            exit;
        }

        // ---------- LOGO DA ESCOLA E BAIRROS ----------
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

        // Criar capa (agora com datas de matrícula e documentos)
        $this->criarCapa($pdf, $logo_escola, $data_hora_pdf);

        // ---------- RESULTADOS POR CURSO (mantido igual) ----------
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->SetY(2);
        $pdf->SetX(9);
        $pdf->Cell(0, 6, mb_convert_encoding(strtoupper($_SESSION['nome_escola']), 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetY(8);
        $pdf->SetX(9);
        $pdf->Cell(0, 8, mb_convert_encoding('RESULTADO FINAL', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');

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

        // Linha separadora verde
        $pdf->SetDrawColor(0, 90, 36);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(10, $pdf->GetY() + 3, 197.55, $pdf->GetY() + 3);
        $pdf->SetLineWidth(0.2);
        $pdf->Ln(8);

        // Bordas sempre pretas
        $pdf->SetDrawColor(0, 0, 0);

        $pdf->SetLeftMargin(10);
        $pdf->SetTextColor(0, 0, 0);

        // Processar cada curso (mantido igual ao preliminar)
        foreach ($cursos as $curso) {
            $curso_id = $curso['id'];
            $curso_nome = $curso['nome_curso'];

            // Contar candidatos por segmento para este curso
            $contagens = $this->contarCandidatosPorSegmento($curso_id);

            // ---------- TÍTULO DO CURSO ----------
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetFillColor(0, 90, 36); // Verde
            $pdf->SetTextColor(255, 255, 255); // Texto branco
            $pdf->Cell(188, 8, mb_convert_encoding($curso_nome, 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', true);

            // ---------- APENAS OS TOTAIS COMO NO EXEMPLO ----------
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetTextColor(0, 0, 0);

            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(94, 6, mb_convert_encoding('Total de Inscritos:', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', true);
            $pdf->Cell(94, 6, $contagens['total'], 0, 1, 'R', true);

            // Segunda linha – cinza claro
            $pdf->SetFillColor(230, 230, 230);
            $pdf->Cell(94, 6, mb_convert_encoding('Total Pública:', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', true);
            $pdf->Cell(94, 6, $contagens['publica_total'], 0, 1, 'R', true);

            // Terceira linha – branco
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(94, 6, mb_convert_encoding('Total Privada:', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', true);
            $pdf->Cell(94, 6, $contagens['privada_total'], 0, 1, 'R', true);

            // Quarta linha – cinza claro
            $pdf->SetFillColor(230, 230, 230);
            $pdf->Cell(94, 6, mb_convert_encoding('Total Cota PCD:', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', true);
            $pdf->Cell(94, 6, $contagens['pcd_total'], 0, 1, 'R', true);

            $pdf->Ln(5);

            // ---------- BUSCAR E ORDENAR CANDIDATOS DO CURSO ----------
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

            // Array para armazenar o segmento original de cada candidato
            $segmento_original_por_id = [];

            foreach ($queries as $key => $sql) {
                $stmt = $this->connect->prepare($sql);
                $stmt->bindValue(':curso', $curso_id);
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

                    // Armazenar o segmento original
                    $candidato['segmento_original'] = $key;

                    // Armazenar no array de segmento original por ID
                    $segmento_original_por_id[$candidato['id']] = $key;

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
                usort($candidatos, function ($a, $b) {
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

            // ---------- CÁLCULO DE VAGAS ----------
            $stmtSelect_vagas = $this->connect->prepare(
                "SELECT quantidade_alunos FROM $this->table2 WHERE id = :id_curso"
            );
            $stmtSelect_vagas->bindValue(':id_curso', $curso_id);
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

            // ---------- REDISTRIBUIÇÃO DE VAGAS ----------
            $vagas_ocupadas = [];
            $ids_classificados = [];

            // Criar lista unificada de PCDs (públicos + privados) mantendo o segmento original
            $total_pcd = [];
            foreach ($todos_candidatos['pcd_publica'] as $candidato) {
                $candidato['segmento_original'] = 'pcd_publica';
                $total_pcd[] = $candidato;
            }
            foreach ($todos_candidatos['pcd_privada'] as $candidato) {
                $candidato['segmento_original'] = 'pcd_privada';
                $total_pcd[] = $candidato;
            }

            usort($total_pcd, fn($a, $b) => $b['media_final'] <=> $a['media_final']);
            $vagas_ocupadas['pcd'] = array_slice($total_pcd, 0, $vagas_pcd);
            foreach ($vagas_ocupadas['pcd'] as $cand) $ids_classificados[] = $cand['id'];

            // Identificar PCDs que ficaram na lista de espera e adicioná-los às listas AC correspondentes
            $ids_pcd_classificados = array_column($vagas_ocupadas['pcd'], 'id');

            // PCDs públicos da lista de espera vão para pública AC
            $pcd_publica_lista_espera = array_filter($todos_candidatos['pcd_publica'], function ($cand) use ($ids_pcd_classificados) {
                return !in_array($cand['id'], $ids_pcd_classificados);
            });

            // PCDs privados da lista de espera vão para privada AC
            $pcd_privada_lista_espera = array_filter($todos_candidatos['pcd_privada'], function ($cand) use ($ids_pcd_classificados) {
                return !in_array($cand['id'], $ids_pcd_classificados);
            });

            // Adicionar PCDs públicos da lista de espera à lista de pública AC (MANTENDO SEGMENTO ORIGINAL)
            if (!empty($pcd_publica_lista_espera)) {
                foreach ($pcd_publica_lista_espera as $candidato) {
                    // Manter o segmento original como 'pcd_publica'
                    $candidato['segmento_original'] = 'pcd_publica';
                    $todos_candidatos['publica_ac'][] = $candidato;
                }
                // Reordenar a lista de pública AC mantendo a ordem por média final
                usort($todos_candidatos['publica_ac'], function ($a, $b) {
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
            }

            // Adicionar PCDs privados da lista de espera à lista de privada AC (MANTENDO SEGMENTO ORIGINAL)
            if (!empty($pcd_privada_lista_espera)) {
                foreach ($pcd_privada_lista_espera as $candidato) {
                    // Manter o segmento original como 'pcd_privada'
                    $candidato['segmento_original'] = 'pcd_privada';
                    $todos_candidatos['privada_ac'][] = $candidato;
                }
                // Reordenar a lista de privada AC mantendo a ordem por média final
                usort($todos_candidatos['privada_ac'], function ($a, $b) {
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
            }

            $vagas_ocupadas['publica_cotas'] = array_slice($todos_candidatos['publica_cotas'], 0, $publica_cotas);
            $ids_publica_cotas_classificados = array_column($vagas_ocupadas['publica_cotas'], 'id');
            foreach ($vagas_ocupadas['publica_cotas'] as $cand) $ids_classificados[] = $cand['id'];

            // Identificar cotistas públicos da lista de espera e adicioná-los à lista de pública AC
            $cotistas_publica_lista_espera = array_filter($todos_candidatos['publica_cotas'], function ($cand) use ($ids_publica_cotas_classificados) {
                return !in_array($cand['id'], $ids_publica_cotas_classificados);
            });

            // Adicionar cotistas públicos da lista de espera à lista de pública AC (MANTENDO SEGMENTO ORIGINAL)
            if (!empty($cotistas_publica_lista_espera)) {
                foreach ($cotistas_publica_lista_espera as $candidato) {
                    // Manter o segmento original como 'publica_cotas'
                    $candidato['segmento_original'] = 'publica_cotas';
                    $todos_candidatos['publica_ac'][] = $candidato;
                }
                // Reordenar a lista de pública AC mantendo a ordem por média final
                usort($todos_candidatos['publica_ac'], function ($a, $b) {
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
            }

            $vagas_ocupadas['privada_cotas'] = array_slice($todos_candidatos['privada_cotas'], 0, $privada_cotas);
            $ids_privada_cotas_classificados = array_column($vagas_ocupadas['privada_cotas'], 'id');
            foreach ($vagas_ocupadas['privada_cotas'] as $cand) $ids_classificados[] = $cand['id'];

            // Identificar cotistas privados da lista de espera e adicioná-los à lista de privada AC
            $cotistas_privada_lista_espera = array_filter($todos_candidatos['privada_cotas'], function ($cand) use ($ids_privada_cotas_classificados) {
                return !in_array($cand['id'], $ids_privada_cotas_classificados);
            });

            // Adicionar cotistas privados da lista de espera à lista de privada AC (MANTENDO SEGMENTO ORIGINAL)
            if (!empty($cotistas_privada_lista_espera)) {
                foreach ($cotistas_privada_lista_espera as $candidato) {
                    // Manter o segmento original como 'privada_cotas'
                    $candidato['segmento_original'] = 'privada_cotas';
                    $todos_candidatos['privada_ac'][] = $candidato;
                }
                // Reordenar a lista de privada AC mantendo a ordem por média final
                usort($todos_candidatos['privada_ac'], function ($a, $b) {
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
            }

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

            // IDs classificados por segmento para verificação de status
            $ids_pcd_classificados_segmento = array_column($vagas_ocupadas['pcd'], 'id');
            $ids_publica_cotas_classificados_segmento = array_column($vagas_ocupadas['publica_cotas'], 'id');
            $ids_privada_cotas_classificados_segmento = array_column($vagas_ocupadas['privada_cotas'], 'id');
            $ids_publica_ac_classificados_segmento = array_column($vagas_ocupadas['publica_ac'], 'id');
            $ids_privada_ac_classificados_segmento = array_column($vagas_ocupadas['privada_ac'], 'id');

            $segmentos = [
                ['titulo' => 'Cota - PCD',               'dados' => $total_pcd, 'ids_classificados' => $ids_pcd_classificados_segmento],
                ['titulo' => 'Rede Publica - AC',        'dados' => $todos_candidatos['publica_ac'], 'ids_classificados' => $ids_publica_ac_classificados_segmento],
                ['titulo' => 'Rede Publica - Cota Bairro', 'dados' => $todos_candidatos['publica_cotas'], 'ids_classificados' => $ids_publica_cotas_classificados_segmento],
                ['titulo' => 'Rede Privada - AC',        'dados' => $todos_candidatos['privada_ac'], 'ids_classificados' => $ids_privada_ac_classificados_segmento],
                ['titulo' => 'Rede Privada - Cota Bairro', 'dados' => $todos_candidatos['privada_cotas'], 'ids_classificados' => $ids_privada_cotas_classificados_segmento]
            ];

            // ---------- IMPRESSÃO DOS SEGMENTOS DO CURSO ----------
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
                $pdf->Cell($celula_cl, $altura_celula + 1, 'CH', 1, 0, 'C', true);
                $pdf->Cell($celula_nome, $altura_celula + 1, 'NOME', 1, 0, 'C', true);
                $pdf->Cell($celula_curso, $altura_celula + 1, 'CURSO', 1, 0, 'C', true);
                $pdf->Cell($celula_segmento, $altura_celula + 1, mb_convert_encoding('SEGM.', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
                $pdf->Cell($celula_origem, $altura_celula + 1, 'ORIGEM', 1, 0, 'C', true);
                $pdf->Cell($celula_media, $altura_celula + 1, mb_convert_encoding('MÉDIA', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
                $pdf->Cell($celula_status, $altura_celula + 1, 'STATUS', 1, 1, 'C', true);

                // Linhas de dados - Zebrado cinza claro / branco
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFont('Arial', '', 8);
                $class = 1;

                foreach ($seg['dados'] as $row) {
                    // Determinar SEGM. baseado no SEGMENTO ORIGINAL (não no segmento atual)
                    $segmento = '';
                    $origem = '';

                    // Verificar o segmento original do candidato
                    $segmento_original = $row['segmento_original'] ?? '';

                    if ($segmento_original == 'pcd_publica' || $segmento_original == 'pcd_privada') {
                        $segmento = 'PCD';
                    } elseif ($segmento_original == 'publica_ac' || $segmento_original == 'privada_ac') {
                        $segmento = 'AC';
                    } elseif ($segmento_original == 'publica_cotas' || $segmento_original == 'privada_cotas') {
                        $segmento = 'COTISTA';
                    }

                    // Determinar ORIGEM baseado no segmento original
                    if ($segmento_original == 'pcd_publica' || $segmento_original == 'publica_ac' || $segmento_original == 'publica_cotas') {
                        $origem = 'PUBLICA';
                    } elseif ($segmento_original == 'pcd_privada' || $segmento_original == 'privada_ac' || $segmento_original == 'privada_cotas') {
                        $origem = 'PRIVADA';
                    }

                    // Verificar se está classificado no próprio segmento
                    $isClassificado = in_array($row['id'], $seg['ids_classificados']);
                    $situacao = $isClassificado ? 'CLASSIFICADO' : 'LISTA DE ESPERA';

                    // Zebrado: linha par = cinza claro, ímpar = branco
                    if ($class % 2 == 0) {
                        $pdf->SetFillColor(240, 240, 240); // cinza claro
                    } else {
                        $pdf->SetFillColor(255, 255, 255); // branco
                    }

                    $pdf->Cell($celula_cl, $altura_celula, sprintf('%03d', $class), 1, 0, 'C', true);
                    $pdf->Cell($celula_nome, $altura_celula, mb_convert_encoding(mb_strtoupper($row['nome']), 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
                    $pdf->Cell($celula_curso, $altura_celula, mb_convert_encoding(mb_strtoupper($curso_nome), 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
                    $pdf->Cell($celula_segmento, $altura_celula, mb_convert_encoding($segmento, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
                    $pdf->Cell($celula_origem, $altura_celula, mb_convert_encoding($origem, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
                    $pdf->Cell($celula_media, $altura_celula, number_format($row['media_final'], 5), 1, 0, 'C', true);
                    $pdf->Cell($celula_status, $altura_celula, mb_convert_encoding($situacao, 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', true);

                    $class++;

                    // Verificar se precisa adicionar nova página
                    if ($pdf->GetY() > 250) {
                        $pdf->AddPage();
                        // Recriar cabeçalho básico na nova página
                        $this->recriarCabecalhoPagina($pdf, $logo_escola);
                    }
                }
                $pdf->Ln(5);
            }

            // Adicionar separador entre cursos (exceto no último curso)
            if ($curso !== end($cursos)) {
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetFillColor(255, 174, 25);
                $pdf->SetTextColor(255, 174, 25);
                $pdf->Cell(188, 1, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C', true);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Ln(5);

                // Verificar se precisa adicionar nova página antes do próximo curso
                if ($pdf->GetY() > 250) {
                    $pdf->AddPage();
                    // Recriar cabeçalho básico na nova página
                    $this->recriarCabecalhoPagina($pdf, $logo_escola);
                }
            }
        }

        // ---------- ADICIONAR SEÇÃO DE DEFERIMENTOS NO FINAL ----------
        $this->criarSecaoDeferimentos($pdf);

        $pdf->Output('I', 'resultado_final.pdf');
    }

    /**
     * Recria o cabeçalho básico em novas páginas
     */
    private function recriarCabecalhoPagina($pdf, $logo_escola)
    {
        // Recriar cabeçalho básico na nova página
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->SetY(2);
        $pdf->SetX(9);
        $pdf->Cell(0, 6, mb_convert_encoding($_SESSION['nome_escola'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetY(8);
        $pdf->SetX(9);
        $pdf->Cell(0, 8, mb_convert_encoding('RESULTADO FINAL', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');

        if ($logo_escola) {
            $pdf->Image($logo_escola, 170, 3, 22, 0, '', '');
        }

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

        $pdf->SetX(8.5);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(255, 174, 25);
        $pdf->Cell(35, 5, mb_convert_encoding('BAIRROS DA COTA:', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
        $pdf->SetTextColor(0, 90, 36);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(39);
        $pdf->Cell(0, 5, mb_convert_encoding($this->bairros_texto_pdf, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');

        $pdf->SetDrawColor(0, 90, 36);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(10, $pdf->GetY() + 3, 197.55, $pdf->GetY() + 3);
        $pdf->SetLineWidth(0.2);
        $pdf->Ln(8);

        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLeftMargin(10);
        $pdf->SetTextColor(0, 0, 0);
    }
}

// Chamar o relatório final
$relatorios = new relatorios($escola);
$relatorios->gerarRelatorioFinal();
