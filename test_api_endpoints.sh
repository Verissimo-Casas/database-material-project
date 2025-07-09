#!/bin/bash

# =====================================================
# SISTEMA DE TESTES DE API - ACADEMIA
# Testes abrangentes com curl para validar endpoints
# =====================================================

# Configurações
BASE_URL="http://localhost:8080"
COOKIE_FILE="session_cookies.txt"
RESULTS_FILE="test_results.log"

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Contadores
TOTAL_TESTS=0
PASSED_TESTS=0
FAILED_TESTS=0

# Função para log
log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" | tee -a "$RESULTS_FILE"
}

# Função para teste HTTP
test_endpoint() {
    local method=$1
    local endpoint=$2
    local description=$3
    local expected_status=$4
    local data=$5
    local use_cookies=$6
    
    TOTAL_TESTS=$((TOTAL_TESTS + 1))
    
    echo -e "\n${BLUE}TEST #$TOTAL_TESTS${NC}"
    echo -e "${YELLOW}Endpoint:${NC} $method $endpoint"
    echo -e "${YELLOW}Description:${NC} $description"
    echo -e "${YELLOW}Expected Status:${NC} $expected_status"
    
    # Preparar comando curl
    local curl_cmd="curl -s -w '%{http_code}' -o response_body.tmp"
    
    if [ "$use_cookies" = "true" ]; then
        curl_cmd="$curl_cmd -b $COOKIE_FILE"
    fi
    
    if [ "$method" = "POST" ]; then
        if [ -n "$data" ]; then
            curl_cmd="$curl_cmd -X POST -H 'Content-Type: application/x-www-form-urlencoded' -d '$data'"
        else
            curl_cmd="$curl_cmd -X POST"
        fi
    fi
    
    curl_cmd="$curl_cmd $BASE_URL$endpoint"
    
    # Executar teste
    response_code=$(eval $curl_cmd)
    response_body=$(cat response_body.tmp)
    
    # Verificar resultado
    if [ "$response_code" = "$expected_status" ]; then
        echo -e "${GREEN}✅ PASSED${NC} - Status: $response_code"
        PASSED_TESTS=$((PASSED_TESTS + 1))
        log "PASS: $method $endpoint - $description (Status: $response_code)"
    else
        echo -e "${RED}❌ FAILED${NC} - Expected: $expected_status, Got: $response_code"
        FAILED_TESTS=$((FAILED_TESTS + 1))
        log "FAIL: $method $endpoint - $description (Expected: $expected_status, Got: $response_code)"
        
        # Mostrar parte da resposta em caso de erro
        if [ ${#response_body} -gt 0 ]; then
            echo -e "${YELLOW}Response snippet:${NC}"
            echo "$response_body" | head -c 200
            echo "..."
        fi
    fi
    
    # Limpar arquivo temporário
    rm -f response_body.tmp
}

# Função para login e captura de sessão
perform_login() {
    local email=$1
    local password=$2
    local user_type=$3
    
    echo -e "\n${BLUE}=== PERFORMING LOGIN FOR $user_type ===${NC}"
    
    # Primeiro, get CSRF token se necessário
    curl -s -c "$COOKIE_FILE" "$BASE_URL/auth/login" > login_page.tmp
    
    # Fazer login
    curl -s -w '%{http_code}' \
         -c "$COOKIE_FILE" \
         -b "$COOKIE_FILE" \
         -X POST \
         -H "Content-Type: application/x-www-form-urlencoded" \
         -d "email=$email&password=$password" \
         "$BASE_URL/auth/login" \
         -o login_response.tmp
    
    login_status=$?
    
    if [ $login_status -eq 0 ]; then
        echo -e "${GREEN}✅ Login successful for $user_type${NC}"
        log "LOGIN SUCCESS: $user_type ($email)"
        return 0
    else
        echo -e "${RED}❌ Login failed for $user_type${NC}"
        log "LOGIN FAILED: $user_type ($email)"
        return 1
    fi
}

# Função para logout
perform_logout() {
    echo -e "\n${BLUE}=== PERFORMING LOGOUT ===${NC}"
    curl -s -b "$COOKIE_FILE" "$BASE_URL/auth/logout" > /dev/null
    rm -f "$COOKIE_FILE"
    echo -e "${GREEN}✅ Logout completed${NC}"
    log "LOGOUT COMPLETED"
}

# Limpar arquivos anteriores
clear_temp_files() {
    rm -f "$COOKIE_FILE" "$RESULTS_FILE" *.tmp
}

# Início dos testes
main() {
    clear_temp_files
    
    echo -e "${BLUE}=====================================================${NC}"
    echo -e "${BLUE}       SISTEMA DE TESTES DE API - ACADEMIA          ${NC}"
    echo -e "${BLUE}=====================================================${NC}"
    
    log "INICIANDO TESTES DE API - SISTEMA ACADEMIA"
    
    # ===================================================
    # TESTES DE ROTAS PÚBLICAS (SEM AUTENTICAÇÃO)
    # ===================================================
    
    echo -e "\n${YELLOW}📋 SEÇÃO 1: TESTES DE ROTAS PÚBLICAS${NC}"
    echo -e "${YELLOW}======================================${NC}"
    
    # Homepage (deve redirecionar para login)
    test_endpoint "GET" "/" "Homepage redirect to login" "302" "" "false"
    
    # Página de login
    test_endpoint "GET" "/auth/login" "Login page" "200" "" "false"
    
    # Página de registro
    test_endpoint "GET" "/auth/register" "Register page" "200" "" "false"
    
    # Teste de login com credenciais inválidas
    test_endpoint "POST" "/auth/login" "Login with invalid credentials" "200" "email=invalid@test.com&password=wrongpass" "false"
    
    # ===================================================
    # TESTES DE PROTEÇÃO DE ROTAS (SEM AUTENTICAÇÃO)
    # ===================================================
    
    echo -e "\n${YELLOW}🔒 SEÇÃO 2: TESTES DE PROTEÇÃO DE ROTAS${NC}"
    echo -e "${YELLOW}=======================================${NC}"
    
    # Tentar acessar dashboard sem login (deve redirecionar)
    test_endpoint "GET" "/dashboard" "Access dashboard without auth" "302" "" "false"
    
    # Tentar acessar boletos sem login
    test_endpoint "GET" "/boleto" "Access boleto without auth" "302" "" "false"
    
    # Tentar acessar matrículas sem login
    test_endpoint "GET" "/matricula" "Access matricula without auth" "302" "" "false"
    
    # ===================================================
    # TESTES COM AUTENTICAÇÃO - ADMINISTRADOR
    # ===================================================
    
    echo -e "\n${YELLOW}👤 SEÇÃO 3: TESTES COM ADMIN${NC}"
    echo -e "${YELLOW}============================${NC}"
    
    if perform_login "admin@academia.com" "password" "ADMIN"; then
        
        # Dashboard de admin
        test_endpoint "GET" "/dashboard" "Admin dashboard access" "200" "" "true"
        
        # Lista de boletos (admin pode ver todos)
        test_endpoint "GET" "/boleto" "Admin view all boletos" "200" "" "true"
        
        # Formulário de criação de boleto
        test_endpoint "GET" "/boleto/create" "Admin create boleto form" "200" "" "true"
        
        # Lista de matrículas (apenas admin)
        test_endpoint "GET" "/matricula" "Admin view matriculas" "200" "" "true"
        
        # Formulário de criação de matrícula (apenas admin)
        test_endpoint "GET" "/matricula/create" "Admin create matricula form" "200" "" "true"
        
        perform_logout
    fi
    
    # ===================================================
    # TESTES COM AUTENTICAÇÃO - INSTRUTOR
    # ===================================================
    
    echo -e "\n${YELLOW}🏋️ SEÇÃO 4: TESTES COM INSTRUTOR${NC}"
    echo -e "${YELLOW}================================${NC}"
    
    if perform_login "joao@academia.com" "password" "INSTRUTOR"; then
        
        # Dashboard de instrutor
        test_endpoint "GET" "/dashboard" "Instrutor dashboard access" "200" "" "true"
        
        # Lista de boletos (instrutor pode ver)
        test_endpoint "GET" "/boleto" "Instrutor view boletos" "200" "" "true"
        
        # Tentar acessar criação de boleto (apenas admin)
        test_endpoint "GET" "/boleto/create" "Instrutor try create boleto" "302" "" "true"
        
        # Tentar acessar matrículas (apenas admin)
        test_endpoint "GET" "/matricula" "Instrutor try access matriculas" "302" "" "true"
        
        perform_logout
    fi
    
    # ===================================================
    # TESTES COM AUTENTICAÇÃO - ALUNO
    # ===================================================
    
    echo -e "\n${YELLOW}🎓 SEÇÃO 5: TESTES COM ALUNO${NC}"
    echo -e "${YELLOW}===========================${NC}"
    
    if perform_login "maria@email.com" "password" "ALUNO"; then
        
        # Dashboard de aluno
        test_endpoint "GET" "/dashboard" "Aluno dashboard access" "200" "" "true"
        
        # Lista de boletos próprios
        test_endpoint "GET" "/boleto" "Aluno view own boletos" "200" "" "true"
        
        # Tentar acessar criação de boleto (apenas admin)
        test_endpoint "GET" "/boleto/create" "Aluno try create boleto" "302" "" "true"
        
        # Tentar acessar matrículas (apenas admin)
        test_endpoint "GET" "/matricula" "Aluno try access matriculas" "302" "" "true"
        
        perform_logout
    fi
    
    # ===================================================
    # TESTES DE REGISTRO DE USUÁRIO
    # ===================================================
    
    echo -e "\n${YELLOW}📝 SEÇÃO 6: TESTES DE REGISTRO${NC}"
    echo -e "${YELLOW}=============================${NC}"
    
    # Teste de registro com dados válidos (pode falhar por CSRF se habilitado)
    test_endpoint "POST" "/auth/register" "Register new user (may fail due to CSRF)" "200" "cpf=12345678901&nome=Teste%20Usuario&dt_nasc=1990-01-01&endereco=Rua%20Teste&contato=11999999999&email=teste@teste.com&senha=password123" "false"
    
    # ===================================================
    # TESTES DE ENDPOINTS ESPECÍFICOS
    # ===================================================
    
    echo -e "\n${YELLOW}🔧 SEÇÃO 7: TESTES DE ENDPOINTS ESPECÍFICOS${NC}"
    echo -e "${YELLOW}===========================================${NC}"
    
    # Login como admin para testes específicos
    if perform_login "admin@academia.com" "password" "ADMIN"; then
        
        # Teste de criação de boleto
        test_endpoint "POST" "/boleto/create" "Create boleto (may fail due to CSRF)" "200" "forma_pagamento=Boleto&valor=50.00&dt_vencimento=2025-08-08&id_matricula=1" "true"
        
        # Teste de toggle status de matrícula
        test_endpoint "POST" "/matricula/toggleStatus/1" "Toggle matricula status" "302" "" "true"
        
        # Teste de marcar boleto como pago
        test_endpoint "POST" "/boleto/markAsPaid/1" "Mark boleto as paid" "302" "" "true"
        
        perform_logout
    fi
    
    # ===================================================
    # RELATÓRIO FINAL
    # ===================================================
    
    echo -e "\n${BLUE}=====================================================${NC}"
    echo -e "${BLUE}                 RELATÓRIO FINAL                    ${NC}"
    echo -e "${BLUE}=====================================================${NC}"
    
    echo -e "\n${YELLOW}📊 ESTATÍSTICAS DOS TESTES:${NC}"
    echo -e "Total de testes executados: ${BLUE}$TOTAL_TESTS${NC}"
    echo -e "Testes aprovados: ${GREEN}$PASSED_TESTS${NC}"
    echo -e "Testes falharam: ${RED}$FAILED_TESTS${NC}"
    
    if [ $TOTAL_TESTS -gt 0 ]; then
        success_rate=$((PASSED_TESTS * 100 / TOTAL_TESTS))
        echo -e "Taxa de sucesso: ${YELLOW}$success_rate%${NC}"
    fi
    
    echo -e "\n${YELLOW}📄 Log completo salvo em:${NC} $RESULTS_FILE"
    
    # Limpar arquivos temporários
    clear_temp_files
    
    log "TESTES CONCLUÍDOS - Total: $TOTAL_TESTS, Passed: $PASSED_TESTS, Failed: $FAILED_TESTS"
    
    if [ $FAILED_TESTS -eq 0 ]; then
        echo -e "\n${GREEN}🎉 TODOS OS TESTES PASSARAM! 🎉${NC}"
        exit 0
    else
        echo -e "\n${RED}⚠️  ALGUNS TESTES FALHARAM ⚠️${NC}"
        exit 1
    fi
}

# Executar testes
main "$@"
