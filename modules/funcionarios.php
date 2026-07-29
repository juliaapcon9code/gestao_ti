<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    die("Acesso negado");
}

$action = $_GET['action'] ?? 'list';

if (!in_array($action, ['profile_update', 'change_password']) && !isAdmin()) {
    die("Acesso negado");
}

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $cargo = $_POST['cargo'];
    $setor = $_POST['setor'];
    $salario = $_POST['salario_base'];
    $role = $_POST['role'] ?? 'funcionario';

    $stmt = $pdo->prepare("INSERT INTO funcionarios (nome, email, senha, cargo, setor, salario_base, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nome, $email, $senha, $cargo, $setor, $salario, $role]);
    
    logAction($pdo, 'Criar Funcionário', "Admin criou funcionário: $email");
    redirect('../admin_funcionarios.php?success=Funcionário criado');
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cargo = $_POST['cargo'];
    $setor = $_POST['setor'];
    $salario = $_POST['salario_base'];
    $status = $_POST['status'];
    $role = $_POST['role'];

    $sql = "UPDATE funcionarios SET nome = ?, email = ?, cargo = ?, setor = ?, salario_base = ?, status = ?, role = ? WHERE id = ?";
    $params = [$nome, $email, $cargo, $setor, $salario, $status, $role, $id];

    if (!empty($_POST['senha'])) {
        $sql = "UPDATE funcionarios SET nome = ?, email = ?, cargo = ?, setor = ?, salario_base = ?, status = ?, role = ?, senha = ? WHERE id = ?";
        $params = [$nome, $email, $cargo, $setor, $salario, $status, $role, password_hash($_POST['senha'], PASSWORD_DEFAULT), $id];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    logAction($pdo, 'Editar Funcionário', "Admin editou funcionário ID: $id");
    redirect('../admin_funcionarios.php?success=Funcionário atualizado');
}

if ($action === 'profile_update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_SESSION['user_id'];
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $cargo = trim($_POST['cargo'] ?? '');
    $setor = trim($_POST['setor'] ?? '');

    $stmt = $pdo->prepare("UPDATE funcionarios SET nome = ?, email = ?, cargo = ?, setor = ? WHERE id = ?");
    $stmt->execute([$nome, $email, $cargo, $setor, $id]);

    $_SESSION['user_nome'] = $nome;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_cargo'] = $cargo;
    $_SESSION['user_setor'] = $setor;

    logAction($pdo, 'Atualizar Perfil', 'Usuário atualizou seus dados cadastrais');
    redirect('../meu_perfil.php?success=Perfil atualizado');
}

if ($action === 'change_password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_SESSION['user_id'];
    $senhaAtual = $_POST['senha_atual'] ?? '';
    $senhaNova = $_POST['senha_nova'] ?? '';
    $confirmacao = $_POST['senha_confirmacao'] ?? '';

    if ($senhaNova !== $confirmacao) {
        redirect('../meu_perfil.php?error=As senhas não conferem');
    }

    $stmt = $pdo->prepare("SELECT senha FROM funcionarios WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($senhaAtual, $user['senha'])) {
        redirect('../meu_perfil.php?error=Senha atual incorreta');
    }

    $newHash = password_hash($senhaNova, PASSWORD_DEFAULT);
    $updateStmt = $pdo->prepare("UPDATE funcionarios SET senha = ? WHERE id = ?");
    $updateStmt->execute([$newHash, $id]);

    logAction($pdo, 'Alterar Senha', 'Usuário alterou sua senha');
    redirect('../meu_perfil.php?success=Senha alterada com sucesso');
}

if ($action === 'delete') {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM funcionarios WHERE id = ? AND role != 'admin'");
    $stmt->execute([$id]);
    
    logAction($pdo, 'Excluir Funcionário', "Admin excluiu funcionário ID: $id");
    redirect('../admin_funcionarios.php?success=Funcionário excluído');
}
?>
