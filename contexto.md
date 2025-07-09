[INÍCIO DO PROMPT]

## Persona e Contexto
Você atuará como um Desenvolvedor Full-Stack Sênior, com especialidade em PHP, arquitetura de software MVC e infraestrutura com Docker. Sua missão é projetar e gerar o código completo para um Sistema de Gestão de Academia (SGF).

## Objetivo Principal
Criar uma aplicação web completa e funcional para gestão de academias, utilizando PHP e Docker, com base estritamente nos requisitos, regras de negócio e no schema de banco de dados fornecidos abaixo.

## Fonte da Verdade (Requisitos Mandatórios)

A seguir estão todos os requisitos e dados que devem ser usados como a única fonte da verdade para a implementação.

### Regras de Negócio

RN-1: Matrículas são obrigatórias para utilização dos serviços.

RN-2: Mensalidades vencidas impedem o acesso do aluno ao sistema.

RN-3: Avaliações físicas devem ser realizadas periodicamente e devidamente registradas.

RN-4: Apenas funcionários autorizados (Instrutores/Administradores) podem editar planos de treino.

### Requisitos Funcionais

RF-1: Sistema de cadastro e login para três perfis: alunos, instrutores e administradores.

RF-2: Módulo de controle de matrículas, registro de pagamentos (boletos) e status de inadimplência.

RF-3: Funcionalidade para cadastrar planos de treino, aulas e avaliações físicas.

RF-4: Geração de relatórios de frequência, inadimplência e desempenho dos alunos.

RF-5: Sistema de notificações para vencimento de mensalidades e necessidade de novas avaliações.

### Requisitos Não Funcionais

RNF-1: A interface deve ser responsiva (compatível com desktop e mobile).

RNF-2: Segurança total dos dados pessoais e históricos, usando as melhores práticas (hashing de senhas, prevenção de SQL Injection).

RNF-3: Disponibilidade mínima de 99%.

RNF-4: Backup automático semanal do banco de dados.

### Schema do Banco de Dados (Estrutura Imutável)
O código SQL a seguir DEVE ser utilizado exatamente como está, sem nenhuma alteração em nomes de tabelas, colunas ou tipos de dados.

CREATE DATABASE IF NOT EXISTS academiabd;
USE academiabd;

-- Tabela administrador
CREATE TABLE administrador (
    ID_Admin INT(11) NOT NULL PRIMARY KEY,
    A_CPF VARCHAR(11) NOT NULL,
    A_Nome VARCHAR(50) NOT NULL,
    A_Endereco VARCHAR(100) NULL,
    A_Dt_Nasc DATE NOT NULL,
    A_Num_Contato VARCHAR(11) NULL,
    A_Email VARCHAR(50) NULL
) ENGINE=InnoDB;

