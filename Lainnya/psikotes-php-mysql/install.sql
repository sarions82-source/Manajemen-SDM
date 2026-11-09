-- Opsional: impor lewat phpMyAdmin bila tabel ingin dibuat manual.
-- Jika tidak diimpor, api.php membuat tabel ini otomatis saat pertama kali dipakai.
CREATE TABLE IF NOT EXISTS psi_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(30) NOT NULL UNIQUE,
  name VARCHAR(60) NOT NULL,
  role ENUM('super','admin','employee') NOT NULL,
  pass_hash VARCHAR(255) NOT NULL,
  active TINYINT(1) NOT NULL DEFAULT 0,
  pending TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS psi_sessions (
  token CHAR(48) NOT NULL PRIMARY KEY,
  username VARCHAR(30) NOT NULL,
  expires INT NOT NULL,
  INDEX (expires)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS psi_results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  test VARCHAR(12) NOT NULL,
  name VARCHAR(100) NOT NULL,
  age VARCHAR(3) NOT NULL DEFAULT '',
  sex VARCHAR(20) NOT NULL DEFAULT '',
  test_date VARCHAR(10) NOT NULL DEFAULT '',
  minutes DOUBLE NULL,
  auto_end TINYINT(1) NOT NULL DEFAULT 0,
  payload LONGTEXT NOT NULL,
  archived TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  INDEX (archived), INDEX (test), INDEX (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS psi_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  created_at DATETIME NOT NULL,
  who VARCHAR(100) NOT NULL,
  msg VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS psi_settings (
  k VARCHAR(40) NOT NULL PRIMARY KEY,
  v MEDIUMTEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
