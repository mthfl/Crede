<?php
require_once(__DIR__ . '/model.select.php');

class cadastrador extends select
{
    function __construct($escola)
    {
        parent::__construct($escola);
    }
    public function cadastrar_candidato(
        string $nome,
        int $id_curso1,
        int $id_curso2,
        string $data_nascimento,
        bool $bairro,
        bool $publica,
        bool $pcd,
        int $id_cadastrador,
        int $lp_6ano,
        int $artes_6ano,
        int $ef_6ano,
        int $li_6ano,
        int $mate_6ano,
        int $cien_6ano,
        int $geo_6ano,
        int $hist_6ano,
        int $reli_6ano,
        int $lp_7ano,
        int $artes_7ano,
        int $ef_7ano,
        int $li_7ano,
        int $mate_7ano,
        int $cien_7ano,
        int $geo_7ano,
        int $hist_7ano,
        int $reli_7ano,
        int $lp_8ano,
        int $artes_8ano,
        int $ef_8ano,
        int $li_8ano,
        int $mate_8ano,
        int $cien_8ano,
        int $geo_8ano,
        int $hist_8ano,
        int $reli_8ano,
        int $lp_9ano,
        int $artes_9ano,
        int $ef_9ano,
        int $li_9ano,
        int $mate_9ano,
        int $cien_9ano,
        int $geo_9ano,
        int $hist_9ano,
        int $reli_9ano,
        int $lp_1bim_9ano,
        int $artes_1bim_9ano,
        int $ef_1bim_9ano,
        int $li_1bim_9ano,
        int $mate_1bim_9ano,
        int $cien_1bim_9ano,
        int $geo_1bim_9ano,
        int $hist_1bim_9ano,
        int $reli_1bim_9ano,
        int $lp_2bim_9ano,
        int $artes_2bim_9ano,
        int $ef_2bim_9ano,
        int $li_2bim_9ano,
        int $mate_2bim_9ano,
        int $cien_2bim_9ano,
        int $geo_2bim_9ano,
        int $hist_2bim_9ano,
        int $reli_2bim_9ano,
        int $lp_3bim_9ano,
        int $artes_3bim_9ano,
        int $ef_3bim_9ano,
        int $li_3bim_9ano,
        int $mate_3bim_9ano,
        int $cien_3bim_9ano,
        int $geo_3bim_9ano,
        int $hist_3bim_9ano,
        int $reli_3bim_9ano,
    ): int {
        try {
            $stmt_candidato = $this->connect->prepare("INSERT INTO $this->table1 (nome, id_curso1, id_curso2, data_nascimento, bairro, publica, pcd, id_cadastrador) VALUES (:nome, :id_curso1, :id_curso2, :data_nascimento, :bairro, :publica, :pcd, :id_cadastrador)");
            $stmt_candidato->bindValue(":nome", $nome);
            $stmt_candidato->bindValue(":id_curso1", $id_curso1);
            $stmt_candidato->bindValue(":id_curso2", $id_curso2);
            $stmt_candidato->bindValue(":data_nascimento", $data_nascimento);
            $stmt_candidato->bindValue(":bairro", $bairro);
            $stmt_candidato->bindValue(":publica", $publica);
            $stmt_candidato->bindValue(":pcd", $pcd);
            $stmt_candidato->bindValue(":id_cadastrador", $id_cadastrador);
            if (!$stmt_candidato->execute()) {
                return 2;
            }
            $stmt_id = $this->connect->prepare("SELECT id FROM $this->table1 WHERE nome = :nome");
            $stmt_id->bindValue(":nome", $nome);
            $stmt_id->execute();
            $id_candidato = $stmt_id->fetch(PDO::FETCH_ASSOC);

            //6ano
            $stmt = $this->connect->prepare("INSERT INTO $this->table6 (id_candidato, l_portuguesa, artes, educacao_fisica, l_inglesa, matematica, ciencias, geografia, historia, religiao) VALUES (:id_candidato, :lp,:artes , :ef, :li, :mate, :cien, :geo, :hist, :reli)");
            $stmt->bindValue(":id_candidato", $id_candidato);
            $stmt->bindValue(":lp", $lp_6ano);
            $stmt->bindValue(":artes", $artes_6ano);
            $stmt->bindValue(":ef", $ef_6ano);
            $stmt->bindValue(":li", $li_6ano);
            $stmt->bindValue(":mate", $mate_6ano);
            $stmt->bindValue(":cien", $cien_6ano);
            $stmt->bindValue(":geo", $geo_6ano);
            $stmt->bindValue(":hist", $hist_6ano);
            $stmt->bindValue(":reli", $reli_6ano);
            if (!$stmt->execute()) {
                return 2;
            }

            //7ano
            $stmt = $this->connect->prepare("INSERT INTO $this->table7 (id_candidato, l_portuguesa, artes, educacao_fisica, l_inglesa, matematica, ciencias, geografia, historia, religiao) VALUES (:id_candidato, :lp,:artes , :ef, :li, :mate, :cien, :geo, :hist, :reli)");
            $stmt->bindValue(":id_candidato", $id_candidato);
            $stmt->bindValue(":lp", $lp_7ano);
            $stmt->bindValue(":artes", $artes_7ano);
            $stmt->bindValue(":ef", $ef_7ano);
            $stmt->bindValue(":li", $li_7ano);
            $stmt->bindValue(":mate", $mate_7ano);
            $stmt->bindValue(":cien", $cien_7ano);
            $stmt->bindValue(":geo", $geo_7ano);
            $stmt->bindValue(":hist", $hist_7ano);
            $stmt->bindValue(":reli", $reli_7ano);
            if (!$stmt->execute()) {
                return 2;
            }

            //8ano
            $stmt = $this->connect->prepare("INSERT INTO $this->table8 (id_candidato, l_portuguesa, artes, educacao_fisica, l_inglesa, matematica, ciencias, geografia, historia, religiao) VALUES (:id_candidato, :lp,:artes , :ef, :li, :mate, :cien, :geo, :hist, :reli)");
            $stmt->bindValue(":id_candidato", $id_candidato);
            $stmt->bindValue(":lp", $lp_8ano);
            $stmt->bindValue(":artes", $artes_8ano);
            $stmt->bindValue(":ef", $ef_8ano);
            $stmt->bindValue(":li", $li_8ano);
            $stmt->bindValue(":mate", $mate_8ano);
            $stmt->bindValue(":cien", $cien_8ano);
            $stmt->bindValue(":geo", $geo_8ano);
            $stmt->bindValue(":hist", $hist_8ano);
            $stmt->bindValue(":reli", $reli_8ano);
            if (!$stmt->execute()) {
                return 2;
            }

            if ($li_1bim_9ano == 0) {

                //9ano
                $stmt = $this->connect->prepare("INSERT INTO $this->table9 (id_candidato, l_portuguesa, artes, educacao_fisica, l_inglesa, matematica, ciencias, geografia, historia, religiao) VALUES (:id_candidato, :lp,:artes , :ef, :li, :mate, :cien, :geo, :hist, :reli)");
                $stmt->bindValue(":id_candidato", $id_candidato);
                $stmt->bindValue(":lp", $lp_9ano);
                $stmt->bindValue(":artes", $artes_9ano);
                $stmt->bindValue(":ef", $ef_9ano);
                $stmt->bindValue(":li", $li_9ano);
                $stmt->bindValue(":mate", $mate_9ano);
                $stmt->bindValue(":cien", $cien_9ano);
                $stmt->bindValue(":geo", $geo_9ano);
                $stmt->bindValue(":hist", $hist_9ano);
                $stmt->bindValue(":reli", $reli_9ano);
                if (!$stmt->execute()) {
                    return 2;
                }
            } else {
                //1bim_9ano
                $stmt = $this->connect->prepare("INSERT INTO $this->table10 (id_candidato, l_portuguesa, artes, educacao_fisica, l_inglesa, matematica, ciencias, geografia, historia, religiao) VALUES (:id_candidato, :lp,:artes , :ef, :li, :mate, :cien, :geo, :hist, :reli)");
                $stmt->bindValue(":id_candidato", $id_candidato);
                $stmt->bindValue(":lp", $lp_1bim_9ano);
                $stmt->bindValue(":artes", $artes_1bim_9ano);
                $stmt->bindValue(":ef", $ef_1bim_9ano);
                $stmt->bindValue(":li", $li_1bim_9ano);
                $stmt->bindValue(":mate", $mate_1bim_9ano);
                $stmt->bindValue(":cien", $cien_1bim_9ano);
                $stmt->bindValue(":geo", $geo_1bim_9ano);
                $stmt->bindValue(":hist", $hist_1bim_9ano);
                $stmt->bindValue(":reli", $reli_1bim_9ano);
                if (!$stmt->execute()) {
                    return 2;
                }

                //2bim_9ano
                $stmt = $this->connect->prepare("INSERT INTO $this->table11 (id_candidato, l_portuguesa, artes, educacao_fisica, l_inglesa, matematica, ciencias, geografia, historia, religiao) VALUES (:id_candidato, :lp,:artes , :ef, :li, :mate, :cien, :geo, :hist, :reli)");
                $stmt->bindValue(":id_candidato", $id_candidato);
                $stmt->bindValue(":lp", $lp_2bim_9ano);
                $stmt->bindValue(":artes", $artes_2bim_9ano);
                $stmt->bindValue(":ef", $ef_2bim_9ano);
                $stmt->bindValue(":li", $li_2bim_9ano);
                $stmt->bindValue(":mate", $mate_2bim_9ano);
                $stmt->bindValue(":cien", $cien_2bim_9ano);
                $stmt->bindValue(":geo", $geo_2bim_9ano);
                $stmt->bindValue(":hist", $hist_2bim_9ano);
                $stmt->bindValue(":reli", $reli_2bim_9ano);
                if (!$stmt->execute()) {
                    return 2;
                }

                $li_9ano_media = ($li_1bim_9ano + $li_2bim_9ano + $li_3bim_9ano) / 3;
                $lp_9ano_media = ($lp_1bim_9ano + $lp_2bim_9ano + $lp_3bim_9ano) / 3;
                $l_inglesa_9ano_media = ($li_1bim_9ano + $li_2bim_9ano + $li_3bim_9ano) / 3;
                $matematica_9ano_media = ($mate_1bim_9ano + $mate_2bim_9ano + $mate_3bim_9ano) / 3;
                $ciencias_9ano_media = ($cien_1bim_9ano + $cien_2bim_9ano + $cien_3bim_9ano) / 3;
                $geografia_9ano_media = ($geo_1bim_9ano + $geo_2bim_9ano + $geo_3bim_9ano) / 3;
                $historia_9ano_media = ($hist_1bim_9ano + $hist_2bim_9ano + $hist_3bim_9ano) / 3;
                if($reli_1bim_9ano == 0){

                
                //3bim_9ano
                $stmt = $this->connect->prepare("INSERT INTO $this->table12 (id_candidato, l_portuguesa, artes, educacao_fisica, l_inglesa, matematica, ciencias, geografia, historia, religiao) VALUES (:id_candidato, :lp,:artes , :ef, :li, :mate, :cien, :geo, :hist, :reli)");
                $stmt->bindValue(":id_candidato", $id_candidato);
                $stmt->bindValue(":lp", $lp_3bim_9ano);
                $stmt->bindValue(":artes", $artes_3bim_9ano);
                $stmt->bindValue(":ef", $ef_3bim_9ano);
                $stmt->bindValue(":li", $li_3bim_9ano);
                $stmt->bindValue(":mate", $mate_3bim_9ano);
                $stmt->bindValue(":cien", $cien_3bim_9ano);
                $stmt->bindValue(":geo", $geo_3bim_9ano);
                $stmt->bindValue(":hist", $hist_3bim_9ano);
                $stmt->bindValue(":reli", $reli_3bim_9ano);
                if (!$stmt->execute()) {
                    return 2;
                }

                
                $stmt_select_6ano = $this->connect->prepare("SELECT id FROM $this->table6 WHERE id_candidato = :id_candidato");
                $stmt_select_6ano->bindValue(":id_candidato", $id_candidato);
                $stmt_select_6ano->execute();
                $id_notas_6ano = $stmt_select_6ano->fetch(PDO::FETCH_ASSOC);
                $stmt_select_7ano = $this->connect->prepare("SELECT id FROM $this->table7 WHERE id_candidato = :id_candidato");
                $stmt_select_7ano->bindValue(":id_candidato", $id_candidato);
                $stmt_select_7ano->execute();
                $id_notas_7ano = $stmt_select_7ano->fetch(PDO::FETCH_ASSOC);
                $stmt_select_8ano = $this->connect->prepare("SELECT id FROM $this->table8 WHERE id_candidato = :id_candidato");
                $stmt_select_8ano->bindValue(":id_candidato", $id_candidato);
                $stmt_select_8ano->execute();
                $id_notas_8ano = $stmt_select_8ano->fetch(PDO::FETCH_ASSOC);
                $stmt_select_9ano = $this->connect->prepare("SELECT id FROM $this->table9 WHERE id_candidato = :id_candidato");
                $stmt_select_9ano->bindValue(":id_candidato", $id_candidato);
                $stmt_select_9ano->execute();
                $id_notas_9ano = $stmt_select_9ano->fetch(PDO::FETCH_ASSOC);

                //6ano
                $stmt = $this->connect->prepare("INSERT INTO $this->table13 (`id_candidato`, `id_notas_6ano`, `id_notas_7ano`, `id_notas_8ano`, `id_notas_9ano`, `l_portuguesa_media`, `artes_media`, `educacao_fisica_media`, `l_inglesa_media`, `matematica_media`, `ciencias_media`, `geografia_media`, `historia_media`, `religiao_media`, `media_final`) 
            VALUES (:id_candidato, :id_notas_6ano, :id_notas_7ano, :id_notas_8ano, :id_notas_9ano, :l_portuguesa_media, :artes_media, :educacao_fisica_media, :l_inglesa_media, :matematica_media, :ciencias_media, :geografia_media, :historia_media, :religiao_media, :media_final)");
                $stmt->bindValue(":id_candidato", $id_candidato);
                $stmt->bindValue(":id_notas_6ano", $id_notas_6ano);
                $stmt->bindValue(":id_notas_7ano", $id_notas_7ano);
                $stmt->bindValue(":id_notas_8ano", $id_notas_8ano);
                $stmt->bindValue(":id_notas_9ano", $id_notas_9ano);
                $stmt->bindValue(":l_portuguesa_media", $l_portuguesa_media);
                $stmt->bindValue(":artes_media", $artes_media);
                $stmt->bindValue(":educacao_fisica_media", $educacao_fisica_media);
                $stmt->bindValue(":l_inglesa_media", $l_inglesa_media);
                $stmt->bindValue(":matematica_media", $matematica_media);
                $stmt->bindValue(":ciencias_media", $ciencias_media);
                $stmt->bindValue(":geografia_media", $geografia_media);
                $stmt->bindValue(":historia_media", $historia_media);
                $stmt->bindValue(":religiao_media", $religiao_media);
                $stmt->bindValue(":media_final", $media_final);
                $stmt->bindValue(":artes", $artes_6ano);
                $stmt->bindValue(":ef", $ef_6ano);
                $stmt->bindValue(":li", $li_6ano);
                $stmt->bindValue(":mate", $mate_6ano);
                $stmt->bindValue(":cien", $cien_6ano);
                $stmt->bindValue(":geo", $geo_6ano);
                $stmt->bindValue(":hist", $hist_6ano);
                $stmt->bindValue(":reli", $reli_6ano);
                if (!$stmt->execute()) {
                    return 2;
                }
            }
            return 1;
        } catch (PDOException $e) {
            return 0;
        }
    }
}
