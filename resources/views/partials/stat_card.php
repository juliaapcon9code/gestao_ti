<?php
/**
 * Stats Card Partial
 * Componente reutilizável para exibir KPIs
 * 
 * Variáveis esperadas:
 * - $label: Rótulo do KPI
 * - $value: Valor do KPI
 * - $icon: Ícone Font Awesome
 * - $footer: Texto do rodapé
 * - $footerIcon: Ícone do rodapé
 * - $variant: 'success', 'warning', 'danger', 'accent' (opcional)
 */

$variant = $variant ?? 'accent';
$variants = [
    'success' => 'rgba(79,125,255,0.12)',
    'warning' => 'rgba(251, 191, 36, 0.12)',
    'danger' => 'rgba(251, 113, 133, 0.12)',
    'accent' => 'rgba(79,125,255,0.12)'
];
$bgColor = $variants[$variant] ?? $variants['accent'];
?>

<div class="card stat-card">
    <div class="stat-card-top">
        <div>
            <div class="stat-label"><?= htmlspecialchars($label ?? '') ?></div>
            <div class="stat-value"><?= htmlspecialchars($value ?? '0') ?></div>
        </div>
        <div class="stat-icon" style="background: <?= $bgColor ?>;">
            <i class="<?= htmlspecialchars($icon ?? 'fas fa-chart-line') ?>"></i>
        </div>
    </div>
    <?php if (!empty($footer)): ?>
    <div class="stat-foot">
        <span><?= htmlspecialchars($footer) ?></span>
        <span><i class="<?= htmlspecialchars($footerIcon ?? 'fas fa-arrow-trend-up') ?>"></i></span>
    </div>
    <?php endif; ?>
</div>
