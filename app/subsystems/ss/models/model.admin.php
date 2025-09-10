<?php
require_once(__DIR__ . '/model.cadastrador.php');
class admin extends cadastrador
{
    function __construct($escola)
    {
        parent::__construct($escola);
    }

    /**
     * CRUD curso
     */
    public function cadastrar_curso(string $curso, string $cor): int
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table2 WHERE nome_curso = :curso");
            $stmt_check->bindValue(":curso", $curso);
            $stmt_check->execute();

            if ($stmt_check->rowCount() == 0) {

                $stmt_check = $this->connect->prepare("INSERT INTO $this->table2 VALUES (NULL, :curso, :cor)");
                $stmt_check->bindValue(":curso", $curso);
                $stmt_check->bindValue(":cor", $cor);

                if ($stmt_check->execute()) {

                    return 1;
                } else {

                    return 2;
                }
            } else {

                return 3;
            }
        } catch (PDOException $e) {
            return 0;
        }
    }
    public function editar_curso(int $id_curso, string $curso, string $cor): int
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table2 WHERE id = :id_curso");
            $stmt_check->bindValue(":id_curso", $id_curso);
            $stmt_check->execute();

            if ($stmt_check->rowCount() == 1) {

                $stmt_check = $this->connect->prepare(" UPDATE $this->table2 SET `nome_curso`= :nome_curso, `cor_curso`= :cor WHERE id = :id_curso");
                $stmt_check->bindValue(":id_curso", $id_curso);
                $stmt_check->bindValue(":nome_curso", $curso);
                $stmt_check->bindValue(":cor", $cor);

                if ($stmt_check->execute()) {

                    return 1;
                } else {

                    return 2;
                }
            } else {

                return 3;
            }
        } catch (PDOException $e) {
            return 0;
        }
    }
    public function excluir_curso(int $id_curso): int
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table2 WHERE id = :id_curso");
            $stmt_check->bindValue(":id_curso", $id_curso);
            $stmt_check->execute();

            if ($stmt_check->rowCount() == 1) {

                $stmt_check = $this->connect->prepare(" DELETE FROM $this->table2 WHERE id = :id_curso");
                $stmt_check->bindValue(":id_curso", $id_curso);

                if ($stmt_check->$stmt_check->execute()) {

                    return 1;
                } else {

                    return 2;
                }
            } else {

                return 3;
            }
        } catch (PDOException $e) {
            return 0;
        }
    }

    /*
     *CRUD usuario 
     */

    public function cadastrar_usuario(string $nome, string $email, string $cpf, string $tipo_usuario): int
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table5 WHERE cpf = :cpf");
            $stmt_check->bindValue(":cpf", $cpf);
            $stmt_check->execute();

            if ($stmt_check->rowCount() == 0) {

                $stmt_usuario = $this->connect->prepare("INSERT INTO $this->table5(`nome_user`, `email`, `cpf`, `tipo_usuario`) VALUES (:nome, :email, :cpf, :tipo)");
                $stmt_usuario->bindValue(":nome", $nome);
                $stmt_usuario->bindValue(":email", $email);
                $stmt_usuario->bindValue(":cpf", $cpf);
                $stmt_usuario->bindValue(":tipo", $tipo_usuario);

                if ($stmt_usuario->execute()) {

                    return 1;
                } else {

                    return 2;
                }
            } else {

                return 3;
            }
        } catch (PDOException $e) {
            return 0;
        }
    }
    public function editar_usuario(int $id_usuario, string $nome, string $email, string $cpf, string $tipo_usuario): int
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table5 WHERE id = :id_usuario");
            $stmt_check->bindValue(":id_usuario", $id_usuario);
            $stmt_check->execute();

            if ($stmt_check->rowCount() == 1) {

                $stmt_usuario = $this->connect->prepare(" UPDATE $this->table5 SET `nome_user`= :nome, `email`= :email, `cpf`= :cpf,`tipo_usuario`= :tipo WHERE id = :id_usuario");
                $stmt_usuario->bindValue(":id_usuario", $id_usuario);
                $stmt_usuario->bindValue(":nome", $nome);
                $stmt_usuario->bindValue(":email", $email);
                $stmt_usuario->bindValue(":cpf", $cpf);
                $stmt_usuario->bindValue(":tipo", $tipo_usuario);

                if ($stmt_usuario->execute()) {

                    return 1;
                } else {

                    return 2;
                }
            } else {

                return 3;
            }
        } catch (PDOException $e) {
            return 0;
        }
    }
    public function excluir_usuario(int $id_usuario): int
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table5 WHERE id = :id_usuario");
            $stmt_check->bindValue(":id_usuario", $id_usuario);
            $stmt_check->execute();

            if ($stmt_check->rowCount() == 1) {

                $stmt_usuario = $this->connect->prepare(" DELETE FROM $this->table5 WHERE id = :id_usuario");
                $stmt_usuario->bindValue(":id_usuario", $id_usuario);

                if ($stmt_usuario->execute()) {

                    return 1;
                } else {

                    return 2;
                }
            } else {

                return 3;
            }
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function limpar_banco(): int
    {
        try {
            $stmt_delete = $this->connect->query("DELETE FROM $this->table4 WHERE 0");
            $stmt_delete = $this->connect->query("DELETE FROM $this->table9 WHERE 0");
            $stmt_delete = $this->connect->query("DELETE FROM $this->table12 WHERE 0");
            $stmt_delete = $this->connect->query("DELETE FROM $this->table11 WHERE 0");
            $stmt_delete = $this->connect->query("DELETE FROM $this->table10 WHERE 0");
            $stmt_delete = $this->connect->query("DELETE FROM $this->table8 WHERE 0");
            $stmt_delete = $this->connect->query("DELETE FROM $this->table7 WHERE 0");
            $stmt_delete = $this->connect->query("DELETE FROM $this->table6 WHERE 0");
            $stmt_delete = $this->connect->query("DELETE FROM $this->table1 WHERE 0");
            $stmt_delete = $this->connect->query("DELETE FROM $this->table2 WHERE 0");

            return 1;
        } catch (PDOException $e) {
            return 0;
        }
    }
}
