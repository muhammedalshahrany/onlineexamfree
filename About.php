<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حولنا - نظام الامتحانات الإلكترونية</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            line-height: 1.6;
        }
        header {
            background-color: rgba(0, 0, 0, 0.85);
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.5);
        }
        nav a {
            color: #ffcc00;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
            transition: color 0.3s;
        }
        nav a:hover {
            color: #fff;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: rgba(0, 0, 0, 0.9);
            border-radius: 15px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        footer {
            background-color: rgba(0, 0, 0, 0.9);
            padding: 20px;
            text-align: center;
            border-top: 2px solid #ffcc00;
        }
        .footer-links a {
            color: #ffcc00;
            text-decoration: none;
            margin: 0 10px;
        }
        .hidden {
            display: none;
        }
        @media (max-width: 600px) {
    /* أنماط للهواتف المحمولة */
}

@media (min-width: 601px) and (max-width: 900px) {
    /* أنماط للأجهزة اللوحية */
}

@media (min-width: 901px) {
    /* أنماط لأجهزة الكمبيوتر */
}
    </style>
</head>
<body>
    <header>
        <h2>نظام الامتحانات الإلكترونية</h2>
        <nav>
            <a href="P1.php"><i class="fas fa-home"></i> الرئيسية</a>
            <a href="topics.php"><i class="fas fa-list"></i> المواضيع</a>
            <a href="search.php"><i class="fas fa-search"></i> بحث</a>
            <a href="courses.php"><i class="fas fa-chalkboard-teacher"></i> الدورات</a>
            <a href="articles.php"><i class="fas fa-newspaper"></i> المقالات</a>
            <a href="about.php"><i class="fas fa-info-circle"></i> حولنا</a>
            <a href="contact.php"><i class="fas fa-phone-alt"></i> اتصل بنا</a>
            <a href="services.php"><i class="fas fa-cogs"></i> الخدمات</a>
            <a href="feedback.php"><i class="fas fa-star"></i> قيمنا</a> 
        </nav>
    </header>

    <div class="container">
        <h1>حولنا</h1>
        <p>نحن شركة متخصصة في تقديم حلول التعليم الإلكتروني، نهدف إلى تسهيل عملية إجراء الامتحانات وتقديم تجربة تعليمية متميزة.</p>
        <p>فريقنا يتكون من مجموعة من الخبراء في مجالات التعليم والتكنولوجيا، ونحرص على تطوير منصتنا لتلبية احتياجات المستخدمين بشكل مستمر.</p>
        <button id="infoButton" style="background-color: #ffcc00; color: black; border: none; border-radius: 5px; padding: 10px 20px; cursor: pointer;">أظهر مزيد من المعلومات</button>
        <div id="moreInfo" class="hidden">
            <p>نحن نستخدم أحدث التقنيات لضمان أمان وموثوقية المنصة.</p>
        </div>
    </div>

    <footer>
        <div class="footer-links">
            <a href="terms.html">شروط الاستخدام</a>
            <a href="privacy.html">سياسة الخصوصية</a>
            <a href="contact.php">معلومات الاتصال</a>
        </div>
        <p>&copy; 2024 نظام الامتحانات الإلكترونية. جميع الحقوق محفوظة.</p>
    </footer>

    <script>
        document.getElementById('infoButton').onclick = function() {
            const info = document.getElementById('moreInfo');
            info.classList.toggle('hidden');
        };
    </script>
</body>
</html>