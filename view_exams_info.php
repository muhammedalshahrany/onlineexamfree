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
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حالة الامتحانات</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        .status-available {
            color: green;
        }

        .status-unavailable {
            color: red;
        }

        #search {
            margin-bottom: 20px;
            padding: 10px;
            width: 100%;
            max-width: 600px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .button {
            display: inline-block;
            padding: 10px 15px;
            margin-top: 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function searchExams() {
            const query = document.getElementById('search').value;
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'search_exams.php?query=' + encodeURIComponent(query), true);
            xhr.onload = function() {
                if (this.status === 200) {
                    document.getElementById('examTableBody').innerHTML = this.responseText;
                }
            };
            xhr.send();
        }
    </script>
</head>
<body>

<h1>حالة الامتحانات</h1>

<input type="text" id="search" placeholder="ابحث عن عنوان الامتحان..." onkeyup="searchExams()">
<button onclick="window.location.href='export_exams.php'" class="button">تصدير إلى Excel</button>

<table>
    <thead>
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
        </tr>
    </thead>
    <tbody id="examTableBody">
        <?php
        // بناء الجدول بالامتحانات
        if ($exams_result->num_rows > 0) {
            while ($exam = $exams_result->fetch_assoc()) {
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
        ?>
    </tbody>
</table>

</body>
</html>

<?php
$conn->close();
?>