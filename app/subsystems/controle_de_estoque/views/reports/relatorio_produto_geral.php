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
        
        // Configure UTF-8 support
        $pdf->SetAuthor('Sistema Credé');
        $pdf->SetCreator('FPDF');
        
        // Function to handle UTF-8 characters
        function utf8_to_iso($string) {
            return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $string);
        }

        // Add image as background
        $pdf->Image('../../assets/images/fundo_horizontal.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'png', '', 0.1);

        // Add date on the top right
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $data_geracao = date('d/m/Y H:i:s'); // Exemplo: 25/08/2025 15:24:00 -03
        $pdf->SetXY($pdf->GetPageWidth() - 5, 1.8); // Top right corner
        $pdf->Cell(4, 0.6, 'Gerado em: ' . $data_geracao, 0, 0, 'R');

        // Fetch categories with their products
        $sql = "SELECT c.id as categoria_id, c.nome_categoria, p.id, p.barcode, p.nome_produto, p.quantidade 
                FROM $this->table1 c 
                LEFT JOIN $this->table4 p ON c.id = p.id_categoria 
                ORDER BY c.nome_categoria, p.nome_produto";
        
        try {
            $query = $this->connect->query($sql);
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Fallback: try to get categories only
            $query_cat = $this->connect->query("SELECT id, nome_categoria FROM $this->table1 ORDER BY nome_categoria");
            $categorias_only = $query_cat->fetchAll(PDO::FETCH_ASSOC);
            $resultado = [];
            foreach ($categorias_only as $cat) {
                $resultado[] = [
                    'categoria_id' => $cat['id'],
                    'nome_categoria' => $cat['nome_categoria'],
                    'id' => null,
                    'barcode' => null,
                    'nome_produto' => null,
                    'quantidade' => 0
                ];
            }
        }
        


        // Group products by category
        $categorias = [];
        $total_produtos = 0;
        $total_categorias = 0;
        $categorias_com_produtos = [];
        $categorias_sem_produtos = [];
        $soma_por_categoria = [];
        

        
        foreach ($resultado as $row) {
            $categoria = $row['nome_categoria'];
            if (!isset($categorias[$categoria])) {
                $categorias[$categoria] = [];
                $total_categorias++;
                $soma_por_categoria[$categoria] = 0;
            }
            if ($row['id']) { // Only add if product exists
                $categorias[$categoria][] = $row;
                $total_produtos++; // Count each different product, not units
                $soma_por_categoria[$categoria] += $row['quantidade'];
                if (!in_array($categoria, $categorias_com_produtos)) {
                    $categorias_com_produtos[] = $categoria;
                }
            }
        }
        

        
        // Separate categories with and without products, ordered by quantity
        $categorias_ordenadas = [];
        
        // First: categories with products (ordered by total quantity - highest first)
        $categorias_com_produtos_ordenadas = [];
        foreach ($categorias_com_produtos as $categoria) {
            $categorias_com_produtos_ordenadas[$categoria] = $soma_por_categoria[$categoria];
        }
        
        // Sort by quantity (highest first)
        arsort($categorias_com_produtos_ordenadas);
        
        // Add categories with products in order
        foreach ($categorias_com_produtos_ordenadas as $categoria => $quantidade) {
            $categorias_ordenadas[$categoria] = $categorias[$categoria];
        }
        
        // Then: categories without products
        foreach ($categorias as $categoria => $produtos) {
            if (empty($produtos) && !isset($categorias_ordenadas[$categoria])) {
                $categorias_ordenadas[$categoria] = [];
            }
        }

        // Add summary card at the top - properly positioned
        $pdf->SetY(5.5);
        
        // Calculate center position for card
        $page_width = $pdf->GetPageWidth();
        $card_width = 8;
        $start_x = ($page_width - $card_width) / 2;
        
        // Card: Total de Produtos (same layout as category cards)
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(1, 88, 36); // Dark green
        $pdf->SetX($start_x);
        $pdf->Cell($card_width, 1, utf8_to_iso('TOTAL PRODUTOS'), 1, 0, 'C', true);
        
        // Values row
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(255, 255, 255); // White
        $pdf->SetY(6.5);
        $pdf->SetX($start_x);
        $pdf->Cell($card_width, 1, $total_produtos, 1, 1, 'C', true);
        




        // Start content from top (after cards)
        $y_position = 8;

        $page_height = $pdf->GetPageHeight();
        $margin_bottom = 2;


        
        foreach ($categorias_ordenadas as $categoria => $produtos) {
            // Check if we need a new page
            if ($y_position > $page_height - 6) {
                $pdf->AddPage();
                $pdf->Image('../../assets/images/fundo_horizontal.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'png', '', 0.1);

                // Repeat date on new page
                $pdf->SetFont('Arial', '', 10);
                $pdf->SetTextColor(0, 0, 0);
                $data_geracao = date('d/m/Y H:i:s'); // Exemplo: 25/08/2025 15:24:00 -03
                $pdf->SetXY($pdf->GetPageWidth() - 5, 1.8); // Top right corner
                $pdf->Cell(4, 0.6, 'Gerado em: ' . $data_geracao, 0, 0, 'R');

                $y_position = 7;
            }

            // Category cards showing total sum (smaller size)
            $soma_categoria = isset($soma_por_categoria[$categoria]) ? $soma_por_categoria[$categoria] : 0;
            
            // Calculate center position for category cards
            $page_width = $pdf->GetPageWidth();
            $card_width = 8;
            $card_spacing = 1;
            $total_cards_width = (2 * $card_width) + $card_spacing;
            $start_x = ($page_width - $total_cards_width) / 2;
            
            // Card 1: Nome da Categoria
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFillColor(1, 88, 36); // Dark green
            $pdf->SetY($y_position);
            $pdf->SetX($start_x);
            $pdf->Cell($card_width, 1, utf8_to_iso($categoria), 1, 0, 'C', true);
            
            // Card 2: Quantidade Total da Categoria
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFillColor(1, 88, 36); // Dark green
            $pdf->SetX($start_x + $card_width + $card_spacing);
            $pdf->Cell($card_width, 1, utf8_to_iso('quantidade total'), 1, 0, 'C', true);
            
            // Values row
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFillColor(255, 255, 255); // White
            $pdf->SetY($y_position + 1);
            $pdf->SetX($start_x);
            $pdf->Cell($card_width, 1, utf8_to_iso('PRODUTOS: ' . number_format(count($produtos), 0, ',', '.')), 1, 0, 'C', true);
            
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFillColor(255, 255, 255); // White
            $pdf->SetX($start_x + $card_width + $card_spacing);
            $pdf->Cell($card_width, 1, number_format($soma_categoria, 0, ',', '.'), 1, 1, 'C', true);
            
            $y_position += 2.5;

            // Only show table if category has products
            if (!empty($produtos)) {
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
                    $pdf->SetXY($pdf->GetPageWidth() - 5, 1.8);
                    $pdf->Cell(4, 0.6, 'Gerado em: ' . $data_geracao, 0, 0, 'R');

                    $y_position = 7;

                    // Just add a small header for continuation
                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetY($y_position);
                    $pdf->SetX(2.5);
                    $pdf->Cell(24, 0.8, utf8_to_iso('Continuação da categoria: ' . $categoria), 0, 1, 'L');
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
                $pdf->Cell(4, 0.8, utf8_decode(number_format($produto['quantidade'], 0, ',', '.')), 1, 1, 'C', true);

                // Reset text color to black for the next cell
                $pdf->SetTextColor(0, 0, 0);
                $y_position += 0.8;
                $fill = !$fill;
            }
            } // End of if (!empty($produtos))

            // Add space between categories
            $y_position += 0.5;
        }

        // Output PDF
        $pdf->Output('I', 'relatorio_produtos_geral.pdf');
    }
}

$relatorio = new relatorio();