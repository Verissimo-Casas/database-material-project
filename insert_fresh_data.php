<?php
// Insert data with explicit future dates
require_once 'config/config.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "<h3>Cleaning and Inserting Fresh Data</h3>";
    
    // Clean existing data
    $conn->exec("DELETE FROM frequenta WHERE 1=1");
    $conn->exec("DELETE FROM cria WHERE 1=1");
    $conn->exec("DELETE FROM aula WHERE 1=1");
    echo "✅ Cleaned existing data<br>";
    
    // Insert aulas with dates well into the future
    $futureAulas = [
        [1, '2025-07-15 14:00:00', 'Musculação Iniciantes'],
        [2, '2025-07-15 18:00:00', 'CrossFit'],
        [3, '2025-07-16 07:00:00', 'Funcional'],
        [4, '2025-07-16 19:00:00', 'Zumba'],
        [5, '2025-07-17 08:00:00', 'Pilates']
    ];
    
    foreach ($futureAulas as $aula) {
        $stmt = $conn->prepare("INSERT INTO aula (ID_Aula, Dt_Hora, Descricao) VALUES (?, ?, ?)");
        $result = $stmt->execute($aula);
        echo ($result ? "✅" : "❌") . " Inserted: {$aula[2]} on {$aula[1]}<br>";
    }
    
    echo "<br><h3>Linking to Instructor</h3>";
    
    // Get instructor
    $stmt = $conn->query("SELECT CREF, L_Nome FROM instrutor LIMIT 1");
    $instrutor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($instrutor) {
        echo "Found instructor: {$instrutor['L_Nome']} (CREF: {$instrutor['CREF']})<br>";
        
        // Link aulas to instructor
        for ($i = 1; $i <= 5; $i++) {
            $stmt = $conn->prepare("INSERT INTO cria (CREF_Instrutor, ID_Aula) VALUES (?, ?)");
            $result = $stmt->execute([$instrutor['CREF'], $i]);
            echo ($result ? "✅" : "❌") . " Linked aula $i to instructor<br>";
        }
    } else {
        echo "❌ No instructor found!<br>";
    }
    
    echo "<br><h3>Adding Students</h3>";
    
    // Get a student
    $stmt = $conn->query("SELECT CPF, AL_Nome FROM aluno LIMIT 1");
    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($aluno) {
        echo "Found student: {$aluno['AL_Nome']} (CPF: {$aluno['CPF']})<br>";
        
        // Add students to classes
        $studentCounts = [10, 8, 12, 6, 9];
        for ($i = 1; $i <= 5; $i++) {
            for ($j = 1; $j <= $studentCounts[$i-1]; $j++) {
                $stmt = $conn->prepare("INSERT INTO frequenta (ID_Aula, AL_CPF, Relatorio_Frequencia) VALUES (?, ?, ?)");
                $stmt->execute([$i, $aluno['CPF'], "Student $j enrolled"]);
            }
            echo "✅ Added {$studentCounts[$i-1]} students to aula $i<br>";
        }
    } else {
        echo "❌ No student found!<br>";
    }
    
    echo "<br><h3>Verification</h3>";
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM aula WHERE Dt_Hora >= NOW()");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Future aulas: {$count['count']}<br>";
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM cria");
    $criaCount = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Instructor links: {$criaCount['count']}<br>";
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM frequenta");
    $freqCount = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Student enrollments: {$freqCount['count']}<br>";
    
    echo "<br>✅ <strong>Data insertion completed!</strong><br>";
    echo '<a href="/dashboard">Go to Dashboard</a> | <a href="/simple_debug.php">Check Debug</a>';
    
} catch (Exception $e) {
    echo '❌ Error: ' . $e->getMessage() . "<br>";
}
?>
