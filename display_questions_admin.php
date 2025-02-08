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

$exam_id = $_GET['exam_id'] ?? 0;

// معالجة عمليات الحذف والتعديل
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_question'])) {
        $question_id = $_POST['question_id'];
        $conn->query("DELETE FROM questions WHERE question_id = $question_id");
        header("Location: " . $_SERVER['PHP_SELF'] . "?exam_id=$exam_id"); // إعادة توجيه إلى نفس الصفحة
        exit;
    } elseif (isset($_POST['update_question'])) {
        $question_id = $_POST['question_id'];
        $question_text = $_POST['question_text'];
        $options = $_POST['options']; // خيارات السؤال
        $correct_answer = $_POST['correct_answer']; // الإجابة الصحيحة

        // تحديث البيانات في قاعدة البيانات
        $conn->query("UPDATE questions SET question_text = '$question_text', options = '$options', correct_answer = '$correct_answer' WHERE question_id = $question_id");
        header("Location: " . $_SERVER['PHP_SELF'] . "?exam_id=$exam_id"); // إعادة توجيه بعد التحديث
        exit;
    }
}

// استعلام لجلب الأسئلة المرتبطة بالامتحان
$questions_sql = "SELECT * FROM questions WHERE exam_id = $exam_id";
$questions_result = $conn->query($questions_sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أسئلة الامتحان</title>
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
            font-size: 2em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 5px;
            border: 1px solid white;
            font-size: 0.9em;
        }
        th {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .button {
            padding: 5px 10px;
            font-size: 0.8em;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
            transition: background-color 0.3s;
        }
        .edit {
            background-color: #007bff; /* أزرق */
        }
        .delete {
            background-color: #dc3545; /* أحمر */
        }
        .hidden {
            display: none;
        }
        .edit-fields {
            display: none; /* إخفاء حقول التعديل بشكل افتراضي */
            margin-top: 10px;
            text-align: left; /* محاذاة النص إلى اليسار */
        }
        input[type="text"] {
            width: 90%; /* عرض الحقول */
            padding: 10px; /* مساحة داخلية أكبر */
            margin-bottom: 10px; /* مسافة بين الحقول */
            border-radius: 5px; /* زوايا دائرية */
            border: 1px solid #ccc; /* إطار رمادي */
            background-color: #fff; /* خلفية بيضاء */
            color: #000; /* نص أسود */
        }
    </style>
</head>
<body>

<div class="container">
    <h1>أسئلة الامتحان</h1>
    
    <table id="questionsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>نص السؤال</th>
                <th>خيارات</th>
                <th>الإجابة الصحيحة</th>
                <th>تاريخ الإنشاء</th>
                <th>العمليات</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($question = $questions_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($question['question_id']); ?></td>
                <td><?php echo htmlspecialchars($question['question_text']); ?></td>
                <td>
                    <?php 
                        $options = explode(',', $question['options']); // تقسيم الخيارات إلى مصفوفة
                        foreach ($options as $option) {
                            echo htmlspecialchars(trim($option)) . '<br>'; // عرض كل خيار في سطر جديد
                        }
                    ?>
                </td>
                <td><?php echo htmlspecialchars($question['correct_answer']); ?></td>
                <td><?php echo htmlspecialchars($question['created_at']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="question_id" value="<?php echo $question['question_id']; ?>">
                        <button type="button" class="button edit" onclick="toggleEdit(this)">تعديل</button>
                        <button type="submit" name="delete_question" class="button delete" onclick="return confirm('هل أنت متأكد من حذف هذا السؤال؟');">حذف</button>
                        <div class="edit-fields">
                            <h4>تعديل السؤال:</h4>
                            <input type="text" name="question_text" value="<?php echo htmlspecialchars($question['question_text']); ?>" placeholder="نص السؤال" required />
                            <input type="text" name="options" value="<?php echo htmlspecialchars($question['options']); ?>" placeholder="خيارات (مفصولة بفواصل)" required />
                            <input type="text" name="correct_answer" value="<?php echo htmlspecialchars($question['correct_answer']); ?>" placeholder="الإجابة الصحيحة" required />
                            <button type="submit" name="update_question" class="button edit">تحديث</button>
                        </div>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
    function toggleEdit(button) {
        const row = button.closest('tr'); // تحديد الصف الحالي
        const editFields = row.querySelector('.edit-fields'); // تحديد حقل التعديل في الصف

        // إظهار أو إخفاء حقول التعديل
        if (editFields.style.display === "none" || editFields.style.display === "") {
            editFields.style.display = "block"; // إظهار حقول التعديل
        } else {
            editFields.style.display = "none"; // إخفاء حقول التعديل
        }
    }
</script>

</body>
</html>