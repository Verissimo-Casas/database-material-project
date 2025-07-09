#!/bin/bash

# =====================================================
# COMANDOS CURL PARA TESTES MANUAIS - SISTEMA ACADEMIA
# Coleção completa de comandos prontos para uso
# =====================================================

BASE_URL="http://localhost:8080"
COOKIE_FILE="manual_test_cookies.txt"

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Função para executar comando com título
run_curl() {
    local title="$1"
    local command="$2"
    local description="$3"
    
    echo -e "\n${BLUE}$title${NC}"
    echo -e "${YELLOW}Descrição:${NC} $description"
    echo -e "${CYAN}Comando:${NC} $command"
    echo -e "${GREEN}Executando...${NC}"
    
    # Executar comando e mostrar resultado
    eval $command
    local exit_code=$?
    
    if [ $exit_code -eq 0 ]; then
        echo -e "${GREEN}✅ Comando executado com sucesso${NC}"
    else
        echo -e "${RED}❌ Erro na execução (código: $exit_code)${NC}"
    fi
    
    echo -e "${PURPLE}─────────────────────────────────────────${NC}"
}

# Função para mostrar menu
show_menu() {
    clear
    echo -e "${PURPLE}╔══════════════════════════════════════════════╗${NC}"
    echo -e "${PURPLE}║           TESTES MANUAIS DE API              ║${NC}"
    echo -e "${PURPLE}║            SISTEMA ACADEMIA                  ║${NC}"
    echo -e "${PURPLE}╚══════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "${CYAN}Escolha uma categoria de testes:${NC}"
    echo ""
    echo -e "${GREEN}1.${NC} 🌐 Páginas Públicas"
    echo -e "${GREEN}2.${NC} 🔐 Autenticação"
    echo -e "${GREEN}3.${NC} 📊 Dashboard"
    echo -e "${GREEN}4.${NC} 💰 Boletos"
    echo -e "${GREEN}5.${NC} 📝 Matrículas"
    echo -e "${GREEN}6.${NC} 🛡️  Testes de Segurança"
    echo -e "${GREEN}7.${NC} ❌ Testes de Erro"
    echo -e "${GREEN}8.${NC} 🔧 Utilitários"
    echo -e "${GREEN}9.${NC} 🚀 Executar Todos os Testes"
    echo -e "${GREEN}0.${NC} 🚪 Sair"
    echo ""
    echo -e "${YELLOW}Digite sua escolha [0-9]:${NC} "
}

# Testes de páginas públicas
test_public_pages() {
    echo -e "${PURPLE}🌐 TESTANDO PÁGINAS PÚBLICAS${NC}"
    echo -e "${PURPLE}=============================${NC}"
    
    run_curl "1. Homepage" \
        "curl -i $BASE_URL/" \
        "Acessar página inicial (deve redirecionar para login)"
    
    run_curl "2. Página de Login" \
        "curl -i $BASE_URL/auth/login" \
        "Acessar formulário de login"
    
    run_curl "3. Página de Registro" \
        "curl -i $BASE_URL/auth/register" \
        "Acessar formulário de registro"
    
    echo -e "\n${GREEN}Testes de páginas públicas concluídos!${NC}"
}

# Testes de autenticação
test_authentication() {
    echo -e "${PURPLE}🔐 TESTANDO AUTENTICAÇÃO${NC}"
    echo -e "${PURPLE}========================${NC}"
    
    echo -e "\n${YELLOW}Limpar cookies anteriores...${NC}"
    rm -f "$COOKIE_FILE"
    
    run_curl "1. Login Admin" \
        "curl -X POST -d 'email=admin@academia.com&password=password' -c $COOKIE_FILE -i $BASE_URL/auth/login" \
        "Login como administrador"
    
    run_curl "2. Verificar Autenticação" \
        "curl -b $COOKIE_FILE -i $BASE_URL/dashboard" \
        "Acessar dashboard após login"
    
    run_curl "3. Logout" \
        "curl -b $COOKIE_FILE -i $BASE_URL/auth/logout" \
        "Fazer logout do sistema"
    
    run_curl "4. Login Instrutor" \
        "curl -X POST -d 'email=joao@academia.com&password=password' -c $COOKIE_FILE -i $BASE_URL/auth/login" \
        "Login como instrutor"
    
    run_curl "5. Login Aluno" \
        "curl -X POST -d 'email=maria@email.com&password=password' -c $COOKIE_FILE -i $BASE_URL/auth/login" \
        "Login como aluno"
    
    echo -e "\n${GREEN}Testes de autenticação concluídos!${NC}"
}

