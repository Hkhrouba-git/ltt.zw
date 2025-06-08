<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$username = $_SESSION['username'] ?? 'مستخدم';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>السلة - متجر LTT.ZW</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet" />
<style>
body {
  font-family: 'Cairo', sans-serif;
  background-color: #f9f9f9;
  margin: 0;
  padding: 0;
  direction: rtl;
}

header {
  background-color:rgb(103, 81, 233);
  color: white;
  padding: 1rem;
  text-align: center;
  font-size: 1.2rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.username {
  font-weight: bold;
}

main {
  max-width: 1000px;
  margin: 2rem auto;
  padding: 1rem;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

h1 {
  text-align: center;
  margin-bottom: 1.5rem;
  color: #333;
  font-size: 2rem;
}

#cart-body {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.cart-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #fafafa;
  border-radius: 12px;
  padding: 1rem;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  transition: box-shadow 0.3s ease;
}

.cart-item:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.cart-item .info {
  flex: 2;
}

.cart-item .info h3 {
  margin: 0;
  font-size: 1.2rem;
  color: #333;
}

.cart-item .info p {
  margin: 0.3rem 0 0;
  font-size: 1rem;
  color: #555;
}

.cart-item .quantity {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.quantity button {
  width: 32px;
  height: 32px;
  background-color: #eee;
  border: none;
  border-radius: 50%;
  font-size: 1.2rem;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.quantity button:hover {
  background-color: #ddd;
}

.quantity span {
  min-width: 30px;
  text-align: center;
  font-size: 1.1rem;
}

.cart-item .total {
  font-weight: bold;
  font-size: 1.2rem;
  color: #333;
}

.cart-item .actions button {
  background-color: #ff4d4d;
  border: none;
  color: white;
  padding: 0.5rem 1rem;
  cursor: pointer;
  border-radius: 8px;
  transition: background-color 0.3s ease;
}

.cart-item .actions button:hover {
  background-color: #e60000;
}

#empty-msg {
  text-align: center;
  color: #999;
  font-size: 1.1rem;
  padding: 2rem;
  border: 2px dashed #ccc;
  border-radius: 12px;
  background-color: #fafafa;
}

.cart-footer {
  text-align: center;
  margin-top: 2rem;
}

.cart-footer .total-price {
  font-size: 1.5rem;
  font-weight: bold;
  color: #333;
  margin-bottom: 1rem;
}

.btn-confirm {
  background-color:rgb(57, 209, 70);
  color: white;
  padding: 0.8rem 2rem;
  border: none;
  border-radius: 8px;
  font-size: 1.1rem;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-confirm:hover {
  background-color: #45a049;
  transform: translateY(-2px);
}

.btn-confirm:disabled,
.btn-confirm[aria-disabled="true"] {
  background-color: #ccc;
  cursor: not-allowed;
  transform: none;
}

.btn-back {
  background-color: #2196F3;
  color: white;
  padding: 0.7rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
  display: block;
  margin: 1rem auto 0;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-back:hover {
  background-color: #1e87e5;
  transform: translateY(-2px);
}

@media (max-width: 600px) {
  .cart-item {
    flex-direction: column;
    align-items: flex-start;
  }

  .cart-item .quantity {
    margin: 0.5rem 0;
  }

  .cart-item .actions {
    align-self: flex-end;
  }
}
</style>
</head>
<body>

<header>
  <div class="username">مرحبا <?= htmlspecialchars($username) ?></div>
</header>

<main>
  <h1>محتويات السلة</h1>

  <section class="cart-container" role="region" aria-label="محتويات السلة">
    <div id="cart-body"></div>
    <div id="empty-msg" style="display:none;" role="alert" aria-live="assertive">السلة فارغة حالياً.</div>
  </section>

  <div class="cart-footer">
    <div class="total-price" id="total-price">المجموع الكلي: 0.00 ر.س</div>
    <button class="btn-confirm" id="confirm-order-btn" disabled aria-disabled="true">تأكيد الطلب</button>
    <button class="btn-back" onclick="window.location.href='products.php'">العودة إلى المنتجات</button>
  </div>
</main>

<script>
function getCart() {
  let cart = localStorage.getItem('cart');
  if(cart) {
    try {
      return JSON.parse(cart);
    } catch {
      return {};
    }
  }
  return {};
}

function saveCart(cart) {
  localStorage.setItem('cart', JSON.stringify(cart));
}

function renderCart() {
  const cart = getCart();
  const cartBody = document.getElementById('cart-body');
  const totalPriceEl = document.getElementById('total-price');
  const emptyMsg = document.getElementById('empty-msg');
  const confirmBtn = document.getElementById('confirm-order-btn');

  cartBody.innerHTML = '';
  let total = 0;
  let hasItems = false;

  for (const id in cart) {
    if (!cart.hasOwnProperty(id)) continue;
    const item = cart[id];
    if(item.quantity < 1) continue;
    hasItems = true;
    const itemTotal = item.price * item.quantity;
    total += itemTotal;

    const itemEl = document.createElement('div');
    itemEl.classList.add('cart-item');
    itemEl.setAttribute('data-product-id', id);

    itemEl.innerHTML = `
      <div class="info">
        <h3>${item.name}</h3>
        <p>سعر الوحدة: ${item.price.toFixed(2)} دينار</p>
      </div>
      <div class="quantity">
        <button class="decrease">-</button>
        <span>${item.quantity}</span>
        <button class="increase">+</button>
      </div>
      <div class="total">${itemTotal.toFixed(2)}دينار</div>
      <div class="actions">
        <button class="btn-delete">حذف</button>
      </div>
    `;

    // زيادة الكمية
    itemEl.querySelector('.increase').addEventListener('click', () => {
      if (item.quantity < 99) {
        item.quantity++;
        saveCart(cart);
        renderCart();
      }
    });

    // تقليل الكمية
    itemEl.querySelector('.decrease').addEventListener('click', () => {
      if (item.quantity > 1) {
        item.quantity--;
      } else {
        delete cart[id];
      }
      saveCart(cart);
      renderCart();
    });

    // حذف العنصر
    itemEl.querySelector('.btn-delete').addEventListener('click', () => {
      delete cart[id];
      saveCart(cart);
      renderCart();
    });

    cartBody.appendChild(itemEl);
  }

  if (!hasItems) {
    emptyMsg.style.display = 'block';
    totalPriceEl.textContent = '  المجموع الكلي:  0.00 دينار';
    confirmBtn.disabled = true;
    confirmBtn.setAttribute('aria-disabled', 'true');
  } else {
    emptyMsg.style.display = 'none';
    totalPriceEl.textContent = 'المجموع الكلي: '   + total.toFixed(2) +   'دينار';
    confirmBtn.disabled = false;
    confirmBtn.setAttribute('aria-disabled', 'false');
  }
}

document.getElementById('confirm-order-btn').addEventListener('click', () => {
  const cart = getCart();
  fetch('save_order.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(cart)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('تم تأكيد الطلب بنجاح!');
      localStorage.removeItem('cart');
      renderCart();
    } else {
      alert('حدث خطأ أثناء تأكيد الطلب. حاول مرة أخرى.');
    }
  })
  .catch(() => {
    alert('خطأ في الاتصال بالخادم.');
  });
});

// بدء تشغيل الصفحة
renderCart();
</script>

</body>
</html>
