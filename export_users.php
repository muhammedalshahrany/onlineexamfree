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

// استعلام لجلب المستخدمين
$users_sql = "SELECT * FROM Users";
$users_result = $conn->query($users_sql);

// إعداد رأس الملف للتصدير
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');
header('Content-Disposition: attachment; filename="export.xlsx"');
header('Pragma: no-cache');
header('Expires: 0');

// إضافة BOM لـ UTF-8
echo "\xEF\xBB\xBF"; 

// طباعة رأس الجدول
echo "ID\tالاسم الكامل\tاسم المستخدم\tالبريد الإلكتروني\tالدور\tتاريخ الإنشاء\tالهاتف\tالجنس\tالحالة\n";

// إذا لم تكن هناك نتائج، قم بطباعة صفوف فارغة
if ($users_result->num_rows > 0) {
    while ($row = $users_result->fetch_assoc()) {
        echo implode("\t", [
            htmlspecialchars($row['id'] ?? ''), 
            htmlspecialchars($row['full_name'] ?? ''),
            htmlspecialchars($row['username'] ?? ''),
            htmlspecialchars($row['email'] ?? ''),
            htmlspecialchars($row['role'] ?? ''),
            htmlspecialchars($row['created_at'] ?? ''),
            htmlspecialchars($row['phone'] ?? ''),
            htmlspecialchars($row['gender'] ?? ''),
            htmlspecialchars($row['status'] ?? '')
        ]) . "\n";
    }
} else {
    // طباعة صفوف فارغة إذا لم تكن هناك نتائج
    echo "\t\t\t\t\t\t\t\t\n"; // 9 أعمدة فارغة
}

$conn->close();
exit; // الخروج بعد المعالجة
?>