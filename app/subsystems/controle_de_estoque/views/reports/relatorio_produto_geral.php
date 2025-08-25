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
        $data_geracao = date('d/m/Y H:i:s'); // Exemplo: 25/08/2025 15:24:00 -03
        $pdf->SetXY($pdf->GetPageWidth() - 7.2, 6.8); // Top right corner
        $pdf->Cell(4, 0.6, 'Gerado em: ' . $data_geracao, 0, 0, 'R');

        // Fetch categories with their products
        $query = $this->connect->query("SELECT c.id as categoria_id, c.nome_categoria, p.id, p.barcode, p.nome_produto, p.quantidade FROM $this->table1 c LEFT JOIN $this->table4 p ON c.id = p.id_categoria ORDER BY c.nome_categoria, p.nome_produto");
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

        // Group products by category
        $categorias = [];
        foreach ($resultado as $row) {
            $categoria = $row['nome_categoria'];
            if (!isset($categorias[$categoria])) {
                $categorias[$categoria] = [];
            }
            if ($row['id']) { // Only add if product exists
                $categorias[$categoria][] = $row;
            }
        }

        // Calculate summary
        $total_categorias = count($categorias);
        $total_produtos = 0;
        foreach ($categorias as $produtos) {
            $total_produtos += count($produtos);
        }

        // Add summary on the left side
        $y_position = 5;
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetY(5.5);
        $pdf->SetX(2.5); // Left alignment
        $pdf->Cell(3, 0.8, 'RESUMO:', 0, 1, 'L');

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetY(6.2);
        $pdf->SetX(2.5);
        $pdf->Cell(3, 0.6, 'Total de Categorias: ' . $total_categorias, 0, 1, 'L');
        $pdf->SetX(2.5);
        $pdf->Cell(3, 0.6, 'Total de Produtos: ' . $total_produtos, 0, 1, 'L');
        $y_position += 2.5;

        $page_height = $pdf->GetPageHeight();
        $margin_bottom = 2;

        foreach ($categorias as $categoria => $produtos) {
            // Check if we need a new page
            if ($y_position > $page_height - 6) {
                $pdf->AddPage();
                $pdf->Image('../../assets/images/fundo_horizontal.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'png', '', 0.1);

                // Repeat date on new page
                $pdf->SetFont('Arial', '', 10);
                $pdf->SetTextColor(0, 0, 0);
                $data_geracao = date('d/m/Y H:i:s'); // Exemplo: 25/08/2025 15:24:00 -03
                $pdf->SetXY($pdf->GetPageWidth() - 7.2, 6.8); // Top right corner
                $pdf->Cell(4, 0.6, 'Gerado em: ' . $data_geracao, 0, 0, 'R');

                $y_position = 4;
                $y_position += 4;
            }

            // Category header with darker green
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetTextColor(255, 255, 255); // White text
            $pdf->SetFillColor(1, 88, 36); // Darker green
            $pdf->SetY($y_position);
            $pdf->SetX(2.5); // Center the cell
            $pdf->Cell(24, 1, 'CATEGORIA: ' . utf8_decode($categoria), 1, 1, 'C', true);
            $y_position += 1;

            // Table header for products with yellowish color
            $pdf->SetFont('Arial', 'B', 12); // Larger font
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFillColor(255, 221, 119); // Yellowish color
            $pdf->SetY($y_position);
            $pdf->SetX(2.5);
            $pdf->Cell(2, 0.8, 'ID', 1, 0, 'C', true);
            $pdf->Cell(4, 0.8, 'BARCODE', 1, 0, 'C', true);
            $pdf->Cell(14, 0.8, 'NOME DO PRODUTO', 1, 0, 'C', true);
            $pdf->Cell(4, 0.8, 'QUANTIDADE', 1, 1, 'C', true);
            $y_position += 0.8;

            // Products in this category with alternating white and gray
            $pdf->SetFont('Arial', '', 11); // Larger font
            $fill = false;
            foreach ($produtos as $produto) {
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
                    $pdf->Cell(3, 0.6, 'Total de Categorias: ' . $total_categorias, 0, 1, 'L');
                    $pdf->SetX(1);
                    $pdf->Cell(3, 0.6, 'Total de Produtos: ' . $total_produtos, 0, 1, 'L');
                    $y_position += 2.5;

                    // Repeat category header
                    $pdf->SetFont('Arial', 'B', 14);
                    $pdf->SetFillColor(1, 88, 36); // Darker green
                    $pdf->SetY($y_position);
                    $pdf->SetX(2.5);
                    $pdf->Cell(24, 1, 'CATEGORIA: ' . utf8_decode($categoria) . ' (continuação)', 1, 1, 'C', true);
                    $y_position += 1;

                    // Repeat table header
                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->SetFillColor(255, 221, 119); // Yellowish color
                    $pdf->SetY($y_position);
                    $pdf->SetX(2.5);
                    $pdf->Cell(2, 0.8, 'ID', 1, 0, 'C', true);
                    $pdf->Cell(4, 0.8, 'BARCODE', 1, 0, 'C', true);
                    $pdf->Cell(14, 0.8, 'NOME DO PRODUTO', 1, 0, 'C', true);
                    $pdf->Cell(4, 0.8, 'QUANTIDADE', 1, 1, 'C', true);
                    $y_position += 0.8;
                    $pdf->SetFont('Arial', '', 11);
                    $fill = false;
                }

                $pdf->SetY($y_position);
                $pdf->SetX(2.5);
                $cor1 = $fill ? 230 : 255;
                $cor2 = $fill ? 230 : 255;
                $cor3 = $fill ? 230 : 255; // Alternate between white and light gray
                $pdf->SetFillColor($cor1, $cor2, $cor3);

                // Set text color to red if quantity is 5 or less
                $pdf->SetTextColor(0, 0, 0); // Default black
                if ($produto['quantidade'] <= 5) {
                    $pdf->SetTextColor(255, 0, 0); // Red for low quantity
                }

                $pdf->Cell(2, 0.8, utf8_decode($produto['id']), 1, 0, 'C', true);
                $pdf->Cell(4, 0.8, utf8_decode($produto['barcode'] ?: 'Sem código'), 1, 0, 'L', true);
                $pdf->Cell(14, 0.8, utf8_decode($produto['nome_produto']), 1, 0, 'L', true);
                $pdf->Cell(4, 0.8, utf8_decode($produto['quantidade']), 1, 1, 'C', true);

                // Reset text color to black for the next cell
                $pdf->SetTextColor(0, 0, 0);
                $y_position += 0.8;
                $fill = !$fill;
            }

            // Add space between categories
            $y_position += 0.5;
        }

        // Output PDF
        $pdf->Output('I', 'relatorio_produtos_geral.pdf');
    }
}

$relatorio = new relatorio();