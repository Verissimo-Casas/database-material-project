<?php
// FILE: app/models/Boleto.php

class Boleto {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function create($data) {
        $query = "INSERT INTO boleto (ID_Pagamento, Forma_Pagamento, Valor, Dt_Vencimento, ID_Matricula) 
                  VALUES (:id, :forma, :valor, :vencimento, :matricula)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':forma', $data['forma']);
        $stmt->bindParam(':valor', $data['valor']);
        $stmt->bindParam(':vencimento', $data['vencimento']);
        $stmt->bindParam(':matricula', $data['matricula']);
        
        return $stmt->execute();
    }
    
    public function getAll() {
        $query = "SELECT b.*, a.AL_Nome as aluno_nome 
                  FROM boleto b 
                  LEFT JOIN matricula m ON b.ID_Matricula = m.ID_Matricula 
                  LEFT JOIN aluno a ON m.ID_Matricula = a.ID_Matricula 
                  ORDER BY b.Dt_Vencimento DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getByMatricula($matricula_id) {
        $query = "SELECT * FROM boleto WHERE ID_Matricula = :matricula ORDER BY Dt_Vencimento DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':matricula', $matricula_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function markAsPaid($id, $data_pagamento) {
        $query = "UPDATE boleto SET Dt_Pagamento = :data WHERE ID_Pagamento = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':data', $data_pagamento);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    public function getOverdue() {
        $query = "SELECT b.*, a.AL_Nome as aluno_nome, a.AL_Email as aluno_email 
                  FROM boleto b 
                  LEFT JOIN matricula m ON b.ID_Matricula = m.ID_Matricula 
                  LEFT JOIN aluno a ON m.ID_Matricula = a.ID_Matricula 
                  WHERE b.Dt_Vencimento < CURDATE() AND b.Dt_Pagamento IS NULL";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getNextId() {
        $query = "SELECT COALESCE(MAX(ID_Pagamento), 0) + 1 as next_id FROM boleto";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['next_id'];
    }
}
?>
