<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['user_id'], $_POST['exam_id'], $_POST['score'], $_POST['total_questions'], $_POST['correct_answers'], $_POST['wrong_answers'], $_POST['ignored_answers'], $_POST['duration'])) {
        $user_id = $_POST['user_id'];
        $exam_id = $_POST['exam_id'];
        $score = $_POST['score'];
        $total_questions = $_POST['total_questions'];
        $correct_answers = $_POST['correct_answers'];
        $wrong_answers = $_POST['wrong_answers'];
        $ignored_answers = $_POST['ignored_answers'];
        $duration = $_POST['duration'];

        $conn = new mysqli("localhost", "root", "", "OnlineExamDB");
        if ($conn->connect_error) {
            die("فشل الاتصال: " . $conn->connect_error);
        }

        $sql = "INSERT INTO Exam_Results (exam_id, user_id, score, total_questions, correct_answers, wrong_answers, ignored_answers, duration) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiddiiii", $exam_id, $user_id, $score, $total_questions, $correct_answers, $wrong_answers, $ignored_answers, $duration);

        if ($stmt->execute()) {
            echo "تم تخزين النتائج بنجاح.";
        } else {
            echo "خطأ في تخزين النتائج: " . $stmt->error;
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