<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) redirect('index.php');

$user_id = $_SESSION['user_id'];
$hoje = date('Y-m-d');

// Buscar registro de ponto de hoje
$stmt = $pdo->prepare("SELECT * FROM ponto WHERE funcionario_id = ? AND data = ?");
$stmt->execute([$user_id, $hoje]);
$ponto_hoje = $stmt->fetch();

// Buscar tickets atribuídos
$stmt = $pdo->prepare("SELECT COUNT(*) FROM servicos WHERE funcionario_id = ? AND status != 'finalizado'");
$stmt->execute([$user_id]);
$tickets_ativos = $stmt->fetchColumn();

// Buscar notificações não lidas
$stmt = $pdo->prepare("SELECT * FROM notificacoes WHERE funcionario_id = ? AND lida = 0 ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$notificacoes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Painel - TechManager</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'resources/views/partials/sidebar.php'; ?>

    <main class="main-content">
        <?php
        $title = 'Olá, ' . explode(' ', $_SESSION['user_nome'])[0] . '!';
        $subtitle = 'Seu painel diário com ponto, chamados e acompanhamento de tarefas.';
        $icon = 'fas fa-home';
        $pills = [date('d/m/Y'), $tickets_ativos . ' ativos'];
        include 'resources/views/partials/header.php';
        ?>

        <section class="dashboard-hero">
            <div class="hero-copy">
                <h1>Resumo do dia</h1>
                <p>Acompanhe registros, notificações e atividades em andamento com uma visão organizada e mais clara.</p>
            </div>
            <div class="hero-pills">
                <div class="hero-pill"><span class="pill-dot"></span> Ponto diário</div>
                <div class="hero-pill"><span class="pill-dot"></span> Suporte em execução</div>
                <div class="hero-pill"><span class="pill-dot"></span> Alertas em tempo real</div>
            </div>
        </section>

        <div class="grid">
            <div class="card stat-card" style="grid-column: span 2;">
                <h3>Registro de Ponto</h3>
                <div style="display: flex; gap: 15px; margin-top: 20px;">
                    <form action="modules/ponto.php" method="POST">
                        <input type="hidden" name="tipo" value="entrada">
                        <button type="submit" class="btn btn-primary" <?= ($ponto_hoje && $ponto_hoje['entrada']) ? 'disabled' : '' ?>>
                            <i class="fas fa-sign-in-alt"></i> Entrada
                        </button>
                    </form>
                    <form action="modules/ponto.php" method="POST">
                        <input type="hidden" name="tipo" value="pausa">
                        <button type="submit" class="btn" style="background: var(--warning-color); color: white;" <?= (!$ponto_hoje || $ponto_hoje['saida_pausa'] || $ponto_hoje['saida']) ? 'disabled' : '' ?>>
                            <i class="fas fa-coffee"></i> Pausa
                        </button>
                    </form>
                    <form action="modules/ponto.php" method="POST">
                        <input type="hidden" name="tipo" value="retorno">
                        <button type="submit" class="btn" style="background: var(--success-color); color: white;" <?= (!$ponto_hoje || !$ponto_hoje['saida_pausa'] || $ponto_hoje['retorno_pausa'] || $ponto_hoje['saida']) ? 'disabled' : '' ?>>
                            <i class="fas fa-undo"></i> Retorno
                        </button>
                    </form>
                    <form action="modules/ponto.php" method="POST">
                        <input type="hidden" name="tipo" value="saida">
                        <button type="submit" class="btn" style="background: var(--danger-color); color: white;" <?= (!$ponto_hoje || $ponto_hoje['saida']) ? 'disabled' : '' ?>>
                            <i class="fas fa-sign-out-alt"></i> Saída
                        </button>
                    </form>
                </div>
                <div style="margin-top: 20px; display: flex; gap: 30px; opacity: 0.8; font-size: 14px;">
                    <span>Entrada: <?= $ponto_hoje['entrada'] ?? '--:--' ?></span>
                    <span>Pausa: <?= $ponto_hoje['saida_pausa'] ?? '--:--' ?></span>
                    <span>Retorno: <?= $ponto_hoje['retorno_pausa'] ?? '--:--' ?></span>
                    <span>Saída: <?= $ponto_hoje['saida'] ?? '--:--' ?></span>
                </div>
            </div>

            <div class="card stat-card">
                <div class="stat-card-top">
                    <div>
                        <div class="stat-label">Meus Tickets</div>
                        <div class="stat-value"><?= $tickets_ativos ?></div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-ticket-alt"></i></div>
                </div>
                <div class="stat-foot">
                    <span>Tickets em aberto</span>
                    <span><i class="fas fa-arrow-right"></i></span>
                </div>
                <a href="meus_tickets.php" class="btn btn-primary" style="width: 100%; margin-top: 6px; justify-content: center;">Ver Todos</a>
            </div>
        </div>

        <div class="grid">
            <div class="card chart-card">
                <div class="chart-card-header">
                    <div>
                        <h3>Últimas Notificações</h3>
                        <small>Atualização em tempo real</small>
                    </div>
                    <span class="status-badge"><i class="fas fa-bell"></i> Live</span>
                </div>
                <div id="notificationList" style="margin-top: 10px;">
                    <?php if (empty($notificacoes)): ?>
                        <p style="opacity: 0.5; font-size: 14px;">Nenhuma notificação nova.</p>
                    <?php else: ?>
                        <?php foreach ($notificacoes as $n): ?>
                            <div style="padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 14px;">
                                <?= htmlspecialchars($n['mensagem']) ?>
                                <div style="font-size: 10px; opacity: 0.5; margin-top: 5px;"><?= date('d/m H:i', strtotime($n['created_at'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card stat-card">
                <div class="stat-card-top">
                    <div>
                        <div class="stat-label">Resumo de Horas</div>
                        <div class="stat-value">124h</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                </div>
                <div class="stat-foot">
                    <span>de 160h previstas</span>
                    <span>77%</span>
                </div>
                <div style="width: 100%; height: 8px; background: rgba(255,255,255,0.08); border-radius: 999px; overflow: hidden; margin-top: 5px;">
                    <div style="width: 77%; height: 100%; background: linear-gradient(90deg, #5c80ff, #3f5bff); border-radius: inherit;"></div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const badge = document.getElementById('notificationBadge');
        const notificationList = document.getElementById('notificationList');

        async function refreshNotifications() {
            try {
                const response = await fetch('ajax/updates.php');
                const data = await response.json();

                if (badge) {
                    const count = Number(data.notification_count || 0);
                    badge.textContent = count;
                    badge.style.display = count > 0 ? 'inline-flex' : 'none';
                }

                if (notificationList && Array.isArray(data.notifications)) {
                    if (data.notifications.length === 0) {
                        notificationList.innerHTML = '<p style="opacity: 0.5; font-size: 14px;">Nenhuma notificação nova.</p>';
                        return;
                    }

                    notificationList.innerHTML = data.notifications.map(item => `
                        <div style="padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 14px;">
                            ${item.mensagem}
                            <div style="font-size: 10px; opacity: 0.5; margin-top: 5px;">${new Date(item.created_at).toLocaleString('pt-BR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit'})}</div>
                        </div>
                    `).join('');
                }
            } catch (error) {
                console.error('Erro ao sincronizar notificações', error);
            }
        }

        refreshNotifications();
        setInterval(refreshNotifications, 30000);
    </script>
</body>
</html>
