<?php

class Escolas
{
    private $conn;
    private string $tableEscolas;

    public function __construct()
    {
        // Reaproveita as mesmas configs globais
        $config = require __DIR__ . '/../../../.env/config.php';
        $tables = require __DIR__ . '/../../../.env/tables.php';

        $this->tableEscolas = $tables['crede_estoque'][0] ?? 'escolas';
        // Pelo tables.php enviado, a tabela de escolas é crede_users[6]
        if (isset($tables['crede_users'][6])) {
            $this->tableEscolas = $tables['crede_users'][6];
        }

        // Tenta conectar ao banco crede_users (local primeiro, depois hospedagem)
        $connSuccess = false;
        
        // Tenta local primeiro
        try {
            $host = $config['local']['crede_users']['host'] ?? null;
            $database = $config['local']['crede_users']['banco'] ?? null;
            $user = $config['local']['crede_users']['user'] ?? null;
            $password = $config['local']['crede_users']['senha'] ?? null;

            if ($host && $database && $user !== null) {
                $this->conn = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', $user, $password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $connSuccess = true;
            }
        } catch (PDOException $e) {
            $connSuccess = false;
        }
        
        // Se local falhou, tenta hospedagem
        if (!$connSuccess) {
            try {
                $host = $config['hospedagem']['crede_users']['host'] ?? null;
                $database = $config['hospedagem']['crede_users']['banco'] ?? null;
                $user = $config['hospedagem']['crede_users']['user'] ?? null;
                $password = $config['hospedagem']['crede_users']['senha'] ?? null;

                if ($host && $database && $user !== null) {
                    $this->conn = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', $user, $password);
                    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                    $connSuccess = true;
                }
            } catch (PDOException $e) {
                $connSuccess = false;
            }
        }
        
        if (!$connSuccess) {
            $this->conn = null;
        }
    }

    public function listarEscolas(): array
    {
        if (!$this->conn) {
            return [];
        }

        try {
            $sql = 'SELECT * FROM ' . $this->tableEscolas . ' ORDER BY nome_escola';
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
