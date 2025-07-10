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
            $_SESSION['error'] = "Acesso negado. Apenas instrutores podem criar aulas.";
            redirect('aula');
            return;
        }
        
        // Buscar lista de instrutores para o dropdown
        $instrutores = [];
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            $query = "SELECT CREF, L_Nome FROM instrutor ORDER BY L_Nome";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $instrutores = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Erro ao buscar instrutores: " . $e->getMessage());
        }
        
        $csrf_token = generateCSRFToken();
        require_once BASE_PATH . '/app/views/aula/create.php';
    }
    
    public function store() {
        // Verificar se o usuário está logado e tem permissão
        if (!isset($_SESSION['user_id'])) {
            redirect('auth/login');
            return;
        }
        
        if ($_SESSION['user_type'] === 'aluno') {
            $_SESSION['error'] = "Acesso negado. Apenas instrutores podem criar aulas.";
            redirect('aula');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('aula/create');
            return;
        }
        
        try {
            // Validar CSRF token
            if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error'] = "Token CSRF inválido.";
                redirect('aula/create');
                return;
            }
            
            // Sanitizar e validar dados
            $titulo = sanitizeInput($_POST['titulo'] ?? '');
            $tipo = sanitizeInput($_POST['tipo'] ?? '');
            $data_aula = sanitizeInput($_POST['data_aula'] ?? '');
            $hora_inicio = sanitizeInput($_POST['hora_inicio'] ?? '');
            $hora_fim = sanitizeInput($_POST['hora_fim'] ?? '');
            $instrutor = sanitizeInput($_POST['instrutor'] ?? '');
            $capacidade = sanitizeInput($_POST['capacidade'] ?? '');
            $local = sanitizeInput($_POST['local'] ?? '');
            $descricao = sanitizeInput($_POST['descricao'] ?? '');
            
            // Validações obrigatórias
            if (empty($titulo) || empty($tipo) || empty($data_aula) || empty($hora_inicio) || empty($hora_fim)) {
                $_SESSION['error'] = "Todos os campos obrigatórios devem ser preenchidos.";
                redirect('aula/create');
                return;
            }
            
            // Combinar data e hora de início para criar datetime
            $dt_hora = $data_aula . ' ' . $hora_inicio . ':00';
            
            // Criar descrição detalhada baseada nos campos do formulário
            $descricao_completa = "Título: $titulo\nTipo: $tipo\nHorário: $hora_inicio - $hora_fim\nLocal: $local\nCapacidade: $capacidade pessoas";
            if (!empty($descricao)) {
                $descricao_completa .= "\nDescrição: $descricao";
            }
            
            $database = new Database();
            $db = $database->getConnection();
            
            // Get next available ID since ID_Aula doesn't have AUTO_INCREMENT
            $query_id = "SELECT COALESCE(MAX(ID_Aula), 0) + 1 as next_id FROM aula";
            $stmt_id = $db->prepare($query_id);
            $stmt_id->execute();
            $result = $stmt_id->fetch(PDO::FETCH_ASSOC);
            $next_id = $result['next_id'];
            
            // Inserir nova aula
            $query = "INSERT INTO aula (ID_Aula, Dt_Hora, Descricao) VALUES (:id_aula, :dt_hora, :descricao)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id_aula', $next_id);
            $stmt->bindParam(':dt_hora', $dt_hora);
            $stmt->bindParam(':descricao', $descricao_completa);
            
            if ($stmt->execute()) {
                $aula_id = $next_id;
                
                // Associar ao instrutor especificado ou ao usuário logado se for instrutor
                $cref_instrutor = null;
                if (!empty($instrutor)) {
                    $cref_instrutor = $instrutor;
                } elseif ($_SESSION['user_type'] === 'instrutor') {
                    $cref_instrutor = $_SESSION['user_id'];
                }
                
                if ($cref_instrutor) {
                    $query_cria = "INSERT INTO cria (CREF_Instrutor, ID_Aula) VALUES (:cref, :aula_id)";
                    $stmt_cria = $db->prepare($query_cria);
                    $stmt_cria->bindParam(':cref', $cref_instrutor);
                    $stmt_cria->bindParam(':aula_id', $aula_id);
                    $stmt_cria->execute();
                }
                
                $_SESSION['success'] = "Aula criada com sucesso!";
                redirect('aula');
                return;
            } else {
                $_SESSION['error'] = "Erro ao criar aula.";
            }
            
        } catch (Exception $e) {
            error_log("Erro ao criar aula: " . $e->getMessage());
            $_SESSION['error'] = "Erro interno do servidor.";
        }
        
        redirect('aula/create');
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
