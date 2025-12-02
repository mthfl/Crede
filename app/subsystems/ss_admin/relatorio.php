<?php
session_start();

// Garantir que a escola esteja definida (vinda do dashboard admin)
$escola = $_SESSION['escola'] ?? null;
if (!$escola) {
    header('Location: ./index.php');
    exit();
}

require_once __DIR__ . '/../ss/config/connect.php';
require_once __DIR__ . '/../ss/models/model.select.php';
require_once __DIR__ . '/../ss/assets/libs/fpdf/fpdf.php';
require_once __DIR__ . '/models/Escolas.php';

/**
 * PDF com fundo padrão dos relatórios do SS
 */
class PDFAdminCursos extends FPDF
{
    public function AddPage($orientation = '', $size = '', $rotation = 0)
    {
        parent::AddPage($orientation, $size, $rotation);
        $this->Image(
            '../ss/assets/imgs/fundo5_pdf.png',
            0,
            0,
            $this->GetPageWidth(),
            $this->GetPageHeight(),
            'png'
        );
    }

    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '')
    {
        $txt = mb_convert_encoding($txt, 'ISO-8859-1', 'UTF-8');
        parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
    }

    public function MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false)
    {
        $txt = mb_convert_encoding($txt, 'ISO-8859-1', 'UTF-8');
        parent::MultiCell($w, $h, $txt, $border, $align, $fill);
    }
}

/**
 * Relatório de cursos (admin) usando o mesmo padrão dos relatórios do SS
 * e os mesmos dados exibidos no dashboard admin.
 */
class RelatorioAdminCursos
{
    private select $select;
    private PDFAdminCursos $pdf;

    public function __construct(string $escola)
    {
        $this->select = new select($escola);
        // Modo retrato para o relatório
        $this->pdf = new PDFAdminCursos('P', 'mm', 'A4');
    }

