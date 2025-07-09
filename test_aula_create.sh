#!/bin/bash

echo "🧪 TESTANDO URL /aula/create"
echo "=============================="

BASE_URL="http://localhost:8080"
COOKIE_FILE="test_session.txt"

# 1. Fazer login como admin
echo "1. Fazendo login..."
LOGIN_RESPONSE=$(curl -s -c "$COOKIE_FILE" -X POST \
    -d "email=admin@academia.com&password=admin123" \
    "$BASE_URL/login")

if echo "$LOGIN_RESPONSE" | grep -q "Dashboard\|Bem-vindo"; then
    echo "✅ Login realizado com sucesso"
else
    echo "❌ Falha no login"
    exit 1
fi

# 2. Testar acesso a /aula/create
echo ""
echo "2. Testando /aula/create..."
CREATE_RESPONSE=$(curl -s -b "$COOKIE_FILE" "$BASE_URL/aula/create")

if echo "$CREATE_RESPONSE" | grep -q "Controller not found"; then
    echo "❌ ERRO: Controller not found"
    echo "Resposta:"
    echo "$CREATE_RESPONSE" | head -10
elif echo "$CREATE_RESPONSE" | grep -q "Nova Aula"; then
    echo "✅ SUCESSO: Página acessível"
    echo "Título encontrado: Nova Aula"
elif echo "$CREATE_RESPONSE" | grep -q "Fatal error\|Parse error"; then
    echo "❌ ERRO PHP encontrado:"
    echo "$CREATE_RESPONSE" | grep -A 5 -B 5 "Fatal error\|Parse error"
else
    echo "⚠️ RESPOSTA INESPERADA:"
    echo "$CREATE_RESPONSE" | head -20
fi

# 3. Testar também com dupla barra
echo ""
echo "3. Testando //aula/create..."
CREATE_RESPONSE2=$(curl -s -b "$COOKIE_FILE" "$BASE_URL//aula/create")

if echo "$CREATE_RESPONSE2" | grep -q "Controller not found"; then
    echo "❌ ERRO: Controller not found"
elif echo "$CREATE_RESPONSE2" | grep -q "Nova Aula"; then
    echo "✅ SUCESSO: Dupla barra corrigida"
else
    echo "⚠️ Resposta com dupla barra diferente"
fi

# Limpar
rm -f "$COOKIE_FILE"

echo ""
echo "🏁 Teste concluído!"
