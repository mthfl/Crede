# Crede 1 – Sistemas Internos

[![License: Apache-2.0](https://img.shields.io/badge/license-Apache%202.0-blue.svg)](LICENSE)

## 📌 Descrição

Este repositório reúne os **sistemas internos desenvolvidos para a CREDE 1 – Coordenadoria Regional da Educação do Ceará**.  
O objetivo é fornecer soluções digitais que apoiem a gestão educacional e administrativa, otimizando processos e garantindo maior eficiência no dia a dia.

Entre os sistemas desenvolvidos estão:

- **Gerenciador de Estoque** → Controle de materiais e insumos.  
- **Gerenciador de Usuários** → Administração de perfis, permissões e acessos.  
- **Sistema de Seleção** → Cadastro, organização e gerenciamento de resultados de seleções para **Escolas de Educação Profissional do Estado do Ceará**.  

---

## 🛠 Tecnologias Utilizadas

- **PHP** → Backend e lógica da aplicação  
- **APIs** → Integração entre sistemas  
- **MySQL** → Banco de dados relacional  
- **Tailwind CSS** → Estilização responsiva e moderna  
- **Git** → Versionamento de código  
- **.gitignore** → Controle de arquivos e pastas no repositório  

---

## 📂 Estrutura do Projeto

/
├── app/ ← Código principal da aplicação
├── default.php ← Ponto de entrada do sistema
├── package.json ← Dependências e scripts (se aplicável)
├── .gitignore ← Arquivos/pastas ignorados no versionamento
└── LICENSE ← Licença Apache-2.0


---

## 🚀 Instalação e Uso

### Pré-requisitos
- PHP instalado (versão compatível)  
- Servidor Web (Apache, Nginx ou similar)  
- MySQL configurado  
- Node.js / npm (se houver dependências JS)  

### Passos
1. Clone o repositório  
   ```bash
   git clone https://github.com/mthfl/Crede.git

    Acesse a pasta do projeto

    cd Crede

    Configure o banco de dados MySQL

        Crie um banco de dados

        Importe as tabelas (script de inicialização, se houver)

        Ajuste credenciais no arquivo de configuração

    Configure o servidor local (Apache/Nginx) para apontar para default.php

    Inicie a aplicação e acesse no navegador

🤝 Contribuição

Contribuições são bem-vindas!
Para colaborar:

    Faça um fork do projeto

    Crie uma branch para sua feature

git checkout -b feature/minha-feature

Commit com mensagem clara

git commit -m "Adiciona nova funcionalidade X"

Faça o push da branch

    git push origin feature/minha-feature

    Abra um Pull Request

📜 Licença

Este projeto está licenciado sob a Licença Apache-2.0.
Consulte o arquivo LICENSE
para mais informações.
