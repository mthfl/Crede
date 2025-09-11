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
    protected string $table13;

    function __construct($escola)
    {
        parent::__construct($escola);
        $table = require(__DIR__ . '/../../../.env/tables.php');
        $this->table1 = $table["ss_$escola"][1];
        $this->table2 = $table["ss_$escola"][2];
        $this->table3 = $table["ss_$escola"][3];
        $this->table4 = $table["ss_$escola"][4];
        $this->table5 = $table["ss_$escola"][5];
        $this->table6 = $table["ss_$escola"][6];
        $this->table7 = $table["ss_$escola"][7];
        $this->table8 = $table["ss_$escola"][8];
        $this->table9 = $table["ss_$escola"][9];
        $this->table10 = $table["ss_$escola"][10];
        $this->table11 = $table["ss_$escola"][11];
        $this->table12 = $table["ss_$escola"][12];
        $this->table13 = $table["ss_$escola"][13];
    }

    public function select_cursos(): array{

        $stmt_cursos = $this->connect->query("SELECT * FROM $this->table2");

        return $cursos = $stmt_cursos->fetchAll(PDO::FETCH_ASSOC);
    }
    public function select_usuarios(): array{

        $stmt_cursos = $this->connect->query("SELECT * FROM $this->table5");

        return $cursos = $stmt_cursos->fetchAll(PDO::FETCH_ASSOC);
    }

    public function select_tipos_usuarios(): array{
        try{
            $stmt = $this->connect->query("SHOW COLUMNS FROM $this->table5 LIKE 'tipo_usuario'");
            $col = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$col || empty($col['Type'])){
                return [];
            }
            $type = $col['Type']; // ex: enum('admin','cadastrador')
            if (preg_match("/enum\\((.*)\\)/i", $type, $matches)){
                $vals = $matches[1];
                $vals = str_getcsv($vals, ',', "'\"");
                // limpar espaços e chaves vazias
                $clean = [];
                foreach($vals as $v){
                    $v = trim($v);
                    if($v !== ''){ $clean[] = $v; }
                }
                return $clean;
            }
            return [];
        }catch(PDOException $e){
            return [];
        }
    }

    public function select_bairros(): array{
        try{
            $stmt = $this->connect->query("SELECT * FROM $this->table13 ORDER BY nome_bairro ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            return [];
        }
    }

}