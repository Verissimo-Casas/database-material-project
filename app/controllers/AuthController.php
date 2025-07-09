<?php
// FILE: app/controllers/AuthController.php

require_once BASE_PATH . '/app/models/User.php';

class AuthController {
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = sanitizeInput($_POST['email']);
            $password = $_POST['password'];
            
            error_log("Login attempt for email: " . $email);
            
            $userModel = new User();
            $user = $userModel->authenticate($email, $password);
            
            if ($user) {
                error_log("User authenticated successfully: " . print_r($user, true));
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_type'] = $user['tipo'];
                $_SESSION['user_name'] = $user['nome'];
                
                // Check if aluno has active matricula and no overdue payments
                if ($user['tipo'] === 'aluno') {
                    $_SESSION['matricula_id'] = $user['ID_Matricula'];
                    
                    if (!isMatriculaActive($user['ID_Matricula'])) {
                        error_log("Matricula inactive for user: " . $user['id']);
                        $error = "Matrícula inativa. Entre em contato com a administração.";
                        session_destroy();
                    } elseif (hasOverduePayments($user['ID_Matricula'])) {
                        error_log("Overdue payments for user: " . $user['id']);
                        $error = "Existem mensalidades em atraso. Regularize sua situação para acessar o sistema.";
                        session_destroy();
                    } else {
                        error_log("Redirecting aluno to dashboard");
                        redirect('dashboard');
                    }
                } else {
                    error_log("Redirecting " . $user['tipo'] . " to dashboard");
                    redirect('dashboard');
                }
            } else {
                error_log("Authentication failed for email: " . $email);
                $error = "Email ou senha inválidos.";
            }
        }
        
        $csrf_token = generateCSRFToken();
        include BASE_PATH . '/app/views/auth/login.php';
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf_token = $_POST['csrf_token'] ?? '';
            
            if (!validateCSRF($csrf_token)) {
                $error = "Token de segurança inválido.";
            } else {
                $userModel = new User();
                
                $data = [
                    'cpf' => sanitizeInput($_POST['cpf']),
                    'nome' => sanitizeInput($_POST['nome']),
                    'dt_nasc' => $_POST['dt_nasc'],
                    'endereco' => sanitizeInput($_POST['endereco']),
                    'contato' => sanitizeInput($_POST['contato']),
                    'email' => sanitizeInput($_POST['email']),
                    'senha' => $_POST['senha'],
                    'matricula' => null
                ];
                
                if ($userModel->createAluno($data)) {
                    $success = "Cadastro realizado com sucesso! Aguarde a ativação da matrícula.";
                } else {
                    $error = "Erro ao realizar cadastro.";
                }
            }
        }
        
        $csrf_token = generateCSRFToken();
        include BASE_PATH . '/app/views/auth/register.php';
    }
    
    public function logout() {
        session_destroy();
        redirect('auth/login');
    }
}
?>
