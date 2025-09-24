<?php
class connect
{
    protected $connect;
    protected $connect_users;
    function __construct($escola_banco)
    {
        $this->connect_database($escola_banco);
    }

    function connect_database($escola_banco)
    {
        try {
            $config = require(__DIR__."/../../../.env/config.php");

            // Tentar primeiro o banco local
            try {
                $host_user = $config['local']["crede_users"]['host'];
                $database_user = $config['local']["crede_users"]['banco'];
                $user_user = $config['local']["crede_users"]['user'];
                $password_user = $config['local']["crede_users"]['senha'];

                $this->connect_users = new PDO('mysql:host=' . $host_user . ';dbname=' . $database_user . ';charset=utf8', $user_user, $password_user);
                $this->connect_users->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_users->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                $host = $config['local']["ss_$escola_banco"]['host'];
                $database = $config['local']["ss_$escola_banco"]['banco'];
                $user = $config['local']["ss_$escola_banco"]['user'];
                $password = $config['local']["ss_$escola_banco"]['senha'];

                $this->connect = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', $user, $password);
                $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                
                // Se falhar, tentar o banco da hospedagem
                $host_user = $config['hospedagem']["crede_users"]['host'];
                $database_user = $config['hospedagem']["crede_users"]['banco'];
                $user_user = $config['hospedagem']["crede_users"]['user'];
                $password_user = $config['hospedagem']["crede_users"]['senha'];

                $this->connect_users = new PDO('mysql:host=' . $host_user . ';dbname=' . $database_user . ';charset=utf8', $user_user, $password_user);
                $this->connect_users->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_users->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                $host = $config['hospedagem']["ss_$escola_banco"]['host'];
                $database = $config['hospedagem']["ss_$escola_banco"]['banco'];
                $user = $config['hospedagem']["ss_$escola_banco"]['user'];
                $password = $config['hospedagem']["ss_$escola_banco"]['senha'];

                $this->connect = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', $user, $password);
                $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {

            error_log("Erro de conexão com banco: " . $e->getMessage());
            $this->connect = null;
            header('location:../views/windows/desconnect.php');
            exit();
        }
    }
}
