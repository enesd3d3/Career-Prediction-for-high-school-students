<?php
session_start();
require __DIR__.'/db.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['uid'])) {
  http_response_code(401);
  exit('Oturum açılmamış.');
}
$user_id = $_SESSION['uid'];

// 1) Ortalama not
$stmt = $pdo->prepare("SELECT score FROM grades WHERE user_id = ?");
$stmt->execute([$user_id]);
$scores = $stmt->fetchAll(PDO::FETCH_COLUMN);
$avg = $scores ? array_sum($scores)/count($scores) : 0;

// 2) Trait skorları normalize
$stmt = $pdo->prepare("SELECT trait, score FROM personality WHERE user_id = ?");
$stmt->execute([$user_id]);
$traits = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
foreach ($traits as $k => $v) {
  $traits[$k] = $v/5;  // 0–1 arası
}

// 3) İlgi alanları temiz
$stmt = $pdo->prepare("SELECT interest FROM interests WHERE user_id = ?");
$stmt->execute([$user_id]);
$interests = array_map('trim', $stmt->fetchAll(PDO::FETCH_COLUMN));

// 4) Portfolyo – örnek profil listesi
$profiles = [
  ['name'=>'Data Scientist', 'interests'=>['Teknoloji','Matematik'], 'traits'=>['openness','conscientiousness'], 'grade_req'=>80],
  ['name'=>'Software Developer', 'interests'=>['Teknoloji'], 'traits'=>['conscientiousness','stability'], 'grade_req'=>60],
  ['name'=>'UX/UI Designer', 'interests'=>['Tasarım'], 'traits'=>['openness','agreeableness'], 'grade_req'=>50],
  ['name'=>'Project Manager', 'interests'=>['Seyahat'], 'traits'=>['extraversion','agreeableness'], 'grade_req'=>50],
  ['name'=>'Cybersecurity Analyst', 'interests'=>['Teknoloji'], 'traits'=>['stability','openness'], 'grade_req'=>70],
  ['name'=>'HR Specialist', 'interests'=>['Kitap Okuma'], 'traits'=>['agreeableness'], 'grade_req'=>50],
];

// Ağırlıklar
$w = ['grade'=>0.4, 'trait'=>0.3, 'interest'=>0.3];

// 5) Puan hesapla
$results = [];
foreach ($profiles as $p) {
  // grade puanı (normalize)
  $g = min(1, $avg / max(1, $p['grade_req']));
  // trait puanı (eşleşen trait ortalaması)
  $ts = 0; $tc = 0;
  foreach ($p['traits'] as $t) if(isset($traits[$t])) { $ts += $traits[$t]; $tc++; }
  $t = $tc ? $ts/$tc : 0;
  // interest puanı (eşleşen etiket oranı)
  $ic = count(array_intersect($p['interests'], $interests));
  $i = count($p['interests']) ? $ic/count($p['interests']) : 0;
  // toplam puan
  $score = $w['grade']*$g + $w['trait']*$t + $w['interest']*$i;
  $results[] = ['name'=>$p['name'], 'score'=>$score];
}

// 6) Sırala ve top3 al
usort($results, fn($a,$b)=>$b['score']<=>$a['score']);
$top = array_slice($results,0,3);

// 7) JSON çıktı
$output = [
  'debug_avg'      => round($avg,2),
  'debug_traits'   => $traits,
  'debug_interests'=> $interests,
];
foreach ($top as $idx=>$p) {
  $output["prof".($idx+1)] = $p['name'];
  $output["conf".($idx+1)] = round($p['score'],2);
}

echo json_encode($output);
