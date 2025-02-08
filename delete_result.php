<?php
session_start();

// تحقق مما إذا كان المستخدم قد سجل الدخول كمسؤول
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// إعداد قاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlineexamdb"; // تأكد من أن اسم قاعدة البيانات صحيح

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// استلام معرّف النتيجة
$result_id = isset($_POST['result_id']) ? intval($_POST['result_id']) : 0;

if ($result_id > 0) {
    // حذف النتيجة من قاعدة البيانات
    $sql = "DELETE FROM exam_results WHERE result_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $result_id);

    if ($stmt->execute()) {
        // إعادة توجيه إلى صفحة عرض النتائج بعد الحذف
        header("Location: view_results_admin.php"); // إعادة التوجيه إلى صفحة عرض النتائج
        exit;
    } else {
        echo "فشل حذف النتيجة: " . htmlspecialchars($stmt->error);
    }

    $stmt->close();
} else {
    echo "معرّف النتيجة غير صحيح.";
}

$conn->close();
?>