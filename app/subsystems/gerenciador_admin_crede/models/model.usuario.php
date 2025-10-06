<?php
require_once(__DIR__ . '/model.select.php');
class usuario extends select
{
    function __construct()
    {
        parent::__construct();
    }

    public function insert_user(string $epKey, string $nome, string $email, string $cpf): bool
    {
        try {
            $map = [
                'estgdm' => [$this->connect_estgdm, $this->table1],
                'epaf'   => [$this->connect_epaf, $this->table2],
                'epmfm'  => [$this->connect_epmfm, $this->table3],
                'epav'   => [$this->connect_epav, $this->table4],
                'eedq'   => [$this->connect_eedq, $this->table5],
                'ejin'   => [$this->connect_ejin, $this->table6],
                'epfads' => [$this->connect_epfads, $this->table7],
                'emcvm'  => [$this->connect_emcvm, $this->table8],
                'eglgfm' => [$this->connect_eglgfm, $this->table9],
                'epldtv' => [$this->connect_epldtv, $this->table10],
                'ercr'   => [$this->connect_ercr, $this->table11],
            ];

            if (!isset($map[$epKey])) {
                throw new InvalidArgumentException('Escola inválida');
            }

            [$pdo, $table] = $map[$epKey];
            $stmt_check = $pdo->prepare("SELECT * FROM  $table WHERE cpf = :cpf AND email = :email");
            $stmt_check->bindValue(':email', $email);
            $stmt_check->bindValue(':cpf', $cpf);
            $stmt_check->execute();

            if ($stmt_check->rowCount() == 0) {

                $stmt = $pdo->prepare("INSERT INTO $table (nome_user, email, cpf, tipo_usuario) VALUES (:nome, :email, :cpf, 'admin')");
                $stmt->bindValue(':nome', $nome);
                $stmt->bindValue(':email', $email);
                $stmt->bindValue(':cpf', $cpf);

                if ($stmt->execute()) {

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

    public function update_user(string $epKey, int $id, string $nome, string $email, string $cpf): bool
    {
        $map = [
            'estgdm' => [$this->connect_estgdm, $this->table1],
            'epaf'   => [$this->connect_epaf, $this->table2],
            'epmfm'  => [$this->connect_epmfm, $this->table3],
            'epav'   => [$this->connect_epav, $this->table4],
            'eedq'   => [$this->connect_eedq, $this->table5],
            'ejin'   => [$this->connect_ejin, $this->table6],
            'epfads' => [$this->connect_epfads, $this->table7],
            'emcvm'  => [$this->connect_emcvm, $this->table8],
            'eglgfm' => [$this->connect_eglgfm, $this->table9],
            'epldtv' => [$this->connect_epldtv, $this->table10],
            'ercr'   => [$this->connect_ercr, $this->table11],
        ];
        if (!isset($map[$epKey])) {
            throw new InvalidArgumentException('Escola inválida');
        }
        [$pdo, $table] = $map[$epKey];
        $stmt = $pdo->prepare("UPDATE $table SET nome_user = :nome, email = :email, cpf = :cpf WHERE id = :id");
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':cpf', $cpf);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete_user(string $epKey, int $id): bool
    {
        $map = [
            'estgdm' => [$this->connect_estgdm, $this->table1],
            'epaf'   => [$this->connect_epaf, $this->table2],
            'epmfm'  => [$this->connect_epmfm, $this->table3],
            'epav'   => [$this->connect_epav, $this->table4],
            'eedq'   => [$this->connect_eedq, $this->table5],
            'ejin'   => [$this->connect_ejin, $this->table6],
            'epfads' => [$this->connect_epfads, $this->table7],
            'emcvm'  => [$this->connect_emcvm, $this->table8],
            'eglgfm' => [$this->connect_eglgfm, $this->table9],
            'epldtv' => [$this->connect_epldtv, $this->table10],
            'ercr'   => [$this->connect_ercr, $this->table11],
        ];
        if (!isset($map[$epKey])) {
            throw new InvalidArgumentException('Escola inválida');
        }
        [$pdo, $table] = $map[$epKey];
        // Em vez de excluir, apenas desativa o usuário (status = 0)
        $stmt = $pdo->prepare("UPDATE $table SET status = 0 WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Função para reativar um usuário
    public function activate_user(string $epKey, int $id): bool
    {
        $map = [
            'estgdm' => [$this->connect_estgdm, $this->table1],
            'epaf'   => [$this->connect_epaf, $this->table2],
            'epmfm'  => [$this->connect_epmfm, $this->table3],
            'epav'   => [$this->connect_epav, $this->table4],
            'eedq'   => [$this->connect_eedq, $this->table5],
            'ejin'   => [$this->connect_ejin, $this->table6],
            'epfads' => [$this->connect_epfads, $this->table7],
            'emcvm'  => [$this->connect_emcvm, $this->table8],
            'eglgfm' => [$this->connect_eglgfm, $this->table9],
            'epldtv' => [$this->connect_epldtv, $this->table10],
            'ercr'   => [$this->connect_ercr, $this->table11],
        ];
        if (!isset($map[$epKey])) {
            throw new InvalidArgumentException('Escola inválida');
        }
        [$pdo, $table] = $map[$epKey];
        $stmt = $pdo->prepare("UPDATE $table SET status = 1 WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
