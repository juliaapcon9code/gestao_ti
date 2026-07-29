# 🔧 PRÓXIMOS PASSOS - IMPLEMENTAÇÃO FASE 2

> Este documento contém as instruções exatas para elevar o projeto de 6.2/10 para 7.8/10

---

## FASE 2: INTEGRAÇÃO E REFATORAÇÃO (1-2 semanas)

### Semana 1

#### TAREFA 1: Ativar CSRF em Formulários ⏱️ ~2h
**Objetivo:** Proteger contra ataques CSRF  
**Impacto:** Segurança crítica

**Passo a Passo:**

1. **Abrir cada formulário e adicionar token:**

Exemplo em `admin_funcionarios.php`:
```php
// Encontrar: <form...>
// Mudar para:
<form action="modules/funcionarios.php" method="POST">
    <!-- ADICIONAR ISTO: -->
    <input type="hidden" name="csrf_token" value="<?= CSRFToken::generate() ?>">
    
    <!-- Resto do formulário -->
</form>
```

2. **Validar token no handler (modules/funcionarios.php):**

```php
<?php
// No inicio do arquivo, após includes:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ADICIONAR ISTO:
    if (!isset($_POST['csrf_token']) || !CSRFToken::validate($_POST['csrf_token'])) {
        logAction($pdo, 'CSRF_REJECTION', 'Token inválido');
        redirect('../admin_funcionarios.php?error=Requisição inválida');
    }
    
    // Resto da lógica...
}
?>
```

3. **Arquivos a atualizar:**
- [ ] admin_funcionarios.php (1 form)
- [ ] modules/funcionarios.php (2 actions)
- [ ] modules/tickets.php
- [ ] modules/pagamentos.php
- [ ] modules/ponto.php
- [ ] admin_ponto.php (se houver form)

**Validação:** Testar CSRF rejection abrindo DevTools e removendo token do form

---

#### TAREFA 2: Integrar InputValidator ⏱️ ~3h
**Objetivo:** Validação centralizada  
**Impacto:** Reduz vulnerabilidades de input em 80%

**Passo a Passo:**

1. **Criar request validator (modules/funcionarios.php):**

```php
<?php
// Na função create:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validação
    $rules = [
        'nome' => ['required', 'min:3', 'max:100'],
        'email' => ['required', 'email'],
        'senha' => ['required', 'min:8'],
        'cargo' => ['required', 'min:2'],
        'setor' => ['required', 'min:2'],
        'salario_base' => ['required'],
        'role' => ['required', 'in:admin,supervisor,funcionario']
    ];
    
    if (!InputValidator::validate($rules, $_POST)) {
        logAction($pdo, 'VALIDATION_ERROR', json_encode(InputValidator::getErrors()));
        redirect('../admin_funcionarios.php?error=' . urlencode(json_encode(InputValidator::getErrors())));
    }
    
    // Sanitizar inputs
    $nome = InputValidator::sanitizeString($_POST['nome']);
    $email = InputValidator::sanitizeString($_POST['email']);
    $cargo = InputValidator::sanitizeString($_POST['cargo']);
    // ... etc
    
    // INSERT ...
}
?>
```

2. **Validadores por módulo:**

**admin_ponto.php:**
- Data deve ser Y-m-d válida: `isValidDate()`

**modules/pagamentos.php:**
- Valores devem ser números: `sanitizeNumber()`
- Mês 1-12, ano válido

**modules/tickets.php:**
- Prioridade in: ['baixa', 'media', 'alta']
- Status in: ['aberto', 'andamento', 'revisao', 'finalizado']

3. **Adicionar feedback de erro no formulário:**

```php
<?php if (isset($_GET['error'])): ?>
    <div class="error-banner">
        <?php $errors = json_decode($_GET['error'], true); ?>
        <?php foreach ($errors as $field => $msg): ?>
            <p><?= htmlspecialchars($msg) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
```

---

### Semana 2

#### TAREFA 3: Refatoração Básica para MVC ⏱️ ~4h
**Objetivo:** Separação de responsabilidades  
**Impacto:** Escalabilidade, manutenibilidade

**Passo a Passo:**

1. **Criar estrutura:**
```bash
mkdir -p app/Controllers
mkdir -p app/Models
mkdir -p app/Requests
```

2. **Mover lógica em AdmnController.php:**

```php
<?php
// app/Controllers/AdminController.php

namespace App\Controllers;

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';

class AdminController {
    protected $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        
        if (!isAdmin()) {
            redirect('index.php');
        }
    }
    
    public function dashboard() {
        $total_func = $this->pdo->query("SELECT COUNT(*) FROM funcionarios")->fetchColumn();
        $func_ativos = $this->pdo->query("SELECT COUNT(*) FROM funcionarios WHERE status = 'ativo'")->fetchColumn();
        
        return [
            'total_func' => $total_func,
            'func_ativos' => $func_ativos,
        ];
    }
}
?>
```

