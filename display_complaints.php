<?php
session_start();

// تحقق من تسجيل الدخول كمسؤول
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); 
    exit;
}

// إعداد قاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "OnlineExamDB";

// إنشاء اتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// معالجة عمليات الحذف والتعديل
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['delete_complaint'])) {
        $complaint_id = $_POST['complaint_id'];
        $conn->query("DELETE FROM Complaints WHERE complaint_id = $complaint_id");
        header("Location: " . $_SERVER['PHP_SELF']); // إعادة توجيه إلى نفس الصفحة
        exit;
    } elseif (isset($_POST['update_complaint'])) {
        $complaint_id = $_POST['complaint_id'];
        $new_comments = $_POST['comments'];
        $conn->query("UPDATE Complaints SET comments = '$new_comments' WHERE complaint_id = $complaint_id");
        header("Location: " . $_SERVER['PHP_SELF']); // إعادة توجيه بعد التحديث
        exit;
    }
}

// استعلام لجلب التظلمات مع تفاصيل الطالب والامتحان
$complaints_sql = "
    SELECT c.complaint_id, c.user_id, c.exam_id, c.complaint_type, c.comments, c.created_at, 
           u.full_name AS student_name, e.title AS exam_title
    FROM Complaints c
    JOIN Users u ON c.user_id = u.id
    JOIN Exams e ON c.exam_id = e.exam_id
";
$complaints_result = $conn->query($complaints_sql);

$conn->close(); // أغلق الاتصال بعد الانتهاء
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض التظلمات</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to left, #6a11cb, #2575fc);
            color: white;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid white;
        }
        th {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .button {
            background-color: #007BFF;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .edit-fields {
            display: none;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>عرض التظلمات</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>اسم الطالب</th>
                <th>معرف الامتحان</th>
                <th>عنوان الامتحان</th>
                <th>نوع التظلم</th>
                <th>التعليقات</th>
                <th>تاريخ الإنشاء</th>
                <th>العمليات</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($complaint = $complaints_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($complaint['complaint_id']); ?></td>
                <td><?php echo htmlspecialchars($complaint['student_name']); ?></td>
                <td><?php echo htmlspecialchars($complaint['exam_id']); ?></td>
                <td><?php echo htmlspecialchars($complaint['exam_title']); ?></td>
                <td><?php echo htmlspecialchars($complaint['complaint_type']); ?></td>
                <td>
                    <span class="complaint-text"><?php echo htmlspecialchars($complaint['comments']); ?></span>
                    <div class="edit-fields">
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="complaint_id" value="<?php echo $complaint['complaint_id']; ?>">
                            <input type="text" name="comments" value="<?php echo htmlspecialchars($complaint['comments']); ?>" required>
                            <button type="submit" name="update_complaint" class="button">تحديث</button>
                        </form>
                    </div>
                </td>
                <td><?php echo htmlspecialchars($complaint['created_at']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="complaint_id" value="<?php echo $complaint['complaint_id']; ?>">
                        <button type="submit" name="delete_complaint" class="button" onclick="return confirm('هل أنت متأكد من حذف هذا التظلم؟');">حذف</button>
                    </form>
                    <button class="button" onclick="toggleEdit(this)">تعديل</button>
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
        if (editFields.style.display === 'none' || editFields.style.display === '') {
            editFields.style.display = 'block'; // إظهار حقول التعديل
        } else {
            editFields.style.display = 'none'; // إخفاء حقول التعديل
        }
    }
</script>

</body>
</html>