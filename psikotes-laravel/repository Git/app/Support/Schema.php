<?php

namespace App\Support;

class Schema
{
    const SQL = <<<'SQL'
CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  username TEXT NOT NULL UNIQUE,
  name TEXT NOT NULL,
  role TEXT NOT NULL CHECK (role IN ('super','admin','employee')),
  pass_hash TEXT NOT NULL,
  active INTEGER NOT NULL DEFAULT 0,
  pending INTEGER NOT NULL DEFAULT 0,
  created_at TEXT NOT NULL
);
CREATE TABLE IF NOT EXISTS sessions (
  token TEXT PRIMARY KEY,
  username TEXT NOT NULL,
  expires INTEGER NOT NULL
);
CREATE TABLE IF NOT EXISTS results (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  test TEXT NOT NULL,
  name TEXT NOT NULL,
  age TEXT NOT NULL DEFAULT '',
  sex TEXT NOT NULL DEFAULT '',
  test_date TEXT NOT NULL DEFAULT '',
  minutes REAL,
  auto_end INTEGER NOT NULL DEFAULT 0,
  payload TEXT NOT NULL,
  archived INTEGER NOT NULL DEFAULT 0,
  created_at TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_results_archived ON results (archived);
CREATE INDEX IF NOT EXISTS idx_results_name ON results (name);
CREATE TABLE IF NOT EXISTS logs (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  created_at TEXT NOT NULL,
  who TEXT NOT NULL,
  msg TEXT NOT NULL
);
CREATE TABLE IF NOT EXISTS settings (
  k TEXT PRIMARY KEY,
  v TEXT NOT NULL
);
SQL;
}
