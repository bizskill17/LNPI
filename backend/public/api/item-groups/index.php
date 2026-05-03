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
    $rows = $pdo->query("SELECT item_group FROM item_groups ORDER BY item_group ASC")->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode(["rows" => array_map(fn($g) => ["itemGroup" => (string)$g], $rows)]);
    exit;
  }

  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $body = json_decode((string)file_get_contents("php://input"), true);
    $itemGroup = trim((string)($body["itemGroup"] ?? ""));
    if ($itemGroup === "") {
      http_response_code(400);
      echo json_encode(["error" => "itemGroup is required"]);
      exit;
    }
    $stmt = $pdo->prepare("INSERT IGNORE INTO item_groups (item_group) VALUES (:g)");
    $stmt->execute([":g" => $itemGroup]);
    echo json_encode(["ok" => true, "itemGroup" => $itemGroup]);
    exit;
  }

  http_response_code(405);
  echo json_encode(["error" => "Method not allowed"]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(["error" => "Server error", "detail" => $e->getMessage()]);
}

