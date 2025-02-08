<?php
session_start();
$conn = new mysqli("localhost", "root", "", "onlineexamdb");

if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        // تشفير كلمة المرور
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // تحديث كلمة المرور في قاعدة البيانات
        $stmt = $conn->prepare("UPDATE Users SET password=? WHERE email=?");
        $stmt->bind_param("ss", $hashed_password, $_SESSION['email']);
        
        if ($stmt->execute()) {
            $message = "تم تغيير كلمة المرور بنجاح.";
            session_destroy(); // إنهاء الجلسة
        } else {
            $message = "فشل تغيير كلمة المرور.";
        }
        $stmt->close();
    } else {
        $message = "كلمات المرور لا تتطابق.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تغيير كلمة المرور</title>
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
        input[type="password"] {
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
        <h1>تغيير كلمة المرور</h1>
        <form action="change_password.php" method="POST">
            <input type="password" name="new_password" placeholder="كلمة المرور الجديدة" required>
            <input type="password" name="confirm_password" placeholder="تأكيد كلمة المرور" required>
            <button type="submit" class="button">تغيير</button>
        </form>
        <?php if (!empty($message)): ?>
            <div class="error-message"><?php echo $message; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>