    public function gerar(): void
    {
        date_default_timezone_set('America/Fortaleza');
        $dataHora = date('d/m/Y H:i:s');

        // Tentar obter o nome completo da escola
        $nomeEscola = $_SESSION['nome_escola'] ?? '';
        if ($nomeEscola === '' && !empty($_SESSION['escola'])) {
            $escolasModel  = new Escolas();
            $schoolsConfig = $escolasModel->listarEscolas();
            foreach ($schoolsConfig as $escolaRow) {
                if (($escolaRow['escola_banco'] ?? '') === $_SESSION['escola']) {
                    $nomeEscola = $escolaRow['nome_escola'] ?? '';
                    break;
                }
            }
        }
        if ($nomeEscola === '') {
            $nomeEscola = 'Escola';
        }

        // Dados gerais (mesmo conceito do dashboard)
        $totalAlunos   = $this->select->countTotalAlunos();
        $totalPublicos = $this->select->countTotalPublicos();
        $totalPrivados = $this->select->countTotalPrivados();
        $totalPCDs     = $this->select->countTotalPCDs();

        // Dados por curso (mesmo método usado em AdminDashboard)
        $cotasPorCurso = $this->select->getCotasPorCurso();

        $this->pdf->AddPage();

        // Cabeçalho
        $this->pdf->SetFont('Arial', 'B', 13);
        $this->pdf->SetY(5);
        $this->pdf->SetX(10);
        $this->pdf->Cell(0, 6, $nomeEscola, 0, 1, 'L');

        $this->pdf->SetY(5);
        $this->pdf->SetX(-80);
        $this->pdf->Cell(70, 6, $dataHora, 0, 1, 'R');

        // Título principal
        $this->pdf->SetY(12);
        $this->pdf->SetX(10);
        $this->pdf->SetFont('Arial', 'B', 18);
        $titulo = 'RELATÓRIO';
        $this->pdf->Cell(0, 8, $titulo, 0, 1, 'L');

        // Bloco de estatísticas gerais (equivalente ao "quick stats" do dashboard)
        $this->pdf->Ln(2);
        $this->pdf->SetFont('Arial', 'B', 11);
        $this->pdf->SetTextColor(0, 90, 36); // verde

        $this->pdf->SetX(10);
        $this->pdf->Cell(0, 6, 'RESUMO GERAL DE CANDIDATOS', 0, 1, 'L');

        $this->pdf->Ln(1);
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->SetTextColor(0, 0, 0);

        // Distribuir as estatísticas em 4 colunas iguais (190mm / 4 = 47.5mm cada)
        $this->pdf->SetX(10);
        $this->pdf->Cell(47, 6, "Total de Candidatos: {$totalAlunos}", 0, 0, 'L');
        $this->pdf->Cell(47, 6, "Escola Publica: {$totalPublicos}", 0, 0, 'L');
        $this->pdf->Cell(47, 6, "Escola Privada: {$totalPrivados}", 0, 0, 'L');
        $this->pdf->Cell(49, 6, "PCD's: {$totalPCDs}", 0, 1, 'L');

        // Legenda das colunas de cotas (equivalente ao dashboard)
        $this->pdf->Ln(4);
        $this->pdf->SetFont('Arial', 'B', 9);
        $this->pdf->SetTextColor(255, 174, 25);
        $this->pdf->SetX(10);
        $this->pdf->Cell(0, 5, 'LEGENDA DOS SEGMENTOS:', 0, 1, 'L');

        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetTextColor(0, 90, 36);
        $this->pdf->SetX(10);
        $this->pdf->Cell(0, 5, 'AMPLA PUBLICA | COTA PUBLICA | PCD PUBLICA/PRIVADA | AMPLA PRIVADA | COTA PRIVADA', 0, 1, 'L');

        // Tabela de cursos com distribuição de cotas
        // Largura útil da página A4 em retrato: 210mm - 10mm (esquerda) - 10mm (direita) = 190mm
        $this->pdf->Ln(3);
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->SetTextColor(255, 255, 255);
        $this->pdf->SetFillColor(0, 90, 36); // verde

        $this->pdf->SetX(10);
        $this->pdf->Cell(60, 7, 'CURSO', 1, 0, 'C', true);
        $this->pdf->Cell(18, 7, 'TOTAL', 1, 0, 'C', true);
        $this->pdf->Cell(22, 7, 'AMPLA PUB.', 1, 0, 'C', true);
        $this->pdf->Cell(22, 7, 'COTA PUB.', 1, 0, 'C', true);
        $this->pdf->Cell(22, 7, 'PCD (TOT.)', 1, 0, 'C', true);
        $this->pdf->Cell(22, 7, 'AMPLA PRIV.', 1, 0, 'C', true);
        $this->pdf->Cell(24, 7, 'COTA PRIV.', 1, 1, 'C', true);

        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetTextColor(0, 0, 0);

        if (empty($cotasPorCurso)) {
            $this->pdf->Ln(4);
            $this->pdf->SetX(10);
            $this->pdf->Cell(0, 6, 'Nenhum curso com candidatos encontrado para esta escola.', 0, 1, 'L');
        } else {
            foreach ($cotasPorCurso as $curso) {
                $nomeCurso      = $curso['nome_curso'] ?? 'Curso sem nome';
                $amplaPublica   = (int)($curso['ampla_publica'] ?? 0);
                $cotaPublica    = (int)($curso['cota_publica'] ?? 0);
                $pcdPublica     = (int)($curso['pcd_publica'] ?? 0);
                $amplaPrivada   = (int)($curso['ampla_privada'] ?? 0);
                $cotaPrivada    = (int)($curso['cota_privada'] ?? 0);
                $pcdPrivada     = (int)($curso['pcd_privada'] ?? 0);

                $totalPCD   = $pcdPublica + $pcdPrivada;
                $totalCurso = $amplaPublica + $cotaPublica + $totalPCD + $amplaPrivada + $cotaPrivada;

                $this->pdf->SetX(10);
                $this->pdf->Cell(60, 7, mb_strtoupper($nomeCurso), 1, 0, 'L');
                $this->pdf->Cell(18, 7, (string)$totalCurso, 1, 0, 'C');
                $this->pdf->Cell(22, 7, (string)$amplaPublica, 1, 0, 'C');
                $this->pdf->Cell(22, 7, (string)$cotaPublica, 1, 0, 'C');
                $this->pdf->Cell(22, 7, (string)$totalPCD, 1, 0, 'C');
                $this->pdf->Cell(22, 7, (string)$amplaPrivada, 1, 0, 'C');
                $this->pdf->Cell(24, 7, (string)$cotaPrivada, 1, 1, 'C');
            }
        }

        $this->pdf->Output('I', 'relatorio_cursos_admin.pdf');
    }
}

$relatorio = new RelatorioAdminCursos($escola);
$relatorio->gerar();


