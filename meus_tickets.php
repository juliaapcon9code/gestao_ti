<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) redirect('index.php');

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT s.*, f.nome as responsavel FROM servicos s LEFT JOIN funcionarios f ON f.id = s.funcionario_id WHERE s.funcionario_id = ? ORDER BY FIELD(s.status, 'aberto', 'andamento', 'revisao', 'finalizado'), s.prioridade DESC, s.created_at DESC");
$stmt->execute([$user_id]);
$tickets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Tickets - TechManager</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'resources/views/partials/sidebar.php'; ?>

    <main class="main-content">
        <?php
        $title = 'Meus Tickets';
        $subtitle = 'Acompanhe suas solicitações, comentários e SLA em um painel mais enxuto.';
        $icon = 'fas fa-ticket-alt';
        $pills = [count($tickets) . ' tickets'];
        include 'resources/views/partials/header.php';
        ?>

        <section class="dashboard-hero">
            <div class="hero-copy">
                <h1>Meu backlog</h1>
                <p>Revise o status dos chamados e mantenha o fluxo de comunicação com a equipe.</p>
            </div>
            <div class="hero-pills">
                <div class="hero-pill"><span class="pill-dot"></span> <?= count($tickets) ?> em análise</div>
                <div class="hero-pill"><span class="pill-dot"></span> Comentários e SLA</div>
            </div>
        </section>

        <div class="grid">
            <?php foreach ($tickets as $ticket):
                $dias = max(0, (int) floor((time() - strtotime($ticket['created_at'])) / 86400));
                $slaAlert = $dias >= 3 ? 'danger' : ($dias >= 2 ? 'warning' : 'success');
                $statusClass = $ticket['status'] === 'finalizado' ? 'success' : ($ticket['status'] === 'andamento' ? 'warning' : 'accent');
            ?>
                <div class="card">
                    <div style="display:flex; justify-content:space-between; align-items:start; gap: 12px; margin-bottom: 16px;">
                        <div>
                            <h3><?= htmlspecialchars($ticket['titulo']) ?></h3>
                            <p style="opacity: 0.7; margin-top: 8px; font-size: 13px;">Setor: <?= htmlspecialchars($ticket['setor']) ?></p>
                        </div>
                        <div style="display:flex; flex-direction:column; gap:8px; align-items:flex-end;">
                            <span style="font-size: 11px; padding: 4px 8px; border-radius:999px; background: rgba(58,134,255,0.15); color: #9ec5ff;"><?= ucfirst($ticket['status']) ?></span>
                            <span style="font-size: 11px; padding: 4px 8px; border-radius:999px; background: rgba(255,255,255,0.06);">Prioridade <?= ucfirst($ticket['prioridade']) ?></span>
                        </div>
                    </div>

                    <p style="opacity: 0.8; font-size: 14px; line-height: 1.5; margin-bottom: 12px;"><?= nl2br(htmlspecialchars($ticket['descricao'])) ?></p>

                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px; font-size: 12px; opacity: 0.65;">
                        <span>Responsável: <?= htmlspecialchars($ticket['responsavel'] ?? 'Sem responsável') ?></span>
                        <span>SLA: <strong><?= $dias ?> dia<?= $dias === 1 ? '' : 's' ?></strong></span>
                    </div>

                    <div style="background: rgba(255,255,255,0.04); border-radius: 10px; padding: 12px; margin-bottom: 12px;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 8px;">
                            <strong style="font-size: 12px; text-transform: uppercase; opacity: 0.7;">Histórico</strong>
                            <span style="font-size: 10px; padding: 3px 6px; border-radius:999px; background: rgba(255,255,255,0.08);">Aberto em <?= date('d/m/Y', strtotime($ticket['created_at'])) ?></span>
                        </div>

                        <?php
                        $commentStmt = $pdo->prepare("SELECT tc.comentario, tc.created_at, f.nome FROM tickets_comentarios tc LEFT JOIN funcionarios f ON f.id = tc.funcionario_id WHERE tc.ticket_id = ? ORDER BY tc.created_at ASC");
                        $commentStmt->execute([$ticket['id']]);
                        $comments = $commentStmt->fetchAll();

                        if (empty($comments)) {
                            echo '<p style="font-size: 12px; opacity: 0.5;">Nenhum comentário registrado ainda.</p>';
                        } else {
                            foreach ($comments as $comment) {
                                echo '<div style="padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.06); font-size: 12px;">';
                                echo '<div style="display:flex; justify-content:space-between; margin-bottom: 4px;">';
                                echo '<strong>' . htmlspecialchars($comment['nome']) . '</strong>';
                                echo '<span style="opacity: 0.5;">' . date('d/m H:i', strtotime($comment['created_at'])) . '</span>';
                                echo '</div>';
                                echo '<p style="opacity: 0.8; line-height: 1.4;">' . htmlspecialchars($comment['comentario']) . '</p>';
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>

                    <form action="modules/tickets.php?action=comment" method="POST" style="display:flex; flex-direction:column; gap:10px;">
                        <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                        <textarea name="comentario" rows="3" placeholder="Adicionar comentário ao ticket..." required style="resize: vertical;"></textarea>
                        <button type="submit" class="btn btn-primary" style="justify-content:center;">Enviar comentário</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
