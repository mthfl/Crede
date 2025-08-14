<?php
require_once("../config/connection.php");

class select extends connection
{
    public function __construct()
    {
        parent::__construct();
    }

    public function select_categoria()
    {
        $query = $this->pdo->query('SELECT * FROM categorias');
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

        return $resultado;
    }
    public function selectProdutosTotal()
    {
        $query = $this->pdo->query('SELECT * FROM produtos');
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

        return $resultado;
    }
    public function selectProdutos($barcode)
    {
        $consulta = "SELECT * FROM produtos WHERE barcode = :barcode";

        $query = $this->pdo->prepare($consulta);
        $query->bindValue(":barcode", $barcode);
        $query->execute();

        return $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectProdutosFlexivel($identificador)
    {
        try {
            // Verificar se é um barcode (numérico) ou nome do produto
            if (is_numeric($identificador)) {
                // Buscar por barcode
                $consulta = "SELECT * FROM produtos WHERE barcode = :identificador";
                $parametro = $identificador;
                $nome_parametro = ":identificador";
            } else {
                // Verificar se já tem prefixo SCB_
                if (strpos($identificador, 'SCB_') === 0) {
                    // Já tem prefixo SCB_, usar como está
                    $consulta = "SELECT * FROM produtos WHERE UPPER(barcode) = UPPER(:barcode_com_prefixo)";
                    $parametro = $identificador;
                    $nome_parametro = ":barcode_com_prefixo";
                } else {
                    // Adicionar prefixo SCB_ para produtos sem código
                    $barcode_com_prefixo = 'SCB_' . $identificador;
                    $consulta = "SELECT * FROM produtos WHERE UPPER(barcode) = UPPER(:barcode_com_prefixo)";
                    $parametro = $barcode_com_prefixo;
                    $nome_parametro = ":barcode_com_prefixo";
                }
            }

            error_log("Consulta SQL: " . $consulta);
            error_log("Parâmetro final: " . $parametro);

            $query = $this->pdo->prepare($consulta);
            $query->bindValue($nome_parametro, $parametro);
            $query->execute();

            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            error_log("Resultado encontrado: " . count($resultado) . " registros");
            
            // Debug: verificar todos os produtos no banco
            $todos_produtos = $this->pdo->query("SELECT barcode, nome_produto FROM produtos")->fetchAll(PDO::FETCH_ASSOC);
            error_log("Todos os produtos no banco: " . print_r($todos_produtos, true));
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Erro no selectProdutosFlexivel: " . $e->getMessage());
            return array();
        }
    }

    public function selectSolicitarProdutos($barcode)
    {
        try {
            $query = $this->pdo->query('SELECT id, barcode, nome_produto, quantidade FROM produtos ORDER BY nome_produto');
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nome_produto']) . " (Estoque: " . htmlspecialchars($row['quantidade']) . ")</option>";      
            }
        } catch (PDOException $e) {
            echo "<option value='' disabled>Erro ao conectar ao banco: " . htmlspecialchars($e->getMessage()) . "</option>";
        }
    }

    public function selectSolicitarResponsaveis($barcode)
    {
        try {
            $query = $this->pdo->query('SELECT nome FROM responsaveis ORDER BY nome');
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='" . htmlspecialchars($row['nome']) . "'>" . htmlspecialchars($row['nome']) . "</option>";
            }
        } catch (PDOException $e) {
            echo "<option value='' disabled>Erro ao conectar ao banco: " . htmlspecialchars($e->getMessage()) . "</option>";
        }
    }


    public function modalRelatorio()
    {
        try {
            $query = $this->pdo->query('SELECT id, nome_produto FROM produtos ORDER BY nome_produto');
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='" . htmlspecialchars($row['id']) . "'>" . strtoupper(htmlspecialchars($row['nome_produto'])) . "</option>";
            }
        } catch (PDOException $e) {
            echo "<option value='' disabled>Erro ao carregar produtos: " . htmlspecialchars($e->getMessage()) . "</option>";
        }
    }
}
?>