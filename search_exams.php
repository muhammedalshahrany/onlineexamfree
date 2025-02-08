<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlineexamdb"; // تأكد من أن اسم قاعدة البيانات صحيح

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// الحصول على كلمة البحث
$query = isset($_GET['query']) ? $_GET['query'] : '';

// استعلام لجلب قائمة الامتحانات بناءً على كلمة البحث
$search_sql = "SELECT exam_id, title, category_id, duration, total_questions, created_at, is_available, start_time, end_time, teacher_id, is_taken FROM exams WHERE title LIKE ?";
$stmt = $conn->prepare($search_sql);
$search_param = "%" . $conn->real_escape_string($query) . "%";
$stmt->bind_param("s", $search_param);
$stmt->execute();
$result = $stmt->get_result();

// بناء الجدول بالامتحانات
if ($result->num_rows > 0) {
    while ($exam = $result->fetch_assoc()) {
        $availability = $exam['is_available'] ? '<span class="status-available">متاحة</span>' : '<span class="status-unavailable">غير متاحة</span>';
        
        // احضار اسم المعلم باستخدام teacher_id
        $teacher_sql = "SELECT full_name FROM users WHERE id = " . intval($exam['teacher_id']);
        $teacher_result = $conn->query($teacher_sql);
        $teacher_name = ($teacher_result->num_rows > 0) ? $teacher_result->fetch_assoc()['full_name'] : 'غير معروف';

        // تحديد ما إذا كان قد تم اختبار الامتحان بناءً على الحقل الجديد
        $exam_taken = $exam['is_taken'] ? 'نعم' : 'لا';

        echo '<tr>
                <td>' . htmlspecialchars($exam['exam_id']) . '</td>
                <td>' . htmlspecialchars($exam['title']) . '</td>
                <td>' . htmlspecialchars($exam['category_id']) . '</td>
                <td>' . htmlspecialchars($exam['duration']) . '</td>
                <td>' . htmlspecialchars($exam['total_questions']) . '</td>
                <td>' . htmlspecialchars($exam['created_at']) . '</td>
                <td>' . $availability . '</td>
                <td>' . htmlspecialchars($exam['start_time']) . '</td>
                <td>' . htmlspecialchars($exam['end_time']) . '</td>
                <td>' . htmlspecialchars($teacher_name) . '</td>
                <td>' . $exam_taken . '</td>
            </tr>';
    }
} else {
    echo '<tr>
            <td colspan="11">لا توجد بيانات للامتحانات.</td>
          </tr>';
}

$stmt->close();
$conn->close();
?>