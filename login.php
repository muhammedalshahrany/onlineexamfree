<?php
session_start(); 

// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "onlineexamdb");

// تحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // استخدم استعلام محضر (Prepared Statement)
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // تحقق من كلمة المرور
        if (password_verify($password, $row['password'])) {
            // تسجيل الجلسة
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];

            // توجيه المسؤول إلى صفحته
            if ($row['role'] === 'admin') {
                header("Location: admin_page.php");
                exit;
            } else {
                echo "ليس لديك صلاحيات كافية.";
            }
        } else {
            $error_message = "كلمة المرور غير صحيحة.";
        }
    } else {
        $error_message = "اسم المستخدم غير صحيح.";
    }

    $stmt->close();
}

// إغلاق الاتصال
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - منصة الامتحانات الإلكترونية</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            background-color: rgba(0, 0, 0, 0.8);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 40px rgba(0, 0, 0, 0.5);
            width: 350px;
            text-align: center;
        }
        h1 {
            font-size: 2em;
            margin-bottom: 20px;
        }
        .input-group {
            position: relative;
            margin: 15px 0;
        }
        input[type="text"],
        input[type="password"] {
            width: calc(100% - 40px);
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            background-color: #fff;
            color: #000;
        }
        .input-group i {
            position: absolute;
            left: 10px;
            top: 12px;
            color: #aaa;
        }
        .button {
            background-color: #007BFF;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.2em;
            transition: background-color 0.3s, transform 0.3s;
            width: 100%;
        }
        .button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }
        .link {
            color: white;
            text-decoration: none;
            margin-top: 15px;
            display: block;
            font-size: 0.9em;
        }
        .link:hover {
            text-decoration: underline;
        }
        .error-message {
            color: red;
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>تسجيل الدخول</h1>
        <form action="login.php" method="POST">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="اسم المستخدم" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="كلمة المرور" required>
            </div>
            <button type="submit" class="button">تسجيل الدخول</button> 
        </form>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <a href="register.php" class="link">إنشاء حساب جديد</a>
    </div>
</body>
</html>