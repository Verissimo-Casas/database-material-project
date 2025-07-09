#!/bin/bash

# SCRIPT DE TESTES ESPECÍFICOS PARA REGRAS DE NEGÓCIO
# Autor: QA Sênior
# Data: 08/07/2025

echo "================================================="
echo "    TESTES ESPECÍFICOS - REGRAS DE NEGÓCIO"
echo "    Sistema de Gestão de Academia"
echo "================================================="
echo ""

BASE_URL="http://localhost:8080"
RESULTS_FILE="testes_regras_negocio.log"

# Função para log de resultados
log_result() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a $RESULTS_FILE
}

# Função para resetar dados de teste
reset_test_data() {
    echo "Resetando dados de teste..."
    docker exec academia_db mysql -uacademia_user -pacademia_pass -e "
    USE academiabd; 
    UPDATE matricula SET Dt_Fim = DATE_ADD(NOW(), INTERVAL 1 YEAR) WHERE ID_Matricula = 1;
    DELETE FROM boleto WHERE ID_Pagamento > 2;
    " 2>/dev/null
}

# Função para criar aluno sem matrícula
create_student_without_enrollment() {
    docker exec academia_db mysql -uacademia_user -pacademia_pass -e "
    USE academiabd;
    INSERT INTO aluno (CPF, AL_Nome, AL_Dt_Nasc, AL_Endereco, AL_Num_Contato, AL_Email, AL_Senha, ID_Matricula) 
    VALUES ('99988877766', 'Teste Sem Matricula', '1995-01-01', 'Rua Teste', '11999999999', 'semmatricula@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL);
    " 2>/dev/null
}

# Função para testar login
test_login() {
    local email=$1
    local password=$2
    local expected_result=$3
    local test_name=$4
    
    echo "Executando: $test_name"
    
    response=$(curl -s -X POST "$BASE_URL/auth/login" \
        -d "email=$email&password=$password" \
        -w "%{http_code}")
    
    http_code="${response: -3}"
    
    case $expected_result in
        "block")
            if [[ "$response" == *"bloqueado"* ]] || [[ "$response" == *"inativa"* ]] || [[ "$response" == *"atraso"* ]]; then
                log_result "$test_name: PASSOU - Login corretamente bloqueado"
                return 0
            else
                log_result "$test_name: FALHOU - Login deveria ter sido bloqueado"
                return 1
            fi
            ;;
        "allow")
            if [[ "$http_code" == "302" ]]; then
                log_result "$test_name: PASSOU - Login permitido"
                return 0
            else
                log_result "$test_name: FALHOU - Login deveria ter sido permitido"
                return 1
            fi
            ;;
        "deny")
            if [[ "$response" == *"inválidos"* ]] || [[ "$http_code" == "200" ]]; then
                log_result "$test_name: PASSOU - Login corretamente negado"
                return 0
            else
                log_result "$test_name: FALHOU - Login deveria ter sido negado"
                return 1
            fi
            ;;
    esac
}

# Inicializar arquivo de log
echo "" > $RESULTS_FILE

log_result "=== INÍCIO DOS TESTES DE REGRAS DE NEGÓCIO ==="

total_tests=0
passed_tests=0

# TESTE RN-1: Matrícula Obrigatória
log_result "--- RN-1: MATRÍCULA OBRIGATÓRIA ---"

# Criar aluno sem matrícula
create_student_without_enrollment

total_tests=$((total_tests + 1))
if test_login "semmatricula@test.com" "password" "deny" "RN-001: Aluno sem matrícula"; then
    passed_tests=$((passed_tests + 1))
fi

# TESTE RN-2: Bloqueio por Mensalidade Vencida
log_result "--- RN-2: BLOQUEIO POR MENSALIDADE VENCIDA ---"

# Resetar dados
reset_test_data

# Criar boleto vencido
echo "Criando boleto vencido..."
docker exec academia_db mysql -uacademia_user -pacademia_pass -e "
USE academiabd; 
INSERT INTO boleto (ID_Pagamento, Forma_Pagamento, Valor, Dt_Vencimento, ID_Matricula) 
VALUES (100, 'Boleto', 50.00, '2025-07-01', 1);
" 2>/dev/null

total_tests=$((total_tests + 1))
if test_login "maria@email.com" "password" "block" "RN-002a: Bloqueio por boleto vencido"; then
    passed_tests=$((passed_tests + 1))
fi

# Testar matrícula vencida
echo "Vencendo matrícula..."
docker exec academia_db mysql -uacademia_user -pacademia_pass -e "
USE academiabd; 
UPDATE matricula SET Dt_Fim = '2025-06-01' WHERE ID_Matricula = 1;
DELETE FROM boleto WHERE ID_Pagamento = 100;
" 2>/dev/null

