<?php
/**
 * Header Partial
 * Componente reutilizável de cabeçalho com user info
 * 
 * Variáveis esperadas:
 * - $title: Título da página
 * - $subtitle: Subtítulo/descrição
 * - $icon: Ícone Font Awesome (ex: 'fas fa-chart-line')
 * - $pills: Array de badges informativos (opcional)
 * - $actionButton: Array com 'text', 'href', 'id' para botão de ação (opcional)
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$pageTitle = $title ?? 'Dashboard';
$pageIcon = $icon ?? 'fas fa-home';
$pageSubtitle = $subtitle ?? '';

$notifications = [];
if (isset($pdo)) {
    $notifStmt = $pdo->prepare("SELECT id, mensagem, created_at FROM notificacoes WHERE funcionario_id = ? ORDER BY created_at DESC LIMIT 5");
    $notifStmt->execute([$_SESSION['user_id'] ?? 0]);
    $notifications = $notifStmt->fetchAll();
}
?>

<header>
    <div>
        <?php if (!empty($breadcrumbs) && is_array($breadcrumbs)): ?>
            <div class="breadcrumbs">
                <?php foreach ($breadcrumbs as $index => $crumb): ?>
                    <span><?= htmlspecialchars($crumb) ?></span>
                    <?php if ($index < count($breadcrumbs) - 1): ?>
                        <span class="breadcrumb-separator">/</span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="hero-kicker">
            <i class="<?= htmlspecialchars($pageIcon) ?>"></i>
            <?= htmlspecialchars($pageTitle) ?>
        </div>
        <?php if (!empty($pageSubtitle)): ?>
            <p><?= htmlspecialchars($pageSubtitle) ?></p>
        <?php endif; ?>
    </div>

    <div class="header-side">
        <?php
        $userName = htmlspecialchars($_SESSION['user_nome'] ?? 'Usuário');
        $userRole = htmlspecialchars($_SESSION['user_role'] ?? 'Colaborador');
        $initials = implode('', array_slice(explode(' ', $userName), 0, 2));
        $unreadCount = count(array_filter($notifications, fn($item) => (int)($item['id'] ?? 0) > 0));
        ?>
        <a href="meu_perfil.php" class="notification-pill" title="Abrir notificações">
            <i class="fas fa-bell"></i>
            <span><?= count($notifications) ?></span>
        </a>
        <a href="meu_perfil.php" class="user-card">
            <div class="avatar"><?= strtoupper(substr($initials, 0, 2)) ?></div>
            <div>
                <strong><?= $userName ?></strong>
                <span><?= ucfirst($userRole) ?></span>
            </div>
        </a>
        <?php if (isset($actionButton) && is_array($actionButton)): ?>
            <button class="btn btn-primary" 
                    onclick="<?= $actionButton['onclick'] ?? "window.location.href='{$actionButton['href']}'" ?>"
                    <?php if (isset($actionButton['id'])): ?>id="<?= htmlspecialchars($actionButton['id']) ?>"<?php endif; ?>>
                <i class="<?= htmlspecialchars($actionButton['icon'] ?? 'fas fa-plus') ?>"></i>
                <?= htmlspecialchars($actionButton['text'] ?? 'Ação') ?>
            </button>
        <?php endif; ?>
    </div>
</header>

<?php if (!empty($pills)): ?>
<section class="dashboard-hero">
    <div class="hero-pills">
        <?php foreach ($pills as $pill): ?>
            <div class="hero-pill">
                <span class="pill-dot"></span> <?= htmlspecialchars($pill) ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
