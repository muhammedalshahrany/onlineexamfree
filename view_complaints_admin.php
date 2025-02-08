<?php
session_start();

// تحقق من تسجيل الدخول كمعلم أو إداري
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

// استعلام لجلب جميع الامتحانات
$sql_exams = "SELECT exam_id, title FROM Exams";
$result_exams = $conn->query($sql_exams);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>استعراض التظلمات</title>
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
        .delete-button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
        }
    </style>
    <script>
        function loadComplaints(examId) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'load_complaints.php?exam_id=' + examId, true);
            xhr.onload = function() {
                if (this.status === 200) {
                    document.getElementById('complaintsTable').innerHTML = this.responseText;
                } else {
                    alert('فشل تحميل التظلمات.');
                }
            };
            xhr.send();
        }

        function deleteAllComplaints(examId) {
            if (confirm("هل أنت متأكد أنك تريد حذف جميع التظلمات لهذا الامتحان؟")) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'delete_complaints.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (this.status === 200) {
                        alert(this.responseText);
                        loadComplaints(examId); // تحديث الجدول بعد الحذف
                    } else {
                        alert('فشل حذف التظلمات.');
                    }
                };
                xhr.send('exam_id=' + examId);
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const firstExamId = document.getElementById('examSelect').value;
            loadComplaints(firstExamId); // تحميل التظلمات عند تحميل الصفحة
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>استعراض التظلمات</h1>
        
        <label for="examSelect">اختر الامتحان:</label>
        <select id="examSelect" onchange="loadComplaints(this.value)">
            <option value="">اختر امتحان</option>
            <?php if ($result_exams->num_rows > 0): ?>
                <?php while ($row = $result_exams->fetch_assoc()): ?>
                    <option value="<?php echo $row['exam_id']; ?>"><?php echo htmlspecialchars($row['title']); ?></option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>

        <button class="delete-button" onclick="deleteAllComplaints(document.getElementById('examSelect').value)">حذف جميع التظلمات</button>

        <table>
            <thead>
                <tr>
                    <th>اسم الطالب</th>
                    <th>التعليق</th>
                    <th>تاريخ التظلم</th>
                </tr>
            </thead>
            <tbody id="complaintsTable">
                <!-- سيتم تحميل التظلمات هنا -->
            </tbody>
        </table>
    </div>

    <?php
    $conn->close();
    ?>
</body>
</html>