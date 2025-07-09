<?php
// FILE: app/controllers/BoletoController.php

require_once BASE_PATH . '/app/models/Boleto.php';
require_once BASE_PATH . '/app/models/Matricula.php';

class BoletoController {
    
    public function __construct() {
        if (!isLoggedIn()) {
            redirect('auth/login');
        }
    }
    
    public function index() {
        $boletoModel = new Boleto();
        
        if (getUserType() === 'aluno') {
            $matricula_id = $_SESSION['matricula_id'];
            $boletos = $boletoModel->getByMatricula($matricula_id);
        } else {
            $boletos = $boletoModel->getAll();
        }
        
        include BASE_PATH . '/app/views/boleto/index.php';
    }
    
    public function create($matricula_id = null) {
        if (getUserType() !== 'administrador') {
            redirect('dashboard');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf_token = $_POST['csrf_token'] ?? '';
            
            if (!validateCSRF($csrf_token)) {
                $error = "Token de segurança inválido.";
            } else {
                $boletoModel = new Boleto();
                
                $next_id = $boletoModel->getNextId();
                
                $data = [
                    'id' => $next_id,
                    'forma' => sanitizeInput($_POST['forma_pagamento']),
                    'valor' => $_POST['valor'],
                    'vencimento' => $_POST['dt_vencimento'],
                    'matricula' => $_POST['id_matricula']
                ];
                
                if ($boletoModel->create($data)) {
                    $success = "Boleto gerado com sucesso!";
                } else {
                    $error = "Erro ao gerar boleto.";
                }
            }
        }
        
        // Get matriculas for dropdown
        $matriculaModel = new Matricula();
        $matriculas = $matriculaModel->getAll();
        
        $csrf_token = generateCSRFToken();
        include BASE_PATH . '/app/views/boleto/create.php';
    }
    
    public function markAsPaid($id) {
        if (getUserType() !== 'administrador') {
            redirect('dashboard');
        }
        
        $boletoModel = new Boleto();
        $boletoModel->markAsPaid($id, date('Y-m-d'));
        
        redirect('boleto');
    }
}
?>
