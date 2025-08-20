<?php
require_once('sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../config/connect.php');
class select extends connect
{
    private string $table1;
    private string $table2;
    private string $table3;
    private string $table4;
    private string $table5;

    function __construct()
    {
        parent::__construct();
        require(__DIR__.'/private/tables.php');
        $this->table1 = $table['crede_users'][1];
        $this->table2 = $table['crede_users'][2];
        $this->table3 = $table['crede_users'][3];
        $this->table4 = $table['crede_users'][4];
        $this->table5 = $table['crede_users'][5];
    }
    
    public function listar_usuarios_setores($setores)
    {
        try {
            $stmt = $this->connect->query("SELECT count(*) AS total FROM $this->table2 s INNER JOIN $this->table1 u ON u.id_setor = s.id WHERE u.id_setor = '$setores'");

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            
            error_log("Erro ao listar setores: " . $e->getMessage());
            return 0;
        }
    }
    public function listar_setores()
    {
        try {
            $stmt = $this->connect->query("SELECT * FROM {$this->table2} ORDER BY nome");

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            
            error_log("Erro ao listar setores: " . $e->getMessage());
            return 0;
        }
    }
    /**
     * Listar sistemas disponíveis
     */
    public function listarSistemas(): array
    {
        try {
            $stmt = $this->connect->prepare("SELECT * FROM {$this->table5} ORDER BY nome");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao listar sistemas: " . $e->getMessage());
            return [];
        }
    }
        /**
     * Listar permissões de um usuário específico
     */
    public function listarPermissoesUsuario(int $id_usuario): array
    {
        try {
            $stmt = $this->connect->prepare("
                SELECT p.*, 
                       t.tipo AS tipo_usuario, 
                       s.nome AS nome_sistema
                FROM {$this->table4} p 
                INNER JOIN {$this->table3} t ON p.id_tipos_usuarios = t.id 
                INNER JOIN {$this->table5} s ON p.id_sistemas = s.id 
                WHERE p.id_usuarios = :id_usuario
                ORDER BY s.nome
            ");
            $stmt->bindValue(":id_usuario", $id_usuario);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao listar permissões do usuário: " . $e->getMessage());
            return [];
        }
    }
        /**
     * Buscar permissão por ID
     */
    public function buscarPermissao(int $id): array
    {
        try {
            $stmt = $this->connect->prepare("
                SELECT p.*, 
                       u.nome AS nome_usuario, 
                       t.tipo AS tipo_usuario, 
                       s.nome AS nome_sistema
                FROM {$this->table4} p 
                INNER JOIN {$this->table1} u ON p.id_usuarios = u.id 
                INNER JOIN {$this->table3} t ON p.id_tipos_usuarios = t.id 
                INNER JOIN {$this->table5} s ON p.id_sistemas = s.id 
                WHERE p.id = :id
            ");
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar permissão: " . $e->getMessage());
            return [];
        }
    }
        /**
     * Listar todas as permissões com informações relacionadas
     */
    public function listarPermissoes(): array
    {
        try {
            $stmt = $this->connect->prepare("
                SELECT p.*, 
                       u.nome AS nome_usuario, 
                       t.tipo AS tipo_usuario, 
                       s.nome AS nome_sistema
                FROM {$this->table4} p 
                INNER JOIN {$this->table1} u ON p.id_usuarios = u.id 
                INNER JOIN {$this->table3} t ON p.id_tipos_usuarios = t.id 
                INNER JOIN {$this->table5} s ON p.id_sistemas = s.id 
                ORDER BY u.nome, s.nome
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao listar permissões: " . $e->getMessage());
            return [];
        }
    }
    public function buscarUsuario(int $id): array
    {
        try {
            $stmt = $this->connect->prepare("SELECT u.*, s.nome AS nome_setor FROM {$this->table1} u INNER JOIN {$this->table2} s ON u.id_setor = s.id WHERE u.id = :id");
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar usuário: " . $e->getMessage());
            return [];
        }
    }    /**
    * Listar todos os usuários com informações do setor
    */
   public function listarUsuarios(): array
   {
       try {
           $stmt = $this->connect->prepare("SELECT u.*, s.nome AS nome_setor FROM {$this->table1} u INNER JOIN {$this->table2} s ON u.id_setor = s.id ORDER BY u.nome");
           $stmt->execute();
           return $stmt->fetchAll(PDO::FETCH_ASSOC);
       } catch (Exception $e) {
           error_log("Erro ao listar usuários: " . $e->getMessage());
           return [];
       }
   }

        /**
     * Listar todos os tipos de usuários
     */
    public function listarTiposUsuarios(): array
    {
        try {
            $stmt = $this->connect->prepare("SELECT * FROM {$this->table3} ORDER BY tipo");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao listar tipos de usuários: " . $e->getMessage());
            return [];
        }
    }

      
    /**
     * Buscar tipo de usuário por ID
     */
    public function buscarTipoUsuario(int $id): array
    {
        try {
            $stmt = $this->connect->prepare("SELECT * FROM {$this->table3} WHERE id = :id");
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar tipo de usuário: " . $e->getMessage());
            return [];
        }
    }

    
    /**
     * Buscar setor por ID
     */
    public function buscarSetor(int $id): array
    {
        try {
            $stmt = $this->connect->prepare("SELECT * FROM {$this->table2} WHERE id = :id");
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar setor: " . $e->getMessage());
            return [];
        }
    }
    
    

}
