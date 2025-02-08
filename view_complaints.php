<?php
session_start(); // بدء الجلسة

// تحقق من تسجيل الدخول كمعلم
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'teacher' && $_SESSION['role'] !== 'admin')) {
    header("Location: login.php"); // إعادة التوجيه إلى صفحة تسجيل الدخول
    exit;
}

// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "OnlineExamDB");

// تحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// استعلام لاسترجاع التظلمات الخاصة بالامتحانات التي أنشأها المعلم
$sql_complaints = "
    SELECT c.complaint_id, c.comments, c.complaint_type, c.created_at, 
           u.full_name AS student_name, e.title 
    FROM complaints c
    JOIN Users u ON c.user_id = u.id
    JOIN Exams e ON c.exam_id = e.exam_id
    WHERE e.teacher_id = ? 
    ORDER BY c.created_at DESC";

$stmt = $conn->prepare($sql_complaints);
$stmt->bind_param("i", $_SESSION['user_id']); // ربط معرف المعلم
$stmt->execute();
$result_complaints = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>استعراض التظلمات - منصة الامتحانات الإلكترونية</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .reply-button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .reply-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .reply-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
        }
    </style>
    <script>
        function openReplyModal(complaintId) {
            document.getElementById('replyModal').style.display = 'flex';
            document.getElementById('complaintId').value = complaintId; // تعيين complaint_id
        }

        function closeReplyModal() {
            document.getElementById('replyModal').style.display = 'none';
        }

        function submitReply() {
            const complaintId = document.getElementById('complaintId').value;
            const replyComments = document.getElementById('replyComments').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'submit_reply.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                if (this.status === 200) {
                    alert(this.responseText); // عرض رسالة التأكيد
                    closeReplyModal();
                    location.reload(); // إعادة تحميل الصفحة لتحديث التظلمات
                } else {
                    alert('فشل إرسال الرد.');
                }
            };

            xhr.send('complaint_id=' + complaintId + '&reply_comments=' + encodeURIComponent(replyComments));
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>استعراض التظلمات</h1>
        <center><a href="teacher_dashboard.php" class="button">العودة</a></center>
        <table>
            <thead>
                <tr>
                    <th>اسم الطالب</th>
                    <th>اسم الامتحان</th>
                    <th>نوع التظلم</th>
                    <th>التعليق</th>
                    <th>تاريخ التظلم</th>
                    <th>الرد</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_complaints->num_rows > 0): ?>
                    <?php while ($row = $result_complaints->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['complaint_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['comments']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <button class="reply-button" onclick="openReplyModal(<?php echo $row['complaint_id']; ?>)">رد</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">لا توجد تظلمات حالياً.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="replyModal" class="reply-modal">
        <div class="reply-content">
            <h2>رد على التظلم</h2>
            <input type="hidden" id="complaintId" value="">
            <textarea id="replyComments" placeholder="اكتب ردك هنا..." rows="4" required></textarea><br>
            <button onclick="submitReply()">إرسال الرد</button>
            <button onclick="closeReplyModal()">إلغاء</button>
        </div>
    </div>

    <?php
    // تأكد من أن المتغيرات موجودة قبل محاولة إغلاقها
    if ($conn) {
        $conn->close();
    }
    ?>
</body>
</html>