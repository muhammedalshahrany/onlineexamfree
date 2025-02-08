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

// جلب قائمة الامتحانات
$exams_sql = "SELECT exam_id, title FROM exams";
$exams_result = $conn->query($exams_sql);

// استعلام لجلب النتائج بدون فلاتر
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
                JOIN users AS users2 ON exams.teacher_id = users2.id"; // إضافة معلم

$results_result = $conn->query($results_sql);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض النتائج</title>
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

        .button {
            padding: 5px 10px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-button {
            background-color: #007bff; /* أزرق */
        }

        .delete {
            background-color: #dc3545; /* أحمر */
        }

        .filter-form {
            margin-bottom: 20px;
        }

        .hidden {
            display: none;
        }
    </style>
    <script>
        function filterResults() {
            const searchName = document.getElementById('search_name').value;
            const filterExam = document.getElementById('filter_exam').value;
            const filterTeacherName = document.getElementById('filter_teacher_name').value;
            const filterDate = document.getElementById('filter_date').value;

            fetch('filter_results.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `search_name=${encodeURIComponent(searchName)}&filter_exam=${encodeURIComponent(filterExam)}&filter_teacher_name=${encodeURIComponent(filterTeacherName)}&filter_date=${encodeURIComponent(filterDate)}`
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('results_table').innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function toggleEdit(resultId) {
            const scoreInput = document.getElementById(`score-input-${resultId}`);
            const editButton = document.querySelector(`#result-${resultId} .edit-button`);
            const saveButton = document.querySelector(`#result-${resultId} .save-button`);

            if (scoreInput.classList.contains('hidden')) {
                scoreInput.classList.remove('hidden');
                editButton.classList.add('hidden');
                saveButton.classList.remove('hidden');
            }
        }

        function saveChanges(resultId) {
            const scoreInput = document.getElementById(`score-input-${resultId}`);
            const score = scoreInput.value;

            fetch('update_score.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `result_id=${resultId}&score=${score}`
            })
            .then(response => {
                if (response.ok) {
                    location.reload(); 
                } else {
                    alert('فشل في تحديث النتيجة.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
</head>
<body>

<h1>عرض النتائج</h1>

<form method="GET" class="filter-form">
    <input type="text" id="search_name" name="search_name" placeholder="بحث عن اسم الطالب" oninput="filterResults()">
    <select id="filter_exam" name="filter_exam" onchange="filterResults()">
        <option value="">اختر الامتحان</option>
        <?php while ($exam = $exams_result->fetch_assoc()): ?>
            <option value="<?php echo htmlspecialchars($exam['exam_id']); ?>">
                <?php echo htmlspecialchars($exam['title']); ?>
            </option>
        <?php endwhile; ?>
    </select>
    
    <input type="text" id="filter_teacher_name" name="filter_teacher_name" placeholder="بحث عن اسم المعلم" oninput="filterResults()">
    
    <input type="date" id="filter_date" name="filter_date" onchange="filterResults()">
</form>

<table id="results_table">
    <thead>
        <tr>
            <th>ID</th>
            <th>اسم الطالب</th>
            <th>عنوان الامتحان</th>
            <th>الدرجة</th>
            <th>إجمالي الأسئلة</th>
            <th>الإجابات الصحيحة</th>
            <th>الإجابات الخاطئة</th>
            <th>الإجابات المهملة</th>
            <th>المدة</th>
            <th>تاريخ الإنشاء</th>
            <th>اسم المعلم</th>
            <th>العمليات</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // بناء الجدول بالنتائج
        while ($result = $results_result->fetch_assoc()) {
            echo '<tr id="result-' . htmlspecialchars($result['result_id']) . '">
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
        ?>
    </tbody>
</table>

<!-- زر تصدير إلى Excel -->
<form action="export_excel.php" method="POST" style="margin-top: 20px;">
    <input type="hidden" name="filter_exam" value="<?php echo htmlspecialchars($filter_exam ?? ''); ?>">
    <input type="hidden" name="filter_teacher_name" value="<?php echo htmlspecialchars($filter_teacher_name ?? ''); ?>">
    <input type="hidden" name="filter_date" value="<?php echo htmlspecialchars($filter_date ?? ''); ?>">
    <button type="submit" class="button">تصدير إلى Excel</button>
</form>

</body>
</html>

<?php
$conn->close();
?>