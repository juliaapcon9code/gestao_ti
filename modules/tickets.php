<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    die("Acesso negado");
}

$action = $_GET['action'] ?? 'list';

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $prioridade = $_POST['prioridade'];
    $setor = $_POST['setor'];
    $funcionario_id = $_POST['funcionario_id'] ?? null;

    $stmt = $pdo->prepare("INSERT INTO servicos (titulo, descricao, prioridade, setor, funcionario_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$titulo, $descricao, $prioridade, $setor, $funcionario_id]);
    
    if ($funcionario_id) {
        createNotification($pdo, $funcionario_id, "Um novo ticket foi atribuído a você: $titulo");
    }

    logAction($pdo, 'Criar Ticket', "Ticket criado: $titulo");
    redirect('../tickets.php?success=Ticket criado');
}

if ($action === 'update_status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE servicos SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    $ownerStmt = $pdo->prepare("SELECT funcionario_id FROM servicos WHERE id = ?");
    $ownerStmt->execute([$id]);
    $responsavel = $ownerStmt->fetchColumn();

    if ($responsavel) {
        createNotification($pdo, $responsavel, "O ticket #$id mudou para $status");
    }
    
    logAction($pdo, 'Atualizar Status Ticket', "Ticket ID $id alterado para $status");
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'comment' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = $_POST['ticket_id'];
    $comentario = $_POST['comentario'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO tickets_comentarios (ticket_id, funcionario_id, comentario) VALUES (?, ?, ?)");
    $stmt->execute([$ticket_id, $user_id, $comentario]);
    
    redirect("../ticket_view.php?id=$ticket_id&success=Comentário adicionado");
}
?>
