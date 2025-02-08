<?php
session_start(); // بدء الجلسة

// تحقق من تسجيل الدخول كمعلم
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php"); // إعادة التوجيه إلى صفحة تسجيل الدخول
    exit;
}

// إعداد قاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "OnlineExamDB";

// إنشاء اتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// تحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// معالجة عملية الإضافة
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'add') {
    // إضافة امتحان جديد
    $title = $_POST['title'];
    $category_id = $_POST['category_id'];
    $duration = $_POST['duration'];
    $total_questions = $_POST['total_questions'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $teacher_id = $_SESSION['user_id']; // معرف المعلم

    $sql = "INSERT INTO Exams (title, category_id, duration, total_questions, start_time, end_time, teacher_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiissi", $title, $category_id, $duration, $total_questions, $start_time, $end_time, $teacher_id);

    if ($stmt->execute()) {
        header("Location: manage_exams.php?message=تم إضافة الامتحان بنجاح");
        exit;
    } else {
        echo "خطأ في الإضافة: " . $stmt->error;
    }

    $stmt->close();
}

// استرجاع التصنيفات
$categories = [];
$sql = "SELECT * FROM Categories";
$category_result = $conn->query($sql);
if ($category_result->num_rows > 0) {
    while ($row = $category_result->fetch_assoc()) {
        $categories[] = $row;
    }
}

$conn->close(); // أغلق الاتصال بعد الانتهاء
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة امتحان جديد</title>
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
        input[type="text"],
        input[type="number"],
        input[type="datetime-local"],
        select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #007BFF;
            font-size: 1em;
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
    </style>
</head>
<body>

<div class="container">
    <h1>إضافة امتحان جديد</h1>
    <form action="" method="POST">
        <input type="hidden" name="action" value="add">
        <label>عنوان الامتحان:</label>
        <input type="text" name="title" required>
        <label>التصنيف:</label>
        <select name="category_id" required>
            <option value="">اختر التصنيف</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <label>مدة الامتحان (بالدقائق):</label>
        <input type="number" name="duration" required>
        <label>إجمالي الأسئلة:</label>
        <input type="number" name="total_questions" required>
        <label>بداية الامتحان:</label>
        <input type="datetime-local" name="start_time" required>
        <label>نهاية الامتحان:</label>
        <input type="datetime-local" name="end_time" required>
        <button type="submit" class="button">إضافة امتحان</button>
    </form>
</div>

</body>
</html>