<?php

declare(strict_types=1);

require_once __DIR__ . "/../../../src/bootstrap.php";

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, PUT, DELETE, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
  http_response_code(204);
  exit;
}

$itemId = trim((string)($_GET["itemId"] ?? ""));
if ($itemId === "") {
  http_response_code(400);
  echo json_encode(["error" => "itemId is required"]);
  exit;
}

$pdo = lnpi_db();

try {
  if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $stmt = $pdo->prepare("SELECT item_id, item_group, item_name, erp FROM items WHERE item_id = :id");
    $stmt->execute([":id" => $itemId]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$r) {
      http_response_code(404);
      echo json_encode(["error" => "Not found"]);
      exit;
    }
    echo json_encode([
      "itemId" => (string)$r["item_id"],
      "itemGroup" => (string)$r["item_group"],
      "itemName" => (string)$r["item_name"],
      "erp" => $r["erp"] !== null ? (string)$r["erp"] : null,
    ]);
    exit;
  }

  if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    $body = json_decode((string)file_get_contents("php://input"), true);
    $itemName = trim((string)($body["itemName"] ?? ""));
    $itemGroup = trim((string)($body["itemGroup"] ?? ""));
    $erp = isset($body["erp"]) ? trim((string)$body["erp"]) : null;
    if ($itemName === "" || $itemGroup === "") {
      http_response_code(400);
      echo json_encode(["error" => "itemName and itemGroup are required"]);
      exit;
    }

    $stmt = $pdo->prepare(
      "UPDATE items SET item_name=:n, item_group=:g, erp=:e WHERE item_id=:id"
    );
    $stmt->execute([":n" => $itemName, ":g" => $itemGroup, ":e" => ($erp === "" ? null : $erp), ":id" => $itemId]);
    echo json_encode(["ok" => true]);
    exit;
  }

  if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $stmt = $pdo->prepare("DELETE FROM items WHERE item_id=:id");
    $stmt->execute([":id" => $itemId]);
    echo json_encode(["ok" => true]);
    exit;
  }

  http_response_code(405);
  echo json_encode(["error" => "Method not allowed"]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(["error" => "Server error", "detail" => $e->getMessage()]);
}

