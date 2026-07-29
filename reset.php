<?php
require_once 'config/db.php';

$adminPasswordHash = password_hash('adm123', PASSWORD_DEFAULT);
$supervisorPasswordHash = password_hash('supervisor123', PASSWORD_DEFAULT);

$messages = [];

try {
    $adminEmail = 'admin@gmail.com';
    $adminStmt = $pdo->prepare("UPDATE funcionarios SET senha = ?, status = 'ativo', role = 'admin' WHERE email = ?");
    $adminStmt->execute([$adminPasswordHash, $adminEmail]);

    if ($adminStmt->rowCount() > 0) {
        $messages[] = [
            'icon' => 'fas fa-shield-halved',
            'title' => 'Admin atualizado',
            'tone' => 'accent',
            'text' => 'O usuário <strong>admin@gmail.com</strong> foi atualizado com a senha <strong>adm123</strong>.'
        ];
    } else {
        $adminInsert = $pdo->prepare("INSERT INTO funcionarios (nome, email, senha, cargo, setor, salario_base, status, role)
                                      VALUES ('Administrador', ?, ?, 'Diretor', 'TI', 5000.00, 'ativo', 'admin')");
        $adminInsert->execute([$adminEmail, $adminPasswordHash]);
        $messages[] = [
            'icon' => 'fas fa-user-shield',
            'title' => 'Admin criado',
            'tone' => 'accent',
            'text' => 'O usuário <strong>admin@gmail.com</strong> foi criado com a senha <strong>adm123</strong>.'
        ];
    }

    $supervisorEmail = 'supervisor@gmail.com';
    $supervisorStmt = $pdo->prepare("UPDATE funcionarios SET senha = ?, cargo = 'Supervisor de TI', setor = 'TI', salario_base = 4000.00, status = 'ativo', role = 'supervisor' WHERE email = ?");
    $supervisorStmt->execute([$supervisorPasswordHash, $supervisorEmail]);

    if ($supervisorStmt->rowCount() > 0) {
        $messages[] = [
            'icon' => 'fas fa-users-gear',
            'title' => 'Supervisor atualizado',
            'tone' => 'success',
            'text' => 'O usuário <strong>supervisor@gmail.com</strong> foi atualizado com a senha <strong>supervisor123</strong>.'
        ];
    } else {
        $supervisorInsert = $pdo->prepare("INSERT INTO funcionarios (nome, email, senha, cargo, setor, salario_base, status, role)
                                           VALUES ('Supervisor TI', ?, ?, 'Supervisor de TI', 'TI', 4000.00, 'ativo', 'supervisor')");
        $supervisorInsert->execute([$supervisorEmail, $supervisorPasswordHash]);
        $messages[] = [
            'icon' => 'fas fa-user-tie',
            'title' => 'Supervisor criado',
            'tone' => 'success',
            'text' => 'O usuário <strong>supervisor@gmail.com</strong> foi criado com a senha <strong>supervisor123</strong>.'
        ];
    }
} catch (Exception $e) {
    $messages[] = [
        'icon' => 'fas fa-triangle-exclamation',
        'title' => 'Erro ao resetar',
        'tone' => 'danger',
        'text' => htmlspecialchars($e->getMessage())
    ];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset de Acesso - TechManager</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --bg: #020617;
            --bg-soft: rgba(15, 23, 42, 0.72);
            --panel: rgba(8, 15, 32, 0.88);
            --border: rgba(148, 163, 184, 0.18);
            --text: #f8fafc;
            --muted: #94a3b8;
            --accent: #4f7dff;
            --accent-soft: rgba(79, 125, 255, 0.16);
            --admin: #60a5fa;
            --admin-soft: rgba(96, 165, 250, 0.16);
            --danger: #fb7185;
            --danger-soft: rgba(251, 113, 133, 0.16);
            --shadow: 0 24px 80px rgba(0,0,0,0.35);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: Inter, system-ui, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top, rgba(79,125,255,0.12), transparent 24%),
                radial-gradient(circle at bottom, rgba(96,165,250,0.16), transparent 28%),
                linear-gradient(180deg, #070e1f, #081225 38%, #060d1b);
        }

        .reset-card {
            width: min(860px, 100%);
            background: var(--panel);
            border: 1px solid var(--border);
            backdrop-filter: blur(8px);
            border-radius: 26px;
            box-shadow: var(--shadow);
            padding: 28px;
        }

        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
            padding-bottom: 18px;
            border-bottom: 1px solid rgba(148,163,184,0.14);
        }

        .hero h1 {
            margin: 0;
            font-size: 1.8rem;
        }

        .subtitle {
            margin: 10px 0 0;
            color: var(--muted);
            line-height: 1.4;
        }

        .hero-badge {
            min-width: 78px;
            height: 78px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            background: linear-gradient(180deg, rgba(79,125,255,0.28), rgba(63,91,255,0.36));
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08);
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
            margin-bottom: 18px;
        }

        .status-item {
            border-radius: 20px;
            padding: 16px 16px 14px;
            border: 1px solid rgba(148,163,184,0.18);
            background: rgba(255,255,255,0.04);
        }

        .status-item.topline {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .status-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.95rem;
        }

        .status-item.accent .status-icon { background: var(--accent-soft); color: var(--accent-strong); }
        .status-item.success .status-icon { background: rgba(79,125,255,0.18); color: var(--accent-strong); }
        .status-item.danger .status-icon { background: var(--danger-soft); color: #fd9dad; }

        .status-item h2 {
            margin: 0;
            font-size: 1rem;
        }

        .status-item p {
            margin: 8px 0 0;
            color: var(--muted);
            line-height: 1.4;
            font-size: 0.93rem;
        }

        .meta-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 10px;
        }

        .meta-chip {
            border-radius: 999px;
            padding: 8px 10px;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            background: rgba(255,255,255,0.06);
            color: var(--muted);
            text-align: center;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 18px;
            border-radius: 999px;
            padding: 12px 15px;
            text-decoration: none;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(180deg, rgba(14,116,144,0.95), rgba(14,116,144,0.72));
            box-shadow: 0 8px 20px rgba(14,116,144,0.24);
        }

        @media (max-width: 700px) {
            .status-grid, .meta-row { grid-template-columns: 1fr; }
            .hero { align-items: flex-start; }
        }
    </style>
</head>
<body>
    <div class="reset-card">
        <section class="hero">
            <div>
                <div class="hero-badge"><i class="fas fa-screwdriver-wrench"></i></div>
                <h1>Reset de acesso</h1>
                <p class="subtitle">Credenciais prontas para uso com destaque visual e navegação rápida.</p>
            </div>
            <div class="meta-chip">Sistema TechManager</div>
        </section>

        <section class="status-grid">
            <?php foreach ($messages as $message): ?>
                <article class="status-item <?= $message['tone'] ?> topline">
                    <div class="status-icon"><i class="<?= htmlspecialchars($message['icon']) ?>"></i></div>
                    <div>
                        <h2><?= htmlspecialchars($message['title']) ?></h2>
                        <p><?= $message['text'] ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>

        <div class="meta-row">
            <div class="meta-chip"><i class="fas fa-key"></i> admin@gmail.com • adm123</div>
            <div class="meta-chip"><i class="fas fa-user-tie"></i> supervisor@gmail.com • supervisor123</div>
        </div>

        <a class="back-link" href="index.php"><i class="fas fa-arrow-right-to-bracket"></i> Ir para a tela de Login</a>
    </div>
</body>
</html>
