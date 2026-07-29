<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) redirect('index.php');

$user_id = $_SESSION['user_id'];
$currentMonth = $_GET['mes'] ?? date('Y-m');

if (!preg_match('/^\d{4}-\d{2}$/', $currentMonth)) {
    $currentMonth = date('Y-m');
}

[$year, $month] = array_map('intval', explode('-', $currentMonth));
$monthStart = new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month));
$monthEnd = $monthStart->modify('last day of this month');
$daysInMonth = (int) $monthEnd->format('d');
$firstWeekday = (int) $monthStart->format('N');

$stmt = $pdo->prepare("SELECT * FROM ponto WHERE funcionario_id = ? AND DATE_FORMAT(data, '%Y-%m') = ? ORDER BY data ASC");
$stmt->execute([$user_id, $currentMonth]);
$registros = $stmt->fetchAll();

$registrosPorDia = [];
foreach ($registros as $registro) {
    $registrosPorDia[$registro['data']] = $registro;
}

$stmt = $pdo->prepare("SELECT COALESCE(SUM(horas_trabalhadas),0) as total_horas, COALESCE(SUM(horas_extras),0) as total_extras, COUNT(*) as total_registros FROM ponto WHERE funcionario_id = ? AND DATE_FORMAT(data, '%Y-%m') = ?");
$stmt->execute([$user_id, $currentMonth]);
$resumo = $stmt->fetch();

$prevMonth = (new DateTimeImmutable($currentMonth . '-01'))->modify('-1 month')->format('Y-m');
$nextMonth = (new DateTimeImmutable($currentMonth . '-01'))->modify('+1 month')->format('Y-m');

$legenda = [
    'presenca' => ['label' => 'Presença', 'color' => 'rgba(79,125,255,0.16)', 'border' => 'rgba(79,125,255,0.44)', 'text' => '#4f7dff'],
    'atraso' => ['label' => 'Atraso', 'color' => 'rgba(245, 158, 11, 0.2)', 'border' => 'rgba(245, 158, 11, 0.6)', 'text' => '#F59E0B'],
    'extra' => ['label' => 'Extra', 'color' => 'rgba(58, 134, 255, 0.2)', 'border' => 'rgba(58, 134, 255, 0.6)', 'text' => '#3A86FF'],
    'falta' => ['label' => 'Sem registro', 'color' => 'rgba(255,255,255,0.03)', 'border' => 'rgba(255,255,255,0.06)', 'text' => '#9ca3af'],
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário de Ponto - TechManager</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 12px;
        }
        .calendar-header {
            font-size: 12px;
            text-transform: uppercase;
            opacity: 0.6;
            margin-bottom: 8px;
            text-align: center;
        }
        .calendar-day {
            min-height: 96px;
            border-radius: 12px;
            padding: 10px;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.03);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .calendar-day.empty {
            opacity: 0.35;
        }
        .calendar-day span.day-number {
            font-size: 14px;
            font-weight: 700;
        }
        .calendar-day .day-meta {
            font-size: 11px;
            line-height: 1.3;
            opacity: 0.8;
        }
        .legend-row {
            display:flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 12px;
        }
        .legend-item {
            display:flex;
            align-items:center;
            gap:8px;
            padding:8px 10px;
            border-radius:999px;
            background: rgba(255,255,255,0.03);
            font-size: 12px;
            border:1px solid rgba(255,255,255,0.06);
        }
        .legend-dot {
            width:10px;
            height:10px;
            border-radius:999px;
        }
    </style>
</head>
<body>
    <?php include 'resources/views/partials/sidebar.php'; ?>

    <main class="main-content">
        <?php
        $title = 'Calendário de Ponto';
        $subtitle = 'Visualize entradas, atrasos e horas extras por dia em um painel mais visual.';
        $icon = 'fas fa-clock';
        $pills = [($resumo['total_registros'] ?? 0) . ' registros'];
        include 'resources/views/partials/header.php';
        ?>

        <div class="page-actions">
            <a class="btn" href="?mes=<?= $prevMonth ?>"><i class="fas fa-chevron-left"></i> Mês anterior</a>
            <div class="card page-info-card" style="min-width: 160px; text-align:center;">
                <strong><?= $monthStart->format('F Y') ?></strong>
            </div>
            <a class="btn" href="?mes=<?= $nextMonth ?>">Próximo mês <i class="fas fa-chevron-right"></i></a>
        </div>

        <section class="dashboard-hero">
            <div class="hero-copy">
                <h1>Resumo mensal</h1>
                <p>Acompanhe desempenho de jornada, horas trabalhadas e extras do mês.</p>
            </div>
            <div class="hero-pills">
                <div class="hero-pill"><span class="pill-dot"></span> <?= number_format((float) $resumo['total_horas'], 1, ',', '.') ?>h trabalhadas</div>
                <div class="hero-pill"><span class="pill-dot"></span> <?= number_format((float) $resumo['total_extras'], 1, ',', '.') ?>h extras</div>
            </div>
        </section>

        <div class="grid">
            <div class="card">
                <h3>Resumo do mês</h3>
                <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-top: 16px;">
                    <div class="card" style="padding: 16px;">
                        <p style="opacity:0.6; font-size:12px;">Horas trabalhadas</p>
                        <h2 style="margin-top:8px;"><?= number_format((float) $resumo['total_horas'], 1, ',', '.') ?>h</h2>
                    </div>
                    <div class="card" style="padding: 16px;">
                        <p style="opacity:0.6; font-size:12px;">Horas extras</p>
                        <h2 style="margin-top:8px;"><?= number_format((float) $resumo['total_extras'], 1, ',', '.') ?>h</h2>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3>Legenda</h3>
                <div class="legend-row">
                    <?php foreach ($legenda as $key => $item): ?>
                        <div class="legend-item">
                            <span class="legend-dot" style="background: <?= $item['text'] ?>;"></span>
                            <?= $item['label'] ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="calendar-grid" style="margin-top: 10px;">
                <?php foreach (['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'] as $weekday): ?>
                    <div class="calendar-header"><?= $weekday ?></div>
                <?php endforeach; ?>

                <?php
                for ($i = 1; $i < $firstWeekday; $i++) {
                    echo '<div class="calendar-day empty"></div>';
                }

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    $registro = $registrosPorDia[$date] ?? null;
                    $status = $registro['status'] ?? 'falta';
                    $statusInfo = $legenda[$status] ?? $legenda['falta'];

                    echo '<div class="calendar-day" style="background:' . $statusInfo['color'] . '; border-color:' . $statusInfo['border'] . '; color:' . $statusInfo['text'] . ';">';
                    echo '<div style="display:flex; justify-content:space-between; align-items:center;">';
                    echo '<span class="day-number">' . $day . '</span>';
                    echo '<span style="font-size:10px; padding:2px 6px; border-radius:999px; background: rgba(255,255,255,0.12); color: inherit;">' . strtoupper($status) . '</span>';
                    echo '</div>';

                    if ($registro) {
                        echo '<div class="day-meta">';
                        echo '<div>Entrada: ' . ($registro['entrada'] ?? '--:--') . '</div>';
                        echo '<div>Saída: ' . ($registro['saida'] ?? '--:--') . '</div>';
                        echo '<div>Extras: ' . ($registro['horas_extras'] ?? 0) . 'h</div>';
                        echo '</div>';
                    } else {
                        echo '<div class="day-meta">Sem registro</div>';
                    }

                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </main>
</body>
</html>
