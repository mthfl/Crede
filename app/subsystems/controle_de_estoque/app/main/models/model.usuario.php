<?php
require_once(__DIR__ . '\..\config\connect.php');
require_once('sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

class usuario extends connect
{
    private string $table1;
    private string $table2;
    private string $table3;
    private string $table4;
    private string $table5;

    function __construct()
    {
        parent::__construct();
        require('private/tables.php');
        $this->table1 = $table['crede_estoque'][1];
        $this->table2 = $table['crede_estoque'][2];
        $this->table3 = $table['crede_estoque'][3];
        $this->table4 = $table['crede_estoque'][4];
        $this->table5 = $table['crede_estoque'][5];
    }

    public function cadastrar_produto($barcode, string $nome, int $quantidade, int $id_categoria, string $validade): int
    {
        $consulta = "SELECT * FROM $this->table4 WHERE nome_produto = :nome";
        $query = $this->connect->prepare($consulta);
        $query->bindValue(":nome", $nome);
        $query->execute();

        if ($query->rowCount() <= 0) {
            date_default_timezone_set('America/Fortaleza');
            $data = date('Y-m-d H:i:s');

            $consulta = "INSERT INTO $this->table4 VALUES (null, :barcode, :nome, :quantidade, :id_categoria,:validade, :data)";
            $query = $this->connect->prepare($consulta);
            $query->bindValue(":nome", $nome);
            $query->bindValue(":barcode", $barcode);
            $query->bindValue(":quantidade", $quantidade);
            $query->bindValue(":id_categoria", $id_categoria);
            $query->bindValue(":validade", $validade);
            $query->bindValue(":data", $data);

            if ($query->execute()) {
                return 1;
            } else {
                return 2;
            }

        } else {
            return 3;
        }
    }
    public function verificar_produto_barcode(int $barcode): bool
    {
        $stmt_check = $this->connect->prepare("SELECT * FROM $this->table4 WHERE barcode = :barcode");
        $stmt_check->bindParam(':barcode', $barcode);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {

            return true;
        } else {

            return false;
        }
    }
    public function verificar_produto_nome(string $nome): bool
    {
        $stmt_check = $this->connect->prepare("SELECT * FROM $this->table4 WHERE nome_produto = :nome");
        $stmt_check->bindParam(':nome', $nome);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {

            return true;
        } else {

            return false;
        }
    }
    public function registrar_perda(
        $id_produto,
        $quantidade,
        $tipo_perda,
        $data_perda
    ) {

        $stmt_check = $this->connect->prepare("SELECT * FROM produtos WHERE id = :id");
        $stmt_check->bindParam(':id', $id_produto);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {

            $dados = $stmt_check->fetch(PDO::FETCH_ASSOC);
            $nova_quantidade = $dados['quantidade'] - $quantidade;
            $stmt_registrar = $this->connect->prepare("UPDATE `produtos` SET quantidade = :quantidade WHERE id = :id");
            $stmt_registrar->bindParam(':id', $id_produto);
            $stmt_registrar->bindParam(':quantidade', $nova_quantidade);
            $stmt_registrar->execute();

            $stmt_registrar = $this->connect->prepare("INSERT INTO perdas_produtos VALUES(null, :id_produto, :quantidade, :tipo, :data_perda)");
            $stmt_registrar->bindParam(':id_produto', $id_produto);
            $stmt_registrar->bindParam(':quantidade', $quantidade);
            $stmt_registrar->bindParam(':tipo', $tipo_perda);
            $stmt_registrar->bindParam(':data_perda', $data_perda);
            $stmt_registrar->execute();

            if ($stmt_registrar) {

                return 1;
            } else {

                return 2;
            }
        } else {

            return 3;
        }
    }

    public function cadastrar_categoria(string $categoria): int
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table1 WHERE nome_categoria = :nome");
            $stmt_check->bindValue(":nome", $categoria);
            $stmt_check->execute();

            if ($stmt_check->rowCount() <= 0) {

                $stmt_check = $this->connect->prepare("INSERT INTO $this->table1 VALUES(NULL, :nome)");
                $stmt_check->bindValue(":nome", $categoria);

                if ($stmt_check->execute()) {

                    return 1;
                } else {
                    return 2;
                }
            } else {

                return 3;
            }
        } catch (Exception $e) {

            return 0;
        }
    }

    public function adicionar_produto($barcode, $quantidade)
    {
        $consulta = "UPDATE produtos SET quantidade = quantidade + :quantidade WHERE barcode = :barcode";
        $stmt_adicionar = $this->connect->prepare($consulta);
        $stmt_adicionar->bindValue(":quantidade", $quantidade);
        $stmt_adicionar->bindValue(":barcode", $barcode);

        if ($stmt_adicionar->execute()) {

            return 1;
        } else {
            return 2;
        }
    }
    public function consultarestoque($barcode)
    {
        $consulta = "SELECT quantidade FROM produtos WHERE barcode = :barcode";
        $query = $this->pdo->prepare($consulta);
        $query->bindValue(":barcode", $barcode);
        $query->execute();
        $produto = $query->fetch(PDO::FETCH_ASSOC);

        if ($produto) {
            header("location: ../view/adcprodutoexistente.php?barcode=" . urlencode($barcode));
        } else {
            header("location: ../view/adcnovoproduto.php?barcode=" . urlencode($barcode));
        }
    }

    public function buscarProdutoPorBarcode($barcode)
    {
        try {
            error_log("Buscando produto com barcode: " . $barcode);

            $consulta = "SELECT id, barcode, nome_produto, quantidade, natureza FROM produtos WHERE barcode = :barcode";
            $query = $this->pdo->prepare($consulta);
            $query->bindValue(":barcode", $barcode);
            $query->execute();
            $produto = $query->fetch(PDO::FETCH_ASSOC);

            if ($produto) {
                error_log("Produto encontrado: " . json_encode($produto));
            } else {
                error_log("Produto não encontrado para barcode: " . $barcode);
            }

            return $produto;
        } catch (PDOException $e) {
            error_log("Erro ao buscar produto por barcode: " . $e->getMessage());
            return false;
        }
    }

    public function consultarProdutoSemCodigo($nome_produto)
    {
        // Verificar se já tem prefixo SCB_
        if (strpos($nome_produto, 'SCB_') === 0) {
            // Já tem prefixo SCB_, usar como está
            $barcode_com_prefixo = $nome_produto;
        } else {
            // Adicionar prefixo SCB_ para produtos sem código
            $barcode_com_prefixo = 'SCB_' . $nome_produto;
        }

        // Verificar se já existe um produto com este nome como barcode
        $consulta = "SELECT quantidade FROM produtos WHERE barcode = :barcode_com_prefixo";
        $query = $this->pdo->prepare($consulta);
        $query->bindValue(":barcode_com_prefixo", $barcode_com_prefixo);
        $query->execute();
        $produto = $query->fetch(PDO::FETCH_ASSOC);

        if ($produto) {
            // Produto já existe, redirecionar para adicionar quantidade
            header("location: ../view/adcprodutoexistente.php?barcode=" . urlencode($barcode_com_prefixo));
        } else {
            // Produto não existe, redirecionar para cadastrar novo produto
            header("location: ../view/adcnovoproduto.php?barcode=" . urlencode($barcode_com_prefixo));
        }
    }

    public function adcaoestoquePorNome($nome_produto, $quantidade)
    {
        // Verificar se já tem prefixo SCB_
        if (strpos($nome_produto, 'SCB_') === 0) {
            // Já tem prefixo SCB_, usar como está
            $barcode_com_prefixo = $nome_produto;
        } else {
            // Adicionar prefixo SCB_ para produtos sem código
            $barcode_com_prefixo = 'SCB_' . $nome_produto;
        }

        $consulta = "UPDATE produtos SET quantidade = quantidade + :quantidade WHERE barcode = :barcode_com_prefixo";
        $query = $this->pdo->prepare($consulta);
        $query->bindValue(":quantidade", $quantidade);
        $query->bindValue(":barcode_com_prefixo", $barcode_com_prefixo);
        $query->execute();

        header("location:../view/estoque.php");
    }

    public function solicitar_produto_id($valor_retirada, $id_produto, $id_retirante, $datetime, $usuario)
    {
        try {

            $consultaProduto = "SELECT * FROM produtos WHERE id = :id";
            $queryProduto = $this->connect->prepare($consultaProduto);
            $queryProduto->bindValue(":id", $id_produto);
            $queryProduto->execute();
            $produto = $queryProduto->fetch(PDO::FETCH_ASSOC);
            $barcode_produto = $produto['barcode'];

            if ($produto['quantidade'] < $valor_retirada) {
                return 3;
            }

            $consultaUpdate = "UPDATE produtos SET quantidade = quantidade - :valor_retirada WHERE id = :id";
            $queryUpdate = $this->connect->prepare($consultaUpdate);
            $queryUpdate->bindValue(":valor_retirada", $valor_retirada, PDO::PARAM_INT);
            $queryUpdate->bindValue(":id", $id_produto);
            $queryUpdate->execute();

            $consultaInsert = "INSERT INTO movimentacao VALUES (NULL, :id_produto, :usuario, :id_responsaveis, :datareg, :barcode_produto, :quantidade_retirada)";
            $queryInsert = $this->connect->prepare($consultaInsert);
            $queryInsert->bindValue(":id_produto", $id_produto);
            $queryInsert->bindValue(":usuario", $usuario);
            $queryInsert->bindValue(":id_responsaveis", $id_retirante);
            $queryInsert->bindValue(":datareg", $datetime);
            $queryInsert->bindValue(":barcode_produto", $barcode_produto);
            $queryInsert->bindValue(":quantidade_retirada", $valor_retirada);

            if ($queryInsert->execute()) {
                return 1;
            } else {
                return 2;
            }
        } catch (PDOException $e) {

            return 0;
        }
    }

    public function solicitar_produto_barcode($valor_retirada, $barcode, $id_retirante, $datetime,  $usuario)
    {
        try {

            $consultaProduto = "SELECT * FROM produtos WHERE barcode = :barcode";
            $queryProduto = $this->connect->prepare($consultaProduto);
            $queryProduto->bindValue(":barcode", $barcode);
            $queryProduto->execute();
            $produto = $queryProduto->fetch(PDO::FETCH_ASSOC);
            $id_produto = $produto['id'];

            if ($produto['quantidade'] < $valor_retirada) {
                return 3;
            }

            $consultaUpdate = "UPDATE produtos SET quantidade = quantidade - :valor_retirada WHERE id = :id";
            $queryUpdate = $this->connect->prepare($consultaUpdate);
            $queryUpdate->bindValue(":valor_retirada", $valor_retirada, PDO::PARAM_INT);
            $queryUpdate->bindValue(":id", $id_produto);
            $queryUpdate->execute();

            $consultaInsert = "INSERT INTO movimentacao VALUES (NULL, :id_produto, :usuario, :id_responsaveis, :datareg, :barcode_produto, :quantidade_retirada)";
            $queryInsert = $this->connect->prepare($consultaInsert);
            $queryInsert->bindValue(":id_produto", $id_produto);
            $queryInsert->bindValue(":usuario", $usuario);
            $queryInsert->bindValue(":id_responsaveis", $id_retirante);
            $queryInsert->bindValue(":datareg", $datetime);
            $queryInsert->bindValue(":barcode_produto", $barcode);
            $queryInsert->bindValue(":quantidade_retirada", $valor_retirada);

            if ($queryInsert->execute()) {
                return 1;
            } else {
                return 2;
            }
        } catch (PDOException $e) {

            return 0;
        }
    }
    public function editarProduto($id, $nome, $barcode, $quantidade, $natureza)
    {
        try {

            $consulta = "UPDATE produtos SET barcode = :barcode, nome_produto = :nome, quantidade = :quantidade, natureza = :natureza WHERE id = :id";
            $query = $this->pdo->prepare($consulta);
            $query->bindValue(":id", $id);
            $query->bindValue(":barcode", $barcode);
            $query->bindValue(":nome", $nome);
            $query->bindValue(":quantidade", $quantidade);
            $query->bindValue(":natureza", $natureza);

            $resultado = $query->execute();
            $linhasAfetadas = $query->rowCount();

            error_log("Query executada com sucesso");
            error_log("Linhas afetadas: " . $linhasAfetadas);

            if ($linhasAfetadas > 0) {
                error_log("Produto editado com sucesso");
                return true;
            } else {
                error_log("Nenhuma linha foi afetada - produto pode não existir");
                return false;
            }
        } catch (PDOException $e) {
            error_log("Erro ao editar produto: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function apagarProduto($id)
    {
        try {
            error_log("=== INICIANDO EXCLUSÃO DE PRODUTO ===");
            error_log("ID do produto: " . $id);

            // Verificar se o produto existe antes de tentar excluir
            $consultaVerificar = "SELECT id, nome_produto FROM produtos WHERE id = :id";
            $queryVerificar = $this->pdo->prepare($consultaVerificar);
            $queryVerificar->bindValue(":id", $id);
            $queryVerificar->execute();
            $produto = $queryVerificar->fetch(PDO::FETCH_ASSOC);

            if (!$produto) {
                error_log("Produto não encontrado com ID: " . $id);
                return false;
            }

            error_log("Produto encontrado: " . $produto['nome_produto']);

            // Verificar se há movimentações relacionadas
            $consultaMovimentacoes = "SELECT COUNT(*) as total FROM movimentacao WHERE fk_produtos_id = :id";
            $queryMovimentacoes = $this->pdo->prepare($consultaMovimentacoes);
            $queryMovimentacoes->bindValue(":id", $id);
            $queryMovimentacoes->execute();
            $movimentacoes = $queryMovimentacoes->fetch(PDO::FETCH_ASSOC);

            error_log("Movimentações relacionadas: " . $movimentacoes['total']);

            // Excluir movimentações relacionadas primeiro (se houver)
            if ($movimentacoes['total'] > 0) {
                error_log("Excluindo movimentações relacionadas...");
                $consultaDeleteMovimentacoes = "DELETE FROM movimentacao WHERE fk_produtos_id = :id";
                $queryDeleteMovimentacoes = $this->pdo->prepare($consultaDeleteMovimentacoes);
                $queryDeleteMovimentacoes->bindValue(":id", $id);
                $queryDeleteMovimentacoes->execute();
                error_log("Movimentações excluídas com sucesso");
            }

            // Excluir o produto
            $consulta = "DELETE FROM produtos WHERE id = :id";
            $query = $this->pdo->prepare($consulta);
            $query->bindValue(":id", $id);
            $resultado = $query->execute();
            $linhasAfetadas = $query->rowCount();

            error_log("Query de exclusão executada");
            error_log("Linhas afetadas: " . $linhasAfetadas);

            if ($linhasAfetadas > 0) {
                error_log("Produto excluído com sucesso");
                return true;
            } else {
                error_log("Nenhuma linha foi afetada na exclusão");
                return false;
            }
        } catch (PDOException $e) {
            error_log("Erro ao apagar produto: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function buscarProdutoPorId($id)
    {
        try {
            error_log("Buscando produto com ID: " . $id);
            error_log("Tipo do ID: " . gettype($id));

            $consulta = "SELECT * FROM produtos WHERE id = :id";
            $query = $this->pdo->prepare($consulta);
            $query->bindValue(":id", $id, PDO::PARAM_INT);
            $query->execute();

            $resultado = $query->fetch(PDO::FETCH_ASSOC);

            if ($resultado) {
                error_log("Produto encontrado por ID: " . json_encode($resultado));
            } else {
                error_log("Produto não encontrado para ID: " . $id);
            }

            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao buscar produto por ID: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return null;
        }
    }
}

class relatorios extends connect
{
    function __construct()
    {
        parent::__construct();
    }

    public function buscarProdutosPorData($data_inicio, $data_fim)
    {
        try {
            // Buscar produtos cadastrados no período especificado
            $consulta = "SELECT id, barcode, nome_produto, quantidade, natureza, 
                        data as data 
                        FROM produtos 
                        WHERE DATE(data) BETWEEN :data_inicio AND :data_fim
                        ORDER BY data DESC";

            $query = $this->pdo->prepare($consulta);
            $query->bindValue(":data_inicio", $data_inicio);
            $query->bindValue(":data_fim", $data_fim);
            $query->execute();

            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            error_log("Produtos encontrados no período: " . count($resultado));

            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao buscar produtos por data: " . $e->getMessage());
            throw new Exception("Erro ao buscar produtos: " . $e->getMessage());
        }
    }

    public function buscarMovimentacoesPorData($data_inicio, $data_fim)
    {
        try {
            // Buscar movimentações no período especificado
            $consulta = "SELECT e.id, e.fk_produtos_id, e.fk_responsaveis_id, e.barcode_produto, e.datareg, 
                            e.quantidade_retirada,
                            p.nome_produto AS nome_produto, r.nome AS nome_responsavel, r.cargo AS cargo
                     FROM movimentacao e
                     LEFT JOIN produtos p ON e.fk_produtos_id = p.id
                     LEFT JOIN responsaveis r ON e.fk_responsaveis_id = r.id
                     WHERE DATE(e.datareg) BETWEEN :data_inicio AND :data_fim 
                     ORDER BY e.datareg DESC";

            $query = $this->pdo->prepare($consulta);
            $query->bindValue(":data_inicio", $data_inicio);
            $query->bindValue(":data_fim", $data_fim);
            $query->execute();

            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            error_log("Movimentações encontradas no período: " . count($resultado));

            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao buscar movimentações por data: " . $e->getMessage());
            throw new Exception("Erro ao buscar movimentações: " . $e->getMessage());
        }
    }
    public function relatorioestoque()
    {
        $consulta = "SELECT * FROM produtos ORDER BY natureza, nome_produto";
        $query = $this->pdo->prepare($consulta);
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
        // Fundo verde sólido
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
        $pdf->Cell(0, 24, utf8_decode("RELATÓRIO DE ESTOQUE"), 0, 1, 'L');

        $pdf->SetFont('Arial', '', 12);
        $pdf->SetXY(40 + $logoWidth + 15, $pdf->GetY());
        $pdf->Cell(0, 15, utf8_decode("EEEP Salaberga Torquato Gomes de Matos"), 0, 1, 'L');

        // Data de geração
        $pdf->SetXY($pdf->GetPageWidth() - 200, 30);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(160, 15, utf8_decode("Gerado no dia: " . date("d/m/Y", time())), 0, 1, 'R');

        // ===== RESUMO DE DADOS EM CARDS =====
        $consultaResumo = "SELECT 
        COUNT(*) as total_produtos,
            SUM(CASE WHEN quantidade <= 5 THEN 1 ELSE 0 END) as produtos_criticos,
            COUNT(DISTINCT natureza) as total_categorias
        FROM produtos";
        $queryResumo = $this->pdo->prepare($consultaResumo);
        $queryResumo->execute();
        $resumo = $queryResumo->fetch(PDO::FETCH_ASSOC);

        // Criar cards para os resumos
        $cardWidth = 200;
        $cardHeight = 80;
        $cardMargin = 20;
        $startX = ($pdf->GetPageWidth() - (3 * $cardWidth + 2 * $cardMargin)) / 2;
        $startY = 110;

        // Card 1 - Total Produtos Críticos
        $pdf->SetFillColor($corBranco[0], $corBranco[1], $corBranco[2]);
        $pdf->RoundedRect($startX, $startY, $cardWidth, $cardHeight, 8, 'F');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor($corPreto[0], $corPreto[1], $corPreto[2]);
        $pdf->SetXY($startX + 15, $startY + 15);
        $pdf->Cell($cardWidth - 30, 20, utf8_decode("PRODUTOS CRÍTICOS"), 0, 1, 'L');

        // Card 2 - Categorias
        $pdf->SetFillColor($corBranco[0], $corBranco[1], $corBranco[2]);
        $pdf->RoundedRect($startX + $cardWidth + $cardMargin, $startY, $cardWidth, $cardHeight, 8, 'F');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor($corPreto[0], $corPreto[1], $corPreto[2]);
        $pdf->SetXY($startX + $cardWidth + $cardMargin + 15, $startY + 15);
        $pdf->Cell($cardWidth - 30, 20, utf8_decode("CATEGORIAS"), 0, 1, 'L');

        // Card 3 - (Placeholder para futuro uso, mantendo layout com 3 cards)
        $pdf->SetFillColor($corBranco[0], $corBranco[1], $corBranco[2]);
        $pdf->RoundedRect($startX + 2 * ($cardWidth + $cardMargin), $startY, $cardWidth, $cardHeight, 8, 'F');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor($corPreto[0], $corPreto[1], $corPreto[2]);
        $pdf->SetXY($startX + 2 * ($cardWidth + $cardMargin) + 15, $startY + 15);
        $pdf->Cell($cardWidth - 30, 20, utf8_decode("RESERVADO"), 0, 1, 'L');

        $pdf->SetFont('Arial', 'B', 24);
        $pdf->SetTextColor($corTextoSubtil[0], $corTextoSubtil[1], $corTextoSubtil[2]);
        $pdf->SetXY($startX + 2 * ($cardWidth + $cardMargin) + 15, $startY + 40);
        $pdf->Cell($cardWidth - 30, 25, "-", 0, 1, 'L');

        // ===== TABELA DE PRODUTOS COM MELHOR DESIGN =====
        $margemTabela = 40;
        $larguraDisponivel = $pdf->GetPageWidth() - (2 * $margemTabela);

        // Definindo colunas e larguras proporcionais
        $colunas = array('ID', 'Código', 'Produto', 'Quant.');
        $larguras = array(
            round($larguraDisponivel * 0.08), // ID
            round($larguraDisponivel * 0.20), // Código
            round($larguraDisponivel * 0.52), // Produto
            round($larguraDisponivel * 0.20)  // Quantidade
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
        $categoriaAtual = '';
        $linhaAlternada = false;
        $alturaLinhaDados = 24;

        if ($result > 0) {
            foreach ($query as $idx => $row) {
                // Cabeçalho de categoria
                if ($categoriaAtual != $row['natureza']) {
                    $categoriaAtual = $row['natureza'];

                    // Verificar se é necessário adicionar nova página
                    if ($y + 40 > $pdf->GetPageHeight() - 60) {
                        $pdf->AddPage();
                        $pdf->SetDrawColor($corSecondary[0], $corSecondary[1], $corSecondary[2]);
                        $pdf->SetLineWidth(2);
                        $pdf->Line(40, 40, 240, 40);
                        $pdf->SetLineWidth(0.5);
                        $y = 50;
                    } else {
                        $y += 10;
                    }

                    $pdf->SetXY($margemTabela, $y);
                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->SetTextColor($corBranco[0], $corBranco[1], $corBranco[2]);
                    $pdf->SetFillColor($corSecondary[0], $corSecondary[1], $corSecondary[2]);

                    // Cabeçalho de categoria com cantos arredondados
                    $pdf->RoundedRect($margemTabela, $y, array_sum($larguras), 26, 5, 'FD');
                    $pdf->SetXY($margemTabela + 10, $y);
                    $pdf->Cell(array_sum($larguras) - 20, 26, utf8_decode(strtoupper($categoriaAtual)), 0, 1, 'L');

                    $y = $pdf->GetY();
                    $linhaAlternada = false;
                }

                // Cor de fundo alternada para linhas
                if ($linhaAlternada) {
                    $pdf->SetFillColor($corCinzaClaro[0], $corCinzaClaro[1], $corCinzaClaro[2]);
                } else {
                    $pdf->SetFillColor($corBranco[0], $corBranco[1], $corBranco[2]);
                }

                // Verificar se é necessário adicionar nova página
                if ($y + $alturaLinhaDados > $pdf->GetPageHeight() - 60) {
                    $pdf->AddPage();

                    // Redesenhar cabeçalho da tabela na nova página
                    $y = 40;
                    $posX = $margemTabela;
                    $pdf->SetFillColor($corPrimary[0], $corPrimary[1], $corPrimary[2]);
                    $pdf->SetTextColor($corBranco[0], $corBranco[1], $corBranco[2]);

                    // Cabeçalho da tabela
                    $pdf->RoundedRect($posX, $y, $larguras[0], $alturaLinha, 5, 'FD', '1');
                    $pdf->SetXY($posX, $y);
                    $pdf->SetFont('Arial', 'B', 11);
                    $pdf->Cell($larguras[0], $alturaLinha, utf8_decode($colunas[0]), 0, 0, 'C');
                    $posX += $larguras[0];

                    for ($i = 1; $i < count($colunas) - 1; $i++) {
                        $pdf->Rect($posX, $y, $larguras[$i], $alturaLinha, 'FD');
                        $pdf->SetXY($posX, $y);
                        $pdf->Cell($larguras[$i], $alturaLinha, utf8_decode($colunas[$i]), 0, 0, 'C');
                        $posX += $larguras[$i];
                    }

                    $pdf->RoundedRect($posX, $y, $larguras[count($colunas) - 1], $alturaLinha, 5, 'FD', '2');
                    $pdf->SetXY($posX, $y);
                    $pdf->Cell($larguras[count($colunas) - 1], $alturaLinha, utf8_decode($colunas[count($colunas) - 1]), 0, 0, 'C');

                    $pdf->Ln($alturaLinha);
                    $y = $pdf->GetY();

                    // Redesenhar cabeçalho de categoria
                    $pdf->SetXY($margemTabela, $y);
                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->SetTextColor($corBranco[0], $corBranco[1], $corBranco[2]);
                    $pdf->SetFillColor($corSecondary[0], $corSecondary[1], $corSecondary[2]);

                    $pdf->RoundedRect($margemTabela, $y, array_sum($larguras), 26, 5, 'FD');
                    $pdf->SetXY($margemTabela + 10, $y);
                    $pdf->Cell(array_sum($larguras) - 20, 26, utf8_decode(strtoupper($categoriaAtual)), 0, 1, 'L');

                    $y = $pdf->GetY();

                    // Restaurar cor de fundo para a linha
                    if ($linhaAlternada) {
                        $pdf->SetFillColor($corCinzaClaro[0], $corCinzaClaro[1], $corCinzaClaro[2]);
                    } else {
                        $pdf->SetFillColor($corBranco[0], $corBranco[1], $corBranco[2]);
                    }
                }

                // Configurar texto
                $pdf->SetFont('Arial', '', 10);
                $pdf->SetTextColor($corPreto[0], $corPreto[1], $corPreto[2]);

                // Desenhar linha de dados
                $posX = $margemTabela;
                $estoqueCritico = $row['quantidade'] <= 5;

                // ID
                $pdf->Rect($posX, $y, $larguras[0], $alturaLinhaDados, 'FD');
                $pdf->SetXY($posX, $y);
                $pdf->Cell($larguras[0], $alturaLinhaDados, $row['id'], 0, 0, 'C');
                $posX += $larguras[0];

                // Barcode
                $pdf->Rect($posX, $y, $larguras[1], $alturaLinhaDados, 'FD');
                $pdf->SetXY($posX + 5, $y);
                $pdf->Cell($larguras[1] - 10, $alturaLinhaDados, $row['barcode'], 0, 0, 'L');
                $posX += $larguras[1];

                // Nome do produto
                $pdf->Rect($posX, $y, $larguras[2], $alturaLinhaDados, 'FD');
                $pdf->SetXY($posX + 5, $y);
                $pdf->Cell($larguras[2] - 10, $alturaLinhaDados, utf8_decode($row['nome_produto']), 0, 0, 'L');
                $posX += $larguras[2];

                // Quantidade
                $pdf->Rect($posX, $y, $larguras[3], $alturaLinhaDados, 'FD');
                $pdf->SetXY($posX, $y);
                if ($estoqueCritico) {
                    $pdf->SetTextColor($corAlerta[0], $corAlerta[1], $corAlerta[2]);
                    $pdf->SetFont('Arial', 'B', 10);
                }
                $pdf->Cell($larguras[3], $alturaLinhaDados, $row['quantidade'], 0, 0, 'C');
                $pdf->SetTextColor($corPreto[0], $corPreto[1], $corPreto[2]);
                $pdf->SetFont('Arial', '', 10);
                $posX += $larguras[2];

                $y += $alturaLinhaDados;
                $linhaAlternada = !$linhaAlternada;

                // Verificar se é o último item
                if ($idx == $result - 1) {
                    // Adicionar cantos arredondados na última linha da tabela
                    $pdf->SetDrawColor(220, 220, 220);
                    $pdf->RoundedRect($margemTabela, $y - $alturaLinhaDados, $larguras[0], $alturaLinhaDados, 5, 'D', '4');
                    $pdf->RoundedRect($posX, $y - $alturaLinhaDados, $larguras[3], $alturaLinhaDados, 5, 'D', '3');

                    // ===== RODAPÉ PROFISSIONAL =====
                    // Verificar se há espaço suficiente para o rodapé (aproximadamente 60 pontos para 4 linhas de 15 pontos cada)
                    if ($y + 60 > $pdf->GetPageHeight() - 60) {
                        $pdf->AddPage();
                        $y = 40; // Reiniciar Y na nova página
                    }

                    // Desativar quebra automática para garantir que o rodapé seja desenhado como um bloco
                    $pdf->SetAutoPageBreak(false);

                    // Configurar fonte e cor do texto
                    $pdf->SetTextColor($corPreto[0], $corPreto[1], $corPreto[2]);
                    $pdf->SetFont('Arial', '', 10);

                    // Desenhar o rodapé
                    $pdf->SetXY(40, $y + 15);
                    $pdf->Cell(0, 10, utf8_decode("SCB = SEM CÓDIGO DE BARRA"), 0, 1, 'L');

                    $pdf->SetXY(40, $y + 25);
                    $pdf->Cell(0, 10, utf8_decode("Sistema de Gerenciamento de Estoque - STGM v1.2.0"), 0, 1, 'L');

                    $pdf->SetXY(40, $y + 35);
                    $pdf->Cell(0, 10, utf8_decode("© " . date('Y') . " - Desenvolvido por alunos EEEP STGM"), 0, 1, 'L');

                    // Número da página (alinhado à direita)
                    $pdf->SetXY(-60, $y + 35);
                    $pdf->Cell(30, 10, utf8_decode('Página ' . $pdf->PageNo()), 0, 0, 'R');

                    // Reativar quebra automática após o rodapé
                    $pdf->SetAutoPageBreak(true, 60);
                }
            }
        } else {
            $pdf->SetXY($margemTabela, $y);
            $pdf->SetFont('Arial', 'I', 12);
            $pdf->SetTextColor($corTextoSubtil[0], $corTextoSubtil[1], $corTextoSubtil[2]);
            $pdf->SetFillColor(250, 250, 250);
            $pdf->RoundedRect($margemTabela, $y, array_sum($larguras), 40, 5, 'FD');
            $pdf->SetXY($margemTabela, $y + 12);
            $pdf->Cell(array_sum($larguras), 16, utf8_decode("Não existem produtos com estoque crítico (quantidade ≤ 5)"), 0, 1, 'C');

            // ===== RODAPÉ PROFISSIONAL =====
            // Verificar se há espaço suficiente para o rodapé (aproximadamente 60 pontos para 4 linhas de 15 pontos cada)
            if ($y + 60 > $pdf->GetPageHeight() - 60) {
                $pdf->AddPage();
                $y = 40; // Reiniciar Y na nova página
            }

            // Desativar quebra automática para garantir que o rodapé seja desenhado como um bloco
            $pdf->SetAutoPageBreak(false);

            // Configurar fonte e cor do texto
            $pdf->SetTextColor($corPreto[0], $corPreto[1], $corPreto[2]);
            $pdf->SetFont('Arial', '', 10);

            // Desenhar o rodapé
            $pdf->SetXY(40, $y + 15);
            $pdf->Cell(0, 10, utf8_decode("SCB = SEM CÓDIGO DE BARRA"), 0, 1, 'L');

            $pdf->SetXY(40, $y + 25);
            $pdf->Cell(0, 10, utf8_decode("Sistema de Gerenciamento de Estoque - STGM v1.2.0"), 0, 1, 'L');

            $pdf->SetXY(40, $y + 35);
            $pdf->Cell(0, 10, utf8_decode("© " . date('Y') . " - Desenvolvido por alunos EEEP STGM"), 0, 1, 'L');

            // Número da página (alinhado à direita)
            $pdf->SetXY(-60, $y + 35);
            $pdf->Cell(30, 10, utf8_decode('Página ' . $pdf->PageNo()), 0, 0, 'R');

            // Reativar quebra automática após o rodapé
            $pdf->SetAutoPageBreak(true, 60);
        }

        // Saída do PDF
        $pdf->Output("relatorio_estoque.pdf", "I");
    }





    public function relatoriocriticostoque()
    {
        $consulta = "SELECT * FROM produtos WHERE quantidade <= 5 ORDER BY natureza, nome_produto";
        $query = $this->pdo->prepare($consulta);
        $query->execute();
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
        $pdf->Cell($larguraDisponivel, 24, utf8_decode("RELATÓRIO DE ESTOQUE CRÍTICO"), 0, 1, 'L');

        $pdf->SetFont('Arial', '', 12);
        $pdf->SetXY(40 + $logoWidth + 5, $pdf->GetY()); // Reduzindo o espaçamento de 15 para 5
        $pdf->Cell($larguraDisponivel, 10, utf8_decode("EEEP Salaberga Torquato Gomes de Matos"), 0, 1, 'L');

        // ===== RESUMO DE DADOS EM CARDS =====
        $totalProdutosCriticos = $result;
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

        // Card 1 - Total de Itens Críticos
        $pdf->SetFillColor($corBranco[0], $corBranco[1], $corBranco[2]);
        $pdf->RoundedRect($startX, $startY, $cardWidth, $cardHeight, 8, 'F');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor($corPreto[0], $corPreto[1], $corPreto[2]);
        $pdf->SetXY($startX + 15, $startY + 15);
        $pdf->Cell($cardWidth - 30, 20, utf8_decode("ITENS CRÍTICOS"), 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 24);
        $pdf->SetTextColor($corAlerta[0], $corAlerta[1], $corAlerta[2]); // Vermelho como na imagem
        $pdf->SetXY($startX + 15, $startY + 40);
        $pdf->Cell($cardWidth - 30, 25, $totalProdutosCriticos, 0, 1, 'L');

        // Card 2 - Total em Estoque (quantidade total dos itens críticos)
        $pdf->SetFillColor($corBranco[0], $corBranco[1], $corBranco[2]);
        $pdf->RoundedRect($startX + $cardWidth + $cardMargin, $startY, $cardWidth, $cardHeight, 8, 'F');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor($corPreto[0], $corPreto[1], $corPreto[2]);
        $pdf->SetXY($startX + $cardWidth + $cardMargin + 15, $startY + 15);
        $pdf->Cell($cardWidth - 30, 20, utf8_decode("TOTAL EM ESTOQUE"), 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 24);
        $pdf->SetTextColor($corAlerta[0], $corAlerta[1], $corAlerta[2]); // Vermelho como na imagem
        $pdf->SetXY($startX + $cardWidth + $cardMargin + 15, $startY + 40);
        $pdf->Cell($cardWidth - 30, 25, $totalQuantidade, 0, 1, 'L');

        // ===== TABELA DE PRODUTOS CRÍTICOS =====
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
        $pdf->Cell($larguraPagina - 30, 15, utf8_decode("DETALHAMENTO DO ESTOQUE CRÍTICO"), 0, 1, 'L');

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
            $pdf->Cell(array_sum($larguras), 16, utf8_decode("Não existem produtos com estoque crítico (quantidade ≤ 5)"), 0, 1, 'C');
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
