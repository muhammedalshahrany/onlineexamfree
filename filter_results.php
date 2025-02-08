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

// استلام المعايير من الطلب
$search_name = $conn->real_escape_string($_POST['search_name'] ?? '');
$filter_exam = $conn->real_escape_string($_POST['filter_exam'] ?? '');
$filter_teacher_name = $conn->real_escape_string($_POST['filter_teacher_name'] ?? '');
$filter_date = $conn->real_escape_string($_POST['filter_date'] ?? '');

// استعلام لجلب النتائج مع الفلاتر
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
                WHERE users.full_name LIKE '%$search_name%' 
                AND (exams.exam_id = '$filter_exam' OR '$filter_exam' = '') 
                AND (users2.full_name LIKE '%$filter_teacher_name%' OR '$filter_teacher_name' = '') 
                AND (DATE(exam_results.created_at) = '$filter_date' OR '$filter_date' = '')";

$results_result = $conn->query($results_sql);

// بناء الجدول بالنتائج
$output = '';
while ($result = $results_result->fetch_assoc()) {
    $output .= '<tr id="result-' . htmlspecialchars($result['result_id']) . '">
                    <td>' . htmlspecialchars($result['result_id']) . '</td>
                    <td>' . htmlspecialchars($result['student_name']) . '</td>
                    <td>' . htmlspecialchars($result['title']) . '</td>
                    <td class="score-cell">' . htmlspecialchars($result['score']) . '</td>
                    <td>' . htmlspecialchars($result['total_questions']) . '</td>
                    <td>' . htmlspecialchars($result['correct_answers']) . '</td>
                    <td>' . htmlspecialchars($result['wrong_answers']) . '</td>
                    <td>' . htmlspecialchars($result['ignored_answers']) . '</td>
                    <td>' . htmlspecialchars($result['duration']) . '</td>
                    <td>' . htmlspecialchars($result['created_at']) . '</td>
                    <td>' . htmlspecialchars($result['teacher_name']) . '</td>
                    <td>
                        <button class="button edit-button" onclick="toggleEdit(' . htmlspecialchars($result['result_id']) . ')">تعديل</button>
                        <input type="number" id="score-input-' . htmlspecialchars($result['result_id']) . '" class="score-input hidden" value="' . htmlspecialchars($result['score']) . '">
                        <button class="button save-button hidden" onclick="saveChanges(' . htmlspecialchars($result['result_id']) . ')">حفظ</button>
                        <form method="POST" action="delete_result.php" style="display:inline;">
                            <input type="hidden" name="result_id" value="' . htmlspecialchars($result['result_id']) . '">
                            <button type="submit" name="delete_result" class="button delete" onclick="return confirm(\'هل أنت متأكد من حذف هذه النتيجة؟\');">حذف</button>
                        </form>
                    </td>
                </tr>';
}

echo $output;

$conn->close();
?>