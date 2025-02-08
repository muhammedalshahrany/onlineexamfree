<?php
session_start();

// تحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// تخزين معلومات المستخدم من الجلسة
$user_id = $_SESSION['user_id'];
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : '';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$phone = isset($_SESSION['phone']) ? $_SESSION['phone'] : '';
$gender = isset($_SESSION['gender']) ? $_SESSION['gender'] : '';

// إعداد قاعدة البيانات
$servername = "localhost";
$username_db = "root"; // يجب تعديلها إذا كان لديك اسم مستخدم آخر
$password_db = ""; // كلمة المرور
$dbname = "OnlineExamDB"; // اسم قاعدة البيانات

// إنشاء اتصال
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// معالجة عملية التعديل
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // استلام البيانات من النموذج
    $new_full_name = $_POST['full_name'];
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
   
    $new_phone = $_POST['phone'];
    $new_gender = $_POST['gender'];

    // تحديث معلومات المستخدم في قاعدة البيانات
    $sql = "UPDATE Users SET full_name = ?, username = ?, email = ?, phone = ?, gender = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $new_full_name, $new_username, $new_email, $new_phone, $new_gender, $user_id);

    if ($stmt->execute()) {
        // تحديث معلومات الجلسة
        $_SESSION['full_name'] = $new_full_name;
        $_SESSION['username'] = $new_username;
        $_SESSION['email'] = $new_email;
        $_SESSION['phone'] = $new_phone;
        $_SESSION['gender'] = $new_gender;

        // إعادة التوجيه إلى صفحة معلومات المستخدم مع رسالة نجاح
        header("Location: user_info.php?message=تم تحديث المعلومات بنجاح");
        exit;
    } else {
        echo "خطأ في التحديث: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close(); // إغلاق الاتصال بعد الانتهاء
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل معلومات المستخدم</title>
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
            transition: 0.3s;
        }
        .container:hover {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select {
            width: 96%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #007BFF;
            font-size: 1em;
            text-align: right;
        }
        .button {
            display: block;
            width: 96%;
            padding: 15px;
            margin: 10px 0;
            text-align: center;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s;
            text-decoration: none;
        }
       
        .button:hover {
            background-color: #0056b3;
        }
        .a{
            text-decoration: none;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>تعديل معلومات المستخدم</h1>
        
        <form method="POST">
            <label>الاسم الكامل:</label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>
            <label>اسم المستخدم:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
            <label>البريد الإلكتروني:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            <label>رقم الهاتف:</label>
            <input type="tel" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
            <label>الجنس:</label>
            <select name="gender" required>
                <option value="ذكر" <?php echo ($gender === 'ذكر') ? 'selected' : ''; ?>>ذكر</option>
                <option value="أنثى" <?php echo ($gender === 'أنثى') ? 'selected' : ''; ?>>أنثى</option>
                <option value="غير محدد" <?php echo ($gender === 'غير محدد') ? 'selected' : ''; ?>>غير محدد</option>
            </select>
            <button type="submit" class="button" style="width: 99%;">حفظ التعديلات</button>
        </form>
<div>
        <a href="user_info.php" class="button">العودة إلى معلومات المستخدم</a>
        <a href="logout.php" class="button">تسجيل الخروج</a>
        </div>
        <div class="footer">
            <p>&copy;  جميع الحقوق محفوظة. <?php echo date("Y")?></p>
        </div>
    </div>
</body>
</html>