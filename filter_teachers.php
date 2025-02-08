<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlineexamdb"; // تأكد من أن اسم قاعدة البيانات صحيح

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// استلام اسم المعلم من الطلب
$search_name = $conn->real_escape_string($_POST['search_name'] ?? '');

// استعلام لجلب المعلمين مع الفلترة
$teachers_sql = "SELECT id, full_name, email, created_at FROM users WHERE role = 'teacher' AND full_name LIKE '%$search_name%'";
$teachers_result = $conn->query($teachers_sql);

// بناء الجدول بالمعلمين
$output = '<thead>
            <tr>
                <th>ID</th>
                <th>اسم المعلم</th>
                <th>البريد الإلكتروني</th>
                <th>تاريخ الإنشاء</th>
                <th>العمليات</th>
            </tr>
           </thead>
           <tbody>';

if ($teachers_result->num_rows > 0) {
    while ($teacher = $teachers_result->fetch_assoc()) {
        $output .= '<tr id="teacher-' . htmlspecialchars($teacher['id']) . '">
                        <td>' . htmlspecialchars($teacher['id']) . '</td>
                        <td>' . htmlspecialchars($teacher['full_name']) . '</td>
                        <td>' . htmlspecialchars($teacher['email']) . '</td>
                        <td>' . htmlspecialchars($teacher['created_at']) . '</td>
                        <td>
                            <button class="button delete" onclick="deleteTeacher(' . htmlspecialchars($teacher['id']) . ')">حذف</button>
                        </td>
                    </tr>';
    }
} else {
    $output .= '<tr>
                    <td colspan="5">لا توجد بيانات للمعلمين.</td>
                </tr>';
}

$output .= '</tbody>';
echo $output;

$conn->close();
?>