<?php
require_once __DIR__ . "/../../../models/sessions.php";
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once __DIR__ . "/../../../config/connect.php";
$escola = $_SESSION["escola"];

new connect($escola);
require_once __DIR__ . "/../../../assets/libs/fpdf/fpdf.php";

class PDF extends FPDF
{
    function AddPage($orientation = "", $size = "", $rotation = 0)
    {
        parent::AddPage($orientation, $size, $rotation);
        $this->Image(
            "../../../assets/imgs/fundo5_pdf.png",
            0,
            0,
            $this->GetPageWidth(),
            $this->GetPageHeight(),
            "png",
        );
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

    // dados que serão impressos depois do segmento PCD
    public $data_hora_pdf;
    public $bairros_texto_pdf;

    function __construct($escola)
    {
        parent::__construct($escola);
        $table = require __DIR__ . "/../../../../../.env/tables.php";
        $this->table1 = $table["ss_$escola"][1];
        $this->table2 = $table["ss_$escola"][2];
        $this->table3 = $table["ss_$escola"][3];
        $this->table4 = $table["ss_$escola"][4];
        $this->table5 = $table["ss_$escola"][5];
        $this->table13 = $table["ss_$escola"][13];
    }

    public function gerarRelatorio($curso, $tipo_relatorio = "TODOS")
    {
        /* ---------- CONFIGURAÇÕES DE LAYOUT ---------- */
        if (
            isset($_SESSION["tipo_usuario"]) &&
            $_SESSION["tipo_usuario"] === "admin"
        ) {
            $celula_cl = 10;
            $celula_nome = 93;
            $celula_curso = 30;
            $celula_origem = 20;
            $celula_segmento = 20;
            $celula_media = 15;
            $altura_celula = 5;
            $p = 0;
            $orientacao = "P";
        } elseif (
            isset($_SESSION["tipo_usuario"]) &&
            $_SESSION["tipo_usuario"] === "cadastrador"
        ) {
            $celula_cl = 10;
            $celula_nome = 93;
            $celula_curso = 30;
            $celula_origem = 20;
            $celula_segmento = 20;
            $celula_media = 15;
            $altura_celula = 5;
            $p = 1;
            $orientacao = "P";
        }

        /* ---------- CÁLCULO DE VAGAS ---------- */
        $stmtSelect_vagas = $this->connect->prepare(
            "SELECT quantidade_alunos FROM $this->table2 WHERE id = :id_curso",
        );
        $stmtSelect_vagas->bindValue(":id_curso", $curso);
        $stmtSelect_vagas->execute();
        $vagas_curso = $stmtSelect_vagas->fetch(PDO::FETCH_ASSOC);
        $total_vagas = $vagas_curso["quantidade_alunos"];

        $vagas_pcd = 2;
        $vagas_restantes = $total_vagas - $vagas_pcd;

        $total_publica = round($vagas_restantes * 0.8);
        $total_privada = round($vagas_restantes * 0.2);

        $publica_cotas = round($total_publica * 0.3);
        $privada_cotas = round($total_privada * 0.3);

        $publica_ac = round($total_publica * 0.7);
        $privada_ac = round($total_privada * 0.7);

        /* ---------- CONSULTAS POR SEGMENTO ---------- */
        $classificados = [];

        // PÚBLICA - AC
        $stmt = $this->connect->prepare(
            "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
             FROM $this->table1 can
             INNER JOIN $this->table4 m ON m.id_candidato = can.id
             INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id
             WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.pcd = 0 AND can.bairro = 0 AND status = 1
             ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC",
        );
        $stmt->bindValue(":curso", $curso);
        $stmt->execute();
        $classificados["publica_ac"] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // PÚBLICA - COTA
        $stmt = $this->connect->prepare(
            "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
             FROM $this->table1 can
             INNER JOIN $this->table4 m ON m.id_candidato = can.id
             INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id
             WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.pcd = 0 AND can.bairro = 1 AND status = 1
             ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC",
        );
        $stmt->bindValue(":curso", $curso);
        $stmt->execute();
        $classificados["publica_cotas"] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // PCD - PÚBLICA
        $stmt = $this->connect->prepare(
            "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
             FROM $this->table1 can
             INNER JOIN $this->table4 m ON m.id_candidato = can.id
             INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id
             WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.pcd = 1 AND can.bairro = 0 AND status = 1
             ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC",
        );
        $stmt->bindValue(":curso", $curso);
        $stmt->execute();
        $classificados["pcd_publica"] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // PRIVADA - AC
        $stmt = $this->connect->prepare(
            "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
             FROM $this->table1 can
             INNER JOIN $this->table4 m ON m.id_candidato = can.id
             INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id
             WHERE can.id_curso1 = :curso AND can.publica = 0 AND can.pcd = 0 AND can.bairro = 0 AND status = 1
             ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC",
        );
        $stmt->bindValue(":curso", $curso);
        $stmt->execute();
        $classificados["privada_ac"] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // PRIVADA - COTA
        $stmt = $this->connect->prepare(
            "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
             FROM $this->table1 can
             INNER JOIN $this->table4 m ON m.id_candidato = can.id
             INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id
             WHERE can.id_curso1 = :curso AND can.publica = 0 AND can.pcd = 0 AND can.bairro = 1 AND status = 1
             ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC",
        );
        $stmt->bindValue(":curso", $curso);
        $stmt->execute();
        $classificados["privada_cotas"] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // PCD - PRIVADA
        $stmt = $this->connect->prepare(
            "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
             FROM $this->table1 can
             INNER JOIN $this->table4 m ON m.id_candidato = can.id
             INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id
             WHERE can.id_curso1 = :curso AND can.publica = 0 AND can.pcd = 1 AND can.bairro = 0 AND status = 1
             ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC",
        );
        $stmt->bindValue(":curso", $curso);
        $stmt->execute();
        $classificados["pcd_privada"] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /* ---------- REDISTRIBUIÇÃO DE VAGAS ---------- */
        $vagas_ocupadas = [];

        // PCD (2 vagas)
        $total_pcd = array_merge(
            $classificados["pcd_publica"],
            $classificados["pcd_privada"],
        );
        usort(
            $total_pcd,
            fn($a, $b) => $b["media_final"] <=> $a["media_final"],
        );
        $vagas_ocupadas["pcd"] = array_slice($total_pcd, 0, $vagas_pcd);
        $vagas_sobra_pcd = $vagas_pcd - count($vagas_ocupadas["pcd"]);

        // COTAS PÚBLICA
        $vagas_ocupadas["publica_cotas"] = array_slice(
            $classificados["publica_cotas"],
            0,
            $publica_cotas,
        );
        $vagas_sobra_publica_cotas =
            $publica_cotas - count($vagas_ocupadas["publica_cotas"]);

        // COTAS PRIVADA
        $vagas_ocupadas["privada_cotas"] = array_slice(
            $classificados["privada_cotas"],
            0,
            $privada_cotas,
        );
        $vagas_sobra_privada_cotas =
            $privada_cotas - count($vagas_ocupadas["privada_cotas"]);

        // AC PÚBLICA (sobra PCD + sobra cota pública)
        $limite_publica_ac =
            $publica_ac + $vagas_sobra_publica_cotas + $vagas_sobra_pcd;
        $vagas_ocupadas["publica_ac"] = array_slice(
            $classificados["publica_ac"],
            0,
            round($limite_publica_ac),
        );

        // AC PRIVADA (sobra cota privada)
        $limite_privada_ac = $privada_ac + $vagas_sobra_privada_cotas;
        $vagas_ocupadas["privada_ac"] = array_slice(
            $classificados["privada_ac"],
            0,
            round($limite_privada_ac),
        );

        /* ---------- INÍCIO DO PDF ---------- */
        $pdf = new PDF($orientacao, "mm", "A4");
        $pdf->AddPage();

        date_default_timezone_set("America/Fortaleza");
        $pdf->SetFont("Arial", "B", 13);
        $pdf->SetY(3);
        $pdf->Cell(4, 4, $_SESSION["nome_escola"], 0, 0, "L");

        // TÍTULO PRINCIPAL
        $pdf->SetFont("Arial", "B", 20);
        $pdf->SetY(8);
        $pdf->SetX(8.5);
        $pdf->Cell(
            22,
            8,
            mb_convert_encoding("CLASSIFICADOS", "ISO-8859-1", "UTF-8"),
            0,
            1,
            "L",
        );

        // LEGENDAS (PCD | COTISTA | AC) – posição onde antes ficavam os bairros
        $pdf->SetY(20);
        $pdf->SetX(8.5);
        $pdf->SetFont("Arial", "B", 8);
        $pdf->SetTextColor(255, 174, 25);
        $pdf->Cell(
            20,
            6,
            mb_convert_encoding("PCD:", "ISO-8859-1", "UTF-8"),
            0,
            0,
            "L",
        );
        $pdf->SetTextColor(0, 90, 36);
        $pdf->SetFont("Arial", "", 8);
        $pdf->SetX(16);
        $pdf->Cell(
            70,
            6,
            mb_convert_encoding(
                "PESSOA COM DEFICIÊNCIA  |",
                "ISO-8859-1",
                "UTF-8",
            ),
            0,
            0,
            "L",
        );

        $pdf->SetX(58);
        $pdf->SetFont("Arial", "B", 8);
        $pdf->SetTextColor(255, 174, 25);
        $pdf->Cell(
            25,
            6,
            mb_convert_encoding("COTISTA:", "ISO-8859-1", "UTF-8"),
            0,
            0,
            "L",
        );
        $pdf->SetTextColor(0, 90, 36);
        $pdf->SetFont("Arial", "", 8);
        $pdf->SetX(72);
        $pdf->Cell(
            70,
            6,
            mb_convert_encoding("COTA DO BAIRRO  |", "ISO-8859-1", "UTF-8"),
            0,
            0,
            "L",
        );

        $pdf->SetX(101);
        $pdf->SetFont("Arial", "B", 8);
        $pdf->SetTextColor(255, 174, 25);
        $pdf->Cell(
            15,
            6,
            mb_convert_encoding("AC:", "ISO-8859-1", "UTF-8"),
            0,
            0,
            "L",
        );
        $pdf->SetTextColor(0, 90, 36);
        $pdf->SetFont("Arial", "", 8);
        $pdf->SetX(107);
        $pdf->Cell(
            0,
            6,
            mb_convert_encoding("AMPLA CONCORRÊNCIA", "ISO-8859-1", "UTF-8"),
            0,
            1,
            "L",
        );

        $pdf->SetLeftMargin(10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(185, 10, "", 0, 1, "C");

        // PREPARA DATA/HORA E BAIRROS (serão impressos depois do segmento PCD)
        $stmt_bairros = $this->connect->query("SELECT * FROM $this->table13");
        $dados_bairros = $stmt_bairros->fetchAll(PDO::FETCH_ASSOC);
        $bairros_para_mostrar = array_slice($dados_bairros, 0, 5);
        $bairros_texto = "";
        foreach ($bairros_para_mostrar as $i => $b) {
            $bairro = strtoupper(
                mb_convert_encoding($b["bairros"], "ISO-8859-1", "UTF-8"),
            );
            $bairros_texto .=
                $i < count($bairros_para_mostrar) - 1
                    ? $bairro . " | "
                    : $bairro;
        }

        $this->bairros_texto_pdf = $bairros_texto;

        /* ---------- IMPRESSÃO DOS SEGMENTOS ---------- */
        $segmentos = [
            [
                "titulo" => "PÚBLICA - AC",
                "dados" => $vagas_ocupadas["publica_ac"],
            ],
            [
                "titulo" => "PÚBLICA - COTA",
                "dados" => $vagas_ocupadas["publica_cotas"],
            ],
            ["titulo" => "PCD", "dados" => $vagas_ocupadas["pcd"]],
            [
                "titulo" => "PRIVADA - AC",
                "dados" => $vagas_ocupadas["privada_ac"],
            ],
            [
                "titulo" => "PRIVADA - COTA",
                "dados" => $vagas_ocupadas["privada_cotas"],
            ],
        ];

        $primeira_pagina = true;
        $bairros_exibidos = false;
        foreach ($segmentos as $seg) {
            $titulo = $seg["titulo"];
            $dados = $seg["dados"];
            if (empty($dados)) {
                continue;
            }

            // Verificar se precisa exibir bairros antes de criar nova página
            $y_atual = $pdf->GetY();
            $linhas = count($dados) + 2;
            $espaco = $linhas * $altura_celula + 10;

            // Se está na primeira página e próximo ao final, exibir bairros antes de criar nova página
            if (
                $primeira_pagina &&
                $y_atual + $espaco > 250 &&
                !$bairros_exibidos
            ) {
                // Exibir bairros no final da primeira página
                $pdf->SetY(260);
                $pdf->SetX(8.5);

                // Data/Hora à direita
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFont("Arial", "", 8);
                $pdf->Cell(0, 5, $this->data_hora_pdf, 0, 1, "R");

                // Bairros
                $pdf->SetFont("Arial", "B", 8);
                $pdf->SetTextColor(255, 174, 25);
                $pdf->SetX(8.5);
                $pdf->Cell(
                    35,
                    5,
                    mb_convert_encoding(
                        "BAIRROS DA COTA:",
                        "ISO-8859-1",
                        "UTF-8",
                    ),
                    0,
                    0,
                    "L",
                );
                $pdf->SetTextColor(0, 90, 36);
                $pdf->SetFont("Arial", "", 8);
                $pdf->SetX(37);
                $pdf->Cell(0, 5, $this->bairros_texto_pdf, 0, 1, "L");
                $bairros_exibidos = true;

                // Criar nova página após exibir bairros
                $pdf->AddPage();
                $primeira_pagina = false;
            } elseif ($espaco > $pdf->GetPageHeight() - $y_atual - 10) {
                $pdf->AddPage();
                $primeira_pagina = false;
                $pdf->SetY(10);
            }

            // Título do segmento
            $pdf->SetFont("Arial", "B", 12);
            $pdf->SetFillColor(0, 90, 36);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(
                188,
                5,
                mb_convert_encoding($titulo, "ISO-8859-1", "UTF-8"),
                1,
                1,
                "C",
                true,
            );

            // Cabeçalho da tabela
            $pdf->SetFont("Arial", "B", 10);
            $pdf->SetFillColor(0, 90, 36);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell($celula_cl, $altura_celula, "CL", 1, 0, "C", true);
            $pdf->Cell($celula_nome, $altura_celula, "NOME", 1, 0, "C", true);
            $pdf->Cell($celula_curso, $altura_celula, "CURSO", 1, 0, "C", true);
            $pdf->Cell(
                $celula_origem,
                $altura_celula,
                "SEGM.",
                1,
                0,
                "C",
                true,
            );
            $pdf->Cell(
                $celula_segmento,
                $altura_celula,
                "ORIGEM",
                1,
                0,
                "C",
                true,
            );
            $pdf->Cell($celula_media, $altura_celula, "MEDIA", 1, 1, "C", true);

            // Linhas de dados
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont("Arial", "", 8);
            $class = 1;
            foreach ($dados as $row) {
                $origem = $row["publica"]
                    ? mb_convert_encoding("PÚBLICA", "ISO-8859-1", "UTF-8")
                    : mb_convert_encoding("PRIVADA", "ISO-8859-1", "UTF-8");

                // operador ternário com parênteses (linha 317)
                $cota = $row["pcd"]
                    ? mb_convert_encoding("PCD", "ISO-8859-1", "UTF-8")
                    : ($row["bairro"]
                        ? mb_convert_encoding("COTISTA", "ISO-8859-1", "UTF-8")
                        : mb_convert_encoding("AC", "ISO-8859-1", "UTF-8"));

                $pdf->SetFillColor(
                    $class % 2 ? 255 : 192,
                    $class % 2 ? 255 : 192,
                    $class % 2 ? 255 : 192,
                );

                $pdf->Cell(
                    $celula_cl,
                    $altura_celula,
                    sprintf("%03d", $class),
                    1,
                    0,
                    "C",
                    true,
                );
                $pdf->Cell(
                    $celula_nome,
                    $altura_celula,
                    mb_convert_encoding(
                        mb_strtoupper($row["nome"], "UTF-8"),
                        "ISO-8859-1",
                        "UTF-8",
                    ),
                    1,
                    0,
                    "L",
                    true,
                );
                $pdf->Cell(
                    $celula_curso,
                    $altura_celula,
                    mb_convert_encoding(
                        mb_strtoupper($row["nome_curso"], "UTF-8"),
                        "ISO-8859-1",
                        "UTF-8",
                    ),
                    1,
                    0,
                    "L",
                    true,
                );
                $pdf->Cell(
                    $celula_origem,
                    $altura_celula,
                    $cota,
                    1,
                    0,
                    "C",
                    true,
                );
                $pdf->Cell(
                    $celula_segmento,
                    $altura_celula,
                    $origem,
                    1,
                    0,
                    "L",
                    true,
                );
                $pdf->Cell(
                    $celula_media,
                    $altura_celula,
                    number_format($row["media_final"], 2),
                    1,
                    1,
                    "C",
                    true,
                );
                $class++;
            }

            $pdf->Ln(10);
        }

        // Se ainda estamos na primeira página e não exibimos os bairros, exibir no final
        if ($primeira_pagina && !$bairros_exibidos) {
            $y_atual = $pdf->GetY();
            if ($y_atual < 260) {
                $pdf->SetY(260);
            } else {
                $pdf->SetY($y_atual + 5);
            }
            $pdf->SetX(8.5);

            // Data/Hora à direita
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont("Arial", "", 8);
            $pdf->Cell(0, 5, $this->data_hora_pdf, 0, 1, "R");

            // Bairros
            $pdf->SetFont("Arial", "B", 8);
            $pdf->SetTextColor(255, 174, 25);
            $pdf->SetX(8.5);
            $pdf->Cell(
                35,
                5,
                mb_convert_encoding("BAIRROS DA COTA:", "ISO-8859-1", "UTF-8"),
                0,
                0,
                "L",
            );
            $pdf->SetTextColor(0, 90, 36);
            $pdf->SetFont("Arial", "", 8);
            $pdf->SetX(37);
            $pdf->Cell(0, 5, $this->bairros_texto_pdf, 0, 1, "L");
        }

        $pdf->Output("classificados.pdf", "I");
    }
}

if (isset($_GET["curso"]) && !empty($_GET["curso"])) {
    $relatorios = new relatorios($escola);
    $curso = $_GET["curso"];
    $tipo_relatorio = $_GET["tipo_relatorio"] ?? "TODOS";
    $relatorios->gerarRelatorio($curso, $tipo_relatorio);
} else {
    header("Location: ../../../index.php");
    exit();
}
