# 🚀 TechManager - Sistema de Gestão para Equipes de TI

> Um sistema web moderno e profissional para gestão de tickets, controle de ponto, folha de pagamento e equipes. Desenvolvido com PHP, MySQL e design UI/UX contemporâneo.

![Status](https://img.shields.io/badge/Status-Ativo-brightgreen)
![PHP](https://img.shields.io/badge/PHP-8.0+-blue)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-blue)
![License](https://img.shields.io/badge/License-MIT-green)

## ✨ Funcionalidades

### 👨‍💼 Para Administradores
- 📊 Dashboard executivo com KPIs e estatísticas
- 👥 Gestão de funcionários (CRUD)
- ⏰ Controle centralizado de ponto
- 💰 Folha de pagamento e horas extras
- 🎫 Sistema de tickets Kanban
- 🔍 Auditoria completa do sistema

### 👔 Para Supervisores
- 📋 Dashboard de supervisão da equipe
- 🎫 Gestão de tickets do setor
- ⏰ Visibilidade de ponto dos subordinados
- 📊 Relatórios de performance

### 👨‍💻 Para Funcionários
- 📱 Meu painel pessoal
- ⏰ Registro de ponto
- 🎫 Meus tickets e SLA
- 💵 Extrato de pagamentos

## 🛠️ Stack Tecnológico

- **Backend:** PHP 8.0+ com PDO
- **Banco de Dados:** MySQL 8.0+
- **Frontend:** HTML5, CSS3, Vanilla JS
- **UI:** Design System com Dark Mode
- **Icons:** Font Awesome 6.5
- **Charts:** Chart.js

## 📋 Pré-requisitos

- PHP 8.0 ou superior
- MySQL 8.0 ou superior
- Servidor Apache (com mod_rewrite)
- Composer (opcional)

## 🚀 Instalação Rápida

### 1. Clone o repositório
```bash
git clone https://github.com/seu-usuario/techmanager.git
cd techmanager
```

### 2. Configure o banco de dados
```bash
mysql -u root -p < sql/database.sql
```

### 3. Configure as variáveis de ambiente
```bash
cp config/.env.example config/.env
```

Edite `config/.env` com suas credenciais:
```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=gestao_ti
DB_USER=root
DB_PASS=
```

### 4. Acesse a aplicação
```
http://localhost/gestao_ti/
```

### 5. Reset de credenciais (desenvolvimento)
```
http://localhost/gestao_ti/reset.php
```

## 👤 Credenciais Padrão

| Papel | Email | Senha | Acesso |
|-------|-------|-------|--------|
| Admin | admin@gmail.com | adm123 | Gerenciamento total |
| Supervisor | supervisor@gmail.com | supervisor123 | Supervisão de setor |
| Funcionário | usuario@example.com | senha123 | Seu painel |

## 📁 Estrutura do Projeto

```
techmanager/
├── app/
│   ├── controllers/       # Controllers (lógica de negócio)
│   ├── models/            # Models (acesso a dados)
│   └── middleware/        # Middleware (auth, validation)
├── resources/
│   ├── views/             # Views (apresentação)
│   ├── partials/          # Componentes reutilizáveis
│   └── css/               # Estilos
├── config/
│   ├── db.php             # Conexão banco
│   └── .env               # Variáveis ambiente
├── sql/
│   └── database.sql       # Schema do banco
├── public/
│   ├── index.php          # Ponto de entrada
│   ├── assets/            # Arquivos estáticos
│   └── ajax/              # Endpoints AJAX
└── README.md              # Este arquivo
```

## 🔐 Segurança

- ✅ Password hashing com bcrypt
- ✅ Prepared statements contra SQL Injection
- ✅ CSRF tokens em formulários
- ✅ Validação e sanitização de entrada
- ✅ Logs de auditoria de todas as ações
- ✅ Controle de permissões (RBAC)
- ✅ Proteção contra XSS

## 📊 Endpoints Principais

### Dashboard
- `GET /` → Login
- `GET /admin_dashboard.php` → Dashboard Admin
- `GET /supervisor_dashboard.php` → Dashboard Supervisor
- `GET /funcionario_dashboard.php` → Dashboard Funcionário

### Funcionários
- `GET /admin_funcionarios.php` → Listar
- `POST /modules/funcionarios.php?action=create` → Criar
- `POST /modules/funcionarios.php?action=update` → Atualizar

### Tickets
- `GET /tickets.php` → Kanban
- `GET /meus_tickets.php` → Meus tickets

### Ponto
- `GET /admin_ponto.php` → Controle admin
- `GET /meu_ponto.php` → Meu ponto

### Pagamentos
- `GET /admin_pagamentos.php` → Folha admin
- `GET /meus_pagamentos.php` → Meus pagamentos

## 🗄️ Banco de Dados

### Tabelas Principais
- `funcionarios` - Dados de usuários
- `ponto` - Registros de presença
- `pagamento` - Folha de pagamento
- `servicos` - Tickets/Chamados
- `tickets_comentarios` - Comentários
- `logs_sistema` - Auditoria
- `notificacoes` - Notificações

## 🎨 Design System

### Cores
- **Primary:** #22c55e (Verde)
- **Secondary:** #0e7490 (Azul Petróleo)
- **Background:** #01040d (Azul Escuro)
- **Text:** #f8fafc (Branco)

### Componentes
- Cards com border e backdrop-filter
- Grid layout responsivo
- Dark mode nativo
- Paleta consistente

## 🚀 Melhorias Planejadas

- [ ] API REST completa
- [ ] Exportação PDF/Excel
- [ ] Webhooks para integrações
- [ ] Two-factor authentication
- [ ] Dashboard em tempo real com WebSockets
- [ ] Mobile app nativa
- [ ] Plugin system

## 📈 Performance

- Queries otimizadas com índices
- Lazy loading em tabelas
- Paginação implementada
- Cache de sessão

## 🤝 Contribuindo

1. Fork o repositório
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto é licenciado sob a MIT License - veja o arquivo [LICENSE](LICENSE) para detalhes.

## 👨‍💻 Autor

Desenvolvido como um projeto moderno de gestão de equipes, com foco em usabilidade, segurança e performance.

## 📞 Suporte

Para reportar bugs ou sugerir features, abra uma issue no repositório.

---

**Desenvolvido com ❤️ em 2024**
