<?php
session_start();
require __DIR__ . '/db.php';

if (!isset($_SESSION['uid'])) {
  http_response_code(401);
  exit('Oturum açılmamış.');
}

$user_id = $_SESSION['uid'];
$tag     = trim($_POST['interest'] ?? '');

if ($tag === '') {
  http_response_code(422);
  exit('Boş ilgi alanı girilemez.');
}

// Ekle (varsa atla)
$stmt = $pdo->prepare(
  "INSERT IGNORE INTO interests (user_id, interest) VALUES (?, ?)"
);

try {
  $stmt->execute([$user_id, $tag]);
  echo 'ok';
} catch (PDOException $e) {
  http_response_code(500);
  exit('Veritabanı hatası.');
}
