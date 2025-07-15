# Sistema de Backup - Academia

## 📋 Visão Geral

O sistema de backup foi implementado para permitir que administradores criem, gerenciem e baixem backups completos do banco de dados da academia.

## 🔧 Funcionalidades Implementadas

### 1. **Página de Backup** (`/backup`)
- Interface web para gerenciar backups
- Disponível apenas para administradores
- Acesso através do dashboard admin

### 2. **Criação de Backup**
- Gera backup completo do banco de dados MySQL
- Arquivo SQL com timestamp automático
- Utiliza Docker para execução segura
- Formato: `backup_sistema_academia_YYYY-MM-DD_HH-MM-SS.sql`

### 3. **Histórico de Backups**
- Lista todos os backups existentes
- Mostra data de criação e tamanho do arquivo
- Ordenação por data (mais recente primeiro)

### 4. **Download de Backups**
- Download seguro de arquivos de backup
- Validação de nome do arquivo
- Verificação de permissões

### 5. **Exclusão de Backups**
- Remoção segura de backups antigos
- Confirmação antes da exclusão
- Validação de permissões

## 🛠️ Arquivos Criados/Modificados

### Novos Arquivos:
- `app/controllers/BackupController.php` - Controlador principal do backup
- `app/views/backup/index.php` - Interface web do sistema de backup
- `test_backup_system.sh` - Script de teste do sistema
- `backups/` - Diretório para armazenamento dos backups

### Arquivos Modificados:
- `config/database.php` - Adicionada função `getDatabaseConfig()`
- `app/views/dashboard/admin.php` - Já continha o botão de backup

## 🔒 Segurança

### Controles de Acesso:
- ✅ Apenas administradores podem acessar
- ✅ Verificação de sessão ativa
- ✅ Validação de tipo de usuário

### Validação de Arquivos:
- ✅ Verificação de formato de nome
- ✅ Validação de diretório de backup
- ✅ Sanitização de entradas

### Execução Segura:
- ✅ Uso de Docker para isolamento
- ✅ Prepared statements para consultas
- ✅ Validação de comandos

## 📊 Dados Incluídos no Backup

O backup inclui todas as tabelas do sistema:
- `aluno` - Dados dos alunos
- `instrutor` - Dados dos instrutores
- `administrador` - Dados dos administradores
- `matricula` - Controle de matrículas
- `boleto` - Gestão de pagamentos
- `plano_treino` - Planos de exercícios
- `aula` - Agendamento de aulas
- `avaliacao_fisica` - Histórico de avaliações

## 🚀 Como Usar

### 1. Acesso ao Sistema
1. Faça login como administrador
2. Acesse o dashboard
3. Clique no botão "Backup" nas ações rápidas

### 2. Criar Backup
1. Na página de backup, clique em "Criar Backup"
2. Confirme a operação
3. Aguarde o processamento
4. O backup aparecerá no histórico

### 3. Gerenciar Backups
- **Download**: Clique no ícone de download
- **Exclusão**: Clique no ícone de lixeira e confirme

## 🔧 Configuração Técnica

### Requisitos:
- Docker e Docker Compose
- Containers da aplicação rodando
- Permissões de escrita no diretório `backups/`

### Comando de Backup:
```bash
docker-compose exec -T db mysqldump -u academia_user -pacademia_pass academiabd > backup_file.sql
```

## 🧪 Testes

### Teste Manual:
1. Execute `./test_backup_system.sh`
2. Acesse `http://localhost:8080/test_backup_route.php`
3. Teste o login admin em `http://localhost:8080/quick_admin_login.php`

### Teste de Funcionalidade:
1. Login como admin
2. Acesse `/backup`
3. Crie um backup
4. Verifique o histórico
5. Teste download
6. Teste exclusão

## 📝 Logs e Monitoramento

### Erros Comuns:
- **Backup vazio**: Verificar conexão com banco
- **Erro de permissão**: Verificar direitos de escrita
- **Timeout**: Aguardar processamento completo

### Monitoramento:
- Tamanho dos arquivos de backup
- Frequência de criação
- Espaço em disco disponível

## 🔄 Backup Automático

O sistema já possui backup automático semanal configurado no Docker. O backup manual complementa esta funcionalidade.

## 📞 Suporte

Para problemas com o sistema de backup:
1. Verifique os logs do Docker: `docker-compose logs app`
2. Teste a conexão com o banco: `docker-compose exec db mysql -u academia_user -pacademia_pass academiabd`
3. Verifique permissões do diretório `backups/`

## 🎯 Próximos Passos

Funcionalidades que podem ser adicionadas:
- [ ] Backup automático agendado via interface
- [ ] Restauração de backup via interface
- [ ] Compressão de arquivos de backup
- [ ] Backup incremental
- [ ] Notificações por email
- [ ] Integração com armazenamento em nuvem

---

**Sistema implementado com sucesso!** 🎉

O botão "Backup" no dashboard do administrador agora abre uma página completa de gerenciamento de backups, permitindo criar, baixar e gerenciar backups do sistema de forma segura e intuitiva.
