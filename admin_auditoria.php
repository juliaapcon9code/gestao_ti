<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (!isAdmin()) redirect('index.php');

$sql = "SELECT ls.id, ls.acao, ls.detalhes, ls.ip, ls.created_at, f.nome as funcionario
        FROM logs_sistema ls
        LEFT JOIN funcionarios f ON f.id = ls.funcionario_id
        ORDER BY ls.created_at DESC
        LIMIT 100";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$logs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoria do Sistema - TechManager</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'resources/views/partials/sidebar.php'; ?>

    <main class="main-content">
        <?php
        $title = 'Auditoria do Sistema';
        $subtitle = 'Histórico completo de ações e mudanças com visão centralizada.';
        $icon = 'fas fa-shield-alt';
        $pills = [count($logs) . ' registros'];
        include 'resources/views/partials/header.php';
        ?>

        <section class="dashboard-hero">
            <div class="hero-copy">
                <h1>Rastreamento de ações</h1>
                <p>Monitore entradas, alterações e eventos críticos do ambiente com mais clareza.</p>
            </div>
            <div class="hero-pills">
                <div class="hero-pill"><span class="pill-dot"></span> <?= count($logs) ?> eventos</div>
                <div class="hero-pill"><span class="pill-dot"></span> Monitoramento ativo</div>
            </div>
        </section>

        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
                <h3>Últimas ações registradas</h3>
                <span style="font-size: 12px; opacity: 0.6;">Mostrando os 100 registros mais recentes</span>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Usuário</th>
                        <th>Ação</th>
                        <th>Detalhes</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
                            <td><?= htmlspecialchars($log['funcionario'] ?? 'Sistema') ?></td>
                            <td><strong><?= htmlspecialchars($log['acao']) ?></strong></td>
                            <td><?= htmlspecialchars($log['detalhes'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($log['ip'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
