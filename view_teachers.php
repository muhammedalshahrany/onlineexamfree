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

// استعلام لجلب قائمة المعلمين
$teachers_sql = "SELECT id, full_name, username, email, created_at FROM users WHERE role = 'teacher'";
$teachers_result = $conn->query($teachers_sql);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير المعلمين</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        .button {
            padding: 5px 10px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .delete {
            background-color: #dc3545; /* أحمر */
        }

        .filter-form {
            margin-bottom: 20px;
        }
    </style>
    <script>
        function filterTeachers() {
            const searchName = document.getElementById('search_name').value;

            fetch('filter_teachers.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `search_name=${encodeURIComponent(searchName)}`
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('teachers_table').innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function deleteTeacher(teacherId) {
            if (confirm('هل أنت متأكد من حذف هذا المعلم؟')) {
                fetch('delete_teacher.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `teacher_id=${teacherId}`
                })
                .then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('فشل في حذف المعلم.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }

        function exportToExcel() {
            window.location.href = 'export_teachers.php';
        }
    </script>
</head>
<body>

<h1>تقرير المعلمين</h1>

<button onclick="exportToExcel()" class="button">تصدير إلى Excel</button>

<form method="GET" class="filter-form">
    <input type="text" id="search_name" name="search_name" placeholder="بحث عن اسم المعلم" oninput="filterTeachers()">
</form>

<table id="teachers_table">
    <thead>
        <tr>
            <th>ID</th>
            <th>اسم المعلم</th>
            <th>اسم المستخدم</th> <!-- إضافة اسم المستخدم -->
            <th>البريد الإلكتروني</th>
            <th>تاريخ الإنشاء</th>
            <th>العمليات</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // بناء الجدول بالمعلمين
        if ($teachers_result->num_rows > 0) {
            while ($teacher = $teachers_result->fetch_assoc()) {
                echo '<tr id="teacher-' . htmlspecialchars($teacher['id']) . '">
                        <td>' . htmlspecialchars($teacher['id']) . '</td>
                        <td>' . htmlspecialchars($teacher['full_name']) . '</td>
                        <td>' . htmlspecialchars($teacher['username']) . '</td> <!-- عرض اسم المستخدم -->
                        <td>' . htmlspecialchars($teacher['email']) . '</td>
                        <td>' . htmlspecialchars($teacher['created_at']) . '</td>
                        <td>
                            <button class="button delete" onclick="deleteTeacher(' . htmlspecialchars($teacher['id']) . ')">حذف</button>
                        </td>
                    </tr>';
            }
        } else {
            echo '<tr>
                    <td colspan="6">لا توجد بيانات للمعلمين.</td>
                  </tr>';
        }
        ?>
    </tbody>
</table>

</body>
</html>

<?php
$conn->close();
?>