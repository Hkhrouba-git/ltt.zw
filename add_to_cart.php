<?php
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['name'], $data['price'], $data['qty'])) {
    http_response_code(400);
    echo json_encode(['error' => 'بيانات غير كاملة']);
    exit;
}

$id = (int)$data['id'];
$name = $data['name'];
$price = (float)$data['price'];
$qty = (int)$data['qty'];

if ($qty < 1) $qty = 1;
if ($qty > 100) $qty = 100;

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// تحديث السلة في الجلسة
$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['id'] === $id) {
        $item['qty'] += $qty;
        if ($item['qty'] > 100) $item['qty'] = 100;
        $found = true;
        break;
    }
}
unset($item);

if (!$found) {
    $_SESSION['cart'][] = ['id' => $id, 'name' => $name, 'price' => $price, 'qty' => $qty];
}

$totalQty = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalQty += $item['qty'];
}

echo json_encode(['success' => true, 'cartCount' => $totalQty]);
