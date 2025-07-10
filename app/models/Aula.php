<?php
// FILE: app/models/Aula.php

require_once BASE_PATH . '/config/database.php';

class Aula {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function getAll() {
        $stmt = $this->conn->query("
            SELECT a.*, i.L_Nome as instrutor_nome, i.CREF
            FROM aula a 
            LEFT JOIN cria c ON a.ID_Aula = c.ID_Aula 
            LEFT JOIN instrutor i ON c.CREF_Instrutor = i.CREF
            ORDER BY a.Dt_Hora ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getUpcoming($limit = 5) {
        $limit = (int)$limit; // Ensure it's an integer
        
        // Much simpler query - just get the aulas first
        $stmt = $this->conn->prepare("
            SELECT a.ID_Aula, a.Dt_Hora, a.Descricao
            FROM aula a 
            WHERE a.Dt_Hora >= NOW()
            ORDER BY a.Dt_Hora ASC
            LIMIT " . $limit
        );
        $stmt->execute();
        $aulas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Then get additional info for each aula separately
        foreach ($aulas as &$aula) {
            // Get instructor info
            $stmt2 = $this->conn->prepare("
                SELECT i.L_Nome as instrutor_nome, i.CREF
                FROM cria c 
                INNER JOIN instrutor i ON c.CREF_Instrutor = i.CREF
                WHERE c.ID_Aula = ?
            ");
            $stmt2->execute([$aula['ID_Aula']]);
            $instrutor = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            $aula['instrutor_nome'] = $instrutor ? $instrutor['instrutor_nome'] : 'Sem instrutor';
            $aula['CREF'] = $instrutor ? $instrutor['CREF'] : '';
            
            // Get student count
            $stmt3 = $this->conn->prepare("
                SELECT COUNT(*) as total
                FROM frequenta f
                WHERE f.ID_Aula = ?
            ");
            $stmt3->execute([$aula['ID_Aula']]);
            $count = $stmt3->fetch(PDO::FETCH_ASSOC);
            
            $aula['total_alunos'] = $count ? $count['total'] : 0;
        }
        
        // Debug: log the query result
        error_log("getUpcoming found " . count($aulas) . " classes");
        
        return $aulas;
    }
    
    public function getTodayClasses() {
        $stmt = $this->conn->query("
            SELECT COUNT(*) as total
            FROM aula 
            WHERE DATE(Dt_Hora) = CURDATE()
        ");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    public function getByInstructor($cref) {
        $stmt = $this->conn->prepare("
            SELECT a.ID_Aula, a.Dt_Hora, a.Descricao, 
                   COUNT(f.AL_CPF) as total_alunos
            FROM aula a 
            INNER JOIN cria c ON a.ID_Aula = c.ID_Aula 
            LEFT JOIN frequenta f ON a.ID_Aula = f.ID_Aula
            WHERE c.CREF_Instrutor = ?
            GROUP BY a.ID_Aula, a.Dt_Hora, a.Descricao
            ORDER BY a.Dt_Hora ASC
        ");
        $stmt->execute([$cref]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->conn->prepare("
            SELECT a.*, i.L_Nome as instrutor_nome, i.CREF
            FROM aula a 
            LEFT JOIN cria c ON a.ID_Aula = c.ID_Aula 
            LEFT JOIN instrutor i ON c.CREF_Instrutor = i.CREF
            WHERE a.ID_Aula = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO aula (ID_Aula, Dt_Hora, Descricao) 
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([
            $data['ID_Aula'],
            $data['Dt_Hora'],
            $data['Descricao']
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->conn->prepare("
            UPDATE aula 
            SET Dt_Hora = ?, Descricao = ?
            WHERE ID_Aula = ?
        ");
        return $stmt->execute([
            $data['Dt_Hora'],
            $data['Descricao'],
            $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM aula WHERE ID_Aula = ?");
        return $stmt->execute([$id]);
    }
}
?>
