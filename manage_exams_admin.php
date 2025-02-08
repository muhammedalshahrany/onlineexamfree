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

// معالجة عمليات الحذف والتعديل وتغيير الحالة
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_exam'])) {
        $exam_id = $_POST['exam_id'];
        $conn->query("DELETE FROM exams WHERE exam_id = $exam_id");
        header("Location: " . $_SERVER['PHP_SELF']); // إعادة توجيه إلى نفس الصفحة
        exit;
    } elseif (isset($_POST['toggle_exam'])) {
        $exam_id = $_POST['exam_id'];
        $current_status = $_POST['current_status'];
        $new_status = ($current_status == '1') ? '0' : '1';
        $conn->query("UPDATE exams SET is_available = '$new_status' WHERE exam_id = $exam_id");
    } elseif (isset($_POST['update_exam'])) {
        $exam_id = $_POST['exam_id'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $conn->query("UPDATE exams SET start_time = '$start_time', end_time = '$end_time' WHERE exam_id = $exam_id");
        header("Location: " . $_SERVER['PHP_SELF']); // إعادة توجيه بعد التحديث
        exit;
    }
}

// استعلام لجلب الامتحانات مع اسم المعلم من جدول المستخدمين
$exams_sql = "SELECT 
                exams.exam_id, 
                exams.title, 
                exams.start_time, 
                exams.end_time, 
                exams.is_available, 
                users.full_name AS teacher_name 
              FROM exams 
              JOIN users ON exams.teacher_id = users.id";
$exams_result = $conn->query($exams_sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar">
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
        .toggle-active {
            background-color: #28a745; /* أخضر */
        }
        .toggle-inactive {
            background-color: #dc3545; /* أحمر */
        }
        .delete {
            background-color: #dc3545; /* أحمر */
        }
        .update {
            background-color: #28a745; /* أخضر */
        }
        .view-questions {
            background-color: #ffc107; /* أصفر */
        }
        .button:hover {
            opacity: 0.9;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>إدارة الامتحانات</h1>
    
    <input type="text" id="search" placeholder="بحث باسم المعلم" onkeyup="searchExams()" />

    <table id="examsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>اسم الامتحان</th>
                <th>اسم المعلم</th>
                <th>تاريخ البدء</th>
                <th>تاريخ الانتهاء</th>
                <th>الحالة</th>
                <th>العمليات</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($exam = $exams_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($exam['exam_id']); ?></td>
                <td><?php echo htmlspecialchars($exam['title']); ?></td>
                <td><?php echo htmlspecialchars($exam['teacher_name']); ?></td>
                <td class="start_time"><?php echo htmlspecialchars($exam['start_time']); ?></td>
                <td class="end_time"><?php echo htmlspecialchars($exam['end_time']); ?></td>
                <td><?php echo htmlspecialchars($exam['is_available']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="exam_id" value="<?php echo $exam['exam_id']; ?>">
                        <input type="hidden" name="current_status" value="<?php echo $exam['is_available']; ?>">
                        
                        <button type="submit" name="toggle_exam" class="button <?php echo $exam['is_available'] == '1' ? 'toggle-active' : 'toggle-inactive'; ?>">
                            <?php echo $exam['is_available'] == '1' ? 'إيقاف' : 'تفعيل'; ?>
                        </button>
                        
                        <button type="button" class="button edit" onclick="toggleEdit(this)">تعديل</button>
                        <div class="hidden edit-fields">
                            <input type="datetime-local" name="start_time" value="<?php echo htmlspecialchars($exam['start_time']); ?>" />
                            <input type="datetime-local" name="end_time" value="<?php echo htmlspecialchars($exam['end_time']); ?>" />
                            <button type="submit" name="update_exam" class="button update">تحديث</button>
                        </div>
                        
                        <button type="submit" name="delete_exam" class="button delete" onclick="return confirm('هل أنت متأكد من حذف هذا الامتحان؟');">حذف</button>
                        <a href="display_questions_admin.php?exam_id=<?php echo $exam['exam_id']; ?>" class="button view-questions">عرض الأسئلة</a>
                        
                        <!-- إضافة زر "إضافة أسئلة" وتنسيقه ليكون مشابهًا لزر عرض الأسئلة -->
                        <a href="add_questions_admin.php?exam_id=<?php echo $exam['exam_id']; ?>" class="button view-questions">إضافة أسئلة</a>
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
        if (editFields.classList.contains('hidden')) {
            editFields.classList.remove('hidden'); // إظهار حقول التعديل
        } else {
            editFields.classList.add('hidden'); // إخفاء حقول التعديل
        }
    }

    function searchExams() {
        const input = document.getElementById('search').value.toLowerCase();
        const rows = document.querySelectorAll('#examsTable tbody tr');

        rows.forEach(row => {
            const teacherName = row.cells[2].textContent.toLowerCase(); // اسم المعلم
            if (teacherName.includes(input)) {
                row.style.display = ''; // إظهار الصف
            } else {
                row.style.display = 'none'; // إخفاء الصف
            }
        });
    }
</script>

</body>
</html>