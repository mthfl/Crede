<?php
class Connection
{
    protected $pdo;

    public function __construct()
    {
        $this->connect_database();
    }

    public function connect_database()
    {

        try {
            $local_dsn = "mysql:host=localhost;dbname=u750204740_estoque;charset=utf8mb4";
            $local_user = "root";
            $local_pass = "";
            $this->pdo = new PDO($local_dsn, $local_user, $local_pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Conexão local estabelecida com sucesso!";
            return true;
        } catch (PDOException $e) {
            // echo "Erro na conexão local: " . $e->getMessage();

            // Configurações de conexão da hospedagem
            $host_dsn = "mysql:host=u750204740_estoque;dbname=u750204740_estoque;charset=utf8mb4";
            $host_user = "u750204740_estoque";
            $host_pass = "paoComOvo123!@##";
            $this->pdo = new PDO($host_dsn, $host_user, $host_pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Conexão com hospedagem estabelecida com sucesso!";
            return true;
        } catch (PDOException $e) {
            echo "Erro na conexão com hospedagem: " . $e->getMessage();
            return false;
        }
    }

    public function getPdo()
    {
        return $this->pdo;
    }
}
