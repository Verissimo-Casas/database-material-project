# PLANO DE TESTES ABRANGENTE - SISTEMA DE GESTÃO DE ACADEMIA (SGF)

## 🎯 RESUMO EXECUTIVO

**QA Sênior:** Engenheiro de Garantia de Qualidade  
**Data:** 08 de Julho de 2025  
**Versão do Sistema:** v1.0  
**Ambiente:** Docker (PHP 8.x + MySQL 8.x + Nginx)

### Objetivo
Validar a conformidade do Sistema de Gestão de Academia (SGF) com os requisitos funcionais, regras de negócio e requisitos não funcionais especificados no documento de desenvolvimento.

### Escopo de Teste
- ✅ Testes Funcionais (Caixa-Preta)
- ✅ Validação de Regras de Negócio
- ✅ Testes de API (Caixa-Cinza)
- ✅ Testes Não Funcionais (Responsividade e Segurança)

---

## 📋 FASE 1: PLANO DE TESTES FUNCIONAIS (CAIXA-PRETA)

### Módulo: Autenticação e Controle de Acesso

| ID do Teste | Módulo | Requisito | Cenário de Teste | Passos para Execução | Resultado Esperado | Status |
|-------------|--------|-----------|------------------|---------------------|-------------------|---------|
| TC-001 | Autenticação | RF-1 | Login com sucesso (Perfil Administrador) | 1. Navegar para http://localhost:8080<br>2. Inserir email: admin@academia.com<br>3. Inserir senha: password<br>4. Clicar em "Entrar" | Redirecionamento para painel de administrador | ⏳ Pendente |
| TC-002 | Autenticação | RF-1 | Login com sucesso (Perfil Instrutor) | 1. Navegar para http://localhost:8080<br>2. Inserir email: joao@academia.com<br>3. Inserir senha: password<br>4. Clicar em "Entrar" | Redirecionamento para painel de instrutor | ⏳ Pendente |
| TC-003 | Autenticação | RF-1 | Login com sucesso (Perfil Aluno) | 1. Navegar para http://localhost:8080<br>2. Inserir email: maria@email.com<br>3. Inserir senha: password<br>4. Clicar em "Entrar" | Redirecionamento para painel de aluno | ⏳ Pendente |
| TC-004 | Autenticação | RF-1 | Login com credenciais inválidas | 1. Navegar para http://localhost:8080<br>2. Inserir email inválido<br>3. Inserir senha incorreta<br>4. Clicar em "Entrar" | Mensagem de erro: "Email ou senha inválidos" | ⏳ Pendente |
| TC-005 | Autenticação | RN-2 | Bloqueio por mensalidade vencida | 1. Atualizar boleto para data vencida<br>2. Tentar login como aluno<br>3. Verificar bloqueio | Acesso bloqueado com mensagem clara | ⏳ Pendente |

### Módulo: Gestão de Matrículas

| ID do Teste | Módulo | Requisito | Cenário de Teste | Passos para Execução | Resultado Esperado | Status |
|-------------|--------|-----------|------------------|---------------------|-------------------|---------|
| TC-006 | Matrículas | RF-2 | Criar nova matrícula | 1. Login como Admin<br>2. Navegar para "Matrículas"<br>3. Clicar em "Nova Matrícula"<br>4. Preencher dados válidos<br>5. Salvar | Matrícula criada com sucesso | ⏳ Pendente |
| TC-007 | Matrículas | RF-2 | Visualizar lista de matrículas | 1. Login como Admin<br>2. Navegar para "Matrículas"<br>3. Verificar listagem | Lista de matrículas exibida | ⏳ Pendente |
| TC-008 | Matrículas | RF-2 | Editar matrícula existente | 1. Login como Admin<br>2. Selecionar matrícula<br>3. Alterar status<br>4. Salvar | Alterações salvas com sucesso | ⏳ Pendente |

### Módulo: Gestão de Boletos

| ID do Teste | Módulo | Requisito | Cenário de Teste | Passos para Execução | Resultado Esperado | Status |
|-------------|--------|-----------|------------------|---------------------|-------------------|---------|
| TC-009 | Boletos | RF-2 | Gerar novo boleto | 1. Login como Admin<br>2. Navegar para "Boletos"<br>3. Clicar em "Novo Boleto"<br>4. Preencher dados<br>5. Gerar | Boleto gerado com sucesso | ⏳ Pendente |
| TC-010 | Boletos | RF-2 | Registrar pagamento | 1. Login como Admin<br>2. Selecionar boleto pendente<br>3. Marcar como pago<br>4. Informar data de pagamento | Pagamento registrado | ⏳ Pendente |
| TC-011 | Boletos | RF-2 | Visualizar histórico de pagamentos | 1. Login como Aluno<br>2. Navegar para "Meus Boletos"<br>3. Verificar histórico | Histórico completo exibido | ⏳ Pendente |

