<?php
session_start();
require 'db.php';

$last_check = $_GET['last_check'] ?? null;

if (!$last_check) {
    echo json_encode(['new_orders' => false]);
    exit;
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE created_at > ?");
$stmt->execute([$last_check]);
$count = $stmt->fetchColumn();

echo json_encode(['new_orders' => $count > 0]);
