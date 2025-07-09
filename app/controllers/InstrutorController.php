<?php
// FILE: app/controllers/InstrutorController.php

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/User.php';

class InstrutorController {
    
    public function index() {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['user_id'])) {
            redirect('auth/login');
            return;
        }
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            if ($_SESSION['user_type'] === 'administrador') {
                // Admin pode ver todos os instrutores
                $query = "SELECT CREF, L_Nome as nome, L_Email as email, L_Num_Contato as telefone 
                         FROM instrutor ORDER BY L_Nome";
                $stmt = $db->prepare($query);
            } else {
                // Outros usuários veem apenas dados básicos
                $query = "SELECT CREF, L_Nome as nome, L_Email as email 
                         FROM instrutor ORDER BY L_Nome";
                $stmt = $db->prepare($query);
            }
            
            $stmt->execute();
            $instrutores = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Erro ao buscar instrutores: " . $e->getMessage());
            $instrutores = [];
        }
        
        $csrf_token = generateCSRFToken();
        require_once BASE_PATH . '/app/views/instrutor/index.php';
    }
    
    public function create() {
        // Verificar se o usuário está logado e é admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'administrador') {
            redirect('auth/login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCSRFToken($_POST['csrf_token'])) {
                $error = "Token CSRF inválido.";
            } else {
                $cref = sanitizeInput($_POST['cref']);
                $cpf = sanitizeInput($_POST['cpf']);
                $nome = sanitizeInput($_POST['nome']);
                $dt_nasc = sanitizeInput($_POST['dt_nasc']);
                $endereco = sanitizeInput($_POST['endereco']);
                $contato = sanitizeInput($_POST['contato']);
                $email = sanitizeInput($_POST['email']);
                $senha = sanitizeInput($_POST['senha']);
                
                if (empty($cref) || empty($nome) || empty($email) || empty($senha)) {
                    $error = "CREF, nome, email e senha são obrigatórios.";
                } else {
                    try {
                        $database = new Database();
                        $db = $database->getConnection();
                        
                        // Verificar se CREF já existe
                        $checkQuery = "SELECT CREF FROM instrutor WHERE CREF = :cref";
                        $checkStmt = $db->prepare($checkQuery);
                        $checkStmt->bindParam(':cref', $cref);
                        $checkStmt->execute();
                        
                        if ($checkStmt->fetch()) {
                            $error = "CREF já cadastrado no sistema.";
                        } else {
                            // Inserir novo instrutor
                            $hashedPassword = password_hash($senha, PASSWORD_DEFAULT);
                            
                            $query = "INSERT INTO instrutor (CREF, L_CPF, L_Nome, L_Dt_Nasc, L_Endereco, L_Num_Contato, L_Email, L_Senha) 
                                     VALUES (:cref, :cpf, :nome, :dt_nasc, :endereco, :contato, :email, :senha)";
                            $stmt = $db->prepare($query);
                            
                            $stmt->bindParam(':cref', $cref);
                            $stmt->bindParam(':cpf', $cpf);
                            $stmt->bindParam(':nome', $nome);
                            $stmt->bindParam(':dt_nasc', $dt_nasc);
                            $stmt->bindParam(':endereco', $endereco);
                            $stmt->bindParam(':contato', $contato);
                            $stmt->bindParam(':email', $email);
                            $stmt->bindParam(':senha', $hashedPassword);
                            
                            if ($stmt->execute()) {
                                $success = "Instrutor cadastrado com sucesso!";
                            } else {
                                $error = "Erro ao cadastrar instrutor.";
                            }
                        }
                        
                    } catch (Exception $e) {
                        error_log("Erro ao criar instrutor: " . $e->getMessage());
                        $error = "Erro interno do servidor.";
                    }
                }
            }
        }
        
        $csrf_token = generateCSRFToken();
        require_once BASE_PATH . '/app/views/instrutor/create.php';
    }
    
    public function show($cref) {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['user_id'])) {
            redirect('auth/login');
            return;
        }
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            $query = "SELECT * FROM instrutor WHERE CREF = :cref";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':cref', $cref);
            $stmt->execute();
            
            $instrutor = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$instrutor) {
                redirect('instrutor');
                return;
            }
            
        } catch (Exception $e) {
            error_log("Erro ao buscar instrutor: " . $e->getMessage());
            redirect('instrutor');
            return;
        }
        
        require_once BASE_PATH . '/app/views/instrutor/show.php';
    }
    
    public function edit($cref) {
        // Verificar se o usuário está logado e é admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'administrador') {
            redirect('auth/login');
            return;
        }
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            // Buscar instrutor
            $query = "SELECT * FROM instrutor WHERE CREF = :cref";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':cref', $cref);
            $stmt->execute();
            
            $instrutor = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$instrutor) {
                redirect('instrutor');
                return;
            }
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!validateCSRFToken($_POST['csrf_token'])) {
                    $error = "Token CSRF inválido.";
                } else {
                    $nome = sanitizeInput($_POST['nome']);
                    $endereco = sanitizeInput($_POST['endereco']);
                    $contato = sanitizeInput($_POST['contato']);
                    $email = sanitizeInput($_POST['email']);
                    
                    if (empty($nome) || empty($email)) {
                        $error = "Nome e email são obrigatórios.";
                    } else {
                        try {
                            $updateQuery = "UPDATE instrutor SET L_Nome = :nome, L_Endereco = :endereco, 
                                          L_Num_Contato = :contato, L_Email = :email WHERE CREF = :cref";
                            $updateStmt = $db->prepare($updateQuery);
                            
                            $updateStmt->bindParam(':nome', $nome);
                            $updateStmt->bindParam(':endereco', $endereco);
                            $updateStmt->bindParam(':contato', $contato);
                            $updateStmt->bindParam(':email', $email);
                            $updateStmt->bindParam(':cref', $cref);
                            
                            if ($updateStmt->execute()) {
                                $success = "Instrutor atualizado com sucesso!";
                                // Recarregar dados
                                $stmt->execute();
                                $instrutor = $stmt->fetch(PDO::FETCH_ASSOC);
                            } else {
                                $error = "Erro ao atualizar instrutor.";
                            }
                            
                        } catch (Exception $e) {
                            error_log("Erro ao atualizar instrutor: " . $e->getMessage());
                            $error = "Erro interno do servidor.";
                        }
                    }
                }
            }
            
        } catch (Exception $e) {
            error_log("Erro ao editar instrutor: " . $e->getMessage());
            redirect('instrutor');
            return;
        }
        
        $csrf_token = generateCSRFToken();
        require_once BASE_PATH . '/app/views/instrutor/edit.php';
    }
    
    public function delete($cref) {
        // Verificar se o usuário está logado e é admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'administrador') {
            redirect('auth/login');
            return;
        }
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            $query = "DELETE FROM instrutor WHERE CREF = :cref";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':cref', $cref);
            
            if ($stmt->execute()) {
                $success = "Instrutor excluído com sucesso!";
            } else {
                $error = "Erro ao excluir instrutor.";
            }
            
        } catch (Exception $e) {
            error_log("Erro ao excluir instrutor: " . $e->getMessage());
            $error = "Erro interno do servidor.";
        }
        
        redirect('instrutor');
    }
}
?>
