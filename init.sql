-- FILE: init.sql
CREATE DATABASE IF NOT EXISTS academiabd;

-- Create user and grant privileges
CREATE USER IF NOT EXISTS 'academia_user'@'%' IDENTIFIED BY 'academia_pass';
GRANT ALL PRIVILEGES ON academiabd.* TO 'academia_user'@'%';
FLUSH PRIVILEGES;

USE academiabd;

-- Tabela administrador
CREATE TABLE administrador (
    ID_Admin INT(11) NOT NULL PRIMARY KEY,
    A_CPF VARCHAR(11) NOT NULL,
    A_Nome VARCHAR(50) NOT NULL,
    A_Endereco VARCHAR(100) NULL,
    A_Dt_Nasc DATE NOT NULL,
    A_Num_Contato VARCHAR(11) NULL,
    A_Email VARCHAR(50) NULL,
    A_Senha VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- Tabela instrutor
CREATE TABLE instrutor (
    CREF VARCHAR(8) NOT NULL PRIMARY KEY,
    L_CPF VARCHAR(11) NOT NULL,
    L_Nome VARCHAR(50) NOT NULL,
    L_Dt_Nasc DATE NOT NULL,
    L_Endereco VARCHAR(100) NULL,
    L_Num_Contato VARCHAR(11) NULL,
    L_Email VARCHAR(50) NOT NULL,
    L_Senha VARCHAR(255) NOT NULL
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
    AL_Senha VARCHAR(255) NOT NULL,
    ID_Matricula INT(11) NULL,
    FOREIGN KEY (ID_Matricula) REFERENCES matricula(ID_Matricula)
) ENGINE=InnoDB;

-- Tabela boleto
CREATE TABLE boleto (
    ID_Pagamento INT(11) NOT NULL PRIMARY KEY,
    Forma_Pagamento VARCHAR(20) NULL,
    Valor DECIMAL(9,2) NULL DEFAULT 50.00,
    Dt_Pagamento DATE NULL,
    Dt_Vencimento DATE NOT NULL,
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

-- Inserir dados iniciais para teste
INSERT INTO administrador (ID_Admin, A_CPF, A_Nome, A_Endereco, A_Dt_Nasc, A_Num_Contato, A_Email, A_Senha) 
VALUES (1, '12345678901', 'Admin Sistema', 'Rua Admin, 123', '1980-01-01', '11999999999', 'admin@academia.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO instrutor (CREF, L_CPF, L_Nome, L_Dt_Nasc, L_Endereco, L_Num_Contato, L_Email, L_Senha) 
VALUES ('12345678', '98765432101', 'João Silva', '1985-05-15', 'Rua Instrutor, 456', '11888888888', 'joao@academia.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO matricula (ID_Matricula, M_Status, Dt_Inicio, Dt_Fim) 
VALUES (1, 1, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR));

INSERT INTO aluno (CPF, AL_Nome, AL_Dt_Nasc, AL_Endereco, AL_Num_Contato, AL_Email, AL_Senha, ID_Matricula) 
VALUES ('11122233344', 'Maria Santos', '1990-03-20', 'Rua Aluno, 789', '11777777777', 'maria@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Inserir boletos de exemplo
INSERT INTO boleto (ID_Pagamento, Forma_Pagamento, Valor, Dt_Vencimento, ID_Matricula) 
VALUES (1, 'Boleto', 50.00, '2025-08-08', 1);

INSERT INTO boleto (ID_Pagamento, Forma_Pagamento, Valor, Dt_Pagamento, Dt_Vencimento, ID_Matricula) 
VALUES (2, 'PIX', 50.00, '2025-07-05', '2025-07-08', 1);

-- Inserir relacionamentos de gerenciamento
INSERT INTO gerencia (ID_Admin, ID_Matricula) VALUES (1, 1);
