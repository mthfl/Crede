<?php

// Inicia a sessão se ainda não estiver ativa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Classe para gerenciar sessões
class sessions {
    public function autenticar_session() {
        if (!isset($_SESSION['login']) || !isset($_SESSION['estoque_adm'])) {
            session_unset();
            session_destroy();
            header('Location: ../../../../../main/views/autenticacao/login.php');
            exit();
        }
        $logado = $_SESSION['login'];
    }

    public function tempo_session($tempo) {
        if (isset($_SESSION['ultimo_acesso'])) {
            if (time() - $_SESSION['ultimo_acesso'] > $tempo) {
                session_unset();
                session_destroy();
                header('Location: ../../../../../main/views/autenticacao/login.php');
                exit();
            }
        }
        $_SESSION['ultimo_acesso'] = time();
    }

    public function quebra_session() {
        // Garante que a sessão está ativa antes de destruí-la
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Limpa todas as variáveis de sessão
        session_unset();
        // Destrói a sessão
        session_destroy();
        // Remove o cookie de sessão
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        // Redireciona para a página de login
        header('Location: ../../../../../main/views/autenticacao/login.php');
        exit();
    }
}

// Verifica se o parâmetro 'sair' foi enviado
if (isset($_GET['sair'])) {
    $session = new sessions();
    $session->quebra_session();
}
?>