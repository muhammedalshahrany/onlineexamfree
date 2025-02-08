<?php
session_start();

// تحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// إعداد قاعدة البيانات
$servername = "localhost";
$username = "root"; // يجب تعديلها إذا كان لديك اسم مستخدم آخر
$password = ""; // كلمة المرور
$dbname = "OnlineExamDB"; // اسم قاعدة البيانات

// إنشاء اتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// معالجة عملية إرسال التقييم
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $comment = $_POST['rating']; // نص التعليق
    $heart_count = $_POST['heart_count']; // عدد ضغطات القلب
    $like_count = $_POST['like_count']; // عدد ضغطات الإعجاب

    // إدراج البيانات في جدول التقييمات
    $sql = "INSERT INTO ratings (user_id, comment, heart_count, like_count) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isii", $user_id, $comment, $heart_count, $like_count);

    if ($stmt->execute()) {
        echo "تم إرسال التقييم بنجاح!";
    } else {
        echo "خطأ في إدخال البيانات: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close(); // إغلاق الاتصال
?>