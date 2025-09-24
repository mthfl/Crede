<?php
require_once(__DIR__.'/../config/connect.php');
class model_select extends connect
{
    private string $table1;
    private string $table2;
    private string $table3;
    private string $table4;
    private string $table5;
    private string $table6;

    function __construct()
    {
        parent::__construct();
        $table = require(__DIR__.'/../../../.env/tables.php');
        $this->table1 = $table['crede_users'][1];
        $this->table2 = $table['crede_users'][2];
        $this->table3 = $table['crede_users'][3];
        $this->table4 = $table['crede_users'][4];
        $this->table5 = $table['crede_users'][5];
        $this->table6 = $table['crede_users'][6];
    }

    public function select_escolas_nome($escola): string{
        $stmt = $this->connect->query("SELECT * FROM $this->table6 WHERE nome_escola = '$escola'");
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        return $escola_banco = $dados['escola_banco'];
    }

    public function select_escolas(): array{
        $stmt = $this->connect->query("SELECT * FROM $this->table6");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
