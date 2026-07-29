# TechManager

Sistema de gestão para equipes de TI com controle de ponto, tickets, folha de pagamento e administração de funcionários.

## Sobre o projeto

Desenvolvi este sistema para praticar os conceitos aprendidos no curso Técnico em Desenvolvimento de Sistemas do SENAI. O objetivo foi criar uma aplicação que resolvesse problemas reais de gestão de uma equipe de TI, como controle de jornada, organização de chamados e administração de pessoal.

O projeto surgiu da necessidade de centralizar informações que normalmente ficam espalhadas em planilhas e e-mails, facilitando o dia a dia de administradores, supervisores e funcionários.

## Funcionalidades

### Autenticação e acesso
- Login com e-mail e senha
- Três níveis de acesso: admin, supervisor e funcionário
- Redirecionamento automático para dashboard específico de cada perfil
- Logout seguro

### Dashboard administrativo
- Visualização de estatísticas gerais (total de funcionários, ativos, tickets pendentes)
- Gráfico de presença semanal com Chart.js
- Gráfico de tickets por status
- Acesso a todas as áreas de gestão

### Gestão de funcionários (admin)
- Listagem de funcionários com busca por nome ou e-mail
- Cadastro de novos funcionários
- Edição de dados (nome, e-mail, cargo, setor, salário, status, perfil)
- Exclusão de funcionários
- Modal para criação de novos registros

### Controle de ponto
- Registro de entrada, pausa, retorno e saída
- Cálculo automático de horas trabalhadas e extras
- Identificação de atrasos (entrada após 09:15)
- Calendário mensal visual com status de cada dia
- Filtro por mês no calendário
- Visão administrativa dos registros por data

### Sistema de tickets
- Quadro Kanban com colunas: aberto, andamento, revisão, finalizado
- Criação de tickets com título, descrição, prioridade e setor
- Arrastar e soltar para mudar status
- Atribuição de responsável
- Comentários nos tickets
- Visualização dos tickets atribuídos a cada funcionário
- Indicador de SLA (dias desde abertura)

### Folha de pagamento (admin)
- Geração de folha por mês/ano
- Cálculo de horas extras com adicional de 50%
- Benefícios fixos (VA + VR)
- Descontos por falta
- Visualização de salário base, extras, descontos, benefícios e total
- Status de pagamento (pendente/pago)

### Dashboard supervisor
- Visão da equipe do setor
- Contagem de tickets do setor
- Atrasos e extras do dia
- Lista de funcionários com status e ponto do dia
- Distribuição de prioridades dos tickets

### Perfil do funcionário
- Visualização de dados cadastrais
- Atualização de nome, e-mail, cargo e setor
- Alteração de senha com confirmação
- Histórico de notificações

### Auditoria (admin)
- Log de todas as ações do sistema
- Registro de usuário, ação, detalhes, IP e data
- Visualização dos 100 registros mais recentes

### Notificações
- Sistema de notificações para usuários
- Atualização em tempo real via AJAX
- Marcação como lida

## Tecnologias utilizadas

- **Linguagem**: PHP
- **Banco de dados**: MySQL
- **Conexão**: PDO (PHP Data Objects)
- **Frontend**: HTML5, CSS3, JavaScript
- **Ícones**: Font Awesome 6.5
- **Gráficos**: Chart.js
- **Segurança**: password_hash, prepared statements

## Estrutura do projeto

