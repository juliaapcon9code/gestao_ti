<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (!isSupervisor()) redirect('index.php');

$setor = $_SESSION['user_setor'] ?? 'Suporte';

$stmt = $pdo->prepare("SELECT COUNT(*) FROM funcionarios WHERE setor = ? AND role IN ('funcionario', 'supervisor') AND status = 'ativo'");
$stmt->execute([$setor]);
$equipeAtiva = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM servicos WHERE setor = ? AND status != 'finalizado'");
$stmt->execute([$setor]);
$ticketsSetor = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM ponto p JOIN funcionarios f ON f.id = p.funcionario_id WHERE f.setor = ? AND p.data = CURDATE() AND p.status = 'atraso'");
$stmt->execute([$setor]);
$atrasosHoje = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM ponto p JOIN funcionarios f ON f.id = p.funcionario_id WHERE f.setor = ? AND p.data = CURDATE() AND p.status = 'extra'");
$stmt->execute([$setor]);
$extrasHoje = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT f.nome, f.cargo, f.status, p.data, p.status, p.entrada, p.saida FROM funcionarios f LEFT JOIN ponto p ON p.funcionario_id = f.id AND p.data = CURDATE() WHERE f.setor = ? AND f.role = 'funcionario' ORDER BY f.nome ASC");
$stmt->execute([$setor]);
$equipes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Supervisor - TechManager</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'resources/views/partials/sidebar.php'; ?>

    <main class="main-content">
        <?php
        $title = 'Dashboard de Supervisão';
        $subtitle = 'Acompanhamento do setor ' . htmlspecialchars($setor) . ' com foco em equipe, tempo e prioridades.';
        $icon = 'fas fa-layer-group';
        $pills = [htmlspecialchars($setor), $equipeAtiva . ' na equipe', $ticketsSetor . ' tickets'];
        include 'resources/views/partials/header.php';
        ?>

        <section class="dashboard-hero">
            <div class="hero-copy">
                <h1>Monitoramento do setor</h1>
                <p>Centralize presença, atrasos e tickets do seu time em um painel de visão rápida e gestão preventiva.</p>
            </div>
            <div class="hero-pills">
                <div class="hero-pill"><span class="pill-dot"></span> <?= htmlspecialchars($setor) ?></div>
                <div class="hero-pill"><span class="pill-dot"></span> <?= $equipeAtiva ?> na equipe</div>
                <div class="hero-pill"><span class="pill-dot"></span> <?= $ticketsSetor ?> tickets</div>
            </div>
        </section>

        <div class="grid">
            <div class="card stat-card">
                <div class="stat-card-top">
                    <div>
                        <div class="stat-label">Equipe ativa</div>
                        <div class="stat-value"><?= $equipeAtiva ?></div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                </div>
                <div class="stat-foot">
                    <span>Colaboradores em operação</span>
                    <span><i class="fas fa-chart-pie"></i></span>
                </div>
            </div>
            <div class="card stat-card">
                <div class="stat-card-top">
                    <div>
                        <div class="stat-label">Tickets do setor</div>
                        <div class="stat-value"><?= $ticketsSetor ?></div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-ticket-alt"></i></div>
                </div>
                <div class="stat-foot">
                    <span>Chamados em análise</span>
                    <span><i class="fas fa-arrow-trend-up"></i></span>
                </div>
            </div>
            <div class="card stat-card">
                <div class="stat-card-top">
                    <div>
                        <div class="stat-label">Atrasos hoje</div>
                        <div class="stat-value" style="color: var(--warning-color);"><?= $atrasosHoje ?></div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
                <div class="stat-foot">
                    <span>Registros fora do horário</span>
                    <span><i class="fas fa-bell"></i></span>
                </div>
            </div>
            <div class="card stat-card">
                <div class="stat-card-top">
                    <div>
                        <div class="stat-label">Extras hoje</div>
                        <div class="stat-value" style="color: var(--accent-strong);"><?= $extrasHoje ?></div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                </div>
                <div class="stat-foot">
                    <span>Horas adicionais registradas</span>
                    <span><i class="fas fa-circle-check"></i></span>
                </div>
            </div>
        </div>

        <div class="grid" style="grid-template-columns: 1.5fr 1fr;"> 
            <div class="card">
                <h3>Alinhamento da equipe</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Funcionário</th>
                            <th>Cargo</th>
                            <th>Status</th>
                            <th>Ponto do dia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($equipes as $membro): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($membro['nome']) ?></strong></td>
                            <td><?= htmlspecialchars($membro['cargo']) ?></td>
                            <td>
                                <span style="padding:4px 8px; border-radius:999px; font-size:11px; background: <?= $membro['status']==='ativo' ? 'rgba(79,125,255,0.12)' : 'rgba(239,68,68,0.12)' ?>; color: <?= $membro['status']==='ativo' ? 'var(--accent-color)' : 'var(--danger-color)' ?>;">
                                    <?= ucfirst($membro['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($membro['data']): ?>
                                    <strong><?= $membro['status'] === 'atraso' ? 'Atraso' : 'Presença' ?></strong><br>
                                    <span style="font-size:11px; opacity:0.6;">Entrada: <?= $membro['entrada'] ?? '--:--' ?></span>
                                <?php else: ?>
                                    <span style="font-size:11px; opacity:0.6;">Sem registro</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h3>Prioridade do setor</h3>
                <div style="display:flex; flex-direction:column; gap:12px; margin-top: 18px;">
                    <?php
                    $prioridades = $pdo->prepare("SELECT prioridade, COUNT(*) as total FROM servicos WHERE setor = ? GROUP BY prioridade ORDER BY FIELD(prioridade, 'alta', 'media', 'baixa')");
                    $prioridades->execute([$setor]);
                    $resumoPrioridades = $prioridades->fetchAll();
                    foreach ($resumoPrioridades as $p):
                        $percent = min(100, round(($p['total'] / max(1, $ticketsSetor)) * 100));
                        echo '<div>'; 
                        echo '<div style="display:flex; justify-content:space-between; margin-bottom: 8px;">';
                        echo '<span style="text-transform: capitalize;">' . htmlspecialchars($p['prioridade']) . '</span>';
                        echo '<strong>' . $p['total'] . '</strong>';
                        echo '</div>';
                        echo '<div style="width:100%; height: 8px; border-radius:999px; background: rgba(255,255,255,0.08); overflow:hidden;">';
                        echo '<div style="width:' . $percent . '%; height:100%; background: ' . ($p['prioridade']=='alta' ? 'var(--danger-color)' : ($p['prioridade']=='media' ? 'var(--warning-color)' : 'var(--success-color)')) . '; border-radius:999px;"></div>';
                        echo '</div>';
                        echo '</div>';
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
