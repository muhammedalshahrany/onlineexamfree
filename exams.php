<?php
session_start(); // بدء الجلسة

$servername = "localhost"; // الخادم
$username = "root"; // اسم المستخدم الافتراضي في XAMPP
$password = ""; // كلمة المرور الافتراضية
$dbname = "OnlineExamDB"; // استبدل باسم قاعدة البيانات الخاصة بك

// إنشاء اتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// تأكد من وجود معرف الامتحان
if (!isset($_SESSION['exam_id'])) {
    die("معرف الامتحان غير موجود في الجلسة.");
}

// استرجاع الأسئلة من قاعدة البيانات وفقًا لمعرف الامتحان
$exam_id = $_SESSION['exam_id']; // الحصول على معرف الامتحان من الجلسة
$sql = "SELECT question_text, options, correct_answer FROM Questions WHERE exam_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
}
$stmt->close();
$conn->close(); // أغلق الاتصال بعد الانتهاء
?>