<?php
// FILE: app/models/Avaliacao.php

require_once BASE_PATH . '/config/database.php';

class Avaliacao {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function getAll() {
        $stmt = $this->conn->query("
            SELECT av.*, a.AL_Nome as aluno_nome, i.L_Nome as instrutor_nome, r.Relatorio_Avaliacao
            FROM avaliacao_fisica av
            LEFT JOIN realiza r ON av.ID_Avaliacao = r.ID_Avaliacao
            LEFT JOIN aluno a ON r.AL_CPF = a.CPF
            LEFT JOIN constroi c ON av.ID_Avaliacao = c.ID_Avaliacao
            LEFT JOIN instrutor i ON c.CREF_j = i.CREF
            ORDER BY av.Data_Av DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getRecent($limit = 5) {
        $limit = (int)$limit; // Ensure it's an integer
        $stmt = $this->conn->prepare("
            SELECT av.*, a.AL_Nome as aluno_nome, i.L_Nome as instrutor_nome, r.Relatorio_Avaliacao
            FROM avaliacao_fisica av
            LEFT JOIN realiza r ON av.ID_Avaliacao = r.ID_Avaliacao
            LEFT JOIN aluno a ON r.AL_CPF = a.CPF
            LEFT JOIN constroi c ON av.ID_Avaliacao = c.ID_Avaliacao
            LEFT JOIN instrutor i ON c.CREF_j = i.CREF
            WHERE av.Data_Av >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ORDER BY av.Data_Av DESC
            LIMIT " . $limit
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPendingCount() {
        // Count evaluations created but not yet completed with reports
        $stmt = $this->conn->query("
            SELECT COUNT(*) as total
            FROM avaliacao_fisica av
            LEFT JOIN realiza r ON av.ID_Avaliacao = r.ID_Avaliacao
            WHERE r.Relatorio_Avaliacao IS NULL OR r.Relatorio_Avaliacao = ''
        ");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    public function getActiveStudentsCount() {
        $stmt = $this->conn->query("
            SELECT COUNT(DISTINCT a.CPF) as total
            FROM aluno a
            INNER JOIN matricula m ON a.ID_Matricula = m.ID_Matricula
            WHERE m.M_Status = 1 AND (m.Dt_Fim IS NULL OR m.Dt_Fim > NOW())
        ");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    public function getByInstructor($cref) {
        $stmt = $this->conn->prepare("
            SELECT av.*, a.AL_Nome as aluno_nome, r.Relatorio_Avaliacao
            FROM avaliacao_fisica av
            LEFT JOIN realiza r ON av.ID_Avaliacao = r.ID_Avaliacao
            LEFT JOIN aluno a ON r.AL_CPF = a.CPF
            INNER JOIN constroi c ON av.ID_Avaliacao = c.ID_Avaliacao
            WHERE c.CREF_j = ?
            ORDER BY av.Data_Av DESC
        ");
        $stmt->execute([$cref]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->conn->prepare("
            SELECT av.*, a.AL_Nome as aluno_nome, a.CPF as aluno_cpf, i.L_Nome as instrutor_nome, r.Relatorio_Avaliacao
            FROM avaliacao_fisica av
            LEFT JOIN realiza r ON av.ID_Avaliacao = r.ID_Avaliacao
            LEFT JOIN aluno a ON r.AL_CPF = a.CPF
            LEFT JOIN constroi c ON av.ID_Avaliacao = c.ID_Avaliacao
            LEFT JOIN instrutor i ON c.CREF_j = i.CREF
            WHERE av.ID_Avaliacao = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO avaliacao_fisica (Data_Av, Peso, Altura, IMC) 
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['Data_Av'],
            $data['Peso'],
            $data['Altura'],
            $data['IMC']
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->conn->prepare("
            UPDATE avaliacao_fisica 
            SET Data_Av = ?, Peso = ?, Altura = ?, IMC = ?
            WHERE ID_Avaliacao = ?
        ");
        return $stmt->execute([
            $data['Data_Av'],
            $data['Peso'],
            $data['Altura'],
            $data['IMC'],
            $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM avaliacao_fisica WHERE ID_Avaliacao = ?");
        return $stmt->execute([$id]);
    }
}
?>
