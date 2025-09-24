<?php
require_once(__DIR__ . '/../../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once('../../config/connect.php');
require_once('../../assets/libs/FPDF/fpdf.php');

class relatorio extends connect {
    private string $table1;
    private string $table2;
    private string $table3;
    private string $table4;
    private string $table5;

    function __construct() {
        parent::__construct();
        require('../../models/private/tables.php');
        $this->table1 = $table['crede_estoque'][1]; // Categories table
        $this->table2 = $table['crede_estoque'][2];
        $this->table3 = $table['crede_estoque'][3];
        $this->table4 = $table['crede_estoque'][4]; // Products table
        $this->table5 = $table['crede_estoque'][5];
        $this->relatorio_produtos_geral();
    }

    public function relatorio_produtos_geral() {
        $pdf = new FPDF('L', 'cm', 'A4');
        $pdf->AddPage();

        // Add image as background
        $pdf->Image('../../assets/images/fundo_horizontal.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'png', '', 0.1); // Adjust opacity (0.1) and path


        // Set font for the header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(200, 200, 200); // Light gray background for header
        
        $pdf->SetY(8);
        $pdf->SetX(4);
        // Table header with adjusted column widths
        $pdf->Cell(2.5, 1, 'ID', 1, 0, 'C', true);
        $pdf->Cell(4, 1, 'BARCODE', 1, 0, 'C', true);
        $pdf->Cell(5, 1, 'NOME', 1, 0, 'C', true);
        $pdf->Cell(5, 1, 'CATEGORIA', 1, 0, 'C', true);
        $pdf->Cell(3.5, 1, 'VENCIMENTO', 1, 0, 'C', true);
        $pdf->Cell(3.5, 1, 'QUANTIDADE', 1, 1, 'C', true); // ln=1 for new line

        // Fetch data
        $query = $this->connect->query("SELECT p.*, c.nome_categoria AS categoria FROM $this->table4 p INNER JOIN $this->table1 c ON p.id_categoria = c.id");
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

        // Set font for table content
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetFillColor(255, 255, 255); // White background for data rows

        // Loop through results and populate table
        foreach ($resultado as $row) {
            $pdf->SetY(9);
            $pdf->SetX(4);
            $pdf->Cell(2.5, 1, $row['id'], 1, 0, 'C');
            $pdf->Cell(4, 1, $row['barcode'], 1, 0, 'C');
            $pdf->Cell(5, 1, utf8_decode($row['nome_produto']), 1, 0, 'C'); // Left-align for longer text
            $pdf->Cell(5, 1, utf8_decode($row['categoria']), 1, 0, 'C');
            $pdf->Cell(3.5, 1, $row['vencimento'], 1, 0, 'C');
            $pdf->Cell(3.5, 1, $row['quantidade'], 1, 1, 'C'); // ln=1 for new line
        }

        // Output PDF
        $pdf->Output('I', 'relatorio_produtos_geral.pdf');
    }
}

$relatorio = new relatorio();