# 🚀 GETTING STARTED - Usando as Novas Features

> Guia rápido de como usar as funcionalidades criadas na Phase 1

---

## 📚 Índice

1. [CSRF Tokens](#csrf-tokens)
2. [Input Validator](#input-validator)
3. [Componentes Reutilizáveis](#componentes-reutilizáveis)
4. [Helper Functions](#helper-functions)
5. [Exemplos Completos](#exemplos-completos)

---

## CSRF Tokens

### O que é?
Proteção contra ataques Cross-Site Request Forgery (OWASP A01:2021)

### Como usar?

#### 1️⃣ Gerar token no formulário
```php
<form method="POST" action="handler.php">
    <input type="hidden" name="csrf_token" value="<?= CSRFToken::generate() ?>">
    
    <input type="email" name="email" required>
    <button type="submit">Enviar</button>
</form>
```

#### 2️⃣ Validar no handler
```php
<?php
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar CSRF token
    $token = $_POST['csrf_token'] ?? '';
    if (!CSRFToken::validate($token)) {
        http_response_code(403);
        die('CSRF token inválido. Tente novamente.');
    }
    
    // Processar dados seguros aqui
    echo "Token válido!";
}
?>
```

#### 3️⃣ API
```php
CSRFToken::generate()    // Gera novo token
CSRFToken::getToken()    // Obtém token atual
CSRFToken::validate($token)  // Valida token
CSRFToken::invalidate()  // Invalida token atual
```

---

## Input Validator

### O que é?
Validação centralizada de inputs com 15+ regras pré-configuradas

### Como usar?

#### 1️⃣ Validação simples
```php
<?php
require_once 'includes/functions.php';

// Validar email
if (!InputValidator::isValidEmail($_POST['email'])) {
    echo "Email inválido";
}

// Validar senha
$errors = InputValidator::validatePassword($_POST['password']);
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo $error . "\n";
    }
}

// Validar se está entre valores permitidos
if (!InputValidator::isInAllowedValues($_POST['role'], ['admin', 'user', 'guest'])) {
    echo "Role inválida";
}
?>
```

#### 2️⃣ Validação em batch (recomendado)
```php
<?php
require_once 'includes/functions.php';

$rules = [
    'name' => ['required', 'min:3', 'max:100'],
    'email' => ['required', 'email'],
    'password' => ['required', 'min:8'],
    'role' => ['required', 'in:admin,supervisor,user'],
    'age' => ['required', 'min:18', 'max:120'],
];

if (InputValidator::validate($rules, $_POST)) {
    // Dados válidos, processar
    echo "Todos os dados estão válidos!";
} else {
    // Mostrar erros
    $errors = InputValidator::getErrors();
    foreach ($errors as $field => $message) {
        echo "<p>$field: $message</p>";
    }
}
?>
```

#### 3️⃣ Sanitização de inputs
```php
<?php
require_once 'includes/functions.php';

// Sanitizar string (previne XSS)
$safe_name = InputValidator::sanitizeString($_POST['name']);
echo $safe_name; // Seguro para HTML

// Sanitizar número
$price = InputValidator::sanitizeNumber($_POST['price']);
if ($price !== null) {
    // É um número válido
}

// Obter erro de um campo
$emailError = InputValidator::getError('email');
if ($emailError) {
    echo "Erro no email: $emailError";
}

// Verificar se há erros
if (InputValidator::hasErrors()) {
    echo "Formulário tem erros";
}
?>
```

#### 4️⃣ Regras disponíveis
```
'required'     - Campo obrigatório
'email'        - Deve ser email válido
'min:N'        - Mínimo N caracteres
'max:N'        - Máximo N caracteres
'in:a,b,c'     - Deve estar entre valores
```

---

## Componentes Reutilizáveis

### 1️⃣ Sidebar
```php
<?php
$currentPage = 'admin_dashboard'; // Página ativa
include 'resources/views/partials/sidebar.php';
?>
```

**Variáveis:**
- `$currentPage` - Página ativa para highlight

**Resultado:** Menu dinâmico por role (Admin/Supervisor/Funcionário)

### 2️⃣ Header
```php
<?php
$title = 'Gestão de Funcionários';
$icon = 'fas fa-users';
$subtitle = 'Cadastre e gerencie sua equipe';
$pills = ['3 colaboradores', 'Ativo'];
$actionButton = [
    'text' => 'Novo Funcionário',
    'href' => '#',
    'onclick' => "alert('Clicked!')",
    'icon' => 'fas fa-plus',
    'id' => 'btnCreate'
];

include 'resources/views/partials/header.php';
?>
```

**Resultado:** Cabeçalho profissional com ícones e informações

### 3️⃣ Stat Card
```php
<?php
$label = 'Total Funcionários';
$value = 42;
$icon = 'fas fa-users';
$footer = 'Pessoas cadastradas';
$footerIcon = 'fas fa-arrow-trend-up';
$variant = 'success'; // success, warning, danger, accent

include 'resources/views/partials/stat_card.php';
?>
```

**Resultado:** KPI card reutilizável

---

## Helper Functions

### Formatação Monetária
```php
<?php
require_once 'includes/functions.php';

echo formatMoney(1500.00);      // R$ 1.500,00
echo formatMoney(0);             // R$ 0,00
echo formatMoney(1234567.89);   // R$ 1.234.567,89
?>
```

### Formatação de Datas
```php
<?php
require_once 'includes/functions.php';

echo formatDate('2024-01-15');           // 15/01/2024
echo formatDate('2024-01-15', true);     // 15/01/2024 10:30
echo formatDate('');                     // -
echo formatDate('0000-00-00');          // -
?>
```

### Formatação de Horas
```php
<?php
require_once 'includes/functions.php';

echo formatTime('10:30:00');   // 10:30
echo formatTime('23:59:59');   // 23:59
echo formatTime('');           // -
?>
```

### Badges de Status
```php
<?php
require_once 'includes/functions.php';

// Tipo default: ativo, inativo
echo statusBadge('ativo', 'default');

// Tipo ticket: aberto, andamento, revisao, finalizado
echo statusBadge('andamento', 'ticket');

// Tipo payment: pago, pendente, cancelado
echo statusBadge('pago', 'payment');

// Tipo default (sem type)
echo statusBadge('ativo');
?>
```

### Truncar Texto
```php
<?php
require_once 'includes/functions.php';

echo truncate('Um texto muito longo...', 20);
// Output: Um texto muito lo...

echo truncate('Curto', 20);
// Output: Curto
?>
```

### Tempo Relativo
```php
<?php
require_once 'includes/functions.php';

echo timeAgo('2024-01-10 10:30:00', 'pt');
// Output: há 5 dias

echo timeAgo('2024-01-15 23:45:00', 'pt');
// Output: há 1 segundo
?>
```

### Calcular Diferença em Dias
```php
<?php
require_once 'includes/functions.php';

$dias = daysDiff('2024-01-01', '2024-01-15');
// Output: 14

$dias = daysDiff('2024-01-01'); // Até hoje
// Output: (dias até hoje)
?>
```

### Outras Funções
```php
<?php
require_once 'includes/functions.php';

truncate($text, 50);           // Trunca texto
boolToString(true);            // "Sim"
calculateAge('2000-05-15');    // Idade em anos
slugify('Meu Título');         // 'meu-titulo'
detectBrowser();               // 'Chrome', 'Firefox', etc
getClientIP();                 // IP com proxy support
?>
```

---

## Exemplos Completos

### ✅ EXEMPLO 1: Formulário com CSRF + Validação

**form.php:**
```php
<?php
require_once 'includes/functions.php';
?>

<form method="POST" action="handler.php">
    <input type="hidden" name="csrf_token" value="<?= CSRFToken::generate() ?>">
    
    <div>
        <label>Nome</label>
        <input type="text" name="name" required>
    </div>
    
    <div>
        <label>Email</label>
        <input type="email" name="email" required>
    </div>
    
    <div>
        <label>Senha</label>
        <input type="password" name="password" required>
    </div>
    
    <button type="submit">Criar</button>
</form>
```

**handler.php:**
```php
<?php
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Validar CSRF
    if (!CSRFToken::validate($_POST['csrf_token'] ?? '')) {
        die('CSRF token inválido');
    }
    
    // 2. Validar inputs
    $rules = [
        'name' => ['required', 'min:3', 'max:100'],
        'email' => ['required', 'email'],
        'password' => ['required', 'min:8']
    ];
    
    if (!InputValidator::validate($rules, $_POST)) {
        header('Location: form.php?errors=' . json_encode(InputValidator::getErrors()));
        exit;
    }
    
    // 3. Sanitizar
    $name = InputValidator::sanitizeString($_POST['name']);
    $email = InputValidator::sanitizeString($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // 4. Salvar no banco
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $password]);
    
    echo "Usuário criado com sucesso!";
}
?>
```

### ✅ EXEMPLO 2: Página com Componentes

```php
<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) redirect('index.php');

$currentPage = 'admin_dashboard';

// Buscar dados
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$totalUsers = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- SIDEBAR -->
    <?php include 'resources/views/partials/sidebar.php'; ?>
    
    <main class="main-content">
        <!-- HEADER -->
        <?php
        $title = 'Dashboard';
        $icon = 'fas fa-chart-line';
        $subtitle = 'Bem-vindo ao painel de controle';
        $pills = [$totalUsers . ' usuários'];
        include 'resources/views/partials/header.php';
        ?>
        
        <!-- STATS -->
        <div class="grid">
            <?php
            $label = 'Total Usuários';
            $value = $totalUsers;
            $icon = 'fas fa-users';
            $footer = 'Pessoas cadastradas';
            $variant = 'success';
            include 'resources/views/partials/stat_card.php';
            ?>
        </div>
    </main>
</body>
</html>
```

### ✅ EXEMPLO 3: Tabela com Helpers

```php
<table>
    <thead>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Salário</th>
            <th>Status</th>
            <th>Criado em</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= formatMoney($user['salary']) ?></td>
            <td><?= statusBadge($user['status']) ?></td>
            <td><?= formatDate($user['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
```

---

## 📋 Checklist de Uso

- [ ] Importou `includes/functions.php`?
- [ ] Adicionou CSRF token em todos os formulários?
- [ ] Validando inputs com InputValidator?
- [ ] Usando helpers para formatação?
- [ ] Usando partials para componentes?
- [ ] Testando CSRF rejection?
- [ ] Tratando erros de validação?

---

**Próximo:** Consulte [NEXT_STEPS.md](NEXT_STEPS.md) para Phase 2
