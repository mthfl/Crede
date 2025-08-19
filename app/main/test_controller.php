<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Teste do Controller de Autenticação</h2>";

// Simular dados POST para teste
$_POST['email'] = 'teste@teste.com';
$_POST['senha'] = '123456';

echo "<p>Dados simulados:</p>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<p>Verificando condições do controller...</p>";

// Verificar se os campos existem
if (isset($_POST['senha']) && !empty($_POST['senha']) && is_string($_POST['senha']) &&
    isset($_POST['email']) && !empty($_POST['email']) && is_string($_POST['email'])) {
    
    echo "<p>✓ Campos de login válidos</p>";
    
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    
    echo "<p>Email: $email</p>";
    echo "<p>Senha: " . str_repeat('*', strlen($senha)) . "</p>";
    
    try {
        echo "<p>Carregando modelo...</p>";
        require_once(__DIR__."/models/model.usuario.php");
        $model_usuario = new model_usuario();
        echo "<p>✓ Modelo carregado</p>";
        
        echo "<p>Executando login...</p>";
        $result = $model_usuario->login($email, $senha);
        echo "<p>Resultado: $result</p>";
        
        switch ($result) {
            case 1:
                echo "<p>✓ Login bem-sucedido - Redirecionando para subsystems.php</p>";
                echo "<p>Sessão criada:</p>";
                echo "<pre>";
                print_r($_SESSION);
                echo "</pre>";
                
                // Verificar se o arquivo de destino existe
                $arquivo_destino = __DIR__ . '/views/subsystems.php';
                if (file_exists($arquivo_destino)) {
                    echo "<p>✓ Arquivo subsystems.php existe</p>";
                } else {
                    echo "<p>✗ Arquivo subsystems.php NÃO existe em: $arquivo_destino</p>";
                }
                break;
            case 2:
                echo "<p>✗ Erro no sistema</p>";
                break;
            case 3:
                echo "<p>✗ Email não encontrado</p>";
                break;
            case 4:
                echo "<p>✗ Senha incorreta</p>";
                break;
            case 0:
                echo "<p>✗ Erro de conexão</p>";
                break;
            default:
                echo "<p>✗ Resultado desconhecido: $result</p>";
        }
        
    } catch (Exception $e) {
        echo "<p>✗ Erro no controller: " . $e->getMessage() . "</p>";
        echo "<p>Stack trace:</p>";
        echo "<pre>";
        print_r($e->getTraceAsString());
        echo "</pre>";
    }
    
} else {
    echo "<p>✗ Campos de login inválidos</p>";
}

echo "<h3>Verificando arquivos de destino</h3>";

$arquivos_destino = [
    'subsystems.php' => __DIR__ . '/views/subsystems.php',
    'login.php' => __DIR__ . '/login.php',
    'primeiro_acesso.php' => __DIR__ . '/views/primeiro_acesso.php'
];

foreach ($arquivos_destino as $nome => $caminho) {
    if (file_exists($caminho)) {
        echo "<p>✓ $nome existe</p>";
    } else {
        echo "<p>✗ $nome NÃO existe em: $caminho</p>";
    }
}

echo "<h3>Teste de Redirecionamento</h3>";
echo "<p>Se o login funcionou acima, você pode testar o redirecionamento manual:</p>";
echo "<p><a href='views/subsystems.php'>Ir para subsystems.php</a></p>";
?>
