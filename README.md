# Sistema de Gestão de Academia (SGF)

Sistema completo para gestão de academias desenvolvido em PHP com arquitetura MVC e Docker.

## 🏋️ Funcionalidades

### Para Alunos
- Login e cadastro de usuário
- Visualização do status da matrícula
- Consulta de mensalidades e pagamentos
- Acesso aos planos de treino
- Histórico de avaliações físicas
- Agenda de aulas

### Para Instrutores
- Criação e edição de planos de treino
- Agendamento de aulas
- Realização de avaliações físicas
- Relatórios de frequência dos alunos
- Dashboard com estatísticas

### Para Administradores
- Gestão completa de matrículas
- Controle de inadimplência
- Geração de boletos
- Cadastro de instrutores
- Relatórios gerenciais
- Backup do sistema

## 🛠️ Tecnologias Utilizadas

- **Backend**: PHP 8.2 com PDO
- **Frontend**: Bootstrap 5, HTML5, CSS3, JavaScript
- **Banco de Dados**: MySQL 8.0
- **Servidor Web**: Nginx
- **Containerização**: Docker & Docker Compose
- **Arquitetura**: MVC (Model-View-Controller)

## 📋 Pré-requisitos

- Docker
- Docker Compose
- Git

## 🚀 Instalação e Configuração

### 1. Clone o repositório
```bash
git clone <url-do-repositorio>
cd sistema-academia
```

### 2. Inicie os containers
```bash
docker-compose up -d
```

### 3. Acesse o sistema
Abra seu navegador e acesse: `http://localhost:8080`

## 👥 Usuários de Teste

O sistema vem com usuários pré-cadastrados para teste:

### Administrador
- **Email**: admin@academia.com
- **Senha**: password

### Instrutor
- **Email**: joao@academia.com
- **Senha**: password

### Aluno
- **Email**: maria@email.com
- **Senha**: password

## 🗂️ Estrutura do Projeto

```
sistema-academia/
├── app/
│   ├── controllers/          # Controladores MVC
│   ├── models/              # Modelos de dados
│   └── views/               # Templates de visualização
├── config/                  # Configurações do sistema
├── public/                  # Arquivos públicos (CSS, JS, imagens)
├── docker-compose.yml       # Configuração Docker
├── Dockerfile              # Imagem Docker customizada
├── nginx.conf              # Configuração do Nginx
└── init.sql                # Script de inicialização do banco
```

## 🔒 Recursos de Segurança

- **Autenticação**: Sistema de login com senhas hash (password_hash)
- **Autorização**: Controle de acesso baseado em perfis de usuário
- **CSRF Protection**: Tokens CSRF em todos os formulários
- **SQL Injection**: Uso de Prepared Statements (PDO)
- **Sanitização**: Validação e sanitização de todas as entradas
- **Sessões**: Controle seguro de sessões PHP

## 💾 Banco de Dados

O sistema utiliza as seguintes tabelas principais:

- `aluno` - Dados dos alunos
- `instrutor` - Dados dos instrutores
- `administrador` - Dados dos administradores
- `matricula` - Controle de matrículas
- `boleto` - Gestão de pagamentos
- `plano_treino` - Planos de exercícios
- `aula` - Agendamento de aulas
- `avaliacao_fisica` - Histórico de avaliações

## 🔧 Comandos Úteis

### Parar os containers
```bash
docker-compose down
```

### Reiniciar os containers
```bash
docker-compose restart
```

### Ver logs dos containers
```bash
docker-compose logs -f
```

### Acessar o container da aplicação
```bash
docker-compose exec app bash
```

### Acessar o MySQL
```bash
docker-compose exec db mysql -u academia_user -p academiabd
```

## 📊 Relatórios Disponíveis

- Frequência dos alunos
- Inadimplência
- Desempenho nas avaliações físicas
- Estatísticas gerais da academia

## 🔄 Backup Automático

O sistema está configurado para backup semanal automático do banco de dados. Os backups são armazenados no volume Docker `mysql_data`.

## 🌐 Responsividade

A interface é totalmente responsiva, compatível com:
- Desktop
- Tablets
- Smartphones

## 🐛 Solução de Problemas

### Erro de conexão com banco de dados
1. Verifique se os containers estão rodando: `docker-compose ps`
2. Reinicie os containers: `docker-compose restart`

### Permissões de arquivo
```bash
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
```

### Container não inicia
1. Verifique os logs: `docker-compose logs app`
2. Reconstrua a imagem: `docker-compose build --no-cache`

## 📞 Suporte

Para dúvidas ou problemas:
1. Consulte a documentação no código
2. Verifique os logs dos containers
3. Abra uma issue no repositório

## 📝 Licença

Este projeto está sob a licença MIT. Consulte o arquivo LICENSE para mais detalhes.

## 🤝 Contribuindo

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## ✅ Status do Projeto

- [x] Estrutura Docker
- [x] Autenticação e autorização
- [x] Gestão de matrículas
- [x] Sistema de pagamentos
- [x] Dashboard responsivo
- [x] Segurança implementada
- [ ] Relatórios avançados
- [ ] API REST
- [ ] Notificações por email
- [ ] Backup automático aprimorado

---

Desenvolvido com ❤️ para gestão eficiente de academias.