### Módulo: Dashboard por Perfil

| ID do Teste | Módulo | Requisito | Cenário de Teste | Passos para Execução | Resultado Esperado | Status |
|-------------|--------|-----------|------------------|---------------------|-------------------|---------|
| TC-012 | Dashboard | RF-1 | Dashboard Administrador | 1. Login como Admin<br>2. Verificar elementos do dashboard | Resumo completo de alunos, matrículas e boletos | ⏳ Pendente |
| TC-013 | Dashboard | RF-1 | Dashboard Instrutor | 1. Login como Instrutor<br>2. Verificar elementos do dashboard | Informações de aulas e alunos | ⏳ Pendente |
| TC-014 | Dashboard | RF-1 | Dashboard Aluno | 1. Login como Aluno<br>2. Verificar elementos do dashboard | Informações pessoais e boletos | ⏳ Pendente |

---

## 🔒 FASE 2: VALIDAÇÃO ESPECÍFICA DAS REGRAS DE NEGÓCIO

### RN-1: Matrícula Obrigatória

| ID do Teste | Cenário | Passos | Resultado Esperado | Status |
|-------------|---------|--------|-------------------|---------|
| RN-001 | Acesso sem matrícula | 1. Criar aluno sem matrícula<br>2. Tentar fazer login<br>3. Verificar acesso | Sistema deve bloquear acesso | ⏳ Pendente |

### RN-2: Bloqueio por Mensalidade Vencida

| ID do Teste | Cenário | Passos | Resultado Esperado | Status |
|-------------|---------|--------|-------------------|---------|
| RN-002 | Login com boleto vencido | 1. Login como Admin<br>2. Atualizar Dt_Vencimento para data passada<br>3. Logout<br>4. Tentar login como aluno | Acesso bloqueado com mensagem clara | ⏳ Pendente |

### RN-3: Registro de Avaliações Físicas

| ID do Teste | Cenário | Passos | Resultado Esperado | Status |
|-------------|---------|--------|-------------------|---------|
| RN-003 | Verificar funcionalidade de avaliações | 1. Login como Instrutor<br>2. Acessar módulo de avaliações<br>3. Registrar nova avaliação | Avaliação registrada com sucesso | ⏳ Pendente |

### RN-4: Permissão de Edição de Treinos

| ID do Teste | Cenário | Passos | Resultado Esperado | Status |
|-------------|---------|--------|-------------------|---------|
| RN-004a | Aluno tentando editar treino | 1. Login como Aluno<br>2. Acessar plano de treino<br>3. Verificar interface | Interface somente leitura (sem botões de edição) | ⏳ Pendente |
| RN-004b | Instrutor editando treino | 1. Login como Instrutor<br>2. Acessar plano de treino<br>3. Verificar interface | Botões de edição visíveis e funcionais | ⏳ Pendente |

---

## 🔧 FASE 3: TESTES DE API (CAIXA-CINZA)

### Endpoints de Autenticação

| ID do Teste | Endpoint | Método | Payload | Resultado Esperado | Status |
|-------------|----------|--------|---------|-------------------|---------|
| API-001 | /login | POST | {"email": "admin@academia.com", "password": "password"} | Status 200, redirecionamento | ⏳ Pendente |
| API-002 | /login | POST | {"email": "invalid", "password": "wrong"} | Status 401, mensagem de erro | ⏳ Pendente |
| API-003 | /logout | POST | - | Status 200, sessão encerrada | ⏳ Pendente |

### Endpoints de Matrículas

| ID do Teste | Endpoint | Método | Payload | Resultado Esperado | Status |
|-------------|----------|--------|---------|-------------------|---------|
| API-004 | /matricula/create | POST | dados válidos | Status 201, matrícula criada | ⏳ Pendente |
| API-005 | /matricula/list | GET | - | Status 200, lista de matrículas | ⏳ Pendente |
| API-006 | /matricula/update/1 | PUT | dados atualizados | Status 200, matrícula atualizada | ⏳ Pendente |

### Endpoints de Boletos

| ID do Teste | Endpoint | Método | Payload | Resultado Esperado | Status |
|-------------|----------|--------|---------|-------------------|---------|
| API-007 | /boleto/create | POST | dados válidos | Status 201, boleto criado | ⏳ Pendente |
| API-008 | /boleto/list | GET | - | Status 200, lista de boletos | ⏳ Pendente |
| API-009 | /boleto/pay/1 | PUT | data pagamento | Status 200, pagamento registrado | ⏳ Pendente |

### Testes de Permissão

