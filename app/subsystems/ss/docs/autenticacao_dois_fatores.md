# Autenticação de Dois Fatores - Sistema de Cursos

## Visão Geral

O sistema de cursos agora implementa autenticação de dois fatores quando há candidatos associados a um curso. Esta funcionalidade foi implementada seguindo o mesmo padrão usado no módulo "Limpar Banco".

## Como Funciona

### 1. Detecção de Candidato Associado
Quando um usuário acessa a página de cursos com o parâmetro GET `candidato_associado`, o sistema ativa automaticamente a autenticação de dois fatores.

**URL de exemplo:**
```
cursos.php?candidato_associado=1
```

### 2. Processo de Autenticação

#### Etapa 1: Solicitação de Email
- O sistema exibe um modal solicitando o email do administrador
- O email é validado usando `FILTER_VALIDATE_EMAIL`
- Um código de 6 dígitos é gerado aleatoriamente (100000-999999)
- O código é armazenado na sessão (`$_SESSION['codigo']`)
- O email é armazenado na sessão (`$_SESSION['codigo_email']`)
- Um email é enviado para o administrador com o código de verificação

#### Etapa 2: Validação do Código
- O sistema exibe um modal para inserção do código
- O código inserido é comparado com o código armazenado na sessão
- O email inserido é comparado com o email armazenado na sessão
- Se ambos coincidirem, a autenticação é bem-sucedida
- Se não coincidirem, o usuário é redirecionado com erro (`?erro_codigo`)

### 3. Interface do Usuário

#### Modal de Email
- Design consistente com o tema do sistema
- Aviso de segurança destacado
- Campo de email pré-preenchido com o email da sessão (se disponível)
- Botão "Enviar código" com animações

#### Modal de Código
- Campo numérico para inserção do código de 6 dígitos
- Formatação especial para melhor legibilidade
- Botões "Cancelar" e "Validar Código"
- Exibição do email para confirmação

### 4. Tratamento de Erros

O sistema trata os seguintes cenários de erro:

- **Código inválido**: Redireciona para `cursos.php?erro_codigo`
- **Email inválido**: Validação no frontend e backend
- **Falha no envio de email**: Depende da configuração do servidor

### 5. Feedback Visual

O sistema exibe feedback visual através de modais informativos:

- **Sucesso**: Ícone verde com mensagem de confirmação
- **Erro**: Ícone vermelho com mensagem de erro
- **Aviso**: Ícone amarelo com mensagem de atenção

## Implementação Técnica

### Variáveis de Sessão Utilizadas
```php
$_SESSION['codigo']        // Código de verificação gerado
$_SESSION['codigo_email']  // Email para onde o código foi enviado
```

### Parâmetros GET Suportados
- `candidato_associado=1` - Ativa a autenticação de dois fatores
- `erro_codigo` - Exibe erro de código inválido

### Estrutura do Código
```php
// Verificação do parâmetro GET
if (isset($_GET['candidato_associado'])) {
    // Lógica de autenticação de dois fatores
    if (isset($_POST['email']) && !isset($_POST['codigo'])) {
        // Etapa 1: Envio do código
    }
    if (isset($_POST['codigo']) && isset($_POST['email'])) {
        // Etapa 2: Validação do código
    }
}
```

## Segurança

- Códigos são gerados aleatoriamente com alta entropia
- Validação tanto no frontend quanto no backend
- Limpeza das variáveis de sessão após validação bem-sucedida
- Redirecionamento seguro em caso de erro

## Compatibilidade

- Funciona com o sistema de sessões existente
- Compatível com o sistema de permissões atual
- Mantém a consistência visual com o resto da aplicação
- Responsivo para diferentes tamanhos de tela

## Uso Recomendado

Esta funcionalidade deve ser ativada quando:
- Há candidatos associados a um curso
- É necessária confirmação adicional para operações sensíveis
- O administrador precisa de uma camada extra de segurança

## Exemplo de Uso

```php
// Para ativar a autenticação de dois fatores
header('Location: cursos.php?candidato_associado=1');
```

```html
<!-- Link para ativar a autenticação -->
<a href="cursos.php?candidato_associado=1">Gerenciar Cursos (2FA)</a>
```
