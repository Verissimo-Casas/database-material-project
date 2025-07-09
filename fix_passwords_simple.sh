#!/bin/bash

# Script simplificado para corrigir senhas em texto plano
echo "🔒 Corrigindo BUG-002: Senhas em texto plano"
echo "============================================"

# Primeiro, vamos verificar se existem senhas em texto plano
echo "📊 Verificando senhas..."

# Executar comando SQL diretamente
docker exec academia_db mysql -u academia_user -pacademia_pass academiabd -e "
SELECT 'ALUNOS' as tipo, COUNT(*) as total_plain 
FROM aluno 
WHERE AL_Senha NOT LIKE '\$2y\$%' AND AL_Senha IS NOT NULL

UNION ALL

SELECT 'INSTRUTORES' as tipo, COUNT(*) as total_plain 
FROM instrutor 
WHERE L_Senha NOT LIKE '\$2y\$%' AND L_Senha IS NOT NULL

UNION ALL

SELECT 'ADMINS' as tipo, COUNT(*) as total_plain 
FROM administrador 
WHERE A_Senha NOT LIKE '\$2y\$%' AND A_Senha IS NOT NULL;
" 2>/dev/null

echo ""
echo "🔧 Executando correção via PHP..."

# Executar PHP diretamente no container
docker exec academia_app php -r "
require_once '/var/www/html/config/database.php';

\$database = new Database();
\$conn = \$database->getConnection();

echo \"Corrigindo senhas em texto plano...\n\";

// Corrigir alunos
\$stmt = \$conn->prepare(\"SELECT CPF, AL_Senha FROM aluno WHERE AL_Senha NOT LIKE '\$2y\$%' AND AL_Senha IS NOT NULL\");
\$stmt->execute();
\$alunos = \$stmt->fetchAll(PDO::FETCH_ASSOC);

foreach (\$alunos as \$aluno) {
    \$hashedPassword = password_hash(\$aluno['AL_Senha'], PASSWORD_DEFAULT);
    \$updateStmt = \$conn->prepare(\"UPDATE aluno SET AL_Senha = ? WHERE CPF = ?\");
    \$updateStmt->execute([\$hashedPassword, \$aluno['CPF']]);
    echo \"✅ Aluno CPF \" . \$aluno['CPF'] . \" corrigido\n\";
}

// Corrigir instrutores
\$stmt = \$conn->prepare(\"SELECT CREF, L_Senha FROM instrutor WHERE L_Senha NOT LIKE '\$2y\$%' AND L_Senha IS NOT NULL\");
\$stmt->execute();
\$instrutores = \$stmt->fetchAll(PDO::FETCH_ASSOC);

foreach (\$instrutores as \$instrutor) {
    \$hashedPassword = password_hash(\$instrutor['L_Senha'], PASSWORD_DEFAULT);
    \$updateStmt = \$conn->prepare(\"UPDATE instrutor SET L_Senha = ? WHERE CREF = ?\");
    \$updateStmt->execute([\$hashedPassword, \$instrutor['CREF']]);
    echo \"✅ Instrutor CREF \" . \$instrutor['CREF'] . \" corrigido\n\";
}

// Corrigir administradores
\$stmt = \$conn->prepare(\"SELECT ID_Admin, A_Senha FROM administrador WHERE A_Senha NOT LIKE '\$2y\$%' AND A_Senha IS NOT NULL\");
\$stmt->execute();
\$admins = \$stmt->fetchAll(PDO::FETCH_ASSOC);

foreach (\$admins as \$admin) {
    \$hashedPassword = password_hash(\$admin['A_Senha'], PASSWORD_DEFAULT);
    \$updateStmt = \$conn->prepare(\"UPDATE administrador SET A_Senha = ? WHERE ID_Admin = ?\");
    \$updateStmt->execute([\$hashedPassword, \$admin['ID_Admin']]);
    echo \"✅ Admin ID \" . \$admin['ID_Admin'] . \" corrigido\n\";
}

echo \"✅ Correção concluída!\n\";
"

echo ""
echo "✅ Verificando resultado..."

# Verificar novamente
RESULT=$(docker exec academia_db mysql -u academia_user -pacademia_pass academiabd -e "
SELECT 
    (SELECT COUNT(*) FROM aluno WHERE AL_Senha NOT LIKE '\$2y\$%' AND AL_Senha IS NOT NULL) +
    (SELECT COUNT(*) FROM instrutor WHERE L_Senha NOT LIKE '\$2y\$%' AND L_Senha IS NOT NULL) +
    (SELECT COUNT(*) FROM administrador WHERE A_Senha NOT LIKE '\$2y\$%' AND A_Senha IS NOT NULL) as total_plain;
" 2>/dev/null | tail -n 1)

if [ "$RESULT" = "0" ]; then
    echo "✅ SUCESSO: Todas as senhas foram hasheadas!"
    echo "🔒 BUG-002 CORRIGIDO"
else
    echo "❌ Ainda existem $RESULT senhas em texto plano"
fi
