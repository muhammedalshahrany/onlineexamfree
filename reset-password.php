<?php
session_start();
$conn = new mysqli("localhost", "root", "", "onlineexamdb");

if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    
    // تحقق من وجود البريد الإلكتروني في قاعدة البيانات
    $stmt = $conn->prepare("SELECT * FROM Users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // توليد كود تحقق عشوائي
        $verification_code = rand(100000, 999999);
        
        // تخزين كود التحقق في الجلسة
        $_SESSION['verification_code'] = $verification_code;
        $_SESSION['email'] = $email;

        // إرسال البريد الإلكتروني
        $subject = "كود إعادة تعيين كلمة المرور";
        $message_body = "استخدام الكود التالي لإعادة تعيين كلمة المرور: " . $verification_code;
        $headers = "From: alshhranymuha39@gmail.com";

        // تحقق من نجاح إرسال البريد
        if (mb_send_mail($email, $subject, $message_body, $headers)) {
            header("Location: enter_verification_code.php");
            exit;
        } else {
            $message = "فشل إرسال البريد الإلكتروني. حاول مرة أخرى.";
        }
    } else {
        $message = "هذا البريد الإلكتروني غير مسجل.";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعادة تعيين كلمة المرور</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(to left, #6a11cb, #2575fc);
            color: white;
        }
        .container {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            width: 300px;
            text-align: center;
        }
        h1 {
            font-size: 1.8em;
            margin-bottom: 20px;
        }
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            font-size: 1em;
        }
        .button {
            background-color: #007BFF;
            color: white;
            padding: 10px;
            margin-top: 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.2em;
            transition: background-color 0.3s, transform 0.3s;
        }
        .button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        .error-message {
            color: red;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>إعادة تعيين كلمة المرور</h1>
        <form action="reset-password.php" method="POST"> 
            <input type="email" name="email" placeholder="البريد الإلكتروني" required>
            <button type="submit" class="button">إعادة تعيين</button> 
        </form>
        <?php if (!empty($message)): ?>
            <div class="error-message"><?php echo $message; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>