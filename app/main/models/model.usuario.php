<?php
require_once("../config/connect.php");
class model_usuario extends connect
{
    private string $table1;
    private string $table2;
    private string $table3;
    private string $table4;
    private string $table5;

    function __construct()
    {
        parent::__construct();
        require('private/tables.php');
        $this->table1 = $table['crede_users'][1];
        $this->table2 = $table['crede_users'][2];
        $this->table3 = $table['crede_users'][3];
        $this->table4 = $table['crede_users'][4];
        $this->table5 = $table['crede_users'][5];
    }

    public function pre_cadastro(string $cpf, string $email): int
    {
        try {

            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table1 WHERE email = :email AND cpf = :cpf");
            $stmt_check->bindValue(":cpf", $cpf);
            $stmt_check->bindValue(":email", $email);
            $stmt_check->execute();

            if ($stmt_check->rowCount() > 0) {

                session_start();
                $_SESSION['email'] = $email;
                $_SESSION['cpf'] = $cpf;

                return 1;
            } else {

                return 3;
            }
        } catch (Exception $e) {

            return 0;
        }
    }
    public function primeiro_acesso($cpf, $email, $senha)
    {
        try {

            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table1 WHERE email = :email AND cpf = :cpf");
            $stmt_check->bindValue(":cpf", $cpf);
            $stmt_check->bindValue(":email", $email);
            $stmt_check->execute();

            if ($stmt_check->rowCount() > 0) {

                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt_check = $this->connect->prepare("UPDATE $this->table1 SET senha = :senha WHERE email = :email AND cpf = :cpf");
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

            return 0;
        }
    }
    public function login(string $email, string $senha): int
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT u.*, s.nome AS setor FROM $this->table1 u INNER JOIN $this->table2 s ON u.id_setor = s.id WHERE email = :email");
            $stmt_check->bindValue(':email', $email);
            $stmt_check->execute();

            $user = $stmt_check->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                if (password_verify($senha, $user['senha'])) {

                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['nome'] = $user['nome'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['setor'] = $user['setor'];
                    return 1;
                } else {
                    return 4;
                }
            } else {

                return 3;
            }
        } catch (Exception $e) {

            return 0;
        }
    }
}
