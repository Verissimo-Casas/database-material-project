-- Add notification table to the database
USE academiabd;

CREATE TABLE IF NOT EXISTS notificacao (
    ID_Notificacao INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Tipo_Notificacao VARCHAR(50) NOT NULL,
    Titulo VARCHAR(100) NOT NULL,
    Mensagem TEXT NOT NULL,
    Data_Criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Data_Leitura DATETIME NULL,
    Status ENUM('nao_lida', 'lida', 'arquivada') DEFAULT 'nao_lida',
    Destinatario_CPF VARCHAR(11) NULL,
    Destinatario_Tipo ENUM('aluno', 'instrutor', 'administrador') NOT NULL,
    Remetente_ID VARCHAR(50) NULL,
    Remetente_Tipo ENUM('aluno', 'instrutor', 'administrador') NOT NULL,
    ID_Referencia INT(11) NULL COMMENT 'ID of related entity (avaliacao, aula, etc)',
    Tipo_Referencia VARCHAR(50) NULL COMMENT 'Type of related entity',
    FOREIGN KEY (Destinatario_CPF) REFERENCES aluno(CPF) ON DELETE CASCADE,
    INDEX idx_destinatario (Destinatario_CPF, Status),
    INDEX idx_data_criacao (Data_Criacao),
    INDEX idx_referencia (ID_Referencia, Tipo_Referencia)
) ENGINE=InnoDB;
