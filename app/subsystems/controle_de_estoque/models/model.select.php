<?php
require_once('sessions.php');
// Permitir que APIs retornem JSON sem redirecionar a login
if (!defined('SKIP_AUTH')) {
    $session = new sessions();
    $session->autenticar_session();
    $session->tempo_session();
}

require_once(__DIR__ . '/../config/connect.php');
//print_r($_POST);
class select extends connect
{
    protected string $table1;
    protected string $table2;
    protected string $table3;
    protected string $table4;
    protected string $table5;
    protected string $table6;
    protected string $table7;
    protected string $table8;
    protected string $table9;

    function __construct()
    {
        parent::__construct();
        require(__DIR__.'/private/tables.php');
        $this->table1 = $table['crede_estoque'][1];
        $this->table2 = $table['crede_estoque'][2];
        $this->table3 = $table['crede_estoque'][3];
        $this->table4 = $table['crede_estoque'][4];
        $this->table5 = $table['crede_users'][1];
        $this->table6 = $table['crede_users'][2];
        $this->table7 = $table['crede_users'][3];
        $this->table8 = $table['crede_users'][4];
        $this->table9 = $table['crede_users'][5];
    }

    public function select_produtos_id($id)
    {
        $query = $this->connect->query("SELECT p.*, c.nome_categoria AS categoria, c.id as id_categoria FROM $this->table4 p INNER JOIN $this->table1 c ON p.id_categoria = c.id WHERE p.id = '$id'");
        $resultado = $query->fetch(PDO::FETCH_ASSOC);

        return $resultado;
    }
    public function select_categoria()
    {
        $query = $this->connect->query("SELECT * FROM $this->table1");
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

        return $resultado;
    }
    public function select_produtos()
    {
        $query = $this->connect->query("SELECT p.*, c.nome_categoria AS categoria, c.id as id_categoria FROM $this->table4 p INNER JOIN $this->table1 c ON p.id_categoria = c.id ORDER BY p.id DESC");
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
        $consulta = "SELECT p.*, c.nome_categoria AS categoria, c.id as id_categoria FROM $this->table4 p INNER JOIN $this->table1 c ON p.id_categoria = c.id WHERE p.barcode = :barcode";

        $query = $this->connect->prepare($consulta);
        $query->bindValue(":barcode", $barcode);
        $query->execute();

        return $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
    }
    public function select_responsavel()
    {
        $consulta = "SELECT u.nome, s.nome AS nome_setor FROM $this->table5 u INNER JOIN $this->table6 s ON u.id_setor = s.id";
        $query = $this->connect_users->query($consulta);

        return $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Métodos para estatísticas do gráfico
    public function select_produtos_em_estoque()
    {
        $query = $this->connect->query("SELECT count(*) as total FROM $this->table4 WHERE quantidade > 5");
        $resultado = $query->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }

    public function select_produtos_estoque_critico()
    {
        $query = $this->connect->query("SELECT count(*) as total FROM $this->table4 WHERE quantidade > 0 AND quantidade <= 5");
        $resultado = $query->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }

    public function select_produtos_sem_estoque()
    {
        $query = $this->connect->query("SELECT count(*) as total FROM $this->table4 WHERE quantidade = 0");
        $resultado = $query->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }
    
}

