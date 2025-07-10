<?php
// FILE: app/controllers/DashboardController.php

require_once BASE_PATH . '/app/models/Matricula.php';
require_once BASE_PATH . '/app/models/Boleto.php';
require_once BASE_PATH . '/app/models/Aula.php';
require_once BASE_PATH . '/app/models/Avaliacao.php';

class DashboardController {
    
    public function __construct() {
        if (!isLoggedIn()) {
            redirect('auth/login');
        }
    }
    
    public function index() {
        $userType = getUserType();
        $userName = $_SESSION['user_name'];
        
        // Get specific data based on user type
        switch ($userType) {
            case 'aluno':
                $this->alunoIndex();
                break;
            case 'instrutor':
                $this->instrutorIndex();
                break;
            case 'administrador':
                $this->adminIndex();
                break;
            default:
                redirect('auth/logout');
        }
    }
    
    private function alunoIndex() {
        $matriculaModel = new Matricula();
        $boletoModel = new Boleto();
        
        $matricula_id = $_SESSION['matricula_id'];
        $matricula = $matriculaModel->getById($matricula_id);
        $boletos = $boletoModel->getByMatricula($matricula_id);
        
        include BASE_PATH . '/app/views/dashboard/aluno.php';
    }
    
    private function instrutorIndex() {
        // Get real data for instructor dashboard
        $aulaModel = new Aula();
        $avaliacaoModel = new Avaliacao();
        
        // Get upcoming classes (all classes, not filtered by instructor yet due to relationship complexity)
        $proximasAulas = $aulaModel->getUpcoming(3);
        $aulasHoje = $aulaModel->getTodayClasses();
        
        // Get recent evaluations
        $avaliacoesRecentes = $avaliacaoModel->getRecent(3);
        $avaliacoesPendentes = $avaliacaoModel->getPendingCount();
        $alunosAtivos = $avaliacaoModel->getActiveStudentsCount();
        
        include BASE_PATH . '/app/views/dashboard/instrutor.php';
    }
    
    private function adminIndex() {
        $matriculaModel = new Matricula();
        $boletoModel = new Boleto();
        
        $matriculas = $matriculaModel->getAll();
        $boletos_vencidos = $boletoModel->getOverdue();
        
        include BASE_PATH . '/app/views/dashboard/admin.php';
    }
}
?>
