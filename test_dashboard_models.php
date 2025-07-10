<?php
// Test dashboard data loading
require_once 'config/config.php';
require_once 'app/models/Aula.php';
require_once 'app/models/Avaliacao.php';

try {
    echo "=== TESTING AULA MODEL ===\n";
    $aulaModel = new Aula();
    
    $proximasAulas = $aulaModel->getUpcoming(3);
    echo "Próximas aulas found: " . count($proximasAulas) . "\n";
    foreach ($proximasAulas as $aula) {
        echo "- {$aula['Descricao']} em {$aula['Dt_Hora']} ({$aula['total_alunos']} alunos)\n";
    }
    
    $aulasHoje = $aulaModel->getTodayClasses();
    echo "\nAulas hoje: $aulasHoje\n";
    
    echo "\n=== TESTING AVALIACAO MODEL ===\n";
    $avaliacaoModel = new Avaliacao();
    
    $avaliacoesRecentes = $avaliacaoModel->getRecent(3);
    echo "Avaliações recentes found: " . count($avaliacoesRecentes) . "\n";
    foreach ($avaliacoesRecentes as $av) {
        echo "- {$av['aluno_nome']} em {$av['Data_Av']} (IMC: {$av['IMC']})\n";
    }
    
    $avaliacoesPendentes = $avaliacaoModel->getPendingCount();
    echo "\nAvaliações pendentes: $avaliacoesPendentes\n";
    
    $alunosAtivos = $avaliacaoModel->getActiveStudentsCount();
    echo "Alunos ativos: $alunosAtivos\n";
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
