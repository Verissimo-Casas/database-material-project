#!/bin/bash

# ========================================================
# COMANDOS CURL ESPECÍFICOS - SISTEMA ACADEMIA
# Comandos prontos para testar cada endpoint manualmente
# ========================================================

BASE_URL="http://localhost:8080"
COOKIE_FILE="session.txt"

echo "🔧 SISTEMA ACADEMIA - COMANDOS CURL PARA TESTES MANUAIS"
echo "======================================================"

cat << 'EOF'

📋 INSTRUÇÕES DE USO:
1. Copie e cole os comandos abaixo no terminal
2. Modifique dados conforme necessário
3. Observe os códigos de status HTTP retornados
4. Use o arquivo session.txt para manter cookies entre chamadas

⚠️  IMPORTANTE: Execute os comandos na ordem para manter a sessão

EOF

echo ""
echo "🔓 TESTES DE AUTENTICAÇÃO"
echo "========================"

echo ""
echo "# 1. Página de Login"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -o login_page.html \\"
echo "     '$BASE_URL/auth/login'"

echo ""
echo "# 2. Login como Administrador"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -c '$COOKIE_FILE' \\"
echo "     -X POST \\"
echo "     -H 'Content-Type: application/x-www-form-urlencoded' \\"
echo "     -d 'email=admin@academia.com&password=password' \\"
echo "     -o login_admin_response.html \\"
echo "     '$BASE_URL/auth/login'"

echo ""
echo "# 3. Login como Instrutor"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -c '$COOKIE_FILE' \\"
echo "     -X POST \\"
echo "     -H 'Content-Type: application/x-www-form-urlencoded' \\"
echo "     -d 'email=joao@academia.com&password=password' \\"
echo "     -o login_instrutor_response.html \\"
echo "     '$BASE_URL/auth/login'"

echo ""
echo "# 4. Login como Aluno"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -c '$COOKIE_FILE' \\"
echo "     -X POST \\"
echo "     -H 'Content-Type: application/x-www-form-urlencoded' \\"
echo "     -d 'email=maria@email.com&password=password' \\"
echo "     -o login_aluno_response.html \\"
echo "     '$BASE_URL/auth/login'"

echo ""
echo "# 5. Login com Credenciais Inválidas"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -X POST \\"
echo "     -H 'Content-Type: application/x-www-form-urlencoded' \\"
echo "     -d 'email=invalid@email.com&password=wrongpassword' \\"
echo "     -o login_invalid_response.html \\"
echo "     '$BASE_URL/auth/login'"

echo ""
echo "# 6. Página de Registro"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -o register_page.html \\"
echo "     '$BASE_URL/auth/register'"

echo ""
echo "🏠 TESTES DE DASHBOARD"
echo "====================="

echo ""
echo "# 7. Dashboard sem Autenticação (deve redirecionar)"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -o dashboard_no_auth.html \\"
echo "     '$BASE_URL/dashboard'"

echo ""
echo "# 8. Dashboard com Autenticação (após login)"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -b '$COOKIE_FILE' \\"
echo "     -o dashboard_authenticated.html \\"
echo "     '$BASE_URL/dashboard'"

echo ""
echo "💰 TESTES DE BOLETOS"
echo "==================="

echo ""
echo "# 9. Lista de Boletos sem Autenticação"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -o boletos_no_auth.html \\"
echo "     '$BASE_URL/boleto'"

echo ""
echo "# 10. Lista de Boletos com Autenticação"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -b '$COOKIE_FILE' \\"
echo "     -o boletos_list.html \\"
echo "     '$BASE_URL/boleto'"

echo ""
echo "# 11. Formulário de Criação de Boleto (Admin apenas)"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -b '$COOKIE_FILE' \\"
echo "     -o boleto_create_form.html \\"
echo "     '$BASE_URL/boleto/create'"

echo ""
echo "# 12. Criar Boleto com Dados Válidos"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -b '$COOKIE_FILE' \\"
echo "     -X POST \\"
echo "     -H 'Content-Type: application/x-www-form-urlencoded' \\"
echo "     -d 'forma_pagamento=Boleto&valor=75.00&dt_vencimento=2025-09-08&id_matricula=1' \\"
echo "     -o boleto_create_result.html \\"
echo "     '$BASE_URL/boleto/create'"

echo ""
echo "# 13. Criar Boleto para Matrícula Específica"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -b '$COOKIE_FILE' \\"
echo "     -o boleto_create_specific.html \\"
echo "     '$BASE_URL/boleto/create/1'"

echo ""
echo "# 14. Marcar Boleto como Pago"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -b '$COOKIE_FILE' \\"
echo "     -X POST \\"
echo "     -o boleto_mark_paid.html \\"
echo "     '$BASE_URL/boleto/markAsPaid/1'"

echo ""
echo "🎓 TESTES DE MATRÍCULAS"
echo "======================"

echo ""
echo "# 15. Lista de Matrículas sem Autenticação"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -o matriculas_no_auth.html \\"
echo "     '$BASE_URL/matricula'"