# Testes de dashboard
test_dashboard() {
    echo -e "${PURPLE}📊 TESTANDO DASHBOARD${NC}"
    echo -e "${PURPLE}====================${NC}"
    
    # Garantir que está logado como admin
    run_curl "0. Login Admin (preparação)" \
        "curl -X POST -d 'email=admin@academia.com&password=password' -c $COOKIE_FILE -s $BASE_URL/auth/login" \
        "Fazer login para os testes"
    
    run_curl "1. Dashboard Principal" \
        "curl -b $COOKIE_FILE -i $BASE_URL/dashboard" \
        "Acessar dashboard principal"
    
    run_curl "2. Dashboard Index" \
        "curl -b $COOKIE_FILE -i $BASE_URL/dashboard/index" \
        "Acessar dashboard via index"
    
    echo -e "\n${YELLOW}Testando dashboards específicos por tipo de usuário...${NC}"
    
    # Teste dashboard admin
    run_curl "3. Dashboard Admin" \
        "curl -b $COOKIE_FILE $BASE_URL/dashboard" \
        "Dashboard administrativo"
    
    # Login e teste instrutor
    run_curl "4. Login Instrutor para Dashboard" \
        "curl -X POST -d 'email=joao@academia.com&password=password' -c $COOKIE_FILE -s $BASE_URL/auth/login" \
        "Login como instrutor"
    
    run_curl "5. Dashboard Instrutor" \
        "curl -b $COOKIE_FILE $BASE_URL/dashboard" \
        "Dashboard do instrutor"
    
    # Login e teste aluno
    run_curl "6. Login Aluno para Dashboard" \
        "curl -X POST -d 'email=maria@email.com&password=password' -c $COOKIE_FILE -s $BASE_URL/auth/login" \
        "Login como aluno"
    
    run_curl "7. Dashboard Aluno" \
        "curl -b $COOKIE_FILE $BASE_URL/dashboard" \
        "Dashboard do aluno"
    
    echo -e "\n${GREEN}Testes de dashboard concluídos!${NC}"
}

# Testes de boletos
test_boletos() {
    echo -e "${PURPLE}💰 TESTANDO BOLETOS${NC}"
    echo -e "${PURPLE}==================${NC}"
    
    # Garantir que está logado como admin
    run_curl "0. Login Admin (preparação)" \
        "curl -X POST -d 'email=admin@academia.com&password=password' -c $COOKIE_FILE -s $BASE_URL/auth/login" \
        "Fazer login para os testes"
    
    run_curl "1. Listar Boletos" \
        "curl -b $COOKIE_FILE -i $BASE_URL/boleto" \
        "Listar todos os boletos"
    
    run_curl "2. Listar Boletos (Index)" \
        "curl -b $COOKIE_FILE -i $BASE_URL/boleto/index" \
        "Listar boletos via index"
    
    run_curl "3. Formulário Criar Boleto" \
        "curl -b $COOKIE_FILE -i $BASE_URL/boleto/create" \
        "Acessar formulário de criação de boleto"
    
    run_curl "4. Criar Boleto (GET com ID)" \
        "curl -b $COOKIE_FILE -i $BASE_URL/boleto/create/1" \
        "Criar boleto para matrícula ID 1"
    
    run_curl "5. Marcar Boleto como Pago" \
        "curl -X POST -b $COOKIE_FILE -i $BASE_URL/boleto/markAsPaid/1" \
        "Marcar boleto ID 1 como pago"
    
    echo -e "\n${YELLOW}Testando cenários de erro para boletos...${NC}"
    
    run_curl "6. Boleto Inexistente" \
        "curl -X POST -b $COOKIE_FILE -i $BASE_URL/boleto/markAsPaid/999" \
        "Tentar marcar boleto inexistente como pago"
    
    echo -e "\n${GREEN}Testes de boletos concluídos!${NC}"
}

