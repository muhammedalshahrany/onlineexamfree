<?php
session_start();

// تحقق من تسجيل الدخول كمعلم أو إداري
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'teacher' && $_SESSION['role'] !== 'admin')) {
    http_response_code(403);
    echo "غير مسموح لك بالوصول.";
    exit;
}

// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "OnlineExamDB");

// تحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// معرف الامتحان
$exam_id = $_POST['exam_id'];

// استعلام لحذف جميع ردود التظلمات المرتبطة بهذا الامتحان
$sql_delete_replies = "DELETE FROM complaint_replies WHERE complaint_id IN (SELECT complaint_id FROM complaints WHERE exam_id = ?)";
$stmt_delete_replies = $conn->prepare($sql_delete_replies);
$stmt_delete_replies->bind_param("i", $exam_id);
$stmt_delete_replies->execute();

// استعلام لحذف جميع الإشعارات المرتبطة بالتظلمات
$sql_delete_notifications = "DELETE FROM notifications WHERE complaint_id IN (SELECT complaint_id FROM complaints WHERE exam_id = ?)";
$stmt_delete_notifications = $conn->prepare($sql_delete_notifications);
$stmt_delete_notifications->bind_param("i", $exam_id);
$stmt_delete_notifications->execute();

// استعلام لحذف جميع التظلمات الخاصة بالامتحان
$sql_delete = "DELETE FROM complaints WHERE exam_id = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("i", $exam_id);

if ($stmt_delete->execute()) {
    echo "تم حذف جميع التظلمات بنجاح.";
} else {
    echo "فشل حذف التظلمات: " . $conn->error;
}

$conn->close();
?>