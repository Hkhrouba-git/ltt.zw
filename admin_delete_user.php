<?php
session_start();
require 'db.php';

// التحقق من صلاحية المسؤول
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit;
}

// التحقق من وجود معرف المستخدم في GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('معرف المستخدم غير موجود.');
}

$user_id = (int)$_GET['id'];

// حذف المستخدم من قاعدة البيانات
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$user_id]);

// إعادة التوجيه إلى صفحة المسؤول
header('Location: admin.php');
exit;
