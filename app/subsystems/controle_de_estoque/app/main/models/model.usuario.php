<?php
require_once('sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../config/connect.php');
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
        require(__DIR__.'/private/tables.php');
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
            $query = $this->connect->prepare($consulta);
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
            $queryVerificar = $this->connect->prepare($consultaVerificar);
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
            $queryMovimentacoes = $this->connect->prepare($consultaMovimentacoes);
            $queryMovimentacoes->bindValue(":id", $id);
            $queryMovimentacoes->execute();
            $movimentacoes = $queryMovimentacoes->fetch(PDO::FETCH_ASSOC);

            error_log("Movimentações relacionadas: " . $movimentacoes['total']);

            // Excluir movimentações relacionadas primeiro (se houver)
            if ($movimentacoes['total'] > 0) {
                error_log("Excluindo movimentações relacionadas...");
                $consultaDeleteMovimentacoes = "DELETE FROM movimentacao WHERE fk_produtos_id = :id";
                $queryDeleteMovimentacoes = $this->connect->prepare($consultaDeleteMovimentacoes);
                $queryDeleteMovimentacoes->bindValue(":id", $id);
                $queryDeleteMovimentacoes->execute();
                error_log("Movimentações excluídas com sucesso");
            }

            // Excluir o produto
            $consulta = "DELETE FROM produtos WHERE id = :id";
            $query = $this->connect->prepare($consulta);
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
}
