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
        try {
            require(__DIR__ . "\..\models\private\config.php");

            //banco no localhost
            $host = $config['local']['crede_users']['host'];
            $database = $config['local']['crede_users']['banco'];
            $user = $config['local']['crede_users']['user'];
            $password = $config['local']['crede_users']['senha'];

            $this->connect = new PDO('mysql:host=' . $host . ';dbname=' . $database, $user, $password);
            $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            if (!$this->connect) {
                //banco no hostinger
                $host = $config['hospedagem']['crede_users']['host'];
                $database = $config['hospedagem']['crede_users']['banco'];
                $user = $config['hospedagem']['crede_users']['user'];
                $password = $config['hospedagem']['crede_users']['senha'];
                $this->connect = new PDO('mysql:host=' . $host . ';dbname=' . $database, $user, $password);
                $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            }
        } catch (PDOException $e) {

            error_log("Erro de conexão com banco: " . $e->getMessage());
            // Não lançar exceção, apenas definir connect como null
            $this->connect = null;
            
            header('location: ../views/windows/desconnect.php');
            exit();
        }
    }
}
