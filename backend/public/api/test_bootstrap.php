<?php

// Diagnostic endpoint: verifies bootstrap + config.php load without routing/rewrite.
header("Content-Type: application/json; charset=utf-8");

try {
  require_once __DIR__ . "/../../src/bootstrap.php";
  echo json_encode(["ok" => true, "bootstrap" => "loaded", "time" => gmdate("c")]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(["ok" => false, "error" => $e->getMessage(), "class" => get_class($e)]);
}