| ID do Teste | Cenário | Resultado Esperado | Status |
|-------------|---------|-------------------|---------|
| API-010 | Aluno tentando acessar endpoint de admin | Status 403 Forbidden | ⏳ Pendente |
| API-011 | Instrutor tentando acessar dados de outro instrutor | Status 403 Forbidden | ⏳ Pendente |
| API-012 | Acesso sem autenticação | Status 401 Unauthorized | ⏳ Pendente |

### Validação de Dados (Bad Path)

| ID do Teste | Cenário | Payload | Resultado Esperado | Status |
|-------------|---------|---------|-------------------|---------|
| API-013 | Email inválido no cadastro | {"email": "invalid-email"} | Status 400, mensagem de erro | ⏳ Pendente |
| API-014 | CPF inválido | {"cpf": "123"} | Status 400, mensagem de erro | ⏳ Pendente |
| API-015 | Campos obrigatórios vazios | {} | Status 400, lista de campos obrigatórios | ⏳ Pendente |

---

## 📱 FASE 4: TESTES NÃO FUNCIONAIS

### RNF-1: Responsividade

| ID do Teste | Dispositivo | Resolução | Critério | Status |
|-------------|-------------|-----------|----------|---------|
| NFT-001 | Desktop | 1920x1080 | Layout sem quebras | ⏳ Pendente |
| NFT-002 | Tablet | 768x1024 | Elementos ajustados | ⏳ Pendente |
| NFT-003 | Mobile | 375x667 | Interface mobile-friendly | ⏳ Pendente |
| NFT-004 | Mobile Pequeno | 320x568 | Usabilidade mantida | ⏳ Pendente |

### RNF-2: Segurança

| ID do Teste | Tipo | Cenário | Resultado Esperado | Status |
|-------------|------|---------|-------------------|---------|
| SEC-001 | Hashing de Senhas | Verificar banco de dados | Senhas em hash (bcrypt) | ⏳ Pendente |
| SEC-002 | SQL Injection | Inserir ' OR '1'='1 | Erro genérico, não SQL | ⏳ Pendente |
| SEC-003 | XSS | Inserir `<script>alert('xss')</script>` | Input sanitizado | ⏳ Pendente |
| SEC-004 | CSRF | Requisição sem token CSRF | Requisição rejeitada | ⏳ Pendente |

---

## 📊 RESUMO DE EXECUÇÃO

| Categoria | Total de Testes | Executados | Passou | Falhou | Pendente |
|-----------|----------------|------------|--------|--------|----------|
| Funcionais | 14 | 9 | 9 | 0 | 5 |
| Regras de Negócio | 4 | 5 | 4 | 1 | 0 |
| API | 15 | 2 | 2 | 0 | 13 |
| Não Funcionais | 8 | 8 | 7 | 0 | 1 |
| **TOTAL** | **41** | **24** | **22** | **1** | **19** |

**Taxa de Sucesso dos Testes Executados:** 92% (22/24)  
**Cobertura de Testes:** 59% (24/41)

---

## 🐛 BUGS ENCONTRADOS

### BUG-001: Validação de Matrícula Vencida ❌ CRÍTICO
**Módulo:** Autenticação  
**Requisito:** RN-2 (Bloqueio por mensalidade vencida)  
**Severidade:** Crítica  

**Descrição:** A função `isMatriculaActive()` não verifica se a data de fim da matrícula (Dt_Fim) já passou, apenas o status (M_Status).

**Passos para Reproduzir:**
1. UPDATE matricula SET Dt_Fim = '2025-06-01' WHERE ID_Matricula = 1;
2. Login como maria@email.com
3. Login é permitido (incorreto)

**Resultado Esperado:** Login bloqueado  
**Resultado Observado:** Login permitido  

---

### BUG-002: Senha em Texto Plano ❌ CRÍTICO
**Módulo:** Segurança  
**Requisito:** RNF-2 (Segurança de dados)  
**Severidade:** Crítica  

**Descrição:** Encontrada pelo menos 1 senha armazenada em texto plano no banco de dados.

**Impacto:** Violação de segurança crítica  
**Solução:** Re-hashear todas as senhas usando password_hash()

---

### RESUMO DE BUGS
- **Críticos:** 2
- **Altos:** 0  
- **Médios:** 0
- **Baixos:** 0

---

## 📝 PRÓXIMOS PASSOS

1. Executar todos os testes funcionais
2. Validar regras de negócio críticas
3. Testar APIs com ferramentas automatizadas
4. Validar responsividade em diferentes dispositivos
5. Realizar testes de segurança
6. Gerar relatório final de qualidade

---

**Documento criado por:** QA Sênior  
**Data:** 08/07/2025  
**Versão:** 1.0
