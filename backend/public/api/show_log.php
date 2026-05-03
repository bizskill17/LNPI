<?php

// Temporary diagnostic endpoint: shows last lines of api_error.log.
// Protect with a token in config.php:
//   return [ ..., "debugToken" => "CHANGE_ME_LONG_RANDOM", ... ];
//
// Then call: /api/show_log.php?token=...&lines=200

header("Content-Type: text/plain; charset=utf-8");

$diagLog = __DIR__ . "/api_error.log";

try {
  // config.php lives in `public_html/config.php`
  $configPath = __DIR__ . "/../config.php";
  if (!file_exists($configPath)) {
    http_response_code(500);
    echo "Missing config.php at $configPath\n";
    exit;
  }

  $cfg = include $configPath;
  if (!is_array($cfg)) {
    http_response_code(500);
    echo "config.php did not return an array\n";
    exit;
  }

  $token = (string)($_GET["token"] ?? "");
  $expected = (string)($cfg["debugToken"] ?? "");

  if ($expected === "" || $token === "" || !hash_equals($expected, $token)) {
    http_response_code(403);
    echo "Forbidden\n";
    exit;
  }

  $lines = (int)($_GET["lines"] ?? 200);
  $lines = max(10, min(1000, $lines));

  $logPath = __DIR__ . "/api_error.log";
  if (!file_exists($logPath)) {
    // Try to create so we can confirm write permissions.
    @touch($logPath);
  }
  if (!file_exists($logPath)) {
    echo "Log file not found: $logPath\n";
    echo "api dir writable: " . (is_writable(__DIR__) ? "yes" : "no") . "\n";
    exit;
  }
  if (!is_readable($logPath)) {
    http_response_code(500);
    echo "Log file exists but is not readable: $logPath\n";
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
} catch (Throwable $e) {
  @file_put_contents(
    $diagLog,
    "[" . gmdate("c") . "] show_log.php " . get_class($e) . ": " . $e->getMessage() . " @ " . $e->getFile() . ":" . $e->getLine() . "\n",
    FILE_APPEND
  );
  http_response_code(500);
  echo "show_log.php failed: " . $e->getMessage() . "\n";
}
