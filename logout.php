<?php
session_start(); // بدء الجلسة

// إنهاء الجلسة
session_unset(); // إزالة جميع المتغيرات من الجلسة
session_destroy(); // تدمير الجلسة

// إعادة التوجيه إلى صفحة تسجيل الدخول
header("Location: login.php");
exit;
?>