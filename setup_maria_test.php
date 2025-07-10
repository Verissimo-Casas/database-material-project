<?php
// Quick test script to set up Maria's data and test financial report

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "=== Setting up Maria test data ===\n";
    
    // Check if Maria exists
    $check_maria = $db->prepare("SELECT COUNT(*) as count FROM aluno WHERE AL_Nome LIKE '%Maria%'");
    $check_maria->execute();
    $maria_count = $check_maria->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($maria_count == 0) {
        echo "Creating Maria Silva...\n";
        
        // Create matricula first
        $insert_matricula = $db->prepare("INSERT INTO matricula (M_Status, Data_Inicio, Data_Fim) VALUES (1, '2024-01-01', '2024-12-31')");
        $insert_matricula->execute();
        $matricula_id = $db->lastInsertId();
        
        // Create Maria
        $insert_maria = $db->prepare("INSERT INTO aluno (CPF, AL_Nome, AL_Email, AL_Num_Contato, AL_Endereco, Data_Nasc, ID_Matricula) VALUES ('12345678901', 'Maria Silva', 'maria@test.com', '11999999999', 'Rua Test, 123', '1990-05-15', :matricula_id)");
        $insert_maria->bindParam(':matricula_id', $matricula_id);
        $insert_maria->execute();
        
        echo "Maria created with matricula ID: $matricula_id\n";
    } else {
        echo "Maria already exists\n";
        // Get Maria's matricula ID
        $get_matricula = $db->prepare("SELECT m.ID_Matricula FROM aluno a INNER JOIN matricula m ON a.ID_Matricula = m.ID_Matricula WHERE AL_Nome LIKE '%Maria%' LIMIT 1");
        $get_matricula->execute();
        $matricula_id = $get_matricula->fetch(PDO::FETCH_ASSOC)['ID_Matricula'];
        echo "Maria's matricula ID: $matricula_id\n";
    }
    
    // Clear existing boletos for Maria to avoid duplicates
    $delete_boletos = $db->prepare("DELETE FROM boleto WHERE ID_Matricula = :matricula_id");
    $delete_boletos->bindParam(':matricula_id', $matricula_id);
    $delete_boletos->execute();
    echo "Cleared existing boletos for Maria\n";
    
    // Create test boletos with different dates and statuses
    $boletos = [
        ['Valor' => 100.00, 'Dt_Vencimento' => '2024-03-15', 'Dt_Pagamento' => '2024-03-10'], // Paid early
        ['Valor' => 100.00, 'Dt_Vencimento' => '2024-04-15', 'Dt_Pagamento' => '2024-04-15'], // Paid on time
        ['Valor' => 100.00, 'Dt_Vencimento' => '2024-05-15', 'Dt_Pagamento' => '2024-05-20'], // Paid late
        ['Valor' => 100.00, 'Dt_Vencimento' => '2024-06-15', 'Dt_Pagamento' => NULL], // Overdue
        ['Valor' => 100.00, 'Dt_Vencimento' => '2025-01-15', 'Dt_Pagamento' => '2025-01-10'], // Current year paid
        ['Valor' => 100.00, 'Dt_Vencimento' => '2025-02-15', 'Dt_Pagamento' => NULL], // Current year pending
        ['Valor' => 100.00, 'Dt_Vencimento' => '2025-07-15', 'Dt_Pagamento' => NULL], // Future date
    ];
    
    foreach ($boletos as $index => $boleto) {
        $insert_boleto = $db->prepare("INSERT INTO boleto (ID_Matricula, Valor, Dt_Vencimento, Dt_Pagamento) VALUES (:matricula_id, :valor, :dt_vencimento, :dt_pagamento)");
        $insert_boleto->bindParam(':matricula_id', $matricula_id);
        $insert_boleto->bindParam(':valor', $boleto['Valor']);
        $insert_boleto->bindParam(':dt_vencimento', $boleto['Dt_Vencimento']);
        $insert_boleto->bindParam(':dt_pagamento', $boleto['Dt_Pagamento']);
        $insert_boleto->execute();
        
        $boleto_id = $db->lastInsertId();
        echo "Created boleto #$boleto_id: R$ {$boleto['Valor']} due {$boleto['Dt_Vencimento']}" . 
             ($boleto['Dt_Pagamento'] ? " (paid {$boleto['Dt_Pagamento']})" : " (unpaid)") . "\n";
    }
    
    echo "\n=== Testing financial report queries ===\n";
    
    // Test monthly query for July 2025 (should be 1 boleto)
    $monthly_query = $db->prepare("SELECT COUNT(*) as total_boletos, SUM(Valor) as valor_total FROM boleto WHERE DATE_FORMAT(Dt_Vencimento, '%Y-%m') = '2025-07'");
    $monthly_query->execute();
    $monthly_result = $monthly_query->fetch(PDO::FETCH_ASSOC);
    echo "July 2025 boletos: {$monthly_result['total_boletos']} (R$ {$monthly_result['valor_total']})\n";
    
    // Test all boletos query
    $all_query = $db->prepare("SELECT COUNT(*) as total_boletos, SUM(Valor) as valor_total FROM boleto");
    $all_query->execute();
    $all_result = $all_query->fetch(PDO::FETCH_ASSOC);
    echo "All boletos: {$all_result['total_boletos']} (R$ {$all_result['valor_total']})\n";
    
    // Test Maria's boletos specifically
    $maria_query = $db->prepare("SELECT COUNT(*) as total_boletos, SUM(b.Valor) as valor_total FROM boleto b INNER JOIN matricula m ON b.ID_Matricula = m.ID_Matricula INNER JOIN aluno a ON m.ID_Matricula = a.ID_Matricula WHERE a.AL_Nome LIKE '%Maria%'");
    $maria_query->execute();
    $maria_result = $maria_query->fetch(PDO::FETCH_ASSOC);
    echo "Maria's boletos: {$maria_result['total_boletos']} (R$ {$maria_result['valor_total']})\n";
    
    echo "\n=== Test data setup complete! ===\n";
    echo "You can now test the financial report at /relatorio/financeiro\n";
    echo "- Use the monthly view for July 2025 (should show 1 boleto)\n";
    echo "- Check the 'Todos' checkbox to see all boletos (should show 7 boletos)\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
