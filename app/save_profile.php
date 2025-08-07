<?php
session_start();
require __DIR__ . '/db.php';

if (!isset($_SESSION['uid'])) {
  http_response_code(401);
  exit('Oturum açılmamış.');
}

$user_id   = $_SESSION['uid'];
$full_name = trim($_POST['full_name'] ?? '');
$field     = $_POST['field'] ?? '';

if (!$full_name || !in_array($field, ['numerical','equal_weight','no_field','unhappy'])) {
  http_response_code(422);
  exit('Ad Soyad ve Alan zorunlu.');
}

// Eğer kayıt yoksa ekle; varsa güncelle
$stmt = $pdo->prepare(
  "INSERT INTO student_profiles (user_id, full_name, field)
   VALUES (?, ?, ?)
   ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), field = VALUES(field)"
);
try {
  $stmt->execute([$user_id, $full_name, $field]);
  echo 'ok';
} catch (PDOException $e) {
  http_response_code(500);
  exit('Veritabanı hatası.');
}
