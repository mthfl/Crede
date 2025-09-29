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

    public function select_cursos(): array
    {

        $stmt_estgdm = $this->connect_estgdm->query("SELECT * FROM $this->table5 WHERE tipo_usuario = 'admin'");
        $estgdm = $stmt_estgdm->fetchAll(PDO::FETCH_ASSOC);

        $stmt_epaf = $this->connect_epaf->query("SELECT * FROM $this->table5 WHERE tipo_usuario = 'admin'");
        $epaf = $stmt_epaf->fetchAll(PDO::FETCH_ASSOC);

        $stmt_epmfm = $this->connect_epmfm->query("SELECT * FROM $this->table5 WHERE tipo_usuario = 'admin'");
        $epmfm = $stmt_epmfm->fetchAll(PDO::FETCH_ASSOC);

        $stmt_epav = $this->connect_epav->query("SELECT * FROM $this->table5 WHERE tipo_usuario = 'admin'");
        $epav = $stmt_epav->fetchAll(PDO::FETCH_ASSOC);

        $stmt_eedq = $this->connect_eedq->query("SELECT * FROM $this->table5 WHERE tipo_usuario = 'admin'");
        $eedq = $stmt_eedq->fetchAll(PDO::FETCH_ASSOC);

        $stmt_ejin = $this->connect_ejin->query("SELECT * FROM $this->table5 WHERE tipo_usuario = 'admin'");
        $ejin = $stmt_ejin->fetchAll(PDO::FETCH_ASSOC);

        $stmt_epfads = $this->connect_epfads->query("SELECT * FROM $this->table5 WHERE tipo_usuario = 'admin'");
        $epfads = $stmt_epfads->fetchAll(PDO::FETCH_ASSOC);

        $stmt_emcvm = $this->connect_emcvm->query("SELECT * FROM $this->table5 WHERE tipo_usuario = 'admin'");
        $emcvm = $stmt_emcvm->fetchAll(PDO::FETCH_ASSOC);

        $stmt_eglgfm = $this->connect_eglgfm->query("SELECT * FROM $this->table5 WHERE tipo_usuario = 'admin'");
        $eglgfm = $stmt_eglgfm->fetchAll(PDO::FETCH_ASSOC);

        $stmt_epldtv = $this->connect_epldtv->query("SELECT * FROM $this->table5 WHERE tipo_usuario = 'admin'");
        $epldtv = $stmt_epldtv->fetchAll(PDO::FETCH_ASSOC);

        $stmt_ercr = $this->connect_ercr->query("SELECT * FROM $this->table5 WHERE tipo_usuario = 'admin'");
        $ercr = $stmt_ercr->fetchAll(PDO::FETCH_ASSOC);

        return [
            $estgdm,
            $epaf,
            $epmfm,
            $epav,
            $eedq,
            $ejin,
            $epfads,
            $emcvm,
            $eglgfm,
            $epldtv,
            $ercr
        ];

    }
    
}
