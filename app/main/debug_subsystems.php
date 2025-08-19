<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug da Página Subsystems</h2>";

// Simular sessão de login
session_start();
$_SESSION['email'] = 'teste@teste.com';
$_SESSION['nome'] = 'teste';
$_SESSION['id'] = 1;
$_SESSION['setor'] = 'Administrativo';

echo "<p>1. Sessão criada:</p>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<p>2. Testando carregamento do sessions.php...</p>";
try {
    require_once(__DIR__."/models/sessions.php");
    echo "<p>✓ Sessions.php carregado com sucesso</p>";
    
    $session = new sessions();
    echo "<p>✓ Instância de sessions criada</p>";
    
    echo "<p>3. Testando autenticar_session()...</p>";
    $session->autenticar_session();
    echo "<p>✓ Autenticação de sessão passou</p>";
    
    echo "<p>4. Testando tempo_session()...</p>";
    $session->tempo_session();
    echo "<p>✓ Verificação de tempo de sessão passou</p>";
    
    echo "<p>5. ✓ Tudo funcionando! A página subsystems deve carregar normalmente agora.</p>";
    
} catch (Exception $e) {
    echo "<p>✗ Erro: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>";
    print_r($e->getTraceAsString());
    echo "</pre>";
}

echo "<h3>Teste Manual</h3>";
echo "<p><a href='views/subsystems.php'>Ir para subsystems.php</a></p>";
?>
