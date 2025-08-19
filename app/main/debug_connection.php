<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug de Conexão</h2>";

try {
    echo "<p>1. Carregando configuração...</p>";
    $config = require(__DIR__."/models/private/config.php");
    echo "<p>✓ Configuração carregada com sucesso</p>";
    
    echo "<pre>";
    print_r($config);
    echo "</pre>";
    
    echo "<p>2. Testando conexão local...</p>";
    try {
        $host = $config['local']['crede_users']['host'];
        $database = $config['local']['crede_users']['banco'];
        $user = $config['local']['crede_users']['user'];
        $password = $config['local']['crede_users']['senha'];
        
        echo "<p>Conectando a: $host, banco: $database, usuário: $user</p>";
        
        $pdo = new PDO('mysql:host=' . $host . ';dbname=' . $database, $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p>✓ Conexão local bem-sucedida</p>";
        
        // Testar se as tabelas existem
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<p>Tabelas encontradas:</p>";
        echo "<pre>";
        print_r($tables);
        echo "</pre>";
        
    } catch (PDOException $e) {
        echo "<p>✗ Erro na conexão local: " . $e->getMessage() . "</p>";
        
        echo "<p>3. Testando conexão da hospedagem...</p>";
        try {
            $host = $config['hospedagem']['crede_users']['host'];
            $database = $config['hospedagem']['crede_users']['banco'];
            $user = $config['hospedagem']['crede_users']['user'];
            $password = $config['hospedagem']['crede_users']['senha'];
            
            echo "<p>Conectando a: $host, banco: $database, usuário: $user</p>";
            
            $pdo = new PDO('mysql:host=' . $host . ';dbname=' . $database, $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "<p>✓ Conexão da hospedagem bem-sucedida</p>";
            
            // Testar se as tabelas existem
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "<p>Tabelas encontradas:</p>";
            echo "<pre>";
            print_r($tables);
            echo "</pre>";
            
        } catch (PDOException $e2) {
            echo "<p>✗ Erro na conexão da hospedagem: " . $e2->getMessage() . "</p>";
        }
    }
    
    echo "<p>4. Testando carregamento das tabelas...</p>";
    try {
        $table = require(__DIR__."/models/private/tables.php");
        echo "<p>✓ Tabelas carregadas com sucesso</p>";
        echo "<pre>";
        print_r($table);
        echo "</pre>";
    } catch (Exception $e) {
        echo "<p>✗ Erro ao carregar tabelas: " . $e->getMessage() . "</p>";
    }
    
    echo "<p>5. Testando criação do modelo...</p>";
    try {
        require_once(__DIR__."/models/model.usuario.php");
        $model = new model_usuario();
        echo "<p>✓ Modelo criado com sucesso</p>";
    } catch (Exception $e) {
        echo "<p>✗ Erro ao criar modelo: " . $e->getMessage() . "</p>";
        echo "<p>Stack trace:</p>";
        echo "<pre>";
        print_r($e->getTraceAsString());
        echo "</pre>";
    }
    
} catch (Exception $e) {
    echo "<p>✗ Erro geral: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>";
    print_r($e->getTraceAsString());
    echo "</pre>";
}

echo "<h3>Informações do PHP:</h3>";
echo "<p>Versão do PHP: " . phpversion() . "</p>";
echo "<p>Extensões carregadas:</p>";
echo "<pre>";
print_r(get_loaded_extensions());
echo "</pre>";
?>
