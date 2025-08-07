<?php
session_start();
require __DIR__ . '/db.php';

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$pw    = $_POST['password'] ?? '';

if (!$email || !$pw) {
  http_response_code(422);
  exit('Geçersiz girdi.');
}

// Kullanıcıyı bul
$stmt = $pdo->prepare("SELECT id, pass_hash FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($pw, $user['pass_hash'])) {
  http_response_code(401);
  exit('E-posta veya şifre yanlış.');
}

// Oturumu başlat
$_SESSION['uid'] = $user['id'];
header('Content-Type: application/json');
echo json_encode(['status'=>'ok']);
