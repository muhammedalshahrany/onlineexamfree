<?php
session_start();

// تحقق مما إذا كان المستخدم قد سجل الدخول كمسؤول
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // إعادة توجيه إلى صفحة تسجيل الدخول
    exit;
}

// إعدادات قاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "OnlineExamDB";

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// تحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// استعلامات لجلب الإحصائيات
$students_count_sql = "SELECT COUNT(*) as count FROM Users WHERE role = 'student'";
$teachers_count_sql = "SELECT COUNT(*) as count FROM Users WHERE role = 'teacher'";
$exams_count_sql = "SELECT COUNT(*) as count FROM Exams";
$complaints_count_sql = "SELECT COUNT(*) as count FROM Complaints";

$students_count = $conn->query($students_count_sql)->fetch_assoc();
$teachers_count = $conn->query($teachers_count_sql)->fetch_assoc();
$exams_count = $conn->query($exams_count_sql)->fetch_assoc();
$complaints_count = $conn->query($complaints_count_sql)->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم المسؤول</title>
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
            margin: 40px auto;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
            font-size: 2em;
        }
        .statistics {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
        }
        .stat-card {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            width: 180px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }
        .stat-card h2 {
            margin: 0;
            font-size: 1.5em;
        }
        .stat-card p {
            font-size: 1.2em;
        }
        .buttons {
            margin-top: 30px;
        }
        .button {
            padding: 10px 20px;
            font-size: 1em;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            margin: 5px;
            display: inline-block;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>لوحة تحكم المسؤول</h1>

    <div class="statistics">
        <div class="stat-card">
            <h2>عدد الطلاب</h2>
            <p><?php echo htmlspecialchars($students_count['count']); ?></p>
        </div>
        <div class="stat-card">
            <h2>عدد المعلمين</h2>
            <p><?php echo htmlspecialchars($teachers_count['count']); ?></p>
        </div>
        <div class="stat-card">
            <h2>عدد الامتحانات</h2>
            <p><?php echo htmlspecialchars($exams_count['count']); ?></p>
        </div>
        <div class="stat-card">
            <h2>عدد التظلمات</h2>
            <p><?php echo htmlspecialchars($complaints_count['count']); ?></p>
        </div>
    </div>

    <div class="buttons">
        <h2>التحكم في النظام</h2>
        <a href="manage_users.php" class="button">إدارة المستخدمين</a>
        <a href="add_exams_admin.php" class="button">إنشاء امتحان جديد</a>
        <a href="manage_exams_admin.php" class="button">عرض الامتحانات</a>
        <a href="view_results_admin.php" class="button">عرض نتائج الطلاب</a>
        <a href="display_complaints.php" class="button">عرض التظلمات</a>
        <a href="view_complaints_admin.php" class="button">التظلمات الخاصة بامتحاناتك</a>
        <a href="manage_roles.php" class="button">ادارة الادوار </a>
        <a href="view_results_admin.php" class="button">تقارير نتائج الطلاب</a>
        <a href="view_teachers.php" class="button">تقارير عن المعلمين</a>
        <a href="view_exams_info.php" class="button">تقارير عن الامتحانات</a>
    </div>
</div>

</body>
</html>