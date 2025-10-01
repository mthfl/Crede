<?php
require_once(__DIR__ . '/model.cadastrador.php');
class admin extends cadastrador
{
    protected string $table_user1;
    protected string $table_user2;
    function __construct($escola)
    {
        parent::__construct($escola);
        $table = require(__DIR__ . '/../../../.env/tables.php');
        $this->table_user1 = $table["crede_users"][1];
        $this->table_user2 = $table["crede_users"][2];
    }

    public function verificar_senha($email, $senha, $id_curso = null)
    {
        try {
            $stmt_check = $this->connect_users->prepare("SELECT * FROM $this->table_user1 WHERE email = :email");
            $stmt_check->bindValue(':email', $email);
            $stmt_check->execute();

            $user = $stmt_check->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                if (password_verify($senha, $user['senha'])) {

                    if ($id_curso !== null) {
                        $result = $this->excluir_candidato_curso($id_curso);
                    } else {
                        $result = $this->limpar_banco();
                    }
                    if ($result == 1) {
                        return 1;
                    } else {
                        return 2;
                    }
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

                $stmt_check = $this->connect->prepare("SELECT * FROM $this->table1 WHERE id_curso1 = :id_curso");
                $stmt_check->bindValue(":id_curso", $id_curso);
                $stmt_check->execute();

                if ($stmt_check->rowCount() > 0) {
                    return 4;
                }

                $stmt_check = $this->connect->prepare(" DELETE FROM $this->table2 WHERE id = :id_curso");
                $stmt_check->bindValue(":id_curso", $id_curso);

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

    public function excluir_candidato_curso(int $id_curso): int
    {
        try {

            $stmt_candidato = $this->connect->query("SELECT * FROM $this->table1 WHERE id_curso1 = '$id_curso'");
            $id_candidatos = $stmt_candidato->fetchAll(PDO::FETCH_ASSOC);

            foreach ($id_candidatos as $id_candidato) {
                $stmt_delete = $this->connect->prepare("DELETE FROM $this->table4 WHERE id_candidato = :id_candidato");
                $stmt_delete->bindValue(":id_candidato", $id_candidato['id']);
                $stmt_delete->execute();
                $stmt_delete = $this->connect->prepare("DELETE FROM $this->table9 WHERE id_candidato = :id_candidato");
                $stmt_delete->bindValue(":id_candidato", $id_candidato['id']);
                $stmt_delete->execute();
                $stmt_delete = $this->connect->prepare("DELETE FROM $this->table12 WHERE id_candidato = :id_candidato");
                $stmt_delete->bindValue(":id_candidato", $id_candidato['id']);
                $stmt_delete->execute();
                $stmt_delete = $this->connect->prepare("DELETE FROM $this->table11 WHERE id_candidato = :id_candidato");
                $stmt_delete->bindValue(":id_candidato", $id_candidato['id']);
                $stmt_delete->execute();
                $stmt_delete = $this->connect->prepare("DELETE FROM $this->table10 WHERE id_candidato = :id_candidato");
                $stmt_delete->bindValue(":id_candidato", $id_candidato['id']);
                $stmt_delete->execute();
                $stmt_delete = $this->connect->prepare("DELETE FROM $this->table8 WHERE id_candidato = :id_candidato");
                $stmt_delete->bindValue(":id_candidato", $id_candidato['id']);
                $stmt_delete->execute();
                $stmt_delete = $this->connect->prepare("DELETE FROM $this->table7 WHERE id_candidato = :id_candidato");
                $stmt_delete->bindValue(":id_candidato", $id_candidato['id']);
                $stmt_delete->execute();
                $stmt_delete = $this->connect->prepare("DELETE FROM $this->table6 WHERE id_candidato = :id_candidato");
                $stmt_delete->bindValue(":id_candidato", $id_candidato['id']);
                $stmt_delete->execute();
                $stmt_candidato = $this->connect->prepare(" DELETE FROM $this->table1 WHERE id = :id_candidato");
                $stmt_candidato->bindValue(":id_candidato", $id_candidato['id']);
                $stmt_candidato->execute();
                $stmt_candidato = $this->connect->prepare(" DELETE FROM $this->table2 WHERE id = :id_curso");
                $stmt_candidato->bindValue(":id_curso", $id_curso);
                $stmt_candidato->execute();
            }

            return 1;
        } catch (PDOException $e) {
            return 0;
        }
    }

    /*
     *CRUD usuario 
     */

    public function cadastrar_usuario(string $nome, string $email, string $cpf, string $tipo_usuario, string $perfil): int
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table5 WHERE cpf = :cpf");
            $stmt_check->bindValue(":cpf", $cpf);
            $stmt_check->execute();

            if ($stmt_check->rowCount() == 0) {

                $stmt_usuario = $this->connect->prepare("INSERT INTO $this->table5(`nome_user`, `email`, `cpf`, `tipo_usuario`, `id_perfil`) VALUES (:nome, :email, :cpf, :tipo, :perfil)");
                $stmt_usuario->bindValue(":nome", $nome);
                $stmt_usuario->bindValue(":email", $email);
                $stmt_usuario->bindValue(":cpf", $cpf);
                $stmt_usuario->bindValue(":tipo", $tipo_usuario);
                $stmt_usuario->bindValue(":perfil", $perfil);
                if ($stmt_usuario->execute())  {

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
    public function editar_usuario(int $id_usuario, string $nome, string $email, string $cpf, string $tipo_usuario, string $perfil): int
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table5 WHERE id = :id_usuario");
            $stmt_check->bindValue(":id_usuario", $id_usuario);
            $stmt_check->execute();

            if ($stmt_check->rowCount() == 1) {

                $stmt_usuario = $this->connect->prepare(" UPDATE $this->table5 SET `nome_user`= :nome, `email`= :email, `cpf`= :cpf,`tipo_usuario`= :tipo, `id_perfil`= :perfil WHERE id = :id_usuario");
                $stmt_usuario->bindValue(":id_usuario", $id_usuario);
                $stmt_usuario->bindValue(":nome", $nome);
                $stmt_usuario->bindValue(":email", $email);
                $stmt_usuario->bindValue(":cpf", $cpf);
                $stmt_usuario->bindValue(":tipo", $tipo_usuario);
                $stmt_usuario->bindValue(":perfil", $perfil);   
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

    /*
     *CRUD bairros de cota
     */
    public function cadastrar_bairro(string $nome): int
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table13 WHERE bairros = :nome");
            $stmt_check->bindValue(":nome", $nome);
            $stmt_check->execute();

            if ($stmt_check->rowCount() == 0) {

                $stmt_usuario = $this->connect->prepare("INSERT INTO $this->table13(`bairros`) VALUES (:nome)");
                $stmt_usuario->bindValue(":nome", $nome);

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
    public function editar_bairro(int $id_bairro, string $nome): int
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table13 WHERE id = :id_bairro");
            $stmt_check->bindValue(":id_bairro", $id_bairro);
            $stmt_check->execute();

            if ($stmt_check->rowCount() == 1) {

                $stmt_usuario = $this->connect->prepare(" UPDATE $this->table13 SET `bairros`= :nome WHERE id = :id_bairro");
                $stmt_usuario->bindValue(":id_bairro", $id_bairro);
                $stmt_usuario->bindValue(":nome", $nome);

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
    public function excluir_bairro(int $id_bairro): int
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table13 WHERE id = :id_bairro");
            $stmt_check->bindValue(":id_bairro", $id_bairro);
            $stmt_check->execute();

            if ($stmt_check->rowCount() == 1) {

                $stmt_usuario = $this->connect->prepare(" DELETE FROM $this->table13 WHERE id = :id_bairro");
                $stmt_usuario->bindValue(":id_bairro", $id_bairro);

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

    public function excluir_candidato(int $id_candidato)
    {

        //try {
        $stmt_check = $this->connect->prepare("SELECT * FROM $this->table1 WHERE id = :id_candidato");
        $stmt_check->bindValue(":id_candidato", $id_candidato);
        $stmt_check->execute();

        if ($stmt_check->rowCount() == 1) {

            $stmt_delete = $this->connect->prepare("DELETE FROM $this->table4 WHERE id_candidato = :id_candidato");
            $stmt_delete->bindValue(":id_candidato", $id_candidato);
            $stmt_delete->execute();
            $stmt_delete = $this->connect->prepare("DELETE FROM $this->table9 WHERE id_candidato = :id_candidato");
            $stmt_delete->bindValue(":id_candidato", $id_candidato);
            $stmt_delete->execute();
            $stmt_delete = $this->connect->prepare("DELETE FROM $this->table12 WHERE id_candidato = :id_candidato");
            $stmt_delete->bindValue(":id_candidato", $id_candidato);
            $stmt_delete->execute();
            $stmt_delete = $this->connect->prepare("DELETE FROM $this->table11 WHERE id_candidato = :id_candidato");
            $stmt_delete->bindValue(":id_candidato", $id_candidato);
            $stmt_delete->execute();
            $stmt_delete = $this->connect->prepare("DELETE FROM $this->table10 WHERE id_candidato = :id_candidato");
            $stmt_delete->bindValue(":id_candidato", $id_candidato);
            $stmt_delete->execute();
            $stmt_delete = $this->connect->prepare("DELETE FROM $this->table8 WHERE id_candidato = :id_candidato");
            $stmt_delete->bindValue(":id_candidato", $id_candidato);
            $stmt_delete->execute();
            $stmt_delete = $this->connect->prepare("DELETE FROM $this->table7 WHERE id_candidato = :id_candidato");
            $stmt_delete->bindValue(":id_candidato", $id_candidato);
            $stmt_delete->execute();
            $stmt_delete = $this->connect->prepare("DELETE FROM $this->table6 WHERE id_candidato = :id_candidato");
            $stmt_delete->bindValue(":id_candidato", $id_candidato);
            $stmt_delete->execute();
            $stmt_candidato = $this->connect->prepare(" DELETE FROM $this->table1 WHERE id = :id_candidato");
            $stmt_candidato->bindValue(":id_candidato", $id_candidato);

            if ($stmt_candidato->execute()) {

                return 1;
            } else {

                return 2;
            }
        } else {

            return 3;
        }
        /*} catch (PDOException $e) {
            return 0;
        }*/
    }
    public function limpar_banco(): int
    {
        try {
            $stmt_delete = $this->connect->query("DELETE FROM $this->table14");
            $stmt_delete = $this->connect->query("DELETE FROM $this->table4");
            $stmt_delete = $this->connect->query("DELETE FROM $this->table9");
            $stmt_delete = $this->connect->query("DELETE FROM $this->table12");
            $stmt_delete = $this->connect->query("DELETE FROM $this->table11");
            $stmt_delete = $this->connect->query("DELETE FROM $this->table10");
            $stmt_delete = $this->connect->query("DELETE FROM $this->table8");
            $stmt_delete = $this->connect->query("DELETE FROM $this->table7");
            $stmt_delete = $this->connect->query("DELETE FROM $this->table6");
            $stmt_delete = $this->connect->query("DELETE FROM $this->table1");
            $stmt_delete = $this->connect->query("DELETE FROM $this->table2");
            $stmt_delete = $this->connect->query("DELETE FROM $this->table13");

            return 1;
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Editar candidato e notas (6º,7º,8º e 9º) com base no banco
     */
    public function editar_candidato(
        int $id_candidato,
        string $nome,
        string $data_nascimento,
        int $bairro,
        int $publica,
        int $pcd,
        float $lp_6ano,
        float $artes_6ano,
        float $ef_6ano,
        float $li_6ano,
        float $mate_6ano,
        float $cien_6ano,
        float $geo_6ano,
        float $hist_6ano,
        float $reli_6ano,
        float $lp_7ano,
        float $artes_7ano,
        float $ef_7ano,
        float $li_7ano,
        float $mate_7ano,
        float $cien_7ano,
        float $geo_7ano,
        float $hist_7ano,
        float $reli_7ano,
        float $lp_8ano,
        float $artes_8ano,
        float $ef_8ano,
        float $li_8ano,
        float $mate_8ano,
        float $cien_8ano,
        float $geo_8ano,
        float $hist_8ano,
        float $reli_8ano,
        float $lp_9ano,
        float $artes_9ano,
        float $ef_9ano,
        float $li_9ano,
        float $mate_9ano,
        float $cien_9ano,
        float $geo_9ano,
        float $hist_9ano,
        float $reli_9ano,
        float $lp_1bim_9ano,
        float $artes_1bim_9ano,
        float $ef_1bim_9ano,
        float $li_1bim_9ano,
        float $mate_1bim_9ano,
        float $cien_1bim_9ano,
        float $geo_1bim_9ano,
        float $hist_1bim_9ano,
        float $reli_1bim_9ano,
        float $lp_2bim_9ano,
        float $artes_2bim_9ano,
        float $ef_2bim_9ano,
        float $li_2bim_9ano,
        float $mate_2bim_9ano,
        float $cien_2bim_9ano,
        float $geo_2bim_9ano,
        float $hist_2bim_9ano,
        float $reli_2bim_9ano,
        float $lp_3bim_9ano,
        float $artes_3bim_9ano,
        float $ef_3bim_9ano,
        float $li_3bim_9ano,
        float $mate_3bim_9ano,
        float $cien_3bim_9ano,
        float $geo_3bim_9ano,
        float $hist_3bim_9ano,
        float $reli_3bim_9ano
    ): int {
        try {
            // Atualiza dados do candidato
            $stmt = $this->connect->prepare("UPDATE $this->table1 SET nome=:nome, data_nascimento=:data_nascimento, bairro=:bairro, publica=:publica, pcd=:pcd WHERE id=:id");
            $stmt->bindValue(":nome", $nome);
            $stmt->bindValue(":data_nascimento", $data_nascimento);
            $stmt->bindValue(":bairro", $bairro);
            $stmt->bindValue(":publica", $publica);
            $stmt->bindValue(":pcd", $pcd);
            $stmt->bindValue(":id", $id_candidato);
            if (!$stmt->execute()) return 2;

            // Atualiza 6º ano
            $stmt = $this->connect->prepare("UPDATE $this->table6 SET l_portuguesa=:lp, artes=:artes, educacao_fisica=:ef, l_inglesa=:li, matematica=:mate, ciencias=:cien, geografia=:geo, historia=:hist, religiao=:reli WHERE id_candidato=:id");
            $stmt->bindValue(":id", $id_candidato);
            $stmt->bindValue(":lp", $lp_6ano);
            $stmt->bindValue(":artes", $artes_6ano);
            $stmt->bindValue(":ef", $ef_6ano);
            $stmt->bindValue(":li", $li_6ano);
            $stmt->bindValue(":mate", $mate_6ano);
            $stmt->bindValue(":cien", $cien_6ano);
            $stmt->bindValue(":geo", $geo_6ano);
            $stmt->bindValue(":hist", $hist_6ano);
            $stmt->bindValue(":reli", $reli_6ano);
            $stmt->execute();

            // Atualiza 7º ano
            $stmt = $this->connect->prepare("UPDATE $this->table7 SET l_portuguesa=:lp, artes=:artes, educacao_fisica=:ef, l_inglesa=:li, matematica=:mate, ciencias=:cien, geografia=:geo, historia=:hist, religiao=:reli WHERE id_candidato=:id");
            $stmt->bindValue(":id", $id_candidato);
            $stmt->bindValue(":lp", $lp_7ano);
            $stmt->bindValue(":artes", $artes_7ano);
            $stmt->bindValue(":ef", $ef_7ano);
            $stmt->bindValue(":li", $li_7ano);
            $stmt->bindValue(":mate", $mate_7ano);
            $stmt->bindValue(":cien", $cien_7ano);
            $stmt->bindValue(":geo", $geo_7ano);
            $stmt->bindValue(":hist", $hist_7ano);
            $stmt->bindValue(":reli", $reli_7ano);
            $stmt->execute();

            // Atualiza 8º ano
            $stmt = $this->connect->prepare("UPDATE $this->table8 SET l_portuguesa=:lp, artes=:artes, educacao_fisica=:ef, l_inglesa=:li, matematica=:mate, ciencias=:cien, geografia=:geo, historia=:hist, religiao=:reli WHERE id_candidato=:id");
            $stmt->bindValue(":id", $id_candidato);
            $stmt->bindValue(":lp", $lp_8ano);
            $stmt->bindValue(":artes", $artes_8ano);
            $stmt->bindValue(":ef", $ef_8ano);
            $stmt->bindValue(":li", $li_8ano);
            $stmt->bindValue(":mate", $mate_8ano);
            $stmt->bindValue(":cien", $cien_8ano);
            $stmt->bindValue(":geo", $geo_8ano);
            $stmt->bindValue(":hist", $hist_8ano);
            $stmt->bindValue(":reli", $reli_8ano);
            $stmt->execute();

            // Se houver bimestres informados, atualiza 1º, 2º e 3º bimestres do 9º ano; caso contrário, usa médias
            if (
                $li_1bim_9ano > 0 || $lp_1bim_9ano > 0 || $mate_1bim_9ano > 0 || $cien_1bim_9ano > 0 || $geo_1bim_9ano > 0 || $hist_1bim_9ano > 0 || $reli_1bim_9ano > 0 || $artes_1bim_9ano > 0 || $ef_1bim_9ano > 0
                || $li_2bim_9ano > 0 || $lp_2bim_9ano > 0 || $mate_2bim_9ano > 0 || $cien_2bim_9ano > 0 || $geo_2bim_9ano > 0 || $hist_2bim_9ano > 0 || $reli_2bim_9ano > 0 || $artes_2bim_9ano > 0 || $ef_2bim_9ano > 0
                || $li_3bim_9ano > 0 || $lp_3bim_9ano > 0 || $mate_3bim_9ano > 0 || $cien_3bim_9ano > 0 || $geo_3bim_9ano > 0 || $hist_3bim_9ano > 0 || $reli_3bim_9ano > 0 || $artes_3bim_9ano > 0 || $ef_3bim_9ano > 0
            ) {

                // 1º bimestre
                $stmt = $this->connect->prepare("UPDATE $this->table10 SET l_portuguesa=:lp, artes=:artes, educacao_fisica=:ef, l_inglesa=:li, matematica=:mate, ciencias=:cien, geografia=:geo, historia=:hist, religiao=:reli WHERE id_candidato=:id");
                $stmt->bindValue(":id", $id_candidato);
                $stmt->bindValue(":lp", $lp_1bim_9ano);
                $stmt->bindValue(":artes", $artes_1bim_9ano);
                $stmt->bindValue(":ef", $ef_1bim_9ano);
                $stmt->bindValue(":li", $li_1bim_9ano);
                $stmt->bindValue(":mate", $mate_1bim_9ano);
                $stmt->bindValue(":cien", $cien_1bim_9ano);
                $stmt->bindValue(":geo", $geo_1bim_9ano);
                $stmt->bindValue(":hist", $hist_1bim_9ano);
                $stmt->bindValue(":reli", $reli_1bim_9ano);
                $stmt->execute();

                // 2º bimestre
                $stmt = $this->connect->prepare("UPDATE $this->table11 SET l_portuguesa=:lp, artes=:artes, educacao_fisica=:ef, l_inglesa=:li, matematica=:mate, ciencias=:cien, geografia=:geo, historia=:hist, religiao=:reli WHERE id_candidato=:id");
                $stmt->bindValue(":id", $id_candidato);
                $stmt->bindValue(":lp", $lp_2bim_9ano);
                $stmt->bindValue(":artes", $artes_2bim_9ano);
                $stmt->bindValue(":ef", $ef_2bim_9ano);
                $stmt->bindValue(":li", $li_2bim_9ano);
                $stmt->bindValue(":mate", $mate_2bim_9ano);
                $stmt->bindValue(":cien", $cien_2bim_9ano);
                $stmt->bindValue(":geo", $geo_2bim_9ano);
                $stmt->bindValue(":hist", $hist_2bim_9ano);
                $stmt->bindValue(":reli", $reli_2bim_9ano);
                $stmt->execute();

                // 3º bimestre
                $stmt = $this->connect->prepare("UPDATE $this->table12 SET l_portuguesa=:lp, artes=:artes, educacao_fisica=:ef, l_inglesa=:li, matematica=:mate, ciencias=:cien, geografia=:geo, historia=:hist, religiao=:reli WHERE id_candidato=:id");
                $stmt->bindValue(":id", $id_candidato);
                $stmt->bindValue(":lp", $lp_3bim_9ano);
                $stmt->bindValue(":artes", $artes_3bim_9ano);
                $stmt->bindValue(":ef", $ef_3bim_9ano);
                $stmt->bindValue(":li", $li_3bim_9ano);
                $stmt->bindValue(":mate", $mate_3bim_9ano);
                $stmt->bindValue(":cien", $cien_3bim_9ano);
                $stmt->bindValue(":geo", $geo_3bim_9ano);
                $stmt->bindValue(":hist", $hist_3bim_9ano);
                $stmt->bindValue(":reli", $reli_3bim_9ano);
                $stmt->execute();

                // recalcula médias e atualiza 9º (média)
                $li_9ano = ($li_1bim_9ano + $li_2bim_9ano + $li_3bim_9ano) / 3;
                $lp_9ano = ($lp_1bim_9ano + $lp_2bim_9ano + $lp_3bim_9ano) / 3;
                $artes_9ano = ($artes_1bim_9ano + $artes_2bim_9ano + $artes_3bim_9ano) / 3;
                $ef_9ano = ($ef_1bim_9ano + $ef_2bim_9ano + $ef_3bim_9ano) / 3;
                $mate_9ano = ($mate_1bim_9ano + $mate_2bim_9ano + $mate_3bim_9ano) / 3;
                $cien_9ano = ($cien_1bim_9ano + $cien_2bim_9ano + $cien_3bim_9ano) / 3;
                $geo_9ano = ($geo_1bim_9ano + $geo_2bim_9ano + $geo_3bim_9ano) / 3;
                $hist_9ano = ($hist_1bim_9ano + $hist_2bim_9ano + $hist_3bim_9ano) / 3;
                $d = 3;
                if ($reli_1bim_9ano == 0) {
                    $d -= 1;
                }
                if ($reli_2bim_9ano == 0) {
                    $d -= 1;
                }
                if ($reli_3bim_9ano == 0) {
                    $d -= 1;
                }
                $reli_9ano = $d == 0 ? 0 : ($reli_1bim_9ano + $reli_2bim_9ano + $reli_3bim_9ano) / $d;
            }

            // Atualiza 9º (média)
            $stmt = $this->connect->prepare("UPDATE $this->table9 SET l_portuguesa=:lp, artes=:artes, educacao_fisica=:ef, l_inglesa=:li, matematica=:mate, ciencias=:cien, geografia=:geo, historia=:hist, religiao=:reli WHERE id_candidato=:id");
            $stmt->bindValue(":id", $id_candidato);
            $stmt->bindValue(":lp", $lp_9ano);
            $stmt->bindValue(":artes", $artes_9ano);
            $stmt->bindValue(":ef", $ef_9ano);
            $stmt->bindValue(":li", $li_9ano);
            $stmt->bindValue(":mate", $mate_9ano);
            $stmt->bindValue(":cien", $cien_9ano);
            $stmt->bindValue(":geo", $geo_9ano);
            $stmt->bindValue(":hist", $hist_9ano);
            $stmt->bindValue(":reli", $reli_9ano);
            $stmt->execute();

            // ===== Recalcular e atualizar tabela de médias ($this->table4) =====
            $stmt_select_6ano = $this->connect->prepare("SELECT id FROM $this->table6 WHERE id_candidato = :id");
            $stmt_select_6ano->bindValue(":id", $id_candidato);
            $stmt_select_6ano->execute();
            $id_notas_6ano = $stmt_select_6ano->fetch(PDO::FETCH_ASSOC);

            $stmt_select_7ano = $this->connect->prepare("SELECT id FROM $this->table7 WHERE id_candidato = :id");
            $stmt_select_7ano->bindValue(":id", $id_candidato);
            $stmt_select_7ano->execute();
            $id_notas_7ano = $stmt_select_7ano->fetch(PDO::FETCH_ASSOC);

            $stmt_select_8ano = $this->connect->prepare("SELECT id FROM $this->table8 WHERE id_candidato = :id");
            $stmt_select_8ano->bindValue(":id", $id_candidato);
            $stmt_select_8ano->execute();
            $id_notas_8ano = $stmt_select_8ano->fetch(PDO::FETCH_ASSOC);

            $stmt_select_9ano = $this->connect->prepare("SELECT id FROM $this->table9 WHERE id_candidato = :id");
            $stmt_select_9ano->bindValue(":id", $id_candidato);
            $stmt_select_9ano->execute();
            $id_notas_9ano = $stmt_select_9ano->fetch(PDO::FETCH_ASSOC);

            $l_portuguesa_media = ($lp_6ano + $lp_7ano + $lp_8ano + $lp_9ano) / 4;
            $l_inglesa_media = ($li_6ano + $li_7ano + $li_8ano + $li_9ano) / 4;
            $matematica_media = ($mate_6ano + $mate_7ano + $mate_8ano + $mate_9ano) / 4;
            $ciencias_media = ($cien_6ano + $cien_7ano + $cien_8ano + $cien_9ano) / 4;
            $geografia_media = ($geo_6ano + $geo_7ano + $geo_8ano + $geo_9ano) / 4;
            $historia_media = ($hist_6ano + $hist_7ano + $hist_8ano + $hist_9ano) / 4;

            $d_media = 4;
            if ($artes_6ano == 0) {
                $d_media -= 1;
            }
            if ($artes_7ano == 0) {
                $d_media -= 1;
            }
            if ($artes_8ano == 0) {
                $d_media -= 1;
            }
            if ($artes_9ano == 0) {
                $d_media -= 1;
            }
            $artes_media = ($ef_6ano + $ef_7ano + $ef_8ano + $ef_9ano) / $d_media; // espelha a lógica existente no cadastro

            $d_media = 4;
            if ($ef_6ano == 0) {
                $d_media -= 1;
            }
            if ($ef_7ano == 0) {
                $d_media -= 1;
            }
            if ($ef_8ano == 0) {
                $d_media -= 1;
            }
            if ($ef_9ano == 0) {
                $d_media -= 1;
            }
            $ef_media = ($ef_6ano + $ef_7ano + $ef_8ano + $ef_9ano) / $d_media;

            $d_media = 4;
            if ($reli_6ano == 0) {
                $d_media -= 1;
            }
            if ($reli_7ano == 0) {
                $d_media -= 1;
            }
            if ($reli_8ano == 0) {
                $d_media -= 1;
            }
            if ($reli_9ano == 0) {
                $d_media -= 1;
            }
            $reli_media = ($reli_6ano + $reli_7ano + $reli_8ano + $reli_9ano) / $d_media;

            $d_media_final = 9;
            if ($artes_media == 0) {
                $d_media_final -= 1;
            }
            if ($ef_media == 0) {
                $d_media_final -= 1;
            }
            if ($reli_media == 0) {
                $d_media -= 1;
            }
            $media_final = ($l_portuguesa_media + $artes_media + $ef_media + $l_inglesa_media + $matematica_media + $ciencias_media + $geografia_media + $historia_media + $reli_media) / $d_media_final;

            // Upsert na tabela de médias
            $stmt_check_media = $this->connect->prepare("SELECT id FROM $this->table4 WHERE id_candidato = :id");
            $stmt_check_media->bindValue(":id", $id_candidato);
            $stmt_check_media->execute();
            $has_media = $stmt_check_media->fetch(PDO::FETCH_ASSOC);

            if ($has_media) {
                $stmt = $this->connect->prepare("UPDATE $this->table4 SET id_notas_6ano=:n6, id_notas_7ano=:n7, id_notas_8ano=:n8, id_notas_9ano=:n9, l_portuguesa_media=:lp_m, artes_media=:artes_m, educacao_fisica_media=:ef_m, l_inglesa_media=:li_m, matematica_media=:mate_m, ciencias_media=:cien_m, geografia_media=:geo_m, historia_media=:hist_m, religiao_media=:reli_m, media_final=:media WHERE id_candidato = :id");
                $stmt->bindValue(":id", $id_candidato);
                $stmt->bindValue(":n6", $id_notas_6ano['id'] ?? null, PDO::PARAM_INT);
                $stmt->bindValue(":n7", $id_notas_7ano['id'] ?? null, PDO::PARAM_INT);
                $stmt->bindValue(":n8", $id_notas_8ano['id'] ?? null, PDO::PARAM_INT);
                $stmt->bindValue(":n9", $id_notas_9ano['id'] ?? null, PDO::PARAM_INT);
                $stmt->bindValue(":lp_m", $l_portuguesa_media);
                $stmt->bindValue(":artes_m", $artes_media);
                $stmt->bindValue(":ef_m", $ef_media);
                $stmt->bindValue(":li_m", $l_inglesa_media);
                $stmt->bindValue(":mate_m", $matematica_media);
                $stmt->bindValue(":cien_m", $ciencias_media);
                $stmt->bindValue(":geo_m", $geografia_media);
                $stmt->bindValue(":hist_m", $historia_media);
                $stmt->bindValue(":reli_m", $reli_media);
                $stmt->bindValue(":media", $media_final);
                $stmt->execute();
            } else {
                $stmt = $this->connect->prepare("INSERT INTO $this->table4 (id, id_candidato, id_notas_6ano, id_notas_7ano, id_notas_8ano, id_notas_9ano, l_portuguesa_media, artes_media, educacao_fisica_media, l_inglesa_media, matematica_media, ciencias_media, geografia_media, historia_media, religiao_media, media_final) VALUES (NULL, :id, :n6, :n7, :n8, :n9, :lp_m, :artes_m, :ef_m, :li_m, :mate_m, :cien_m, :geo_m, :hist_m, :reli_m, :media)");
                $stmt->bindValue(":id", $id_candidato);
                $stmt->bindValue(":n6", $id_notas_6ano['id'] ?? null, PDO::PARAM_INT);
                $stmt->bindValue(":n7", $id_notas_7ano['id'] ?? null, PDO::PARAM_INT);
                $stmt->bindValue(":n8", $id_notas_8ano['id'] ?? null, PDO::PARAM_INT);
                $stmt->bindValue(":n9", $id_notas_9ano['id'] ?? null, PDO::PARAM_INT);
                $stmt->bindValue(":lp_m", $l_portuguesa_media);
                $stmt->bindValue(":artes_m", $artes_media);
                $stmt->bindValue(":ef_m", $ef_media);
                $stmt->bindValue(":li_m", $l_inglesa_media);
                $stmt->bindValue(":mate_m", $matematica_media);
                $stmt->bindValue(":cien_m", $ciencias_media);
                $stmt->bindValue(":geo_m", $geografia_media);
                $stmt->bindValue(":hist_m", $historia_media);
                $stmt->bindValue(":reli_m", $reli_media);
                $stmt->bindValue(":media", $media_final);
                $stmt->execute();
            }

            return 1;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function requisicao_alteracao_realizada(int $id_requisicao): int
    {
        try {
            $stmt = $this->connect->prepare("UPDATE $this->table14 SET status = 'Concluido' WHERE id = :id");
            $stmt->bindValue(":id", $id_requisicao);
            if ($stmt->execute()) {
                return 1;
            } else {
                return 2;
            }
        } catch (PDOException $e) {
            return 0;
        }
    }
    public function requisicao_alteracao_recusada(int $id_requisicao): int
    {
        try {
            $stmt = $this->connect->prepare("UPDATE $this->table14 SET status = 'Recusado' WHERE id = :id");
            $stmt->bindValue(":id", $id_requisicao);
            if ($stmt->execute()) {
                return 1;
            } else {
                return 2;
            }
        } catch (PDOException $e) {
            return 0;
        }
 
    }
    public function requisicao_alteracao_pendente(int $id_requisicao): int
    {
        try {
            $stmt = $this->connect->prepare("UPDATE $this->table14 SET status = 'Pendente' WHERE id = :id");
            $stmt->bindValue(":id", $id_requisicao);
            if ($stmt->execute()) {
                return 1;
            } else {
                return 2;
            }
        } catch (PDOException $e) {
            return 0;
        }
    }
    public function desabilitar_usuario(int $id_usuario): int
    {
        try {
            $stmt = $this->connect->prepare("UPDATE $this->table5 SET status = 0 WHERE id = :id");
            $stmt->bindValue(":id", $id_usuario);
            if ($stmt->execute()) {
                return 1;
            } else {
                return 2;
            }
        } catch (PDOException $e) {
            return 0;
        }
    }
    public function habilitar_usuario(int $id_usuario): int
    {
        try {
            $stmt = $this->connect->prepare("UPDATE $this->table5 SET status = 1 WHERE id = :id");
            $stmt->bindValue(":id", $id_usuario);
            if ($stmt->execute()) {
                return 1;
            } else {
                return 2;
            }
        } catch (PDOException $e) {
            return 0;
        }
    }
    public function cadastrar_perfil(string $perfil): int
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table15 WHERE nome_perfil = :perfil");
            $stmt_check->bindValue(":perfil", $perfil);
            $stmt_check->execute();
            if ($stmt_check->rowCount() == 1) {
                return 3;
            }
            $stmt = $this->connect->prepare("INSERT INTO $this->table15 (nome_perfil) VALUES (:perfil)");
            $stmt->bindValue(":perfil", $perfil);
            if ($stmt->execute()) {
                return 1;
            } else {
                return 2;
            }
        } catch (PDOException $e) {
            return 0;
        }
    }
    public function editar_perfil(int $id_perfil, string $perfil): int
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table15 WHERE nome_perfil = :perfil AND id != :id");
            $stmt_check->bindValue(":perfil", $perfil);
            $stmt_check->bindValue(":id", $id_perfil);
            $stmt_check->execute();
            if ($stmt_check->rowCount() == 1) {
                return 3;
            }
            $stmt = $this->connect->prepare("UPDATE $this->table15 SET nome_perfil = :perfil WHERE id = :id");
            $stmt->bindValue(":perfil", $perfil);
            $stmt->bindValue(":id", $id_perfil);
            if ($stmt->execute()) {
                return 1;
            } else {
                return 2;
            }
        } catch (PDOException $e) {
            return 0;
        }
    }
    public function excluir_perfil(int $id_perfil): int
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table15 WHERE id = :id");
            $stmt_check->bindValue(":id", $id_perfil);
            $stmt_check->execute();
            if ($stmt_check->rowCount() == 0) {
                return 3;
            }
            $stmt = $this->connect->prepare("UPDATE $this->table5 SET id_perfil = NULL WHERE id_perfil = :id");
            $stmt->bindValue(":id", $id_perfil);
            $stmt->execute();
            
            $stmt = $this->connect->prepare("DELETE FROM $this->table15 WHERE id = :id");
            $stmt->bindValue(":id", $id_perfil);
            if ($stmt->execute()) {
                return 1;
            } else {
                return 2;
            }
        } catch (PDOException $e) {
            return 0;
        }
    }
}
