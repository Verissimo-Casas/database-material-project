#!/bin/bash

# =====================================================
# SISTEMA DE TESTES ABRANGENTES DE API - ACADEMIA
# Testes completos com curl para validar funcionalidades
# =====================================================

# Configurações
BASE_URL="http://localhost:8080"
COOKIE_FILE="session_cookies.txt"
RESULTS_FILE="comprehensive_test_results.log"
REPORT_FILE="API_TEST_REPORT.md"

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Contadores
TOTAL_TESTS=0
PASSED_TESTS=0
FAILED_TESTS=0
SKIPPED_TESTS=0

# Arrays para relatório
declare -a test_results
declare -a failed_tests

# Função para log
log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" | tee -a "$RESULTS_FILE"
}

# Função para teste HTTP avançado
test_endpoint() {
    local method=$1
    local endpoint=$2
    local description=$3
    local expected_status=$4
    local data=$5
    local use_cookies=${6:-"false"}
    local content_check=${7:-""}
    local test_category=${8:-"API"}
    
    TOTAL_TESTS=$((TOTAL_TESTS + 1))
    
    echo -e "\n${BLUE}TEST #$TOTAL_TESTS - $test_category${NC}"
    echo -e "${YELLOW}Endpoint:${NC} $method $endpoint"
    echo -e "${YELLOW}Description:${NC} $description"
    echo -e "${YELLOW}Expected Status:${NC} $expected_status"
    
    # Preparar comando curl
    local curl_cmd="curl -s -w '%{http_code}' -o response_body.tmp -X $method"
    
    if [ "$use_cookies" = "true" ]; then
        curl_cmd="$curl_cmd -b $COOKIE_FILE -c $COOKIE_FILE"
    fi
    
    if [ "$method" = "POST" ] && [ -n "$data" ]; then
        curl_cmd="$curl_cmd -d '$data'"
    fi
    
    curl_cmd="$curl_cmd $BASE_URL$endpoint"
    
    # Executar teste
    echo -e "${CYAN}Executing:${NC} $curl_cmd"
    actual_status=$(eval $curl_cmd)
    response_body=$(cat response_body.tmp 2>/dev/null || echo "")
    
    # Verificar status code
    if [ "$actual_status" = "$expected_status" ]; then
        # Verificar conteúdo se especificado
        if [ -n "$content_check" ] && ! echo "$response_body" | grep -q "$content_check"; then
            echo -e "${RED}❌ FAILED${NC} - Status OK but content check failed"
            echo -e "${RED}Expected content:${NC} $content_check"
            FAILED_TESTS=$((FAILED_TESTS + 1))
            test_results+=("FAIL: $description")
            failed_tests+=("$test_category: $description - Content check failed")
        else
            echo -e "${GREEN}✅ PASSED${NC} - Status: $actual_status"
            PASSED_TESTS=$((PASSED_TESTS + 1))
            test_results+=("PASS: $description")
        fi
    else
        echo -e "${RED}❌ FAILED${NC} - Expected: $expected_status, Got: $actual_status"
        echo -e "${RED}Response:${NC} $(echo "$response_body" | head -c 200)..."
        FAILED_TESTS=$((FAILED_TESTS + 1))
        test_results+=("FAIL: $description")
        failed_tests+=("$test_category: $description - Expected $expected_status, got $actual_status")
    fi
    
    log "[$test_category] $description: $actual_status (expected: $expected_status)"
}

# Função para capturar valor de formulário
extract_form_value() {
    local html_content="$1"
    local field_name="$2"
    echo "$html_content" | grep -o "name=\"$field_name\" value=\"[^\"]*\"" | sed "s/name=\"$field_name\" value=\"//g" | sed 's/"//g'
}

# Função para verificar se o serviço está rodando
check_service() {
    echo -e "${PURPLE}🔍 Verificando se o serviço está rodando...${NC}"
    
    if curl -s -f "$BASE_URL" > /dev/null; then
        echo -e "${GREEN}✅ Serviço está rodando em $BASE_URL${NC}"
        return 0
    else
        echo -e "${RED}❌ Serviço não está rodando em $BASE_URL${NC}"
        echo -e "${YELLOW}💡 Execute: docker-compose up -d${NC}"
        return 1
    fi
}

