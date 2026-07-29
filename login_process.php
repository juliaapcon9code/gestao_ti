<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

$brokenAdminHash = '$2y$10$8W3Y6u3m8l5p2z9q1w2e3uGvFvFvFvFvFvFvFvFvFvFvFvFvFvFv';

function completeLogin($pdo, $user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_nome'] = $user['nome'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_setor'] = $user['setor'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_cargo'] = $user['cargo'];
    $_SESSION['user_last_login'] = date('d/m/Y H:i');

    logAction($pdo, 'Login', 'Usuário logou no sistema');

    if ($user['role'] === 'admin') {
        redirect('admin_dashboard.php');
    } elseif ($user['role'] === 'supervisor') {
        redirect('supervisor_dashboard.php');
    } else {
        redirect('funcionario_dashboard.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        redirect('index.php?error=Preencha todos os campos');
    }

    $stmt = $pdo->prepare("SELECT * FROM funcionarios WHERE email = ? AND status = 'ativo'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        completeLogin($pdo, $user);
        exit; // Garantir que para após redirect
    }

    if ($email === 'admin@gmail.com') {
        $repairStmt = $pdo->prepare("SELECT id, nome, email, senha, role, setor, status FROM funcionarios WHERE email = ? AND role = 'admin' LIMIT 1");
        $repairStmt->execute([$email]);
        $adminUser = $repairStmt->fetch();

        if ($adminUser && $adminUser['senha'] === $brokenAdminHash) {
            $newHash = password_hash('adm123', PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare("UPDATE funcionarios SET senha = ?, status = 'ativo', role = 'admin' WHERE id = ?");
            $updateStmt->execute([$newHash, $adminUser['id']]);
            $adminUser['senha'] = $newHash;

            if (password_verify($senha, $adminUser['senha'])) {
                completeLogin($pdo, $adminUser);
                exit; // Garantir que para após redirect
            }
        }
    }

    redirect('index.php?error=Credenciais inválidas ou usuário inativo');
} else {
    redirect('index.php');
}
?>
