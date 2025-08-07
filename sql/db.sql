CREATE DATABASE IF NOT EXISTS project2_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE project2_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(120) UNIQUE NOT NULL,
  pass_hash CHAR(60) NOT NULL
);

CREATE TABLE IF NOT EXISTS student_profiles (
  user_id INT PRIMARY KEY,
  full_name VARCHAR(120),
  age TINYINT,
  phone VARCHAR(20),
  address VARCHAR(255),
  field ENUM('numerical','equal_weight','no_field','unhappy'),
  FOREIGN KEY(user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS grades (
  user_id INT,
  course VARCHAR(50),
  score TINYINT,
  PRIMARY KEY(user_id, course),
  FOREIGN KEY(user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS personality (
  user_id INT,
  trait ENUM('openness','agreeableness','extraversion','stability','conscientiousness'),
  score TINYINT,
  PRIMARY KEY(user_id, trait),
  FOREIGN KEY(user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS interests (
  user_id INT,
  interest VARCHAR(60),
  PRIMARY KEY(user_id, interest),
  FOREIGN KEY(user_id) REFERENCES users(id)
);
