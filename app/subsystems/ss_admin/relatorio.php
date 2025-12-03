<?php
session_start();

// Garantir que a escola esteja definida
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
 * PDF com fundo padrão
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
        $txt = mb_convert_encoding(mb_strtoupper($txt, 'UTF-8'), 'ISO-8859-1', 'UTF-8');
        parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
    }

    public function MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false)
    {
        $txt = mb_convert_encoding(mb_strtoupper($txt, 'UTF-8'), 'ISO-8859-1', 'UTF-8');
        parent::MultiCell($w, $h, $txt, $border, $align, $fill);
    }
}

class RelatorioAdminCursos
{
    private select $select;
    private PDFAdminCursos $pdf;
    private string $table5;
    private $connect;

    public function __construct(string $escola)
    {
        $this->select = new select($escola);
        $this->pdf = new PDFAdminCursos('P', 'mm', 'A4');
        
        $table = require(__DIR__ . '/../../.env/tables.php');
        $this->table5 = $table["ss_$escola"][5]; // usuarios

        $connectTemp = new connect($escola);
        $reflection  = new ReflectionClass($connectTemp);
        $property    = $reflection->getProperty('connect');
        $property->setAccessible(true);
        $this->connect = $property->getValue($connectTemp);
    }
    
    private function getUsuariosAtivos(): array
    {
        try {
            $sql = "SELECT nome_user, email, cpf, tipo_usuario FROM {$this->table5} WHERE status = 1 ORDER BY nome_user";
            $stmt = $this->connect->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function gerar(): void
    {
        date_default_timezone_set('America/Fortaleza');
        $dataHora = date('d/m/Y H:i:s');

        // Nome da escola em caixa alta
        $nomeEscola = mb_strtoupper($_SESSION['nome_escola'] ?? '');
        if ($nomeEscola === '' && !empty($_SESSION['escola'])) {
            $escolasModel  = new Escolas();
            $schoolsConfig = $escolasModel->listarEscolas();
            foreach ($schoolsConfig as $escolaRow) {
                if (($escolaRow['escola_banco'] ?? '') === $_SESSION['escola']) {
                    $nomeEscola = mb_strtoupper($escolaRow['nome_escola'] ?? 'ESCOLA');
                    break;
                }
            }
        }
        $nomeEscola = $nomeEscola !== '' ? $nomeEscola : 'ESCOLA';

        // Dados gerais
        $totalAlunos   = $this->select->countTotalAlunos();
        $totalPublicos = $this->select->countTotalPublicos();
        $totalPrivados = $this->select->countTotalPrivados();
        $totalPCDs     = $this->select->countTotalPCDs();
        $cotasPorCurso = $this->select->getCotasPorCurso();

        $this->pdf->AddPage();

        // Cabeçalho
        $this->pdf->SetFont('Arial', 'B', 13);
        $this->pdf->SetY(3);
        $this->pdf->SetX(9);
        $this->pdf->Cell(0, 6, $nomeEscola, 0, 1, 'L');

        $this->pdf->SetY(5);
        $this->pdf->SetX(-80);
        $this->pdf->Cell(70, 6, $dataHora, 0, 1, 'R');

        // Título
        $this->pdf->SetY(9.5);
        $this->pdf->SetX(8.5);  
        $this->pdf->SetFont('Arial', 'B', 17);
        $this->pdf->Cell(0, 8, 'RELATÓRIO GERAL', 0, 1, 'L');

        // Resumo geral
        $this->pdf->Ln(2);
        $this->pdf->SetFont('Arial', 'B', 11);
        $this->pdf->SetTextColor(0, 90, 36);
        $this->pdf->SetX(10);
        $this->pdf->Cell(0, 6, 'RESUMO GERAL DE CANDIDATOS', 0, 1, 'L');

        $this->pdf->Ln(1);
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetX(10);
        $this->pdf->Cell(60, 6, "TOTAL DE CANDIDATOS: {$totalAlunos}", 0, 0, 'L');
        $this->pdf->Cell(47, 6, "ESCOLA PÚBLICA: {$totalPublicos}", 0, 0, 'L');
        $this->pdf->Cell(47, 6, "ESCOLA PRIVADA: {$totalPrivados}", 0, 0, 'L');
        $this->pdf->Cell(49, 6, "PCD'S: {$totalPCDs}", 0, 1, 'L');

        // Tabela de cursos
        $this->pdf->Ln(3);
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->SetTextColor(255, 255, 255);
        $this->pdf->SetFillColor(0, 90, 36);
        $this->pdf->SetX(10);
        $this->pdf->Cell(60, 7, 'CURSO', 1, 0, 'C', true);
        $this->pdf->Cell(18, 7, 'TOTAL', 1, 0, 'C', true);
        $this->pdf->Cell(23, 7, 'AMPLA PUB.', 1, 0, 'C', true);
        $this->pdf->Cell(23, 7, 'COTA PUB.', 1, 0, 'C', true);
        $this->pdf->Cell(18, 7, 'PCD', 1, 0, 'C', true);
        $this->pdf->Cell(24, 7, 'AMPLA PRIV.', 1, 0, 'C', true);
        $this->pdf->Cell(24, 7, 'COTA PRIV.', 1, 1, 'C', true);

        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetTextColor(0, 0, 0);

        if (empty($cotasPorCurso)) {
            $this->pdf->Ln(4);
            $this->pdf->SetX(10);
            $this->pdf->Cell(0, 6, 'NENHUM CURSO COM CANDIDATOS ENCONTRADO PARA ESTA ESCOLA.', 0, 1, 'L');
        } else {
            foreach ($cotasPorCurso as $curso) {
                $nomeCurso    = $curso['nome_curso'] ?? 'CURSO SEM NOME';
                $amplaPublica = (int)($curso['ampla_publica'] ?? 0);
                $cotaPublica  = (int)($curso['cota_publica'] ?? 0);
                $pcdPublica   = (int)($curso['pcd_publica'] ?? 0);
                $amplaPrivada = (int)($curso['ampla_privada'] ?? 0);
                $cotaPrivada  = (int)($curso['cota_privada'] ?? 0);
                $pcdPrivada   = (int)($curso['pcd_privada'] ?? 0);

                $totalPCD   = $pcdPublica + $pcdPrivada;
                $totalCurso = $amplaPublica + $cotaPublica + $totalPCD + $amplaPrivada + $cotaPrivada;

                $this->pdf->SetX(10);
                $this->pdf->Cell(60, 7, $nomeCurso, 1, 0, 'L');
                $this->pdf->Cell(18, 7, (string)$totalCurso, 1, 0, 'C');
                $this->pdf->Cell(23, 7, (string)$amplaPublica, 1, 0, 'C');
                $this->pdf->Cell(23, 7, (string)$cotaPublica, 1, 0, 'C');
                $this->pdf->Cell(18, 7, (string)$totalPCD, 1, 0, 'C');
                $this->pdf->Cell(24, 7, (string)$amplaPrivada, 1, 0, 'C');
                $this->pdf->Cell(24, 7, (string)$cotaPrivada, 1, 1, 'C');
            }
        }

        // COMISSÃO DE SELEÇÃO
        $usuariosAtivos = $this->getUsuariosAtivos();
        
        if (!empty($usuariosAtivos)) {
            if ($this->pdf->GetY() > 230) {
                $this->pdf->AddPage();
            }
            
            $this->pdf->Ln(10);
            
            $this->pdf->SetFont('Arial', 'B', 14);
            $this->pdf->SetFillColor(0, 90, 36);
            $this->pdf->SetTextColor(255, 255, 255);
            $this->pdf->SetX(10);
            $this->pdf->Cell(190, 9, 'COMISSÃO DE SELEÇÃO - USUÁRIOS ATIVOS', 1, 1, 'C', true);
            
            // Cabeçalho da tabela
            $this->pdf->SetFont('Arial', 'B', 10);
            $this->pdf->SetFillColor(0, 90, 36);
            $this->pdf->SetTextColor(255, 255, 255);
            $this->pdf->SetX(10);
            $this->pdf->Cell(65, 8, 'NOME', 1, 0, 'C', true);
            $this->pdf->Cell(60, 8, 'E-MAIL', 1, 0, 'C', true);
            $this->pdf->Cell(30, 8, 'CPF', 1, 0, 'C', true);
            $this->pdf->Cell(35, 8, 'TIPO DE USUÁRIO', 1, 1, 'C', true);
            
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetTextColor(0, 0, 0);
            
            foreach ($usuariosAtivos as $usuario) {
                $nome  = $usuario['nome_user'] ?? '';
                $email = $usuario['email'] ?? '';
                $cpf   = $usuario['cpf'] ?? '';
                $tipo  = $usuario['tipo_usuario'] ?? 'NÃO INFORMADO';

                // CPF formatado
                if ($cpf && strlen($cpf) === 11) {
                    $cpf = preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $cpf);
                }

                if ($this->pdf->GetY() > 270) {
                    $this->pdf->AddPage();
                    $this->pdf->SetFont('Arial', 'B', 10);
                    $this->pdf->SetFillColor(0, 90, 36);
                    $this->pdf->SetTextColor(255, 255, 255);
                    $this->pdf->SetX(10);
                    $this->pdf->Cell(65, 8, 'NOME', 1, 0, 'C', true);
                    $this->pdf->Cell(60, 8, 'E-MAIL', 1, 0, 'C', true);
                    $this->pdf->Cell(30, 8, 'CPF', 1, 0, 'C', true);
                    $this->pdf->Cell(35, 8, 'TIPO DE USUÁRIO', 1, 1, 'C', true);
                    $this->pdf->SetFont('Arial', '', 9);
                    $this->pdf->SetTextColor(0, 0, 0);
                }
                
                $this->pdf->SetX(10);
                $this->pdf->Cell(65, 8, $nome, 1, 0, 'L');
                $this->pdf->Cell(60, 8, $email, 1, 0, 'L');
                $this->pdf->Cell(30, 8, $cpf, 1, 0, 'C');
                $this->pdf->Cell(35, 8, $tipo, 1, 1, 'L');
            }
        }

        $this->pdf->Output('I', 'relatorio_cursos_admin.pdf');
    }
}

// Executa
$relatorio = new RelatorioAdminCursos($escola);
$relatorio->gerar();