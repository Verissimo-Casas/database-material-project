<?php
// FILE: app/controllers/AulaController.php

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/User.php';

class AulaController {
    
    public function index() {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['user_id'])) {
            redirect('auth/login');
            return;
        }
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            if ($_SESSION['user_type'] === 'aluno') {
                // Alunos veem apenas aulas que frequentam
                $query = "SELECT a.*, i.L_Nome as instrutor_nome, f.Relatorio_Frequencia 
                         FROM aula a 
                         LEFT JOIN cria c ON a.ID_Aula = c.ID_Aula 
                         LEFT JOIN instrutor i ON c.CREF_Instrutor = i.CREF
                         LEFT JOIN frequenta f ON a.ID_Aula = f.ID_Aula AND f.AL_CPF = :cpf
                         WHERE f.AL_CPF = :cpf
                         ORDER BY a.Dt_Hora DESC";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':cpf', $_SESSION['user_id']);
            } else {
                // Instrutores e admins veem todas as aulas
                $query = "SELECT a.*, i.L_Nome as instrutor_nome 
                         FROM aula a 
                         LEFT JOIN cria c ON a.ID_Aula = c.ID_Aula 
                         LEFT JOIN instrutor i ON c.CREF_Instrutor = i.CREF
                         ORDER BY a.Dt_Hora DESC";
                $stmt = $db->prepare($query);
            }
            
            $stmt->execute();
            $aulas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Erro ao buscar aulas: " . $e->getMessage());
            $aulas = [];
        }
        
        $csrf_token = generateCSRFToken();
        require_once BASE_PATH . '/app/views/aula/index.php';
    }
    
    public function create() {
        // Verificar se o usuário está logado e tem permissão
        if (!isset($_SESSION['user_id'])) {
            redirect('auth/login');
            return;
        }
        
        if ($_SESSION['user_type'] === 'aluno') {
            http_response_code(403);
            echo "Acesso negado. Apenas instrutores podem criar aulas.";
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCSRFToken($_POST['csrf_token'])) {
                $error = "Token CSRF inválido.";
            } else {
                $dt_hora = sanitizeInput($_POST['dt_hora']);
                $descricao = sanitizeInput($_POST['descricao']);
                
                if (empty($dt_hora) || empty($descricao)) {
                    $error = "Data/hora e descrição são obrigatórias.";
                } else {
                    try {
                        $database = new Database();
                        $db = $database->getConnection();
                        
                        // Inserir nova aula
                        $query = "INSERT INTO aula (Dt_Hora, Descricao) VALUES (:dt_hora, :descricao)";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':dt_hora', $dt_hora);
                        $stmt->bindParam(':descricao', $descricao);
                        
                        if ($stmt->execute()) {
                            $aula_id = $db->lastInsertId();
                            
                            // Associar ao instrutor (se for instrutor)
                            if ($_SESSION['user_type'] === 'instrutor') {
                                $query_cria = "INSERT INTO cria (CREF_Instrutor, ID_Aula) VALUES (:cref, :aula_id)";
                                $stmt_cria = $db->prepare($query_cria);
                                $stmt_cria->bindParam(':cref', $_SESSION['user_id']);
                                $stmt_cria->bindParam(':aula_id', $aula_id);
                                $stmt_cria->execute();
                            }
                            
                            $success = "Aula criada com sucesso!";
                        } else {
                            $error = "Erro ao criar aula.";
                        }
                        
                    } catch (Exception $e) {
                        error_log("Erro ao criar aula: " . $e->getMessage());
                        $error = "Erro interno do servidor.";
                    }
                }
            }
        }
        
        $csrf_token = generateCSRFToken();
        require_once BASE_PATH . '/app/views/aula/create.php';
    }
    
    public function frequencia($id = null) {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['user_id'])) {
            redirect('auth/login');
            return;
        }
        
        if (!$id) {
            http_response_code(404);
            echo "Aula não encontrada.";
            return;
        }
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['user_type'] !== 'aluno') {
                // Apenas instrutores podem registrar frequência
                if (!validateCSRFToken($_POST['csrf_token'])) {
                    $error = "Token CSRF inválido.";
                } else {
                    $cpf_aluno = sanitizeInput($_POST['cpf_aluno']);
                    $relatorio = sanitizeInput($_POST['relatorio_frequencia']);
                    
                    $query = "INSERT INTO frequenta (ID_Aula, AL_CPF, Relatorio_Frequencia) 
                             VALUES (:aula_id, :cpf, :relatorio)
                             ON DUPLICATE KEY UPDATE Relatorio_Frequencia = :relatorio2";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':aula_id', $id);
                    $stmt->bindParam(':cpf', $cpf_aluno);
                    $stmt->bindParam(':relatorio', $relatorio);
                    $stmt->bindParam(':relatorio2', $relatorio);
                    
                    if ($stmt->execute()) {
                        $success = "Frequência registrada com sucesso!";
                    } else {
                        $error = "Erro ao registrar frequência.";
                    }
                }
            }
            
            // Buscar dados da aula
            $query = "SELECT a.*, i.L_Nome as instrutor_nome 
                     FROM aula a 
                     LEFT JOIN cria c ON a.ID_Aula = c.ID_Aula 
                     LEFT JOIN instrutor i ON c.CREF_Instrutor = i.CREF
                     WHERE a.ID_Aula = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $aula = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Buscar frequências
            $query_freq = "SELECT f.*, a.AL_Nome 
                          FROM frequenta f 
                          INNER JOIN aluno a ON f.AL_CPF = a.CPF
                          WHERE f.ID_Aula = :id";
            $stmt_freq = $db->prepare($query_freq);
            $stmt_freq->bindParam(':id', $id);
            $stmt_freq->execute();
            $frequencias = $stmt_freq->fetchAll(PDO::FETCH_ASSOC);
            
            // Buscar todos os alunos (para instrutor adicionar)
            if ($_SESSION['user_type'] !== 'aluno') {
                $query_alunos = "SELECT CPF, AL_Nome FROM aluno ORDER BY AL_Nome";
                $stmt_alunos = $db->prepare($query_alunos);
                $stmt_alunos->execute();
                $alunos = $stmt_alunos->fetchAll(PDO::FETCH_ASSOC);
            }
            
        } catch (Exception $e) {
            error_log("Erro ao buscar frequência: " . $e->getMessage());
            $error = "Erro interno do servidor.";
        }
        
        $csrf_token = generateCSRFToken();
        require_once BASE_PATH . '/app/views/aula/frequencia.php';
    }
}
?>
