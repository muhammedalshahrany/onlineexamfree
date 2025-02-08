<?php
session_start();

// تحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// تخزين معلومات المستخدم من الجلسة
$name = isset($_SESSION['username']) ? $_SESSION['username'] : 'طالبنا العزيز';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'البريد الإلكتروني غير متوفر';

// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "OnlineExamDB");

// تحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// استعلام للتحقق من عدد الإشعارات الجديدة
$sql_notifications = "SELECT COUNT(*) AS count FROM notifications WHERE user_id = ? AND is_read = 0";
$stmt = $conn->prepare($sql_notifications);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$notification_count = $result->fetch_assoc()['count'];

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - الطالب</title>
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
        }
        .button {
            display: block;
            width: 96%;
            padding: 15px;
            margin: 10px 0; /* مسافة بين الأزرار */
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
        .notification-badge {
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 5px 10px;
            position: absolute;
            top: -10px;
            right: -10px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>مرحبًا بك، <?php echo htmlspecialchars($name); ?></h1>
        
        <div class="info">
            <strong>معلومات الحساب:</strong><br>
            البريد الإلكتروني: <?php echo htmlspecialchars($email); ?>
        </div>

        <a href="available_exams.php" class="button">عرض الامتحانات المتاحة</a>
        <a href="previous_results.php" class="button">عرض نتائج الاختبارات السابقة</a>
       
        
        <div style="position: relative; display: inline-block;">
            <a href="notifications.php" class="button" style="width: 770px;">الاشعارات</a>
            <?php if ($notification_count > 0): ?>
                <span class="notification-badge"><?php echo $notification_count; ?></span>
            <?php endif; ?>
        </div>

        <a href="user_info.php" class="button"> معلومات المستخدم </a>
       
        <a href="logout.php" class="button">تسجيل الخروج</a>
    </div>
</body>
</html>