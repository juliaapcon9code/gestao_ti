<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$stmt = $pdo->prepare("SELECT id, nome, email, cargo, setor, salario_base, role, status, created_at FROM funcionarios WHERE id = ? LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    redirect('logout.php');
}

$markRead = $pdo->prepare("UPDATE notificacoes SET lida = 1 WHERE funcionario_id = ?");
$markRead->execute([$_SESSION['user_id']]);

$notifStmt = $pdo->prepare("SELECT mensagem, created_at FROM notificacoes WHERE funcionario_id = ? ORDER BY created_at DESC LIMIT 8");
$notifStmt->execute([$_SESSION['user_id']]);
$notifications = $notifStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - TechManager</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php $currentPage = 'meu_perfil'; include 'resources/views/partials/sidebar.php'; ?>

    <main class="main-content">
        <?php
        $title = 'Meu Perfil';
        $subtitle = 'Gerencie seus dados, acompanhe a última atividade e mantenha o acesso seguro.';
        $icon = 'fas fa-user-circle';
        $breadcrumbs = ['Início', 'Meu Perfil'];
        include 'resources/views/partials/header.php';
        ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <section class="grid profile-grid">
            <article class="card profile-summary">
                <div class="profile-header">
                    <div class="avatar avatar-lg">
                        <?= strtoupper(substr($_SESSION['user_nome'] ?? 'US', 0, 2)) ?>
                    </div>
                    <div>
                        <h3><?= htmlspecialchars($user['nome']) ?></h3>
                        <p><?= htmlspecialchars($user['cargo'] ?: 'Colaborador') ?></p>
                        <span class="status-badge"><i class="fas fa-circle-check"></i> <?= ucfirst($user['role']) ?></span>
                    </div>
                </div>

                <div class="profile-details">
                    <div>
                        <small>E-mail</small>
                        <strong><?= htmlspecialchars($user['email']) ?></strong>
                    </div>
                    <div>
                        <small>Setor</small>
                        <strong><?= htmlspecialchars($user['setor'] ?: 'Não informado') ?></strong>
                    </div>
                    <div>
                        <small>Salário Base</small>
                        <strong><?= formatMoney($user['salario_base'] ?? 0) ?></strong>
                    </div>
                    <div>
                        <small>Status</small>
                        <strong><?= $user['status'] === 'ativo' ? 'Ativo' : 'Inativo' ?></strong>
                    </div>
                    <div>
                        <small>Último acesso</small>
                        <strong><?= htmlspecialchars($_SESSION['user_last_login'] ?? 'Ainda não registrado') ?></strong>
                    </div>
                    <div>
                        <small>Cadastro</small>
                        <strong><?= formatDate($user['created_at']) ?></strong>
                    </div>
                </div>
            </article>

            <article class="card">
                <h3><i class="fas fa-id-card"></i> Atualizar dados</h3>
                <p class="text-muted">Atualize seu nome, e-mail e função para manter as informações da equipe sincronizadas.</p>

                <form action="modules/funcionarios.php?action=profile_update" method="POST" class="profile-form">
                    <label>
                        Nome completo
                        <input type="text" name="nome" value="<?= htmlspecialchars($user['nome']) ?>" required>
                    </label>
                    <label>
                        E-mail corporativo
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </label>
                    <label>
                        Cargo
                        <input type="text" name="cargo" value="<?= htmlspecialchars($user['cargo'] ?: '') ?>">
                    </label>
                    <label>
                        Setor
                        <input type="text" name="setor" value="<?= htmlspecialchars($user['setor'] ?: '') ?>">
                    </label>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Salvar alterações</button>
                </form>
            </article>
        </section>

        <section class="grid profile-grid">
            <article class="card">
                <h3><i class="fas fa-lock"></i> Alterar senha</h3>
                <p class="text-muted">Use uma senha forte e exclusiva para proteger o acesso à plataforma.</p>

                <form action="modules/funcionarios.php?action=change_password" method="POST" class="profile-form">
                    <label>
                        Senha atual
                        <input type="password" name="senha_atual" required>
                    </label>
                    <label>
                        Nova senha
                        <input type="password" name="senha_nova" required>
                    </label>
                    <label>
                        Confirmar nova senha
                        <input type="password" name="senha_confirmacao" required>
                    </label>
                    <button class="btn btn-secondary" type="submit"><i class="fas fa-key"></i> Alterar senha</button>
                </form>
            </article>

            <article class="card" id="notifications">
                <h3><i class="fas fa-bell"></i> Notificações recentes</h3>
                <p class="text-muted">Resumo das ações e alertas mais recentes para sua conta.</p>

                <?php if (empty($notifications)): ?>
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <p>Você está em dia. Nenhuma notificação recente.</p>
                    </div>
                <?php else: ?>
                    <ul class="notification-list">
                        <?php foreach ($notifications as $notification): ?>
                            <li>
                                <strong><?= htmlspecialchars($notification['mensagem']) ?></strong>
                                <span><?= formatDate($notification['created_at'], true) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </article>
        </section>
    </main>
</body>
</html>
