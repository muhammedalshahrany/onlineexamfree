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
$exam_id = $_GET['exam_id'];

// استعلام لاسترجاع التظلمات الخاصة بالامتحان
$sql_complaints = "
    SELECT c.complaint_id, c.comments, c.created_at, u.full_name AS student_name
    FROM complaints c
    JOIN Users u ON c.user_id = u.id
    WHERE c.exam_id = ? 
    ORDER BY c.created_at DESC";

$stmt = $conn->prepare($sql_complaints);
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$result_complaints = $stmt->get_result();

if ($result_complaints->num_rows > 0) {
    while ($row = $result_complaints->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['student_name']) . "</td>
                <td>" . htmlspecialchars($row['comments']) . "</td>
                <td>" . htmlspecialchars($row['created_at']) . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='3'>لا توجد تظلمات لهذا الامتحان.</td></tr>";
}

$conn->close();
?>