# Testes de matrículas
test_matriculas() {
    echo -e "${PURPLE}📝 TESTANDO MATRÍCULAS${NC}"
    echo -e "${PURPLE}======================${NC}"
    
    # Garantir que está logado como admin
    run_curl "0. Login Admin (preparação)" \
        "curl -X POST -d 'email=admin@academia.com&password=password' -c $COOKIE_FILE -s $BASE_URL/auth/login" \
        "Fazer login para os testes"
    
    run_curl "1. Listar Matrículas" \
        "curl -b $COOKIE_FILE -i $BASE_URL/matricula" \
        "Listar todas as matrículas"
    
    run_curl "2. Listar Matrículas (Index)" \
        "curl -b $COOKIE_FILE -i $BASE_URL/matricula/index" \
        "Listar matrículas via index"
    
    run_curl "3. Formulário Criar Matrícula" \
        "curl -b $COOKIE_FILE -i $BASE_URL/matricula/create" \
        "Acessar formulário de criação de matrícula"
    
    run_curl "4. Alternar Status Matrícula" \
        "curl -X POST -b $COOKIE_FILE -i $BASE_URL/matricula/toggleStatus/1" \
        "Alternar status da matrícula ID 1"
    
    echo -e "\n${YELLOW}Testando cenários de erro para matrículas...${NC}"
    
    run_curl "5. Matrícula Inexistente" \
        "curl -X POST -b $COOKIE_FILE -i $BASE_URL/matricula/toggleStatus/999" \
        "Tentar alterar status de matrícula inexistente"
    
    echo -e "\n${GREEN}Testes de matrículas concluídos!${NC}"
}

# Testes de segurança
test_security() {
    echo -e "${PURPLE}🛡️ TESTANDO SEGURANÇA${NC}"
    echo -e "${PURPLE}=====================${NC}"
    
    echo -e "\n${YELLOW}Removendo cookies para simular usuário não autenticado...${NC}"
    rm -f "$COOKIE_FILE"
    
    run_curl "1. Dashboard Sem Auth" \
        "curl -i $BASE_URL/dashboard" \
        "Tentar acessar dashboard sem estar logado"
    
    run_curl "2. Boletos Sem Auth" \
        "curl -i $BASE_URL/boleto" \
        "Tentar acessar boletos sem estar logado"
    
    run_curl "3. Matrículas Sem Auth" \
        "curl -i $BASE_URL/matricula" \
        "Tentar acessar matrículas sem estar logado"
    
    run_curl "4. Criar Boleto Sem Auth" \
        "curl -i $BASE_URL/boleto/create" \
        "Tentar criar boleto sem estar logado"
    
    run_curl "5. Ação Admin Sem Auth" \
        "curl -X POST -i $BASE_URL/boleto/markAsPaid/1" \
        "Tentar ação administrativa sem estar logado"
    
    echo -e "\n${GREEN}Testes de segurança concluídos!${NC}"
}

# Testes de erro
test_errors() {
    echo -e "${PURPLE}❌ TESTANDO CENÁRIOS DE ERRO${NC}"
    echo -e "${PURPLE}=============================${NC}"
    
    run_curl "1. Rota Inexistente" \
        "curl -i $BASE_URL/rota/que/nao/existe" \
        "Acessar rota que não existe"
    
    run_curl "2. Controller Inexistente" \
        "curl -i $BASE_URL/controller_inexistente" \
        "Acessar controller que não existe"
    
    run_curl "3. Action Inexistente" \
        "curl -i $BASE_URL/boleto/acao_inexistente" \
        "Acessar ação que não existe"
    
    run_curl "4. Login com Email Inválido" \
        "curl -X POST -d 'email=usuario_inexistente@test.com&password=password' -i $BASE_URL/auth/login" \
        "Tentar login com email que não existe"
    
    run_curl "5. Login com Senha Incorreta" \
        "curl -X POST -d 'email=admin@academia.com&password=senha_errada' -i $BASE_URL/auth/login" \
        "Tentar login com senha incorreta"
    
    run_curl "6. Método HTTP Não Permitido" \
        "curl -X DELETE -i $BASE_URL/dashboard" \
        "Usar método HTTP não suportado"
    
    echo -e "\n${GREEN}Testes de erro concluídos!${NC}"
}