# Função para gerar relatório
generate_report() {
    cat > "$REPORT_FILE" << EOF
# 📊 RELATÓRIO DE TESTES DE API - SISTEMA ACADEMIA

**Data do Teste:** $(date '+%Y-%m-%d %H:%M:%S')  
**Base URL:** $BASE_URL  
**Total de Testes:** $TOTAL_TESTS  

## 📈 Resumo dos Resultados

| Métrica | Valor | Porcentagem |
|---------|-------|-------------|
| ✅ **Testes Aprovados** | $PASSED_TESTS | $(( PASSED_TESTS * 100 / TOTAL_TESTS ))% |
| ❌ **Testes Falharam** | $FAILED_TESTS | $(( FAILED_TESTS * 100 / TOTAL_TESTS ))% |
| ⏭️ **Testes Pulados** | $SKIPPED_TESTS | $(( SKIPPED_TESTS * 100 / TOTAL_TESTS ))% |

## 🎯 Taxa de Sucesso: $(( PASSED_TESTS * 100 / TOTAL_TESTS ))%

---

## 📋 Detalhes dos Testes

### ✅ Testes Aprovados ($PASSED_TESTS)
EOF

    for result in "${test_results[@]}"; do
        if [[ $result == PASS:* ]]; then
            echo "- ${result#PASS: }" >> "$REPORT_FILE"
        fi
    done

    echo "" >> "$REPORT_FILE"
    echo "### ❌ Testes Falharam ($FAILED_TESTS)" >> "$REPORT_FILE"

    if [ ${#failed_tests[@]} -eq 0 ]; then
        echo "- Nenhum teste falhou! 🎉" >> "$REPORT_FILE"
    else
        for failed in "${failed_tests[@]}"; do
            echo "- $failed" >> "$REPORT_FILE"
        done
    fi

    cat >> "$REPORT_FILE" << EOF

---

## 🔧 Comandos Curl para Testes Manuais

### Autenticação
\`\`\`bash
# Login como Admin
curl -X POST -d "email=admin@academia.com&password=password" \\
     -c cookies.txt http://localhost:8080/auth/login

# Login como Instrutor  
curl -X POST -d "email=joao@academia.com&password=password" \\
     -c cookies.txt http://localhost:8080/auth/login

# Login como Aluno
curl -X POST -d "email=maria@email.com&password=password" \\
     -c cookies.txt http://localhost:8080/auth/login
\`\`\`

### Dashboard
\`\`\`bash
# Acessar dashboard (após login)
curl -b cookies.txt http://localhost:8080/dashboard
\`\`\`

### Boletos
\`\`\`bash
# Listar boletos
curl -b cookies.txt http://localhost:8080/boleto

# Criar boleto
curl -b cookies.txt http://localhost:8080/boleto/create

# Marcar boleto como pago (ID = 1)
curl -X POST -b cookies.txt http://localhost:8080/boleto/markAsPaid/1
\`\`\`

### Matrículas
\`\`\`bash
# Listar matrículas
curl -b cookies.txt http://localhost:8080/matricula

# Criar matrícula
curl -b cookies.txt http://localhost:8080/matricula/create

# Alternar status da matrícula (ID = 1)
curl -X POST -b cookies.txt http://localhost:8080/matricula/toggleStatus/1
\`\`\`

---

## 🛠️ Resolução de Problemas

### Se algum teste falhar:

1. **Verificar se o serviço está rodando:**
   \`\`\`bash
   docker-compose ps
   docker-compose logs web
   \`\`\`

2. **Reiniciar os serviços:**
   \`\`\`bash
   docker-compose down
   docker-compose up -d
   \`\`\`

3. **Verificar logs de erro:**
   \`\`\`bash
   tail -f comprehensive_test_results.log
   \`\`\`

---

*Relatório gerado automaticamente - $(date '+%Y-%m-%d %H:%M:%S')*
EOF

    echo -e "\n${GREEN}📄 Relatório salvo em: $REPORT_FILE${NC}"
}

# Função principal de testes
run_tests() {
    echo -e "${PURPLE}🚀 INICIANDO TESTES ABRANGENTES DE API${NC}"
    echo -e "${PURPLE}=======================================${NC}"
    
    # Limpar arquivos anteriores
    rm -f "$COOKIE_FILE" "$RESULTS_FILE" response_body.tmp
    
    # Verificar serviço
    if ! check_service; then
        exit 1
    fi
    
    echo -e "\n${PURPLE}📋 CATEGORIA: PÁGINAS PÚBLICAS${NC}"
    echo -e "${PURPLE}==============================${NC}"
    
    # Testes de páginas públicas
    test_endpoint "GET" "/" "Homepage (redirecionamento)" "302" "" "false" "" "PUBLIC"
    test_endpoint "GET" "/auth/login" "Página de login" "200" "" "false" "Login" "PUBLIC"
    test_endpoint "GET" "/auth/register" "Página de registro" "200" "" "false" "Registro" "PUBLIC"
    
    echo -e "\n${PURPLE}📋 CATEGORIA: PROTEÇÃO DE ROTAS${NC}"
    echo -e "${PURPLE}================================${NC}"
    
    # Testes de proteção sem autenticação
    test_endpoint "GET" "/dashboard" "Dashboard sem auth (redirect)" "302" "" "false" "" "SECURITY"
    test_endpoint "GET" "/boleto" "Boletos sem auth (redirect)" "302" "" "false" "" "SECURITY"
    test_endpoint "GET" "/matricula" "Matrículas sem auth (redirect)" "302" "" "false" "" "SECURITY"
    
    echo -e "\n${PURPLE}📋 CATEGORIA: AUTENTICAÇÃO${NC}"
    echo -e "${PURPLE}============================${NC}"
    
    # Testes de autenticação
    test_endpoint "POST" "/auth/login" "Login admin válido" "302" "email=admin@academia.com&password=password" "true" "" "AUTH"
    
    # Verificar se o login foi bem-sucedido testando acesso ao dashboard
    test_endpoint "GET" "/dashboard" "Dashboard após login admin" "200" "" "true" "Dashboard" "AUTH"
    
    echo -e "\n${PURPLE}📋 CATEGORIA: FUNCIONALIDADES AUTENTICADAS${NC}"
    echo -e "${PURPLE}===========================================${NC}"
    
    # Testes com usuário logado
    test_endpoint "GET" "/boleto" "Listar boletos (autenticado)" "200" "" "true" "" "BUSINESS"
    test_endpoint "GET" "/boleto/create" "Formulário criar boleto" "200" "" "true" "" "BUSINESS"
    test_endpoint "GET" "/matricula" "Listar matrículas (autenticado)" "200" "" "true" "" "BUSINESS"
    test_endpoint "GET" "/matricula/create" "Formulário criar matrícula" "200" "" "true" "" "BUSINESS"
    
    echo -e "\n${PURPLE}📋 CATEGORIA: TESTE DE OUTROS USUÁRIOS${NC}"
    echo -e "${PURPLE}=======================================${NC}"
    
    # Logout e teste com outros usuários
    test_endpoint "GET" "/auth/logout" "Logout" "302" "" "true" "" "AUTH"
    
    # Login como instrutor
    test_endpoint "POST" "/auth/login" "Login instrutor válido" "302" "email=joao@academia.com&password=password" "true" "" "AUTH"
    test_endpoint "GET" "/dashboard" "Dashboard instrutor" "200" "" "true" "Dashboard" "AUTH"
    
    # Logout e login como aluno
    test_endpoint "GET" "/auth/logout" "Logout instrutor" "302" "" "true" "" "AUTH"
    test_endpoint "POST" "/auth/login" "Login aluno válido" "302" "email=maria@email.com&password=password" "true" "" "AUTH"
    test_endpoint "GET" "/dashboard" "Dashboard aluno" "200" "" "true" "Dashboard" "AUTH"
    
    echo -e "\n${PURPLE}📋 CATEGORIA: TESTES DE ERRO${NC}"
    echo -e "${PURPLE}============================${NC}"
    
    # Logout para testes de erro
    test_endpoint "GET" "/auth/logout" "Logout para testes de erro" "302" "" "true" "" "ERROR"
    
    # Testes de login inválido
    test_endpoint "POST" "/auth/login" "Login com email inválido" "200" "email=invalido@test.com&password=password" "false" "" "ERROR"
    test_endpoint "POST" "/auth/login" "Login com senha inválida" "200" "email=admin@academia.com&password=senhaerrada" "false" "" "ERROR"
    
    # Testes de rotas inexistentes
    test_endpoint "GET" "/rota/inexistente" "Rota inexistente" "404" "" "false" "" "ERROR"
    test_endpoint "GET" "/boleto/acao/inexistente" "Ação inexistente" "404" "" "false" "" "ERROR"
    
    echo -e "\n${PURPLE}📋 CATEGORIA: VALIDAÇÃO DE PARÂMETROS${NC}"
    echo -e "${PURPLE}===================================${NC}"
    
    # Login novamente para testes com parâmetros
    test_endpoint "POST" "/auth/login" "Re-login para testes de parâmetros" "302" "email=admin@academia.com&password=password" "true" "" "PARAM"
    
    # Testes com IDs inválidos
    test_endpoint "POST" "/boleto/markAsPaid/999" "Marcar boleto inexistente como pago" "404" "" "true" "" "PARAM"
    test_endpoint "POST" "/matricula/toggleStatus/999" "Toggle status matrícula inexistente" "404" "" "true" "" "PARAM"
    test_endpoint "GET" "/boleto/create/999" "Criar boleto para matrícula inexistente" "404" "" "true" "" "PARAM"
    
    # Limpeza
    rm -f response_body.tmp
    
    echo -e "\n${PURPLE}📊 RESUMO FINAL DOS TESTES${NC}"
    echo -e "${PURPLE}===========================${NC}"
    echo -e "${GREEN}✅ Testes Aprovados: $PASSED_TESTS${NC}"
    echo -e "${RED}❌ Testes Falharam: $FAILED_TESTS${NC}"
    echo -e "${YELLOW}⏭️ Testes Pulados: $SKIPPED_TESTS${NC}"
    echo -e "${CYAN}📊 Total de Testes: $TOTAL_TESTS${NC}"
    
    success_rate=$(( PASSED_TESTS * 100 / TOTAL_TESTS ))
    if [ $success_rate -ge 90 ]; then
        echo -e "${GREEN}🎉 Taxa de Sucesso: $success_rate% - EXCELENTE!${NC}"
    elif [ $success_rate -ge 70 ]; then
        echo -e "${YELLOW}⚠️ Taxa de Sucesso: $success_rate% - BOM${NC}"
    else
        echo -e "${RED}🚨 Taxa de Sucesso: $success_rate% - NECESSITA ATENÇÃO${NC}"
    fi
    
    generate_report
    
    echo -e "\n${CYAN}📋 Logs detalhados salvos em: $RESULTS_FILE${NC}"
    echo -e "${CYAN}📄 Relatório completo em: $REPORT_FILE${NC}"
}

# Verificar parâmetros
case "$1" in
    "help"|"-h"|"--help")
        echo "Uso: $0 [opção]"
        echo ""
        echo "Opções:"
        echo "  help, -h, --help    Mostrar esta ajuda"
        echo "  quick              Executar apenas testes básicos"
        echo "  (sem parâmetro)    Executar todos os testes"
        echo ""
        echo "Exemplos:"
        echo "  $0                 # Executar todos os testes"
        echo "  $0 quick           # Executar apenas testes básicos"
        exit 0
        ;;
    "quick")
        echo -e "${YELLOW}🏃 Executando testes rápidos (modo básico)${NC}"
        # Implementar versão rápida se necessário
        run_tests
        ;;
    *)
        run_tests
        ;;
esac
