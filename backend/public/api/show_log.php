<?php

// Temporary diagnostic endpoint: shows last lines of api_error.log.
// Protect with a token in config.php:
//   return [ ..., "debugToken" => "CHANGE_ME_LONG_RANDOM", ... ];
//
// Then call: /api/show_log.php?token=...&lines=200

header("Content-Type: text/plain; charset=utf-8");

function lnpi_read_config(): array {
  $configPath = __DIR__ . "/../../config.php";
  if (!file_exists($configPath)) return [];
  $cfg = require $configPath;
  return is_array($cfg) ? $cfg : [];
}

$cfg = lnpi_read_config();
$token = (string)($_GET["token"] ?? "");
$expected = (string)($cfg["debugToken"] ?? "");

if ($expected === "" || $token === "" || !hash_equals($expected, $token)) {
  http_response_code(403);
  echo "Forbidden\n";
  exit;
}

$lines = (int)($_GET["lines"] ?? 200);
$lines = max(10, min(1000, $lines));

$logPath = __DIR__ . "/../api_error.log";
if (!file_exists($logPath)) {
  echo "Log file not found: $logPath\n";
  exit;
}

$data = @file($logPath, FILE_IGNORE_NEW_LINES);
if ($data === false) {
  http_response_code(500);
  echo "Could not read log file.\n";
  exit;
}

$slice = array_slice($data, -$lines);
echo implode("\n", $slice) . "\n";

