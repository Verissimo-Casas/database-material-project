<?php
// FILE: app/controllers/MatriculaController.php

require_once BASE_PATH . '/app/models/Matricula.php';
require_once BASE_PATH . '/app/models/User.php';

class MatriculaController {
    
    public function __construct() {
        if (!isLoggedIn() || getUserType() !== 'administrador') {
            redirect('auth/login');
        }
    }
    
    public function index() {
        $matriculaModel = new Matricula();
        $matriculas = $matriculaModel->getAll();
        
        include BASE_PATH . '/app/views/matricula/index.php';
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf_token = $_POST['csrf_token'] ?? '';
            
            if (!validateCSRF($csrf_token)) {
                $error = "Token de segurança inválido.";
            } else {
                $matriculaModel = new Matricula();
                $userModel = new User();
                
                // Create matricula first
                $next_id = $matriculaModel->getNextId();
                $dt_fim = date('Y-m-d H:i:s', strtotime('+1 year'));
                
                $matriculaData = [
                    'id' => $next_id,
                    'status' => 1,
                    'dt_inicio' => date('Y-m-d H:i:s'),
                    'dt_fim' => $dt_fim
                ];
                
                if ($matriculaModel->create($matriculaData)) {
                    // Create aluno with matricula
                    $alunoData = [
                        'cpf' => sanitizeInput($_POST['cpf']),
                        'nome' => sanitizeInput($_POST['nome']),
                        'dt_nasc' => $_POST['dt_nasc'],
                        'endereco' => sanitizeInput($_POST['endereco']),
                        'contato' => sanitizeInput($_POST['contato']),
                        'email' => sanitizeInput($_POST['email']),
                        'senha' => $_POST['senha'],
                        'matricula' => $next_id
                    ];
                    
                    if ($userModel->createAluno($alunoData)) {
                        $success = "Matrícula criada com sucesso!";
                    } else {
                        $error = "Erro ao criar aluno.";
                    }
                } else {
                    $error = "Erro ao criar matrícula.";
                }
            }
        }
        
        $csrf_token = generateCSRFToken();
        include BASE_PATH . '/app/views/matricula/create.php';
    }
    
    public function toggleStatus($id) {
        $matriculaModel = new Matricula();
        $matricula = $matriculaModel->getById($id);
        
        if ($matricula) {
            $newStatus = $matricula['M_Status'] == 1 ? 0 : 1;
            $matriculaModel->updateStatus($id, $newStatus);
        }
        
        redirect('matricula');
    }
}
?>
