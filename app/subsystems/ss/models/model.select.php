<?php
require_once(__DIR__ . '/../config/connect.php');
class select extends connect
{
    protected string $table1;
    protected string $table2;
    protected string $table3;
    protected string $table4;
    protected string $table5;
    protected string $table6;
    protected string $table7;
    protected string $table8;
    protected string $table9;
    protected string $table10;
    protected string $table11;
    protected string $table12;
    protected string $table13;
    protected string $table14;
    protected string $table15;

    function __construct($escola)
    {
        parent::__construct($escola);
        $table = require(__DIR__ . '/../../../.env/tables.php');
        $this->table1 = $table["ss_$escola"][1];
        $this->table2 = $table["ss_$escola"][2];
        $this->table3 = $table["ss_$escola"][3];
        $this->table4 = $table["ss_$escola"][4];
        $this->table5 = $table["ss_$escola"][5];
        $this->table6 = $table["ss_$escola"][6];
        $this->table7 = $table["ss_$escola"][7];
        $this->table8 = $table["ss_$escola"][8];
        $this->table9 = $table["ss_$escola"][9];
        $this->table10 = $table["ss_$escola"][10];
        $this->table11 = $table["ss_$escola"][11];
        $this->table12 = $table["ss_$escola"][12];
        $this->table13 = $table["ss_$escola"][13];
        $this->table14 = $table["ss_$escola"][14];
        $this->table15 = $table["ss_$escola"][15];
    }

