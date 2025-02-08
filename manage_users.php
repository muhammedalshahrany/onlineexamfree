<?php
session_start(); // بدء الجلسة

// تحقق مما إذا كان المستخدم قد سجل الدخول كمسؤول
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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

// معالجة عمليات الحذف والتنشيط
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_all_users'])) {
        // حذف السجلات من الجداول المرتبطة أولاً
        $conn->query("DELETE FROM exam_results"); // حذف نتائج الاختبارات
        $conn->query("DELETE FROM notifications"); // حذف إشعارات
        $conn->query("DELETE FROM complaints"); // حذف الشكاوى
        $conn->query("DELETE FROM complaint_replies"); // حذف ردود الشكاوى
        $conn->query("DELETE FROM student_exams"); // حذف اختبارات الطلاب
        
        // الآن حذف جميع المستخدمين
        if ($conn->query("DELETE FROM Users") === TRUE) {
            $_SESSION['message'] = 'تم حذف جميع المستخدمين بنجاح.'; // تخزين الرسالة في الجلسة
        } else {
            $_SESSION['message'] = 'حدث خطأ أثناء حذف جميع المستخدمين: ' . $conn->error;
        }
        header("Location: manage_users.php"); // إعادة توجيه إلى نفس الصفحة
        exit;
    } elseif (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        
        // حذف السجلات المرتبطة في جدول exam_results
        $conn->query("DELETE FROM exam_results WHERE user_id = $user_id");
        
        // حذف السجلات المرتبطة في جدول student_exams
        $conn->query("DELETE FROM student_exams WHERE user_id = $user_id");
        
        // حذف السجلات المرتبطة في جدول complaint_replies
        $conn->query("DELETE FROM complaint_replies WHERE teacher_id = $user_id");
        
        // حذف السجلات المرتبطة في جدول complaints
        $conn->query("DELETE FROM complaints WHERE user_id = $user_id");
        
        // الآن حذف المستخدم من جدول Users
        if ($conn->query("DELETE FROM Users WHERE id = $user_id") === TRUE) {
            $_SESSION['message'] = 'تم حذف المستخدم بنجاح.'; // تخزين الرسالة في الجلسة
        } else {
            $_SESSION['message'] = 'حدث خطأ أثناء حذف المستخدم: ' . $conn->error;
        }
        header("Location: manage_users.php"); // إعادة توجيه إلى نفس الصفحة
        exit;
    } elseif (isset($_POST['activate_user'])) {
        $user_id = $_POST['user_id'];
        $conn->query("UPDATE Users SET status = 'active' WHERE id = $user_id");
    } elseif (isset($_POST['deactivate_user'])) {
        $user_id = $_POST['user_id'];
        $conn->query("UPDATE Users SET status = 'inactive' WHERE id = $user_id");
    }
}

// استعلام لجلب المستخدمين
$users_sql = "SELECT * FROM Users";
$users_result = $conn->query($users_sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المستخدمين</title>
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
        .message {
            padding: 10px;
            margin-bottom: 20px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            display: none; /* إخفاء الرسالة في البداية */
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
        .activate {
            background-color: #28a745; /* أخضر */
        }
        .deactivate {
            background-color: #dc3545; /* أحمر */
        }
        .delete {
            background-color: #dc3545; /* أحمر */
        }
        .button:hover {
            opacity: 0.9;
        }
        #search {
            margin-bottom: 20px;
            padding: 10px;
            width: 300px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        #roleFilter {
            margin-bottom: 20px;
            padding: 10px;
        }
    </style>
    <script>
        let currentRoleFilter = 'all'; // متغير لتخزين الفلترة الحالية

        function searchUsers() {
            const searchValue = document.getElementById('search').value.toLowerCase();
            const rows = document.querySelectorAll('#userTable tr');
            rows.forEach(row => {
                const usernameCell = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const fullNameCell = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const roleCell = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
                const matchesSearch = usernameCell.includes(searchValue) || fullNameCell.includes(searchValue);
                const matchesRole = currentRoleFilter === 'all' || roleCell === currentRoleFilter.toLowerCase();
                row.style.display = matchesSearch && matchesRole ? '' : 'none';
            });
        }

        function confirmDelete(form) {
            return confirm('هل أنت متأكد من حذف هذا المستخدم؟');
        }

        function updateRow(userId, status) {
            const rows = document.querySelectorAll('#userTable tr');
            rows.forEach(row => {
                const idCell = row.querySelector('td:first-child').textContent;
                if (idCell == userId) {
                    const statusCell = row.querySelector('td:nth-child(9)');
                    statusCell.textContent = status === 'active' ? 'نشط' : 'غير نشط';
                    const activateButton = row.querySelector('button[name="activate_user"]');
                    const deactivateButton = row.querySelector('button[name="deactivate_user"]');
                    if (status === 'active') {
                        deactivateButton.style.display = 'inline-block';
                        activateButton.style.display = 'none';
                    } else {
                        activateButton.style.display = 'inline-block';
                        deactivateButton.style.display = 'none';
                    }
                }
            });
        }

        function setRoleFilter() {
            currentRoleFilter = document.getElementById('roleFilter').value.toLowerCase();
            searchUsers();
        }

        window.onload = function() {
            const message = document.getElementById('message');
            if (message) {
                message.style.display = 'block'; // عرض الرسالة
                setTimeout(() => {
                    message.style.display = 'none'; // إخفاء الرسالة بعد ثلاث ثوان
                }, 3000);
            }
        };
    </script>
</head>
<body>

<div class="container">
    <h1>إدارة المستخدمين</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="message" id="message">
            <?php echo $_SESSION['message']; ?>
            <?php unset($_SESSION['message']); // إفراغ الرسالة بعد عرضها ?>
        </div>
    <?php endif; ?>

    <form method="POST" style="display:inline;">
        <button type="submit" name="delete_all_users" class="button delete" onclick="return confirm('هل أنت متأكد من حذف جميع المستخدمين؟');">حذف جميع المستخدمين</button>
    </form>
    
    <a href="export_users.php" class="button" style="margin-bottom: 20px; display: inline-block;">تصدير إلى Excel</a>
    
    <input type="text" id="search" placeholder="بحث عن مستخدم" onkeyup="searchUsers()">
    
    <select id="roleFilter" onchange="setRoleFilter()">
        <option value="all">كل الأدوار</option>
        <option value="student">طالب</option>
        <option value="teacher">معلم</option>
        <option value="admin">مدير</option>
    </select>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>الاسم الكامل</th>
                <th>اسم المستخدم</th>
                <th>البريد الإلكتروني</th>
                <th>الدور</th>
                <th>تاريخ الإنشاء</th>
                <th>الهاتف</th>
                <th>الجنس</th>
                <th>الحالة</th>
                <th>العمليات</th>
            </tr>
        </thead>
        <tbody id="userTable">
            <?php while ($user = $users_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                <td><?php echo htmlspecialchars($user['gender']); ?></td>
                <td><?php echo htmlspecialchars($user['status']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        <?php if ($user['status'] === 'active'): ?>
                            <button type="submit" name="deactivate_user" class="button deactivate" onclick="updateRow(<?php echo $user['id']; ?>, 'inactive');">إيقاف</button>
                        <?php else: ?>
                            <button type="submit" name="activate_user" class="button activate" onclick="updateRow(<?php echo $user['id']; ?>, 'active');">تفعيل</button>
                        <?php endif; ?>
                        <button type="submit" name="delete_user" class="button delete" onclick="return confirmDelete(this.parentNode);">حذف</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>