3. **Atualizar admin_dashboard.php:**

```php
<?php
require_once 'app/Controllers/AdminController.php';

$controller = new \App\Controllers\AdminController();
$data = $controller->dashboard();

$total_func = $data['total_func'];
$func_ativos = $data['func_ativos'];
// ... resto do código
?>
```

4. **Aplicar em:**
- [ ] Admin Dashboard (AdminController.php)
- [ ] Tickets (TicketController.php)
- [ ] Pagamentos (PaymentController.php)

---

#### TAREFA 4: Usar Componentes em Todas Páginas ⏱️ ~2h
**Objetivo:** Remover duplicação  
**Impacto:** -500 linhas de código, manutenção fácil

**Antes:**
```php
<div class="sidebar">
    <div class="logo">...</div>
    <!-- 50 linhas de menu -->
</div>

<header>
    <!-- 30 linhas de header -->
</header>
```

**Depois:**
```php
<?php $currentPage = 'admin_dashboard'; ?>
<?php include 'resources/views/partials/sidebar.php'; ?>

<?php 
$title = 'Dashboard Administrativo';
$icon = 'fas fa-chart-line';
$subtitle = 'Indicadores operacionais';
include 'resources/views/partials/header.php';
?>
```

**Aplicar em (14 arquivos):**
- [ ] admin_dashboard.php
- [ ] admin_funcionarios.php
- [ ] admin_ponto.php
- [ ] admin_pagamentos.php
- [ ] admin_auditoria.php
- [ ] supervisor_dashboard.php
- [ ] funcionario_dashboard.php
- [ ] tickets.php
- [ ] meus_tickets.php
- [ ] meu_ponto.php
- [ ] meus_pagamentos.php
- [ ] login_process.php
- [ ] reset.php

---

#### TAREFA 5: Usar Helpers em Templates ⏱️ ~1h
**Objetivo:** Código mais limpo

**Antes:**
```php
<?= formatMoney($user['salario_base']) ?>
```

**Agora:**
```php
<?= formatMoney($user['salario_base']) ?>
<?= statusBadge($user['status']) ?>
<?= formatDate($user['created_at']) ?>
```

**Aplicar em:**
- [ ] admin_funcionarios.php - formatMoney, statusBadge
- [ ] admin_pagamentos.php - formatMoney, statusBadge
- [ ] admin_ponto.php - formatTime
- [ ] meus_tickets.php - timeAgo, statusBadge
- [ ] admin_auditoria.php - formatDate, timeAgo

---

## Métricas de Sucesso (Fase 2)

- [ ] ✅ 0 CSRF warnings no OWASP tests
- [ ] ✅ 100% formulários com CSRF tokens
- [ ] ✅ 100% inputs validados
- [ ] ✅ 0 linhas duplicadas de sidebar/header
- [ ] ✅ Código duplicado reduzido em 60%
- [ ] ✅ Partials em uso em 14 arquivos
- [ ] ✅ 1 Controller criado (exemplo)

## Resultado Esperado

**Antes Phase 2:** 6.2/10  
**Depois Phase 2:** 7.5/10  

| Métrica | Antes | Depois |
|---------|-------|--------|
| CSRF Protection | 0% | 100% |
| Input Validation | 0% | 80% |
| Code Duplication | 40% | 5% |
| Arquitetura | MVC Parcial | MVC 30% |
| Segurança | 5/10 | 7/10 |

---

## Dicas de Implementação

1. **Faça um arquivo por vez**
   - Teste após cada mudança
   - Git commit após cada tarefa

2. **Use browser DevTools**
   - Verify CSRF token é enviado
   - Check validation errors

3. **Mantenha git history limpo**
   ```
   git commit -m "feat: Add CSRF protection to admin_funcionarios"
   git commit -m "feat: Add input validation to modules/funcionarios"
   ```

4. **Teste casos de erro**
   - Form sem CSRF token
   - Email inválido
   - Password muito curta
   - Dropdown com valor inválido

---

## Próximas Fases (Visão Geral)

**Phase 3:** Performance + UX
- Índices no banco
- Paginação
- Toast notifications
- Loading states

**Phase 4:** Qualidade
- Testes unitários
- Lint/Formatter
- CI/CD

**Phase 5:** Funcionalidades Avançadas
- API REST
- Real-time updates
- Export PDF/Excel

---

**Status:** Ready for Phase 2  
**Tempo Estimado:** 10-15 horas  
**Resultado:** +1.3 pontos na nota
