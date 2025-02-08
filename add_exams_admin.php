<?php
session_start();

// تحقق مما إذا كان المستخدم قد سجل الدخول كمسؤول
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // إعادة توجيه إلى صفحة تسجيل الدخول
    exit;
}

// إعدادات قاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlineexamdb";

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// تحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// جلب الفئات من قاعدة البيانات
$categories = [];
$result = $conn->query("SELECT category_id, name FROM categories");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// معالجة إدخال الامتحان الجديد
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $category_id = $_POST['category_id'];
    $duration = $_POST['duration'];
    $total_questions = $_POST['total_questions'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $is_available = isset($_POST['is_available']) ? 1 : 0; // تحديد الحالة
    $teacher_id = $_SESSION['user_id']; // استخدام معرف المعلم من الجلسة

    // إدخال البيانات في قاعدة البيانات
    $stmt = $conn->prepare("INSERT INTO exams (title, category_id, duration, total_questions, start_time, end_time, is_available, teacher_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssii", $title, $category_id, $duration, $total_questions, $start_time, $end_time, $is_available, $teacher_id);

    if ($stmt->execute()) {
        $exam_id = $conn->insert_id; // الحصول على معرف الامتحان الجديد
        $success_message = "تم إنشاء الامتحان بنجاح!";
    } else {
        $error_message = "فشل في إنشاء الامتحان: " . $stmt->error; // عرض خطأ قاعدة البيانات
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء امتحان جديد</title>
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
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            text-align: left;
        }
        h1 {
            margin-bottom: 20px;
            font-size: 2em;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="datetime-local"],
        input[type="number"] {
            width: 96%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: #fff;
            color: #000;
            font-size: 1em;
        }
        .button {
            padding: 10px 15px;
            font-size: 1em;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s;
            width: 100%;
        }
        .submit {
            background-color: #28a745; /* أخضر */
        }
        .button:hover {
            opacity: 0.9;
        }
        .message {
            display: none;
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            background-color: #28a745;
            color: white;
            text-align: center;
        }
    </style>
    <script>
        function showMessage() {
            const message = document.getElementById('successMessage');
            message.style.display = 'block';
            setTimeout(() => {
                message.style.display = 'none';
                window.location.href = 'add_questions_admin.php?exam_id=<?php echo $exam_id; ?>'; // إعادة التوجيه إلى صفحة إضافة الأسئلة
            }, 3000);
        }
    </script>
</head>
<body>

<div class="container">
    <h1>إنشاء امتحان جديد</h1>
    <?php if (isset($success_message)): ?>
        <div id="successMessage" class="message"><?php echo $success_message; ?></div>
        <script>showMessage();</script>
    <?php elseif (isset($error_message)): ?>
        <div class="message" style="background-color: #dc3545;"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label for="title">عنوان الامتحان:</label>
            <input type="text" name="title" id="title" placeholder="عنوان الامتحان" required />
        </div>

        <div class="form-group">
            <label for="category_id">الفئة:</label>
            <select name="category_id" id="category_id" required>
                <option value="">اختر فئة</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="duration">مدة الامتحان (بالدقائق):</label>
            <input type="number" name="duration" id="duration" placeholder="مدة الامتحان" required />
        </div>

        <div class="form-group">
            <label for="total_questions">إجمالي الأسئلة:</label>
            <input type="number" name="total_questions" id="total_questions" placeholder="إجمالي الأسئلة" required />
        </div>

        <div class="form-group">
            <label for="start_time">تاريخ البدء:</label>
            <input type="datetime-local" name="start_time" id="start_time" required />
        </div>

        <div class="form-group">
            <label for="end_time">تاريخ الانتهاء:</label>
            <input type="datetime-local" name="end_time" id="end_time" required />
        </div>

        <div class="checkbox-label">
            <label>تفعيل الامتحان:</label>
            <input type="checkbox" name="is_available" />
        </div>

        <button type="submit" class="button submit">إنشاء امتحان</button>
    </form>
</div>

</body>
</html>