<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso negado']);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM notificacoes WHERE funcionario_id = ? AND lida = 0");
$stmt->execute([$user_id]);
$notificationCount = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT id, mensagem, created_at FROM notificacoes WHERE funcionario_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM servicos WHERE funcionario_id = ? AND status != 'finalizado'");
$stmt->execute([$user_id]);
$openTickets = (int)$stmt->fetchColumn();

header('Content-Type: application/json');
echo json_encode([
    'notification_count' => $notificationCount,
    'open_tickets' => $openTickets,
    'notifications' => array_map(function ($item) {
        return [
            'mensagem' => $item['mensagem'],
            'created_at' => $item['created_at'],
        ];
    }, $notifications),
]);
