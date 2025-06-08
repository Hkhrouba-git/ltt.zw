<?php
session_start();
require 'db.php';

// تحقق من صلاحية المسؤول
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// عندما يتم إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);

    // التحقق من وجود صورة
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $file_name = $_FILES['image']['name'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed)) {
            // مسار حفظ الصورة
            $new_filename = uniqid() . '.' . $file_ext;
            $upload_path = 'uploads/' . $new_filename;

            // تأكد أن مجلد uploads موجود
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

            if (move_uploaded_file($file_tmp, $upload_path)) {
                // أدخل المنتج في قاعدة البيانات
                $stmt = $pdo->prepare("INSERT INTO products (name, image, price) VALUES (?, ?, ?)");
                if ($stmt->execute([$name, $upload_path, $price])) {
                    $success = "تم إضافة المنتج بنجاح!";
                    // إعادة التوجيه إلى صفحة admin
                    header('Location: admin.php');
                    exit;
                } else {
                    $error = "حدث خطأ أثناء إضافة المنتج.";
                }
            } else {
                $error = "فشل رفع الصورة.";
            }
        } else {
            $error = "صيغة الصورة غير مدعومة. الصيغ المسموحة: jpg, jpeg, png, gif.";
        }
    } else {
        $error = "يجب رفع صورة للمنتج.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>إضافة منتج جديد - LTT.ZW</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f0f4ff;
            padding: 20px;
            text-align: center;
        }
        h2 {
            color: #1f4e8c;
            margin-bottom: 20px;
        }
        form {
            display: inline-block;
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            text-align: right;
        }
        input[type="text"],
        input[type="number"],
        input[type="file"] {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
        }
        button {
            background-color: #1662d1;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        button:hover {
            background-color: #124a9e;
        }
        .error {
            color: #d32f2f;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .success {
            color: #388e3c;
            margin-bottom: 15px;
            font-weight: bold;
        }
        a.back {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #1f4e8c;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h2>إضافة منتج جديد</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>اسم المنتج:</label>
        <input type="text" name="name" required />

        <label>السعر:</label>
        <input type="number" name="price" step="0.01" required />

        <label>صورة المنتج:</label>
        <input type="file" name="image" required />

        <button type="submit">إضافة المنتج</button>
    </form>

    <br>
    <a href="admin.php" class="back">← رجوع إلى لوحة التحكم</a>

</body>
</html>
