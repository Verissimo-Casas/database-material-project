#!/bin/bash

# Script para identificar e corrigir senhas em texto plano no banco de dados
# BUG-002 FIX: Hash de senhas em texto plano

echo "🔒 CORREÇÃO BUG-002: Hash de Senhas em Texto Plano"
echo "=================================================="

# Função para conectar ao banco e executar consultas
execute_mysql() {
    docker exec academia_db mysql -u academia_user -pacademia_pass academiabd -e "$1"
}

echo ""
echo "📊 1. Verificando senhas em texto plano..."

# Verificar senhas em texto plano (não começam com $2y$ que é o padrão do password_hash)
echo "Alunos com senhas em texto plano:"
execute_mysql "SELECT CPF, AL_Nome, AL_Email, AL_Senha FROM aluno WHERE AL_Senha NOT LIKE '\$2y\$%' AND AL_Senha IS NOT NULL;"

echo ""
echo "Instrutores com senhas em texto plano:"
execute_mysql "SELECT CREF, L_Nome, L_Email, L_Senha FROM instrutor WHERE L_Senha NOT LIKE '\$2y\$%' AND L_Senha IS NOT NULL;"

echo ""
echo "Administradores com senhas em texto plano:"
execute_mysql "SELECT ID_Admin, A_Nome, A_Email, A_Senha FROM administrador WHERE A_Senha NOT LIKE '\$2y\$%' AND A_Senha IS NOT NULL;"

echo ""
echo "🔧 2. Corrigindo senhas em texto plano..."

# Criar script PHP temporário para rehash das senhas
cat > /tmp/fix_passwords.php << 'EOF'
<?php
require_once '/var/www/html/config/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "Corrigindo senhas em texto plano...\n";

// Corrigir senhas de alunos
$query = "SELECT CPF, AL_Senha FROM aluno WHERE AL_Senha NOT LIKE '$2y$%' AND AL_Senha IS NOT NULL";
$stmt = $conn->prepare($query);
$stmt->execute();
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($alunos as $aluno) {
    $hashedPassword = password_hash($aluno['AL_Senha'], PASSWORD_DEFAULT);
    $updateQuery = "UPDATE aluno SET AL_Senha = :senha WHERE CPF = :cpf";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':senha', $hashedPassword);
    $updateStmt->bindParam(':cpf', $aluno['CPF']);
    $updateStmt->execute();
    echo "✅ Senha do aluno CPF " . $aluno['CPF'] . " corrigida\n";
}

// Corrigir senhas de instrutores
$query = "SELECT CREF, L_Senha FROM instrutor WHERE L_Senha NOT LIKE '$2y$%' AND L_Senha IS NOT NULL";
$stmt = $conn->prepare($query);
$stmt->execute();
$instrutores = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($instrutores as $instrutor) {
    $hashedPassword = password_hash($instrutor['L_Senha'], PASSWORD_DEFAULT);
    $updateQuery = "UPDATE instrutor SET L_Senha = :senha WHERE CREF = :cref";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':senha', $hashedPassword);
    $updateStmt->bindParam(':cref', $instrutor['CREF']);
    $updateStmt->execute();
    echo "✅ Senha do instrutor CREF " . $instrutor['CREF'] . " corrigida\n";
}

// Corrigir senhas de administradores
$query = "SELECT ID_Admin, A_Senha FROM administrador WHERE A_Senha NOT LIKE '$2y$%' AND A_Senha IS NOT NULL";
$stmt = $conn->prepare($query);
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($admins as $admin) {
    $hashedPassword = password_hash($admin['A_Senha'], PASSWORD_DEFAULT);
    $updateQuery = "UPDATE administrador SET A_Senha = :senha WHERE ID_Admin = :id";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':senha', $hashedPassword);
    $updateStmt->bindParam(':id', $admin['ID_Admin']);
    $updateStmt->execute();
    echo "✅ Senha do administrador ID " . $admin['ID_Admin'] . " corrigida\n";
}

echo "✅ Todas as senhas foram corrigidas!\n";
?>
EOF

# Executar o script de correção
echo "Executando correção de senhas..."
docker exec academia_app php /tmp/fix_passwords.php

echo ""
echo "✅ 3. Verificando correção..."

# Verificar se ainda existem senhas em texto plano
PLAIN_PASSWORDS=$(execute_mysql "
SELECT 
    (SELECT COUNT(*) FROM aluno WHERE AL_Senha NOT LIKE '\$2y\$%' AND AL_Senha IS NOT NULL) +
    (SELECT COUNT(*) FROM instrutor WHERE L_Senha NOT LIKE '\$2y\$%' AND L_Senha IS NOT NULL) +
    (SELECT COUNT(*) FROM administrador WHERE A_Senha NOT LIKE '\$2y\$%' AND A_Senha IS NOT NULL) as total_plain;
" | grep -v "Warning" | tail -n 1)

if [ "$PLAIN_PASSWORDS" = "0" ]; then
    echo "✅ SUCESSO: Todas as senhas foram hasheadas corretamente!"
else
    echo "❌ ERRO: Ainda existem $PLAIN_PASSWORDS senhas em texto plano"
    exit 1
fi

echo ""
echo "🔒 BUG-002 CORRIGIDO COM SUCESSO!"
echo "Todas as senhas agora estão hasheadas usando password_hash() do PHP."
