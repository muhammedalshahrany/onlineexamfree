<?php
session_start(); // بدء الجلسة

// تحقق مما إذا كان المستخدم قد سجل الدخول كمعلم
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher' ) {
    header("Location: login.php"); // إعادة توجيه إلى صفحة تسجيل الدخول
    exit;
}

// إعدادات قاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "OnlineExamDB";

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// تحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// تعيين ترميز الأحرف إلى UTF-8
$conn->set_charset("utf8mb4");

// الحصول على امتحانات المعلم
$year_sql = "SELECT DISTINCT YEAR(created_at) AS exam_year FROM Exams WHERE teacher_id = ? ORDER BY exam_year DESC";
$stmt = $conn->prepare($year_sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$years_result = $stmt->get_result();

// الحصول على امتحانات المعلم
$exams_sql = "SELECT exam_id, title FROM Exams WHERE teacher_id = ?";
$stmt = $conn->prepare($exams_sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$exams_result = $stmt->get_result();

// معالجة عمليات التحديث
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    if ($_POST['action'] === 'update') {
        // معالجة التحديثات
        $result_id = $_POST['result_id'];
        $score = $_POST['score'];
        $student_name = $_POST['student_name'];

        $sql = "UPDATE Exam_Results er JOIN Users u ON er.user_id = u.id SET u.full_name = ?, er.score = ? WHERE er.result_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $student_name, $score, $result_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'تم التحديث بنجاح']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'خطأ في التحديث: ' . $stmt->error]);
        }
        $stmt->close();
        exit; // الخروج بعد المعالجة
    }
}

// تصدير النتائج إلى Excel
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    // الحصول على الامتحان والسنة والشهر المحددين
    $exam_id = isset($_POST['exam_id']) ? $_POST['exam_id'] : '';
    $selected_year = isset($_POST['exam_year']) ? $_POST['exam_year'] : '';
    $selected_month = isset($_POST['exam_month']) ? $_POST['exam_month'] : '';

    // استعلام لجلب النتائج
    $sql = "
        SELECT 
            er.result_id, 
            u.full_name AS student_name, 
            e.title AS exam_title, 
            er.score, 
            er.total_questions, 
            er.correct_answers, 
            er.wrong_answers, 
            er.ignored_answers, 
            e.duration AS duration,  
            er.created_at 
        FROM Exam_Results er
        JOIN Users u ON er.user_id = u.id 
        JOIN Exams e ON er.exam_id = e.exam_id
        WHERE er.exam_id = ? AND YEAR(e.created_at) = ? AND MONTH(e.created_at) = ? AND e.teacher_id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $exam_id, $selected_year, $selected_month, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    // إعداد رأس الملف
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');
    header('Content-Disposition: attachment; filename="export.xlsx"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // إضافة BOM لـ UTF-8
    echo "\xEF\xBB\xBF"; 

    // طباعة رأس الجدول
    echo "اسم الطالب\tاسم الامتحان\tالدرجة\tإجمالي الأسئلة\tالإجابات الصحيحة\tالإجابات الخاطئة\tالإجابات المتروكة\tالمدة\tتاريخ الامتحان\n";

    // إذا لم تكن هناك نتائج، قم بطباعة صفوف فارغة
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo implode("\t", [
                htmlspecialchars($row['student_name'] ?? ''), // استخدام ?? لضمان وجود قيمة
                htmlspecialchars($row['exam_title'] ?? ''),
                htmlspecialchars($row['score'] ?? ''),
                htmlspecialchars($row['total_questions'] ?? ''),
                htmlspecialchars($row['correct_answers'] ?? ''),
                htmlspecialchars($row['wrong_answers'] ?? ''),
                htmlspecialchars($row['ignored_answers'] ?? ''),
                htmlspecialchars($row['duration'] ?? ''),
                htmlspecialchars($row['created_at'] ?? '')
            ]) . "\n";
        }
    } else {
        // طباعة صفوف فارغة إذا لم تكن هناك نتائج
        echo "\t\t\t\t\t\t\t\t\n"; // 9 أعمدة فارغة
    }
    exit; // الخروج بعد المعالجة
}

// استرجاع نتائج الطلاب بناءً على الامتحان المحدد
$exam_id = isset($_POST['exam_id']) ? $_POST['exam_id'] : '';
$selected_year = isset($_POST['exam_year']) ? $_POST['exam_year'] : '';
$selected_month = isset($_POST['exam_month']) ? $_POST['exam_month'] : '';

// استعلام للنتائج
$sql = "
    SELECT 
        er.result_id, 
        u.full_name AS student_name, 
        e.title AS exam_title, 
        er.score, 
        er.total_questions, 
        er.correct_answers, 
        er.wrong_answers, 
        er.ignored_answers, 
        e.duration AS duration,  
        er.created_at 
    FROM Exam_Results er
    JOIN Users u ON er.user_id = u.id 
    JOIN Exams e ON er.exam_id = e.exam_id
    WHERE er.exam_id = ? AND YEAR(e.created_at) = ? AND MONTH(e.created_at) = ? AND e.teacher_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $exam_id, $selected_year, $selected_month, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$conn->close();

