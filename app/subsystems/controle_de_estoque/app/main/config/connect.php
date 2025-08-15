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
            $host = $config['local']['crede_estoque']['host'];
            $database = $config['local']['crede_estoque']['banco'];
            $user = $config['local']['crede_estoque']['user'];
            $password = $config['local']['crede_estoque']['senha'];

            $this->connect = new PDO('mysql:host=' . $host . ';dbname=' . $database, $user, $password);

            if (!$this->connect) {
                //banco no hostinger
                $host = $config['hospedagem']['crede_estoque']['host'];
                $database = $config['hospedagem']['crede_estoque']['banco'];
                $user = $config['hospedagem']['crede_estoque']['user'];
                $password = $config['hospedagem']['crede_estoque']['senha'];
                $this->connect = new PDO('mysql:host=' . $host . ';dbname=' . $database, $user, $password);
            }
        } catch (PDOException $e) {

            header('location: ../views/windows/desconnect.php');
            exit();
        }
    }
}
