<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (!isAdmin()) redirect('index.php');

// Buscar estatísticas
$total_func = $pdo->query("SELECT COUNT(*) FROM funcionarios")->fetchColumn();
$func_ativos = $pdo->query("SELECT COUNT(*) FROM funcionarios WHERE status = 'ativo'")->fetchColumn();
$tickets_abertos = $pdo->query("SELECT COUNT(*) FROM servicos WHERE status != 'finalizado'")->fetchColumn();
$pagamentos_mes = $pdo->query("SELECT SUM(total_receber) FROM pagamento WHERE mes = MONTH(CURRENT_DATE) AND ano = YEAR(CURRENT_DATE)")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TechManager</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'resources/views/partials/sidebar.php'; ?>

    <main class="main-content">
        <?php
        $title = 'Dashboard Administrativo';
        $subtitle = 'Indicadores operacionais e governança da equipe em tempo real.';
        $icon = 'fas fa-chart-line';
        $pills = [
            'Operação ativa',
            "$func_ativos ativos",
            "$tickets_abertos pendentes"
        ];
        include 'resources/views/partials/header.php';
        ?>

        <section class="dashboard-hero">
            <div class="hero-copy">
                <h1>Visão geral da operação</h1>
                <p>Monitore receita, presença e tickets em um painel com indicadores de alto nível para decisões rápidas e consistentes.</p>
            </div>
            <div class="hero-pills">
                <div class="hero-pill"><span class="pill-dot"></span> Operação ativa</div>
                <div class="hero-pill"><span class="pill-dot"></span> <?= $func_ativos ?> ativos</div>
                <div class="hero-pill"><span class="pill-dot"></span> <?= $tickets_abertos ?> pendentes</div>
            </div>
        </section>

        <div class="grid">
            <?php
            $label = 'Total Funcionários';
            $value = $total_func;
            $icon = 'fas fa-users';
            $footer = 'Pessoas cadastradas';
            $footerIcon = 'fas fa-arrow-trend-up';
            $variant = 'accent';
            include 'resources/views/partials/stat_card.php';

            $label = 'Ativos';
            $value = $func_ativos;
            $icon = 'fas fa-user-check';
            $footer = 'Colaboradores no sistema';
            $footerIcon = 'fas fa-layer-group';
            $variant = 'success';
            include 'resources/views/partials/stat_card.php';

            $label = 'Tickets Pendentes';
            $value = $tickets_abertos;
            $icon = 'fas fa-ticket-alt';
            $footer = 'Chamados em aberto';
            $footerIcon = 'fas fa-bolt';
            $variant = 'warning';
            include 'resources/views/partials/stat_card.php';

            $label = 'Folha do Mês';
            $value = formatMoney($pagamentos_mes ?? 0);
            $icon = 'fas fa-dollar-sign';
            $footer = 'Receita em processamento';
            $footerIcon = 'fas fa-circle-check';
            $variant = 'accent';
            include 'resources/views/partials/stat_card.php';
            ?>
        </div>

        <div class="grid" style="grid-template-columns: 2fr 1fr;">
            <div class="card chart-card">
                <div class="chart-card-header">
                    <div>
                        <h3>Presença Semanal</h3>
                        <small>Registros por dia da semana</small>
                    </div>
                    <span class="status-badge"><i class="fas fa-chart-area"></i> Monitor</span>
                </div>
                <canvas id="presencaChart" style="margin-top: 10px;"></canvas>
            </div>
            <div class="card chart-card">
                <div class="chart-card-header">
                    <div>
                        <h3>Tickets por Status</h3>
                        <small>Distribuição de chamados</small>
                    </div>
                    <span class="status-badge"><i class="fas fa-ticket"></i> Acompanhamento</span>
                </div>
                <canvas id="ticketsChart" style="margin-top: 10px;"></canvas>
            </div>
        </div>
    </main>

    <script>
        const ctx1 = document.getElementById('presencaChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex'],
                datasets: [{
                    label: 'Funcionários Presentes',
                    data: [12, 15, 14, 16, 13],
                    borderColor: '#5c80ff',
                    backgroundColor: 'rgba(92, 128, 255, 0.16)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointBackgroundColor: '#dbe3ff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: 'rgba(148,163,184,0.08)' }, ticks: { color: '#94a3b8' } },
                    y: { grid: { color: 'rgba(148,163,184,0.08)' }, ticks: { color: '#94a3b8' } }
                }
            }
        });

        const ctx2 = document.getElementById('ticketsChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Aberto', 'Andamento', 'Revisão', 'Finalizado'],
                datasets: [{
                    data: [5, 8, 3, 12],
                    backgroundColor: ['#4f7dff', '#fbbf24', '#60a5fa', '#fb7185']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#E6EDF7' }
                    }
                }
            }
        });
    </script>
</body>
</html>
