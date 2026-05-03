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

try {
  $pdo = lnpi_db();

  if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $q = trim((string)($_GET["q"] ?? ""));
    $page = max(1, (int)($_GET["page"] ?? 1));
    $pageSize = min(100, max(1, (int)($_GET["pageSize"] ?? 25)));

    $whereSql = "";
    $params = [];
    if ($q !== "") {
      $whereSql = "WHERE item_id LIKE :q OR item_name LIKE :q OR item_group LIKE :q";
      $params[":q"] = "%" . $q . "%";
    }

    $stmtTotal = $pdo->prepare("SELECT COUNT(*) AS c FROM items $whereSql");
    $stmtTotal->execute($params);
    $total = (int)($stmtTotal->fetchColumn() ?: 0);

    $offset = ($page - 1) * $pageSize;
    $stmt = $pdo->prepare(
      "SELECT item_id, item_group, item_name, erp
       FROM items
       $whereSql
       ORDER BY item_name ASC
       LIMIT :limit OFFSET :offset"
    );

    foreach ($params as $k => $v) $stmt->bindValue($k, $v, PDO::PARAM_STR);
    $stmt->bindValue(":limit", $pageSize, PDO::PARAM_INT);
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    $rows = [];
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $rows[] = [
        "itemId" => (string)$r["item_id"],
        "itemGroup" => (string)$r["item_group"],
        "itemName" => (string)$r["item_name"],
        "erp" => $r["erp"] !== null ? (string)$r["erp"] : null,
      ];
    }

    echo json_encode(["rows" => $rows, "total" => $total]);
    exit;
  }

  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $body = json_decode((string)file_get_contents("php://input"), true);
    $itemId = trim((string)($body["itemId"] ?? ""));
    $itemName = trim((string)($body["itemName"] ?? ""));
    $itemGroup = trim((string)($body["itemGroup"] ?? ""));
    $erp = isset($body["erp"]) ? trim((string)$body["erp"]) : null;

    if ($itemId === "" || $itemName === "" || $itemGroup === "") {
      http_response_code(400);
      echo json_encode(["error" => "itemId, itemName, itemGroup are required"]);
      exit;
    }

    // Ensure item group exists to satisfy FK.
    $stmtG = $pdo->prepare("INSERT IGNORE INTO item_groups (item_group) VALUES (:g)");
    $stmtG->execute([":g" => $itemGroup]);

    $stmt = $pdo->prepare(
      "INSERT INTO items (item_id, item_group, item_name, erp) VALUES (:id, :g, :n, :e)"
    );
    $stmt->execute([":id" => $itemId, ":g" => $itemGroup, ":n" => $itemName, ":e" => ($erp === "" ? null : $erp)]);
    echo json_encode(["ok" => true]);
    exit;
  }

  http_response_code(405);
  echo json_encode(["error" => "Method not allowed"]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(["error" => "Server error", "detail" => $e->getMessage()]);
}
