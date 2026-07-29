<?php
/**
 * TechManager - Core Functions
 * 
 * Funções utilitárias essenciais do sistema.
 * Importa middlewares de segurança.
 */

// Iniciar sessão
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Importar classes de segurança
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/validator.php';
require_once __DIR__ . '/helpers.php';

/**
 * Verifica se usuário está autenticado
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function isSupervisor() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'supervisor';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function logAction($pdo, $action, $details = null) {
    $userId = $_SESSION['user_id'] ?? null;
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $pdo->prepare("INSERT INTO logs_sistema (funcionario_id, acao, detalhes, ip) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $action, $details, $ip]);
}

function createNotification($pdo, $userId, $message) {
    $stmt = $pdo->prepare("INSERT INTO notificacoes (funcionario_id, mensagem) VALUES (?, ?)");
    $stmt->execute([$userId, $message]);
}
?>
