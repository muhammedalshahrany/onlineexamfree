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

// استلام معرّف النتيجة والدرجة الجديدة
$result_id = isset($_POST['result_id']) ? intval($_POST['result_id']) : 0;
$score = isset($_POST['score']) ? intval($_POST['score']) : 0;

if ($result_id > 0 && $score >= 0) {
    // تحديث النتيجة في قاعدة البيانات
    $sql = "UPDATE exam_results SET score = ? WHERE result_id = ?";
    $stmt = $conn->prepare($sql);
    
    // تحقق من نجاح التحضير
    if (!$stmt) {
        die("فشل التحضير: " . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("ii", $score, $result_id);

    if ($stmt->execute()) {
        // إعادة توجيه إلى صفحة عرض النتائج بعد التحديث
        echo "تم تحديث النتيجة بنجاح.";
    } else {
        echo "فشل تحديث النتيجة: " . htmlspecialchars($stmt->error);
    }

    $stmt->close();
} else {
    echo "معرّف النتيجة أو الدرجة غير صحيح.";
}

$conn->close();
?>