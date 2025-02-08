<?php
session_start(); // بدء الجلسة

// تحقق من تسجيل الدخول كمعلم
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'teacher' && $_SESSION['role'] !== 'admin')) {
    header("Location: login.php"); // إعادة التوجيه إلى صفحة تسجيل الدخول
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم المعلم</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to left, #6a11cb, #2575fc);
            color: white;
        }
        .container {
            max-width: 800px;
            min-height: 400px;
            margin: 40px auto;
            padding: 30px;
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
        }
        .button {
            background-color: #007BFF;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin: 10px;
            font-size: 1.2em;
            width: 100%;
            max-width: 300px;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>لوحة تحكم المعلم</h1>
    <button class="button" onclick="window.location.href='Add_exam.php'">إضافة امتحان جديد</button>
    <button class="button" onclick="window.location.href='manage_exams.php'">إدارة الامتحانات السابقة</button>
    <button class="button" onclick="window.location.href='view_results.php'">عرض نتائج الطلاب</button>
    <button class="button" onclick="window.location.href='view_complaints.php'">عرض تظلمات الطلاب</button>
    <button class="button" onclick="window.location.href='logout.php'">تسجيل الخروج</button>
</div>

</body>
</html>