<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlineexamdb"; // تأكد من أن اسم قاعدة البيانات صحيح

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// استلام ID المعلم من الطلب
$teacher_id = $conn->real_escape_string($_POST['teacher_id'] ?? '');

// استعلام لحذف المعلم
$delete_sql = "DELETE FROM users WHERE id = '$teacher_id' AND role = 'teacher'";
if ($conn->query($delete_sql) === TRUE) {
    echo "تم الحذف بنجاح";
} else {
    echo "فشل في الحذف: " . $conn->error;
}

$conn->close();
?>