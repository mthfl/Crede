<?php
class connect
{
    protected $connect;
    function __construct($escola)
    {
        $this->connect_database($escola);
    }

    function connect_database($escola)
    {
        try {
            $config = require(__DIR__."/../../../.env/config.php");

            // Tentar primeiro o banco local
            try {
                $host = $config['local']["ss_$escola"]['host'];
                $database = $config['local']["ss_$escola"]['banco'];
                $user = $config['local']["ss_$escola"]['user'];
                $password = $config['local']["ss_$escola"]['senha'];

                $this->connect = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', $user, $password);
                $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Se falhar, tentar o banco da hospedagem
                $host = $config['hospedagem']["ss_$escola"]['host'];
                $database = $config['hospedagem']["ss_$escola"]['banco'];
                $user = $config['hospedagem']["ss_$escola"]['user'];
                $password = $config['hospedagem']["ss_$escola"]['senha'];

                $this->connect = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', $user, $password);
                $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {

            /*error_log("Erro de conexão com banco: " . $e->getMessage());
            $this->connect = null;
            header('location:../views/windows/desconnect.php');
            exit();*/
        }
    }
}
