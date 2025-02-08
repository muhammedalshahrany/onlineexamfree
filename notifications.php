<?php
session_start();

// تحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "OnlineExamDB");

// تحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// تحديث حالة الإشعارات لتعيينها كمقروءة
$sql_update_notifications = "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0";
$stmt_update = $conn->prepare($sql_update_notifications);
$stmt_update->bind_param("i", $_SESSION['user_id']);
$stmt_update->execute();
$stmt_update->close();

// استعلام لاسترجاع إشعارات الطالب
$sql_notifications = "
    SELECT n.notification_id, n.message, n.created_at, c.complaint_id,
           c.comments AS complaint_message,  -- جلب رسالة التظلم
           GROUP_CONCAT(DISTINCT r.reply_comments SEPARATOR '; ') AS reply_comments,
           e.title AS exam_title,
           MAX(r.created_at) AS reply_date
    FROM notifications n
    JOIN complaints c ON n.complaint_id = c.complaint_id
    LEFT JOIN complaint_replies r ON c.complaint_id = r.complaint_id
    LEFT JOIN exams e ON c.exam_id = e.exam_id
    WHERE n.user_id = ?
    GROUP BY n.notification_id, n.message, n.created_at, c.complaint_id, e.title, c.comments
    ORDER BY n.created_at DESC";

$stmt = $conn->prepare($sql_notifications);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result_notifications = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الإشعارات - منصة الامتحانات الإلكترونية</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>الإشعارات</h1>

        <table>
            <thead>
                <tr>
                    <th>عنوان الامتحان</th>
                    <th>رسالة التظلم</th> <!-- تغيير هنا -->
                    <th>الرسالة</th>
                    <th>تاريخ الإشعار</th>
                    <th>رد المعلم</th>
                    <th>تاريخ الرد</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_notifications->num_rows > 0): ?>
                    <?php while ($row = $result_notifications->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['exam_title']); ?></td>
                            <td><?php echo htmlspecialchars($row['complaint_message']); ?></td> <!-- عرض رسالة التظلم -->
                            <td><?php echo htmlspecialchars($row['message']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <?php if ($row['reply_comments']): ?>
                                    <strong></strong> <?php echo htmlspecialchars($row['reply_comments']); ?>
                                <?php else: ?>
                                    <strong>لا يوجد رد حتى الآن.</strong>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $row['reply_date'] ? htmlspecialchars($row['reply_date']) : 'لم يتم الرد بعد.'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">لا توجد إشعارات جديدة.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php
    // تأكد من أن المتغيرات موجودة قبل محاولة إغلاقها
    if ($conn) {
        $conn->close();
    }
    ?>
</body>
</html>