#!/bin/bash

# SCRIPT DE TESTES DE RESPONSIVIDADE
# Autor: QA Sênior
# Data: 08/07/2025

echo "================================================="
echo "    TESTES DE RESPONSIVIDADE - SGF"
echo "    Sistema de Gestão de Academia"
echo "================================================="
echo ""

BASE_URL="http://localhost:8080"
RESULTS_FILE="testes_responsividade.log"

# Função para log de resultados
log_result() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a $RESULTS_FILE
}

# Inicializar arquivo de log
echo "" > $RESULTS_FILE

log_result "=== TESTES DE RESPONSIVIDADE E UI ==="

total_tests=0
passed_tests=0

# Fazer login como admin para ter acesso às páginas
echo "Fazendo login como administrador..."
curl -s -c session_cookies.txt -X POST "$BASE_URL/auth/login" \
    -d "email=admin@academia.com&password=password" > /dev/null

# Testar páginas principais
pages=(
    "/"
    "/dashboard"
    "/matricula"
    "/boleto"
    "/auth/login"
)

log_result "--- TESTANDO CARREGAMENTO DE PÁGINAS ---"

for page in "${pages[@]}"; do
    total_tests=$((total_tests + 1))
    echo "Testando página: $page"
    
    response=$(curl -s -b session_cookies.txt "$BASE_URL$page" -w "%{http_code}")
    http_code="${response: -3}"
    
    # Verificar se a página carrega
    if [[ "$http_code" == "200" ]] || [[ "$http_code" == "302" ]]; then
        # Verificar se contém Bootstrap (indicador de responsividade)
        if [[ "$response" == *"bootstrap"* ]] || [[ "$response" == *"viewport"* ]]; then
            log_result "NFT-$(printf "%03d" $total_tests): PASSOU - Página $page carrega e contém elementos responsivos"
            passed_tests=$((passed_tests + 1))
        else
            log_result "NFT-$(printf "%03d" $total_tests): FALHOU - Página $page não contém elementos responsivos"
        fi
    else
        log_result "NFT-$(printf "%03d" $total_tests): FALHOU - Página $page não carrega (HTTP: $http_code)"
    fi
done

# Verificar se há CSS responsivo
total_tests=$((total_tests + 1))
echo "Verificando CSS responsivo..."

css_response=$(curl -s "$BASE_URL/assets/css/custom.css")
if [[ "$css_response" == *"@media"* ]] || [[ "$css_response" == *"responsive"* ]]; then
    log_result "NFT-$(printf "%03d" $total_tests): PASSOU - CSS contém regras responsivas"
    passed_tests=$((passed_tests + 1))
else
    log_result "NFT-$(printf "%03d" $total_tests): INDETERMINADO - CSS responsivo não detectado (pode usar Bootstrap)"
fi

# Verificar meta viewport
total_tests=$((total_tests + 1))
main_page=$(curl -s -b session_cookies.txt "$BASE_URL/")
if [[ "$main_page" == *'name="viewport"'* ]]; then
    log_result "NFT-$(printf "%03d" $total_tests): PASSOU - Meta viewport configurado"
    passed_tests=$((passed_tests + 1))
else
    log_result "NFT-$(printf "%03d" $total_tests): FALHOU - Meta viewport não encontrado"
fi

# Testar acessibilidade básica
total_tests=$((total_tests + 1))
if [[ "$main_page" == *'alt='* ]] && [[ "$main_page" == *'aria-'* ]]; then
    log_result "NFT-$(printf "%03d" $total_tests): PASSOU - Elementos de acessibilidade encontrados"
    passed_tests=$((passed_tests + 1))
else
    log_result "NFT-$(printf "%03d" $total_tests): INDETERMINADO - Elementos de acessibilidade limitados"
fi

log_result "=== RESUMO DOS TESTES DE RESPONSIVIDADE ==="
log_result "Total de testes: $total_tests"
log_result "Testes aprovados: $passed_tests"
success_rate=$(( passed_tests * 100 / total_tests ))
log_result "Taxa de sucesso: $success_rate%"

if [[ $success_rate -ge 75 ]]; then
    log_result "STATUS: APROVADO - Interface responsiva adequada"
else
    log_result "STATUS: REPROVADO - Interface precisa de melhorias de responsividade"
fi

# Limpar cookies
rm -f session_cookies.txt

log_result "=== FIM DOS TESTES DE RESPONSIVIDADE ==="

echo ""
echo "Testes de responsividade concluídos!"
echo "Resultados salvos em: $RESULTS_FILE"
echo "Taxa de sucesso: $success_rate%"
