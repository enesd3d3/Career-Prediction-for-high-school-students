<?php
$pdo = new PDO(
  "mysql:host=localhost;dbname=project2_db;charset=utf8mb4",
  "root", ""                             // XAMPP varsayılan
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
