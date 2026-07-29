<?php
require_once 'config/db.php';
$nova_senha = password_hash('adm123', PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE funcionarios SET senha = ? WHERE email = 'admin@gmail.com'");
if($stmt->execute([$nova_senha])) {
    echo "Senha do admin atualizada com sucesso! Agora você pode logar.";
} else {
    echo "Erro ao atualizar.";
}
?>
