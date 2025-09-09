<?php
require_once(__DIR__ . '/../config/connect.php');
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
    protected string $table10;
    protected string $table11;
    protected string $table12;

    function __construct($escola)
    {
        parent::__construct($escola);
        $table = require(__DIR__ . '/../../../.env/tables.php');
        
        // Verificar se a configuração da escola existe
        $escola_key = "ss_$escola";
        if (!isset($table[$escola_key])) {
            throw new Exception("Configuração de tabelas não encontrada para $escola_key");
        }
        
        $this->table1 = $table[$escola_key][1] ?? 'default_table1';
        $this->table2 = $table[$escola_key][2] ?? 'default_table2';
        $this->table3 = $table[$escola_key][3] ?? 'default_table3';
        $this->table4 = $table[$escola_key][4] ?? 'default_table4';
        $this->table5 = $table[$escola_key][5] ?? 'default_table5';
        $this->table6 = $table[$escola_key][6] ?? 'default_table6';
        $this->table7 = $table[$escola_key][7] ?? 'default_table7';
        $this->table8 = $table[$escola_key][8] ?? 'default_table8';
        $this->table9 = $table[$escola_key][9] ?? 'default_table9';
        $this->table10 = $table[$escola_key][10] ?? 'default_table10';
        $this->table11 = $table[$escola_key][11] ?? 'default_table11';
        $this->table12 = $table[$escola_key][12] ?? 'default_table12';
    }

    public function select_cursos(): array{

        $stmt_cursos = $this->connect->query("SELECT * FROM $this->table2");

        return $cursos = $stmt_cursos->fetchAll(PDO::FETCH_ASSOC);
    }
    public function select_usuarios(): array{

        $stmt_cursos = $this->connect->query("SELECT * FROM $this->table5");

        return $cursos = $stmt_cursos->fetchAll(PDO::FETCH_ASSOC);
    }

}