- **config/**: Arquivos de configuração
  - db.php: Conexão com o banco de dados
  - .env.example: Exemplo de variáveis de ambiente

- **includes/**: Arquivos de funções e classes reutilizáveis
  - functions.php: Funções principais (autenticação, redirecionamento, logs)
  - helpers.php: Funções auxiliares (formatação de dados)
  - csrf.php: Classe para proteção CSRF
  - validator.php: Classe para validação de inputs

- **modules/**: Lógica de processamento de formulários
  - funcionarios.php: CRUD de funcionários e perfil
  - ponto.php: Registro de ponto
  - tickets.php: Criação e atualização de tickets
  - pagamentos.php: Geração de folha de pagamento

- **resources/views/partials/**: Componentes de interface
  - header.php: Cabeçalho das páginas
  - sidebar.php: Menu lateral
  - stat_card.php: Card de estatísticas

- **assets/css/**: Estilos
  - style.css: Folha de estilos principal

- **ajax/**: Endpoints assíncronos
  - updates.php: Atualização de notificações

- **sql/**: Banco de dados
  - database.sql: Script de criação das tabelas
  - fix_admin.php: Script auxiliar para admin

- **Páginas principais**: index.php (login), dashboards (admin, supervisor, funcionário), páginas de gestão (funcionários, ponto, tickets, pagamentos, auditoria, perfil)

## Como executar o projeto

### Requisitos
- Servidor web (Apache)
- PHP
- MySQL
- Navegador web

### Instalação

1. Coloque os arquivos na pasta do servidor (htdocs no XAMPP)

2. Crie o banco de dados importando o arquivo:
   - Acesse o phpMyAdmin
   - Crie um banco chamado gestao_ti
   - Importe o arquivo sql/database.sql

3. Configure a conexão em config/db.php se necessário:
   - Host: 127.0.0.1
   - Banco: gestao_ti
   - Usuário: root
   - Senha: (em branco no XAMPP padrão)

4. Acesse o sistema:
   - http://localhost/gestao_ti/

5. Para resetar as credenciais padrão:
   - http://localhost/gestao_ti/reset.php

### Credenciais de acesso

- **Admin**: admin@gmail.com / adm123
- **Supervisor**: supervisor@gmail.com / supervisor123

## Banco de dados

O sistema utiliza MySQL com as seguintes tabelas:

- **funcionarios**: Dados dos usuários (nome, e-mail, senha, cargo, setor, salário, status, perfil)
- **ponto**: Registros de ponto (funcionário, data, entrada, saída pausa, retorno pausa, saída, horas trabalhadas, extras, status)
- **pagamento**: Folha de pagamento (funcionário, mês, ano, salário base, extras, descontos, benefícios, total, status)
- **servicos**: Tickets de suporte (título, descrição, status, prioridade, funcionário responsável, setor)
- **tickets_comentarios**: Comentários nos tickets
- **logs_sistema**: Registro de auditoria (funcionário, ação, detalhes, IP, data)
- **notificacoes**: Notificações para usuários (funcionário, mensagem, lida, data)

O arquivo sql/database.sql contém o script completo de criação das tabelas com suas relações (foreign keys).

## Conceitos aplicados

- Programação orientada a objetos (classes CSRFToken e InputValidator)
- Autenticação de usuários com sessões
- Controle de acesso baseado em roles (RBAC)
- Operações CRUD com banco de dados
- Validação e sanitização de inputs
- Proteção contra SQL Injection com prepared statements
- Hash de senhas com bcrypt
- AJAX para atualizações assíncronas
- Manipulação de datas e horas
- Cálculos matemáticos (horas extras, folha de pagamento)
- Organização de código em módulos
- Reutilização de componentes (partials)
- Design responsivo com CSS Grid e Flexbox

## Aprendizados

Durante o desenvolvimento deste projeto, pratiquei:

- Integração entre PHP e MySQL usando PDO
- Criação de sistemas de login com diferentes níveis de permissão
- Organização de código em arquivos separados por responsabilidade
- Implementação de boas práticas de segurança (hash de senhas, validação)
- Desenvolvimento de interface com HTML, CSS e JavaScript
- Uso de bibliotecas externas (Chart.js, Font Awesome)
- Resolução de problemas de lógica de negócio (cálculo de horas, geração de folha)
- Criação de layouts responsivos
- Manipulação de formulários e processamento de dados

## Autor

Julia Aparecida

Técnico em Desenvolvimento de Sistemas - SENAI
