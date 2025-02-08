<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "onlineexamdb");

// تحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// تفعيل عرض الأخطاء
error_reporting(E_ALL);
ini_set('display_errors', 1);

$users = [];

// جلب جميع المستخدمين
$result = $conn->query("SELECT id, username, full_name, role FROM users");
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

// معالجة تحديث الدور عند الإرسال
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];

    // تحقق من أن الدور الجديد هو أحد القيم المسموح بها
    $valid_roles = ['student', 'teacher', 'admin'];
    if (!in_array($new_role, $valid_roles)) {
        echo json_encode(['success' => false, 'message' => 'الدور غير صالح.']);
        exit;
    }

    // طباعة بيانات POST للتحقق
    error_log(print_r($_POST, true)); // طباعة بيانات POST في سجل الأخطاء
    error_log("User ID: $user_id, New Role: $new_role");

    // تحديث الدور للمستخدم
    $stmt = $conn->prepare("UPDATE users SET role=? WHERE id=?");
    $stmt->bind_param("si", $new_role, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'تم تحديث الدور بنجاح.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل تحديث الدور: ' . $stmt->error]);
    }
    exit; // لإنهاء تنفيذ السكربت بعد الرد
}

// إغلاق الاتصال
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الأدوار</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: right; }
        th { background-color: #f2f2f2; }
        .button-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .search-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .filter-container {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="button-container">
        <a href="admin_page.php" class="button">العودة إلى صفحة الإدارة</a>
    </div>

    <div class="search-container">
        <input type="text" id="usernameSearch" placeholder="ابحث عن اسم المستخدم">
        <input type="text" id="fullNameSearch" placeholder="ابحث عن الاسم الكامل">
    </div>

    <div class="filter-container">
        <select id="roleFilter">
            <option value="">تصفية حسب الدور</option>
            <option value="student">طالب</option>
            <option value="teacher">معلم</option>
            <option value="admin">مسؤول</option>
        </select>
    </div>
    
    <table id="userTable">
        <thead>
            <tr>
                <th>الاسم الكامل</th>
                <th>اسم المستخدم</th>
                <th>الدور الحالي</th>
                <th>تعديل الدور</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['full_name']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td class="role"><?php echo $user['role']; ?></td>
                    <td>
                        <select class="role-select" data-user-id="<?php echo $user['id']; ?>">
                            <option value="student" <?php echo ($user['role'] == 'student') ? 'selected' : ''; ?>>طالب</option>
                            <option value="teacher" <?php echo ($user['role'] == 'teacher') ? 'selected' : ''; ?>>معلم</option>
                            <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>مسؤول</option>
                        </select>
                        <button class="update-role">تحديث</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            // بحث المستخدمين حسب اسم المستخدم
            $('#usernameSearch').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('#userTable tbody tr').filter(function() {
                    const username = $(this).find('td:nth-child(2)').text().toLowerCase();
                    $(this).toggle(username.indexOf(searchTerm) > -1);
                });
            });

            // بحث المستخدمين حسب الاسم الكامل
            $('#fullNameSearch').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('#userTable tbody tr').filter(function() {
                    const fullName = $(this).find('td:nth-child(1)').text().toLowerCase();
                    $(this).toggle(fullName.indexOf(searchTerm) > -1);
                });
            });

            // تصفية حسب الدور
            $('#roleFilter').change(function() {
                const selectedRole = $(this).val();
                $('#userTable tbody tr').filter(function() {
                    const role = $(this).find('.role').text();
                    if (selectedRole === "") {
                        $(this).show();
                    } else {
                        $(this).toggle(role === selectedRole);
                    }
                });
            });

            // تحديث الدور
            $('.update-role').click(function() {
                const row = $(this).closest('tr');
                const userId = $(this).siblings('.role-select').data('user-id');
                const newRole = $(this).siblings('.role-select').val();

                $.ajax({
                    url: 'manage_roles.php',
                    type: 'POST',
                    data: { user_id: userId, role: newRole },
                    success: function(response) {
                        const result = JSON.parse(response);
                        alert(result.message);
                        if (result.success) {
                            row.find('.role').text(newRole); // تحديث النص في الخلية
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log("Error Status: " + textStatus);
                        console.log("Error Thrown: " + errorThrown);
                        alert('حدث خطأ أثناء تحديث الدور. تحقق من وحدة التحكم لمزيد من التفاصيل.');
                    }
                });
            });
        });
    </script>
</body>
</html>