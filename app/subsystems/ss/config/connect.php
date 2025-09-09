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

            // Verificar se as configurações existem antes de acessá-las
            $db_key = "ss_$escola";
            
            // Tentar primeiro o banco local
            try {
                if (isset($config['local'][$db_key])) {
                    $host = $config['local'][$db_key]['host'] ?? 'localhost';
                    $database = $config['local'][$db_key]['banco'] ?? 'default_db';
                    $user = $config['local'][$db_key]['user'] ?? 'root';
                    $password = $config['local'][$db_key]['senha'] ?? '';

                    $this->connect = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', $user, $password);
                    $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                } else {
                    throw new PDOException("Configuração local não encontrada para $db_key");
                }
            } catch (PDOException $e) {
                // Se falhar, tentar o banco da hospedagem
                if (isset($config['hospedagem'][$db_key])) {
                    $host = $config['hospedagem'][$db_key]['host'] ?? 'localhost';
                    $database = $config['hospedagem'][$db_key]['banco'] ?? 'default_db';
                    $user = $config['hospedagem'][$db_key]['user'] ?? 'root';
                    $password = $config['hospedagem'][$db_key]['senha'] ?? '';

                    $this->connect = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', $user, $password);
                    $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                } else {
                    throw new PDOException("Configuração de hospedagem não encontrada para $db_key");
                }
            }
        } catch (PDOException $e) {
            error_log("Erro de conexão com banco: " . $e->getMessage());
            $this->connect = null;
            // header('location:../views/windows/desconnect.php');
            // exit();
        }
    }
}
