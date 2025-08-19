<?php

// Versão temporária sem dependência do connect.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class sessions
{
    function autenticar_session()
    {
        if (!isset($_SESSION['email']) || !isset($_SESSION['nome']) || !isset($_SESSION['id'])) {
            session_unset();
            session_destroy();
            header('location:../login.php');
            exit();
        }
    }

    function tempo_session($tempo = 600)
    {
        if (isset($_SESSION['ultimo_acesso'])) {
            if (time() - $_SESSION['ultimo_acesso'] > $tempo) {
                session_unset();
                session_destroy();
                header('location:../login.php');
                exit();
            }
        }
        $_SESSION['ultimo_acesso'] = time();
    }

    function deslogar()
    {
        session_unset();
        session_destroy();
        header('location:../login.php');
        exit();
    }
}

if(isset($_GET['sair'])){
    $session = new sessions();
    $session->deslogar();
}
?>
