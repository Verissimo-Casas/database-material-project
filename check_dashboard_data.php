<?php
// Check dashboard data
require_once 'config/database.php';

try {
    $pdo = getConnection();
    echo "=== CHECKING AULAS TABLE ===\n";
    $stmt = $pdo->query('SELECT * FROM aula LIMIT 5');
    $aulas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo 'Aulas found: ' . count($aulas) . "\n";
    foreach($aulas as $aula) {
        print_r($aula);
    }
    
    echo "\n=== CHECKING AVALIACAO_FISICA TABLE ===\n";
    $stmt = $pdo->query('SELECT * FROM avaliacao_fisica LIMIT 5');
    $avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo 'Avaliacoes found: ' . count($avaliacoes) . "\n";
    foreach($avaliacoes as $av) {
        print_r($av);
    }
    
    echo "\n=== CHECKING ALUNOS TABLE ===\n";
    $stmt = $pdo->query('SELECT CPF, AL_Nome FROM aluno LIMIT 5');
    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo 'Alunos found: ' . count($alunos) . "\n";
    foreach($alunos as $aluno) {
        print_r($aluno);
    }
    
    echo "\n=== CHECKING INSTRUTOR TABLE ===\n";
    $stmt = $pdo->query('SELECT CREF, L_Nome FROM instrutor LIMIT 5');
    $instrutores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo 'Instrutores found: ' . count($instrutores) . "\n";
    foreach($instrutores as $inst) {
        print_r($inst);
    }
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>
