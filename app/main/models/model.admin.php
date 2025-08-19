<?php
require_once('model.usuario.php');
class model_admin extends usuario
{
    private string $table1;
    private string $table2;
    private string $table3;
    private string $table4;
    private string $table5;

    function __construct()
    {
        parent::__construct();
        $table = require(__DIR__ . '/private/tables.php');
        $this->table1 = $table['crede_users'][1]; // usuarios
        $this->table2 = $table['crede_users'][2]; // setores
        $this->table3 = $table['crede_users'][3]; // tipos_usuarios
        $this->table4 = $table['crede_users'][4]; // permissoes
        $this->table5 = $table['crede_users'][5]; // sistemas
    }

    // ==================== FUNÇÕES PARA SETORES ====================
    
    /**
     * Criar novo setor
     */
    public function criarSetor(string $nome): array
    {
        try {
            // Verificar se o setor já existe
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table2} WHERE nome = :nome");
            $stmt_check->bindValue(":nome", trim($nome));
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                return ['success' => false, 'message' => 'Setor já existe'];
            }
            
            $stmt = $this->connect->prepare("INSERT INTO {$this->table2} (nome) VALUES (:nome)");
            $stmt->bindValue(":nome", trim($nome));
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Setor criado com sucesso', 'id' => $this->connect->lastInsertId()];
            } else {
                return ['success' => false, 'message' => 'Erro ao criar setor'];
            }
        } catch (Exception $e) {
            error_log("Erro ao criar setor: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro interno do sistema'];
        }
    }
    
    /**
     * Editar setor existente
     */
    public function editarSetor(int $id, string $nome): array
    {
        try {
            // Verificar se o setor existe
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table2} WHERE id = :id");
            $stmt_check->bindValue(":id", $id);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() == 0) {
                return ['success' => false, 'message' => 'Setor não encontrado'];
            }
            
            // Verificar se o novo nome já existe em outro setor
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table2} WHERE nome = :nome AND id != :id");
            $stmt_check->bindValue(":nome", trim($nome));
            $stmt_check->bindValue(":id", $id);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                return ['success' => false, 'message' => 'Nome de setor já existe'];
            }
            
            $stmt = $this->connect->prepare("UPDATE {$this->table2} SET nome = :nome WHERE id = :id");
            $stmt->bindValue(":nome", trim($nome));
            $stmt->bindValue(":id", $id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Setor atualizado com sucesso'];
            } else {
                return ['success' => false, 'message' => 'Erro ao atualizar setor'];
            }
        } catch (Exception $e) {
            error_log("Erro ao editar setor: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro interno do sistema'];
        }
    }
    
    /**
     * Excluir setor
     */
    public function excluirSetor(int $id): array
    {
        try {
            // Verificar se há usuários usando este setor
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table1} WHERE id_setor = :id");
            $stmt_check->bindValue(":id", $id);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                return ['success' => false, 'message' => 'Não é possível excluir o setor. Existem usuários vinculados a ele.'];
            }
            
            $stmt = $this->connect->prepare("DELETE FROM {$this->table2} WHERE id = :id");
            $stmt->bindValue(":id", $id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Setor excluído com sucesso'];
            } else {
                return ['success' => false, 'message' => 'Erro ao excluir setor'];
            }
        } catch (Exception $e) {
            error_log("Erro ao excluir setor: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro interno do sistema'];
        }
    }
    
    /**
     * Listar todos os setores
     */
    public function listarSetores(): array
    {
        try {
            $stmt = $this->connect->prepare("SELECT * FROM {$this->table2} ORDER BY nome");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao listar setores: " . $e->getMessage());
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

    // ==================== FUNÇÕES PARA TIPOS DE USUÁRIOS ====================
    
    /**
     * Criar novo tipo de usuário
     */
    public function criarTipoUsuario(string $tipo): array
    {
        try {
            // Verificar se o tipo já existe
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table3} WHERE tipo = :tipo");
            $stmt_check->bindValue(":tipo", trim($tipo));
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                return ['success' => false, 'message' => 'Tipo de usuário já existe'];
            }
            
            $stmt = $this->connect->prepare("INSERT INTO {$this->table3} (tipo) VALUES (:tipo)");
            $stmt->bindValue(":tipo", trim($tipo));
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Tipo de usuário criado com sucesso', 'id' => $this->connect->lastInsertId()];
            } else {
                return ['success' => false, 'message' => 'Erro ao criar tipo de usuário'];
            }
        } catch (Exception $e) {
            error_log("Erro ao criar tipo de usuário: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro interno do sistema'];
        }
    }
    
    /**
     * Editar tipo de usuário existente
     */
    public function editarTipoUsuario(int $id, string $tipo): array
    {
        try {
            // Verificar se o tipo existe
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table3} WHERE id = :id");
            $stmt_check->bindValue(":id", $id);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() == 0) {
                return ['success' => false, 'message' => 'Tipo de usuário não encontrado'];
            }
            
            // Verificar se o novo tipo já existe em outro registro
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table3} WHERE tipo = :tipo AND id != :id");
            $stmt_check->bindValue(":tipo", trim($tipo));
            $stmt_check->bindValue(":id", $id);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                return ['success' => false, 'message' => 'Tipo de usuário já existe'];
            }
            
            $stmt = $this->connect->prepare("UPDATE {$this->table3} SET tipo = :tipo WHERE id = :id");
            $stmt->bindValue(":tipo", trim($tipo));
            $stmt->bindValue(":id", $id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Tipo de usuário atualizado com sucesso'];
            } else {
                return ['success' => false, 'message' => 'Erro ao atualizar tipo de usuário'];
            }
        } catch (Exception $e) {
            error_log("Erro ao editar tipo de usuário: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro interno do sistema'];
        }
    }
    
    /**
     * Excluir tipo de usuário
     */
    public function excluirTipoUsuario(int $id): array
    {
        try {
            // Verificar se há permissões usando este tipo
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table4} WHERE id_tipos_usuarios = :id");
            $stmt_check->bindValue(":id", $id);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                return ['success' => false, 'message' => 'Não é possível excluir o tipo. Existem permissões vinculadas a ele.'];
            }
            
            $stmt = $this->connect->prepare("DELETE FROM {$this->table3} WHERE id = :id");
            $stmt->bindValue(":id", $id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Tipo de usuário excluído com sucesso'];
            } else {
                return ['success' => false, 'message' => 'Erro ao excluir tipo de usuário'];
            }
        } catch (Exception $e) {
            error_log("Erro ao excluir tipo de usuário: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro interno do sistema'];
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

    // ==================== FUNÇÕES PARA USUÁRIOS ====================
    
    /**
     * Criar novo usuário
     */
    public function criarUsuario(array $dados): array
    {
        try {
            // Verificar se o email já existe
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table1} WHERE email = :email");
            $stmt_check->bindValue(":email", $dados['email']);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                return ['success' => false, 'message' => 'E-mail já cadastrado'];
            }
            
            // Verificar se o CPF já existe
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table1} WHERE cpf = :cpf");
            $stmt_check->bindValue(":cpf", $dados['cpf']);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                return ['success' => false, 'message' => 'CPF já cadastrado'];
            }
            
            // Verificar se o setor existe
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table2} WHERE id = :id_setor");
            $stmt_check->bindValue(":id_setor", $dados['id_setor']);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() == 0) {
                return ['success' => false, 'message' => 'Setor não encontrado'];
            }
            
            $stmt = $this->connect->prepare("INSERT INTO {$this->table1} (nome, email, telefone, cpf, senha, id_setor) VALUES (:nome, :email, :telefone, :cpf, :senha, :id_setor)");
            $stmt->bindValue(":nome", trim($dados['nome']));
            $stmt->bindValue(":email", trim($dados['email']));
            $stmt->bindValue(":telefone", $dados['telefone'] ?? null);
            $stmt->bindValue(":cpf", $dados['cpf']);
            $stmt->bindValue(":senha", password_hash($dados['senha'], PASSWORD_DEFAULT));
            $stmt->bindValue(":id_setor", $dados['id_setor']);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Usuário criado com sucesso', 'id' => $this->connect->lastInsertId()];
            } else {
                return ['success' => false, 'message' => 'Erro ao criar usuário'];
            }
        } catch (Exception $e) {
            error_log("Erro ao criar usuário: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro interno do sistema'];
        }
    }
    
    /**
     * Editar usuário existente
     */
    public function editarUsuario(int $id, array $dados): array
    {
        try {
            // Verificar se o usuário existe
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table1} WHERE id = :id");
            $stmt_check->bindValue(":id", $id);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() == 0) {
                return ['success' => false, 'message' => 'Usuário não encontrado'];
            }
            
            // Verificar se o email já existe em outro usuário
            if (isset($dados['email'])) {
                $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table1} WHERE email = :email AND id != :id");
                $stmt_check->bindValue(":email", trim($dados['email']));
                $stmt_check->bindValue(":id", $id);
                $stmt_check->execute();
                
                if ($stmt_check->rowCount() > 0) {
                    return ['success' => false, 'message' => 'E-mail já cadastrado'];
                }
            }
            
            // Verificar se o CPF já existe em outro usuário
            if (isset($dados['cpf'])) {
                $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table1} WHERE cpf = :cpf AND id != :id");
                $stmt_check->bindValue(":cpf", $dados['cpf']);
                $stmt_check->bindValue(":id", $id);
                $stmt_check->execute();
                
                if ($stmt_check->rowCount() > 0) {
                    return ['success' => false, 'message' => 'CPF já cadastrado'];
                }
            }
            
            // Verificar se o setor existe
            if (isset($dados['id_setor'])) {
                $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table2} WHERE id = :id_setor");
                $stmt_check->bindValue(":id_setor", $dados['id_setor']);
                $stmt_check->execute();
                
                if ($stmt_check->rowCount() == 0) {
                    return ['success' => false, 'message' => 'Setor não encontrado'];
                }
            }
            
            // Construir query dinamicamente
            $campos = [];
            $valores = [];
            
            if (isset($dados['nome'])) {
                $campos[] = "nome = :nome";
                $valores[':nome'] = trim($dados['nome']);
            }
            if (isset($dados['email'])) {
                $campos[] = "email = :email";
                $valores[':email'] = trim($dados['email']);
            }
            if (isset($dados['telefone'])) {
                $campos[] = "telefone = :telefone";
                $valores[':telefone'] = $dados['telefone'];
            }
            if (isset($dados['cpf'])) {
                $campos[] = "cpf = :cpf";
                $valores[':cpf'] = $dados['cpf'];
            }
            if (isset($dados['senha'])) {
                $campos[] = "senha = :senha";
                $valores[':senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);
            }
            if (isset($dados['id_setor'])) {
                $campos[] = "id_setor = :id_setor";
                $valores[':id_setor'] = $dados['id_setor'];
            }
            
            if (empty($campos)) {
                return ['success' => false, 'message' => 'Nenhum campo para atualizar'];
            }
            
            $valores[':id'] = $id;
            $sql = "UPDATE {$this->table1} SET " . implode(", ", $campos) . " WHERE id = :id";
            $stmt = $this->connect->prepare($sql);
            
            if ($stmt->execute($valores)) {
                return ['success' => true, 'message' => 'Usuário atualizado com sucesso'];
            } else {
                return ['success' => false, 'message' => 'Erro ao atualizar usuário'];
            }
        } catch (Exception $e) {
            error_log("Erro ao editar usuário: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro interno do sistema'];
        }
    }
    
    /**
     * Excluir usuário
     */
    public function excluirUsuario(int $id): array
    {
        try {
            // Verificar se há permissões usando este usuário
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table4} WHERE id_usuarios = :id");
            $stmt_check->bindValue(":id", $id);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                return ['success' => false, 'message' => 'Não é possível excluir o usuário. Existem permissões vinculadas a ele.'];
            }
            
            $stmt = $this->connect->prepare("DELETE FROM {$this->table1} WHERE id = :id");
            $stmt->bindValue(":id", $id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Usuário excluído com sucesso'];
            } else {
                return ['success' => false, 'message' => 'Erro ao excluir usuário'];
            }
        } catch (Exception $e) {
            error_log("Erro ao excluir usuário: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro interno do sistema'];
        }
    }
    
    /**
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
     * Buscar usuário por ID
     */
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
    }

    // ==================== FUNÇÕES PARA PERMISSÕES ====================
    
    /**
     * Adicionar permissão para usuário
     */
    public function adicionarPermissao(int $id_usuario, int $id_tipo_usuario, int $id_sistema): array
    {
        try {
            // Verificar se o usuário existe
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table1} WHERE id = :id");
            $stmt_check->bindValue(":id", $id_usuario);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() == 0) {
                return ['success' => false, 'message' => 'Usuário não encontrado'];
            }
            
            // Verificar se o tipo de usuário existe
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table3} WHERE id = :id");
            $stmt_check->bindValue(":id", $id_tipo_usuario);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() == 0) {
                return ['success' => false, 'message' => 'Tipo de usuário não encontrado'];
            }
            
            // Verificar se o sistema existe
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table5} WHERE id = :id");
            $stmt_check->bindValue(":id", $id_sistema);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() == 0) {
                return ['success' => false, 'message' => 'Sistema não encontrado'];
            }
            
            // Verificar se a permissão já existe
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table4} WHERE id_usuarios = :id_usuario AND id_tipos_usuarios = :id_tipo_usuario AND id_sistemas = :id_sistema");
            $stmt_check->bindValue(":id_usuario", $id_usuario);
            $stmt_check->bindValue(":id_tipo_usuario", $id_tipo_usuario);
            $stmt_check->bindValue(":id_sistema", $id_sistema);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                return ['success' => false, 'message' => 'Permissão já existe'];
            }
            
            $stmt = $this->connect->prepare("INSERT INTO {$this->table4} (id_usuarios, id_tipos_usuarios, id_sistemas) VALUES (:id_usuario, :id_tipo_usuario, :id_sistema)");
            $stmt->bindValue(":id_usuario", $id_usuario);
            $stmt->bindValue(":id_tipo_usuario", $id_tipo_usuario);
            $stmt->bindValue(":id_sistema", $id_sistema);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Permissão adicionada com sucesso', 'id' => $this->connect->lastInsertId()];
            } else {
                return ['success' => false, 'message' => 'Erro ao adicionar permissão'];
            }
        } catch (Exception $e) {
            error_log("Erro ao adicionar permissão: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro interno do sistema'];
        }
    }
    
    /**
     * Editar permissão existente
     */
    public function editarPermissao(int $id, int $id_usuario, int $id_tipo_usuario, int $id_sistema): array
    {
        try {
            // Verificar se a permissão existe
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table4} WHERE id = :id");
            $stmt_check->bindValue(":id", $id);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() == 0) {
                return ['success' => false, 'message' => 'Permissão não encontrada'];
            }
            
            // Verificar se o usuário existe
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table1} WHERE id = :id");
            $stmt_check->bindValue(":id", $id_usuario);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() == 0) {
                return ['success' => false, 'message' => 'Usuário não encontrado'];
            }
            
            // Verificar se o tipo de usuário existe
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table3} WHERE id = :id");
            $stmt_check->bindValue(":id", $id_tipo_usuario);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() == 0) {
                return ['success' => false, 'message' => 'Tipo de usuário não encontrado'];
            }
            
            // Verificar se o sistema existe
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table5} WHERE id = :id");
            $stmt_check->bindValue(":id", $id_sistema);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() == 0) {
                return ['success' => false, 'message' => 'Sistema não encontrado'];
            }
            
            // Verificar se a nova combinação já existe em outra permissão
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table4} WHERE id_usuarios = :id_usuario AND id_tipos_usuarios = :id_tipo_usuario AND id_sistemas = :id_sistema AND id != :id");
            $stmt_check->bindValue(":id_usuario", $id_usuario);
            $stmt_check->bindValue(":id_tipo_usuario", $id_tipo_usuario);
            $stmt_check->bindValue(":id_sistema", $id_sistema);
            $stmt_check->bindValue(":id", $id);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                return ['success' => false, 'message' => 'Permissão já existe'];
            }
            
            $stmt = $this->connect->prepare("UPDATE {$this->table4} SET id_usuarios = :id_usuario, id_tipos_usuarios = :id_tipo_usuario, id_sistemas = :id_sistema WHERE id = :id");
            $stmt->bindValue(":id_usuario", $id_usuario);
            $stmt->bindValue(":id_tipo_usuario", $id_tipo_usuario);
            $stmt->bindValue(":id_sistema", $id_sistema);
            $stmt->bindValue(":id", $id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Permissão atualizada com sucesso'];
            } else {
                return ['success' => false, 'message' => 'Erro ao atualizar permissão'];
            }
        } catch (Exception $e) {
            error_log("Erro ao editar permissão: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro interno do sistema'];
        }
    }
    
    /**
     * Excluir permissão
     */
    public function excluirPermissao(int $id): array
    {
        try {
            // Verificar se a permissão existe
            $stmt_check = $this->connect->prepare("SELECT id FROM {$this->table4} WHERE id = :id");
            $stmt_check->bindValue(":id", $id);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() == 0) {
                return ['success' => false, 'message' => 'Permissão não encontrada'];
            }
            
            $stmt = $this->connect->prepare("DELETE FROM {$this->table4} WHERE id = :id");
            $stmt->bindValue(":id", $id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Permissão excluída com sucesso'];
            } else {
                return ['success' => false, 'message' => 'Erro ao excluir permissão'];
            }
        } catch (Exception $e) {
            error_log("Erro ao excluir permissão: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro interno do sistema'];
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
}
