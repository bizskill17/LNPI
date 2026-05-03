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
      $whereSql = "WHERE i.item_name LIKE :q OR g.item_group LIKE :q OR i.erp LIKE :q";
      $params[":q"] = "%" . $q . "%";
    }

    $stmtTotal = $pdo->prepare(
      "SELECT COUNT(*) AS c
       FROM items i
       JOIN item_groups g ON g.id = i.item_group_id
       $whereSql"
    );
    $stmtTotal->execute($params);
    $total = (int)($stmtTotal->fetchColumn() ?: 0);

    $offset = ($page - 1) * $pageSize;
    $stmt = $pdo->prepare(
      "SELECT i.id, i.item_name, i.erp, i.item_group_id, g.item_group
       FROM items i
       JOIN item_groups g ON g.id = i.item_group_id
       $whereSql
       ORDER BY i.item_name ASC
       LIMIT :limit OFFSET :offset"
    );

    foreach ($params as $k => $v) $stmt->bindValue($k, $v, PDO::PARAM_STR);
    $stmt->bindValue(":limit", $pageSize, PDO::PARAM_INT);
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    $rows = [];
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $rows[] = [
        "id" => (int)$r["id"],
        "itemName" => (string)$r["item_name"],
        "erp" => $r["erp"] !== null ? (string)$r["erp"] : null,
        "itemGroupId" => (int)$r["item_group_id"],
        "itemGroup" => (string)$r["item_group"],
      ];
    }

    echo json_encode(["rows" => $rows, "total" => $total]);
    exit;
  }

  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $body = json_decode((string)file_get_contents("php://input"), true);
    $itemName = trim((string)($body["itemName"] ?? ""));
    $itemGroupId = (int)($body["itemGroupId"] ?? 0);
    $erp = isset($body["erp"]) ? trim((string)$body["erp"]) : null;

    if ($itemName === "" || $itemGroupId <= 0) {
      http_response_code(400);
      echo json_encode(["error" => "itemName and itemGroupId are required"]);
      exit;
    }

    $stmt = $pdo->prepare(
      "INSERT INTO items (item_group_id, item_name, erp) VALUES (:gid, :n, :e)"
    );
    $stmt->execute([":gid" => $itemGroupId, ":n" => $itemName, ":e" => ($erp === "" ? null : $erp)]);
    echo json_encode(["ok" => true, "id" => (int)$pdo->lastInsertId()]);
    exit;
  }

  http_response_code(405);
  echo json_encode(["error" => "Method not allowed"]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(["error" => "Server error", "detail" => $e->getMessage()]);
}
