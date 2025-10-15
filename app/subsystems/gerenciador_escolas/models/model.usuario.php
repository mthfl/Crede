<?php
require_once(__DIR__ . '/../config/connect_escolas.php');
class model_usuario extends connect_escolas
{
    private string $table5;


    function __construct($escola_banco)
    {
        parent::__construct($escola_banco);
        $table = require(__DIR__ . '/../../../.env/tables.php');

        $this->table5 = $table["ss_$escola_banco"][5];
    }

    public function pre_cadastro(string $cpf, string $email): int
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table5 WHERE senha IS NULL AND email = :email AND cpf = :cpf");
            $stmt_check->bindValue(":cpf", $cpf);
            $stmt_check->bindValue(":email", $email);
            $stmt_check->execute();

            $user = $stmt_check->fetch(PDO::FETCH_ASSOC);
            if ($user['status'] == 0) {
                return 4;
            }
            if ($stmt_check->rowCount() == 1) {

                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['email'] = $email;
                $_SESSION['cpf'] = $cpf;

                return 1;
            } else {

                return 3;
            }
        } catch (Exception $e) {

            error_log("Erro no login: " . $e->getMessage());
            return 0;
        }
    }
    public function primeiro_acesso($cpf, $email, $senha)
    {
        try {

            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table5 WHERE email = :email AND cpf = :cpf");
            $stmt_check->bindValue(":cpf", $cpf);
            $stmt_check->bindValue(":email", $email);
            $stmt_check->execute();
            $user = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($stmt_check->rowCount() > 0) {

                if ($user['status'] == 0) {
                    return 4;
                }
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt_check = $this->connect->prepare("UPDATE $this->table5 SET senha = :senha WHERE email = :email AND cpf = :cpf");
                $stmt_check->bindValue(":cpf", $cpf);
                $stmt_check->bindValue(":email", $email);
                $stmt_check->bindValue(":senha", $hash);

                if ($stmt_check->execute()) {

                    return 1;
                } else {
                    return 2;
                }
            } else {

                return 3;
            }
        } catch (Exception $e) {

            error_log("Erro no login: " . $e->getMessage());
            return 0;
        }
    }
    public function login(string $email, string $senha, string $escola): int
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table5 WHERE email = :email");
            $stmt_check->bindValue(':email', $email);
            $stmt_check->execute();

            $user = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if ($user['status'] == 0) {
                    return 4;
                }
                if (password_verify($senha, $user['senha'])) {

                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }

                    $_SESSION['id'] = $user['id'];
                    $_SESSION['nome'] = $user['nome_user'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['tipo_usuario'] = $user['tipo_usuario'];
                    $_SESSION['id_perfil'] = $user['id_perfil'];
                    $_SESSION['escola'] = $escola;
                    return 1;
                } else {
                    return 3;
                }
            } else {

                return 3;
            }
        } catch (Exception $e) {

            error_log("Erro no login: " . $e->getMessage());
            return 0;
        }
    }
    public function verificar_email(string $email, string $escola_banco): int
    {
        try {
            $stmt = $this->connect->prepare("SELECT * FROM $this->table5 WHERE email = :email");
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                return 2;
            }
            
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['email_recuperar_senha'] = $email;
            $_SESSION['codigo'] = random_int(100000, 999999);
            if(mail($email,'RECUPERAR SENHA','Este é o codigo de verificarção: '.$_SESSION['codigo'].' para recuperar sua senha!','AreaDev')){
                return 2;
            }
            return 1;
        } catch (Exception $e) {
            error_log("Erro no login: " . $e->getMessage());
            return 0;
        }
    }
}
