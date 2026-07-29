<?php
/**
 * Sidebar Partial
 * Componente reutilizável de navegação lateral
 * 
 * Variáveis esperadas:
 * - $currentPage: página ativa para highlighting
 * - $user: dados do usuário ($_SESSION)
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>

<div class="sidebar">
    <div class="logo"><i class="fas fa-microchip"></i> TechManager</div>
    <div class="sidebar-tagline">Plataforma corporativa de operações e governança de TI.</div>
    <nav>
        <ul>
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <!-- Menu Admin -->
                <li><a href="admin_dashboard.php" class="<?= ($currentPage ?? '') === 'admin_dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a></li>
                <li><a href="admin_funcionarios.php" class="<?= ($currentPage ?? '') === 'admin_funcionarios' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> Funcionários
                </a></li>
                <li><a href="admin_ponto.php" class="<?= ($currentPage ?? '') === 'admin_ponto' ? 'active' : '' ?>">
                    <i class="fas fa-clock"></i> Controle de Ponto
                </a></li>
                <li><a href="admin_pagamentos.php" class="<?= ($currentPage ?? '') === 'admin_pagamentos' ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice-dollar"></i> Pagamentos
                </a></li>
                <li><a href="tickets.php" class="<?= ($currentPage ?? '') === 'tickets' ? 'active' : '' ?>">
                    <i class="fas fa-ticket-alt"></i> Tickets
                </a></li>
                <li><a href="admin_auditoria.php" class="<?= ($currentPage ?? '') === 'admin_auditoria' ? 'active' : '' ?>">
                    <i class="fas fa-shield-alt"></i> Auditoria
                </a></li>
                
            <?php elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'supervisor'): ?>
                <!-- Menu Supervisor -->
                <li><a href="supervisor_dashboard.php" class="<?= ($currentPage ?? '') === 'supervisor_dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-layer-group"></i> Dashboard Supervisor
                </a></li>
                <li><a href="tickets.php" class="<?= ($currentPage ?? '') === 'tickets' ? 'active' : '' ?>">
                    <i class="fas fa-ticket-alt"></i> Tickets
                </a></li>
                <li><a href="meu_ponto.php" class="<?= ($currentPage ?? '') === 'meu_ponto' ? 'active' : '' ?>">
                    <i class="fas fa-clock"></i> Meu Ponto
                </a></li>
                
            <?php else: ?>
                <!-- Menu Funcionário -->
                <li><a href="funcionario_dashboard.php" class="<?= ($currentPage ?? '') === 'funcionario_dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i> Início
                </a></li>
                <li><a href="meu_ponto.php" class="<?= ($currentPage ?? '') === 'meu_ponto' ? 'active' : '' ?>">
                    <i class="fas fa-clock"></i> Meu Ponto
                </a></li>
                <li><a href="meus_tickets.php" class="<?= ($currentPage ?? '') === 'meus_tickets' ? 'active' : '' ?>">
                    <i class="fas fa-ticket-alt"></i> Meus Tickets
                </a></li>
                <li><a href="meus_pagamentos.php" class="<?= ($currentPage ?? '') === 'meus_pagamentos' ? 'active' : '' ?>">
                    <i class="fas fa-wallet"></i> Meus Pagamentos
                </a></li>
            <?php endif; ?>

            <li><a href="meu_perfil.php" class="<?= ($currentPage ?? '') === 'meu_perfil' ? 'active' : '' ?>">
                <i class="fas fa-user-circle"></i> Meu Perfil
            </a></li>
            
            <!-- Logout (comum a todos) -->
            <li class="sidebar-footer">
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </li>
        </ul>
    </nav>
</div>
