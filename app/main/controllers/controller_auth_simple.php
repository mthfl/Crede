<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Teste Simples do Controller</h2>";

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<p>✓ Requisição POST detectada</p>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    // Verificar se tem os campos necessários para login
    if (isset($_POST['email']) && isset($_POST['senha'])) {
        echo "<p>✓ Campos de login encontrados</p>";
        
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        
        echo "<p>Email: $email</p>";
        echo "<p>Senha: " . str_repeat('*', strlen($senha)) . "</p>";
        
        // Tentar carregar o modelo
        try {
            require_once("../models/model.usuario.php");
            echo "<p>✓ Modelo carregado</p>";
            
            $model_usuario = new model_usuario();
            echo "<p>✓ Instância do modelo criada</p>";
            
            $result = $model_usuario->login($email, $senha);
            echo "<p>Resultado do login: $result</p>";
            
            switch ($result) {
                case 1:
                    echo "<p>✓ Login bem-sucedido - Redirecionando...</p>";
                    header('Location: ../views/subsystems.php');
                    exit();
                case 2:
                    echo "<p>✗ Erro no sistema</p>";
                    header('Location: ../login.php?erro');
                    exit();
                case 3:
                    echo "<p>✗ Email não encontrado</p>";
                    header('Location: ../login.php?erro_email');
                    exit();
                case 4:
                    echo "<p>✗ Senha incorreta</p>";
                    header('Location: ../login.php?erro_senha');
                    exit();
                default:
                    echo "<p>✗ Falha desconhecida</p>";
                    header('Location: ../login.php?falha');
                    exit();
            }
            
        } catch (Exception $e) {
            echo "<p>✗ Erro ao processar login: " . $e->getMessage() . "</p>";
            echo "<pre>";
            print_r($e->getTraceAsString());
            echo "</pre>";
        }
        
    } else {
        echo "<p>✗ Campos de login não encontrados</p>";
    }
    
} else {
    echo "<p>✗ Não é uma requisição POST</p>";
    echo "<p>Método: " . $_SERVER['REQUEST_METHOD'] . "</p>";
}
?>
