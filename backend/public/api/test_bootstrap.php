<?php

// Diagnostic endpoint: verifies bootstrap + config.php load without routing/rewrite.
header("Content-Type: application/json; charset=utf-8");

try {
  $path = __DIR__ . "/../../src/bootstrap.php";
  $info = [
    "bootstrapPath" => $path,
    "realpath" => realpath($path) ?: null,
    "exists" => file_exists($path),
    "readable" => is_readable($path),
    "open_basedir" => ini_get("open_basedir") ?: null,
  ];
  require_once $path;
  echo json_encode(["ok" => true, "bootstrap" => "loaded", "info" => $info, "time" => gmdate("c")]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode([
    "ok" => false,
    "error" => $e->getMessage(),
    "class" => get_class($e),
    "open_basedir" => ini_get("open_basedir") ?: null,
  ]);
}
