<?php
session_start();

// تحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// تخزين معلومات المستخدم من الجلسة
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'طالبنا العزيز';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'البريد الإلكتروني غير متوفر';
$phone = isset($_SESSION['phone']) ? $_SESSION['phone'] : 'رقم الهاتف غير متوفر';
$gender = isset($_SESSION['gender']) ? $_SESSION['gender'] : 'غير محدد';
$created_at = isset($_SESSION['created_at']) ? $_SESSION['created_at'] : 'غير محدد';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>معلومات المستخدم</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }
        .container:hover {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .info {
            margin: 20px 0;
            padding: 15px;
            background: #e9ecef;
            border-radius: 5px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .info strong {
            color: #007bff;
        }
        .button {
            display: block;
            width: 96%;
            padding: 15px;
            margin: 10px 0;
            text-align: center;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }

        
    </style>
</head>
<body>
    <div class="container">
        <h1>معلومات المستخدم</h1>
        
        <div class="info">
            <strong>الاسم الكامل:</strong> <?php echo htmlspecialchars($full_name); ?>
            <strong>اسم المستخدم:</strong> <?php echo htmlspecialchars($username); ?>
            <strong>البريد الإلكتروني:</strong> <?php echo htmlspecialchars($email); ?>
            <strong>رقم الهاتف:</strong> <?php echo htmlspecialchars($phone); ?>
            <strong>الجنس:</strong> <?php echo htmlspecialchars($gender); ?>
            <strong>تاريخ الإنشاء:</strong> <?php echo htmlspecialchars($created_at); ?>
        </div>

        <a href="edit_user_info.php" class="button"  style="text-decoration: nune;">تعديل المعلومات</a>
        <a href="logout.php" class="button"  style="text-decoration: nune;">تسجيل الخروج</a>

        <div class="footer">
            <p>&copy;  جميع الحقوق محفوظة. <?php echo date("Y")?></p>
        </div>
    </div>
</body>
</html>