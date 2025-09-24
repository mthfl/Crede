<?php
require_once(__DIR__ . '/../../../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../../../config/connect.php');
$escola = $_SESSION['escola'];

new connect($escola);
require_once(__DIR__ . '/../../../assets/libs/fpdf/fpdf.php');

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
        $pdf = new FPDF();
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
        $pdf->Cell(185, 10, utf8_decode('RESULTADO FINAL'), 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(185, 5, utf8_decode(" - " . $curso_nome['nome_curso'] . " - "), 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 10, ('PCD = PESSOA COM DEFICIENCIA | COTISTA = INCLUSO NA COTA DO BAIRRO | AC = AMPLA CONCORRENCIA'), 0, 1, 'C');
        $pdf->SetFont('Arial', 'b', 12);
        $pdf->Cell(185, 10, '', 0, 1, 'C');

        //PUBLICA - AC
        $stmtSelect_ac_publica = $this->connect->prepare(
            "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
        FROM $this->table1 can    
        INNER JOIN $this->table4 m ON m.id_candidato = can.id 
        INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
        WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.pcd = 0 AND can.bairro = 0 
        ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC LIMIT 24 
        "
        );
        $stmtSelect_ac_publica->bindValue(':curso', $curso);
        $stmtSelect_ac_publica->execute();
        $result = $stmtSelect_ac_publica->fetchAll(PDO::FETCH_ASSOC);
        // Fonte do cabeçalho
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(93, 164, 67); //fundo verde
        $pdf->SetTextColor(255, 255, 255);  //texto branco
        $pdf->Cell(191, -8, "Rede Publica - AC", 1, 0, 'C', true);
        $pdf->Ln(0);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(93, 164, 67); //fundo verde
        $pdf->SetTextColor(255, 255, 255);  //texto branco
        $pdf->Cell(10, 7, 'CH', 1, 0, 'C', true);
        $pdf->Cell(90, 7, 'Nome', 1, 0, 'C', true);
        $pdf->Cell(32, 7, 'Curso', 1, 0, 'C', true);
        $pdf->Cell(18, 7, 'Origem', 1, 0, 'C', true);
        $pdf->Cell(26, 7, 'Segmento', 1, 0, 'C', true);
        $pdf->Cell(15, 7, 'Media', 1, 1, 'C', true);

        // Resetar cor do texto para preto
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 10);

        // Dados com cores alternadas
        $classificacao = 001;

        foreach ($result as $row) {
            // Definir curso
            // Definir escola
            $escola = ($row['publica'] == 1) ? ('PUBLICA') : ('PRIVADA');

            // Definir cota
            if ($row['pcd'] == 1) {
                $cota = 'PCD';
            } else if ($row['publica'] == 1 && $row['bairro'] == 1) {
                $cota = 'COSTISTA';
            } else {
                $cota = 'AC';
            }

            // Definir cor da linha
            $cor = $classificacao % 2 ? 255 : 192;
            $pdf->SetFillColor($cor, $cor, $cor);

            // Imprimir linha no PDF
            $pdf->Cell(10, 7, sprintf("%03d", $classificacao), 1, 0, 'C', true);
            $pdf->Cell(90, 7, strToUpper(utf8_decode($row['nome'])), 1, 0, 'L', true);
            $pdf->Cell(32, 7, utf8_decode($row['nome_curso']), 1, 0, 'L', true);
            $pdf->Cell(18, 7, $escola, 1, 0, 'L', true);
            $pdf->Cell(26, 7, $cota, 1, 0, 'L', true);
            $pdf->Cell(15, 7, number_format($row['media_final'], 2), 1, 1, 'C', true);

            $classificacao++;
        }
        $pdf->Ln(20);

        //PUBLICA - COTA
        $stmtSelect_bairro_publica = $this->connect->prepare(
            "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
        FROM $this->table1 can    
        INNER JOIN $this->table4 m ON m.id_candidato = can.id 
        INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
        WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.pcd = 0 AND can.bairro = 1 
        ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC LIMIT 10 
        ;
    "
        );
        $stmtSelect_bairro_publica->bindValue(':curso', $curso);
        $stmtSelect_bairro_publica->execute();
        $result = $stmtSelect_bairro_publica->fetchAll(PDO::FETCH_ASSOC);
        // Fonte do cabeçalho
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(93, 164, 67); //fundo verde
        $pdf->SetTextColor(255, 255, 255);  //texto branco
        $pdf->Cell(191, -8, "Rede Publica - Cota", 1, 0, 'C', true);
        $pdf->Ln(0);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(93, 164, 67); //fundo verde
        $pdf->SetTextColor(255, 255, 255);  //texto branco
        $pdf->Cell(10, 7, 'CH', 1, 0, 'C', true);
        $pdf->Cell(90, 7, 'Nome', 1, 0, 'C', true);
        $pdf->Cell(32, 7, 'Curso', 1, 0, 'C', true);
        $pdf->Cell(18, 7, 'Origem', 1, 0, 'C', true);
        $pdf->Cell(26, 7, 'Segmento', 1, 0, 'C', true);
        $pdf->Cell(15, 7, 'Media', 1, 1, 'C', true);

        // Resetar cor do texto para preto
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 10);

        // Dados com cores alternadas
        $classificacao = 001;

        foreach ($result as $row) {
            // Definir curso

            // Definir escola
            $escola = ($row['publica'] == 1) ? ('PUBLICA') : ('PRIVADA');

            // Definir cota
            if ($row['pcd'] == 1) {
                $cota = 'PCD';
            } else if ($row['publica'] == 1 && $row['bairro'] == 1) {
                $cota = 'COSTISTA';
            } else {
                $cota = 'AC';
            }

            // Definir cor da linha
            $cor = $classificacao % 2 ? 255 : 192;
            $pdf->SetFillColor($cor, $cor, $cor);

            // Imprimir linha no PDF
            $pdf->Cell(10, 7, sprintf("%03d", $classificacao), 1, 0, 'C', true);
            $pdf->Cell(90, 7, strToUpper(utf8_decode($row['nome'])), 1, 0, 'L', true);
            $pdf->Cell(32, 7, utf8_decode($row['nome_curso']), 1, 0, 'L', true);
            $pdf->Cell(18, 7, $escola, 1, 0, 'L', true);
            $pdf->Cell(26, 7, $cota, 1, 0, 'L', true);
            $pdf->Cell(15, 7, number_format($row['media_final'], 2), 1, 1, 'C', true);

            $classificacao++;
        }
        $pdf->Ln(20);

        //PCD - COTA
        $stmtSelect_pcd_publica = $this->connect->prepare(
            "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
        FROM $this->table1 can    
        INNER JOIN $this->table4 m ON m.id_candidato = can.id 
        INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
        WHERE can.id_curso1 = :curso AND can.publica = 1 AND can.pcd = 1 AND can.bairro = 0 
        ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC LIMIT 2 
    ");
        $stmtSelect_pcd_publica->bindValue(':curso', $curso);
        $stmtSelect_pcd_publica->execute();
        $result = $stmtSelect_pcd_publica->fetchAll(PDO::FETCH_ASSOC);

        // Fonte do cabeçalho
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(93, 164, 67); //fundo verde
        $pdf->SetTextColor(255, 255, 255);  //texto branco
        $pdf->Cell(191, -8, "PCD", 1, 0, 'C', true);
        $pdf->Ln(0);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(93, 164, 67); //fundo verde
        $pdf->SetTextColor(255, 255, 255);  //texto branco
        $pdf->Cell(10, 7, 'CH', 1, 0, 'C', true);
        $pdf->Cell(90, 7, 'Nome', 1, 0, 'C', true);
        $pdf->Cell(32, 7, 'Curso', 1, 0, 'C', true);
        $pdf->Cell(18, 7, 'Origem', 1, 0, 'C', true);
        $pdf->Cell(26, 7, 'Segmento', 1, 0, 'C', true);
        $pdf->Cell(15, 7, 'Media', 1, 1, 'C', true);

        // Resetar cor do texto para preto
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 10);

        // Dados com cores alternadas
        $classificacao = 001;

        foreach ($result as $row) {
            // Definir curso
            // Definir escola
            $escola = ($row['publica'] == 1) ? ('PUBLICA') : ('PRIVADA');

            // Definir cota
            if ($row['pcd'] == 1) {
                $cota = 'PCD';
            } else if ($row['publica'] == 1 && $row['bairro'] == 1) {
                $cota = 'COSTISTA';
            } else {
                $cota = 'AC';
            }

            // Definir cor da linha
            $cor = $classificacao % 2 ? 255 : 192;
            $pdf->SetFillColor($cor, $cor, $cor);

            // Imprimir linha no PDF
            $pdf->Cell(10, 7, sprintf("%03d", $classificacao), 1, 0, 'C', true);
            $pdf->Cell(90, 7, strToUpper(utf8_decode($row['nome'])), 1, 0, 'L', true);
            $pdf->Cell(32, 7, utf8_decode($row['nome_curso']), 1, 0, 'L', true);
            $pdf->Cell(18, 7, $escola, 1, 0, 'L', true);
            $pdf->Cell(26, 7, $cota, 1, 0, 'L', true);
            $pdf->Cell(15, 7, number_format($row['media_final'], 2), 1, 1, 'C', true);

            $classificacao++;
        }
        $pdf->Ln(20);

        //PRIVADA - AC
        $stmtSelect_ac_privada = $this->connect->prepare(
        "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
        FROM $this->table1 can    
        INNER JOIN $this->table4 m ON m.id_candidato = can.id 
        INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
        WHERE can.id_curso1 = :curso AND can.publica = 0 AND can.pcd = 0 AND can.bairro = 0 
        ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC LIMIT 10 
    ");
        $stmtSelect_ac_privada->bindValue(':curso', $curso);
        $stmtSelect_ac_privada->execute();
        $result = $stmtSelect_ac_privada->fetchAll(PDO::FETCH_ASSOC);

        // Fonte do cabeçalho
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(93, 164, 67); //fundo verde
        $pdf->SetTextColor(255, 255, 255);  //texto branco
        $pdf->Cell(191, -8, "Rede Privada - AC", 1, 0, 'C', true);
        $pdf->Ln(0);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(93, 164, 67); //fundo verde
        $pdf->SetTextColor(255, 255, 255);  //texto branco
        $pdf->Cell(10, 7, 'CH', 1, 0, 'C', true);
        $pdf->Cell(90, 7, 'Nome', 1, 0, 'C', true);
        $pdf->Cell(32, 7, 'Curso', 1, 0, 'C', true);
        $pdf->Cell(18, 7, 'Origem', 1, 0, 'C', true);
        $pdf->Cell(26, 7, 'Segmento', 1, 0, 'C', true);
        $pdf->Cell(15, 7, 'Media', 1, 1, 'C', true);

        // Resetar cor do texto para preto
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 10);

        // Dados com cores alternadas
        $classificacao = 001;

        foreach ($result as $row) {
            // Definir escola
            $escola = ($row['publica'] == 1) ? ('PUBLICA') : ('PRIVADA');

            // Definir cota
            if ($row['pcd'] == 1) {
                $cota = 'PCD';
            } else if ($row['publica'] == 1 && $row['bairro'] == 1) {
                $cota = 'COSTISTA';
            } else {
                $cota = 'AC';
            }

            // Definir cor da linha
            $cor = $classificacao % 2 ? 255 : 192;
            $pdf->SetFillColor($cor, $cor, $cor);

            // Imprimir linha no PDF
            $pdf->Cell(10, 7, sprintf("%03d", $classificacao), 1, 0, 'C', true);
            $pdf->Cell(90, 7, strToUpper(utf8_decode($row['nome'])), 1, 0, 'L', true);
            $pdf->Cell(32, 7, utf8_decode($row['nome_curso']), 1, 0, 'L', true);
            $pdf->Cell(18, 7, $escola, 1, 0, 'L', true);
            $pdf->Cell(26, 7, $cota, 1, 0, 'L', true);
            $pdf->Cell(15, 7, number_format($row['media_final'], 2), 1, 1, 'C', true);

            $classificacao++;
        }
        $pdf->Ln(20);

        //PRIVADA - COTA
        $stmtSelect_bairro_privada = $this->connect->prepare(
    "SELECT can.nome, cur.nome_curso, can.publica, can.bairro, can.pcd, m.media_final
        FROM $this->table1 can    
        INNER JOIN $this->table4 m ON m.id_candidato = can.id 
        INNER JOIN $this->table2 cur ON can.id_curso1 = cur.id 
        WHERE can.id_curso1 = :curso AND can.publica = 0 AND can.pcd = 0 AND can.bairro = 1 
        ORDER BY m.media_final DESC, can.data_nascimento DESC, m.l_portuguesa_media DESC, m.matematica_media DESC LIMIT 6;
    ");
        $stmtSelect_bairro_privada->bindValue(':curso', $curso);
        $stmtSelect_bairro_privada->execute();
        $result = $stmtSelect_bairro_privada->fetchAll(PDO::FETCH_ASSOC);

        // Fonte do cabeçalho
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(93, 164, 67); //fundo verde
        $pdf->SetTextColor(255, 255, 255);  //texto branco
        $pdf->Cell(191, -8, "Rede Privada - Cota", 1, 0, 'C', true);
        $pdf->Ln(0);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(93, 164, 67); //fundo verde
        $pdf->SetTextColor(255, 255, 255);  //texto branco
        $pdf->Cell(10, 7, 'CH', 1, 0, 'C', true);
        $pdf->Cell(90, 7, 'Nome', 1, 0, 'C', true);
        $pdf->Cell(32, 7, 'Curso', 1, 0, 'C', true);
        $pdf->Cell(18, 7, 'Origem', 1, 0, 'C', true);
        $pdf->Cell(26, 7, 'Segmento', 1, 0, 'C', true);
        $pdf->Cell(15, 7, 'Media', 1, 1, 'C', true);

        // Resetar cor do texto para preto
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 10);

        // Dados com cores alternadas
        $classificacao = 001;

        foreach ($result as $row) {
            // Definir curso

            // Definir escola
            $escola = ($row['publica'] == 1) ? ('PUBLICA') : ('PRIVADA');

            // Definir cota
            if ($row['pcd'] == 1) {
                $cota = 'PCD';
            } else if ($row['bairro'] == 1) {
                $cota = 'COSTISTA';
            } else {
                $cota = 'AC';
            }

            // Definir cor da linha
            $cor = $classificacao % 2 ? 255 : 192;
            $pdf->SetFillColor($cor, $cor, $cor);

            // Imprimir linha no PDF
            $pdf->Cell(10, 7, sprintf("%03d", $classificacao), 1, 0, 'C', true);
            $pdf->Cell(90, 7, strToUpper(utf8_decode($row['nome'])), 1, 0, 'L', true);
            $pdf->Cell(32, 7, utf8_decode($row['nome_curso']), 1, 0, 'L', true);
            $pdf->Cell(18, 7, $escola, 1, 0, 'L', true);
            $pdf->Cell(26, 7, $cota, 1, 0, 'L', true);
            $pdf->Cell(15, 7, number_format($row['media_final'], 2), 1, 1, 'C', true);

            $classificacao++;
        }
        $pdf->Ln(20);


        $pdf->Output('classificados.pdf', 'I');
    }
}
if (isset($_GET['curso']) && !empty($_GET['curso'])) {
    $relatorios = new relatorios($escola);
    $curso = $_GET['curso'];
    $relatorios->private_ac($curso);
} /*else {
    header('location:../../../index.php');
    exit();
}*/
