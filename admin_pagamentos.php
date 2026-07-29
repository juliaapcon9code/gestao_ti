<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (!isAdmin()) redirect('index.php');

$mes = $_GET['mes'] ?? date('m');
$ano = $_GET['ano'] ?? date('Y');

$stmt = $pdo->prepare("SELECT p.*, f.nome, f.cargo FROM pagamento p JOIN funcionarios f ON p.funcionario_id = f.id WHERE p.mes = ? AND p.ano = ?");
$stmt->execute([$mes, $ano]);
$pagamentos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Pagamentos - TechManager</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'resources/views/partials/sidebar.php'; ?>

    <main class="main-content">
        <?php
        $title = 'Folha de Pagamento';
        $subtitle = 'Controle salários, extras e descontos por período em um painel operacional organizado.';
        $icon = 'fas fa-file-invoice-dollar';
        $pills = [count($pagamentos) . ' registros'];
        include 'resources/views/partials/header.php';
        ?>

        <div class="page-actions">
            <form method="GET" class="form-inline">
                <select name="mes">
                    <?php for($i=1; $i<=12; $i++): ?>
                        <option value="<?= $i ?>" <?= $mes == $i ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $i, 10)) ?></option>
                    <?php endfor; ?>
                </select>
                <input type="number" name="ano" value="<?= $ano ?>" min="2020" max="<?= date('Y') ?>">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>

            <form action="modules/pagamentos.php?action=gerar" method="POST" class="form-inline">
                <input type="hidden" name="mes" value="<?= $mes ?>">
                <input type="hidden" name="ano" value="<?= $ano ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-sync"></i> Gerar Folha do Mês</button>
            </form>
        </div>

        <section class="dashboard-hero">
            <div class="hero-copy">
                <h1>Financeiro do time</h1>
                <p>Revisão de benefícios, horas extras e total a receber com acompanhamento mensal.</p>
            </div>
            <div class="hero-pills">
                <div class="hero-pill"><span class="pill-dot"></span> <?= count($pagamentos) ?> pagamentos</div>
                <div class="hero-pill"><span class="pill-dot"></span> Período <?= $mes ?>/<?= $ano ?></div>
            </div>
        </section>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Funcionário</th>
                        <th>Salário Base</th>
                        <th>H. Extras</th>
                        <th>Descontos</th>
                        <th>Benefícios</th>
                        <th>Total a Receber</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pagamentos)): ?>
                        <tr><td colspan="8" style="text-align: center; opacity: 0.5;">Nenhum pagamento gerado para este período.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($pagamentos as $p): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($p['nome']) ?></strong><br>
                            <span style="font-size: 12px; opacity: 0.6;"><?= htmlspecialchars($p['cargo']) ?></span>
                        </td>
                        <td><?= formatMoney($p['salario_base']) ?></td>
                        <td><?= formatMoney($p['valor_extras']) ?> (<?= $p['total_horas_extras'] ?>h)</td>
                        <td style="color: var(--danger-color);"><?= formatMoney($p['descontos']) ?></td>
                        <td style="color: var(--success-color);"><?= formatMoney($p['beneficios']) ?></td>
                        <td><strong><?= formatMoney($p['total_receber']) ?></strong></td>
                        <td>
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; background: <?= $p['status'] === 'pago' ? 'rgba(79,125,255,0.12)' : 'rgba(245, 158, 11, 0.1)' ?>; color: <?= $p['status'] === 'pago' ? 'var(--accent-color)' : 'var(--warning-color)' ?>;">
                                <?= ucfirst($p['status']) ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn" style="padding: 5px; color: var(--accent-color);"><i class="fas fa-file-pdf"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
