<?php
// 1) Veritabanı bağlantısını yükle
require __DIR__ . '/db.php';

// 2) POST’dan verileri al & validasyon
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$pw    = $_POST['password'] ?? '';

if (!$email || strlen($pw) < 8) {
  http_response_code(422);
  exit('Geçersiz e-posta veya şifre çok kısa (min 8 karakter).');
}

// 3) Şifreyi hash’le
$hash = password_hash($pw, PASSWORD_BCRYPT);

// 4) Kayıt SQL’i
try {
  $stmt = $pdo->prepare("INSERT INTO users (email, pass_hash) VALUES (?, ?)");
  $stmt->execute([$email, $hash]);
  echo 'ok';           // JS tarafında “res.ok” için döndürüyoruz
} catch (PDOException $e) {
  // Örn. UNIQUE ihlali: aynı e-posta daha önce kayıtlı
  http_response_code(409);
  exit('Bu e-posta zaten kullanılıyor.');
}
