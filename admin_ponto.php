<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (!isAdmin()) redirect('index.php');

$data = $_GET['data'] ?? date('Y-m-d');

$stmt = $pdo->prepare("SELECT p.*, f.nome FROM ponto p JOIN funcionarios f ON p.funcionario_id = f.id WHERE p.data = ?");
$stmt->execute([$data]);
$pontos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Ponto - TechManager</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'resources/views/partials/sidebar.php'; ?>

    <main class="main-content">
        <?php
        $title = 'Registros de Ponto';
        $subtitle = 'Visualize entradas, pausas e status de presença do dia em um painel direto.';
        $icon = 'fas fa-clock';
        $pills = [count($pontos) . ' registros'];
        include 'resources/views/partials/header.php';
        ?>

        <div class="page-actions">
            <form method="GET" class="form-inline">
                <input type="date" name="data" value="<?= $data ?>">
                <button type="submit" class="btn btn-primary">Ver Data</button>
            </form>
        </div>

        <section class="dashboard-hero">
            <div class="hero-copy">
                <h1>Presença do dia</h1>
                <p>Monitore a jornada do time e os pontos críticos com um painel mais claro.</p>
            </div>
            <div class="hero-pills">
                <div class="hero-pill"><span class="pill-dot"></span> Data <?= date('d/m/Y', strtotime($data)) ?></div>
                <div class="hero-pill"><span class="pill-dot"></span> <?= count($pontos) ?> entradas</div>
            </div>
        </section>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Funcionário</th>
                        <th>Entrada</th>
                        <th>Saída Almoço</th>
                        <th>Retorno Almoço</th>
                        <th>Saída</th>
                        <th>Total Horas</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pontos)): ?>
                        <tr><td colspan="7" style="text-align: center; opacity: 0.5;">Nenhum registro para esta data.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($pontos as $p): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($p['nome']) ?></strong></td>
                        <td><?= $p['entrada'] ?></td>
                        <td><?= $p['saida_pausa'] ?? '--:--' ?></td>
                        <td><?= $p['retorno_pausa'] ?? '--:--' ?></td>
                        <td><?= $p['saida'] ?? '--:--' ?></td>
                        <td><?= $p['horas_trabalhadas'] ?>h (+<?= $p['horas_extras'] ?>h)</td>
                        <td>
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; background: <?= $p['status'] === 'presenca' ? 'rgba(79,125,255,0.12)' : 'rgba(245, 158, 11, 0.1)' ?>; color: <?= $p['status'] === 'presenca' ? 'var(--accent-color)' : 'var(--warning-color)' ?>;">
                                <?= ucfirst($p['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
