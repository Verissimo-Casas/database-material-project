<?php
// FILE: app/models/User.php

class User {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function authenticate($email, $password) {
        error_log("Authenticating user with email: " . $email);
        
        // Try aluno table first
        $query = "SELECT CPF as id, AL_Nome as nome, AL_Email as email, AL_Senha as senha, 
                         ID_Matricula, 'aluno' as tipo 
                  FROM aluno WHERE AL_Email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Aluno query result: " . print_r($user, true));
        
        if (!$user) {
            // Try instrutor table
            $query = "SELECT CREF as id, L_Nome as nome, L_Email as email, L_Senha as senha, 
                             'instrutor' as tipo 
                      FROM instrutor WHERE L_Email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log("Instrutor query result: " . print_r($user, true));
        }
        
        if (!$user) {
            // Try administrador table
            $query = "SELECT ID_Admin as id, A_Nome as nome, A_Email as email, A_Senha as senha, 
                             'administrador' as tipo 
                      FROM administrador WHERE A_Email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log("Administrador query result: " . print_r($user, true));
        }
        
        if ($user) {
            error_log("Password verification - Password: " . $password . ", Hash: " . $user['senha']);
            $passwordMatch = password_verify($password, $user['senha']) || $password === $user['senha'];
            error_log("Password match result: " . ($passwordMatch ? 'true' : 'false'));
            
            if ($passwordMatch) {
                return $user;
            }
        }
        
        return false;
    }
    
    public function createAluno($data) {
        $query = "INSERT INTO aluno (CPF, AL_Nome, AL_Dt_Nasc, AL_Endereco, AL_Num_Contato, AL_Email, AL_Senha, ID_Matricula) 
                  VALUES (:cpf, :nome, :dt_nasc, :endereco, :contato, :email, :senha, :matricula)";
        
        $stmt = $this->conn->prepare($query);
        
        $hashedPassword = password_hash($data['senha'], PASSWORD_DEFAULT);
        
        $stmt->bindParam(':cpf', $data['cpf']);
        $stmt->bindParam(':nome', $data['nome']);
        $stmt->bindParam(':dt_nasc', $data['dt_nasc']);
        $stmt->bindParam(':endereco', $data['endereco']);
        $stmt->bindParam(':contato', $data['contato']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':senha', $hashedPassword);
        $stmt->bindParam(':matricula', $data['matricula']);
        
        return $stmt->execute();
    }
    
    public function createInstrutor($data) {
        $query = "INSERT INTO instrutor (CREF, L_CPF, L_Nome, L_Dt_Nasc, L_Endereco, L_Num_Contato, L_Email, L_Senha) 
                  VALUES (:cref, :cpf, :nome, :dt_nasc, :endereco, :contato, :email, :senha)";
        
        $stmt = $this->conn->prepare($query);
        
        $hashedPassword = password_hash($data['senha'], PASSWORD_DEFAULT);
        
        $stmt->bindParam(':cref', $data['cref']);
        $stmt->bindParam(':cpf', $data['cpf']);
        $stmt->bindParam(':nome', $data['nome']);
        $stmt->bindParam(':dt_nasc', $data['dt_nasc']);
        $stmt->bindParam(':endereco', $data['endereco']);
        $stmt->bindParam(':contato', $data['contato']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':senha', $hashedPassword);
        
        return $stmt->execute();
    }
}
?>
