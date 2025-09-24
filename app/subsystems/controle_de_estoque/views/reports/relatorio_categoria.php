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
        $this->relatorio_produtos_por_categoria();
    }

    public function relatorio_produtos_por_categoria()
    {
        $pdf = new FPDF('L', 'cm', 'A4');
        $pdf->AddPage();

        // Add image as background
        $pdf->Image('../../assets/images/fundo_horizontal.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'png', '', 0.1);

        // Add date on the top right
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0, 0, 0); // Preto por padrão
        $data_geracao = date('d/m/Y H:i:s'); // Exemplo: 25/08/2025 17:01:00 -03
        $pdf->SetXY($pdf->GetPageWidth() - 7.2, 6.8); // Top right corner
        $pdf->Cell(4, 0.6, 'Gerado em: ' . $data_geracao, 0, 0, 'R');

        // Get category from POST
        $categoria_id = isset($_POST['categoria']) ? $_POST['categoria'] : null;

        if (!$categoria_id) {
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetY(10);
            $pdf->SetX(2.5);
            $pdf->Cell(24, 1, 'Erro: Nenhuma categoria selecionada.', 0, 1, 'C');
            $pdf->Output('I', 'erro_relatorio.pdf');
            exit;
        }

        // Fetch products for the selected category
        $query = $this->connect->prepare("SELECT c.id as categoria_id, c.nome_categoria, p.id, p.barcode, p.nome_produto, p.quantidade FROM $this->table1 c LEFT JOIN $this->table4 p ON c.id = p.id_categoria WHERE c.id = :categoria_id ORDER BY p.nome_produto");
        $query->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
        $query->execute();
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

        if (empty($resultado)) {
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetY(10);
            $pdf->SetX(2.5);
            $pdf->Cell(24, 1, 'Nenhum produto encontrado para esta categoria.', 0, 1, 'C');
            $pdf->Output('I', 'relatorio_vazio.pdf');
            exit;
        }

        // Extract category name from the first result
        $categoria_nome = $resultado[0]['nome_categoria'];

        // Calculate summary for the category
        $total_produtos = count($resultado);

        // Add summary on the left side
        $y_position = 5;
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetY(5.5);
        $pdf->SetX(2.5);
        $pdf->Cell(3, 0.8, 'RESUMO:', 0, 1, 'L');

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetY(6.2);
        $pdf->SetX(2.5);
        $pdf->Cell(3, 0.6, 'Categoria: ' . utf8_decode($categoria_nome), 0, 1, 'L');
        $pdf->SetX(2.5);
        $pdf->Cell(3, 0.6, 'Total de Produtos: ' . $total_produtos, 0, 1, 'L');
        $y_position += 2.5;

        $page_height = $pdf->GetPageHeight();
        $margin_bottom = 2;

        // Category header with darker green
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(255, 255, 255); // White text
        $pdf->SetFillColor(1, 88, 36); // Darker green
        $pdf->SetY($y_position);
        $pdf->SetX(2.5);
        $pdf->Cell(24, 1, 'CATEGORIA: ' . utf8_decode($categoria_nome), 1, 1, 'C', true);
        $y_position += 1;

        // Table header for products with yellowish color
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 0, 0); // Preto por padrão
        $pdf->SetFillColor(255, 221, 119); // Yellowish color
        $pdf->SetY($y_position);
        $pdf->SetX(2.5);
        $pdf->Cell(2, 0.8, 'ID', 1, 0, 'C', true);
        $pdf->Cell(4, 0.8, 'BARCODE', 1, 0, 'C', true);
        $pdf->Cell(14, 0.8, 'NOME DO PRODUTO', 1, 0, 'C', true);
        $pdf->Cell(4, 0.8, 'QUANTIDADE', 1, 1, 'C', true);
        $y_position += 0.8;

        // Products in this category with alternating white and gray
        $pdf->SetFont('Arial', '', 11);
        $fill = false;
        foreach ($resultado as $produto) {
            if ($y_position > $page_height - 3) {
                $pdf->AddPage();
                $pdf->Image('../../assets/images/fundo_horizontal.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'png', '', 0.1);

                // Repeat date on new page
                $pdf->SetFont('Arial', '', 10);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetXY($pdf->GetPageWidth() - 7.2, 6.8);
                $pdf->Cell(4, 0.6, 'Gerado em: ' . $data_geracao, 0, 0, 'R');

                $y_position = 4;
                $y_position += 4;

                // Repeat summary on new page
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->SetX(2.5);
                $pdf->Cell(3, 0.8, 'RESUMO:', 0, 1, 'L');

                $pdf->SetFont('Arial', '', 10);
                $pdf->SetX(2.5);
                $pdf->Cell(3, 0.6, 'Categoria: ' . utf8_decode($categoria_nome), 0, 1, 'L');
                $pdf->SetX(2.5);
                $pdf->Cell(3, 0.6, 'Total de Produtos: ' . $total_produtos, 0, 1, 'L');
                $y_position += 2.5;

                // Repeat category header
                $pdf->SetFont('Arial', 'B', 14);
                $pdf->SetFillColor(1, 88, 36);
                $pdf->SetY($y_position);
                $pdf->SetX(2.5);
                $pdf->Cell(24, 1, 'CATEGORIA: ' . utf8_decode($categoria_nome) . ' (continuação)', 1, 1, 'C', true);
                $y_position += 1;

                // Repeat table header
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->SetFillColor(255, 221, 119);
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

            // Set text color to black by default
            $pdf->SetTextColor(0, 0, 0);
            $quantidade_cor = ($produto['quantidade'] <= 5) ? [255, 0, 0] : [0, 0, 0]; // Vermelho se <= 5, preto caso contrário

            $pdf->Cell(2, 0.8, utf8_decode($produto['id']), 1, 0, 'C', true);
            $pdf->Cell(4, 0.8, utf8_decode($produto['barcode'] ?: 'Sem código'), 1, 0, 'L', true);
            $pdf->Cell(14, 0.8, utf8_decode($produto['nome_produto']), 1, 0, 'L', true);

            // Apply specific color only to quantity
            $pdf->SetTextColor($quantidade_cor[0], $quantidade_cor[1], $quantidade_cor[2]);
            $pdf->Cell(4, 0.8, utf8_decode($produto['quantidade']), 1, 1, 'C', true);

            // Reset text color to black for the next row
            $pdf->SetTextColor(0, 0, 0);
            $y_position += 0.8;
            $fill = !$fill;
        }

        // Output PDF
        $pdf->Output('I', 'relatorio_' . strtolower(str_replace(' ', '_', $categoria_nome)) . '.pdf');
    }
}

$relatorio = new relatorio();