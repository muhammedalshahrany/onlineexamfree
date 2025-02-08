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

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// معالجة عمليات الإدخال
$message = '';
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_question') {
        // إضافة منطق إضافة سؤال (كما في الكود السابق)
    } elseif ($_POST['action'] === 'update') {
        // إضافة منطق تحديث امتحان (كما في الكود السابق)
    }
}

// معالجة الحذف
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $exam_id = $_GET['id'];

    // حذف الامتحان
    $sql = "DELETE FROM Exams WHERE exam_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $exam_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "تم حذف الامتحان بنجاح."; // رسالة النجاح
    } else {
        $_SESSION['message'] = "خطأ في الحذف: " . $stmt->error;
    }
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']); // إعادة التوجيه إلى نفس الصفحة
    exit;
}

// استرجاع الامتحانات الخاصة بالمعلم الحالي
$sql = "SELECT e.exam_id, e.title, e.duration, e.total_questions, e.category_id, c.name AS category_name, e.start_time, e.end_time 
        FROM Exams e 
        JOIN Categories c ON e.category_id = c.category_id
        WHERE e.teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']); // ربط معرف المعلم الحالي
$stmt->execute();
$result = $stmt->get_result();

// استرجاع التصنيفات
$categories = [];
$sql = "SELECT * FROM Categories";
$category_result = $conn->query($sql);
if ($category_result->num_rows > 0) {
    while ($row = $category_result->fetch_assoc()) {
        $categories[] = $row;
    }
}

$stmt->close();
$conn->close(); // أغلق الاتصال بعد الانتهاء

$message = '';
if (isset($_SESSION['message'])) {
    $message = htmlspecialchars($_SESSION['message']);
    unset($_SESSION['message']); // إزالة الرسالة بعد عرضها
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الامتحانات</title>
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
            padding: 30px;
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
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        .button {
            background-color: #007BFF;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .success-message, .error-message {
            background: #28a745;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none; /* إخفاء الرسالة افتراضيًا */
        }
        .error-message {
            background: #dc3545; /* لون الرسالة عند الخطأ */
        }
        .edit-form {
            display: none;
            margin-top: 20px;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            text-align: left;
        }
        .edit-form label {
            display: block;
            margin: 10px 0 5px;
        }
    </style>
    <script>
        function confirmDelete(examId) {
            if (confirm("هل أنت متأكد أنك تريد حذف هذا الامتحان؟")) {
                window.location.href = '?action=delete&id=' + examId;
            }
        }

        function toggleEditForm(examId) {
            const form = document.getElementById('edit-form-' + examId);
            form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none'; // التبديل بين العرض والإخفاء
        }

        // وظيفة لإخفاء رسالة النجاح بعد 3 ثواني
        window.onload = function() {
            const successMessage = document.querySelector('.success-message');
            if (successMessage) {
                successMessage.style.display = 'block';
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 3000); // إخفاء بعد 3000 مللي ثانية (3 ثواني)
            }
        }
    </script>
</head>
<body>

<div class="container">
    <h1>إدارة الامتحانات</h1>

    <button class="button" onclick="window.location.href='teacher_exam_management.php'">العودة</button> <!-- زر العودة -->

    <?php if ($message): ?>
        <div class="success-message">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <h3>الامتحانات الحالية</h3>
    <table>
        <thead>
            <tr>
                <th>عنوان الامتحان</th>
                <th>التصنيف</th>
                <th>مدة الامتحان</th>
                <th>إجمالي الأسئلة</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['duration']); ?> دقيقة</td>
                        <td><?php echo htmlspecialchars($row['total_questions']); ?></td>
                        <td>
                            <button class="button" onclick="toggleEditForm(<?php echo $row['exam_id']; ?>)">تعديل</button>
                            <button class="button" onclick="confirmDelete(<?php echo $row['exam_id']; ?>)">حذف</button>
                            <button class="button" onclick="window.location.href='add_questions.php?exam_id=<?php echo $row['exam_id']; ?>'">إضافة أسئلة</button>
                            <button class="button" onclick="window.location.href='view_questions.php?exam_id=<?php echo $row['exam_id']; ?>'">عرض الأسئلة</button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <div class="edit-form" id="edit-form-<?php echo $row['exam_id']; ?>">
                                <h3>تعديل الامتحان</h3>
                                <form action="" method="POST">
                                    <input type="hidden" name="exam_id" value="<?php echo $row['exam_id']; ?>">
                                    <input type="hidden" name="action" value="update">
                                    <label>عنوان الامتحان:</label>
                                    <input type="text" name="title" required value="<?php echo htmlspecialchars($row['title']); ?>">
                                    <label>التصنيف:</label>
                                    <select name="category_id" required>
                                        <option value="">اختر التصنيف</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['category_id']; ?>" <?php echo ($category['category_id'] == $row['category_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label>مدة الامتحان (بالدقائق):</label>
                                    <input type="number" name="duration" required value="<?php echo htmlspecialchars($row['duration']); ?>">
                                    <label>إجمالي الأسئلة:</label>
                                    <input type="number" name="total_questions" required value="<?php echo htmlspecialchars($row['total_questions']); ?>">
                                    <label>بداية الامتحان:</label>
                                    <input type="datetime-local" name="start_time" required value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($row['start_time']))); ?>">
                                    <label>نهاية الامتحان:</label>
                                    <input type="datetime-local" name="end_time" required value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($row['end_time']))); ?>">
                                    <button type="submit" class="button">حفظ التعديلات</button>
                                    <button type="button" class="button" onclick="toggleEditForm(<?php echo $row['exam_id']; ?>);">إلغاء</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">لا توجد امتحانات حالية.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>