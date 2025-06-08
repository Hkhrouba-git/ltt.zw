<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require 'db.php';

$user_id = $_SESSION['user_id'];

// جلب الطلبات الخاصة بالمستخدم
$stmt = $pdo->prepare("SELECT id, order_details, created_at, status FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

function countItemsInOrder($order_details_json) {
    $items = json_decode($order_details_json, true);
    return is_array($items) ? count($items) : 0;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>قائمة الطلبات</title>
    <style>
        body {
            font-family: "Cairo", sans-serif;
            background-color: #f0f2f5;
            margin: 20px;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #34495e;
            font-size: 32px;
            margin-bottom: 25px;
        }
        table {
            width: 90%;
            max-width: 1000px;
            margin: 20px auto;
            border-collapse: separate;
            border-spacing: 0 10px;
        }
        th {
            background-color: #006699;
            color: white;
            padding: 14px;
            border-radius: 6px 6px 0 0;
            font-size: 15px;
        }
        td {
            background-color: #fff;
            padding: 14px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
            font-size: 15px;
        }
        tr {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        tr:hover {
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        a.details-link {
            color: #006699;
            text-decoration: none;
            font-weight: bold;
            padding: 6px 12px;
            border: 1px solid #006699;
            border-radius: 20px;
            transition: all 0.3s ease;
            display: inline-block;
        }
        a.details-link:hover {
            background-color: #006699;
            color: #fff;
        }
        .badge {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 13px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 13px;
            color: white;
        }
        .status-pending { background-color: #f39c12; } /* معلق */
        .status-processing { background-color: #2980b9; } /* قيد التنفيذ */
        .status-completed { background-color: #27ae60; } /* تم التسليم */
        .status-cancelled { background-color: #e74c3c; } /* ملغي */
        
        .no-orders {
            text-align: center;
            margin-top: 80px;
            color: #888;
            font-size: 1.3em;
        }
        .back-btn {
            display: block;
            width: 200px;
            margin: 40px auto 20px auto;
            padding: 12px 0;
            background-color: #006699;
            color: white;
            text-align: center;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
            font-size: 16px;
        }
        .back-btn:hover {
            background-color: #004d66;
        }
    </style>
</head>
<body>
    <h1>🛍️ قائمة الطلبات الخاصة بك</h1>

    <?php if (count($orders) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>رقم الطلب</th>
                <th>تاريخ الطلب</th>
                <th>عدد المنتجات</th>
                <th>حالة الطلب</th>
                <th>تفاصيل الطلب</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <?php
                // تجهيز كلاس الحالة
                $status_class = 'status-pending';
                if ($order['status'] == 'قيد التنفيذ') $status_class = 'status-processing';
                elseif ($order['status'] == 'تم التسليم') $status_class = 'status-completed';
                elseif ($order['status'] == 'ملغي') $status_class = 'status-cancelled';
            ?>
            <tr>
                <td><?php echo htmlspecialchars($order['id']); ?></td>
                <td><?php echo date("Y-m-d H:i", strtotime($order['created_at'])); ?></td>
                <td><span class="badge"><?php echo countItemsInOrder($order['order_details']); ?></span></td>
                <td><span class="status-badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($order['status']); ?></span></td>
                <td><a class="details-link" href="order_details.php?id=<?php echo urlencode($order['id']); ?>">عرض التفاصيل</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p class="no-orders">لا توجد طلبات حتى الآن.</p>
    <?php endif; ?>

    <a href="products.php" class="back-btn">⬅️ العودة للصفحة الرئيسية</a>
</body>
</html>
