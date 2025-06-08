<?php
session_start();
require 'db.php';

// التحقق من صلاحية المسؤول
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit;
}

// جلب المستخدمين
$stmt = $pdo->query("SELECT id, username FROM users ORDER BY id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// جلب المنتجات
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// جلب الطلبات
$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// جلب بيانات المخزون
$stmt = $pdo->query("SELECT * FROM stock ORDER BY id DESC");
$stock_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>لوحة تحكم المسؤول - LTT.ZW</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f0f4ff;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        .sidebar {
            width: 220px;
            background-color: #1f4e8c;
            color: white;
            padding: 20px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .sidebar h2 {
            margin: 0 0 30px 0;
            font-weight: bold;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            padding: 10px 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #124a9e;
        }
        main {
            flex-grow: 1;
            background-color: #fff;
            padding: 25px;
            overflow-y: auto;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        header h1 {
            color: #1f4e8c;
            margin: 0;
        }
        header .logout-btn {
            background-color: #d11a2a;
            border: none;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background-color 0.3s;
        }
        header .logout-btn:hover {
            background-color: #a50f1c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: center;
        }
        th {
            background-color: #1f4e8c;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9faff;
        }
        .action-link {
            color: #d11a2a;
            font-size: 18px;
            cursor: pointer;
            text-decoration: none;
        }
        .action-link:hover {
            color: #a50f1c;
        }
        /* زر الإضافة */
        .add-btn {
            display: inline-block;
            margin-bottom: 10px;
            padding: 8px 15px;
            background-color: #1662d1;
            color: white;
            border-radius: 6px;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .add-btn:hover {
            background-color: #0d47a1;
        }
        /* Sections */
        .section {
            display: none;
        }
        .section.active {
            display: block;
        }
    </style>
</head>
<body>
<!-- صندوق التنبيه داخل الصفحة -->
<div id="notification" style="
    display:none;
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #27ae60;
    color: white;
    padding: 15px 30px;
    border-radius: 5px;
    box-shadow: 0 0 15px rgba(0,0,0,0.2);
    font-weight: bold;
    z-index: 1000;
    cursor: pointer;
">
    لديك طلب جديد! اضغط هنا لعرض الطلبات.
</div>

<!-- صوت التنبيه -->
<audio id="notifSound" src="notification.mp3" preload="auto"></audio>

    <div class="sidebar">
        <h2>لوحة التحكم</h2>
        <a href="#" class="active" onclick="showSection('users'); return false;">👤 إدارة المستخدمين</a>
        <a href="#" onclick="showSection('products'); return false;">📦 إدارة المنتجات</a>
        <a href="#" onclick="showSection('orders'); return false;">📝 إدارة الطلبات</a>
        <a href="#" onclick="showSection('stock'); return false;">🏪 إدارة المخزون</a>
        <a href="logout.php" style="margin-top:auto; background-color:#d11a2a; border-radius:8px; justify-content:center;">
            🔒 تسجيل الخروج
        </a>
    </div>

    <main>
        <header>
            <h1>مرحباً، <?= htmlspecialchars($_SESSION['username'] ?? 'المسؤول') ?></h1>
            <form method="post" action="logout.php" style="margin:0;">
                <button type="submit" class="logout-btn" title="تسجيل الخروج">🔒 تسجيل الخروج</button>
            </form>
        </header>

        <!-- إدارة المستخدمين -->
        <section id="users" class="section active">
            <h2>إدارة المستخدمين</h2>
            <a href="register.php" class="add-btn">+ إضافة مستخدم جديد</a>
            <?php if (count($users) === 0): ?>
                <p>لا يوجد مستخدمون حالياً.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>رقم المستخدم</th>
                        <th>اسم المستخدم</th>
                        <th>حذف</th>
                    </tr>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td>
                            <a href="admin_delete_user.php?id=<?= $user['id'] ?>" 
                               onclick="return confirm('هل أنت متأكد من حذف المستخدم؟');"
                               class="action-link" title="حذف المستخدم">🗑️</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </section>

        <!-- إدارة المنتجات -->
        <section id="products" class="section">
            <h2>إدارة المنتجات</h2>
            <p><a href="admin_add_product.php" class="add-btn">+ إضافة منتج جديد</a></p>
            <?php if (count($products) === 0): ?>
                <p>لا توجد منتجات حالياً.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>الصورة</th>
                        <th>اسم المنتج</th>
                        <th>السعر (ZW$)</th>
                        <th>تعديل</th>
                        <th>حذف</th>
                    </tr>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="max-width: 80px; max-height: 60px;"></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= number_format($product['price'], 2) ?></td>
                        <td><a href="admin_edit_product.php?id=<?= $product['id'] ?>" class="action-link" title="تعديل المنتج">✏️</a></td>
                        <td>
                            <form method="post" action="admin_delete_product.php" onsubmit="return confirm('هل أنت متأكد من حذف المنتج؟');" style="margin:0;">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>" />
                                <button type="submit" class="action-link" style="background:none; border:none; padding:0; font-size:18px; color:#d11a2a; cursor:pointer;">🗑️</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </section>

        <!-- إدارة الطلبات -->
        <section id="orders" class="section">
            <h2>إدارة الطلبات</h2>
            <?php if (count($orders) === 0): ?>
                <p>لا توجد طلبات حالياً.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>الوكيل</th>
                        <th>تاريخ الطلب</th>
                        <th>حالة الطلب</th>
                        <th>تعديل</th>
                    </tr>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['id']) ?></td>
                        <td><?= htmlspecialchars($order['username']) ?></td>
                        <td><?= htmlspecialchars($order['created_at']) ?></td>
                        <td><?= htmlspecialchars($order['status']) ?></td>
                        <td><a href="admin_edit_order.php?id=<?= $order['id'] ?>" class="action-link" title="تعديل الطلب">✏️</a></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </section>

        <!-- إدارة المخزون -->
        <section id="stock" class="section">
            <h2>إدارة المخزون</h2>
            <?php if (count($stock_items) === 0): ?>
                <p>لا توجد عناصر في المخزون حالياً.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>اسم العنصر</th>
                        <th>الكمية</th>
                        <th>تعديل</th>
                    </tr>
                    <?php foreach ($stock_items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td><a href="admin_edit_stock.php?id=<?= $item['id'] ?>" class="action-link" title="تعديل العنصر">✏️</a></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </section>

    </main>

    <script>
        function showSection(id) {
            const sections = document.querySelectorAll('.section');
            sections.forEach(sec => {
                if (sec.id === id) {
                    sec.classList.add('active');
                } else {
                    sec.classList.remove('active');
                }
            });
            // تحديث حالة القائمة الجانبية
            const links = document.querySelectorAll('.sidebar a');
            links.forEach(link => {
                if (link.textContent.trim().includes(id === 'users' ? 'المستخدمين' : id === 'products' ? 'المنتجات' : id === 'orders' ? 'الطلبات' : 'المخزون')) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        }
    </script>
<script>
let lastCheck = new Date().toISOString();
const audio = document.getElementById('notifSound');
const notification = document.getElementById('notification');

function checkNewOrders() {
    fetch('check_new_orders.php?last_check=' + encodeURIComponent(lastCheck))
        .then(response => response.json())
        .then(data => {
            if (data.new_orders) {
                // عرض التنبيه
                notification.style.display = 'block';
                // تشغيل الصوت
                audio.play();
                // تحديث آخر وقت فحص
                lastCheck = new Date().toISOString();
            }
        })
        .catch(console.error);
}

// إخفاء التنبيه عند الضغط عليه والانتقال إلى صفحة الطلبات
notification.addEventListener('click', () => {
    notification.style.display = 'none';
    window.location.href = 'orders.php';  // عدل الرابط حسب موقع صفحة الطلبات عندك
});

// تحقق كل 10 ثواني
setInterval(checkNewOrders, 10000);
</script>

</body>
</html>
