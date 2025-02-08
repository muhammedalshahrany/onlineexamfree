<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['question_text'], $_POST['selected_answer'], $_POST['is_correct'], $_POST['user_id'], $_POST['exam_id'])) {
        $question_text = $_POST['question_text'];
        $selected_answer = $_POST['selected_answer'];
        $is_correct = $_POST['is_correct']; // تأكد من أنه 1 أو 0
        $user_id = $_POST['user_id'];
        $exam_id = $_POST['exam_id'];

        $conn = new mysqli("localhost", "root", "", "OnlineExamDB");
        if ($conn->connect_error) {
            die("فشل الاتصال: " . $conn->connect_error);
        }

        $sql = "INSERT INTO Answers (question_text, selected_answer, is_correct, user_id, exam_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiii", $question_text, $selected_answer, $is_correct, $user_id, $exam_id);

        if ($stmt->execute()) {
            echo "تم تخزين الإجابة بنجاح.";
        } else {
            echo "خطأ في تخزين الإجابة: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "خطأ: البيانات غير مكتملة.";
    }
} else {
    echo "خطأ: طلب غير صالح.";
}
?>