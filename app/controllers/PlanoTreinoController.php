<?php
// FILE: app/controllers/PlanoTreinoController.php

require_once BASE_PATH . '/app/models/User.php';

class PlanoTreinoController {
    
    public function index() {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "auth/login");
            exit();
        }
        
        // Verificar permissões - apenas instrutores e admins podem ver planos
        if ($_SESSION['user_type'] === 'aluno') {
            // Alunos podem ver apenas seus próprios planos
            $this->viewMyPlans();
            return;
        }
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            $query = "SELECT p.*, i.L_Nome as instrutor_nome 
                     FROM plano_treino p 
                     LEFT JOIN monta m ON p.ID_Plano = m.ID_Plano 
                     LEFT JOIN instrutor i ON m.CREF_j = i.CREF
                     ORDER BY p.ID_Plano DESC";
            
            $stmt = $db->prepare($query);
            $stmt->execute();
            $planos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Erro ao buscar planos: " . $e->getMessage());
            $planos = [];
        }
        
        $csrf_token = generateCSRFToken();
        render('plano_treino/index', compact('planos', 'csrf_token'));
    }
    
    public function create() {
        // Verificar se o usuário está logado e tem permissão
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "auth/login");
            exit();
        }
        
        if ($_SESSION['user_type'] === 'aluno') {
            http_response_code(403);
            echo "Acesso negado. Apenas instrutores podem criar planos de treino.";
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCSRFToken($_POST['csrf_token'])) {
                $error = "Token CSRF inválido.";
            } else {
                $descricao = sanitizeInput($_POST['descricao']);
                
                if (empty($descricao)) {
                    $error = "Descrição é obrigatória.";
                } else {
                    try {
                        $database = new Database();
                        $db = $database->getConnection();
                        
                        // Inserir novo plano
                        $query = "INSERT INTO plano_treino (Descricao) VALUES (:descricao)";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':descricao', $descricao);
                        
                        if ($stmt->execute()) {
                            $plano_id = $db->lastInsertId();
                            
                            // Associar ao instrutor (se for instrutor)
                            if ($_SESSION['user_type'] === 'instrutor') {
                                $query_monta = "INSERT INTO monta (CREF_j, ID_Plano) VALUES (:cref, :plano_id)";
                                $stmt_monta = $db->prepare($query_monta);
                                $stmt_monta->bindParam(':cref', $_SESSION['user_id']);
                                $stmt_monta->bindParam(':plano_id', $plano_id);
                                $stmt_monta->execute();
                            }
                            
                            $success = "Plano de treino criado com sucesso!";
                        } else {
                            $error = "Erro ao criar plano de treino.";
                        }
                        
                    } catch (Exception $e) {
                        error_log("Erro ao criar plano: " . $e->getMessage());
                        $error = "Erro interno do servidor.";
                    }
                }
            }
        }
        
        $csrf_token = generateCSRFToken();
        $error = $error ?? null;
        $success = $success ?? null;
        render('plano_treino/create', compact('csrf_token', 'error', 'success'));
    }
    
    public function viewMyPlans() {
        // Para alunos visualizarem seus próprios planos
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'aluno') {
            header("Location: " . BASE_URL . "auth/login");
            exit();
        }

        try {
            $database = new Database();
            $db = $database->getConnection();
            
            // Query corrigida - buscar planos associados ao aluno através da matrícula
            // Como não há tabela 'segue', vamos mostrar todos os planos disponíveis para o aluno
            $query = "SELECT p.*, i.L_Nome as instrutor_nome 
                     FROM plano_treino p 
                     LEFT JOIN monta m ON p.ID_Plano = m.ID_Plano
                     LEFT JOIN instrutor i ON m.CREF_j = i.CREF
                     ORDER BY p.ID_Plano DESC";
            
            $stmt = $db->prepare($query);
            $stmt->execute();
            $planos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Erro ao buscar planos do aluno: " . $e->getMessage());
            $planos = [];
        }
        
        require_once BASE_PATH . '/app/views/plano_treino/meus_planos.php';
    }
    
    public function edit($id = null) {
        // Verificar permissões
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] === 'aluno') {
            http_response_code(403);
            echo "Acesso negado. Apenas instrutores e administradores podem editar planos.";
            return;
        }
        
        if (!$id) {
            http_response_code(404);
            echo "Plano não encontrado.";
            return;
        }
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!validateCSRFToken($_POST['csrf_token'])) {
                    $error = "Token CSRF inválido.";
                } else {
                    $descricao = sanitizeInput($_POST['descricao']);
                    
                    $query = "UPDATE plano_treino SET Descricao = :descricao WHERE ID_Plano = :id";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':descricao', $descricao);
                    $stmt->bindParam(':id', $id);
                    
                    if ($stmt->execute()) {
                        $success = "Plano atualizado com sucesso!";
                    } else {
                        $error = "Erro ao atualizar plano.";
                    }
                }
            }
            
            // Buscar plano atual
            $query = "SELECT * FROM plano_treino WHERE ID_Plano = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $plano = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$plano) {
                http_response_code(404);
                echo "Plano não encontrado.";
                return;
            }
            
        } catch (Exception $e) {
            error_log("Erro ao buscar/editar plano: " . $e->getMessage());
            $error = "Erro interno do servidor.";
        }
        
        $csrf_token = generateCSRFToken();
        $error = $error ?? null;
        $success = $success ?? null;
        render('plano_treino/edit', compact('plano', 'csrf_token', 'error', 'success'));
    }
    
    public function view($id = null) {
        // Verificar se o ID foi fornecido
        if (!$id) {
            $_SESSION['error'] = "ID do plano não fornecido.";
            header("Location: " . BASE_URL . "plano_treino");
            exit();
        }
        
        // Verificar se o usuário está logado
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "auth/login");
            exit();
        }
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            // Buscar o plano com informações do instrutor
            $query = "SELECT p.*, i.L_Nome as instrutor_nome, i.L_Email as instrutor_email
                     FROM plano_treino p 
                     LEFT JOIN monta m ON p.ID_Plano = m.ID_Plano
                     LEFT JOIN instrutor i ON m.CREF_j = i.CREF
                     WHERE p.ID_Plano = :id";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $plano = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$plano) {
                $_SESSION['error'] = "Plano de treino não encontrado.";
                header("Location: " . BASE_URL . "plano_treino");
                exit();
            }
            
        } catch (Exception $e) {
            error_log("Erro ao buscar plano: " . $e->getMessage());
            $_SESSION['error'] = "Erro ao carregar plano de treino.";
            header("Location: " . BASE_URL . "plano_treino");
            exit();
        }
        
        require_once BASE_PATH . '/app/views/plano_treino/view.php';
    }
}
?>
