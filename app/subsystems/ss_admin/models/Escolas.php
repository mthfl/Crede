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

        // Conecta ao banco crede_users (mesma lógica do connect.php de outros módulos)
        $host = $config['local']['crede_users']['host'];
        $database = $config['local']['crede_users']['banco'];
        $user = $config['local']['crede_users']['user'];
        $password = $config['local']['crede_users']['senha'];

        try {
            $this->conn = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', $user, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
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
