<?php
// Insert sample data with future dates for próximas aulas
require_once 'config/config.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "=== INSERTING SAMPLE AULAS WITH FUTURE DATES ===\n\n";
    
    // Delete existing aulas to start fresh
    $conn->exec("DELETE FROM frequenta");
    $conn->exec("DELETE FROM cria");
    $conn->exec("DELETE FROM aula");
    echo "Cleaned existing data\n";
    
    // Insert aulas with future dates (based on current date July 9, 2025)
    $aulas = [
        [1, '2025-07-09 14:00:00', 'Musculação Iniciantes'],
        [2, '2025-07-09 18:00:00', 'CrossFit'],
        [3, '2025-07-10 07:00:00', 'Funcional'],
        [4, '2025-07-10 19:00:00', 'Zumba'],
        [5, '2025-07-11 08:00:00', 'Pilates'],
        [6, '2025-07-11 16:00:00', 'Yoga'],
        [7, '2025-07-12 09:00:00', 'HIIT']
    ];
    
    foreach ($aulas as $aula) {
        $stmt = $conn->prepare("INSERT INTO aula (ID_Aula, Dt_Hora, Descricao) VALUES (?, ?, ?)");
        $result = $stmt->execute($aula);
        echo "Aula '{$aula[2]}' em {$aula[1]}: " . ($result ? "OK" : "ERRO") . "\n";
    }
    
    echo "\n=== LINKING AULAS TO INSTRUCTOR ===\n";
    
    // Get instructor CREF
    $stmt = $conn->query("SELECT CREF FROM instrutor LIMIT 1");
    $instrutor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($instrutor) {
        $cref = $instrutor['CREF'];
        echo "Using instructor CREF: $cref\n";
        
        // Link all aulas to instructor
        for ($i = 1; $i <= 7; $i++) {
            $stmt = $conn->prepare("INSERT INTO cria (CREF_Instrutor, ID_Aula) VALUES (?, ?)");
            $result = $stmt->execute([$cref, $i]);
            echo "Link aula $i: " . ($result ? "OK" : "ERRO") . "\n";
        }
    }
    
    echo "\n=== ADDING STUDENTS TO CLASSES ===\n";
    
    // Get student CPF
    $stmt = $conn->query("SELECT CPF FROM aluno LIMIT 1");
    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($aluno) {
        $cpf = $aluno['CPF'];
        echo "Using student CPF: $cpf\n";
        
        // Add student to some classes
        $studentCounts = [12, 8, 15, 10, 6, 14, 9]; // Different student counts per class
        for ($i = 1; $i <= 7; $i++) {
            for ($j = 1; $j <= $studentCounts[$i-1]; $j++) {
                $stmt = $conn->prepare("INSERT IGNORE INTO frequenta (ID_Aula, AL_CPF, Relatorio_Frequencia) VALUES (?, ?, ?)");
                $result = $stmt->execute([$i, $cpf, "Estudante $j"]);
            }
            echo "Added {$studentCounts[$i-1]} students to class $i\n";
        }
    }
    
    echo "\n=== SAMPLE DATA INSERTION COMPLETED ===\n";
    echo "Now test the dashboard at: http://localhost:8080/dashboard\n";
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>
