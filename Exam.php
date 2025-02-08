<?php
session_start(); // بدء الجلسة

// إعداد قاعدة البيانات
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "OnlineExamDB"; 

// إنشاء اتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// الحصول على معرف الطالب من الجلسة
$user_id = $_SESSION['user_id'];
$user_full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'مستخدم غير معروف'; // استرجاع اسم المستخدم

$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0; // الحصول على exam_id من الرابط

// تحقق مما إذا كان الطالب قد اختبر هذا الامتحان مسبقًا
$sql_check = "SELECT * FROM Exam_Results WHERE user_id = ? AND exam_id = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("ii", $user_id, $exam_id);
$stmt->execute();
$result_check = $stmt->get_result();

if ($result_check->num_rows > 0) {
    echo "<script>alert('لقد قمت باختبار هذه المادة بالفعل.'); window.location.href='student_page.php';</script>";
    exit; // إنهاء السكربت
}

// استرجاع الأسئلة من قاعدة البيانات المرتبطة بالامتحان المحدد
$sql = "SELECT q.question_text, q.options, q.correct_answer, e.title AS exam_title 
        FROM Questions q 
        JOIN Exams e ON q.exam_id = e.exam_id 
        WHERE q.exam_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $exam_id); // استخدم معرف الامتحان
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
} else {
    echo "<script>alert('لا توجد أسئلة مرتبطة بهذا الامتحان.'); window.location.href='student_page.php';</script>";
    exit; // إنهاء السكربت إذا لم توجد أسئلة
}

