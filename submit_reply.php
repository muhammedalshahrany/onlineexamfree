<?php
session_start();

// تحقق من تسجيل الدخول كمعلم
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'teacher') {
    exit('يجب تسجيل الدخول.');
}

// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "OnlineExamDB");
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// الحصول على البيانات من الطلب
$complaint_id = $_POST['complaint_id'];
$reply_comments = $_POST['reply_comments'];

// إدخال الرد في قاعدة البيانات
$sql_insert = "INSERT INTO complaint_replies (complaint_id, reply_comments, teacher_id) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql_insert);
$stmt->bind_param("isi", $complaint_id, $reply_comments, $_SESSION['user_id']);

if ($stmt->execute()) {
    // إدخال إشعار للطالب
    $sql_notification = "INSERT INTO notifications (user_id, complaint_id, message) VALUES (?, ?, ?)";
    $stmt_notification = $conn->prepare($sql_notification);
    
    // احصل على user_id الطالب من التظلم
    $stmt_get_user = $conn->prepare("SELECT user_id FROM complaints WHERE complaint_id = ?");
    $stmt_get_user->bind_param("i", $complaint_id);
    $stmt_get_user->execute();
    $stmt_get_user->bind_result($student_id);
    $stmt_get_user->fetch();
    $stmt_get_user->close();

    $message = "لقد تم الرد على تظلمك.";
    $stmt_notification->bind_param("iis", $student_id, $complaint_id, $message);
    $stmt_notification->execute();

    echo "تم إرسال الرد بنجاح!";
} else {
    echo "فشل إرسال الرد: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>