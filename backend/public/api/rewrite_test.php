<?php
header("Content-Type: text/plain; charset=utf-8");
echo "rewrite_test ok\n";
echo "REQUEST_URI=" . ($_SERVER["REQUEST_URI"] ?? "") . "\n";
echo "SCRIPT_NAME=" . ($_SERVER["SCRIPT_NAME"] ?? "") . "\n";

