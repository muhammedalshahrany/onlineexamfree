<?php
session_start();

// تحقق مما إذا كان المستخدم قد سجل الدخول كمسؤول
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// إعدادات قاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlineexamdb"; // تأكد من أن اسم قاعدة البيانات صحيح

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// تحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// استعلام لجلب قائمة المعلمين مع اسم المستخدم
$teachers_sql = "SELECT id, full_name, username, email, created_at FROM users WHERE role = 'teacher'";
$teachers_result = $conn->query($teachers_sql);

// إعداد رأس الملف للتصدير
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');
header('Content-Disposition: attachment; filename="teachers_report.xlsx"');
header('Pragma: no-cache');
header('Expires: 0');

// إضافة BOM لـ UTF-8
echo "\xEF\xBB\xBF"; 

// طباعة رأس الجدول
echo "ID\tالاسم الكامل\tاسم المستخدم\tالبريد الإلكتروني\tتاريخ الإنشاء\n";

// إذا لم تكن هناك نتائج، قم بطباعة صفوف فارغة
if ($teachers_result->num_rows > 0) {
    while ($row = $teachers_result->fetch_assoc()) {
        echo implode("\t", [
            htmlspecialchars($row['id'] ?? ''), 
            htmlspecialchars($row['full_name'] ?? ''),
            htmlspecialchars($row['username'] ?? ''), // إضافة اسم المستخدم
            htmlspecialchars($row['email'] ?? ''),
            htmlspecialchars($row['created_at'] ?? '')
        ]) . "\n";
    }
} else {
    // طباعة صفوف فارغة إذا لم تكن هناك نتائج
    echo "\t\t\t\t\n"; // 4 أعمدة فارغة
}

$conn->close();
exit; // الخروج بعد المعالجة
?>