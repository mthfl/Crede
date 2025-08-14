<?php
require_once(__DIR__ . '/../config/connection.php');

class MainModel extends connection
{
    public function __construct()
    {
        parent::__construct();
    }

    public function registrar_perda(
        $id_produto,
        $quantidade,
        $tipo_perda,
        $data_perda
    ) {

        $stmt_check = $this->pdo->prepare("SELECT * FROM produtos WHERE id = :id");
        $stmt_check->bindParam(':id', $id_produto);
        $stmt_check->execute();
        
        if ($stmt_check->rowCount() > 0) {

            $dados = $stmt_check->fetch(PDO::FETCH_ASSOC);
            $nova_quantidade = $dados['quantidade'] - $quantidade;
            $stmt_registrar = $this->pdo->prepare("UPDATE `produtos` SET quantidade = :quantidade WHERE id = :id");
            $stmt_registrar->bindParam(':id', $id_produto);
            $stmt_registrar->bindParam(':quantidade', $nova_quantidade);
            $stmt_registrar->execute();

            $stmt_registrar = $this->pdo->prepare("INSERT INTO perdas_produtos VALUES(null, :id_produto, :quantidade, :tipo, :data_perda)");
            $stmt_registrar->bindParam(':id_produto', $id_produto);
            $stmt_registrar->bindParam(':quantidade', $quantidade);
            $stmt_registrar->bindParam(':tipo', $tipo_perda);
            $stmt_registrar->bindParam(':data_perda', $data_perda);
            $stmt_registrar->execute();

            if($stmt_registrar){

                return 1;
            }else{

                return 2;
            }
            
        }else{

            return 3;
        }
    }

    public function verificar_produto($barcode){

        $stmt_check = $this->pdo->prepare("SELECT * FROM produtos WHERE barcode = :barcode");
        $stmt_check->bindParam(':barcode', $barcode);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {

            return true;
        }else{

            return false;
        }
    }
};
