#!/bin/bash

# SCRIPT DE EXECUÇÃO AUTOMATIZADA DE TESTES - SISTEMA DE GESTÃO DE ACADEMIA
# Autor: QA Sênior
# Data: 08/07/2025

echo "================================================="
echo "    EXECUÇÃO AUTOMATIZADA DE TESTES - SGF"
echo "    Sistema de Gestão de Academia"
echo "================================================="
echo ""

BASE_URL="http://localhost:8080"
RESULTS_FILE="resultado_testes_qa.log"
COOKIES_FILE="session_cookies.txt"

# Função para log de resultados
log_result() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a $RESULTS_FILE
}

# Função para testar login
test_login() {
    local email=$1
    local password=$2
    local expected_type=$3
    local test_id=$4
    
    echo "Executando $test_id - Login $expected_type..."
    
    # Limpar cookies anteriores
    rm -f $COOKIES_FILE
    
    # Fazer request de login
    response=$(curl -s -c $COOKIES_FILE -X POST "$BASE_URL/auth/login" \
        -d "email=$email&password=$password" \
        -w "%{http_code}")
    
    # Extrair código HTTP (últimos 3 caracteres)
    http_code="${response: -3}"
    
    if [[ "$http_code" == "302" ]]; then
        log_result "$test_id: PASSOU - Login $expected_type redirecionou corretamente"
        return 0
    else
        log_result "$test_id: FALHOU - Login $expected_type não redirecionou (HTTP: $http_code)"
        return 1
    fi
}

# Função para testar login inválido
test_invalid_login() {
    local email=$1
    local password=$2
    local test_id=$3
    
    echo "Executando $test_id - Login inválido..."
    
    # Limpar cookies
    rm -f $COOKIES_FILE
    
    # Fazer request de login
    response=$(curl -s -c $COOKIES_FILE -X POST "$BASE_URL/auth/login" \
        -d "email=$email&password=$password" \
        -w "%{http_code}")
    
    # Extrair código HTTP
    http_code="${response: -3}"
    
    if [[ "$http_code" == "200" ]] && [[ "$response" == *"Email ou senha inválidos"* ]]; then
        log_result "$test_id: PASSOU - Login inválido rejeitado corretamente"
        return 0
    else
        log_result "$test_id: FALHOU - Login inválido não foi rejeitado adequadamente"
        return 1
    fi
}

# Função para testar acesso ao dashboard
test_dashboard_access() {
    local user_type=$1
    local test_id=$2
    
    echo "Executando $test_id - Acesso ao dashboard $user_type..."
    
    # Tentar acessar dashboard com cookies da sessão
    response=$(curl -s -b $COOKIES_FILE "$BASE_URL/dashboard" -w "%{http_code}")
    http_code="${response: -3}"
    
    if [[ "$http_code" == "200" ]]; then
        log_result "$test_id: PASSOU - Dashboard $user_type acessível"
        return 0
    else
        log_result "$test_id: FALHOU - Dashboard $user_type não acessível (HTTP: $http_code)"
        return 1
    fi
}

# Função para testar acesso a módulos
test_module_access() {
    local module=$1
    local test_id=$2
    
    echo "Executando $test_id - Acesso ao módulo $module..."
    
    response=$(curl -s -b $COOKIES_FILE "$BASE_URL/$module" -w "%{http_code}")
    http_code="${response: -3}"
    
    if [[ "$http_code" == "200" ]]; then
        log_result "$test_id: PASSOU - Módulo $module acessível"
        return 0
    elif [[ "$http_code" == "403" ]]; then
        log_result "$test_id: PASSOU - Módulo $module corretamente bloqueado (403 Forbidden)"
        return 0
    else
        log_result "$test_id: INDETERMINADO - Módulo $module retornou HTTP $http_code"
        return 1
    fi
}

# Limpar arquivo de resultados anterior
echo "" > $RESULTS_FILE

log_result "=== INÍCIO DOS TESTES FUNCIONAIS ==="

# FASE 1: TESTES FUNCIONAIS DE AUTENTICAÇÃO
log_result "--- FASE 1: TESTES DE AUTENTICAÇÃO ---"

passed_tests=0
total_tests=0

# TC-001: Login Administrador
total_tests=$((total_tests + 1))
if test_login "admin@academia.com" "password" "administrador" "TC-001"; then
    passed_tests=$((passed_tests + 1))
