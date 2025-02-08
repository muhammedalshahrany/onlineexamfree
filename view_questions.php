<?php
session_start(); // بدء الجلسة

// تحقق من تسجيل الدخول كمعلم
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php"); // إعادة التوجيه إلى صفحة تسجيل الدخول
    exit;
}

// إعداد قاعدة البيانات
$conn = new mysqli("localhost", "root", "", "OnlineExamDB");

// تحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// استرجاع exam_id من الرابط
$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;

// معالجة حذف السؤال
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $question_id = isset($_POST['question_id']) ? intval($_POST['question_id']) : 0;
    if ($question_id > 0) {
        $sql = "DELETE FROM Questions WHERE question_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $question_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "تم حذف السؤال بنجاح.";
        } else {
            $_SESSION['message'] = "خطأ في حذف السؤال: " . $stmt->error;
        }
        $stmt->close();
    }
}

// معالجة تحديث السؤال
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'update') {
    $question_id = isset($_POST['question_id']) ? intval($_POST['question_id']) : 0;
    $question_text = $_POST['question_text'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_answer = $_POST['correct_answer'];

    if ($question_id > 0) {
        $sql = "UPDATE Questions SET question_text = ?, options = ?, correct_answer = ? WHERE question_id = ?";
        $options = json_encode(['a' => $option_a, 'b' => $option_b, 'c' => $option_c, 'd' => $option_d]);
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $question_text, $options, $correct_answer, $question_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "تم تعديل السؤال بنجاح.";
        } else {
            $_SESSION['message'] = "خطأ في تعديل السؤال: " . $stmt->error;
        }
        $stmt->close();
    }
}

// استعلام لجلب الأسئلة الخاصة بالامتحان المحدد
$sql = "SELECT q.question_id, q.question_text, q.options, q.correct_answer, e.title AS exam_title
        FROM Questions q
        JOIN Exams e ON q.exam_id = e.exam_id
        WHERE q.exam_id = ? AND e.teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $exam_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['options'] = json_decode($row['options'], true);
        $questions[] = $row;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض الأسئلة</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .button {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .button-danger {
            background-color: #dc3545;
        }
        .button:hover {
            opacity: 0.9;
        }
        .edit-form {
            display: none;
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .edit-form label {
            display: block;
            margin: 10px 0 5px;
        }
        .edit-form input, .edit-form select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .edit-form button {
            width: 100%;
        }
        .message {
            background: #28a745; 
            color: white; 
            padding: 10px; 
            border-radius: 5px; 
            margin-bottom: 20px; 
            display: none; /* إخفاء الرسالة في البداية */
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>عرض الأسئلة</h1>

        <div class="message" id="message"></div> <!-- رسالة النجاح -->

        <table id="questionsTable">
            <thead>
                <tr>
                    <th>نص السؤال</th>
                    <th>الخيار (أ)</th>
                    <th>الخيار (ب)</th>
                    <th>الخيار (ج)</th>
                    <th>الخيار (د)</th>
                    <th>الإجابة الصحيحة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($questions)): ?>
                    <tr>
                        <td colspan="7">لا توجد أسئلة لعرضها.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($questions as $row): ?>
                        <tr id="question-<?php echo $row['question_id']; ?>">
                            <td><?php echo htmlspecialchars($row['question_text']); ?></td>
                            <td><?php echo htmlspecialchars($row['options']['a']); ?></td>
                            <td><?php echo htmlspecialchars($row['options']['b']); ?></td>
                            <td><?php echo htmlspecialchars($row['options']['c']); ?></td>
                            <td><?php echo htmlspecialchars($row['options']['d']); ?></td>
                            <td><?php echo htmlspecialchars($row['correct_answer']); ?></td>
                            <td>
                                <div class="actions">
                                    <button class="button" onclick="showEditForm(<?php echo $row['question_id']; ?>)">تعديل</button>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="question_id" value="<?php echo $row['question_id']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="button button-danger" onclick="return confirm('هل أنت متأكد أنك تريد حذف هذا السؤال؟')">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr class="edit-form" id="edit-form-<?php echo $row['question_id']; ?>">
                            <td colspan="7">
                                <form method="POST" action="">
                                    <input type="hidden" name="question_id" value="<?php echo $row['question_id']; ?>">
                                    <input type="hidden" name="action" value="update">
                                    <label>نص السؤال:</label>
                                    <input type="text" name="question_text" value="<?php echo htmlspecialchars($row['question_text']); ?>" required>
                                    <label>الخيار (أ):</label>
                                    <input type="text" name="option_a" value="<?php echo htmlspecialchars($row['options']['a']); ?>" required>
                                    <label>الخيار (ب):</label>
                                    <input type="text" name="option_b" value="<?php echo htmlspecialchars($row['options']['b']); ?>" required>
                                    <label>الخيار (ج):</label>
                                    <input type="text" name="option_c" value="<?php echo htmlspecialchars($row['options']['c']); ?>" required>
                                    <label>الخيار (د):</label>
                                    <input type="text" name="option_d" value="<?php echo htmlspecialchars($row['options']['d']); ?>" required>
                                    <label>الإجابة الصحيحة:</label>
                                    <select name="correct_answer" required>
                                        <option value="a" <?php echo $row['correct_answer'] == 'a' ? 'selected' : ''; ?>>أ</option>
                                        <option value="b" <?php echo $row['correct_answer'] == 'b' ? 'selected' : ''; ?>>ب</option>
                                        <option value="c" <?php echo $row['correct_answer'] == 'c' ? 'selected' : ''; ?>>ج</option>
                                        <option value="d" <?php echo $row['correct_answer'] == 'd' ? 'selected' : ''; ?>>د</option>
                                    </select>
                                    <button type="submit" class="button">حفظ التغييرات</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <center><a href="teacher_exam_management.php" class="button">العودة</a></center>
    </div>

    <script>
        function showEditForm(questionId) {
            $('#edit-form-' + questionId).toggle(); // إظهار أو إخفاء حقول التعديل
        }

        // وظيفة لإظهار رسالة النجاح
        function showMessage(message) {
            const messageBox = document.getElementById('message');
            messageBox.textContent = message;
            messageBox.style.display = 'block';
            setTimeout(() => {
                messageBox.style.display = 'none';
            }, 3000); // إخفاء الرسالة بعد 3 ثواني
        }

        // عرض الرسالة عند التحميل
        window.onload = function() {
            <?php if (isset($_SESSION['message'])): ?>
                showMessage("<?php echo htmlspecialchars($_SESSION['message']); ?>");
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
        }
    </script>
</body>
</html>