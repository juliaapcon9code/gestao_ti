<?php
$host = '127.0.0.1';
$dbname = 'gestao_ti';
$username = 'root';
$password = ''; // No sandbox do Manus, geralmente o root não tem senha por padrão ou usamos o que estiver disponível.

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>
