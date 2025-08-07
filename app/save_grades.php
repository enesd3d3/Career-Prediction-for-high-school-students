<?php
session_start();
require __DIR__ . '/db.php';

if (!isset($_SESSION['uid'])) {
  http_response_code(401);
  exit('Oturum açılmamış.');
}

$user_id = $_SESSION['uid'];
$course  = trim($_POST['course']  ?? '');
$score   = (int) ($_POST['score'] ?? -1);

if ($course === '' || $score < 0 || $score > 100) {
  http_response_code(422);
  exit('Geçersiz ders adı veya not (0–100).');
}

// Ders + not ekle veya güncelle
$stmt = $pdo->prepare(
  "INSERT INTO grades (user_id, course, score)
   VALUES (?, ?, ?)
   ON DUPLICATE KEY UPDATE score = VALUES(score)"
);

try {
  $stmt->execute([$user_id, $course, $score]);
  echo 'ok';
} catch (PDOException $e) {
  http_response_code(500);
  exit('Veritabanı hatası.');
}
