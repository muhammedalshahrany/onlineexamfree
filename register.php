<?php
// إعدادات قاعدة البيانات
$conn = new mysqli("localhost", "root", "", "onlineexamdb");

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

$error_message = "";
$success_message = ""; 
$username = ""; 
$email = ""; 
$role = ""; 
$full_name = "";
$phone = "";
$gender = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];
    $email = $conn->real_escape_string($_POST['email']);
    $role = $conn->real_escape_string($_POST['role']);
    $gender = $conn->real_escape_string($_POST['gender']); 
   
    if ($pass !== $confirm_pass) {
        $error_message = "كلمات المرور لا تتطابق.";
    } else {
        // تحقق من وجود اسم المستخدم
        $check_query = "SELECT * FROM users WHERE username='$username' OR email='$email'";
        $result = $conn->query($check_query);

        if ($result->num_rows > 0) {
            $error_message = "اسم المستخدم أو البريد الإلكتروني موجود بالفعل.";
        } else {
            // تحقق إذا كان الدور هو مسؤول
            if ($role == "admin" && ($username !== "your_admin_username" || $pass !== "your_admin_password")) {
                $error_message = "لا يمكنك إنشاء حساب كمسؤول.";
            } else {
                // تشفير كلمة المرور
                $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
                
                // إدخال البيانات في قاعدة البيانات باستخدام استعلام محضر
                $stmt = $conn->prepare("INSERT INTO users (full_name, username, password, email, role, phone, gender) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $full_name, $username, $hashed_pass, $email, $role, $phone, $gender);

                if ($stmt->execute()) {
                    $success_message = "تم إنشاء حسابك بنجاح! يمكنك الآن تسجيل الدخول.";
                    echo '<script>window.location.href="login.php";</script>';
                } else {
                    $error_message = "فشل إنشاء الحساب: " . $conn->error;
                }
            }
        }
    }
}

// إغلاق الاتصال
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب جديد - منصة الامتحانات الإلكترونية</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; height: 100vh; background: linear-gradient(to left, #6a11cb, #2575fc); color: white; }
        .container { background-color: rgba(0, 0, 0, 0.8); padding: 40px; border-radius: 10px; box-shadow: 0 4px 40px rgba(0, 0, 0, 0.5); width: 350px; text-align: center; }
        h1 { font-size: 2em; margin-bottom: 20px; }
        .input-group { position: relative; margin: 15px 0; }
        input[type="text"], input[type="password"], input[type="email"], select { width: calc(100% - 40px); padding: 12px 20px; border: none; border-radius: 5px; font-size: 1em; }
        .input-group i { position: absolute; left: 10px; top: 12px; color: #aaa; }
        .button { background-color: #007BFF; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 1.2em; transition: background-color 0.3s, transform 0.3s; width: 100%; }
        .button:hover { background-color: #0056b3; transform: scale(1.05); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5); }
        .link { color: white; text-decoration: none; margin-top: 15px; display: block; font-size: 0.9em; }
        .link:hover { text-decoration: underline; }
        .error-message { color: red; text-align: center; margin-top: 15px; }
        .success-message { color: green; text-align: center; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>إنشاء حساب جديد</h1>
        <form action="register.php" method="POST">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="اسم المستخدم" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>
            <div class="input-group">
                <i class="fas fa-user-circle"></i>
                <input type="text" name="full_name" placeholder="الاسم الكامل" value="<?php echo htmlspecialchars($full_name); ?>" required>
            </div>
            <div class="input-group">
                <i class="fas fa-phone"></i>
                <input type="text" name="phone" placeholder="رقم الهاتف" value="<?php echo htmlspecialchars($phone); ?>" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="كلمة المرور" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" placeholder="تأكيد كلمة المرور" required>
            </div>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="الايميل" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="input-group">
                <select name="role" required>
                    <option value="" disabled selected>اختر الدور</option>
                    <option value="student">طالب</option>
                    <option value="teacher">معلم</option>
                    <option value="admin">مسؤول</option>
                </select>
            </div>
            <div class="input-group">
                <select name="gender" required>
                    <option value="" disabled selected>اختر الجنس</option>
                    <option value="male">ذكر</option>
                    <option value="female">أنثى</option>
                </select>
            </div>
            <button type="submit" class="button">إنشاء حساب</button> 
        </form>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <a href="login.php" class="link">لديك حساب بالفعل؟ تسجيل الدخول</a>
    </div>
</body>
</html>