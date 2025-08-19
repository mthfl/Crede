<?php
require_once('sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__.'/../config/connect.php');

class select extends connect
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

    public function select_categoria()
    {
        $query = $this->connect->query("SELECT * FROM $this->table1");
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

        return $resultado;
    }
    public function select_produtos()
    {
        $query = $this->connect->query("SELECT p.*, c.nome_categoria AS categoria FROM $this->table4 p INNER JOIN $this->table1 c ON p.id_categoria = c.id");
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

        return $resultado;
    }
    public function select_produtos_total()
    {
        $query = $this->connect->query("SELECT count(*) as total FROM $this->table4");
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

        return $resultado;
    }
    public function select_produtos_critico()
    {
        $query = $this->connect->query("SELECT count(*) as total FROM $this->table4 WHERE quantidade <= 5");
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

        return $resultado;
    }
    public function select_total_categorias()
    {
        $query = $this->connect->query("SELECT count(*) as total FROM $this->table1");
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

        return $resultado;
    }
    public function select_produto_nome($barcode)
    {
        $consulta = "SELECT * FROM $this->table4 WHERE barcode = :barcode";

        $query = $this->connect->prepare($consulta);
        $query->bindValue(":barcode", $barcode);
        $query->execute();

        return $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
    }
    public function select_responsavel(){
        
        $consulta = "SELECT * FROM $this->table5";
        $query = $this->connect->query($consulta);

        return $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
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