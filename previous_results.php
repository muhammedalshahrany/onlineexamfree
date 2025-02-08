<?php
session_start();

// تحقق من تسجيل الدخول
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "OnlineExamDB");

// تحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// استعلام لاسترجاع جميع نتائج الامتحانات السابقة للطالب
$sql_results = "
    SELECT er.result_id, e.title, er.score, 
           e.total_questions AS total_exam_questions, 
           e.duration AS total_exam_duration,
           er.correct_answers, er.wrong_answers, 
           er.total_questions AS answered_questions, 
           er.duration AS duration_per_question,
           er.created_at,
           er.exam_id
    FROM exam_results er
    JOIN Exams e ON er.exam_id = e.exam_id
    WHERE er.user_id = ?
    ORDER BY er.created_at DESC";

$stmt = $conn->prepare($sql_results);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result_results = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نتائج الامتحانات السابقة - منصة الامتحانات الإلكترونية</title>
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
        .back-button {
            margin-top: 20px;
            display: block;
            text-align: center;
            font-size: 16px;
        }
        .complaint-button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .complaint-modal {
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
        .complaint-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
        }
    </style>
    <script>
        function openComplaintModal(examId) {
            document.getElementById('complaintModal').style.display = 'flex';
            document.getElementById('examId').value = examId; // تعيين exam_id
        }

        function closeComplaintModal() {
            document.getElementById('complaintModal').style.display = 'none';
        }

        function submitComplaint() {
            const examId = document.getElementById('examId').value;
            const complaintType = document.querySelector('input[name="complaintType"]:checked');
            const comments = document.getElementById('comments').value;

            // تحقق من اختيار نوع التظلم
            if (!complaintType) {
                alert("يرجى اختيار نوع التظلم.");
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'submit_complaint.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                if (this.status === 200) {
                    alert(this.responseText); // عرض رسالة التأكيد
                    closeComplaintModal();
                } else {
                    alert('فشل إرسال التظلم.');
                }
            };

            // تأكد من تمرير جميع البيانات بشكل صحيح
            xhr.send('exam_id=' + examId + '&complaint_type=' + complaintType.value + '&comments=' + encodeURIComponent(comments));
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>نتائج الامتحانات السابقة</h1>

        <table>
            <thead>
                <tr>
                    <th>اسم الامتحان</th>
                    <th>الدرجة</th>
                    <th>عدد الأسئلة الكاملة</th>
                    <th>المدة الكاملة للاختبار (دقائق)</th>
                    <th>عدد الأسئلة المجاب عليها</th>
                    <th>الإجابات الصحيحة</th>
                    <th>الإجابات الخاطئة</th>
                    <th>المدة لكل سؤال (دقائق)</th>
                    <th>تاريخ الامتحان</th>
                    <th>رفع تظلم</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($result_results) && $result_results->num_rows > 0): ?>
                    <?php while ($row = $result_results->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['score']); ?></td>
                            <td><?php echo htmlspecialchars($row['total_exam_questions']); ?></td>
                            <td><?php echo htmlspecialchars($row['total_exam_duration']); ?></td> 
                            <td><?php echo htmlspecialchars($row['answered_questions']); ?></td>
                            <td><?php echo htmlspecialchars($row['correct_answers']); ?></td>
                            <td><?php echo htmlspecialchars($row['wrong_answers']); ?></td>
                            <td><?php echo htmlspecialchars($row['duration_per_question']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <button class="complaint-button" onclick="openComplaintModal(<?php echo $row['exam_id']; ?>)">رفع تظلم</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10">لا توجد نتائج امتحانات سابقة.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="student_page.php" class="back-button">العودة إلى لوحة التحكم</a>
    </div>

    <div id="complaintModal" class="complaint-modal">
        <div class="complaint-content">
            <h2>رفع تظلم</h2>
            <input type="hidden" id="examId" value="">
            <label><input type="radio" name="complaintType" value="درجات" required> درجات</label><br>
            <label><input type="radio" name="complaintType" value="سؤال محدد" required> سؤال محدد</label><br>
            <label><input type="radio" name="complaintType" value="الامتحان كامل" required> امتحان كامل</label><br>
            <textarea id="comments" placeholder="اكتب تعليقك هنا..." rows="4" required></textarea><br>
            <button onclick="submitComplaint()">إرسال التظلم</button>
            <button onclick="closeComplaintModal()">إلغاء</button>
        </div>
    </div>

    <?php
    // تأكد من أن المتغيرات موجودة قبل محاولة إغلاقها
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
    ?>
</body>
</html>