-- Tabela instrutor
CREATE TABLE instrutor (
    CREF VARCHAR(8) NOT NULL PRIMARY KEY,
    L_CPF VARCHAR(11) NOT NULL,
    L_Nome VARCHAR(50) NOT NULL,
    L_Dt_Nasc DATE NOT NULL,
    L_Endereco VARCHAR(100) NULL,
    L_Num_Contato VARCHAR(11) NULL,
    L_Email VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

-- Tabela matricula
CREATE TABLE matricula (
    ID_Matricula INT(11) NOT NULL PRIMARY KEY,
    M_Status TINYINT(4) NULL DEFAULT 1,
    Dt_Inicio DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    Dt_Fim DATETIME NULL
) ENGINE=InnoDB;

-- Tabela plano_treino
CREATE TABLE plano_treino (
    ID_Plano INT(11) NOT NULL PRIMARY KEY,
    Descricao VARCHAR(300) NULL
) ENGINE=InnoDB;

-- Tabela aula
CREATE TABLE aula (
    ID_Aula INT(11) NOT NULL PRIMARY KEY,
    Dt_Hora DATETIME NULL,
    Descricao VARCHAR(300) NULL
) ENGINE=InnoDB;

-- Tabela avaliacao_fisica
CREATE TABLE avaliacao_fisica (
    ID_Avaliacao INT(11) NOT NULL PRIMARY KEY,
    Data_Av DATE NOT NULL,
    Peso DECIMAL(5,2) NOT NULL,
    Altura DECIMAL(5,2) NOT NULL,
    IMC DECIMAL(5,2) NOT NULL
) ENGINE=InnoDB;

-- Tabela aluno
CREATE TABLE aluno (
    CPF VARCHAR(11) NOT NULL PRIMARY KEY,
    AL_Nome VARCHAR(50) NOT NULL,
    AL_Dt_Nasc DATE NOT NULL,
    AL_Endereco VARCHAR(100) NULL,
    AL_Num_Contato VARCHAR(11) NULL,
    AL_Email VARCHAR(50) NOT NULL,
    ID_Matricula INT(11) NULL,
    FOREIGN KEY (ID_Matricula) REFERENCES matricula(ID_Matricula)
) ENGINE=InnoDB;

-- Tabela boleto
CREATE TABLE boleto (
    ID_Pagamento INT(11) NOT NULL PRIMARY KEY,
    Forma_Pagamento VARCHAR(20) NULL,
    Valor DECIMAL(9,2) NULL DEFAULT 50.00,
    Dt_Pagamento DATE NULL,
    ID_Matricula INT(11) NULL,
    FOREIGN KEY (ID_Matricula) REFERENCES matricula(ID_Matricula)
) ENGINE=InnoDB;

-- Tabelas de relacionamento
CREATE TABLE constroi (
    CREF_j VARCHAR(8) NOT NULL,
    ID_Avaliacao INT(11) NULL,
    FOREIGN KEY (CREF_j) REFERENCES instrutor(CREF),
    FOREIGN KEY (ID_Avaliacao) REFERENCES avaliacao_fisica(ID_Avaliacao)
) ENGINE=InnoDB;

CREATE TABLE cria (
    CREF_Instrutor VARCHAR(8) NULL,
    ID_Aula INT(11) NULL,
    FOREIGN KEY (CREF_Instrutor) REFERENCES instrutor(CREF),
    FOREIGN KEY (ID_Aula) REFERENCES aula(ID_Aula)
) ENGINE=InnoDB;

CREATE TABLE frequenta (
    ID_Aula INT(11) NULL,
    AL_CPF VARCHAR(11) NULL,
    Relatorio_Frequencia VARCHAR(300) NULL,
    FOREIGN KEY (ID_Aula) REFERENCES aula(ID_Aula),
    FOREIGN KEY (AL_CPF) REFERENCES aluno(CPF)
) ENGINE=InnoDB;

CREATE TABLE gerencia (
    ID_Admin INT(11) NULL,
    ID_Matricula INT(11) NULL,
    FOREIGN KEY (ID_Admin) REFERENCES administrador(ID_Admin),
    FOREIGN KEY (ID_Matricula) REFERENCES matricula(ID_Matricula)
) ENGINE=InnoDB;

CREATE TABLE monta (
    CREF_j VARCHAR(8) NOT NULL,
    ID_Plano INT(11) NULL,
    FOREIGN KEY (CREF_j) REFERENCES instrutor(CREF),
    FOREIGN KEY (ID_Plano) REFERENCES plano_treino(ID_Plano)
) ENGINE=InnoDB;

CREATE TABLE realiza (
    ID_Avaliacao INT(11) NULL,
    AL_CPF VARCHAR(11) NULL,
    Relatorio_Avaliacao VARCHAR(500) NULL,
    FOREIGN KEY (ID_Avaliacao) REFERENCES avaliacao_fisica(ID_Avaliacao),
    FOREIGN KEY (AL_CPF) REFERENCES aluno(CPF)
) ENGINE=InnoDB;

## Requisitos de Implementação Detalhados

Ambiente de Desenvolvimento (Docker):

Crie um arquivo docker-compose.yml que orquestre três serviços:

app: Serviço PHP 8.x (imagem oficial), com o diretório do projeto montado como volume.

db: Serviço MySQL 8.x, com um volume para persistência de dados e configurado para criar o banco academiabd na inicialização.

webserver: Serviço Nginx (ou Apache), configurado para servir a aplicação PHP.

Inclua um arquivo de inicialização (init.sql) para o serviço db que execute o schema SQL fornecido.

Estrutura da Aplicação (PHP MVC):

Organize o código PHP seguindo o padrão MVC (Model-View-Controller).

Crie uma estrutura de pastas clara: /app, /config, /public, /views, /models, /controllers.

O diretório /public será o document_root do webserver, contendo o index.php (front controller) e os assets (CSS, JS).

Lógica de Negócio e Módulos:

Autenticação: Crie um AuthController que gerencie o login. A lógica deve verificar as tabelas aluno, instrutor e administrador para autenticar e redirecionar para o painel correto.

Controle de Acesso: Implemente um sistema de sessão que verifique o perfil do usuário e o status da matrícula (M_Status) a cada requisição em áreas protegidas.

Models: Crie classes Model para cada tabela principal (ex: Aluno.php, Matricula.php, Instrutor.php) que abstraiam as operações de banco de dados (CRUD).

Frontend: Utilize um framework CSS como Bootstrap 5 ou Tailwind CSS para garantir a responsividade (RNF-1).

## Critérios de Qualidade e Padrões Técnicos

Segurança:

Utilize PDO com Prepared Statements para todas as consultas ao banco de dados para prevenir SQL Injection.

Use password_hash() e password_verify() para o gerenciamento de senhas. Não armazene senhas em texto plano.

Valide e sanitize todas as entradas de usuário.

Código Limpo:

Siga as recomendações de estilo da comunidade (PSR-12).

Adicione comentários claros em lógicas complexas.

Formato de Saída:

Apresente o código em blocos distintos para cada arquivo.

Comece pelo docker-compose.yml, seguido pelos arquivos de configuração do Nginx/Apache, e depois a estrutura de arquivos PHP.

Indique o caminho completo de cada arquivo em um comentário no topo (ex: // FILE: docker-compose.yml ou // FILE: app/controllers/AuthController.php).

[FIM DO PROMPT]