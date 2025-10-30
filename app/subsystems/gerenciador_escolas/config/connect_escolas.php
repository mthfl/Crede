<?php
class connect_escolas
{
    protected $connect;
    protected $connect_crede1;
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
                $host_crede1 = $config['local']["crede_users"]['host'];
                $database_crede1 = $config['local']["crede_users"]['banco'];
                $user_crede1 = $config['local']["crede_users"]['user'];
                $password_crede1 = $config['local']["crede_users"]['senha'];

                $this->connect_crede1 = new PDO('mysql:host=' . $host_crede1 . ';dbname=' . $database_crede1 . ';charset=utf8', $user_crede1, $password_crede1);
                $this->connect_crede1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_crede1->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                $host = $config['local']["ss_$escola_banco"]['host'];
                $database = $config['local']["ss_$escola_banco"]['banco'];
                $user = $config['local']["ss_$escola_banco"]['user'];
                $password = $config['local']["ss_$escola_banco"]['senha'];

                $this->connect = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', $user, $password);
                $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Se falhar, tentar o banco da hospedagem

                $host_crede1 = $config['hospedagem']["crede_users"]['host'];
                $database_crede1 = $config['hospedagem']["crede_users"]['banco'];
                $user_crede1 = $config['hospedagem']["crede_users"]['user'];
                $password_crede1 = $config['hospedagem']["crede_users"]['senha'];

                $this->connect_crede1 = new PDO('mysql:host=' . $host_crede1 . ';dbname=' . $database_crede1 . ';charset=utf8', $user_crede1, $password_crede1);
                $this->connect_crede1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_crede1->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                
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
