# 🧪 PLANO COMPLETO DE TESTES DE API - SISTEMA ACADEMIA

## 📊 Visão Geral

Este documento apresenta um plano abrangente de testes para validar todos os endpoints da API do Sistema Academia usando curl. Os testes cobrem cenários de sucesso, falha por permissão e falha por dados inválidos.

---

## 🏗️ Metodologia de Teste

### 1. Login e Captura de Sessão
```bash
# Login e salvamento de cookies de sessão
curl -c cookie.txt -X POST \
     -H "Content-Type: application/x-www-form-urlencoded" \
     -d "email=admin@academia.com&password=password" \
     http://localhost:8080/auth/login

# Verificar se o login foi bem-sucedido
curl -b cookie.txt http://localhost:8080/dashboard
```

### 2. Requisições Autenticadas
```bash
# Usar cookies salvos para chamadas autenticadas
curl -b cookie.txt http://localhost:8080/boleto
```

---

## 📋 PLANO DE TESTES DETALHADO

### 🔓 MÓDULO: AUTENTICAÇÃO

| Rota | Método | Descrição do Teste | Status HTTP Esperado | Breve Descrição da Resposta |
|------|--------|-------------------|---------------------|------------------------------|
| `/auth/login` | GET | Exibir página de login | 200 OK | HTML da página de login |
| `/auth/login` | POST | Login com credenciais válidas (admin) | 302 Redirect | Redirecionamento para dashboard |
| `/auth/login` | POST | Login com credenciais válidas (instrutor) | 302 Redirect | Redirecionamento para dashboard |
| `/auth/login` | POST | Login com credenciais válidas (aluno) | 302 Redirect | Redirecionamento para dashboard |
| `/auth/login` | POST | Login com email inválido | 200 OK | HTML com mensagem de erro |
| `/auth/login` | POST | Login com senha inválida | 200 OK | HTML com mensagem de erro |
| `/auth/login` | POST | Login sem dados | 200 OK | HTML com validação de campos |
| `/auth/register` | GET | Exibir página de registro | 200 OK | HTML da página de registro |
| `/auth/register` | POST | Registro com dados válidos | 200 OK | HTML com mensagem de sucesso |
| `/auth/register` | POST | Registro com email duplicado | 200 OK | HTML com mensagem de erro |
| `/auth/register` | POST | Registro com CPF inválido | 200 OK | HTML com mensagem de erro |
| `/auth/logout` | GET | Encerrar sessão do usuário | 302 Redirect | Redirecionamento para login |

### 🏠 MÓDULO: DASHBOARD

| Rota | Método | Descrição do Teste | Status HTTP Esperado | Breve Descrição da Resposta |
|------|--------|-------------------|---------------------|------------------------------|
| `/dashboard` | GET | Acesso sem autenticação | 302 Redirect | Redirecionamento para login |
| `/dashboard` | GET | Dashboard do administrador | 200 OK | HTML do painel administrativo |
| `/dashboard` | GET | Dashboard do instrutor | 200 OK | HTML do painel do instrutor |
| `/dashboard` | GET | Dashboard do aluno | 200 OK | HTML do painel do aluno |

### 💰 MÓDULO: BOLETOS