$message = '';
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نتائج الطلاب</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to left, #6a11cb, #2575fc);
            color: white;
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
            font-size: 1.5em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 0.9em;
        }
        th, td {
            padding: 5px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        .button {
            padding: 4px 8px;
            font-size: 0.9em;
        }
        .edit-form {
            display: none;
            margin-top: 10px;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 10px;
            border-radius: 5px;
            text-align: left;
        }
        .form-group {
            display: flex;
            align-items: center;
            margin: 5px 0;
        }
        label {
            flex: 1; /* توسيع التوضيح ليكون في اليسار */
            font-weight: bold;
            text-align: right;
            margin-right: 10px; /* مسافة بين التوضيح والحقل */
        }
        input[type="text"],
        input[type="number"],
        select {
            flex: 2; /* توسيع الحقول لتكون في اليمين */
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #007BFF;
            font-size: 0.9em;
        }
    </style>
    <script>
        function confirmDelete(resultId) {
            if (confirm("هل أنت متأكد أنك تريد حذف هذه النتيجة؟")) {
                fetch(`?action=delete&id=${resultId}`)
                    .then(response => response.json())
                    .then(data => {
                        displayMessage(data);
                        document.getElementById(`row-${resultId}`).remove(); // إزالة الصف من الجدول
                    });
            }
        }

        function showEditForm(resultId) {
            const form = document.getElementById('edit-form-' + resultId);
            form.style.display = (form.style.display === 'block') ? 'none' : 'block';
        }

        function updateResult(resultId) {
            const studentName = document.querySelector(`#edit-form-${resultId} input[name='student_name']`).value;
            const score = document.querySelector(`#edit-form-${resultId} input[name='score']`).value;

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update&result_id=${resultId}&student_name=${studentName}&score=${score}`
            })
            .then(response => response.json())
            .then(data => {
                displayMessage(data);
                // تحديث المعلومات في الجدول
                const row = document.getElementById(`row-${resultId}`);
                row.querySelector('td:nth-child(1)').innerText = studentName;
                row.querySelector('td:nth-child(3)').innerText = score;
            });
        }

        function displayMessage(data) {
            const messageDiv = document.getElementById('message');
            messageDiv.className = data.status === 'success' ? 'message success' : 'message error';
            messageDiv.innerText = data.message;
            messageDiv.style.display = 'block';
            setTimeout(() => {
                messageDiv.style.display = 'none'; // إخفاء الرسالة بعد ثلاث ثوانٍ
            }, 3000);
        }

        function filterResults() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const studentName = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                if (studentName.includes(input)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</head>
<body>

<div class="container">
    <a href="teacher_exam_management.php" class="button" style="text-decoration: none;">العودة</a> <!-- زر العودة -->

    <div id="message" class="message"></div> <!-- رسالة النجاح أو الفشل -->

    <h1>نتائج الطلاب</h1>

    <form method="POST" action="">
        <div class="form-group">
            <label>اختر السنة:</label>
            <select name="exam_year" required>
                <option value="">اختر السنة</option>
                <?php while ($year = $years_result->fetch_assoc()): ?>
                    <option value="<?php echo $year['exam_year']; ?>" <?php echo ($selected_year == $year['exam_year']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($year['exam_year']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>اختر الشهر:</label>
            <select name="exam_month" required>
                <option value="">اختر الشهر</option>
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?php echo $m; ?>" <?php echo ($selected_month == $m) ? 'selected' : ''; ?>>
                        <?php echo $m; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="form-group">
            <label>اختر الامتحان:</label>
            <select name="exam_id" required>
                <option value="">اختر الامتحان</option>
                <?php while ($exam = $exams_result->fetch_assoc()): ?>
                    <option value="<?php echo $exam['exam_id']; ?>" <?php echo ($exam_id == $exam['exam_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($exam['title']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <button type="submit" class="button">استرجاع النتائج</button>
    </form>

    <a href="?action=export" class="button">تصدير إلى Excel</a> <!-- زر تصدير النتائج إلى Excel -->

    <input type="text" id="searchInput" placeholder="ابحث عن اسم الطالب..." onkeyup="filterResults()" style="width: calc(100% - 22px); padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #007BFF; font-size: 1em;">

    <table>
        <thead>
            <tr>
                <th>اسم الطالب</th>
                <th>اسم الامتحان</th>
                <th>الدرجة</th>
                <th>إجمالي الأسئلة</th>
                <th>الإجابات الصحيحة</th>
                <th>الإجابات الخاطئة</th>
                <th>الإجابات المتروكة</th>
                <th>المدة</th>
                <th>تاريخ الامتحان</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr id="row-<?php echo $row['result_id']; ?>">
                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['exam_title']); ?></td>
                        <td><?php echo htmlspecialchars($row['score']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_questions']); ?></td>
                        <td><?php echo htmlspecialchars($row['correct_answers']); ?></td>
                        <td><?php echo htmlspecialchars($row['wrong_answers']); ?></td>
                        <td><?php echo htmlspecialchars($row['ignored_answers']); ?></td>
                        <td><?php echo htmlspecialchars($row['duration']); ?> دقيقة</td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td>
                            <button class="button" onclick="showEditForm(<?php echo $row['result_id']; ?>)">تعديل</button>
                            <button class="button" onclick="confirmDelete(<?php echo $row['result_id']; ?>)">حذف</button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="10">
                            <div class="edit-form" id="edit-form-<?php echo $row['result_id']; ?>">
                                <h3>تعديل النتيجة</h3>
                                <form action="" method="POST">
                                    <input type="hidden" name="result_id" value="<?php echo $row['result_id']; ?>">
                                    <input type="hidden" name="action" value="update">
                                    <div class="form-group">
                                        <label>اسم الطالب:</label>
                                        <input type="text" name="student_name" value="<?php echo htmlspecialchars($row['student_name']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>الدرجة:</label>
                                        <input type="number" name="score" value="<?php echo htmlspecialchars($row['score']); ?>" required>
                                    </div>
                                    <button type="button" class="button" onclick="updateResult(<?php echo $row['result_id']; ?>)">حفظ التعديلات</button>
                                    <button type="button" class="button" onclick="showEditForm(<?php echo $row['result_id']; ?>)">إلغاء</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10">لا توجد نتائج للطلاب.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>