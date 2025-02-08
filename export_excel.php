<?php
session_start();

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

// استلام المعايير من الطلب
$filter_exam = $conn->real_escape_string($_POST['filter_exam'] ?? '');
$filter_teacher_name = $conn->real_escape_string($_POST['filter_teacher_name'] ?? '');
$filter_date = $conn->real_escape_string($_POST['filter_date'] ?? '');

// استعلام لجلب النتائج
$results_sql = "SELECT 
                    exam_results.result_id, 
                    exam_results.user_id, 
                    exam_results.score, 
                    exam_results.total_questions, 
                    exam_results.correct_answers, 
                    exam_results.wrong_answers, 
                    exam_results.ignored_answers, 
                    exam_results.duration, 
                    exam_results.created_at, 
                    users.full_name AS student_name, 
                    exams.title,
                    users2.full_name AS teacher_name
                FROM exam_results 
                JOIN users ON exam_results.user_id = users.id 
                JOIN exams ON exam_results.exam_id = exams.exam_id
                JOIN users AS users2 ON exams.teacher_id = users2.id
                WHERE (exams.exam_id = '$filter_exam' OR '$filter_exam' = '') 
                AND (users2.full_name LIKE '%$filter_teacher_name%' OR '$filter_teacher_name' = '') 
                AND (DATE(exam_results.created_at) = '$filter_date' OR '$filter_date' = '')";

$results_result = $conn->query($results_sql);

// إعداد رأس المحتوى للتصدير
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="results.xls"');

// كتابة عنوان الأعمدة
echo "ID\tاسم الطالب\tعنوان الامتحان\tالدرجة\tإجمالي الأسئلة\tالإجابات الصحيحة\tالإجابات الخاطئة\tالإجابات المهملة\tالمدة\tتاريخ الإنشاء\tاسم المعلم\n";

// كتابة البيانات
while ($result = $results_result->fetch_assoc()) {
    echo htmlspecialchars($result['result_id']) . "\t" .
         htmlspecialchars($result['student_name']) . "\t" .
         htmlspecialchars($result['title']) . "\t" .
         htmlspecialchars($result['score']) . "\t" .
         htmlspecialchars($result['total_questions']) . "\t" .
         htmlspecialchars($result['correct_answers']) . "\t" .
         htmlspecialchars($result['wrong_answers']) . "\t" .
         htmlspecialchars($result['ignored_answers']) . "\t" .
         htmlspecialchars($result['duration']) . "\t" .
         htmlspecialchars($result['created_at']) . "\t" .
         htmlspecialchars($result['teacher_name']) . "\n";
}

$conn->close();
exit();
?>