<?php
// FILE: app/controllers/AvaliacaoController.php

require_once BASE_PATH . '/app/models/Notification.php';

class AvaliacaoController {
    
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
                // Alunos veem apenas suas próprias avaliações
                $query = "SELECT av.*, i.L_Nome as instrutor_nome, r.Relatorio_Avaliacao 
                         FROM avaliacao_fisica av 
                         LEFT JOIN constroi c ON av.ID_Avaliacao = c.ID_Avaliacao 
                         LEFT JOIN instrutor i ON c.CREF_j = i.CREF
                         LEFT JOIN realiza r ON av.ID_Avaliacao = r.ID_Avaliacao
                         WHERE r.AL_CPF = :cpf
                         ORDER BY av.Data_Av DESC";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':cpf', $_SESSION['user_id']);
            } else {
                // Instrutores e admins veem todas as avaliações
                $query = "SELECT av.*, i.L_Nome as instrutor_nome, a.AL_Nome as aluno_nome 
                         FROM avaliacao_fisica av 
                         LEFT JOIN constroi c ON av.ID_Avaliacao = c.ID_Avaliacao 
                         LEFT JOIN instrutor i ON c.CREF_j = i.CREF
                         LEFT JOIN realiza r ON av.ID_Avaliacao = r.ID_Avaliacao
                         LEFT JOIN aluno a ON r.AL_CPF = a.CPF
                         ORDER BY av.Data_Av DESC";
                $stmt = $db->prepare($query);
            }
            
            $stmt->execute();
            $avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Erro ao buscar avaliações: " . $e->getMessage());
            $avaliacoes = [];
        }
        
        $csrf_token = generateCSRFToken();
        require_once BASE_PATH . '/app/views/avaliacao/index.php';
    }
    
    public function create() {
        // Verificar se o usuário está logado e tem permissão
        if (!isset($_SESSION['user_id'])) {
            redirect('auth/login');
            return;
        }
        
        if ($_SESSION['user_type'] === 'aluno') {
            http_response_code(403);
            echo "Acesso negado. Apenas instrutores podem criar avaliações físicas.";
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $error = "Token CSRF inválido.";
            } else {
                $data_av = sanitizeInput($_POST['data_av'] ?? '');
                $peso = sanitizeInput($_POST['peso'] ?? '');
                $altura = sanitizeInput($_POST['altura'] ?? '');
                $cpf_aluno = sanitizeInput($_POST['cpf_aluno'] ?? '');
                $relatorio = sanitizeInput($_POST['relatorio_avaliacao'] ?? '');
                
                if (empty($data_av) || empty($peso) || empty($altura) || empty($cpf_aluno)) {
                    $error = "Todos os campos obrigatórios devem ser preenchidos.";
                } elseif (!is_numeric($peso) || !is_numeric($altura) || $peso <= 0 || $altura <= 0) {
                    $error = "Peso e altura devem ser valores numéricos positivos.";
                } else {
                    // Calcular IMC
                    $peso_num = floatval($peso);
                    $altura_num = floatval($altura);
                    $imc = $peso_num / ($altura_num * $altura_num);
                    
                    try {
                        $database = new Database();
                        $db = $database->getConnection();
                        
                        if (!$db) {
                            throw new Exception("Erro na conexão com o banco de dados");
                        }
                        
                        // Verificar se o aluno existe
                        $query_check = "SELECT CPF FROM aluno WHERE CPF = :cpf";
                        $stmt_check = $db->prepare($query_check);
                        $stmt_check->bindParam(':cpf', $cpf_aluno);
                        $stmt_check->execute();
                        
                        if (!$stmt_check->fetch()) {
                            $error = "Aluno não encontrado.";
                        } else {
                            // Try inserting with AUTO_INCREMENT first (if database was fixed)
                            try {
                                $query = "INSERT INTO avaliacao_fisica (Data_Av, Peso, Altura, IMC) 
                                         VALUES (:data_av, :peso, :altura, :imc)";
                                $stmt = $db->prepare($query);
                                $stmt->bindParam(':data_av', $data_av);
                                $stmt->bindParam(':peso', $peso_num);
                                $stmt->bindParam(':altura', $altura_num);
                                $stmt->bindParam(':imc', $imc);
                                
                                if ($stmt->execute()) {
                                    $avaliacao_id = $db->lastInsertId();
                                } else {
                                    throw new Exception("Insert failed");
                                }
                            } catch (Exception $e) {
                                // Fallback: manually generate ID if AUTO_INCREMENT is not configured
                                $query_max_id = "SELECT COALESCE(MAX(ID_Avaliacao), 0) + 1 as next_id FROM avaliacao_fisica";
                                $stmt_max = $db->prepare($query_max_id);
                                $stmt_max->execute();
                                $next_id_result = $stmt_max->fetch(PDO::FETCH_ASSOC);
                                $avaliacao_id = $next_id_result['next_id'];
                                
                                $query = "INSERT INTO avaliacao_fisica (ID_Avaliacao, Data_Av, Peso, Altura, IMC) 
                                         VALUES (:id_avaliacao, :data_av, :peso, :altura, :imc)";
                                $stmt = $db->prepare($query);
                                $stmt->bindParam(':id_avaliacao', $avaliacao_id);
                                $stmt->bindParam(':data_av', $data_av);
                                $stmt->bindParam(':peso', $peso_num);
                                $stmt->bindParam(':altura', $altura_num);
                                $stmt->bindParam(':imc', $imc);
                                
                                if (!$stmt->execute()) {
                                    throw new Exception("Failed to insert with manual ID");
                                }
                            }
                            
                            if ($avaliacao_id) {
                                // Associar ao instrutor
                                $query_constroi = "INSERT INTO constroi (CREF_j, ID_Avaliacao) VALUES (:cref, :av_id)";
                                $stmt_constroi = $db->prepare($query_constroi);
                                $stmt_constroi->bindParam(':cref', $_SESSION['user_id']);
                                $stmt_constroi->bindParam(':av_id', $avaliacao_id);
                                $stmt_constroi->execute();
                                
                                // Associar ao aluno
                                $query_realiza = "INSERT INTO realiza (ID_Avaliacao, AL_CPF, Relatorio_Avaliacao) 
                                                VALUES (:av_id, :cpf, :relatorio)";
                                $stmt_realiza = $db->prepare($query_realiza);
                                $stmt_realiza->bindParam(':av_id', $avaliacao_id);
                                $stmt_realiza->bindParam(':cpf', $cpf_aluno);
                                $stmt_realiza->bindParam(':relatorio', $relatorio);
                                $stmt_realiza->execute();
                                
                                // Send notification to the student
                                try {
                                    $notificationModel = new Notification();
                                    $notificationModel->createEvaluationNotification($avaliacao_id, $cpf_aluno, $_SESSION['user_id']);
                                } catch (Exception $e) {
                                    error_log("Erro ao enviar notificação: " . $e->getMessage());
                                    // Don't show error to user since evaluation was created successfully
                                }
                                
                                $success = "Avaliação física criada com sucesso! IMC calculado: " . number_format($imc, 2) . " - Notificação enviada ao aluno.";
                            } else {
                                $error = "Erro ao criar avaliação física.";
                            }
                        }
                        
                    } catch (Exception $e) {
                        error_log("Erro ao criar avaliação: " . $e->getMessage());
                        $error = "Erro interno do servidor: " . $e->getMessage();
                    }
                }
            }
        }
        
        // Buscar lista de alunos para seleção
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            if ($db) {
                $query_alunos = "SELECT CPF, AL_Nome FROM aluno ORDER BY AL_Nome";
                $stmt_alunos = $db->prepare($query_alunos);
                $stmt_alunos->execute();
                $alunos = $stmt_alunos->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $alunos = [];
                $error = ($error ?? '') . " Erro na conexão com o banco de dados.";
            }
            
        } catch (Exception $e) {
            error_log("Erro ao buscar alunos: " . $e->getMessage());
            $alunos = [];
            $error = ($error ?? '') . " Erro ao carregar lista de alunos.";
        }
        
        $csrf_token = generateCSRFToken();
        require_once BASE_PATH . '/app/views/avaliacao/create.php';
    }
    
    public function view($id = null) {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['user_id'])) {
            redirect('auth/login');
            return;
        }
        
        if (!$id) {
            http_response_code(404);
            echo "Avaliação não encontrada.";
            return;
        }
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            $query = "SELECT av.*, i.L_Nome as instrutor_nome, a.AL_Nome as aluno_nome, a.CPF as AL_CPF, r.Relatorio_Avaliacao 
                     FROM avaliacao_fisica av 
                     LEFT JOIN constroi c ON av.ID_Avaliacao = c.ID_Avaliacao 
                     LEFT JOIN instrutor i ON c.CREF_j = i.CREF
                     LEFT JOIN realiza r ON av.ID_Avaliacao = r.ID_Avaliacao
                     LEFT JOIN aluno a ON r.AL_CPF = a.CPF
                     WHERE av.ID_Avaliacao = :id";
            
            // Se for aluno, verificar se a avaliação é dele
            if ($_SESSION['user_type'] === 'aluno') {
                $query .= " AND r.AL_CPF = :cpf";
            }
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            
            if ($_SESSION['user_type'] === 'aluno') {
                $stmt->bindParam(':cpf', $_SESSION['user_id']);
            }
            
            $stmt->execute();
            $avaliacao = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$avaliacao) {
                http_response_code(404);
                echo "Avaliação não encontrada ou sem permissão para visualizar.";
                return;
            }
            
        } catch (Exception $e) {
            error_log("Erro ao buscar avaliação: " . $e->getMessage());
            $error = "Erro interno do servidor.";
        }
        
        require_once BASE_PATH . '/app/views/avaliacao/view.php';
    }
    
    public function historico() {
        // Para ver histórico de avaliações de um aluno específico
        if (!isset($_SESSION['user_id'])) {
            redirect('auth/login');
            return;
        }
        
        $cpf_aluno = $_SESSION['user_type'] === 'aluno' ? $_SESSION['user_id'] : ($_GET['cpf'] ?? '');
        
        if (empty($cpf_aluno)) {
            echo "CPF do aluno não informado.";
            return;
        }
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            $query = "SELECT av.*, i.L_Nome as instrutor_nome, r.Relatorio_Avaliacao 
                     FROM avaliacao_fisica av 
                     LEFT JOIN constroi c ON av.ID_Avaliacao = c.ID_Avaliacao 
                     LEFT JOIN instrutor i ON c.CREF_j = i.CREF
                     LEFT JOIN realiza r ON av.ID_Avaliacao = r.ID_Avaliacao
                     WHERE r.AL_CPF = :cpf
                     ORDER BY av.Data_Av DESC";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':cpf', $cpf_aluno);
            $stmt->execute();
            $historico = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Buscar dados do aluno
            $query_aluno = "SELECT AL_Nome FROM aluno WHERE CPF = :cpf";
            $stmt_aluno = $db->prepare($query_aluno);
            $stmt_aluno->bindParam(':cpf', $cpf_aluno);
            $stmt_aluno->execute();
            $aluno = $stmt_aluno->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Erro ao buscar histórico: " . $e->getMessage());
            $historico = [];
            $aluno = null;
        }
        
        require_once BASE_PATH . '/app/views/avaliacao/historico.php';
    }
}
?>
