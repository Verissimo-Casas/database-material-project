<?php
require_once '/var/www/html/config/config.php';
require_once '/var/www/html/config/database.php';

$database = new Database();
$db = $database->getConnection();

// Insert test matricula
$db->exec("INSERT IGNORE INTO matricula (ID_Matricula, M_Status, Data_Inicio, Data_Fim) VALUES (999, 1, '2024-01-01', '2024-12-31')");

// Insert test aluno
$db->exec("INSERT IGNORE INTO aluno (CPF, AL_Nome, AL_Email, AL_Num_Contato, AL_Endereco, Data_Nasc, ID_Matricula) VALUES ('99999999999', 'Maria Silva Teste', 'maria.teste@test.com', '11999999999', 'Rua Teste, 123', '1990-05-15', 999)");

// Insert test boletos
$boletos = [
    "INSERT IGNORE INTO boleto (ID_Pagamento, ID_Matricula, Valor, Dt_Vencimento, Dt_Pagamento) VALUES (9001, 999, 100.00, '2024-03-15', '2024-03-10')",
    "INSERT IGNORE INTO boleto (ID_Pagamento, ID_Matricula, Valor, Dt_Vencimento, Dt_Pagamento) VALUES (9002, 999, 100.00, '2024-04-15', '2024-04-15')",
    "INSERT IGNORE INTO boleto (ID_Pagamento, ID_Matricula, Valor, Dt_Vencimento, Dt_Pagamento) VALUES (9003, 999, 100.00, '2025-01-15', '2025-01-10')",
    "INSERT IGNORE INTO boleto (ID_Pagamento, ID_Matricula, Valor, Dt_Vencimento, Dt_Pagamento) VALUES (9004, 999, 100.00, '2025-07-15', NULL)",
    "INSERT IGNORE INTO boleto (ID_Pagamento, ID_Matricula, Valor, Dt_Vencimento, Dt_Pagamento) VALUES (9005, 999, 100.00, '2025-08-15', NULL)"
];

foreach ($boletos as $sql) {
    $db->exec($sql);
}

echo "Test data inserted successfully!";
?>
