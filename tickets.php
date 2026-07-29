<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) redirect('index.php');

$statuses = ['aberto', 'andamento', 'revisao', 'finalizado'];
$tickets = [];
foreach ($statuses as $status) {
    $stmt = $pdo->prepare("SELECT s.*, f.nome as responsavel FROM servicos s LEFT JOIN funcionarios f ON s.funcionario_id = f.id WHERE s.status = ? ORDER BY s.prioridade DESC, s.created_at DESC");
    $stmt->execute([$status]);
    $tickets[$status] = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets Kanban - TechManager</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .priority-alta { border-left: 4px solid var(--danger-color); }
        .priority-media { border-left: 4px solid var(--warning-color); }
        .priority-baixa { border-left: 4px solid var(--success-color); }
    </style>
</head>
<body>
    <?php include 'resources/views/partials/sidebar.php'; ?>

    <main class="main-content">
        <?php
        $title = 'Quadro Kanban';
        $subtitle = 'Visualize backlog, andamento e encerramentos em um fluxo de trabalho organizado.';
        $icon = 'fas fa-ticket-alt';
        $pills = [array_sum(array_map('count', $tickets)) . ' tickets'];
        $actionButton = [
            'text' => 'Abrir Ticket',
            'icon' => 'fas fa-plus',
            'onclick' => "openModal('modalTicket')",
        ];
        include 'resources/views/partials/header.php';
        ?>

        <section class="dashboard-hero">
            <div class="hero-copy">
                <h1>Fluxo de chamados</h1>
                <p>Gerencie prioridades e o ciclo dos tickets com um painel visual mais intuitivo.</p>
            </div>
            <div class="hero-pills">
                <div class="hero-pill"><span class="pill-dot"></span> <?= count($tickets['aberto']) ?? 0 ?> abertos</div>
                <div class="hero-pill"><span class="pill-dot"></span> <?= count($tickets['andamento']) ?? 0 ?> em andamento</div>
                <div class="hero-pill"><span class="pill-dot"></span> <?= count($tickets['revisao']) ?? 0 ?> em revisão</div>
            </div>
        </section>

        <div class="kanban-board">
            <?php foreach ($statuses as $status): ?>
                <div class="kanban-column" id="col-<?= $status ?>" ondrop="drop(event)" ondragover="allowDrop(event)">
                    <h3 style="margin-bottom: 20px; opacity: 0.7; text-transform: uppercase; font-size: 14px;">
                        <?= ucfirst($status) ?> (<?= count($tickets[$status]) ?>)
                    </h3>
                    <div class="kanban-items">
                        <?php foreach ($tickets[$status] as $t):
                            $dias = max(0, (int) floor((time() - strtotime($t['created_at'])) / 86400));
                        ?>
                            <div class="kanban-item priority-<?= $t['prioridade'] ?>" id="ticket-<?= $t['id'] ?>" draggable="true" ondragstart="drag(event)">
                                <h4 style="font-size: 14px; margin-bottom: 8px;"><?= htmlspecialchars($t['titulo']) ?></h4>
                                <p style="font-size: 11px; opacity: 0.7; margin-bottom: 10px; line-height: 1.4;"><?= htmlspecialchars($t['descricao'] ?? 'Sem descrição') ?></p>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px; gap: 10px; flex-wrap: wrap;">
                                    <span style="font-size: 10px; opacity: 0.5;"><?= htmlspecialchars($t['setor']) ?></span>
                                    <span style="font-size: 10px; background: rgba(255,255,255,0.05); padding: 2px 6px; border-radius: 10px;"><?= htmlspecialchars($t['responsavel'] ?? 'Sem resp.') ?></span>
                                </div>
                                <div style="margin-top: 10px; display:flex; justify-content:space-between; align-items:center;">
                                    <span style="font-size: 10px; opacity: 0.6;">Aberto há <?= $dias ?> dia<?= $dias === 1 ? '' : 's' ?></span>
                                    <span style="font-size: 10px; padding: 2px 6px; border-radius:999px; background: rgba(58,134,255,0.15); color: #9ec5ff;">SLA</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Modal Novo Ticket -->
    <div id="modalTicket" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;">
        <div class="card" style="width: 500px;">
            <h3>Novo Ticket</h3>
            <form action="modules/tickets.php?action=create" method="POST" style="margin-top: 20px;">
                <div class="form-group">
                    <label>Título</label>
                    <input type="text" name="titulo" required>
                </div>
                <div class="form-group">
                    <label>Descrição</label>
                    <textarea name="descricao" rows="4"></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Prioridade</label>
                        <select name="prioridade">
                            <option value="baixa">Baixa</option>
                            <option value="media">Média</option>
                            <option value="alta">Alta</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Setor</label>
                        <select name="setor">
                            <option value="Suporte">Suporte</option>
                            <option value="Desenvolvimento">Desenvolvimento</option>
                            <option value="Infraestrutura">Infraestrutura</option>
                        </select>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                    <button type="button" class="btn" onclick="closeModal('modalTicket')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).style.display = 'flex'; }
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }

        function allowDrop(ev) { ev.preventDefault(); }
        function drag(ev) { ev.dataTransfer.setData("text", ev.target.id); }
        function drop(ev) {
            ev.preventDefault();
            var data = ev.dataTransfer.getData("text");
            var ticketId = data.replace('ticket-', '');
            var targetCol = ev.target.closest('.kanban-column').id.replace('col-', '');
            
            fetch('modules/tickets.php?action=update_status', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${ticketId}&status=${targetCol}`
            }).then(() => {
                location.reload();
            });
        }
    </script>
</body>
</html>
