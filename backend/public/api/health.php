<?php

// Minimal health endpoint to verify PHP is executing (no rewrites, no includes).
header("Content-Type: application/json; charset=utf-8");
echo json_encode(["ok" => true, "php" => PHP_VERSION, "time" => gmdate("c")]);

