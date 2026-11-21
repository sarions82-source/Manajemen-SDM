<?php

namespace App\Support;

class Schema
{
    /** Skema MySQL/MariaDB (utf8mb4). Dijalankan satu per satu. */
    const MYSQL = [
        "CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(60) NOT NULL,
  name VARCHAR(150) NOT NULL,
  role VARCHAR(20) NOT NULL,
  pass_hash VARCHAR(255) NOT NULL,
  active TINYINT NOT NULL DEFAULT 0,
  pending TINYINT NOT NULL DEFAULT 0,
  created_at VARCHAR(40) NOT NULL,
  must_change TINYINT NOT NULL DEFAULT 0,
  tests VARCHAR(100) NOT NULL DEFAULT '',
  UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS sessions (
  token VARCHAR(64) NOT NULL PRIMARY KEY,
  username VARCHAR(60) NOT NULL,
  expires BIGINT NOT NULL,
  KEY idx_sessions_user (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  test VARCHAR(20) NOT NULL,
  name VARCHAR(100) NOT NULL,
  age VARCHAR(10) NOT NULL DEFAULT '',
  sex VARCHAR(20) NOT NULL DEFAULT '',
  test_date VARCHAR(20) NOT NULL DEFAULT '',
  minutes DOUBLE NULL,
  auto_end TINYINT NOT NULL DEFAULT 0,
  payload MEDIUMTEXT NOT NULL,
  archived TINYINT NOT NULL DEFAULT 0,
  created_at VARCHAR(40) NOT NULL,
  KEY idx_results_archived (archived),
  KEY idx_results_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  created_at VARCHAR(40) NOT NULL,
  who VARCHAR(100) NOT NULL,
  msg VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS settings (
  k VARCHAR(64) NOT NULL PRIMARY KEY,
  v MEDIUMTEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS attempts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  k VARCHAR(255) NOT NULL,
  t BIGINT NOT NULL,
  KEY idx_attempts_k (k, t)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS alumni (
  username VARCHAR(100) NOT NULL PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  pass_hash VARCHAR(255) NOT NULL,
  active TINYINT NOT NULL DEFAULT 1,
  created BIGINT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS alumni_sessions (
  token VARCHAR(64) NOT NULL PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  expires BIGINT NOT NULL,
  KEY idx_alumni_sessions_user (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS links (
  slug VARCHAR(60) NOT NULL PRIMARY KEY,
  label VARCHAR(100) NOT NULL,
  tests VARCHAR(100) NOT NULL,
  active TINYINT NOT NULL DEFAULT 1,
  owner VARCHAR(60) NOT NULL,
  created BIGINT NOT NULL,
  KEY idx_links_owner (owner)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS iq_files (
  name VARCHAR(64) NOT NULL PRIMARY KEY,
  mime VARCHAR(40) NOT NULL,
  data LONGTEXT NOT NULL,
  created BIGINT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ];
}
