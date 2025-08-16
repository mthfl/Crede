<?php
require("../../config/connect.php");
require("../../assets/libs/FPDF/fpdf.php");

class relatorios extends connect
{

    function __construct()
    {
        parent::__construct();
        $this->relatoriocriticostoque();
    }
    public function relatorioEstoqueProduto($data_inicio, $data_fim, $produto_id = null)
    {
        try {
            // Usar a conexão PDO da classe atual
            $pdo = $this->getPdo();

            // Buscar dados de movimentação
            $query = "
                SELECT 
                    e.id,
                    e.quantidade_retirada as quantidade,
                    e.datareg as data,
                    e.barcode_produto,
                    p.nome_produto,
                    r.nome AS nome_responsavel,
                    r.cargo AS cargo
                FROM movimentacao e
                LEFT JOIN produtos p ON e.fk_produtos_id = p.id
                LEFT JOIN responsaveis r ON e.fk_responsaveis_id = r.id
                WHERE DATE(e.datareg) BETWEEN :data_inicio AND :data_fim
            ";
            if ($produto_id && $produto_id != '') {
                $query .= " AND e.fk_produtos_id = :produto_id ";
            }
            $query .= " ORDER BY e.datareg DESC, e.id DESC";

            $stmt = $pdo->prepare($query);
            if (!$stmt) {
                throw new Exception('Erro ao preparar consulta SQL');
            }

            $stmt->bindParam(':data_inicio', $data_inicio);
            $stmt->bindParam(':data_fim', $data_fim);
            if ($produto_id && $produto_id != '') {
                $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
            }

            $stmt->execute();
            $movimentacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Debug: verificar se há dados
            if (empty($movimentacoes)) {
                // Log para debug
                error_log("Relatório por produto: Nenhuma movimentação encontrada para período: $data_inicio a $data_fim, produto_id: $produto_id");
            }

            // Criar PDF personalizado (mesmo padrão dos outros relatórios)
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
            $larguraDisponivel = $pdf->GetPageWidth() - 300; // Deixando espaço para logo e data
            $pdf->SetXY(40 + $logoWidth + 5, 30); // Reduzindo o espaçamento de 15 para 5
            $pdf->Cell($larguraDisponivel, 24, utf8_decode("RELATÓRIO DE MOVIMENTAÇÃO POR PRODUTO E DATA"), 0, 1, 'L');

            $pdf->SetFont('Arial', '', 12);
            $pdf->SetXY(40 + $logoWidth + 5, $pdf->GetY()); // Reduzindo o espaçamento de 15 para 5
            $pdf->Cell($larguraDisponivel, 10, utf8_decode("EEEP Salaberga Torquato Gomes de Matos"), 0, 1, 'L');



            // ===== RESUMO DE DADOS EM CARDS =====
            $totalMovimentacoes = count($movimentacoes);
            $totalQuantidade = array_sum(array_column($movimentacoes, 'quantidade'));
            $produtosUnicos = count(array_unique(array_column($movimentacoes, 'nome_produto')));

            // Criar cards para os resumos
            $cardWidth = 200;
            $cardHeight = 80;
            $cardMargin = 20;
            $startX = ($pdf->GetPageWidth() - (3 * $cardWidth + 2 * $cardMargin)) / 2;
            $startY = 110;

            // Card 1 - Total de Movimentações
            $pdf->SetFillColor($corBranco[0], $corBranco[1], $corBranco[2]);
            $pdf->RoundedRect($startX, $startY, $cardWidth, $cardHeight, 8, 'F');
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetTextColor($corPreto[0], $corPreto[1], $corPreto[2]);
            $pdf->SetXY($startX + 15, $startY + 15);
            $pdf->Cell($cardWidth - 30, 20, utf8_decode("TOTAL DE MOVIMENTAÇÕES"), 0, 1, 'L');
            $pdf->SetFont('Arial', 'B', 24);
            $pdf->SetTextColor($corPrimary[0], $corPrimary[1], $corPrimary[2]);
            $pdf->SetXY($startX + 15, $startY + 40);
            $pdf->Cell($cardWidth - 30, 25, $totalMovimentacoes, 0, 1, 'L');

            // Card 2 - Total Retirado
            $pdf->SetFillColor($corBranco[0], $corBranco[1], $corBranco[2]);
            $pdf->RoundedRect($startX + $cardWidth + $cardMargin, $startY, $cardWidth, $cardHeight, 8, 'F');
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetTextColor($corPreto[0], $corPreto[1], $corPreto[2]);
            $pdf->SetXY($startX + $cardWidth + $cardMargin + 15, $startY + 15);
            $pdf->Cell($cardWidth - 30, 20, utf8_decode("TOTAL RETIRADO"), 0, 1, 'L');
            $pdf->SetFont('Arial', 'B', 24);
            $pdf->SetTextColor($corAlerta[0], $corAlerta[1], $corAlerta[2]);
            $pdf->SetXY($startX + $cardWidth + $cardMargin + 15, $startY + 40);
            $pdf->Cell($cardWidth - 30, 25, $totalQuantidade, 0, 1, 'L');

            // Card 3 - Produtos Únicos
            $pdf->SetFillColor($corBranco[0], $corBranco[1], $corBranco[2]);
            $pdf->RoundedRect($startX + 2 * ($cardWidth + $cardMargin), $startY, $cardWidth, $cardHeight, 8, 'F');
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetTextColor($corPreto[0], $corPreto[1], $corPreto[2]);
            $pdf->SetXY($startX + 2 * ($cardWidth + $cardMargin) + 15, $startY + 15);
            $pdf->Cell($cardWidth - 30, 20, utf8_decode("PRODUTOS ÚNICOS"), 0, 1, 'L');
            $pdf->SetFont('Arial', 'B', 24);
            $pdf->SetTextColor($corSecondary[0], $corSecondary[1], $corSecondary[2]);
            $pdf->SetXY($startX + 2 * ($cardWidth + $cardMargin) + 15, $startY + 40);
            $pdf->Cell($cardWidth - 30, 25, $produtosUnicos, 0, 1, 'L');

            // ===== TABELA DE MOVIMENTAÇÕES =====
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
            $pdf->Cell($larguraPagina - 30, 15, utf8_decode("DETALHAMENTO DAS MOVIMENTAÇÕES"), 0, 1, 'L');

            $y += 35;

            // Cabeçalhos das colunas
            $pdf->SetFillColor($corDark[0], $corDark[1], $corDark[2]);
            $pdf->SetTextColor($corBranco[0], $corBranco[1], $corBranco[2]);
            $pdf->SetFont('Arial', 'B', 10);

            $colunas = array('ID', 'Data', 'Código', 'Produto', 'Qtd.', 'Responsável', 'Cargo');
            $larguras = array(
                round($larguraPagina * 0.08),  // ID
                round($larguraPagina * 0.12),  // Data
                round($larguraPagina * 0.18),  // Código
                round($larguraPagina * 0.25),  // Produto
                round($larguraPagina * 0.10),  // Quantidade
                round($larguraPagina * 0.15),  // Responsável
                round($larguraPagina * 0.12)   // Cargo
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

            if (empty($movimentacoes)) {
                // Mensagem quando não há movimentações
                $pdf->SetXY($margemTabela, $y);
                $pdf->SetFont('Arial', 'I', 12);
                $pdf->SetTextColor($corTextoSubtil[0], $corTextoSubtil[1], $corTextoSubtil[2]);
                $pdf->SetFillColor(250, 250, 250);
                $pdf->RoundedRect($margemTabela, $y, array_sum($larguras), 40, 5, 'FD');
                $pdf->SetXY($margemTabela, $y + 12);
                $pdf->Cell(array_sum($larguras), 16, utf8_decode("Nenhuma movimentação encontrada para o período selecionado"), 0, 1, 'C');
            } else {
                foreach ($movimentacoes as $idx => $mov) {
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
                    $pdf->Cell($larguras[0], 15, $mov['id'], 0, 0, 'C');
                    $posX += $larguras[0];

                    // Data
                    $pdf->Rect($posX, $y, $larguras[1], 20, 'FD');
                    $pdf->SetXY($posX + 5, $y + 5);
                    $pdf->Cell($larguras[1] - 10, 15, date('d/m/Y', strtotime($mov['data'])), 0, 0, 'C');
                    $posX += $larguras[1];

                    // Barcode
                    $pdf->Rect($posX, $y, $larguras[2], 20, 'FD');
                    $pdf->SetXY($posX + 5, $y + 5);
                    $pdf->Cell($larguras[2] - 10, 15, $mov['barcode_produto'] ?? 'N/A', 0, 0, 'L');
                    $posX += $larguras[2];

                    // Nome do produto
                    $pdf->Rect($posX, $y, $larguras[3], 20, 'FD');
                    $pdf->SetXY($posX + 5, $y + 5);
                    $nomeProduto = utf8_decode($mov['nome_produto'] ?? 'N/A');
                    if (strlen($nomeProduto) > 35) {
                        $nomeProduto = substr($nomeProduto, 0, 32) . '...';
                    }
                    $pdf->Cell($larguras[3] - 10, 15, $nomeProduto, 0, 0, 'L');
                    $posX += $larguras[3];

                    // Quantidade
                    $pdf->Rect($posX, $y, $larguras[4], 20, 'FD');
                    $pdf->SetXY($posX, $y + 5);
                    $pdf->SetTextColor($corAlerta[0], $corAlerta[1], $corAlerta[2]);
                    $pdf->SetFont('Arial', 'B', 9);
                    $pdf->Cell($larguras[4], 15, $mov['quantidade'], 0, 0, 'C');
                    $pdf->SetTextColor($corPreto[0], $corPreto[1], $corPreto[2]);
                    $pdf->SetFont('Arial', '', 9);
                    $posX += $larguras[4];

                    // Responsável
                    $pdf->Rect($posX, $y, $larguras[5], 20, 'FD');
                    $pdf->SetXY($posX + 5, $y + 5);
                    $pdf->Cell($larguras[5] - 10, 15, utf8_decode($mov['nome_responsavel'] ?? 'N/A'), 0, 0, 'L');
                    $posX += $larguras[5];

                    // Cargo
                    $pdf->Rect($posX, $y, $larguras[6], 20, 'FD');
                    $pdf->SetXY($posX + 5, $y + 5);
                    $pdf->Cell($larguras[6] - 10, 15, utf8_decode($mov['cargo'] ?? 'N/A'), 0, 0, 'L');

                    $y += 25;
                    $linhaAlternada = !$linhaAlternada;
                }
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
            $pdf->Output("relatorio_movimentacao_produto_data.pdf", "I");
        } catch (Exception $e) {
            // Em caso de erro, redirecionar com mensagem
            header('Location: ../view/relatorios.php?error=1&message=' . urlencode('Erro ao gerar relatório: ' . $e->getMessage()));
            exit;
        }
    }
}
// Handle the request
if (isset($_GET['produto']) && isset($_GET['data_inicio']) && isset($_GET['data_fim'])) {
    $controller = new relatorios();
    $controller->relatorioEstoqueProduto($_GET['data_inicio'], $_GET['data_fim'], $_GET['produto']);
} else {
    echo "Erro: Parâmetros de data não fornecidos.";
    exit;
}
