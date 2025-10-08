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

    function __construct()
    {
        parent::__construct();
        $table = require(__DIR__ . '/../../../.env/tables.php');
        
        $this->table1 = $table["ss_estgdm"][5];
        $this->table2 = $table["ss_epaf"][5];
        $this->table3 = $table["ss_epmfm"][5];
        $this->table4 = $table["ss_epav"][5];
        $this->table5 = $table["ss_eedq"][5];
        $this->table6 = $table["ss_ejin"][5];
        $this->table7 = $table["ss_epfads"][5];
        $this->table8 = $table["ss_emcvm"][5];
        $this->table9 = $table["ss_eglgfm"][5];
        $this->table10 = $table["ss_epldtv"][5];
        $this->table11 = $table["ss_ercr"][5];
        $this->table12 = $table["crede_users"][6];

    }

    public function select_estgdm() {
        $stmt_estgdm = $this->connect_estgdm->query("SELECT * FROM $this->table1 WHERE tipo_usuario = 'admin'");
        $estgdm = $stmt_estgdm->fetchAll(PDO::FETCH_ASSOC);
        return $estgdm;
    }
    public function select_epaf() {
        $stmt_epaf = $this->connect_epaf->query("SELECT * FROM $this->table2 WHERE tipo_usuario = 'admin'");
        $epaf = $stmt_epaf->fetchAll(PDO::FETCH_ASSOC);
        return $epaf;
    }
    public function select_epmfm() {
        $stmt_epmfm = $this->connect_epmfm->query("SELECT * FROM $this->table3 WHERE tipo_usuario = 'admin'");
        $epmfm = $stmt_epmfm->fetchAll(PDO::FETCH_ASSOC);
        return $epmfm;
    }
    public function select_epav() {
        $stmt_epav = $this->connect_epav->query("SELECT * FROM $this->table4 WHERE tipo_usuario = 'admin'");
        $epav = $stmt_epav->fetchAll(PDO::FETCH_ASSOC);
        return $epav;
    }
    public function select_eedq() {
        $stmt_eedq = $this->connect_eedq->query("SELECT * FROM $this->table5 WHERE tipo_usuario = 'admin'");
        $eedq = $stmt_eedq->fetchAll(PDO::FETCH_ASSOC);
        return $eedq;
    }
    public function select_ejin() {
        $stmt_ejin = $this->connect_ejin->query("SELECT * FROM $this->table6 WHERE tipo_usuario = 'admin'");
        $ejin = $stmt_ejin->fetchAll(PDO::FETCH_ASSOC);
        return $ejin;
    }
    public function select_epfads() {
        $stmt_epfads = $this->connect_epfads->query("SELECT * FROM $this->table7 WHERE tipo_usuario = 'admin'");
        $epfads = $stmt_epfads->fetchAll(PDO::FETCH_ASSOC);
        return $epfads;
    }
    public function select_emcvm() {
        $stmt_emcvm = $this->connect_emcvm->query("SELECT * FROM $this->table8 WHERE tipo_usuario = 'admin'");
        $emcvm = $stmt_emcvm->fetchAll(PDO::FETCH_ASSOC);
        return $emcvm;
    }
    public function select_eglgfm() {
        $stmt_eglgfm = $this->connect_eglgfm->query("SELECT * FROM $this->table9 WHERE tipo_usuario = 'admin'");
        $eglgfm = $stmt_eglgfm->fetchAll(PDO::FETCH_ASSOC);
        return $eglgfm;
    }
    public function select_epldtv() {
        $stmt_epldtv = $this->connect_epldtv->query("SELECT * FROM $this->table10 WHERE tipo_usuario = 'admin'");
        $epldtv = $stmt_epldtv->fetchAll(PDO::FETCH_ASSOC);
        return $epldtv; 
    }
    public function select_ercr() {
        $stmt_ercr = $this->connect_ercr->query("SELECT * FROM $this->table11 WHERE tipo_usuario = 'admin'");
        $ercr = $stmt_ercr->fetchAll(PDO::FETCH_ASSOC);
        return $ercr;
    }

    public function select_escola() {
        $stmt_escolas = $this->connect->query("SELECT * FROM $this->table12");
        $escolas = $stmt_escolas->fetchAll(PDO::FETCH_ASSOC);
        return $escolas;
    }

}
