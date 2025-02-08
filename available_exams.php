<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'مستخدم غير معروف';

$conn = new mysqli("localhost", "root", "", "OnlineExamDB");
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// استعلام لجلب الامتحانات المتاحة التي لم يتم اختبارها
$sql = "SELECT e.exam_id, e.title, e.duration, e.start_time, e.end_time 
        FROM Exams e 
        LEFT JOIN Exam_Results er ON e.exam_id = er.exam_id AND er.user_id = ?
        WHERE e.is_available = TRUE AND er.exam_id IS NULL";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$exams = [];
while ($row = $result->fetch_assoc()) {
    $exams[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الامتحانات المتاحة</title>
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
        .exam {
            border: 1px solid #007bff;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .exam h2 {
            margin: 0;
            color: #007bff;
        }
        .button {
            display: inline-block;
            padding: 10px 15px;
            margin-top: 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .expired {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>الامتحانات المتاحة</h1>
        <p>مرحبًا بك، <?php echo htmlspecialchars($user_full_name); ?>!</p>
        <?php if (empty($exams)): ?>
            <p><center>لا توجد امتحانات متاحة في الوقت الحالي. يرجى التحقق لاحقًا.</center></p>
        <?php else: ?>
            <?php foreach ($exams as $exam): ?>
                <div class="exam">
                    <h2><?php echo htmlspecialchars($exam['title']); ?></h2>
                    <p><strong>المدة:</strong> <?php echo htmlspecialchars($exam['duration']); ?> دقيقة</p>
                    <p><strong>بداية الامتحان:</strong> <?php echo htmlspecialchars($exam['start_time']); ?></p>
                    <p><strong>نهاية الامتحان:</strong> <?php echo htmlspecialchars($exam['end_time']); ?></p>
                    
                    <?php
                    $currentTime = new DateTime();
                    echo "الوقت الحالي: " . $currentTime->format('Y-m-d H:i:s');

                    if ($currentTime < new DateTime($exam['start_time'])): ?>
                        <span class="button" style="background-color: grey;">غير متاح بعد</span>
                    <?php elseif ($currentTime >= new DateTime($exam['start_time']) && $currentTime <= new DateTime($exam['end_time'])): ?>
                        <a href="Exam.php?exam_id=<?php echo $exam['exam_id']; ?>" class="button" onclick="startExam()">بدء الامتحان</a>
                    <?php else: ?>
                        <span class="expired">انتهت صلاحية الامتحان</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <center><a href="student_page.php" class="button">العودة إلى لوحة التحكم</a></center>
    </div>

    <script>
        function startExam() {
            // تحديث تاريخ المتصفح لمنع العودة
            history.pushState(null, null, location.href);
            window.onpopstate = function () {
                history.pushState(null, null, location.href);
            };
        }
    </script>
</body>
</html>