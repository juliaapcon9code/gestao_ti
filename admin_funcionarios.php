<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (!isAdmin()) redirect('index.php');

$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM funcionarios WHERE role != 'admin'";
if ($search) {
    $sql .= " AND (nome LIKE :search OR email LIKE :search)";
}
$stmt = $pdo->prepare($sql);
if ($search) {
    $stmt->execute(['search' => "%$search%"]);
} else {
    $stmt->execute();
}
$funcionarios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Funcionários - TechManager</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'resources/views/partials/sidebar.php'; ?>

    <main class="main-content">
        <?php
        $title = 'Gestão de Funcionários';
        $subtitle = 'Cadastre, pesquise e acompanhe perfis e status da equipe em um painel mais organizado.';
        $icon = 'fas fa-users';
        $pills = [count($funcionarios) . ' registros'];
        $actionButton = [
            'text' => 'Novo Funcionário',
            'href' => '#',
            'onclick' => "openModal('modalCreate')",
            'icon' => 'fas fa-plus',
            'id' => 'newEmployeeBtn'
        ];
        include 'resources/views/partials/header.php';
        ?>

        <div class="card">
            <div style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                <form method="GET" style="flex: 1; display: flex; gap: 10px; min-width: 280px;">
                    <input type="text" name="search" placeholder="Pesquisar por nome ou email..." value="<?= htmlspecialchars($search) ?>" style="flex: 1; padding: 10px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: white;">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </form>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Cargo</th>
                            <th>Setor</th>
                            <th>Salário Base</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                <tbody>
                    <?php foreach ($funcionarios as $f): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($f['nome']) ?></strong><br>
                            <span style="font-size: 12px; opacity: 0.6;"><?= htmlspecialchars($f['email']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($f['cargo']) ?></td>
                        <td><?= htmlspecialchars($f['setor']) ?></td>
                        <td><?= formatMoney($f['salario_base']) ?></td>
                        <td>
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; background: <?= $f['status'] === 'ativo' ? 'rgba(79,125,255,0.12)' : 'rgba(239, 68, 68, 0.1)' ?>; color: <?= $f['status'] === 'ativo' ? 'var(--accent-color)' : 'var(--danger-color)' ?>;">
                                <?= ucfirst($f['status']) ?>
                            </span>
                            <div style="font-size: 11px; opacity: 0.6; margin-top: 6px; text-transform: capitalize;"><?= htmlspecialchars($f['role']) ?></div>
                        </td>
                        <td>
                            <a href="#" class="btn" style="padding: 5px; color: var(--accent-color);"><i class="fas fa-edit"></i></a>
                            <a href="modules/funcionarios.php?action=delete&id=<?= $f['id'] ?>" class="btn" style="padding: 5px; color: var(--danger-color);" onclick="return confirm('Tem certeza?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal Create (Simplificado) -->
    <div id="modalCreate" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;">
        <div class="card" style="width: 500px;">
            <h3>Novo Funcionário</h3>
            <form action="modules/funcionarios.php?action=create" method="POST" style="margin-top: 20px;">
                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="nome" required>
                </div>
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" name="senha" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Cargo</label>
                        <input type="text" name="cargo">
                    </div>
                    <div class="form-group">
                        <label>Setor</label>
                        <select name="setor">
                            <option value="Suporte">Suporte</option>
                            <option value="Desenvolvimento">Desenvolvimento</option>
                            <option value="Infraestrutura">Infraestrutura</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Salário Base</label>
                    <input type="number" step="0.01" name="salario_base" value="2000.00">
                </div>
                <div class="form-group">
                    <label>Perfil</label>
                    <select name="role">
                        <option value="funcionario">Funcionário</option>
                        <option value="supervisor">Supervisor</option>
                    </select>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                    <button type="button" class="btn" onclick="closeModal('modalCreate')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).style.display = 'flex'; }
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    </script>
</body>
</html>