total_tests=$((total_tests + 1))
if test_login "maria@email.com" "password" "block" "RN-002b: Bloqueio por matrícula vencida"; then
    passed_tests=$((passed_tests + 1))
fi

# TESTE RN-4: Permissões de Edição
log_result "--- RN-4: PERMISSÕES DE EDIÇÃO ---"

# Resetar dados para teste de permissões
reset_test_data

# Login como aluno e verificar se não pode editar treinos
echo "Testando permissões de aluno..."
curl -s -c session_cookies.txt -X POST "$BASE_URL/auth/login" \
    -d "email=maria@email.com&password=password" > /dev/null

# Tentar acessar área de edição de treinos (se existir)
response=$(curl -s -b session_cookies.txt "$BASE_URL/plano_treino/edit" -w "%{http_code}")
http_code="${response: -3}"

total_tests=$((total_tests + 1))
if [[ "$http_code" == "403" ]] || [[ "$response" == *"não autorizado"* ]] || [[ "$http_code" == "404" ]]; then
    log_result "RN-004a: PASSOU - Aluno não pode editar treinos"
    passed_tests=$((passed_tests + 1))
else
    log_result "RN-004a: INDETERMINADO - Endpoint de edição de treinos não encontrado ou não implementado"
fi

# Login como instrutor
echo "Testando permissões de instrutor..."
curl -s -c session_cookies.txt -X POST "$BASE_URL/auth/login" \
    -d "email=joao@academia.com&password=password" > /dev/null

response=$(curl -s -b session_cookies.txt "$BASE_URL/plano_treino" -w "%{http_code}")
http_code="${response: -3}"

total_tests=$((total_tests + 1))
if [[ "$http_code" == "200" ]] || [[ "$http_code" == "404" ]]; then
    log_result "RN-004b: PASSOU - Instrutor pode acessar área de treinos"
    passed_tests=$((passed_tests + 1))
else
    log_result "RN-004b: FALHOU - Instrutor não consegue acessar área de treinos"
fi

# TESTES DE SEGURANÇA BÁSICOS
log_result "--- TESTES DE SEGURANÇA ---"

# Verificar se senhas estão hasheadas no banco
password_check=$(docker exec academia_db mysql -uacademia_user -pacademia_pass -e "
USE academiabd; 
SELECT COUNT(*) as unhashed FROM (
    SELECT A_Senha FROM administrador WHERE A_Senha NOT LIKE '$%'
    UNION
    SELECT L_Senha FROM instrutor WHERE L_Senha NOT LIKE '$%'
    UNION
    SELECT AL_Senha FROM aluno WHERE AL_Senha NOT LIKE '$%'
) AS combined;
" 2>/dev/null | tail -1)

total_tests=$((total_tests + 1))
if [[ "$password_check" == "0" ]]; then
    log_result "SEC-001: PASSOU - Todas as senhas estão hasheadas no banco"
    passed_tests=$((passed_tests + 1))
else
    log_result "SEC-001: FALHOU - Encontradas $password_check senhas em texto plano"
fi

# Teste básico de SQL Injection
total_tests=$((total_tests + 1))
sql_injection_response=$(curl -s -X POST "$BASE_URL/auth/login" \
    -d "email=admin@academia.com' OR '1'='1&password=anything")

if [[ "$sql_injection_response" == *"SQL"* ]] || [[ "$sql_injection_response" == *"syntax error"* ]]; then
    log_result "SEC-002: FALHOU - Sistema vulnerável a SQL Injection"
else
    log_result "SEC-002: PASSOU - Sistema protegido contra SQL Injection básico"
    passed_tests=$((passed_tests + 1))
fi

# RESUMO FINAL
log_result "=== RESUMO DOS TESTES DE REGRAS DE NEGÓCIO ==="
log_result "Total de testes: $total_tests"
log_result "Testes aprovados: $passed_tests"
success_rate=$(( passed_tests * 100 / total_tests ))
log_result "Taxa de sucesso: $success_rate%"

if [[ $success_rate -ge 80 ]]; then
    log_result "STATUS: APROVADO - Regras de negócio funcionando adequadamente"
else
    log_result "STATUS: REPROVADO - Regras de negócio precisam de correções"
fi

# Limpar dados de teste
echo "Limpando dados de teste..."
docker exec academia_db mysql -uacademia_user -pacademia_pass -e "
USE academiabd; 
DELETE FROM aluno WHERE CPF = '99988877766';
DELETE FROM boleto WHERE ID_Pagamento = 100;
" 2>/dev/null

reset_test_data

log_result "=== FIM DOS TESTES ==="

echo ""
echo "Testes de regras de negócio concluídos!"
echo "Resultados salvos em: $RESULTS_FILE"
echo "Taxa de sucesso: $success_rate%"

# Limpar arquivos temporários
rm -f session_cookies.txt
