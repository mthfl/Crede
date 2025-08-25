<?php
require_once('sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../config/connect.php');
class liberador extends connect
{
    protected string $table1;
    protected string $table2;
    protected string $table3;
    protected string $table4;

    function __construct()
    {
        parent::__construct();
        require(__DIR__ . '/private/tables.php');
        $this->table1 = $table['crede_estoque'][1];
        $this->table2 = $table['crede_estoque'][2];
        $this->table3 = $table['crede_estoque'][3];
        $this->table4 = $table['crede_estoque'][4];
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

    public function adicionar_produto($barcode, $quantidade, $validade): int
    {

        if ($validade == NULL) {
            $consulta = "UPDATE produtos SET quantidade = quantidade + :quantidade WHERE barcode = :barcode";
            $stmt_adicionar = $this->connect->prepare($consulta);
            $stmt_adicionar->bindValue(":quantidade", $quantidade);
            $stmt_adicionar->bindValue(":barcode", $barcode);

            if ($stmt_adicionar->execute()) {

                return 1;
            } else {
                return 2;
            }
        } else {

            $consulta = "UPDATE produtos SET quantidade = quantidade + :quantidade,  vencimento = :vencimento WHERE barcode = :barcode";
            $stmt_adicionar = $this->connect->prepare($consulta);
            $stmt_adicionar->bindValue(":quantidade", $quantidade);
            $stmt_adicionar->bindValue(":barcode", $barcode);
            $stmt_adicionar->bindValue(":vencimento", $validade);

            if ($stmt_adicionar->execute()) {

                return 1;
            } else {
                return 2;
            }
        }
    }

    public function solicitar_produto_id(int $valor_retirado, int  $id_produto, string $solicitador, string $datetime, string $liberador): int
    {
        /*try {*/

            $consultaProduto = "SELECT * FROM produtos WHERE id = :id";
            $queryProduto = $this->connect->prepare($consultaProduto);
            $queryProduto->bindValue(":id", $id_produto);
            $queryProduto->execute();
            $produto = $queryProduto->fetch(PDO::FETCH_ASSOC);

            if ($produto['quantidade'] < $valor_retirado) {
                return 3;
            }

            $consultaUpdate = "UPDATE produtos SET quantidade = quantidade - :valor_retirada WHERE id = :id";
            $queryUpdate = $this->connect->prepare($consultaUpdate);
            $queryUpdate->bindValue(":valor_retirada", $valor_retirado, PDO::PARAM_INT);
            $queryUpdate->bindValue(":id", $id_produto);
            $queryUpdate->execute();

            $consultaInsert = "INSERT INTO movimentacao VALUES (NULL, :id_produtos, :liberador, :solicitador, :datareg, :quantidade_retirada)";
            $queryInsert = $this->connect->prepare($consultaInsert);
            $queryInsert->bindValue(":id_produtos", $id_produto);
            $queryInsert->bindValue(":liberador", $liberador);
            $queryInsert->bindValue(":solicitador", $solicitador);
            $queryInsert->bindValue(":datareg", $datetime);
            $queryInsert->bindValue(":quantidade_retirada", $valor_retirado);

            if ($queryInsert->execute()) {
                return 1;
            } else {
                return 2;
            }
        /*} catch (PDOException $e) {

            return 0;
        }*/
    }

    public function solicitar_produto_barcode($valor_retirado, $barcode, $solicitador, $datetime, $liberador)
    {
        /*try {*/

            $consultaProduto = "SELECT * FROM produtos WHERE barcode = :barcode";
            $queryProduto = $this->connect->prepare($consultaProduto);
            $queryProduto->bindValue(":barcode", $barcode);
            $queryProduto->execute();
            $produto = $queryProduto->fetch(PDO::FETCH_ASSOC);
            $id_produto = $produto['id'];

            if ($produto['quantidade'] < $valor_retirado) {
                return 3;
            }

            $consultaUpdate = "UPDATE produtos SET quantidade = quantidade - :valor_retirada WHERE id = :id";
            $queryUpdate = $this->connect->prepare($consultaUpdate);
            $queryUpdate->bindValue(":valor_retirada", $valor_retirado, PDO::PARAM_INT);
            $queryUpdate->bindValue(":id", $id_produto);
            $queryUpdate->execute();

            $consultaInsert = "INSERT INTO movimentacao VALUES (NULL, :id_produtos, :liberador, :solicitador, :datareg, :quantidade_retirada)";
            $queryInsert = $this->connect->prepare($consultaInsert);
            $queryInsert->bindValue(":id_produtos", $id_produto);
            $queryInsert->bindValue(":liberador", $liberador);
            $queryInsert->bindValue(":solicitador", $solicitador);
            $queryInsert->bindValue(":datareg", $datetime);
            $queryInsert->bindValue(":quantidade_retirada", $valor_retirado);

            if ($queryInsert->execute()) {
                return 1;
            } else {
                return 2;
            }
        /*} catch (PDOException $e) {

            return 0;
        }*/
    }
    public function select_produto_nome($barcode)
    {
        try {
            $consulta = "SELECT p.*, c.nome_categoria AS categoria, c.id as id_categoria FROM $this->table4 p INNER JOIN $this->table1 c ON p.id_categoria = c.id WHERE p.barcode = :barcode";
            $query = $this->connect->prepare($consulta);
            $query->bindValue(":barcode", $barcode);
            $query->execute();

            return $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function editar_produto_nome($id, $nome): int
    {
        try {
            $consulta = "UPDATE produtos SET nome_produto = :nome WHERE id = :id";
            $query = $this->connect->prepare($consulta);
            $query->bindValue(":id", $id);
            $query->bindValue(":nome", $nome);
            $query->execute();

            if ($query->execute()) {

                return 1;
            } else {

                return 2;
            }
        } catch (PDOException $e) {

            return 0;
        }
    }
}
