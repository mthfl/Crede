<?php
require_once(__DIR__ . '/../../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../../config/connect.php');
require_once(__DIR__ . '/../../assets/libs/FPDF/fpdf.php');

class relatorio extends connect
{
    private string $table1;
    private string $table2;
    private string $table3;
    private string $table4;

    function __construct()
    {
        parent::__construct();
        require(__DIR__ . '/../../models/private/tables.php');
        $this->table1 = $table['crede_estoque'][1]; // Categories table
        $this->table2 = $table['crede_estoque'][2];
        $this->table3 = $table['crede_estoque'][3]; // Perdas produtos table
        $this->table4 = $table['crede_estoque'][4]; // Products table
        $this->relatorio_perdas_geral();
    }

    public function relatorio_perdas_geral()
    {
        $pdf = new FPDF('L', 'cm', 'A4');
        $pdf->AddPage();

        // Add image as background
        $pdf->Image('../../assets/images/fundo_horizontal.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'png', '', 0.1);

        // Add date on the top right
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $data_geracao = date('d/m/Y H:i:s'); // Exemplo: 25/08/2025 16:36:00 -03
        $pdf->SetXY($pdf->GetPageWidth() - 7.2, 6.8);
        $pdf->Cell(4, 0.6, 'Gerado em: ' . $data_geracao, 0, 0, 'R');

        // Fetch perdas with product details
        $query = $this->connect->query("SELECT p.barcode, p.nome_produto, pp.quantidade, pp.tipo, pp.data 
                                        FROM $this->table3 pp 
                                        LEFT JOIN $this->table4 p ON pp.id_produto = p.id 
                                        ORDER BY pp.data");
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

        // Calculate summary
        $total_perdas = count($resultado);
        $total_quantidade_perdida = array_sum(array_column($resultado, 'quantidade'));

        // Add summary on the left side
        $y_position = 5;
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetY(5.5);
        $pdf->SetX(2.5);
        $pdf->Cell(3, 0.8, 'RESUMO:', 0, 1, 'L');

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetY(6.2);
        $pdf->SetX(2.5);
        $pdf->Cell(3, 0.6, 'Total de Perdas: ' . $total_perdas, 0, 1, 'L');
        $pdf->SetX(2.5);
        $pdf->Cell(3, 0.6, 'Total Quantidade Perdida: ' . $total_quantidade_perdida, 0, 1, 'L');
        $y_position += 2.5;

        $page_height = $pdf->GetPageHeight();
        $margin_bottom = 2;

        // Group perdas by date
        $perdas_por_data = [];
        foreach ($resultado as $perda) {
            $data = $perda['data'];
            if (!isset($perdas_por_data[$data])) {
                $perdas_por_data[$data] = [];
            }
            $perdas_por_data[$data][] = $perda;
        }

        foreach ($perdas_por_data as $data => $perdas) {
            // Check if we need a new page
            if ($y_position > $page_height - 6) {
                $pdf->AddPage();
                $pdf->Image('../../assets/images/fundo_horizontal.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'png', '', 0.1);

                // Repeat date on new page
                $pdf->SetFont('Arial', '', 10);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetXY($pdf->GetPageWidth() - 7.2, 6.8);
                $pdf->Cell(4, 0.6, 'Gerado em: ' . $data_geracao, 0, 0, 'R');

                $y_position = 4;
                $y_position += 4;
            }

            // Date header
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFillColor(1, 88, 36);
            $pdf->SetY($y_position);
            $pdf->SetX(2.5);
            $pdf->Cell(24, 1, 'DATA: ' . date('d/m/Y', strtotime($data)), 1, 1, 'C', true); // Only date
            $y_position += 1;

            // Table header for perdas
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFillColor(255, 221, 119);
            $pdf->SetY($y_position);
            $pdf->SetX(2.5);
            $pdf->Cell(4, 0.8, 'BARCODE', 1, 0, 'C', true);
            $pdf->Cell(10, 0.8, 'NOME DO PRODUTO', 1, 0, 'C', true);
            $pdf->Cell(4, 0.8, 'QUANTIDADE', 1, 0, 'C', true);
            $pdf->Cell(4, 0.8, 'TIPO DE PERDA', 1, 0, 'C', true);
            $pdf->Cell(2, 0.8, 'DATA', 1, 1, 'C', true);
            $y_position += 0.8;

            // Perdas for this date
            $pdf->SetFont('Arial', '', 11);
            $fill = false;
            foreach ($perdas as $perda) {
                if ($y_position > $page_height - 3) {
                    $pdf->AddPage();
                    $pdf->Image('../../assets/images/fundo_horizontal.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'png', '', 0.1);

                    // Repeat date on new page
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetXY($pdf->GetPageWidth() - 5, 1);
                    $pdf->Cell(4, 0.6, 'Gerado em: ' . $data_geracao, 0, 0, 'R');

                    $y_position = 1;

                    // Repeat summary on new page
                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetX(1);
                    $pdf->Cell(3, 0.8, 'RESUMO:', 0, 1, 'L');

                    $pdf->SetFont('Arial', '', 10);
                    $pdf->SetX(1);
                    $pdf->Cell(3, 0.6, 'Total de Perdas: ' . $total_perdas, 0, 1, 'L');
                    $pdf->SetX(1);
                    $pdf->Cell(3, 0.6, 'Total Quantidade Perdida: ' . $total_quantidade_perdida, 0, 1, 'L');
                    $y_position += 2.5;

                    // Repeat date header
                    $pdf->SetFont('Arial', 'B', 14);
                    $pdf->SetFillColor(1, 88, 36);
                    $pdf->SetY($y_position);
                    $pdf->SetX(2.5);
                    $pdf->Cell(24, 1, 'DATA: ' . date('d/m/Y', strtotime($data)) . ' (continuação)', 1, 1, 'C', true);
                    $y_position += 1;

                    // Repeat table header
                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->SetFillColor(255, 221, 119);
                    $pdf->SetY($y_position);
                    $pdf->SetX(2.5);
                    $pdf->Cell(4, 0.8, 'BARCODE', 1, 0, 'C', true);
                    $pdf->Cell(10, 0.8, 'NOME DO PRODUTO', 1, 0, 'C', true);
                    $pdf->Cell(4, 0.8, 'QUANTIDADE', 1, 0, 'C', true);
                    $pdf->Cell(4, 0.8, 'TIPO DE PERDA', 1, 0, 'C', true);
                    $pdf->Cell(2, 0.8, 'DATA', 1, 1, 'C', true);
                    $y_position += 0.8;
                    $pdf->SetFont('Arial', '', 11);
                    $fill = false;
                }

                $pdf->SetY($y_position);
                $pdf->SetX(2.5);
                $cor1 = $fill ? 230 : 255;
                $cor2 = $fill ? 230 : 255;
                $cor3 = $fill ? 230 : 255;
                $pdf->SetFillColor($cor1, $cor2, $cor3);

                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(4, 0.8, utf8_decode($perda['barcode'] ?: 'Sem código'), 1, 0, 'L', true);
                $pdf->Cell(10, 0.8, utf8_decode($perda['nome_produto']), 1, 0, 'L', true);
                $pdf->Cell(4, 0.8, utf8_decode($perda['quantidade']), 1, 0, 'C', true);
                $pdf->Cell(4, 0.8, utf8_decode($perda['tipo']), 1, 0, 'L', true);
                $pdf->Cell(2, 0.8, utf8_decode(date('d/m/Y', strtotime($perda['data']))), 1, 1, 'C', true);

                $y_position += 0.8;
                $fill = !$fill;
            }

            $y_position += 0.5;
        }

        // Output PDF
        $pdf->Output('I', 'relatorio_perdas_geral.pdf');
    }
}

$relatorio = new relatorio();