| Rota | Método | Descrição do Teste | Status HTTP Esperado | Breve Descrição da Resposta |
|------|--------|-------------------|---------------------|------------------------------|
| `/boleto` | GET | Listar boletos sem autenticação | 302 Redirect | Redirecionamento para login |
| `/boleto` | GET | Listar todos os boletos (admin) | 200 OK | HTML com lista completa de boletos |
| `/boleto` | GET | Listar boletos próprios (aluno) | 200 OK | HTML com boletos do aluno |
| `/boleto` | GET | Listar boletos (instrutor) | 200 OK | HTML com lista de boletos |
| `/boleto/create` | GET | Formulário criar boleto (admin) | 200 OK | HTML do formulário de criação |
| `/boleto/create` | GET | Tentar acessar criação (instrutor) | 302 Redirect | Redirecionamento para dashboard |
| `/boleto/create` | GET | Tentar acessar criação (aluno) | 302 Redirect | Redirecionamento para dashboard |
| `/boleto/create` | POST | Criar boleto com dados válidos (admin) | 302 Redirect | Redirecionamento após criação |
| `/boleto/create` | POST | Criar boleto sem permissão (instrutor) | 302 Redirect | Redirecionamento para dashboard |
| `/boleto/create` | POST | Criar boleto com dados inválidos | 200 OK | HTML com mensagem de erro |
| `/boleto/create/1` | GET | Criar boleto para matrícula específica | 200 OK | HTML do formulário pré-preenchido |
| `/boleto/markAsPaid/1` | POST | Marcar boleto como pago (admin) | 302 Redirect | Redirecionamento após atualização |
| `/boleto/markAsPaid/1` | POST | Marcar como pago sem permissão | 302 Redirect | Redirecionamento para dashboard |
| `/boleto/markAsPaid/999` | POST | Marcar boleto inexistente como pago | 302 Redirect | Redirecionamento (não encontrado) |

### 🎓 MÓDULO: MATRÍCULAS

| Rota | Método | Descrição do Teste | Status HTTP Esperado | Breve Descrição da Resposta |
|------|--------|-------------------|---------------------|------------------------------|
| `/matricula` | GET | Listar matrículas sem autenticação | 302 Redirect | Redirecionamento para login |
| `/matricula` | GET | Listar matrículas (admin) | 200 OK | HTML com lista de matrículas |
| `/matricula` | GET | Tentar listar matrículas (instrutor) | 302 Redirect | Redirecionamento para dashboard |
| `/matricula` | GET | Tentar listar matrículas (aluno) | 302 Redirect | Redirecionamento para dashboard |
| `/matricula/create` | GET | Formulário criar matrícula (admin) | 200 OK | HTML do formulário de criação |
| `/matricula/create` | GET | Tentar acessar criação (instrutor) | 302 Redirect | Redirecionamento para dashboard |
| `/matricula/create` | GET | Tentar acessar criação (aluno) | 302 Redirect | Redirecionamento para dashboard |
| `/matricula/create` | POST | Criar matrícula com dados válidos | 302 Redirect | Redirecionamento após criação |
| `/matricula/create` | POST | Criar matrícula sem permissão | 302 Redirect | Redirecionamento para dashboard |
| `/matricula/create` | POST | Criar matrícula com dados inválidos | 200 OK | HTML com mensagem de erro |
| `/matricula/toggleStatus/1` | POST | Alternar status matrícula (admin) | 302 Redirect | Redirecionamento após atualização |
| `/matricula/toggleStatus/1` | POST | Alternar status sem permissão | 302 Redirect | Redirecionamento para dashboard |
| `/matricula/toggleStatus/999` | POST | Alternar status matrícula inexistente | 302 Redirect | Redirecionamento (não encontrado) |

---

## 🚀 COMANDOS CURL PARA EXECUÇÃO

### 🔧 Preparação do Ambiente
```bash
# Criar diretório para cookies e logs
mkdir -p test_results
cd test_results

# Definir variáveis
BASE_URL="http://localhost:8080"
COOKIE_FILE="session_cookies.txt"
```

### 🧪 TESTES DE AUTENTICAÇÃO

```bash
# Teste 1: Página de login
curl -s -w "Status: %{http_code}\n" \
     -o login_page.html \
     "$BASE_URL/auth/login"

# Teste 2: Login com credenciais válidas (Admin)
curl -s -w "Status: %{http_code}\n" \
     -c "$COOKIE_FILE" \
     -X POST \
     -H "Content-Type: application/x-www-form-urlencoded" \
     -d "email=admin@academia.com&password=password" \
     -o login_response.html \
     "$BASE_URL/auth/login"

# Teste 3: Login com credenciais inválidas
curl -s -w "Status: %{http_code}\n" \
     -X POST \
     -H "Content-Type: application/x-www-form-urlencoded" \
     -d "email=admin@academia.com&password=senhaerrada" \
     -o login_invalid.html \
     "$BASE_URL/auth/login"

# Teste 4: Página de registro
curl -s -w "Status: %{http_code}\n" \
     -o register_page.html \
     "$BASE_URL/auth/register"
```

