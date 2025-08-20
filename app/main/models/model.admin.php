<?php
require_once('model.usuario.php');
class model_admin extends usuario
{
    private string $table1;
    private string $table2;
    private string $table3;
    private string $table4;
    private string $table5;

    function __construct()
    {
        parent::__construct();
        $table = require(__DIR__ . '/private/tables.php');
        $this->table1 = $table['crede_users'][1]; // usuarios
        $this->table2 = $table['crede_users'][2]; // setores
        $this->table3 = $table['crede_users'][3]; // tipos_usuarios
        $this->table4 = $table['crede_users'][4]; // permissoes
        $this->table5 = $table['crede_users'][5]; // sistemas
    }

}
