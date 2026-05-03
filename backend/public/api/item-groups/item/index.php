<?php

declare(strict_types=1);

require_once __DIR__ . "/../../../../src/bootstrap.php";

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: PUT, DELETE, OPTIONS");

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
  if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    $body = json_decode((string)file_get_contents("php://input"), true);
    $itemGroup = trim((string)($body["itemGroup"] ?? ""));
    if ($itemGroup === "") {
      http_response_code(400);
      echo json_encode(["error" => "itemGroup is required"]);
      exit;
    }
    $stmt = $pdo->prepare("UPDATE item_groups SET item_group=:g WHERE id=:id");
    $stmt->execute([":g" => $itemGroup, ":id" => $id]);
    echo json_encode(["ok" => true]);
    exit;
  }

  if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $stmt = $pdo->prepare("DELETE FROM item_groups WHERE id=:id");
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

