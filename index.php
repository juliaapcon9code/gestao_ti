<?php
require_once 'includes/functions.php';
if (isLoggedIn()) {
    if ($_SESSION['user_role'] === 'admin') redirect('admin_dashboard.php');
    elseif ($_SESSION['user_role'] === 'supervisor') redirect('supervisor_dashboard.php');
    else redirect('funcionario_dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TechManager</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --bg: #081225;
            --bg-soft: rgba(8, 18, 37, 0.82);
            --panel: rgba(12, 26, 52, 0.92);
            --border: rgba(99, 129, 204, 0.18);
            --text: #e7eef8;
            --muted: #a3b0cc;
            --accent: #4f7dff;
            --accent-soft: rgba(79, 125, 255, 0.16);
            --accent-strong: #dbe3ff;
            --danger: #fb7185;
            --danger-soft: rgba(251, 113, 133, 0.14);
            --shadow: 0 28px 90px rgba(0,0,0,0.38);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: Inter, system-ui, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top, rgba(79,125,255,0.12), transparent 24%),
                radial-gradient(circle at bottom, rgba(86,183,255,0.14), transparent 28%),
                linear-gradient(180deg, #070e1f, #081225 45%, #060d1b);
        }

        .auth-shell {
            width: min(960px, 100%);
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 0;
            border-radius: 30px;
            overflow: hidden;
            border: 1px solid rgba(148,163,184,0.18);
            box-shadow: var(--shadow);
            backdrop-filter: blur(8px);
            background: rgba(8, 15, 32, 0.78);
        }

        .auth-visual {
            padding: 30px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background:
                radial-gradient(circle at top left, rgba(79,125,255,0.18), transparent 24%),
                linear-gradient(180deg, rgba(12,26,52,0.95), rgba(12,26,52,0.72));
            border-right: 1px solid rgba(99,129,204,0.16);
        }

        .brand-badge {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            background: linear-gradient(180deg, rgba(79,125,255,0.95), rgba(63,91,255,0.7));
            color: #ffffff;
            box-shadow: 0 10px 28px rgba(79,125,255,0.18);
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 18px;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.18em;
            color: var(--accent-strong);
            background: rgba(79,125,255,0.14);
            border: 1px solid rgba(79,125,255,0.2);
        }

        .auth-visual h1 {
            margin: 20px 0 10px;
            font-size: 2.1rem;
            line-height: 1.1;
        }

        .auth-visual p {
            margin: 0;
            color: var(--muted);
            line-height: 1.5;
            max-width: 520px;
        }

        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 22px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 999px;
            padding: 10px 12px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.06);
            color: #d0f9e4;
            font-size: 0.84rem;
        }

        .feature-item .icon {
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(79,125,255,0.18);
            color: var(--accent-strong);
            font-size: 0.6rem;
        }

        .login-pane {
            padding: 30px;
            display: flex;
            align-items: center;
        }

        .login-card {
            width: 100%;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(148,163,184,0.16);
            border-radius: 26px;
            padding: 24px;
        }

        .login-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
        }

        .login-header h2 {
            margin: 0;
            font-size: 1.45rem;
        }

        .login-header p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 0.9rem;
        }

        .login-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 6px 9px;
            background: rgba(79,125,255,0.14);
            color: var(--accent-strong);
            font-size: 0.62rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
        }

        .login-card form {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .field-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .field-group label {
            font-size: 0.78rem;
            font-weight: 700;
            color: #d6e1f2;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .field-group input {
            border-radius: 14px;
            border: 1px solid rgba(148,163,184,0.24);
            background: rgba(3, 7, 18, 0.72);
            color: var(--text);
            padding: 14px 15px;
            font-size: 0.95rem;
            outline: none;
        }

        .field-group input::placeholder { color: #62748a; }
        .field-group input:focus {
            border-color: rgba(79,125,255,0.72);
            box-shadow: 0 0 0 4px rgba(79,125,255,0.14);
        }

        .submit-button {
            border: 0;
            border-radius: 14px;
            padding: 15px 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 800;
            color: #ffffff;
            background: linear-gradient(180deg, #5c80ff, #3f5bff);
            cursor: pointer;
            box-shadow: 0 14px 36px rgba(79,125,255,0.24);
        }

        .error-banner {
            border-radius: 14px;
            padding: 12px 13px;
            border: 1px solid rgba(251,113,133,0.34);
            background: var(--danger-soft);
            color: #fecdd3;
            font-size: 0.84rem;
            text-align: center;
        }

        .helper-box {
            margin-top: 16px;
            border-radius: 14px;
            padding: 12px 13px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(148,163,184,0.14);
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            font-size: 0.72rem;
        }

        .helper-box strong { color: #fff; }

        @media (max-width: 760px) {
            .auth-shell { grid-template-columns: 1fr; }
            .auth-visual { border-right: 0; border-bottom: 1px solid rgba(148,163,184,0.16); }
        }
    </style>
</head>
<body>
    <div class="auth-shell">
        <section class="auth-visual">
            <div>
                <div class="brand-badge"><i class="fas fa-microchip"></i></div>
                <div class="eyebrow"><i class="fas fa-bolt"></i> Plataforma de TI</div>
                <h1>Gestão inteligente para equipes de suporte.</h1>
                <p>Centralize tickets, acompanhe ponto, monitore desempenho e mantenha o fluxo do time organizado em uma interface moderna.</p>

                <div class="feature-list">
                    <div class="feature-item"><span class="icon"><i class="fas fa-ticket"></i></span> Kanban de chamados e SLA visual</div>
                    <div class="feature-item"><span class="icon"><i class="fas fa-clock"></i></span> Registro de ponto com calendário mensal</div>
                    <div class="feature-item"><span class="icon"><i class="fas fa-users-gear"></i></span> Dashboards para admin, supervisor e equipe</div>
                </div>
            </div>

            <div class="helper-box">
                <div>
                    <strong>Credenciais de demonstração</strong><br>
                    Acesse com o usuário admin ou supervisor para explorar o sistema.
                </div>
                <i class="fas fa-arrow-right"></i>
            </div>
        </section>

        <section class="login-pane">
            <div class="login-card">
                <div class="login-header">
                    <div>
                        <h2>Entrar no sistema</h2>
                        <p>Use e-mail e senha para continuar.</p>
                    </div>
                    <span class="login-status"><i class="fas fa-lock"></i> Seguro</span>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="error-banner">
                        <?= htmlspecialchars($_GET['error']) ?>
                    </div>
                <?php endif; ?>

                <form action="login_process.php" method="POST">
                    <div class="field-group">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" required placeholder="admin@gmail.com">
                    </div>

                    <div class="field-group">
                        <label for="senha">Senha</label>
                        <input type="password" id="senha" name="senha" required placeholder="••••••••">
                    </div>

                    <button type="submit" class="submit-button">
                        <i class="fas fa-right-to-bracket"></i> Entrar no Sistema
                    </button>
                </form>

                <div class="helper-box" style="margin-top: 12px;">
                    <div>
                        <strong>Admin:</strong> admin@gmail.com / adm123<br>
                        <strong>Supervisor:</strong> supervisor@gmail.com / supervisor123
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
