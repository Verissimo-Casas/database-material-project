#!/bin/bash
# FILE: scripts/manage.sh

set -e

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Função para imprimir mensagens coloridas
print_message() {
    echo -e "${2}${1}${NC}"
}

# Função para verificar se Docker está rodando
check_docker() {
    if ! docker info > /dev/null 2>&1; then
        print_message "Docker não está rodando. Por favor, inicie o Docker primeiro." $RED
        exit 1
    fi
}

# Função para mostrar ajuda
show_help() {
    echo "Sistema de Gestão de Academia - Script de Gerenciamento"
    echo ""
    echo "Uso: $0 [comando]"
    echo ""
    echo "Comandos disponíveis:"
    echo "  start      - Iniciar todos os containers"
    echo "  stop       - Parar todos os containers"
    echo "  restart    - Reiniciar todos os containers"
    echo "  status     - Mostrar status dos containers"
    echo "  logs       - Mostrar logs dos containers"
    echo "  build      - Reconstruir as imagens"
    echo "  clean      - Limpar containers e volumes"
    echo "  db-shell   - Acessar shell do MySQL"
    echo "  app-shell  - Acessar shell da aplicação"
    echo "  backup     - Fazer backup do banco de dados"
    echo "  restore    - Restaurar backup do banco de dados"
    echo "  install    - Instalação inicial completa"
    echo "  help       - Mostrar esta ajuda"
}

# Função para iniciar containers
start_containers() {
    print_message "Iniciando containers..." $BLUE
    docker-compose up -d
    
    print_message "Aguardando inicialização dos serviços..." $YELLOW
    sleep 10
    
    print_message "Verificando status..." $BLUE
    docker-compose ps
    
    print_message "Sistema iniciado com sucesso!" $GREEN
    print_message "Acesse: http://localhost:8080" $GREEN
}

# Função para parar containers
stop_containers() {
    print_message "Parando containers..." $BLUE
    docker-compose down
    print_message "Containers parados!" $GREEN
}

# Função para reiniciar containers
restart_containers() {
    print_message "Reiniciando containers..." $BLUE
    docker-compose restart
    print_message "Containers reiniciados!" $GREEN
}

# Função para mostrar status
show_status() {
    print_message "Status dos containers:" $BLUE
    docker-compose ps
}

# Função para mostrar logs
show_logs() {
    print_message "Mostrando logs (Ctrl+C para sair):" $BLUE
    docker-compose logs -f
}

# Função para rebuild
rebuild_images() {
    print_message "Reconstruindo imagens..." $BLUE
    docker-compose down
    docker-compose build --no-cache
    docker-compose up -d
    print_message "Imagens reconstruídas!" $GREEN
}

# Função para limpeza
clean_system() {
    print_message "ATENÇÃO: Isto irá remover todos os containers e volumes!" $RED
    read -p "Tem certeza? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        print_message "Limpando sistema..." $BLUE
        docker-compose down -v
        docker system prune -f
        print_message "Sistema limpo!" $GREEN
    else
        print_message "Operação cancelada." $YELLOW
    fi
}

# Função para acessar shell do banco
db_shell() {
    print_message "Acessando shell do MySQL..." $BLUE
    docker-compose exec db mysql -u academia_user -p academiabd
}

# Função para acessar shell da aplicação
app_shell() {
    print_message "Acessando shell da aplicação..." $BLUE
    docker-compose exec app bash
}

# Função para backup
backup_database() {
    print_message "Fazendo backup do banco de dados..." $BLUE
    
    # Criar diretório de backup se não existir
    mkdir -p backups
    
    # Nome do arquivo de backup com timestamp
    BACKUP_FILE="backups/backup_$(date +%Y%m%d_%H%M%S).sql"
    
    # Fazer backup
    docker-compose exec db mysqldump -u academia_user -pacademia_pass academiabd > $BACKUP_FILE
    
    if [ $? -eq 0 ]; then
        print_message "Backup criado: $BACKUP_FILE" $GREEN
    else
        print_message "Erro ao criar backup!" $RED
        exit 1
    fi
}

# Função para restaurar backup
restore_database() {
    print_message "Restaurando backup do banco de dados..." $BLUE
    
    # Listar backups disponíveis
    if [ ! -d "backups" ] || [ -z "$(ls -A backups)" ]; then
        print_message "Nenhum backup encontrado!" $RED
        exit 1
    fi
    
    echo "Backups disponíveis:"
    ls -la backups/
    
    read -p "Digite o nome do arquivo de backup: " BACKUP_FILE
    
    if [ ! -f "backups/$BACKUP_FILE" ]; then
        print_message "Arquivo de backup não encontrado!" $RED
        exit 1
    fi
    
    print_message "Restaurando backup: $BACKUP_FILE" $BLUE
    docker-compose exec -T db mysql -u academia_user -pacademia_pass academiabd < "backups/$BACKUP_FILE"
    
    if [ $? -eq 0 ]; then
        print_message "Backup restaurado com sucesso!" $GREEN
    else
        print_message "Erro ao restaurar backup!" $RED
        exit 1
    fi
}

# Função para instalação inicial
install_system() {
    print_message "=== INSTALAÇÃO INICIAL DO SISTEMA ===" $BLUE
    
    # Verificar se Docker está instalado
    if ! command -v docker &> /dev/null; then
        print_message "Docker não está instalado!" $RED
        exit 1
    fi
    
    if ! command -v docker-compose &> /dev/null; then
        print_message "Docker Compose não está instalado!" $RED
        exit 1
    fi
    
    print_message "Docker encontrado!" $GREEN
    
    # Construir e iniciar containers
    print_message "Construindo e iniciando containers..." $BLUE
    docker-compose up -d --build
    
    print_message "Aguardando inicialização completa..." $YELLOW
    sleep 30
    
    # Verificar se todos os serviços estão rodando
    print_message "Verificando serviços..." $BLUE
    
    if docker-compose ps | grep -q "Up"; then
        print_message "Todos os serviços estão rodando!" $GREEN
        print_message "" $NC
        print_message "=== INSTALAÇÃO CONCLUÍDA ===" $GREEN
        print_message "Acesse o sistema em: http://localhost:8080" $GREEN
        print_message "" $NC
        print_message "Usuários de teste:" $BLUE
        print_message "Admin: admin@academia.com / password" $BLUE
        print_message "Instrutor: joao@academia.com / password" $BLUE
        print_message "Aluno: maria@email.com / password" $BLUE
    else
        print_message "Alguns serviços falharam ao iniciar!" $RED
        docker-compose ps
        exit 1
    fi
}

# Verificar se Docker está rodando (exceto para help)
if [ "$1" != "help" ] && [ "$1" != "" ]; then
    check_docker
fi

# Switch para comandos
case $1 in
    "start")
        start_containers
        ;;
    "stop")
        stop_containers
        ;;
    "restart")
        restart_containers
        ;;
    "status")
        show_status
        ;;
    "logs")
        show_logs
        ;;
    "build")
        rebuild_images
        ;;
    "clean")
        clean_system
        ;;
    "db-shell")
        db_shell
        ;;
    "app-shell")
        app_shell
        ;;
    "backup")
        backup_database
        ;;
    "restore")
        restore_database
        ;;
    "install")
        install_system
        ;;
    "help"|"")
        show_help
        ;;
    *)
        print_message "Comando desconhecido: $1" $RED
        show_help
        exit 1
        ;;
esac
