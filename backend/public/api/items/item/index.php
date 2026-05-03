<?php

declare(strict_types=1);

require_once __DIR__ . "/../../../../src/bootstrap.php";

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, PUT, DELETE, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
  http_response_code(204);
  exit;
}

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) {
  http_response_code(400);
  echo json_encode(["error" => "id is required"]);
  exit;
}

$pdo = lnpi_db();

try {
  if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $stmt = $pdo->prepare(
      "SELECT i.id, i.item_name, i.erp, i.item_group_id, g.item_group
       FROM items i
       JOIN item_groups g ON g.id = i.item_group_id
       WHERE i.id = :id"
    );
    $stmt->execute([":id" => $id]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$r) {
      http_response_code(404);
      echo json_encode(["error" => "Not found"]);
      exit;
    }
    echo json_encode([
      "id" => (int)$r["id"],
      "itemName" => (string)$r["item_name"],
      "erp" => $r["erp"] !== null ? (string)$r["erp"] : null,
      "itemGroupId" => (int)$r["item_group_id"],
      "itemGroup" => (string)$r["item_group"],
    ]);
    exit;
  }

  if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    $body = json_decode((string)file_get_contents("php://input"), true);
    $itemName = trim((string)($body["itemName"] ?? ""));
    $itemGroupId = (int)($body["itemGroupId"] ?? 0);
    $erp = array_key_exists("erp", (array)$body) ? trim((string)$body["erp"]) : null;

    if ($itemName === "" || $itemGroupId <= 0) {
      http_response_code(400);
      echo json_encode(["error" => "itemName and itemGroupId are required"]);
      exit;
    }

    $stmt = $pdo->prepare(
      "UPDATE items SET item_name=:n, item_group_id=:gid, erp=:e WHERE id=:id"
    );
    $stmt->execute([
      ":n" => $itemName,
      ":gid" => $itemGroupId,
      ":e" => ($erp === "" ? null : $erp),
      ":id" => $id,
    ]);
    echo json_encode(["ok" => true]);
    exit;
  }

  if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $stmt = $pdo->prepare("DELETE FROM items WHERE id=:id");
    $stmt->execute([":id" => $id]);
    echo json_encode(["ok" => true]);
    exit;
  }

  http_response_code(405);
  echo json_encode(["error" => "Method not allowed"]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(["error" => "Server error", "detail" => $e->getMessage()]);
}

