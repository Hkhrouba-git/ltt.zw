<?php
session_start();
header('Content-Type: application/json');

// تحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'غير مسموح، الرجاء تسجيل الدخول']);
    exit;
}

$user_id = $_SESSION['user_id'];

// استلام بيانات الطلب
$data = file_get_contents('php://input');
$order_details = json_decode($data, true);

if (!$order_details || !is_array($order_details) || count($order_details) === 0) {
    echo json_encode(['success' => false, 'message' => 'بيانات الطلب غير صالحة']);
    exit;
}

require 'db.php'; // الاتصال بقاعدة البيانات

try {
    // جلب اسم المستخدم بناءً على user_id
    $stmtUser = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmtUser->execute([$user_id]);
    $username = $stmtUser->fetchColumn();

    if (!$username) {
        echo json_encode(['success' => false, 'message' => 'المستخدم غير موجود']);
        exit;
    }

    // تحويل الطلب إلى JSON
    $order_details_json = json_encode($order_details, JSON_UNESCAPED_UNICODE);

    // حفظ الطلب مع اسم المستخدم
    $stmt = $pdo->prepare('INSERT INTO orders (user_id, username, order_details, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())');
    $stmt->execute([$user_id, $username, $order_details_json]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات: ' . $e->getMessage()]);
}
?>