### 🏠 TESTES DE DASHBOARD

```bash
# Teste 5: Dashboard sem autenticação
curl -s -w "Status: %{http_code}\n" \
     -o dashboard_no_auth.html \
     "$BASE_URL/dashboard"

# Teste 6: Dashboard com autenticação (usar cookie do login anterior)
curl -s -w "Status: %{http_code}\n" \
     -b "$COOKIE_FILE" \
     -o dashboard_admin.html \
     "$BASE_URL/dashboard"
```

### 💰 TESTES DE BOLETOS

```bash
# Teste 7: Lista de boletos sem autenticação
curl -s -w "Status: %{http_code}\n" \
     -o boleto_no_auth.html \
     "$BASE_URL/boleto"

# Teste 8: Lista de boletos com autenticação de admin
curl -s -w "Status: %{http_code}\n" \
     -b "$COOKIE_FILE" \
     -o boleto_list_admin.html \
     "$BASE_URL/boleto"

# Teste 9: Formulário de criação de boleto (admin)
curl -s -w "Status: %{http_code}\n" \
     -b "$COOKIE_FILE" \
     -o boleto_create_form.html \
     "$BASE_URL/boleto/create"

# Teste 10: Criar boleto com dados válidos
curl -s -w "Status: %{http_code}\n" \
     -b "$COOKIE_FILE" \
     -X POST \
     -H "Content-Type: application/x-www-form-urlencoded" \
     -d "forma_pagamento=Boleto&valor=50.00&dt_vencimento=2025-08-08&id_matricula=1" \
     -o boleto_create_result.html \
     "$BASE_URL/boleto/create"

# Teste 11: Marcar boleto como pago
curl -s -w "Status: %{http_code}\n" \
     -b "$COOKIE_FILE" \
     -X POST \
     -o boleto_mark_paid.html \
     "$BASE_URL/boleto/markAsPaid/1"
```

### 🎓 TESTES DE MATRÍCULAS

```bash
# Teste 12: Lista de matrículas sem autenticação
curl -s -w "Status: %{http_code}\n" \
     -o matricula_no_auth.html \
     "$BASE_URL/matricula"

# Teste 13: Lista de matrículas com autenticação de admin
curl -s -w "Status: %{http_code}\n" \
     -b "$COOKIE_FILE" \
     -o matricula_list_admin.html \
     "$BASE_URL/matricula"

# Teste 14: Formulário de criação de matrícula
curl -s -w "Status: %{http_code}\n" \
     -b "$COOKIE_FILE" \
     -o matricula_create_form.html \
     "$BASE_URL/matricula/create"

# Teste 15: Criar matrícula com dados válidos
curl -s -w "Status: %{http_code}\n" \
     -b "$COOKIE_FILE" \
     -X POST \
     -H "Content-Type: application/x-www-form-urlencoded" \
     -d "cpf=98765432100&nome=Novo%20Aluno&dt_nasc=1995-05-15&endereco=Rua%20Nova&contato=11988887777&email=novo@aluno.com&senha=password123" \
     -o matricula_create_result.html \
     "$BASE_URL/matricula/create"

# Teste 16: Alternar status de matrícula
curl -s -w "Status: %{http_code}\n" \
     -b "$COOKIE_FILE" \
     -X POST \
     -o matricula_toggle_status.html \
     "$BASE_URL/matricula/toggleStatus/1"
```

### 🔄 TESTES COM DIFERENTES PERFIS

