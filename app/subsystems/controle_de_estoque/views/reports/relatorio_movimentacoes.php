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
        $this->table2 = $table['crede_estoque'][2]; // Movimentacao table
        $this->table3 = $table['crede_estoque'][3];
        $this->table4 = $table['crede_estoque'][4]; // Products table
        $this->relatorio_produtos_geral();
    }

    public function relatorio_produtos_geral()
    {
        $pdf = new FPDF('L', 'cm', 'A4');
        $pdf->AddPage();

        // Add image as background
        $pdf->Image('../../assets/images/fundo_horizontal.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'png', '', 0.1);

        // Add date on the top right
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $data_geracao = date('d/m/Y H:i:s');
        $pdf->SetXY($pdf->GetPageWidth() - 7.2, 6.8);
        $pdf->Cell(4, 0.6, 'Gerado em: ' . $data_geracao, 0, 0, 'R');

        // Get date range from POST
        $data_inicio = isset($_POST['data_inicio']) ? $_POST['data_inicio'] : '';
        $data_fim = isset($_POST['data_fim']) ? $_POST['data_fim'] : '';

        // Fetch movimentacoes within date range with product details
        $query = $this->connect->prepare("SELECT m.id, p.barcode, p.nome_produto, m.quantidade_retirada, m.liberador, m.solicitador, m.datareg 
                                         FROM $this->table2 m 
                                         LEFT JOIN $this->table4 p ON m.id_produtos = p.id 
                                         WHERE m.datareg BETWEEN :data_inicio AND :data_fim 
                                         ORDER BY m.datareg");
        $query->bindValue(':data_inicio', $data_inicio . ' 00:00:00');
        $query->bindValue(':data_fim', $data_fim . ' 23:59:59');
        $query->execute();
        $movimentacoes = $query->fetchAll(PDO::FETCH_ASSOC);

        // Group movimentacoes by date for better organization
        $movimentacoes_por_data = [];
        foreach ($movimentacoes as $mov) {
            $data = $mov['datareg'];
            if (!isset($movimentacoes_por_data[$data])) {
                $movimentacoes_por_data[$data] = [];
            }
            $movimentacoes_por_data[$data][] = $mov;
        }

        // Calculate summary (total movimentacoes)
        $total_movimentacoes = count($movimentacoes);

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
        $pdf->Cell(3, 0.6, utf8_decode('Total de Movimentações: ') . $total_movimentacoes, 0, 1, 'L');
        $y_position += 2.5;

        $page_height = $pdf->GetPageHeight();
        $margin_bottom = 2;

        foreach ($movimentacoes_por_data as $data => $movs) {
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
            $pdf->SetX(1);
            $pdf->Cell(28, 1, 'DATA: ' . date('d/m/Y H:i:s', strtotime($data)), 1, 1, 'C', true); // Increased to 28cm
            $y_position += 1;

            // Table header for movimentacoes
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFillColor(255, 221, 119);
            $pdf->SetY($y_position);
            $pdf->SetX(1);
            $pdf->Cell(2, 0.8, 'ID', 1, 0, 'C', true);
            $pdf->Cell(4, 0.8, 'BARCODE', 1, 0, 'C', true);
            $pdf->Cell(6, 0.8, 'NOME', 1, 0, 'C', true);
            $pdf->Cell(4, 0.8, 'QTD RETIRADA', 1, 0, 'C', true);
            $pdf->Cell(6, 0.8, 'LIBERADOR', 1, 0, 'C', true); // Increased to 6cm
            $pdf->Cell(6, 0.8, 'SOLICITADOR', 1, 1, 'C', true); // Increased to 6cm
            $y_position += 0.8;

            // Movimentacoes for this date
            $pdf->SetFont('Arial', '', 11);
            $fill = false;
            foreach ($movs as $mov) {
                if ($y_position > $page_height - 3) {
                    $pdf->AddPage();
                    $pdf->Image('../../assets/images/fundo_horizontal.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'png', '', 0.1);

                    // Repeat date on new page
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetXY($pdf->GetPageWidth() - 7.2, 6.8);
                    $pdf->Cell(4, 0.6, 'Gerado em: ' . $data_geracao, 0, 0, 'R');

                    $y_position = 1;

                    // Repeat summary on new page
                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetX(1);
                    $pdf->Cell(3, 0.8, 'RESUMO:', 0, 1, 'L');

                    $pdf->SetFont('Arial', '', 10);
                    $pdf->SetX(1);
                    $pdf->Cell(3, 0.6, 'Total de Movimentações: ' . $total_movimentacoes, 0, 1, 'L');
                    $y_position += 2.5;

                    // Repeat date header
                    $pdf->SetFont('Arial', 'B', 14);
                    $pdf->SetFillColor(1, 88, 36);
                    $pdf->SetY($y_position);
                    $pdf->SetX(2.5);
                    $pdf->Cell(28, 1, 'DATA: ' . date('d/m/Y H:i:s', strtotime($data)) . ' (continuação)', 1, 1, 'C', true);
                    $y_position += 1;

                    // Repeat table header
                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->SetFillColor(255, 221, 119);
                    $pdf->SetY($y_position);
                    $pdf->SetX(1);
                    $pdf->Cell(2, 0.8, 'ID', 1, 0, 'C', true);
                    $pdf->Cell(4, 0.8, 'BARCODE', 1, 0, 'C', true);
                    $pdf->Cell(6, 0.8, 'NOME', 1, 0, 'C', true);
                    $pdf->Cell(4, 0.8, 'QTD RETIRADA', 1, 0, 'C', true);
                    $pdf->Cell(6, 0.8, 'LIBERADOR', 1, 0, 'C', true);
                    $pdf->Cell(6, 0.8, 'SOLICITADOR', 1, 1, 'C', true);
                    $y_position += 0.8;
                    $pdf->SetFont('Arial', '', 11);
                    $fill = false;
                }

                $pdf->SetY($y_position);
                $pdf->SetX(1);
                $cor1 = $fill ? 230 : 255;
                $cor2 = $fill ? 230 : 255;
                $cor3 = $fill ? 230 : 255;
                $pdf->SetFillColor($cor1, $cor2, $cor3);

                // Set text color to red for quantity retirada
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(2, 0.8, utf8_decode($mov['id']), 1, 0, 'C', true);
                $pdf->Cell(4, 0.8, utf8_decode($mov['barcode'] ?: 'Sem código'), 1, 0, 'C', true);
                $pdf->Cell(6, 0.8, utf8_decode($mov['nome_produto']), 1, 0, 'L', true);
                $pdf->SetTextColor(255, 0, 0); // Red for quantity retirada
                $pdf->Cell(4, 0.8, utf8_decode($mov['quantidade_retirada']), 1, 0, 'C', true);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(6, 0.8, utf8_decode($mov['liberador']), 1, 0, 'L', true);
                $pdf->Cell(6, 0.8, utf8_decode($mov['solicitador']), 1, 1, 'L', true);

                $y_position += 0.8;
                $fill = !$fill;
            }

            $y_position += 0.5;
        }

        // Output PDF
        $pdf->Output('I', 'relatorio_movimentações.pdf');
    }
}

$relatorio = new relatorio();