<?php
session_start();
require __DIR__ . '/db.php';

if (!isset($_SESSION['uid'])) {
  http_response_code(401);
  exit('Oturum açılmamış.');
}

$user_id = $_SESSION['uid'];
$traits  = [
  'openness',
  'agreeableness',
  'extraversion',
  'stability',
  'conscientiousness'
];

try {
  $pdo->beginTransaction();
  $stmt = $pdo->prepare(
    "INSERT INTO personality (user_id, trait, score)
     VALUES (?, ?, ?)
     ON DUPLICATE KEY UPDATE score = VALUES(score)"
  );

  foreach ($traits as $trait) {
    $score = isset($_POST[$trait]) ? (int)$_POST[$trait] : 0;
    if ($score < 1 || $score > 5) {
      throw new Exception("Geçersiz skor: $trait");
    }
    $stmt->execute([$user_id, $trait, $score]);
  }
  $pdo->commit();
  echo 'ok';
} catch (Exception $e) {
  $pdo->rollBack();
  http_response_code(422);
  exit($e->getMessage());
}
