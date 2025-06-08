<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $is_admin = isset($_POST['is_admin']) ? (int)$_POST['is_admin'] : 0;

    // تحقق من عدم وجود اسم مستخدم مكرر
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        $error = "اسم المستخدم موجود بالفعل، اختر اسمًا آخر.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, is_admin) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, $is_admin]);

        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['is_admin'] = $is_admin;

        if ($is_admin) {
            header('Location: admin.php');
        } else {
            header('Location: products.php');
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>تسجيل حساب جديد - LTT.ZW</title>
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #a3c4f3, #dceafe);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            direction: rtl;
        }
        .login-box {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-box img {
            width: 100px;
            margin-bottom: 20px;
        }
        .login-box h2 {
            margin-bottom: 25px;
            color: #1f4e8c;
        }
        .login-box input, .login-box select {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: 0.3s;
            font-size: 16px;
            box-sizing: border-box;
        }
        .login-box input:focus, .login-box select:focus {
            border-color: #1f4e8c;
            outline: none;
        }
        .login-box button {
            background-color: #1f4e8c;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            width: 100%;
        }
        .login-box button:hover {
            background-color: #163b6b;
        }
        .login-box p {
            margin-top: 15px;
            font-size: 14px;
        }
        .login-box a {
            color: #1f4e8c;
            text-decoration: none;
        }
        .login-box a:hover {
            text-decoration: underline;
        }
        .error {
            color: #d32f2f;
            margin-bottom: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <img src="logo.png" alt="LTT.ZW" />
        <h2>تسجيل حساب جديد في LTT.ZW</h2>

        <?php if (!empty($error)) : ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <input type="text" name="username" placeholder="اسم المستخدم" required autofocus />
            <input type="password" name="password" placeholder="كلمة المرور" required />
            <label for="is_admin" style="display:block; margin-top: 10px; font-weight: 600; color: #1f4e8c;">نوع المستخدم:</label>
            <select name="is_admin" id="is_admin" required>
                <option value="0" selected>مستخدم</option>
                <option value="1">مسؤول</option>
            </select>
            <button type="submit">تسجيل</button>
        </form>

        <p>لديك حساب؟ <a href="admin.php">دخول</a></p>
    </div>
</body>
</html>
