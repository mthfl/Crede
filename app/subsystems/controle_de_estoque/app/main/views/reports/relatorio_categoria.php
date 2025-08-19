<?php
require_once(__DIR__ . '/../../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require("../../config/connect.php");
require("../../assets/libs/FPDF/fpdf.php");

class relatorios extends connect{

    function __construct($categoria){
        parent::__construct();
        $this->relatorioPorCategoria($categoria);

    }
    public function relatorioPorCategoria($categoria)
    {
        $consulta = "SELECT * FROM produtos WHERE natureza = ? ORDER BY natureza, nome_produto";
        $query = $this->connect->prepare($consulta);
        $query->execute([$categoria]);
        $result = $query->rowCount();

        // Criar PDF personalizado
        $pdf = new PDF("P", "pt", "A4");
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 60);

        // Paleta de cores consistente com o sistema
        $corPrimary = array(0, 90, 36);       // #005A24 - Verde principal
        $corDark = array(26, 60, 52);         // #1A3C34 - Verde escuro
        $corSecondary = array(255, 165, 0);   // #FFA500 - Laranja para destaques
        $corCinzaClaro = array(248, 250, 249); // #F8FAF9 - Fundo alternado
        $corBranco = array(255, 255, 255);    // #FFFFFF - Branco
        $corPreto = array(40, 40, 40);        // #282828 - Quase preto para texto
        $corAlerta = array(220, 53, 69);      // #DC3545 - Vermelho para alertas
        $corTextoSubtil = array(100, 100, 100); // #646464 - Cinza para textos secundários

        // ===== CABEÇALHO COM FUNDO VERDE SÓLIDO =====
        $pdf->SetFillColor($corPrimary[0], $corPrimary[1], $corPrimary[2]);
        $pdf->Rect(0, 0, $pdf->GetPageWidth(), 95, 'F');

        // Logo
        $logoPath = "../assets/imagens/logostgm.png";
        $logoWidth = 60;
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 40, 20, $logoWidth);
            $pdf->SetXY(40 + $logoWidth + 15, 30);
        } else {
            $pdf->SetXY(40, 30);
        }

        // Título e subtítulo
        $pdf->SetFont('Arial', 'B', 15); // Reduzindo o tamanho da fonte para caber melhor
        $pdf->SetTextColor($corBranco[0], $corBranco[1], $corBranco[2]);
        
        // Calculando a largura disponível para o título
        $larguraDisponivel = $pdf->GetPageWidth() - 300; // Deixando espaço para logo
        $pdf->SetXY(40 + $logoWidth + 5, 30); // Reduzindo o espaçamento de 15 para 5
        $pdf->Cell($larguraDisponivel, 24, utf8_decode("RELATÓRIO POR CATEGORIA - " . mb_strtoupper($categoria, 'UTF-8')), 0, 1, 'L');

        $pdf->SetFont('Arial', '', 12);
        $pdf->SetXY(40 + $logoWidth + 5, $pdf->GetY()); // Reduzindo o espaçamento de 15 para 5
        $pdf->Cell($larguraDisponivel, 10, utf8_decode("EEEP Salaberga Torquato Gomes de Matos"), 0, 1, 'L');

        // ===== RESUMO DE DADOS EM CARDS =====
        $totalProdutosNaCategoria = $result;
        $totalQuantidade = 0;
        $categoriasUnicas = 0;
        $produtos = array();
        
        if ($result > 0) {
            $produtos = $query->fetchAll(PDO::FETCH_ASSOC);
            $totalQuantidade = array_sum(array_column($produtos, 'quantidade'));
            $categoriasUnicas = count(array_unique(array_column($produtos, 'natureza')));
        }

        // Criar cards para os resumos (apenas 2 cards como na imagem)
        $cardWidth = 200;
        $cardHeight = 80;
        $cardMargin = 20;
        $startX = ($pdf->GetPageWidth() - (2 * $cardWidth + $cardMargin)) / 2; // Centralizar 2 cards
        $startY = 110;

        // Card 1 - Total de Produtos
        $pdf->SetFillColor($corBranco[0], $corBranco[1], $corBranco[2]);
        $pdf->RoundedRect($startX, $startY, $cardWidth, $cardHeight, 8, 'F');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor($corPreto[0], $corPreto[1], $corPreto[2]);
        $pdf->SetXY($startX + 15, $startY + 15);
        $pdf->Cell($cardWidth - 30, 20, utf8_decode("TOTAL DE PRODUTOS"), 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 24);
        $pdf->SetTextColor($corAlerta[0], $corAlerta[1], $corAlerta[2]); // Vermelho como na imagem
        $pdf->SetXY($startX + 15, $startY + 40);
        $pdf->Cell($cardWidth - 30, 25, $totalProdutosNaCategoria, 0, 1, 'L');

        // Card 2 - Categorias
        $pdf->SetFillColor($corBranco[0], $corBranco[1], $corBranco[2]);
        $pdf->RoundedRect($startX + $cardWidth + $cardMargin, $startY, $cardWidth, $cardHeight, 8, 'F');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor($corPreto[0], $corPreto[1], $corPreto[2]);
        $pdf->SetXY($startX + $cardWidth + $cardMargin + 15, $startY + 15);
        $pdf->Cell($cardWidth - 30, 20, utf8_decode("CATEGORIAS"), 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 24);
        $pdf->SetTextColor($corSecondary[0], $corSecondary[1], $corSecondary[2]); // Laranja para categorias
        $pdf->SetXY($startX + $cardWidth + $cardMargin + 15, $startY + 40);
        $pdf->Cell($cardWidth - 30, 25, $categoriasUnicas, 0, 1, 'L');

        // ===== TABELA DE PRODUTOS NA CATEGORIA =====
        $pdf->Ln(20);
        $y = $pdf->GetY();
        $margemTabela = 40;
        $larguraPagina = $pdf->GetPageWidth() - (2 * $margemTabela);
        
        // Cabeçalho da tabela
        $pdf->SetFillColor($corPrimary[0], $corPrimary[1], $corPrimary[2]);
        $pdf->SetTextColor($corBranco[0], $corBranco[1], $corBranco[2]);
        $pdf->SetFont('Arial', 'B', 12);
        
        $pdf->RoundedRect($margemTabela, $y, $larguraPagina, 30, 5, 'FD');
        $pdf->SetXY($margemTabela + 15, $y + 8);
        $pdf->Cell($larguraPagina - 30, 15, utf8_decode("DETALHAMENTO DOS PRODUTOS NA CATEGORIA"), 0, 1, 'L');
        
        $y += 35;
        
        // Cabeçalhos das colunas
        $pdf->SetFillColor($corDark[0], $corDark[1], $corDark[2]);
        $pdf->SetTextColor($corBranco[0], $corBranco[1], $corBranco[2]);
        $pdf->SetFont('Arial', 'B', 10);
        
        $colunas = array('ID', 'Código', 'Produto', 'Qtd.', 'Natureza');
        $larguras = array(
            round($larguraPagina * 0.08),  // ID
            round($larguraPagina * 0.25),  // Código
            round($larguraPagina * 0.45),  // Produto
            round($larguraPagina * 0.10),  // Quantidade
            round($larguraPagina * 0.12)   // Natureza
        );

        $posX = $margemTabela;
        $pdf->RoundedRect($posX, $y, $larguras[0], 25, 5, 'FD', '1');
        $pdf->SetXY($posX, $y + 7);
        $pdf->Cell($larguras[0], 15, utf8_decode($colunas[0]), 0, 0, 'C');
        $posX += $larguras[0];
        
        for ($i = 1; $i < count($colunas) - 1; $i++) {
            $pdf->Rect($posX, $y, $larguras[$i], 25, 'FD');
            $pdf->SetXY($posX, $y + 7);
            $pdf->Cell($larguras[$i], 15, utf8_decode($colunas[$i]), 0, 0, 'C');
            $posX += $larguras[$i];
        }
        
        $pdf->RoundedRect($posX, $y, $larguras[count($colunas) - 1], 25, 5, 'FD', '2');
        $pdf->SetXY($posX, $y + 7);
        $pdf->Cell($larguras[count($colunas) - 1], 15, utf8_decode($colunas[count($colunas) - 1]), 0, 0, 'C');
        
        $y += 30;

        // Dados da tabela
        $linhaAlternada = false;
        if ($result > 0) {
            foreach ($produtos as $idx => $row) {
                // Verificar se precisa de nova página
                if ($y + 25 > $pdf->GetPageHeight() - 60) {
                    $pdf->AddPage();
                    $y = 40;
                }
                
                // Configurar cor de fundo alternada
                if ($linhaAlternada) {
                    $pdf->SetFillColor($corCinzaClaro[0], $corCinzaClaro[1], $corCinzaClaro[2]);
                } else {
                    $pdf->SetFillColor($corBranco[0], $corBranco[1], $corBranco[2]);
                }
                
                // Configurar texto
                $pdf->SetFont('Arial', '', 9);
                $pdf->SetTextColor($corPreto[0], $corPreto[1], $corPreto[2]);
                
                // Desenhar linha de dados
                $posX = $margemTabela;
                
                // ID
                $pdf->Rect($posX, $y, $larguras[0], 20, 'FD');
                $pdf->SetXY($posX, $y + 5);
                $pdf->Cell($larguras[0], 15, $row['id'], 0, 0, 'C');
                $posX += $larguras[0];
                
                // Barcode
                $pdf->Rect($posX, $y, $larguras[1], 20, 'FD');
                $pdf->SetXY($posX + 5, $y + 5);
                $pdf->Cell($larguras[1] - 10, 15, $row['barcode'], 0, 0, 'L');
                $posX += $larguras[1];
                
                // Nome do produto
                $pdf->Rect($posX, $y, $larguras[2], 20, 'FD');
                $pdf->SetXY($posX + 5, $y + 5);
                $nomeProduto = utf8_decode($row['nome_produto']);
                if (strlen($nomeProduto) > 35) {
                    $nomeProduto = substr($nomeProduto, 0, 32) . '...';
                }
                $pdf->Cell($larguras[2] - 10, 15, $nomeProduto, 0, 0, 'L');
                $posX += $larguras[2];
                
                // Quantidade
                $pdf->Rect($posX, $y, $larguras[3], 20, 'FD');
                $pdf->SetXY($posX, $y + 5);
                $pdf->SetTextColor($corAlerta[0], $corAlerta[1], $corAlerta[2]);
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell($larguras[3], 15, $row['quantidade'], 0, 0, 'C');
                $pdf->SetTextColor($corPreto[0], $corPreto[1], $corPreto[2]);
                $pdf->SetFont('Arial', '', 9);
                $posX += $larguras[3];
                
                // Natureza
                $pdf->Rect($posX, $y, $larguras[4], 20, 'FD');
                $pdf->SetXY($posX + 5, $y + 5);
                $pdf->Cell($larguras[4] - 10, 15, utf8_decode($row['natureza']), 0, 0, 'L');
                
                $y += 25;
                $linhaAlternada = !$linhaAlternada;
            }
        } else {
            $pdf->SetXY($margemTabela, $y);
            $pdf->SetFont('Arial', 'I', 12);
            $pdf->SetTextColor($corTextoSubtil[0], $corTextoSubtil[1], $corTextoSubtil[2]);
            $pdf->SetFillColor(250, 250, 250);
            $pdf->RoundedRect($margemTabela, $y, array_sum($larguras), 40, 5, 'FD');
            $pdf->SetXY($margemTabela, $y + 12);
            $pdf->Cell(array_sum($larguras), 16, utf8_decode(""), 0, 1, 'C');
        }



        // ===== RODAPÉ PROFISSIONAL =====
        if ($y + 60 > $pdf->GetPageHeight() - 60) {
            $pdf->AddPage();
            $y = 40;
        }
        
        $pdf->SetAutoPageBreak(false);
        $pdf->SetTextColor($corTextoSubtil[0], $corTextoSubtil[1], $corTextoSubtil[2]);
        $pdf->SetFont('Arial', '', 9);
        
        $pdf->SetXY($margemTabela, $y + 10);
        $pdf->Cell(0, 15, utf8_decode(""), 0, 1, 'C');
        $pdf->SetXY($margemTabela, $y + 25);
        $pdf->Cell(0, 15, utf8_decode(""), 0, 1, 'C');
        
        // Saída do PDF (mesmo padrão dos outros relatórios)
        $pdf->Output("relatorio_estoque_critico.pdf", "I");
    }
}

if(isset($_POST["categoria"]) && !empty($_POST["categoria"])) {

    $relatorio = new relatorios($_POST['categoria']);
}
?>