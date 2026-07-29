<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isAdmin()) {
    die("Acesso negado");
}

$action = $_GET['action'] ?? 'list';

if ($action === 'gerar') {
    $mes = $_POST['mes'] ?? date('m');
    $ano = $_POST['ano'] ?? date('Y');

    $funcionarios = $pdo->query("SELECT * FROM funcionarios WHERE status = 'ativo'")->fetchAll();

    foreach ($funcionarios as $f) {
        // Calcular horas extras e faltas do mês
        $stmt = $pdo->prepare("SELECT SUM(horas_extras) as extras, COUNT(*) as dias FROM ponto WHERE funcionario_id = ? AND MONTH(data) = ? AND YEAR(data) = ?");
        $stmt->execute([$f['id'], $mes, $ano]);
        $dados_ponto = $stmt->fetch();

        $salario_base = $f['salario_base'];
        $valor_hora = $salario_base / 160; // 40h semanais
        $valor_extras = ($dados_ponto['extras'] ?? 0) * ($valor_hora * 1.5);
        
        // Simulação de benefícios
        $beneficios = 500.00; // VA + VR
        $descontos = 0;
        
        // Verificar faltas (simplificado: se dias trabalhados < 20 no mês útil)
        if ($dados_ponto['dias'] < 20) {
            $faltas = 20 - $dados_ponto['dias'];
            $descontos = $faltas * ($valor_hora * 8);
        }

        $total = $salario_base + $valor_extras + $beneficios - $descontos;

        // Inserir ou atualizar pagamento
        $stmt = $pdo->prepare("INSERT INTO pagamento (funcionario_id, mes, ano, salario_base, total_horas_extras, valor_extras, descontos, beneficios, total_receber) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                               ON DUPLICATE KEY UPDATE total_receber = VALUES(total_receber)");
        $stmt->execute([$f['id'], $mes, $ano, $salario_base, $dados_ponto['extras'] ?? 0, $valor_extras, $descontos, $beneficios, $total]);
        
        createNotification($pdo, $f['id'], "Seu holerite de $mes/$ano já está disponível.");
    }

    logAction($pdo, 'Gerar Folha', "Admin gerou folha de pagamento para $mes/$ano");
    redirect('../admin_pagamentos.php?success=Folha gerada com sucesso');
}
?>
