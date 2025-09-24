<?php
require_once('sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/model.usuario.php');
class admin extends usuario
{
    function __construct()
    {
        parent::__construct();
    }

    public function editar_produto($id_produto, $barcode, $nome, $quantidade, $id_categoria, $validade): int
    {
        $consulta = "UPDATE $this->table4 SET barcode = :barcode, nome_produto = :nome, quantidade = :quantidade, id_categoria = :id_categoria, vencimento = :validade WHERE id = :id";
        $query = $this->connect->prepare($consulta);
        $query->bindValue(":id", $id_produto);
        $query->bindValue(":nome", $nome);
        $query->bindValue(":barcode", $barcode);
        $query->bindValue(":quantidade", $quantidade);
        $query->bindValue(":id_categoria", $id_categoria);
        $query->bindValue(":validade", $validade);

        if ($query->execute()) {
            return 1;
        } else {
            return 2;
        }
    }
    public function excluir_produto($id): int
    {
        try {
            $consultaDeleteMovimentacoes = "DELETE FROM movimentacao WHERE fk_produtos_id = :id";
            $queryDeleteMovimentacoes = $this->connect->prepare($consultaDeleteMovimentacoes);
            $queryDeleteMovimentacoes->bindValue(":id", $id);


            if ($queryDeleteMovimentacoes->execute()) {
                $consulta = "DELETE FROM perdas_produtos WHERE id_produto = :id";
                $query = $this->connect->prepare($consulta);
                $query->bindValue(":id", $id);


                if ($query->execute()) {
                    $consulta = "DELETE FROM produtos WHERE id = :id";
                    $query = $this->connect->prepare($consulta);
                    $query->bindValue(":id", $id);


                    if ($query->execute()) {

                        return 1;
                    } else {
                        return 2;
                    }
                } else {
                    return 2;
                }
            } else {
                return 2;
            }
        } catch (PDOException $e) {
            error_log("Erro ao apagar produto: " . $e->getMessage());

            return 0;
        }
    }
}