echo ""
echo "# 16. Lista de Matrículas (Admin apenas)"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -b '$COOKIE_FILE' \\"
echo "     -o matriculas_list.html \\"
echo "     '$BASE_URL/matricula'"

echo ""
echo "# 17. Formulário de Criação de Matrícula"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -b '$COOKIE_FILE' \\"
echo "     -o matricula_create_form.html \\"
echo "     '$BASE_URL/matricula/create'"

echo ""
echo "# 18. Criar Nova Matrícula"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -b '$COOKIE_FILE' \\"
echo "     -X POST \\"
echo "     -H 'Content-Type: application/x-www-form-urlencoded' \\"
echo "     -d 'cpf=11111111111&nome=Teste%20Usuario&dt_nasc=1992-03-15&endereco=Rua%20Teste%20123&contato=11999888777&email=teste@usuario.com&senha=password123' \\"
echo "     -o matricula_create_result.html \\"
echo "     '$BASE_URL/matricula/create'"

echo ""
echo "# 19. Alternar Status de Matrícula"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -b '$COOKIE_FILE' \\"
echo "     -X POST \\"
echo "     -o matricula_toggle_status.html \\"
echo "     '$BASE_URL/matricula/toggleStatus/1'"

echo ""
echo "🔄 TESTES DE REGISTRO"
echo "===================="

echo ""
echo "# 20. Registrar Novo Usuário"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -X POST \\"
echo "     -H 'Content-Type: application/x-www-form-urlencoded' \\"
echo "     -d 'cpf=22222222222&nome=Novo%20Usuario&dt_nasc=1988-07-20&endereco=Avenida%20Nova%20456&contato=11777666555&email=novo@email.com&senha=novasenha123' \\"
echo "     -o register_new_user.html \\"
echo "     '$BASE_URL/auth/register'"

echo ""
echo "🚪 LOGOUT"
echo "========="

echo ""
echo "# 21. Fazer Logout"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -b '$COOKIE_FILE' \\"
echo "     -o logout_response.html \\"
echo "     '$BASE_URL/auth/logout'"

echo ""
echo "🧹 LIMPEZA"
echo "=========="

echo ""
echo "# 22. Limpar Arquivos de Teste"
echo "rm -f *.html $COOKIE_FILE"

echo ""
echo "📊 COMANDOS DE VERIFICAÇÃO"
echo "========================="

echo ""
echo "# Verificar Status HTTP de qualquer endpoint"
echo "curl -s -I '$BASE_URL/qualquer/rota' | head -1"

echo ""
echo "# Verificar Cookies de Sessão"
echo "cat '$COOKIE_FILE'"

echo ""
echo "# Verificar Headers de Resposta"
echo "curl -s -I -b '$COOKIE_FILE' '$BASE_URL/dashboard'"

echo ""
echo "# Verificar Redirecionamentos"
echo "curl -s -w 'Status: %{http_code}\\nRedirect: %{redirect_url}\\n' -o /dev/null '$BASE_URL/'"

echo ""
echo "🔍 TESTES DE SEGURANÇA"
echo "====================="

echo ""
echo "# Teste de Injeção SQL (deve falhar)"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -X POST \\"
echo "     -H 'Content-Type: application/x-www-form-urlencoded' \\"
echo "     -d \"email=admin@academia.com' OR '1'='1&password=anything\" \\"
echo "     -o sql_injection_test.html \\"
echo "     '$BASE_URL/auth/login'"

echo ""
echo "# Teste de XSS (deve ser sanitizado)"
echo "curl -s -w 'Status: %{http_code}\n' \\"
echo "     -X POST \\"
echo "     -H 'Content-Type: application/x-www-form-urlencoded' \\"
echo "     -d 'email=<script>alert(\"xss\")</script>&password=test' \\"
echo "     -o xss_test.html \\"
echo "     '$BASE_URL/auth/login'"

echo ""
echo "⚡ EXEMPLO DE SEQUÊNCIA COMPLETA"
echo "==============================="

cat << 'EOF'

# Sequência completa de teste para um fluxo típico:

# 1. Fazer login como admin
curl -s -c session.txt -X POST \
     -H "Content-Type: application/x-www-form-urlencoded" \
     -d "email=admin@academia.com&password=password" \
     http://localhost:8080/auth/login

# 2. Acessar dashboard
curl -s -b session.txt http://localhost:8080/dashboard

# 3. Ver lista de boletos
curl -s -b session.txt http://localhost:8080/boleto

# 4. Criar novo boleto
curl -s -b session.txt -X POST \
     -H "Content-Type: application/x-www-form-urlencoded" \
     -d "forma_pagamento=PIX&valor=100.00&dt_vencimento=2025-12-31&id_matricula=1" \
     http://localhost:8080/boleto/create

# 5. Fazer logout
curl -s -b session.txt http://localhost:8080/auth/logout

# 6. Limpar
rm -f session.txt

EOF

echo ""
echo "✅ Todos os comandos estão prontos para uso!"
echo "💡 Dica: Salve este arquivo como curl_commands.txt para referência"