```bash
# Login como Instrutor
curl -s -w "Status: %{http_code}\n" \
     -c "cookie_instrutor.txt" \
     -X POST \
     -H "Content-Type: application/x-www-form-urlencoded" \
     -d "email=joao@academia.com&password=password" \
     -o login_instrutor.html \
     "$BASE_URL/auth/login"

# Teste 17: Instrutor tentando acessar criação de boleto (deve falhar)
curl -s -w "Status: %{http_code}\n" \
     -b "cookie_instrutor.txt" \
     -o instrutor_boleto_create.html \
     "$BASE_URL/boleto/create"

# Login como Aluno
curl -s -w "Status: %{http_code}\n" \
     -c "cookie_aluno.txt" \
     -X POST \
     -H "Content-Type: application/x-www-form-urlencoded" \
     -d "email=maria@email.com&password=password" \
     -o login_aluno.html \
     "$BASE_URL/auth/login"

# Teste 18: Aluno tentando acessar matrículas (deve falhar)
curl -s -w "Status: %{http_code}\n" \
     -b "cookie_aluno.txt" \
     -o aluno_matricula_access.html \
     "$BASE_URL/matricula"
```

### 🧹 LIMPEZA

```bash
# Logout e limpeza
curl -s -b "$COOKIE_FILE" "$BASE_URL/auth/logout"
rm -f *.txt
```

---

## 📊 EXEMPLO DE RELATÓRIO ESPERADO

### ✅ Cenários de Sucesso

```
✅ GET /auth/login - 200 OK
   Página de login carregada corretamente

✅ POST /auth/login (admin) - 302 Redirect
   Login bem-sucedido, redirecionado para dashboard

✅ GET /dashboard (admin autenticado) - 200 OK
   Dashboard administrativo exibido

✅ GET /boleto (admin autenticado) - 200 OK
   Lista completa de boletos exibida

✅ POST /boleto/create (admin) - 302 Redirect
   Boleto criado com sucesso
```

### ❌ Cenários de Falha por Permissão

```
❌ GET /dashboard (não autenticado) - 302 Redirect
   Redirecionado para login (comportamento esperado)

❌ GET /boleto/create (instrutor) - 302 Redirect
   Acesso negado, redirecionado para dashboard

❌ GET /matricula (aluno) - 302 Redirect
   Acesso negado, redirecionado para dashboard
```

### ⚠️ Cenários de Falha por Dados Inválidos

```
⚠️ POST /auth/login (senha inválida) - 200 OK
   Retorna página de login com mensagem de erro

⚠️ POST /boleto/create (dados incompletos) - 200 OK
   Retorna formulário com mensagens de validação
```

---

## 🎯 EXECUÇÃO DOS TESTES

### Método 1: Script Automatizado
```bash
# Executar todos os testes automaticamente
./test_api_endpoints.sh
```

### Método 2: Execução Manual
```bash
# Executar comandos curl individualmente
# Seguir a sequência documentada acima
```

### Método 3: Validação por Módulo
```bash
# Testar apenas autenticação
./test_api_endpoints.sh --module auth

# Testar apenas boletos
./test_api_endpoints.sh --module boleto
```

---

## 📝 OBSERVAÇÕES IMPORTANTES

1. **CSRF Protection**: Alguns testes podem falhar se o CSRF estiver habilitado
2. **Cookies de Sessão**: Necessários para testes de rotas protegidas
3. **Dados de Teste**: Usar dados consistentes com o banco de dados
4. **Status Codes**: Validar tanto código quanto conteúdo da resposta
5. **Limpeza**: Sempre limpar cookies e arquivos temporários após testes

---

## 🔍 VALIDAÇÃO DE RESULTADOS

Cada teste deve validar:
- ✅ **Status HTTP correto**
- ✅ **Redirecionamentos apropriados**
- ✅ **Conteúdo da resposta**
- ✅ **Headers de segurança**
- ✅ **Persistência de sessão**

---

*Documento gerado para Sistema Academia - Testes de API v1.0*
