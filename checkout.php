<?php
session_start();

// استلم بيانات السلة من POST أو من الجلسة
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // جلب بيانات السلة المرسلة كـ JSON
    $cartJson = $_POST['cart'] ?? '[]';
    $cart = json_decode($cartJson, true);
    if (!is_array($cart)) $cart = [];

    // حفظ السلة في الجلسة للمراجعة لاحقاً
    $_SESSION['last_order'] = $cart;

} else {
    // إذا لم يتم إرسال بيانات، استخدم السلة المحفوظة مسبقاً أو اجعلها فارغة
    $cart = $_SESSION['last_order'] ?? [];
}

function calculateTotal($cart) {
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['qty'];
    }
    return $total;
}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>إتمام الطلب - متجر LTT.ZW</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background: #f5f7fa;
            color: #333;
            padding: 20px;
        }
        h1 {
            color: #1f4e8c;
            margin-bottom: 25px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.07);
        }
        th, td {
            padding: 15px;
            text-align: right;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #1662d1;
            color: white;
        }
        td.price, td.qty, td.total {
            font-weight: 700;
            color: #1662d1;
        }
        .total-row td {
            font-size: 20px;
            font-weight: 900;
            color: #0d47a1;
        }
        button.confirm-btn {
            background-color: #1f4e8c;
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 18px;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            display: block;
            margin: 0 auto;
            transition: background-color 0.3s ease;
        }
        button.confirm-btn:hover {
            background-color: #0b3a67;
        }
        .msg {
            text-align: center;
            font-size: 18px;
            color: green;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<h1>إتمام الطلب</h1>

<?php if (empty($cart)): ?>
    <p style="text-align:center; font-size:18px;">سلة التسوق فارغة، يرجى إضافة منتجات قبل تأكيد الطلب.</p>
<?php else: ?>

    <table>
        <thead>
            <tr>
                <th>المنتج</th>
                <th>الكمية</th>
                <th>السعر للوحدة (LYD)</th>
                <th>الإجمالي (LYD)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cart as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td class="qty"><?= (int)$item['qty'] ?></td>
                    <td class="price"><?= number_format($item['price'], 2) ?></td>
                    <td class="total"><?= number_format($item['price'] * $item['qty'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="3">الإجمالي الكلي</td>
                <td><?= number_format(calculateTotal($cart), 2) ?></td>
            </tr>
        </tbody>
    </table>

    <?php
    // معالجة تأكيد الطلب
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
        // هنا يمكن تخزين الطلب في قاعدة البيانات إذا كنت تريد
        // الآن فقط نظهر رسالة تأكيد

        echo '<p class="msg">تم تأكيد الطلب بنجاح، شكراً لتسوقك معنا!</p>';

        // يمكن مسح السلة بعد التأكيد
        unset($_SESSION['last_order']);
        $cart = [];
    }
    ?>

    <?php if (!empty($cart)): ?>
    <form method="post" style="text-align:center;">
        <button type="submit" name="confirm_order" class="confirm-btn">تأكيد الطلب</button>
    </form>
    <?php endif; ?>

<?php endif; ?>

</body>
</html>
