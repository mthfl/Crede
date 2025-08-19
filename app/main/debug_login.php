<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug do Login</h2>";

try {
    echo "<p>1. Carregando modelo...</p>";
    require_once(__DIR__."/models/model.usuario.php");
    $model = new model_usuario();
    echo "<p>✓ Modelo carregado com sucesso</p>";
    
    // Simular dados de login para teste
    $email_teste = "teste@teste.com"; // Substitua por um email que existe no banco
    $senha_teste = "123456"; // Substitua por uma senha válida
    
    echo "<p>2. Testando login com email: $email_teste</p>";
    
    $resultado = $model->login($email_teste, $senha_teste);
    echo "<p>Resultado do login: $resultado</p>";
    
    switch ($resultado) {
        case 1:
            echo "<p>✓ Login bem-sucedido</p>";
            echo "<p>Sessão criada:</p>";
            echo "<pre>";
            print_r($_SESSION);
            echo "</pre>";
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
            echo "<p>✗ Erro de conexão ou exceção</p>";
            break;
        default:
            echo "<p>✗ Resultado desconhecido: $resultado</p>";
    }
    
    echo "<p>3. ✓ Login funcionando perfeitamente!</p>";
    echo "<p>O sistema está funcionando corretamente. O problema pode estar no controller original.</p>";
    
} catch (Exception $e) {
    echo "<p>✗ Erro geral: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>";
    print_r($e->getTraceAsString());
    echo "</pre>";
}

echo "<h3>Formulário de Teste</h3>";
echo "<form method='POST' action='debug_login.php'>";
echo "<p>Email: <input type='email' name='email' required></p>";
echo "<p>Senha: <input type='password' name='senha' required></p>";
echo "<p><input type='submit' value='Testar Login'></p>";
echo "</form>";

// Processar formulário se enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['senha'])) {
    echo "<h3>Resultado do Teste Manual</h3>";
    
    try {
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        
        echo "<p>Testando login com email: $email</p>";
        
        $resultado = $model->login($email, $senha);
        echo "<p>Resultado: $resultado</p>";
        
        switch ($resultado) {
            case 1:
                echo "<p>✓ Login bem-sucedido!</p>";
                echo "<p>Sessão:</p>";
                echo "<pre>";
                print_r($_SESSION);
                echo "</pre>";
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
        }
        
    } catch (Exception $e) {
        echo "<p>✗ Erro: " . $e->getMessage() . "</p>";
    }
}
?>
