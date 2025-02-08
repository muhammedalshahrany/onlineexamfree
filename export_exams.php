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

// استعلام لجلب قائمة الامتحانات
$exams_sql = "SELECT exam_id, title, category_id, duration, total_questions, created_at, is_available, start_time, end_time, teacher_id, is_taken FROM exams";
$exams_result = $conn->query($exams_sql);

// إعداد ملف Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="exams_report.xls"');

// إنشاء الجدول في ملف Excel
echo "<table border='1'>
<tr>
    <th>ID</th>
    <th>العنوان</th>
    <th>الفئة</th>
    <th>المدة (دقائق)</th>
    <th>إجمالي الأسئلة</th>
    <th>تاريخ الإنشاء</th>
    <th>الحالة</th>
    <th>وقت البدء</th>
    <th>وقت الانتهاء</th>
    <th>معلم</th>
    <th>تم الاختبار؟</th>
</tr>";

if ($exams_result->num_rows > 0) {
    while ($exam = $exams_result->fetch_assoc()) {
        $availability = $exam['is_available'] ? 'متاحة' : 'غير متاحة';
        
        // احضار اسم المعلم باستخدام teacher_id
        $teacher_sql = "SELECT full_name FROM users WHERE id = " . intval($exam['teacher_id']);
        $teacher_result = $conn->query($teacher_sql);
        $teacher_name = ($teacher_result->num_rows > 0) ? $teacher_result->fetch_assoc()['full_name'] : 'غير معروف';

        // تحديد ما إذا كان قد تم اختبار الامتحان بناءً على الحقل الجديد
        $exam_taken = $exam['is_taken'] ? 'نعم' : 'لا';

        // كتابة الصفوف في ملف Excel
        echo "<tr>
                <td>" . htmlspecialchars($exam['exam_id']) . "</td>
                <td>" . htmlspecialchars($exam['title']) . "</td>
                <td>" . htmlspecialchars($exam['category_id']) . "</td>
                <td>" . htmlspecialchars($exam['duration']) . "</td>
                <td>" . htmlspecialchars($exam['total_questions']) . "</td>
                <td>" . htmlspecialchars($exam['created_at']) . "</td>
                <td>" . $availability . "</td>
                <td>" . htmlspecialchars($exam['start_time']) . "</td>
                <td>" . htmlspecialchars($exam['end_time']) . "</td>
                <td>" . htmlspecialchars($teacher_name) . "</td>
                <td>" . $exam_taken . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='11'>لا توجد بيانات للامتحانات.</td></tr>";
}

echo "</table>";

$conn->close();
?>