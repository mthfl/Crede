<?php
require_once('sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../config/connect.php');

class admin extends connect
{
    private string $table1;
    private string $table2;
    private string $table3;
    private string $table4;
    private string $table5;

    function __construct()
    {
        parent::__construct();
        require(__DIR__ . '/../../../.env/tables.php');
        $this->table1 = $table['crede_users'][1];
        $this->table2 = $table['crede_users'][2];
        $this->table3 = $table['crede_users'][3];
        $this->table4 = $table['crede_users'][4];
        $this->table5 = $table['crede_users'][5];
    }
}
