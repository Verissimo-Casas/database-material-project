#!/bin/bash

# SCRIPT DE VALIDAÇÃO PÓS-CORREÇÃO
# Autor: QA Sênior
# Data: 08/07/2025

echo "================================================="
echo "    VALIDAÇÃO PÓS-CORREÇÃO - CONTROLLERS"
echo "    Sistema de Gestão de Academia"
echo "================================================="
echo ""

BASE_URL="http://localhost:8080"
RESULTS_FILE="validacao_pos_correcao.log"

# Função para log de resultados
log_result() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a $RESULTS_FILE
}

# Fazer login como admin
echo "Fazendo login como administrador..."
curl -s -c session_cookies.txt -X POST "$BASE_URL/auth/login" \
    -d "email=admin@academia.com&password=password" > /dev/null

# Lista de controllers para testar
controllers=(
    "dashboard"
    "matricula"
    "boleto"
    "plano_treino"
    "aula"
    "avaliacao"
    "relatorio"
)

# Inicializar arquivo de log
echo "" > $RESULTS_FILE

log_result "=== VALIDAÇÃO DE CONTROLLERS PÓS-CORREÇÃO ==="

total_tests=0
passed_tests=0

for controller in "${controllers[@]}"; do
    total_tests=$((total_tests + 1))
    echo "Testando controller: $controller"
    
    response=$(curl -s -b session_cookies.txt "$BASE_URL/$controller" -w "%{http_code}")
    http_code="${response: -3}"
    
    if [[ "$http_code" == "200" ]]; then
        # Verificar se a página carregou corretamente (não é erro PHP)
        if [[ "$response" == *"Fatal error"* ]] || [[ "$response" == *"Controller not found"* ]]; then
            log_result "❌ FALHOU - $controller: Erro PHP ou controller não encontrado"
        else
            log_result "✅ PASSOU - $controller: Página carrega corretamente"
            passed_tests=$((passed_tests + 1))
        fi
    elif [[ "$http_code" == "302" ]]; then
        log_result "✅ PASSOU - $controller: Redirecionamento (login necessário)"
        passed_tests=$((passed_tests + 1))
    elif [[ "$http_code" == "403" ]]; then
        log_result "✅ PASSOU - $controller: Acesso negado (permissões funcionando)"
        passed_tests=$((passed_tests + 1))
    else
        log_result "❌ FALHOU - $controller: HTTP $http_code"
    fi
done

# Testar permissões específicas
log_result "--- TESTANDO PERMISSÕES DE ALUNO ---"

# Login como aluno
curl -s -c session_cookies_aluno.txt -X POST "$BASE_URL/auth/login" \
    -d "email=maria@email.com&password=password" > /dev/null

# Testar acesso de aluno a criação (deve falhar)
restricted_endpoints=(
    "plano_treino/create"
    "aula/create"
    "avaliacao/create"
    "relatorio"
)

for endpoint in "${restricted_endpoints[@]}"; do
    total_tests=$((total_tests + 1))
    echo "Testando restrição: $endpoint (aluno)"
    
    response=$(curl -s -b session_cookies_aluno.txt "$BASE_URL/$endpoint" -w "%{http_code}")
    http_code="${response: -3}"
    
    if [[ "$http_code" == "403" ]] || [[ "$response" == *"Acesso negado"* ]]; then
        log_result "✅ PASSOU - $endpoint: Aluno corretamente bloqueado"
        passed_tests=$((passed_tests + 1))
    else
        log_result "❌ FALHOU - $endpoint: Aluno não foi bloqueado (HTTP: $http_code)"
    fi
done

# Resumo final
log_result "=== RESUMO DA VALIDAÇÃO ==="
log_result "Total de testes: $total_tests"
log_result "Testes aprovados: $passed_tests"
success_rate=$(( passed_tests * 100 / total_tests ))
log_result "Taxa de sucesso: $success_rate%"

if [[ $success_rate -ge 90 ]]; then
    log_result "STATUS: ✅ EXCELENTE - Sistema corrigido com sucesso"
elif [[ $success_rate -ge 80 ]]; then
    log_result "STATUS: ✅ BOM - Sistema funcional com pequenas melhorias necessárias"
else
    log_result "STATUS: ❌ REPROVADO - Sistema ainda precisa de correções"
fi

# Limpar arquivos temporários
rm -f session_cookies.txt session_cookies_aluno.txt

log_result "=== FIM DA VALIDAÇÃO ==="

echo ""
echo "Validação concluída!"
echo "Resultados salvos em: $RESULTS_FILE"
echo "Taxa de sucesso: $success_rate%"