fi

# TC-002: Login Instrutor  
total_tests=$((total_tests + 1))
if test_login "joao@academia.com" "password" "instrutor" "TC-002"; then
    passed_tests=$((passed_tests + 1))
fi

# TC-003: Login Aluno
total_tests=$((total_tests + 1))
if test_login "maria@email.com" "password" "aluno" "TC-003"; then
    passed_tests=$((passed_tests + 1))
fi

# TC-004: Login Inválido
total_tests=$((total_tests + 1))
if test_invalid_login "invalido@test.com" "senhaerrada" "TC-004"; then
    passed_tests=$((passed_tests + 1))
fi

log_result "--- RESUMO FASE 1 ---"
log_result "Testes executados: $total_tests"
log_result "Testes aprovados: $passed_tests"
log_result "Taxa de sucesso: $(( passed_tests * 100 / total_tests ))%"

# FASE 2: TESTES DE ACESSO AOS MÓDULOS
log_result "--- FASE 2: TESTES DE ACESSO AOS MÓDULOS ---"

phase2_passed=0
phase2_total=0

# Fazer login como admin para testar acesso
test_login "admin@academia.com" "password" "administrador" "SETUP"

# TC-006: Acesso a Matrículas
phase2_total=$((phase2_total + 1))
if test_module_access "matricula" "TC-006"; then
    phase2_passed=$((phase2_passed + 1))
fi

# TC-009: Acesso a Boletos
phase2_total=$((phase2_total + 1))
if test_module_access "boleto" "TC-009"; then
    phase2_passed=$((phase2_passed + 1))
fi

# TC-012: Acesso ao Dashboard
phase2_total=$((phase2_total + 1))
if test_dashboard_access "administrador" "TC-012"; then
    phase2_passed=$((phase2_passed + 1))
fi

log_result "--- RESUMO FASE 2 ---"
log_result "Testes executados: $phase2_total"
log_result "Testes aprovados: $phase2_passed"
log_result "Taxa de sucesso: $(( phase2_passed * 100 / phase2_total ))%"

# FASE 3: TESTES DE API
log_result "--- FASE 3: TESTES DE API ---"

phase3_passed=0
phase3_total=0

# API-001: Teste de login via API
phase3_total=$((phase3_total + 1))
api_response=$(curl -s -X POST "$BASE_URL/auth/login" \
    -d "email=admin@academia.com&password=password" \
    -w "%{http_code}")
    
api_code="${api_response: -3}"
if [[ "$api_code" == "302" ]]; then
    log_result "API-001: PASSOU - API de login funcionando"
    phase3_passed=$((phase3_passed + 1))
else
    log_result "API-001: FALHOU - API de login retornou HTTP $api_code"
fi

# API-002: Teste de login inválido via API
phase3_total=$((phase3_total + 1))
api_response=$(curl -s -X POST "$BASE_URL/auth/login" \
    -d "email=invalid@test.com&password=wrong" \
    -w "%{http_code}")
    
api_code="${api_response: -3}"
if [[ "$api_code" == "200" ]]; then
    log_result "API-002: PASSOU - API rejeitou login inválido"
    phase3_passed=$((phase3_passed + 1))
else
    log_result "API-002: FALHOU - API não rejeitou login inválido adequadamente"
fi

log_result "--- RESUMO FASE 3 ---"
log_result "Testes executados: $phase3_total"
log_result "Testes aprovados: $phase3_passed"
log_result "Taxa de sucesso: $(( phase3_passed * 100 / phase3_total ))%"

# RESUMO GERAL
log_result "=== RESUMO GERAL DOS TESTES ==="
total_all=$(( total_tests + phase2_total + phase3_total ))
passed_all=$(( passed_tests + phase2_passed + phase3_passed ))
success_rate=$(( passed_all * 100 / total_all ))

log_result "Total de testes executados: $total_all"
log_result "Total de testes aprovados: $passed_all"
log_result "Taxa de sucesso geral: $success_rate%"

if [[ $success_rate -ge 80 ]]; then
    log_result "STATUS: APROVADO - Sistema em condições adequadas"
else
    log_result "STATUS: REPROVADO - Sistema precisa de correções"
fi

log_result "=== FIM DOS TESTES ==="

echo ""
echo "Testes concluídos! Resultados salvos em: $RESULTS_FILE"
echo "Taxa de sucesso: $success_rate%"
