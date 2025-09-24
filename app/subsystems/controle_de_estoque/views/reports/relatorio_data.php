<?php
require_once(__DIR__ . '\..\..\models\sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require("../../config/connect.php");
require("../../assets/libs/FPDF/fpdf.php");

class relatorios extends connect{

    function __construct(){
        parent::__construct();
        $this->relatoriocriticostoque();

    }
public function relatorioEstoquePorData($data_inicio, $data_fim)
    {
        try {
            // Consulta com JOIN, incluindo quantidade_retirada
            $consulta = "SELECT e.id, e.fk_produtos_id, e.fk_responsaveis_id, e.barcode_produto, e.datareg, 
                            e.quantidade_retirada,
                            p.nome_produto AS nome_produto, r.nome AS nome_responsavel, r.cargo AS cargo
                     FROM movimentacao e
                     LEFT JOIN produtos p ON e.fk_produtos_id = p.id
                     LEFT JOIN responsaveis r ON e.fk_responsaveis_id = r.id
                     WHERE e.datareg BETWEEN :data_inicio AND :data_fim 
                     ORDER BY e.fk_produtos_id, e.barcode_produto";
            $query = $this->pdo->prepare($consulta);
            $query->bindParam(':data_inicio', $data_inicio);
            $query->bindParam(':data_fim', $data_fim);
            $query->execute();
            $result = $query->rowCount();

            // Criar PDF personalizado
            $pdf = new PDF("L", "pt", "A4");
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
            $pdf->SetFont('Arial', 'B', 24);
            $pdf->SetTextColor($corBranco[0], $corBranco[1], $corBranco[2]);
            $pdf->Cell(0, 24, utf8_decode("RELATÓRIO DE MOVIMENTAÇÃO POR DATA"), 0, 1, 'L');

            $pdf->SetFont('Arial', '', 12);
            $pdf->SetXY(40 + $logoWidth + 15, $pdf->GetY());
            $pdf->Cell(0, 15, utf8_decode("EEEP Salaberga Torquato Gomes de Matos"), 0, 1, 'L');

            // Data de geração e período
            $pdf->SetXY($pdf->GetPageWidth() - 200, 30);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(160, 15, utf8_decode("Gerado no dia: " . date("d/m/Y", time())), 0, 1, 'R');
            $pdf->SetXY($pdf->GetPageWidth() - 200, 45);
            $pdf->Cell(160, 15, utf8_decode("Período: " . date("d/m/Y", strtotime($data_inicio)) . " a " . date("d/m/Y", strtotime($data_fim))), 0, 1, 'R');

            // ===== RESUMO DE DADOS EM CARDS =====
            $consultaResumo = "SELECT 
            COUNT(*) as total_itens,
            COUNT(DISTINCT fk_produtos_id) as total_produtos,
            SUM(quantidade_retirada) as total_retirado
            FROM movimentacao WHERE datareg BETWEEN :data_inicio AND :data_fim";
            $queryResumo = $this->pdo->prepare($consultaResumo);
            $queryResumo->bindParam(':data_inicio', $data_inicio);
            $queryResumo->bindParam(':data_fim', $data_fim);
            $queryResumo->execute();
            $resumo = $queryResumo->fetch(PDO::FETCH_ASSOC);

            // Criar cards para os resumos
            $cardWidth = 200;
            $cardHeight = 80;
            $cardMargin = 20;
            $startX = ($pdf->GetPageWidth() - (3 * $cardWidth + 2 * $cardMargin)) / 2;
            $startY = 110;

            // Card 1 - Total Itens
            $pdf->SetFillColor($corBranco[0], $corBranco[1], $corBranco[2]);
            $pdf->RoundedRect($startX, $startY, $cardWidth, $cardHeight, 8, 'F');
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetTextColor($corPreto[0], $corPreto[1], $corPreto[2]);
            $pdf->SetXY($startX + 15, $startY + 15);
            $pdf->Cell($cardWidth - 30, 20, utf8_decode("TOTAL DE MOVIMENTAÇÕES"), 0, 1, 'L');
            $pdf->SetFont('Arial', 'B', 24);
            $pdf->SetTextColor($corPrimary[0], $corPrimary[1], $corPrimary[2]);
            $pdf->SetXY($startX + 15, $startY + 40);
            $pdf->Cell($cardWidth - 30, 25, $resumo['total_itens'], 0, 1, 'L');

            // Card 2 - Total Produtos
            $pdf->SetFillColor($corBranco[0], $corBranco[1], $corBranco[2]);
            $pdf->RoundedRect($startX + $cardWidth + $cardMargin, $startY, $cardWidth, $cardHeight, 8, 'F');
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetTextColor($corPreto[0], $corPreto[1], $corPreto[2]);
            $pdf->SetXY($startX + $cardWidth + $cardMargin + 15, $startY + 15);
            $pdf->Cell($cardWidth - 30, 20, utf8_decode("PRODUTOS DIFERENTES"), 0, 1, 'L');
            $pdf->SetFont('Arial', 'B', 24);
            $pdf->SetTextColor($corSecondary[0], $corSecondary[1], $corSecondary[2]);
            $pdf->SetXY($startX + $cardWidth + $cardMargin + 15, $startY + 40);
            $pdf->Cell($cardWidth - 30, 25, $resumo['total_produtos'], 0, 1, 'L');

            // Card 3 - Total Retirado
            $pdf->SetFillColor($corBranco[0], $corBranco[1], $corBranco[2]);
            $pdf->RoundedRect($startX + 2 * ($cardWidth + $cardMargin), $startY, $cardWidth, $cardHeight, 8, 'F');
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetTextColor($corPreto[0], $corPreto[1], $corPreto[2]);
            $pdf->SetXY($startX + 2 * ($cardWidth + $cardMargin) + 15, $startY + 15);
            $pdf->Cell($cardWidth - 30, 20, utf8_decode("TOTAL RETIRADO"), 0, 1, 'L');
            $pdf->SetFont('Arial', 'B', 24);
            $pdf->SetTextColor($corAlerta[0], $corAlerta[1], $corAlerta[2]);
            $pdf->SetXY($startX + 2 * ($cardWidth + $cardMargin) + 15, $startY + 40);
            $pdf->Cell($cardWidth - 30, 25, $resumo['total_retirado'], 0, 1, 'L');

            // ===== TÍTULO DA TABELA =====
            $pdf->SetY($startY + $cardHeight + 40);
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->SetTextColor($corPrimary[0], $corPrimary[1], $corPrimary[2]);
            $pdf->Cell(0, 20, utf8_decode("DETALHAMENTO DAS MOVIMENTAÇÕES"), 0, 1, 'C');

            // ===== TABELA DE MOVIMENTAÇÕES COM MELHOR DESIGN =====
            $margemTabela = 40;
            $larguraDisponivel = $pdf->GetPageWidth() - (2 * $margemTabela);

            // Definindo colunas e larguras proporcionais
            $colunas = array('ID', 'Código', 'Produto', 'Qtd. Retirada', 'Responsável', 'Cargo', 'Data');
            $larguras = array(
                round($larguraDisponivel * 0.06),  // ID
                round($larguraDisponivel * 0.12),  // Código
                round($larguraDisponivel * 0.25),  // Produto
                round($larguraDisponivel * 0.10),  // Qtd. Retirada
                round($larguraDisponivel * 0.20),  // Responsável
                round($larguraDisponivel * 0.12),  // Cargo
                round($larguraDisponivel * 0.15)   // Data
            );

            $pdf->SetXY($margemTabela, $pdf->GetY() + 10);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetFillColor($corPrimary[0], $corPrimary[1], $corPrimary[2]);
            $pdf->SetTextColor($corBranco[0], $corBranco[1], $corBranco[2]);
            $pdf->SetDrawColor(220, 220, 220);

            // Cabeçalho da tabela com arredondamento personalizado
            $alturaLinha = 30;
            $posX = $margemTabela;

            // Célula de cabeçalho com primeiro canto arredondado (esquerda superior)
            $pdf->RoundedRect($posX, $pdf->GetY(), $larguras[0], $alturaLinha, 5, 'FD', '1');
            $pdf->SetXY($posX, $pdf->GetY());
            $pdf->Cell($larguras[0], $alturaLinha, utf8_decode($colunas[0]), 0, 0, 'C');
            $posX += $larguras[0];

            // Células de cabeçalho intermediárias
            for ($i = 1; $i < count($colunas) - 1; $i++) {
                $pdf->Rect($posX, $pdf->GetY(), $larguras[$i], $alturaLinha, 'FD');
                $pdf->SetXY($posX, $pdf->GetY());
                $pdf->Cell($larguras[$i], $alturaLinha, utf8_decode($colunas[$i]), 0, 0, 'C');
                $posX += $larguras[$i];
            }

            // Última célula com canto arredondado (direita superior)
            $pdf->RoundedRect($posX, $pdf->GetY(), $larguras[count($colunas) - 1], $alturaLinha, 5, 'FD', '2');
            $pdf->SetXY($posX, $pdf->GetY());
            $pdf->Cell($larguras[count($colunas) - 1], $alturaLinha, utf8_decode($colunas[count($colunas) - 1]), 0, 0, 'C');

            $pdf->Ln($alturaLinha);

            // Dados da tabela
            $y = $pdf->GetY();
            $linhaAlternada = false;
            $alturaLinhaDados = 24;

            $query->execute();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                // Verificar se é necessário adicionar nova página
                if ($y + $alturaLinhaDados > $pdf->GetPageHeight() - 60) {
                    $pdf->AddPage();
                    $y = 40;
                }

                // Cor de fundo alternada para linhas
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
                $pdf->Rect($posX, $y, $larguras[0], $alturaLinhaDados, 'FD');
                $pdf->SetXY($posX, $y + 5);
                $pdf->Cell($larguras[0], 15, $row['id'], 0, 0, 'C');
                $posX += $larguras[0];

                // Código
                $pdf->Rect($posX, $y, $larguras[1], $alturaLinhaDados, 'FD');
                $pdf->SetXY($posX + 5, $y + 5);
                $pdf->Cell($larguras[1] - 10, 15, $row['barcode_produto'], 0, 0, 'C');
                $posX += $larguras[1];

                // Produto
                $pdf->Rect($posX, $y, $larguras[2], $alturaLinhaDados, 'FD');
                $pdf->SetXY($posX + 5, $y + 5);
                $nomeProduto = utf8_decode($row['nome_produto']);
                if (strlen($nomeProduto) > 40) {
                    $nomeProduto = substr($nomeProduto, 0, 37) . '...';
                }
                $pdf->Cell($larguras[2] - 10, 15, $nomeProduto, 0, 0, 'L');
                $posX += $larguras[2];

                // Quantidade Retirada
                $pdf->Rect($posX, $y, $larguras[3], $alturaLinhaDados, 'FD');
                $pdf->SetXY($posX, $y + 5);
                $pdf->SetTextColor($corAlerta[0], $corAlerta[1], $corAlerta[2]);
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell($larguras[3], 15, $row['quantidade_retirada'], 0, 0, 'C');
                $pdf->SetTextColor($corPreto[0], $corPreto[1], $corPreto[2]);
                $pdf->SetFont('Arial', '', 9);
                $posX += $larguras[3];

                // Responsável
                $pdf->Rect($posX, $y, $larguras[4], $alturaLinhaDados, 'FD');
                $pdf->SetXY($posX + 5, $y + 5);
                $pdf->Cell($larguras[4] - 10, 15, utf8_decode($row['nome_responsavel']), 0, 0, 'L');
                $posX += $larguras[4];

                // Cargo
                $pdf->Rect($posX, $y, $larguras[5], $alturaLinhaDados, 'FD');
                $pdf->SetXY($posX + 5, $y + 5);
                $pdf->Cell($larguras[5] - 10, 15, utf8_decode($row['cargo']), 0, 0, 'L');
                $posX += $larguras[5];

                // Data
                $pdf->Rect($posX, $y, $larguras[6], $alturaLinhaDados, 'FD');
                $pdf->SetXY($posX + 5, $y + 5);
                $pdf->Cell($larguras[6] - 10, 15, date('d/m/Y H:i', strtotime($row['datareg'])), 0, 0, 'C');

                $y += $alturaLinhaDados;
                $linhaAlternada = !$linhaAlternada;
            }

            // ===== RODAPÉ =====
            $pdf->SetY($pdf->GetPageHeight() - 60);
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetTextColor($corTextoSubtil[0], $corTextoSubtil[1], $corTextoSubtil[2]);
            $pdf->Cell(0, 15, utf8_decode("Relatório gerado automaticamente pelo sistema STGM Estoque"), 0, 1, 'C');
            $pdf->Cell(0, 15, utf8_decode("Período: " . date("d/m/Y", strtotime($data_inicio)) . " a " . date("d/m/Y", strtotime($data_fim))), 0, 1, 'C');

            $pdf->Output("relatorio_movimentacao_" . date("Y-m-d") . ".pdf", "D");
        } catch (PDOException $e) {
            error_log("Erro no relatório por data: " . $e->getMessage());
            echo "Erro ao gerar relatório: " . $e->getMessage();
        }
    }
}
// Handle the request
if (isset($_GET['data_inicio']) && isset($_GET['data_fim'])) {
    $data_inicio = $_GET['data_inicio'];
    $data_fim = $_GET['data_fim'];
    
    $relatorios = new relatorios();
    

        $relatorios->relatorioEstoquePorData($data_inicio, $data_fim);
    
}
?>