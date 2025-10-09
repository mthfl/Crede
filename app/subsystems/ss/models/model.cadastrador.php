<?php
require_once(__DIR__ . '/../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

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
        $data_nascimento,
        int $bairro,
        int $publica,
        int $pcd,
        int $id_cadastrador,
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
        float $reli_3bim_9ano,
    ): int {
        try {
            $stmt_candidato = $this->connect->prepare("INSERT INTO $this->table1 VALUES (NULL, :nome, :id_curso1, :data_nascimento, :bairro, :publica, :pcd, NULL, :id_cadastrador, 1)");
            $stmt_candidato->bindValue(":nome", $nome);
            $stmt_candidato->bindValue(":id_curso1", $id_curso1);
            $stmt_candidato->bindValue(":data_nascimento", $data_nascimento);
            $stmt_candidato->bindValue(":bairro", $bairro);
            $stmt_candidato->bindValue(":publica", $publica);
            $stmt_candidato->bindValue(":pcd", $pcd);
            $stmt_candidato->bindValue(":id_cadastrador", $id_cadastrador);
            if (!$stmt_candidato->execute()) {
                return 2;
            }
            $stmt_id = $this->connect->prepare("SELECT * FROM $this->table1 WHERE nome = :nome");
            $stmt_id->bindValue(":nome", $nome);
            $stmt_id->execute();
            $id_candidato = $stmt_id->fetch(PDO::FETCH_ASSOC);
            //6ano
            $stmt = $this->connect->prepare("INSERT INTO $this->table6 VALUES (null, :id_candidato, :lp,:artes , :ef, :li, :mate, :cien, :geo, :hist, :reli)");
            $stmt->bindValue(":id_candidato", $id_candidato['id']);
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
            $stmt = $this->connect->prepare("INSERT INTO $this->table7 VALUES (NULL, :id_candidato, :lp,:artes , :ef, :li, :mate, :cien, :geo, :hist, :reli)");
            $stmt->bindValue(":id_candidato", $id_candidato['id']);
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
            $stmt->bindValue(":id_candidato", $id_candidato['id']);
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
                $stmt->bindValue(":id_candidato", $id_candidato['id']);
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
                $stmt->bindValue(":id_candidato", $id_candidato['id']);
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
                $stmt->bindValue(":id_candidato", $id_candidato['id']);
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

                //3bim_9ano
                $stmt = $this->connect->prepare("INSERT INTO $this->table12 (id_candidato, l_portuguesa, artes, educacao_fisica, l_inglesa, matematica, ciencias, geografia, historia, religiao) VALUES (:id_candidato, :lp,:artes , :ef, :li, :mate, :cien, :geo, :hist, :reli)");
                $stmt->bindValue(":id_candidato", $id_candidato['id']);
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
                $li_9ano = ($li_1bim_9ano + $li_2bim_9ano + $li_3bim_9ano) / 3;
                $lp_9ano = ($lp_1bim_9ano + $lp_2bim_9ano + $lp_3bim_9ano) / 3;
                $artes_9ano = ($artes_1bim_9ano + $artes_2bim_9ano + $artes_3bim_9ano) / 3;
                $ef_9ano = ($ef_1bim_9ano + $ef_2bim_9ano + $ef_3bim_9ano) / 3;
                $mate_9ano = ($mate_1bim_9ano + $mate_2bim_9ano + $mate_3bim_9ano) / 3;
                $cien_9ano = ($cien_1bim_9ano + $cien_2bim_9ano + $cien_3bim_9ano) / 3;
                $geo_9ano = ($geo_1bim_9ano + $geo_2bim_9ano + $geo_3bim_9ano) / 3;
                $hist_9ano = ($hist_1bim_9ano + $hist_2bim_9ano + $hist_3bim_9ano) / 3;
                $reli_9ano = ($reli_1bim_9ano + $reli_2bim_9ano + $reli_3bim_9ano) / 3;
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
                if ($d == 0) {
                    $reli_9ano = 0;
                } else {
                    $reli_9ano = ($reli_1bim_9ano + $reli_2bim_9ano + $reli_3bim_9ano) / $d;
                }

                $d = 3;
                if ($artes_1bim_9ano == 0) {
                    $d -= 1;
                }
                if ($artes_2bim_9ano == 0) {
                    $d -= 1;
                }
                if ($artes_3bim_9ano == 0) {
                    $d -= 1;
                }
                if ($d == 0) {
                    $artes_9ano = 0;
                } else {
                    $artes_9ano = ($artes_1bim_9ano + $artes_2bim_9ano + $artes_3bim_9ano) / $d;
                }

                $d = 3;
                if ($ef_1bim_9ano == 0) {
                    $d -= 1;
                }
                if ($ef_2bim_9ano == 0) {
                    $d -= 1;
                }
                if ($ef_3bim_9ano == 0) {
                    $d -= 1;
                }
                if ($d == 0) {
                    $ef_9ano = 0;
                } else {
                    $ef_9ano = ($ef_1bim_9ano + $ef_2bim_9ano + $ef_3bim_9ano) / $d;
                }

                //9ano_media
                $stmt = $this->connect->prepare("INSERT INTO $this->table9 (id_candidato, l_portuguesa, artes, educacao_fisica, l_inglesa, matematica, ciencias, geografia, historia, religiao) VALUES (:id_candidato, :lp,:artes , :ef, :li, :mate, :cien, :geo, :hist, :reli)");
                $stmt->bindValue(":id_candidato", $id_candidato['id']);
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
            }

            $stmt_select_6ano = $this->connect->prepare("SELECT id FROM $this->table6 WHERE id_candidato = :id_candidato");
            $stmt_select_6ano->bindValue(":id_candidato", $id_candidato['id']);
            $stmt_select_6ano->execute();
            $id_notas_6ano = $stmt_select_6ano->fetch(PDO::FETCH_ASSOC);
            $stmt_select_7ano = $this->connect->prepare("SELECT id FROM $this->table7 WHERE id_candidato = :id_candidato");
            $stmt_select_7ano->bindValue(":id_candidato", $id_candidato['id']);
            $stmt_select_7ano->execute();
            $id_notas_7ano = $stmt_select_7ano->fetch(PDO::FETCH_ASSOC);
            $stmt_select_8ano = $this->connect->prepare("SELECT id FROM $this->table8 WHERE id_candidato = :id_candidato");
            $stmt_select_8ano->bindValue(":id_candidato", $id_candidato['id']);
            $stmt_select_8ano->execute();
            $id_notas_8ano = $stmt_select_8ano->fetch(PDO::FETCH_ASSOC);
            $stmt_select_9ano = $this->connect->prepare("SELECT id FROM $this->table9 WHERE id_candidato = :id_candidato");
            $stmt_select_9ano->bindValue(":id_candidato", $id_candidato['id']);
            $stmt_select_9ano->execute();
            $id_notas_9ano = $stmt_select_9ano->fetch(PDO::FETCH_ASSOC);

            $l_portuguesa_media = ($lp_6ano + $lp_7ano + $lp_8ano + $lp_9ano) / 4;
            $l_inglesa_media = ($li_6ano + $li_7ano + $li_8ano + $li_9ano) / 4;
            $matematica_media = ($mate_6ano + $mate_7ano + $mate_8ano + $mate_9ano) / 4;
            $ciencias_media = ($cien_6ano + $cien_7ano + $cien_8ano + $cien_9ano) / 4;
            $geografia_media = ($geo_6ano + $geo_7ano + $geo_8ano + $geo_9ano) / 4;
            $historia_media = ($hist_6ano + $hist_7ano + $hist_8ano + $hist_9ano) / 4;

            $d_media = 4;
            if ($ef_6ano == 0 && $ef_7ano == 0 && $ef_8ano == 0 && $ef_9ano == 0) {
                $artes_media = 0;
            } else {
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
                $artes_media = ($ef_6ano + $ef_7ano + $ef_8ano + $ef_9ano) / $d_media;
            }
            if ($ef_6ano == 0 && $ef_7ano == 0 && $ef_8ano == 0 && $ef_9ano == 0) {
                $ef_media = 0;
            } else {
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
            }

            if ($reli_6ano == 0 && $reli_7ano == 0 && $reli_8ano == 0 && $reli_9ano == 0) {
                $reli_media = 0;
            } else {
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
            }

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
            //media final
            $stmt = $this->connect->prepare("INSERT INTO $this->table4 VALUES (null, :id_candidato, :id_notas_6ano, :id_notas_7ano, :id_notas_8ano, :id_notas_9ano, :l_portuguesa_media, :artes_media, :educacao_fisica_media, :l_inglesa_media, :matematica_media, :ciencias_media, :geografia_media, :historia_media, :religiao_media, :media_final)");
            $stmt->bindValue(":id_candidato", $id_candidato['id']);
            $stmt->bindValue(":id_notas_6ano", $id_notas_6ano['id']);
            $stmt->bindValue(":id_notas_7ano", $id_notas_7ano['id']);
            $stmt->bindValue(":id_notas_8ano", $id_notas_8ano['id']);
            $stmt->bindValue(":id_notas_9ano", $id_notas_9ano['id']);
            $stmt->bindValue(":l_portuguesa_media", $l_portuguesa_media);
            $stmt->bindValue(":artes_media", $artes_media);
            $stmt->bindValue(":educacao_fisica_media", $ef_media);
            $stmt->bindValue(":l_inglesa_media", $l_inglesa_media);
            $stmt->bindValue(":matematica_media", $matematica_media);
            $stmt->bindValue(":ciencias_media", $ciencias_media);
            $stmt->bindValue(":geografia_media", $geografia_media);
            $stmt->bindValue(":historia_media", $historia_media);
            $stmt->bindValue(":religiao_media", $reli_media);
            $stmt->bindValue(":media_final", $media_final);
            if (!$stmt->execute()) {
                return 2;
            }

            $_SESSION['candidato'] = $nome;
            date_default_timezone_set('America/Fortaleza');
            $datatime = date('Y/m/d H:i:s');
            $id_usuario = $_SESSION['id'];
            $stmt_candidato = $this->connect->prepare("INSERT INTO $this->table16 VALUES (NULL, :id_usuario, :datatime, :tipo_movimentacao, :descricao)");
            $stmt_candidato->bindValue(":id_usuario", $id_usuario);
            $stmt_candidato->bindValue(":datatime", $datatime);
            $stmt_candidato->bindValue(":tipo_movimentacao", 'CADASTRAR CANDIDATO');
            $stmt_candidato->bindValue(":descricao", $nome);
            if (!$stmt_candidato->execute()) {
                return 2;
            }
            
            return 1;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function requisicao_alteracao(int $id_usuario, int $id_candidato, string $requisicao): int
    {
        try {
            $stmt_usuario = $this->connect->prepare("SELECT * FROM $this->table5 WHERE id = :id");
            $stmt_usuario->bindValue(":id", $id_usuario);
            $stmt_usuario->execute();
            $stmt_candidato = $this->connect->prepare("SELECT * FROM $this->table1 WHERE id = :id");
            $stmt_candidato->bindValue(":id", $id_candidato);
            $stmt_candidato->execute();
            if ($stmt_candidato->rowCount() == 1 && $stmt_usuario->rowCount() == 1) {
                $stmt_requisicao = $this->connect->prepare("INSERT INTO $this->table14 VALUES (NULL, :id_candidato, :id_usuario, :requisicao, :status)");
                $stmt_requisicao->bindValue(":id_usuario", $id_usuario);
                $stmt_requisicao->bindValue(":id_candidato", $id_candidato);
                $stmt_requisicao->bindValue(":requisicao", $requisicao);
                $stmt_requisicao->bindValue(":status", "Pendente");

                if ($stmt_requisicao->execute()) {
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
}
