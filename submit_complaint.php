<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // يجب أن تكون 'Location' وليس 'login.php'
    exit;
}

// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "OnlineExamDB");
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// معالجة البيانات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exam_id = $_POST['exam_id'];
    $complaint_type = $_POST['complaint_type']; // نوع التظلم
    $comments = $_POST['comments'];
    $user_id = $_SESSION['user_id'];

    // التحقق من وجود تظلم مسبق
    $stmt_check = $conn->prepare("SELECT * FROM complaints WHERE user_id = ? AND exam_id = ?");
    $stmt_check->bind_param("ii", $user_id, $exam_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo "لقد قمت برفع تظلم مسبق لهذا الامتحان. انتظر رد المعلم.";
    } else {
        // إدخال التظلم
        $stmt = $conn->prepare("INSERT INTO complaints (user_id, exam_id, complaint_type, comments) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $user_id, $exam_id, $complaint_type, $comments);

        if ($stmt->execute()) {
            echo "تم إرسال التظلم بنجاح.";
        } else {
            echo "فشل إرسال التظلم: " . $stmt->error;
        }

        $stmt->close();
    }

    $stmt_check->close();
}

$conn->close();
?>