# Utilitários
test_utilities() {
    echo -e "${PURPLE}🔧 UTILITÁRIOS${NC}"
    echo -e "${PURPLE}==============${NC}"
    
    run_curl "1. Verificar Status do Serviço" \
        "curl -I $BASE_URL/" \
        "Verificar se o serviço está respondendo"
    
    run_curl "2. Informações do Servidor" \
        "curl -I $BASE_URL/auth/login" \
        "Obter cabeçalhos HTTP do servidor"
    
    run_curl "3. Teste de Conectividade" \
        "curl -s -o /dev/null -w 'HTTP Status: %{http_code}\nTempo Total: %{time_total}s\nTempo de Conexão: %{time_connect}s\n' $BASE_URL/" \
        "Medir tempos de resposta"
    
    echo -e "\n${YELLOW}Verificando arquivos de cookie:${NC}"
    if [ -f "$COOKIE_FILE" ]; then
        echo -e "${GREEN}Cookie file exists:${NC}"
        cat "$COOKIE_FILE"
    else
        echo -e "${RED}Nenhum arquivo de cookie encontrado${NC}"
    fi
    
    echo -e "\n${YELLOW}Verificando conectividade com Docker:${NC}"
    if command -v docker-compose &> /dev/null; then
        echo -e "${GREEN}Docker Compose disponível${NC}"
        docker-compose ps 2>/dev/null || echo -e "${RED}Erro ao verificar containers${NC}"
    else
        echo -e "${RED}Docker Compose não encontrado${NC}"
    fi
    
    echo -e "\n${GREEN}Utilitários concluídos!${NC}"
}

# Executar todos os testes
run_all_tests() {
    echo -e "${PURPLE}🚀 EXECUTANDO TODOS OS TESTES${NC}"
    echo -e "${PURPLE}==============================${NC}"
    
    test_public_pages
    sleep 2
    test_authentication
    sleep 2
    test_dashboard
    sleep 2
    test_boletos
    sleep 2
    test_matriculas
    sleep 2
    test_security
    sleep 2
    test_errors
    sleep 2
    test_utilities
    
    echo -e "\n${GREEN}🎉 TODOS OS TESTES CONCLUÍDOS!${NC}"
    echo -e "${CYAN}Arquivo de cookies usado: $COOKIE_FILE${NC}"
}

# Função principal
main() {
    # Verificar se o script foi chamado com parâmetro
    if [ $# -eq 1 ]; then
        case $1 in
            "public")
                test_public_pages
                ;;
            "auth")
                test_authentication
                ;;
            "dashboard")
                test_dashboard
                ;;
            "boletos")
                test_boletos
                ;;
            "matriculas")
                test_matriculas
                ;;
            "security")
                test_security
                ;;
            "errors")
                test_errors
                ;;
            "utils")
                test_utilities
                ;;
            "all")
                run_all_tests
                ;;
            "help"|"-h"|"--help")
                echo "Uso: $0 [categoria]"
                echo ""
                echo "Categorias disponíveis:"
                echo "  public      - Páginas públicas"
                echo "  auth        - Autenticação"
                echo "  dashboard   - Dashboard"
                echo "  boletos     - Boletos"
                echo "  matriculas  - Matrículas"
                echo "  security    - Segurança"
                echo "  errors      - Cenários de erro"
                echo "  utils       - Utilitários"
                echo "  all         - Todos os testes"
                echo ""
                echo "Exemplos:"
                echo "  $0 auth     # Testar apenas autenticação"
                echo "  $0 all      # Executar todos os testes"
                echo "  $0          # Menu interativo"
                exit 0
                ;;
            *)
                echo -e "${RED}Categoria inválida: $1${NC}"
                echo -e "${YELLOW}Use '$0 help' para ver as opções disponíveis${NC}"
                exit 1
                ;;
        esac
        return
    fi
    
    # Menu interativo
    while true; do
        show_menu
        read -r choice
        
        case $choice in
            1)
                test_public_pages
                read -p "Pressione Enter para continuar..."
                ;;
            2)
                test_authentication
                read -p "Pressione Enter para continuar..."
                ;;
            3)
                test_dashboard
                read -p "Pressione Enter para continuar..."
                ;;
            4)
                test_boletos
                read -p "Pressione Enter para continuar..."
                ;;
            5)
                test_matriculas
                read -p "Pressione Enter para continuar..."
                ;;
            6)
                test_security
                read -p "Pressione Enter para continuar..."
                ;;
            7)
                test_errors
                read -p "Pressione Enter para continuar..."
                ;;
            8)
                test_utilities
                read -p "Pressione Enter para continuar..."
                ;;
            9)
                run_all_tests
                read -p "Pressione Enter para continuar..."
                ;;
            0)
                echo -e "${GREEN}Saindo... Obrigado por usar o sistema de testes!${NC}"
                break
                ;;
            *)
                echo -e "${RED}Opção inválida. Digite um número de 0 a 9.${NC}"
                read -p "Pressione Enter para continuar..."
                ;;
        esac
    done
}

# Executar função principal
main "$@"
