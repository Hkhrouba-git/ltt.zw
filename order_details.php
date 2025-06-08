<?php
session_start();
require 'db.php';  // ملف الاتصال بقاعدة البيانات

// تحقق من تسجيل دخول المستخدم (مفترض أن user_id مخزن في الجلسة)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = $_GET['id'] ?? 0;

if (!$order_id) {
    echo "رقم الطلب غير صالح.";
    exit;
}

// جلب بيانات الطلب مع التأكد أنه يخص المستخدم الحالي
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    echo "الطلب غير موجود أو لا تملك صلاحية الاطلاع عليه.";
    exit;
}

// فك تشفير تفاصيل الطلب (json)
$order_details = json_decode($order['order_details'], true);

if (!$order_details) {
    echo "تفاصيل الطلب غير صالحة.";
    exit;
}

// دالة للحصول على كلاس اللون حسب الحالة
function getStatusClass($status) {
    switch (strtolower($status)) {
        case 'معلق':
            return 'badge badge-blue';
        case 'قيد التنفيذ':
            return 'badge badge-orange';
        case 'تم التسليم':
            return 'badge badge-green';
        case 'ملغي':
            return 'badge badge-red';
        default:
            return 'badge badge-gray';
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>تفاصيل الطلب #<?= htmlspecialchars($order['id']) ?></title>
<style>
  body {
    font-family: 'Cairo', sans-serif;
    background: #f0f2f5;
    padding: 30px;
    direction: rtl;
    color: #333;
  }
  .container {
    max-width: 900px;
    margin: auto;
    background: #fff;
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  }
  h1 {
    color: #006699;
    font-size: 28px;
    margin-bottom: 10px;
  }
  .order-info {
    font-size: 16px;
    margin-bottom: 20px;
    color: #555;
  }
  .order-info span {
    font-weight: bold;
    color: #222;
  }
  .badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 12px;
    font-size: 14px;
    color: #fff;
  }
  .badge-blue { background-color: #3498db; }
  .badge-orange { background-color: #e67e22; }
  .badge-green { background-color: #2ecc71; }
  .badge-red { background-color: #e74c3c; }
  .badge-gray { background-color: #7f8c8d; }
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
  }
  th, td {
    padding: 12px;
    border-bottom: 1px solid #e0e0e0;
    text-align: center;
  }
  th {
    background-color: #006699;
    color: white;
    font-size: 15px;
  }
  tr:hover {
    background-color: #f9f9f9;
  }
  .total {
    font-size: 18px;
    font-weight: bold;
    margin-top: 20px;
    color: #333;
  }
  .back-link {
    display: inline-block;
    margin-top: 25px;
    text-decoration: none;
    background-color: #006699;
    color: white;
    padding: 10px 18px;
    border-radius: 8px;
    transition: background-color 0.3s ease;
  }
  .back-link:hover {
    background-color: #004d66;
  }
</style>
</head>
<body>

<div class="container">
  <h1>تفاصيل الطلب رقم #<?= htmlspecialchars($order['id']) ?></h1>

  <div class="order-info">
    <p><span>تاريخ الطلب:</span> <?= htmlspecialchars($order['created_at']) ?></p>
    <p>
      <span>حالة الطلب:</span> 
      <span class="<?= getStatusClass($order['status']) ?>">
        <?= htmlspecialchars($order['status']) ?>
      </span>
    </p>
  </div>

  <table>
  <thead>
  <tr>
    <th>اسم المنتج</th>
    <th>الكمية</th>
    <th>السعر للوحدة</th>
    <th>الإجمالي</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach ($order_details as $item): ?>
  <tr>
    <td><?= htmlspecialchars($item['name']) ?></td>
    <td><?= (int)$item['quantity'] ?></td>
    <td><?= number_format($item['price'], 2) ?> دينار</td>
    <td><?= number_format($item['price'] * $item['quantity'], 2) ?> دينار</td>
  </tr>
  <?php endforeach; ?>
  </tbody>
  </table>

  <p class="total">الإجمالي الكلي: 
  <?php 
  $total = 0;
  foreach ($order_details as $item) {
      $total += $item['price'] * $item['quantity'];
  }
  echo number_format($total, 2) . " دينار";
  ?>
  </p>

  <a class="back-link" href="orders.php">⬅️ عودة إلى قائمة الطلبات</a>
</div>

</body>
</html>
