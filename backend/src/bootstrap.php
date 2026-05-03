<?php

declare(strict_types=1);

function lnpi_config(): array {
  $configPath = __DIR__ . "/../config.php";
  if (!file_exists($configPath)) {
    throw new RuntimeException("Missing config.php (copy config.example.php to config.php).");
  }
  $cfg = require $configPath;
  if (!is_array($cfg)) throw new RuntimeException("Invalid config.php");
  return $cfg;
}

function lnpi_db(): PDO {
  static $pdo = null;
  if ($pdo instanceof PDO) return $pdo;

  $cfg = lnpi_config();
  $db = $cfg["db"] ?? null;
  if (!is_array($db)) throw new RuntimeException("Missing db config.");

  $host = (string)($db["host"] ?? "");
  $name = (string)($db["name"] ?? "");
  $user = (string)($db["user"] ?? "");
  $pass = (string)($db["pass"] ?? "");
  $charset = (string)($db["charset"] ?? "utf8mb4");

  $dsn = "mysql:host=$host;dbname=$name;charset=$charset";
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);
  return $pdo;
}

