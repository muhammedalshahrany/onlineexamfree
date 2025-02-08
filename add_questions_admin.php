<?php
session_start();

// تحقق من تسجيل الدخول كمسؤول أو معلم
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'teacher' && $_SESSION['role'] !== 'admin')) {
    header("Location: login.php"); 
    exit;
}

// إعداد قاعدة البيانات
$servername = "localhost"; // الخادم
$username = "root"; // اسم المستخدم الافتراضي في XAMPP
$password = ""; // كلمة المرور الافتراضية
$dbname = "OnlineExamDB"; // اسم قاعدة البيانات الخاصة بك

// إنشاء اتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// معالجة عملية الإدخال
$message = '';
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'add_question') {
    // إضافة سؤال جديد
    $exam_id = $_POST['exam_id']; // الحصول على معرف الامتحان من البيانات المرسلة
    $question_text = $_POST['question_text'];
    $options = [
        'a' => $_POST['option_a'],
        'b' => $_POST['option_b'],
        'c' => $_POST['option_c'],
        'd' => $_POST['option_d']
    ];
    $correct_answer = $_POST['correct_answer']; // تأكد من أن هذه القيمة تكون (a، b، c، d)

    // تأكد من أن الخيارات صحيحة
    if (empty($question_text) || empty($options['a']) || empty($options['b']) || empty($options['c']) || empty($options['d']) || empty($correct_answer)) {
        $message = "يرجى ملء جميع الحقول.";
    } else {
        $sql = "INSERT INTO Questions (exam_id, question_text, options, correct_answer) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $options_json = json_encode($options, JSON_UNESCAPED_UNICODE); // تحويل الخيارات إلى JSON مع الحفاظ على الترميز
        $stmt->bind_param("isss", $exam_id, $question_text, $options_json, $correct_answer);

        if ($stmt->execute()) {
            $_SESSION['message'] = "تم إضافة السؤال بنجاح."; // تعيين الرسالة في الجلسة
            header("Location: " . $_SERVER['PHP_SELF'] . "?exam_id=" . $exam_id); // إعادة تحميل الصفحة مع معرف الامتحان
            exit;
        } else {
            $message = "خطأ في الإضافة: " . $stmt->error;
        }

        $stmt->close();
    }
}

// عرض الرسالة من الجلسة إذا كانت موجودة
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // إزالة الرسالة من الجلسة بعد عرضها
}

// الحصول على معرف الامتحان من الرابط
$exam_id = $_GET['exam_id'] ?? null;

// التحقق من وجود معرف امتحان
if ($exam_id === null) {
    die("معرف الامتحان غير موجود.");
}

// جلب تفاصيل الامتحان
$sql = "SELECT title FROM Exams WHERE exam_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$result = $stmt->get_result();
$exam = $result->fetch_assoc();

if (!$exam) {
    die("لم يتم العثور على امتحان بهذا المعرف.");
}

$conn->close(); // أغلق الاتصال بعد الانتهاء
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدخال الأسئلة</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to left, #6a11cb, #2575fc);
            color: white;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
        }
        input[type="text"], select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #007BFF;
            font-size: 1em;
            box-sizing: border-box; /* تأكد من أن الحقول تأخذ العرض الكامل */
        }
        .button {
            background-color: #007BFF;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .message {
            background: #28a745; 
            color: white; 
            padding: 10px; 
            border-radius: 5px; 
            margin-bottom: 20px; 
            display: <?php echo empty($message) ? 'none' : 'block'; ?>;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>إدخال الأسئلة لامتحان: <?php echo htmlspecialchars($exam['title']); ?></h1>
    
    <!-- زر العودة -->
    <p><a href="admin_page.php" class="button">العودة</a></p>

    <div class="message" id="message"><?php echo htmlspecialchars($message); ?></div> <!-- رسالة الإضافة -->

    <form action="" method="POST">
        <input type="hidden" name="action" value="add_question">
        <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">

        <label>نص السؤال:</label>
        <input type="text" name="question_text" required>
        
        <label>الخيار (أ):</label>
        <input type="text" name="option_a" required>
        
        <label>الخيار (ب):</label>
        <input type="text" name="option_b" required>
        
        <label>الخيار (ج):</label>
        <input type="text" name="option_c" required>
        
        <label>الخيار (د):</label>
        <input type="text" name="option_d" required>
        
        <label>الإجابة الصحيحة:</label>
        <select name="correct_answer" required>
            <option value="">اختر الإجابة الصحيحة</option>
            <option value="a">أ</option>
            <option value="b">ب</option>
            <option value="c">ج</option>
            <option value="d">د</option>
        </select>

        <button type="submit" class="button">إضافة سؤال</button>
    </form>
</div>

<script>
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
        <?php if (!empty($message)): ?>
            showMessage("<?php echo htmlspecialchars($message); ?>");
        <?php endif; ?>
    }
</script>

</body>
</html>