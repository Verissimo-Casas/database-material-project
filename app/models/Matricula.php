<?php
// FILE: app/models/Matricula.php

class Matricula {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function create($data) {
        $query = "INSERT INTO matricula (ID_Matricula, M_Status, Dt_Inicio, Dt_Fim) 
                  VALUES (:id, :status, :dt_inicio, :dt_fim)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':dt_inicio', $data['dt_inicio']);
        $stmt->bindParam(':dt_fim', $data['dt_fim']);
        
        return $stmt->execute();
    }
    
    public function getAll() {
        $query = "SELECT m.*, a.AL_Nome as aluno_nome, a.AL_Email as aluno_email 
                  FROM matricula m 
                  LEFT JOIN aluno a ON m.ID_Matricula = a.ID_Matricula 
                  ORDER BY m.Dt_Inicio DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $query = "SELECT m.*, a.AL_Nome as aluno_nome, a.AL_Email as aluno_email 
                  FROM matricula m 
                  LEFT JOIN aluno a ON m.ID_Matricula = a.ID_Matricula 
                  WHERE m.ID_Matricula = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateStatus($id, $status) {
        $query = "UPDATE matricula SET M_Status = :status WHERE ID_Matricula = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    public function getNextId() {
        $query = "SELECT COALESCE(MAX(ID_Matricula), 0) + 1 as next_id FROM matricula";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['next_id'];
    }
    
    /**
     * Verifica se uma matrícula está ativa (não vencida e com status ativo)
     * BUG-001 FIX: Adiciona validação de data de expiração
     */
    public function isMatriculaActive($matriculaId) {
        $query = "SELECT M_Status, Dt_Fim FROM matricula WHERE ID_Matricula = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $matriculaId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verifica se a matrícula existe e está ativa
        if (!$result || $result['M_Status'] != 1) {
            return false;
        }
        
        // Verifica se a matrícula não está vencida
        if ($result['Dt_Fim'] && $result['Dt_Fim'] < date('Y-m-d')) {
            return false;
        }
        
        return true;
    }
}
?>
