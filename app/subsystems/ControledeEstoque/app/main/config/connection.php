<?php
class connect
{
    protected $connect;
    function __construct()
    {
        $this->connect_database();
    }
    function connect_database()
    {
        require("../models/private/config.php");
        try {
            //banco no localhost
            $host = $config['local']['sist_pagamento']['host'];
            $database = $config['local']['sist_pagamento']['banco'];
            $user = $config['local']['sist_pagamento']['user'];
            $password = $config['local']['sist_pagamento']['senha'];

            $this->connect = new PDO('mysql:host=' . $host . ';dbname=' . $database, $user, $password);

            if (!$this->connect) {
                //banco no hostinger
                $host = $config['hospedagem']['sist_pagamento']['host'];
                $database = $config['hospedagem']['sist_pagamento']['banco'];
                $user = $config['hospedagem']['sist_pagamento']['user'];
                $password = $config['hospedagem']['sist_pagamento']['senha'];
                $this->connect = new PDO('mysql:host=' . $host . ';dbname=' . $database, $user, $password);
            }
        } catch (PDOException $e) {

            header('location: ../views/windows/desconnect.php');
            exit();
        }
    }
}