// إغلاق الاتصال بعد الانتهاء
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>امتحان HTML</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to left, #6a11cb, #2575fc);
            color: white;
        }
        .container {
            max-width: 800px;
            min-height: 400px;
            margin: 40px auto;
            padding: 30px;
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        h1, h2 {
            margin-bottom: 20px;
        }
        .button {
            background-color: #007BFF;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin: 10px;
            font-size: 1.1em;
            width: 100%;
            max-width: 300px;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .timer {
            color: red;
            font-weight: bold;
            font-size: 1.2em;
            margin-bottom: 20px;
        }
        .alert {
            margin-top: 10px;
            color: white;
            font-weight: bold;
        }
        .result {
            display: none;
            text-align: center;
            margin: 0 auto;
        }
        .wrong-answer {
            color: red;
        }
        .right-answer {
            color: green;
        }
        .options {
            margin-top: 20px;
            text-align: center;
        }
        .option {
            display: block;
            margin: 10px 0;
            text-align: center;
        }
        .result-list {
            list-style-type: none;
            padding: 0;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>صفحة الامتحان </h1>
    <div class="timer" id="timer">ثواني: 60</div>
    
    <!-- عرض اسم الطالب فقط -->
    <div style="text-align: right; margin-top: -20px;">
        <p>اسم الطالب: <?php echo htmlspecialchars($user_full_name); ?></p>
        <p>اسم الامتحان: <?php echo htmlspecialchars($questions[0]['exam_title']); ?></p>
    </div>
    
    <div class="question" id="question-container"></div>
    <div class="alert" id="alert-message"></div>
    <div id="result" class="result">
        <h2>نتيجة الاختبار</h2>
        <p id="score"></p>
        <h3>الإجابات الخاطئة:</h3>
        <ul id="wrong-answers" class="result-list"></ul>
        <div style="text-align: center;">
            <button class="button" onclick="window.location.href='feedback.php'">قيمنا</button>
            <button class="button" onclick="window.location.href='previous_results.php'">الاطلاع على النتائج</button>
            <button class="button" onclick="window.location.href='student_page.php'">العودة للصفحة الرئيسية</button>
            <button class="button" onclick="window.location.href='logout.php'">تسجيل الخروج</button>
        </div>
    </div>
</div>

<script>
    const questions = <?php echo json_encode($questions); ?>;
    let currentQuestionIndex = 0;
    let timeLeft = 60;
    let timerInterval;
    let wrongAnswers = [];
    const userId = <?php echo json_encode($user_id); ?>;
    const exam_id = <?php echo json_encode($exam_id); ?>;

    // التأكد من إظهار إشعار عند الخروج أو العودة
    window.onbeforeunload = function() {
        return 'إذا قمت بالخروج، سيتم اعتبارك قد اختبرت الامتحان. هل أنت متأكد أنك تريد الخروج؟';
    };

    function startExam() {
        displayQuestion();
        history.pushState(null, null, location.href); // تحديث تاريخ المتصفح
        window.onpopstate = function () {
            history.pushState(null, null, location.href); // منع العودة
        };
    }

    function displayQuestion() {
        clearInterval(timerInterval);
        timeLeft = 60;
        document.getElementById('timer').textContent = `ثواني: ${timeLeft}`;
        document.getElementById('alert-message').textContent = '';

        if (currentQuestionIndex < questions.length) {
            const questionContainer = document.getElementById('question-container');
            const question = questions[currentQuestionIndex];

            // تحليل الخيارات من JSON
            const options = JSON.parse(question.options);
            let optionsHtml = Object.keys(options).map(key => `
                <div class="option">
                    <input type="radio" id="${key}" name="answer" value="${key}">
                    <label for="${key}">${options[key]}</label>
                </div>
            `).join('');

            questionContainer.innerHTML = `
                <h2>السؤال ${currentQuestionIndex + 1}: ${question.question_text}</h2>
                <div class="options">${optionsHtml}</div>
                <button class="button" onclick="submitAnswer()">إرسال</button>
            `;

            resetTimer();
        } else {
            endExam();
        }
    }

    function resetTimer() {
        clearInterval(timerInterval);
        timeLeft = 60;
        document.getElementById('timer').textContent = `ثواني: ${timeLeft}`;
        timerInterval = setInterval(() => {
            timeLeft--;
            document.getElementById('timer').textContent = `ثواني: ${timeLeft}`;
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                document.getElementById('alert-message').textContent = 'انتهى الوقت! الانتقال إلى السؤال التالي.';
                wrongAnswers.push({ question: questions[currentQuestionIndex].question_text, correctAnswer: questions[currentQuestionIndex].correct_answer });
                currentQuestionIndex++;
                setTimeout(displayQuestion, 2000);
            }
        }, 1000);
    }

    function submitAnswer() {
        const selectedOption = document.querySelector('input[name="answer"]:checked');
        const alertMessage = document.getElementById('alert-message');

        if (selectedOption) {
            const userAnswer = selectedOption.value;
            const correctAnswer = questions[currentQuestionIndex].correct_answer;

            // تخزين الإجابة في قاعدة البيانات
            storeAnswer(questions[currentQuestionIndex].question_text, userAnswer, correctAnswer);

            if (userAnswer === correctAnswer) {
                alertMessage.textContent = 'إجابة صحيحة!';
                alertMessage.style.color = 'green';
            } else {
                alertMessage.textContent = `إجابة خاطئة! الإجابة الصحيحة هي: ${correctAnswer}`;
                alertMessage.style.color = 'red';
                wrongAnswers.push({ question: questions[currentQuestionIndex].question_text, correctAnswer });
            }

            currentQuestionIndex++;
            setTimeout(displayQuestion, 2000);
        } else {
            alertMessage.textContent = 'يرجى اختيار إجابة.';
            alertMessage.style.color = 'red';
        }
    }

    function storeAnswer(questionText, selectedAnswer, correctAnswer) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "store_answer.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    console.log(xhr.responseText);
                } else {
                    console.error("خطأ في إرسال البيانات: " + xhr.status);
                }
            }
        };
        const data = `question_text=${encodeURIComponent(questionText)}&selected_answer=${encodeURIComponent(selectedAnswer)}&is_correct=${selectedAnswer === correctAnswer ? 1 : 0}&user_id=${userId}&exam_id=${exam_id}`;
        console.log("إرسال البيانات: ", data);
        xhr.send(data);
    }

    function endExam() {
        clearInterval(timerInterval);
        document.getElementById('question-container').style.display = 'none';
        document.getElementById('timer').style.display = 'none';
        document.getElementById('alert-message').style.display = 'none';

        const score = ((currentQuestionIndex - wrongAnswers.length) / questions.length * 100).toFixed(2);
        const totalQuestions = questions.length;
        const correctAnswers = currentQuestionIndex - wrongAnswers.length;
        const wrongAnswersCount = wrongAnswers.length;

        // تخزين النتيجة في قاعدة البيانات
        storeExamResult(score, totalQuestions, correctAnswers, wrongAnswersCount);

        document.getElementById('score').textContent = `لقد حصلت على: ${score}%`;

        // عرض الإجابات الخاطئة
        const wrongAnswersList = document.getElementById('wrong-answers');
        wrongAnswers.forEach(item => {
            const li = document.createElement('li');
            li.innerHTML = `<span class="wrong-answer">${item.question}</span> (الإجابة الصحيحة: <span class="right-answer">${item.correctAnswer}</span>)`;
            wrongAnswersList.appendChild(li);
        });

        document.getElementById('result').style.display = 'block';
    }

    function storeExamResult(score, totalQuestions, correctAnswers, wrongAnswers) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "store_exam_result.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    console.log(xhr.responseText);
                } else {
                    console.error("خطأ في إرسال البيانات: " + xhr.status);
                }
            }
        };
        xhr.send(`user_id=${userId}&exam_id=${exam_id}&score=${score}&total_questions=${totalQuestions}&correct_answers=${correctAnswers}&wrong_answers=${wrongAnswers}&ignored_answers=0&duration=1`);
    }

    startExam();
</script>

</body>
</html>