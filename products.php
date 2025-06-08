<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'] ?? 'وكيل مركز خدمات الزاوية';

$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8" />
<title>المنتجات - متجر LTT.ZW</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet" />
<style>
    body {
        font-family: 'Cairo', sans-serif;
        margin: 0;
        background: #f5f7fa;
        color: #444;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    header {
        background-color: #3a6ea5;
        color: #e4ebf5;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        gap: 10px;
    }
    .user-cart-wrapper {
        display: flex;
        align-items: center;
        gap: 20px;
        font-size: 18px;
        font-weight: 600;
        color: #e4ebf5;
    }
    .username {
        user-select: none;
    }
    .cart-btn {
        background: transparent;
        border: none;
        color: #e4ebf5;
        cursor: pointer;
        position: relative;
        display: flex;
        align-items: center;
        font-size: 20px;
        transition: color 0.3s ease;
    }
    .cart-btn:hover {
        color: #b8d2ff;
    }
    .cart-count {
        position: absolute;
        top: -6px;
        right: -8px;
        background: #d94e4e;
        color: white;
        border-radius: 50%;
        padding: 2px 7px;
        font-size: 12px;
        font-weight: 700;
        user-select: none;
    }
    header button.logout-btn {
        background-color: #b33a3a;
        border: none;
        color: #f4eaea;
        padding: 8px 18px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: background-color 0.3s;
    }
    header button.logout-btn:hover {
        background-color: #8c2a2a;
    }

    main {
        flex-grow: 1;
        padding: 25px 40px;
    }

    h1 {
        margin-bottom: 25px;
        color: #2f5496;
        font-weight: 700;
        text-align: center;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px;
        justify-content: center;
    }

    .product-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.06);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: transform 0.25s ease;
        max-width: 220px;
        margin: 0 auto;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.10);
    }
    .product-img {
        width: 100%;
        aspect-ratio: 4 / 3;
        object-fit: cover;
        border-bottom: 1px solid #ddd;
    }
    .product-details {
        padding: 15px 18px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .product-name {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #335d90;
    }
    .price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        font-weight: 700;
        color: #2a4d7b;
        font-size: 16px;
    }
    .price-row span {
        user-select: none;
    }
    .quantity-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 15px;
    }
    .quantity-wrapper label {
        font-weight: 600;
        color: #5679a3;
    }
    .quantity-wrapper input[type=number] {
        width: 60px;
        padding: 5px 8px;
        border-radius: 6px;
        border: 1px solid #aac0db;
        font-size: 16px;
        text-align: center;
        outline-offset: 2px;
        transition: border-color 0.2s;
    }
    .quantity-wrapper input[type=number]:focus {
        border-color: #4a7bc1;
        box-shadow: 0 0 6px #7fa3d9aa;
    }
    .btn-add-cart {
        background-color: #4a7bc1;
        border: none;
        color: white;
        font-weight: 700;
        border-radius: 8px;
        padding: 11px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: background-color 0.3s ease;
    }
    .btn-add-cart:hover {
        background-color: #375a8c;
    }
    .btn-add-cart svg {
        width: 20px;
        height: 20px;
        fill: white;
    }

    @media (max-width: 600px) {
        main {
            padding: 20px 15px;
        }
        .product-grid {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        .product-card {
            max-width: 150px;
        }
        .product-name {
            font-size: 16px;
        }
        .price-row {
            font-size: 14px;
        }
        .btn-add-cart {
            padding: 8px;
            font-size: 14px;
        }
    }

    #cart-msg {
        position: fixed;
        bottom: 25px;
        left: 50%;
        transform: translateX(-50%);
        background: #4a7bc1cc;
        color: #eef3fc;
        padding: 12px 28px;
        border-radius: 30px;
        font-weight: 700;
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
        z-index: 1000;
        user-select: none;
    }
    #cart-msg.show {
        opacity: 1;
        pointer-events: auto;
    }
</style>
</head>
<body>

<header>
    <div class="user-cart-wrapper">
       <div class="username">مرحباً، <?= htmlspecialchars($username) ?></div>
        <!-- زر عرض الطلبات الجديد -->
         <form method="post" action="orders.php" style="margin:0;">
        <button class="orders-btn" title="عرض الطلبات" onclick="goToOrders()">
       
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="width:20px;height:20px;">
                <path d="M3 3h18v4H3zM3 8h18v2H3zM3 13h18v6H3z"></path>
            </svg>
        </button>
         </form>
        <button class="cart-btn" aria-label="عرض السلة" onclick="goToCart()">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="width:24px;height:24px;">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            <span class="cart-count" id="cart-count">0</span>
        </button>
    </div>
    <form method="post" action="logout.php" style="margin:0;">
        <button type="submit" class="logout-btn" title="تسجيل الخروج">
            تسجيل خروج
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/></svg>
        </button>
    </form>
</header>

<main>
    <h1>المنتجات المتوفرة</h1>

    <div class="product-grid">
        <?php foreach ($products as $p): ?>
            <div class="product-card" data-id="<?= $p['id'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>" data-price="<?= $p['price'] ?>">
                <img src="<?= htmlspecialchars($p['image']) ?>" alt="صورة <?= htmlspecialchars($p['name']) ?>" class="product-img" />
                <div class="product-details">
                    <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                    <div class="price-row">
                        <span>السعر:</span>
                        <span><?= number_format($p['price'], 2) ?> دينار</span>
                    </div>
                    <div class="quantity-wrapper">
                        <label for="qty-<?= $p['id'] ?>">الكمية:</label>
                        <input type="number" id="qty-<?= $p['id'] ?>" min="1" max="99" value="1" />
                    </div>
                    <button class="btn-add-cart" onclick="addToCart(event, <?= $p['id'] ?>)">أضف إلى السلة
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<div id="cart-msg">تم إضافة المنتج إلى السلة</div>

<script>
// جلب بيانات السلة من localStorage أو إنشاء جديدة
function getCart() {
    let cart = localStorage.getItem('cart');
    if (cart) {
        try {
            return JSON.parse(cart);
        } catch {
            return {};
        }
    }
    return {};
}

// حفظ بيانات السلة في localStorage
function saveCart(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
}

// تحديث رقم السلة في واجهة المستخدم
function updateCartCount() {
    const cart = getCart();
    let count = 0;
    for (const key in cart) {
        count += cart[key].quantity;
    }
    document.getElementById('cart-count').textContent = count;
}

// إضافة منتج إلى السلة
function addToCart(event, productId) {
    event.preventDefault();

    const card = event.currentTarget.closest('.product-card');
    const name = card.dataset.name;
    const price = parseFloat(card.dataset.price);
    const qtyInput = document.getElementById('qty-' + productId);
    let quantity = parseInt(qtyInput.value);

    if (isNaN(quantity) || quantity < 1) {
        alert('يرجى إدخال كمية صحيحة');
        return;
    }

    let cart = getCart();
    if (cart[productId]) {
        cart[productId].quantity += quantity;
    } else {
        cart[productId] = { name, price, quantity };
    }

    saveCart(cart);
    showCartMessage();
}

// الانتقال إلى صفحة السلة
function goToCart() {
    window.location.href = 'cart.php';
}

// عرض رسالة إضافة المنتج للسلة لفترة قصيرة
function showCartMessage() {
    const msg = document.getElementById('cart-msg');
    msg.classList.add('show');
    setTimeout(() => {
        msg.classList.remove('show');
    }, 1800);
}

// تهيئة عدد السلة عند تحميل الصفحة
updateCartCount();
</script>

</body>
</html>
