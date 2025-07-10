<?php
// Insert sample data for dashboard testing
require_once 'config/database.php';

try {
    $pdo = getConnection();
    
    echo "=== INSERTING SAMPLE AULAS ===\n";
    
    // Insert sample aulas
    $aulas = [
        [1, '2025-01-08 14:00:00', 'Musculação Iniciantes'],
        [2, '2025-01-08 18:00:00', 'CrossFit'],
        [3, '2025-01-09 07:00:00', 'Funcional'],
        [4, '2025-01-09 19:00:00', 'Zumba'],
        [5, '2025-01-10 08:00:00', 'Pilates']
    ];
    
    foreach ($aulas as $aula) {
        $stmt = $pdo->prepare("INSERT INTO aula (ID_Aula, Dt_Hora, Descricao) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE Dt_Hora = VALUES(Dt_Hora), Descricao = VALUES(Descricao)");
        $result = $stmt->execute($aula);
        echo "Aula '{$aula[2]}': " . ($result ? "OK" : "ERRO") . "\n";
    }
    
    echo "\n=== INSERTING SAMPLE AVALIACOES ===\n";
    
    // Insert sample avaliacoes
    $avaliacoes = [
        ['2025-01-07', 65.5, 1.65, 24.1],
        ['2025-01-06', 58.0, 1.60, 22.7],
        ['2025-01-05', 70.2, 1.75, 22.9],
        ['2025-01-04', 62.8, 1.58, 25.1],
        ['2025-01-03', 55.0, 1.62, 21.0]
    ];
    
    foreach ($avaliacoes as $av) {
        $stmt = $pdo->prepare("INSERT INTO avaliacao_fisica (Data_Av, Peso, Altura, IMC) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute($av);
        echo "Avaliação {$av[0]}: " . ($result ? "OK" : "ERRO") . "\n";
    }
    
    echo "\n=== LINKING AULAS TO INSTRUCTOR ===\n";
    
    // Get the instructor CREF
    $stmt = $pdo->query("SELECT CREF FROM instrutor LIMIT 1");
    $instrutor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($instrutor) {
        $cref = $instrutor['CREF'];
        echo "Using instructor CREF: $cref\n";
        
        // Link aulas to instructor
        for ($i = 1; $i <= 5; $i++) {
            $stmt = $pdo->prepare("INSERT INTO cria (CREF_Instrutor, ID_Aula) VALUES (?, ?) ON DUPLICATE KEY UPDATE CREF_Instrutor = VALUES(CREF_Instrutor)");
            $result = $stmt->execute([$cref, $i]);
            echo "Link aula $i: " . ($result ? "OK" : "ERRO") . "\n";
        }
    }
    
    echo "\n=== LINKING AVALIACOES TO INSTRUCTOR ===\n";
    
    if ($instrutor) {
        $cref = $instrutor['CREF'];
        
        // Get created avaliacoes
        $stmt = $pdo->query("SELECT ID_Avaliacao FROM avaliacao_fisica ORDER BY ID_Avaliacao DESC LIMIT 5");
        $avaliacoes_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($avaliacoes_ids as $av_id) {
            $stmt = $pdo->prepare("INSERT INTO constroi (CREF_j, ID_Avaliacao) VALUES (?, ?) ON DUPLICATE KEY UPDATE CREF_j = VALUES(CREF_j)");
            $result = $stmt->execute([$cref, $av_id]);
            echo "Link avaliacao $av_id: " . ($result ? "OK" : "ERRO") . "\n";
        }
    }
    
    echo "\n=== LINKING AVALIACOES TO STUDENTS ===\n";
    
    // Get the student CPF
    $stmt = $pdo->query("SELECT CPF, AL_Nome FROM aluno LIMIT 1");
    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($aluno) {
        $cpf = $aluno['CPF'];
        echo "Using student CPF: $cpf ({$aluno['AL_Nome']})\n";
        
        // Get created avaliacoes
        $stmt = $pdo->query("SELECT ID_Avaliacao FROM avaliacao_fisica ORDER BY ID_Avaliacao DESC LIMIT 3");
        $avaliacoes_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($avaliacoes_ids as $av_id) {
            $stmt = $pdo->prepare("INSERT INTO realiza (ID_Avaliacao, AL_CPF, Relatorio_Avaliacao) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE AL_CPF = VALUES(AL_CPF)");
            $result = $stmt->execute([$av_id, $cpf, "Avaliação realizada com sucesso"]);
            echo "Link realiza $av_id: " . ($result ? "OK" : "ERRO") . "\n";
        }
    }
    
    echo "\n=== ADDING STUDENTS TO CLASSES ===\n";
    
    if ($aluno) {
        $cpf = $aluno['CPF'];
        
        // Add student to classes (frequenta)
        for ($i = 1; $i <= 3; $i++) {
            $stmt = $pdo->prepare("INSERT INTO frequenta (ID_Aula, AL_CPF, Relatorio_Frequencia) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE AL_CPF = VALUES(AL_CPF)");
            $result = $stmt->execute([$i, $cpf, "Frequência regular"]);
            echo "Student in class $i: " . ($result ? "OK" : "ERRO") . "\n";
        }
    }
    
    echo "\n=== SAMPLE DATA INSERTION COMPLETED ===\n";
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>
