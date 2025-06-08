<?php
session_start();
require 'db.php';


// مثال بسيط لمعالجة تسجيل الدخول مع "ذكرني"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['is_admin'] = $user['is_admin'];

        if ($remember) {
            // إعداد cookie ليبقى المستخدم مسجّل دخوله لمدة 30 يومًا
            setcookie('remember_user', $user['id'], time() + (86400 * 30), "/");
        }

        if ($user['is_admin']) {
            header('Location: admin.php');
        } else {
            header('Location: products.php');
        }
        exit;
    } else {
        $error = "اسم المستخدم أو كلمة المرور غير صحيحة";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>تسجيل الدخول - LTT.ZW</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet" />
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
            width: 200px;
            margin-bottom: 5px;
        }
        .login-box h2 {
            margin-bottom: 25px;
            color: #1f4e8c;
        }
        .login-box input {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: 0.3s;
            font-size: 16px;
            box-sizing: border-box;
        }
        .login-box input:focus {
            border-color: #1f4e8c;
            outline: none;
        }
        .checkbox-container {
            display: flex;
            align-items: center;
            margin: 10px 0 20px 0;
            font-size: 14px;
            color: #1f4e8c;
            cursor: pointer;
            user-select: none;
        }
        .checkbox-container input[type="checkbox"] {
            margin-left: 10px;
            width: 18px;
            height: 18px;
            cursor: pointer;
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
        <h2>وكلاء مركز خدمات الزاوية</h2>

        <?php if (!empty($error)) : ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <input type="text" name="username" placeholder="اسم المستخدم" required autofocus />
            <input type="password" name="password" placeholder="كلمة المرور" required />
            <label class="checkbox-container">ذكرني
                <input type="checkbox" name="remember" />
            </label>
            <button type="submit">دخول</button>
        </form>

       
    </div>
</body>
</html>
