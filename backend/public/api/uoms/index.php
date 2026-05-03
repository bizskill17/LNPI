<?php

declare(strict_types=1);

require_once __DIR__ . "/../../src/bootstrap.php";

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
  http_response_code(204);
  exit;
}

$pdo = lnpi_db();

try {
  if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $rows = $pdo->query("SELECT uom FROM uoms ORDER BY uom ASC")->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode(["rows" => array_map(fn($u) => ["uom" => (string)$u], $rows)]);
    exit;
  }

  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $body = json_decode((string)file_get_contents("php://input"), true);
    $uom = trim((string)($body["uom"] ?? ""));
    if ($uom === "") {
      http_response_code(400);
      echo json_encode(["error" => "uom is required"]);
      exit;
    }
    $stmt = $pdo->prepare("INSERT IGNORE INTO uoms (uom) VALUES (:u)");
    $stmt->execute([":u" => $uom]);
    echo json_encode(["ok" => true, "uom" => $uom]);
    exit;
  }

  http_response_code(405);
  echo json_encode(["error" => "Method not allowed"]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(["error" => "Server error", "detail" => $e->getMessage()]);
}