    public function select_perfis_usuarios($id_perfil): array
    {
        $stmt = $this->connect->query("SELECT * FROM $this->table15 WHERE id = $id_perfil");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function select_perfis(): array
    {
        $stmt = $this->connect->query("SELECT * FROM $this->table15");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function select_requisicoes_pendentes(){
        $stmt = $this->connect->query("SELECT *, r.id as id_requisicao FROM $this->table14 r INNER JOIN $this->table5 u ON r.id_usuario = u.id INNER JOIN $this->table1 c ON r.id_candidato = c.id WHERE r.status = 'Pendente'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function select_requisicoes_realizadas(){
        $stmt = $this->connect->query("SELECT *, r.id as id_requisicao FROM $this->table14 r INNER JOIN $this->table5 u ON r.id_usuario = u.id INNER JOIN $this->table1 c ON r.id_candidato = c.id WHERE r.status = 'Concluido'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function select_requisicoes_rejeitadas(){
        $stmt = $this->connect->query("SELECT *, r.id as id_requisicao FROM $this->table14 r INNER JOIN $this->table5 u ON r.id_usuario = u.id INNER JOIN $this->table1 c ON r.id_candidato = c.id WHERE r.status = 'Recusado'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function select_requisicoes_usuario(int $id_usuario): array
    {
        try {
            $sql = "SELECT r.id AS id_requisicao,
                           r.id_candidato,
                           r.id_usuario,
                           r.texto,
                           r.status AS status_requisicao,
                           u.nome_user,
                           u.email,
                           u.tipo_usuario,
                           c.nome AS nome,
                           c.id_curso1
                    FROM $this->table14 r
                    INNER JOIN $this->table5 u ON r.id_usuario = u.id
                    INNER JOIN $this->table1 c ON r.id_candidato = c.id
                    WHERE r.id_usuario = :id_usuario
                    ORDER BY r.id DESC";
            $stmt = $this->connect->prepare($sql);
            $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    public function select_cursos(): array
    {

        $stmt_cursos = $this->connect->query("SELECT * FROM $this->table2");

        return $cursos = $stmt_cursos->fetchAll(PDO::FETCH_ASSOC);
    }
    public function select_usuarios(): array
    {

        $stmt_cursos = $this->connect->query("SELECT * FROM $this->table5");

        return $cursos = $stmt_cursos->fetchAll(PDO::FETCH_ASSOC);
    }

    public function select_tipos_usuarios(): array
    {
        try {
            $stmt = $this->connect->query("SHOW COLUMNS FROM $this->table5 LIKE 'tipo_usuario'");
            $col = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$col || empty($col['Type'])) {
                return [];
            }
            $type = $col['Type']; // ex: enum('admin','cadastrador')
            if (preg_match("/enum\\((.*)\\)/i", $type, $matches)) {
                $vals = $matches[1];
                $vals = str_getcsv($vals, ',', "'\"");
                // limpar espaços e chaves vazias
                $clean = [];
                foreach ($vals as $v) {
                    $v = trim($v);
                    if ($v !== '') {
                        $clean[] = $v;
                    }
                }
                return $clean;
            }
            return [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function select_bairros(): array
    {
        try {
            $stmt = $this->connect->query("SELECT * FROM $this->table13");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function select_candidatos(): array
    {
        try {
            $stmt = $this->connect->query("SELECT can.*, cur.nome_curso AS nome_curso, user.nome_user AS nome_user  FROM $this->table1 can INNER JOIN $this->table2 cur ON cur.id = can.id_curso1 INNER JOIN $this->table5 user ON user.id = can.id_cadastrador");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function select_candidato_notas(int $id_candidato)
    {
        try {
            $sql = "SELECT 
                can.id,
                can.nome,
                can.data_nascimento,
                can.publica,
                can.pcd,
                can.bairro,
                can.data,
                cur.nome_curso,
                u.nome_user AS nome_cadastrador,
                n6.l_portuguesa AS n6_portuguesa,
                n6.artes AS n6_artes,
                n6.educacao_fisica AS n6_educacao_fisica,
                n6.l_inglesa AS n6_inglesa,
                n6.matematica AS n6_matematica,
                n6.ciencias AS n6_ciencias,
                n6.geografia AS n6_geografia,
                n6.historia AS n6_historia,
                n6.religiao AS n6_religiao,
                n7.l_portuguesa AS n7_portuguesa,
                n7.artes AS n7_artes,
                n7.educacao_fisica AS n7_educacao_fisica,
                n7.l_inglesa AS n7_inglesa,
                n7.matematica AS n7_matematica,
                n7.ciencias AS n7_ciencias,
                n7.geografia AS n7_geografia,
                n7.historia AS n7_historia,
                n7.religiao AS n7_religiao,
                n8.l_portuguesa AS n8_portuguesa,
                n8.artes AS n8_artes,
                n8.educacao_fisica AS n8_educacao_fisica,
                n8.l_inglesa AS n8_inglesa,
                n8.matematica AS n8_matematica,
                n8.ciencias AS n8_ciencias,
                n8.geografia AS n8_geografia,
                n8.historia AS n8_historia,
                n8.religiao AS n8_religiao,
                n9.l_portuguesa AS n9_portuguesa,
                n9.artes AS n9_artes,
                n9.educacao_fisica AS n9_educacao_fisica,
                n9.l_inglesa AS n9_inglesa,
                n9.matematica AS n9_matematica,
                n9.ciencias AS n9_ciencias,
                n9.geografia AS n9_geografia,
                n9.historia AS n9_historia,
                n9.religiao AS n9_religiao,
                n1b.l_portuguesa AS n1b_portuguesa,
                n1b.artes AS n1b_artes,
                n1b.educacao_fisica AS n1b_educacao_fisica,
                n1b.l_inglesa AS n1b_inglesa,
                n1b.matematica AS n1b_matematica,
                n1b.ciencias AS n1b_ciencias,
                n1b.geografia AS n1b_geografia,
                n1b.historia AS n1b_historia,
                n1b.religiao AS n1b_religiao,
                n2b.l_portuguesa AS n2b_portuguesa,
                n2b.artes AS n2b_artes,
                n2b.educacao_fisica AS n2b_educacao_fisica,
                n2b.l_inglesa AS n2b_inglesa,
                n2b.matematica AS n2b_matematica,
                n2b.ciencias AS n2b_ciencias,
                n2b.geografia AS n2b_geografia,
                n2b.historia AS n2b_historia,
                n2b.religiao AS n2b_religiao,
                n3b.l_portuguesa AS n3b_portuguesa,
                n3b.artes AS n3b_artes,
                n3b.educacao_fisica AS n3b_educacao_fisica,
                n3b.l_inglesa AS n3b_inglesa,
                n3b.matematica AS n3b_matematica,
                n3b.ciencias AS n3b_ciencias,
                n3b.geografia AS n3b_geografia,
                n3b.historia AS n3b_historia,
                n3b.religiao AS n3b_religiao,
                med.l_portuguesa_media AS med_portuguesa,
                med.artes_media AS med_artes,
                med.educacao_fisica_media AS med_educacao_fisica,
                med.l_inglesa_media AS med_inglesa,
                med.matematica_media AS med_matematica,
                med.ciencias_media AS med_ciencias,
                med.geografia_media AS med_geografia,
                med.historia_media AS med_historia,
                med.religiao_media AS med_religiao,
                med.media_final AS media_final
            FROM $this->table1 can
            INNER JOIN $this->table2 cur ON cur.id = can.id_curso1
            INNER JOIN $this->table5 u ON u.id = can.id_cadastrador
            INNER JOIN $this->table6 n6 ON can.id = n6.id_candidato
            INNER JOIN $this->table7 n7 ON can.id = n7.id_candidato
            INNER JOIN $this->table8 n8 ON can.id = n8.id_candidato
            INNER JOIN $this->table9 n9 ON can.id = n9.id_candidato
            LEFT JOIN $this->table10 n1b ON can.id = n1b.id_candidato
            LEFT JOIN $this->table11 n2b ON can.id = n2b.id_candidato
            LEFT JOIN $this->table12 n3b ON can.id = n3b.id_candidato
            LEFT JOIN $this->table4 med ON med.id_candidato = can.id
            WHERE can.id = :id";
            $stmt = $this->connect->prepare($sql);
            $stmt->bindValue(':id', $id_candidato, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
