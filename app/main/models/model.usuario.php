<?php
require_once(dirname(__FILE__) . "../config/connect.php");
class model_usuario extends connect
{
    private string $table1;
    private string $table2;
    private string $table3;

    function __construct(){
        parent::__construct();
        $this->table1 = $table['crede_users'][1];
        $this->table2 = $table['crede_users'][2];
        $this->table3 = $table['crede_users'][3];
    }
}
