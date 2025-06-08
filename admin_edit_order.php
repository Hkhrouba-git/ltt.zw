<?php
session_start();
require 'db.php';

// تحقق من صلاحية المسؤول
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['id'] ?? 0;
if (!$order_id) {
    echo "رقم الطلب غير صالح.";
    exit;
}

// تحديث حالة الطلب عند الإرسال
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'] ?? '';

    $stmt = $pdo->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$new_status, $order_id]);

    header("Location: admin.php");  // ارجع إلى لوحة التحكم (ممكن تغيره حسب مسارك)
    exit;
}

// جلب بيانات الطلب
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    echo "الطلب غير موجود.";
    exit;
}

// قائمة الحالات الممكنة
$status_options = ['معلق', 'قيد التنفيذ', 'تم التسليم', 'ملغي'];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>تعديل حالة الطلب #<?= htmlspecialchars($order['id']) ?></title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f9f9f9;
        padding: 20px;
        direction: rtl;
    }
    form {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        max-width: 400px;
        margin: auto;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
    }
    select {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
    }
    button {
        padding: 10px 20px;
        background-color: #006699;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }
    button:hover {
        background-color: #004466;
    }
</style>
</head>
<body>

<h2>تعديل حالة الطلب رقم #<?= htmlspecialchars($order['id']) ?></h2>

<form method="post">
    <label for="status">حالة الطلب:</label>
    <select name="status" id="status" required>
        <?php foreach ($status_options as $status): ?>
            <option value="<?= htmlspecialchars($status) ?>" <?= $order['status'] === $status ? 'selected' : '' ?>>
                <?= htmlspecialchars($status) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">حفظ التغييرات</button>
</form>

<p style="text-align:center; margin-top:20px;">
    <a href="admin.php">⬅️ رجوع إلى لوحة التحكم</a>
</p>

</body>
</html>
