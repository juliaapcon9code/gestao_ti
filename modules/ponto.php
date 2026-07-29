<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    die("Acesso negado");
}

$funcionario_id = $_SESSION['user_id'];
$hoje = date('Y-m-d');
$agora = date('H:i:s');
$tipo = $_POST['tipo'] ?? ''; // entrada, pausa, retorno, saida

// Verificar se já existe registro para hoje
$stmt = $pdo->prepare("SELECT * FROM ponto WHERE funcionario_id = ? AND data = ?");
$stmt->execute([$funcionario_id, $hoje]);
$ponto = $stmt->fetch();

if (!$ponto) {
    if ($tipo === 'entrada') {
        $status = (strtotime($agora) > strtotime('09:15:00')) ? 'atraso' : 'presenca';
        $stmt = $pdo->prepare("INSERT INTO ponto (funcionario_id, data, entrada, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$funcionario_id, $hoje, $agora, $status]);
    }
} else {
    if ($tipo === 'pausa' && empty($ponto['saida_pausa'])) {
        $stmt = $pdo->prepare("UPDATE ponto SET saida_pausa = ? WHERE id = ?");
        $stmt->execute([$agora, $ponto['id']]);
    } elseif ($tipo === 'retorno' && empty($ponto['retorno_pausa'])) {
        $stmt = $pdo->prepare("UPDATE ponto SET retorno_pausa = ? WHERE id = ?");
        $stmt->execute([$agora, $ponto['id']]);
    } elseif ($tipo === 'saida' && empty($ponto['saida'])) {
        // Calcular horas trabalhadas
        $entrada = new DateTime($ponto['entrada']);
        $saida = new DateTime($agora);
        $intervalo = $entrada->diff($saida);
        
        // Descontar 1 hora de almoço se houver pausa
        $horas = $intervalo->h + ($intervalo->i / 60);
        if (!empty($ponto['saida_pausa']) && !empty($ponto['retorno_pausa'])) {
            $p1 = new DateTime($ponto['saida_pausa']);
            $p2 = new DateTime($ponto['retorno_pausa']);
            $pausa = $p1->diff($p2);
            $horas_pausa = $pausa->h + ($pausa->i / 60);
            $horas -= $horas_pausa;
        } else {
            $horas -= 1; // Padrão 1h se não registrado
        }

        $horas_normais = 8;
        $extras = ($horas > $horas_normais) ? ($horas - $horas_normais) : 0;
        $status = ($extras > 0) ? 'extra' : $ponto['status'];

        $stmt = $pdo->prepare("UPDATE ponto SET saida = ?, horas_trabalhadas = ?, horas_extras = ?, status = ? WHERE id = ?");
        $stmt->execute([$agora, max(0, $horas), $extras, $status, $ponto['id']]);
    }
}

logAction($pdo, 'Registro de Ponto', "Funcionário registrou $tipo");
redirect('../funcionario_dashboard.php?success=Ponto registrado');
?>
