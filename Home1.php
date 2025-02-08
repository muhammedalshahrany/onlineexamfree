<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام الامتحانات الإلكترونية</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style id="theme-style">
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
            position: relative;
        }
        header::after {
            content: '';
            display: block;
            position: absolute;
            left: 50%;
            bottom: -10px;
            width: 80px;
            height: 5px;
            background: #ffcc00;
            border-radius: 5px;
            transform: translateX(-50%);
        }
        nav {
            margin-top: 10px; 
        }
        nav a {
            color: #ffcc00; 
            text-decoration: none; 
            margin: 0 15px;
            font-weight: bold; 
            transition: color 0.3s, transform 0.3s;
            font-size: 20px; 
        }
        nav a:hover {
            color: #fff; 
            transform: scale(1.1); 
        }
        .container {
            max-width: 95%;
            margin: 40px auto;
            padding: 30px;
            background-color: rgba(0, 0, 0, 0.9);
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
        }
        h1, h2 {
            margin-bottom: 20px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
        }
        .button {
            background-color: #ffcc00;
            color: black;
            padding: 10px 20px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            margin: 20px 0; 
            display: inline-block;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
        }
        .button:hover {
            background-color: #ffd700; 
            transform: scale(1.05);
        }
        footer {
            background-color: rgba(0, 0, 0, 0.9);
            padding: 20px;
            text-align: center;
            border-top: 2px solid #ffcc00;
            margin-top: 20px;
            position: relative;
        }
        footer::after {
            content: '';
            display: block;
            position: absolute;
            left: 50%;
            top: -10px;
            width: 80px;
            height: 5px;
            background: #ffcc00;
            border-radius: 5px;
            transform: translateX(-50%);
        }
        .footer-links {
            margin-top: 10px;
        }
        .footer-links a {
            color: #ffcc00;
            text-decoration: none;
            margin: 0 10px;
            transition: color 0.3s;
        }
        .footer-links a:hover {
            color: #fff;
            text-decoration: underline;
        }
        .articles {
            margin: 20px 0;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 10px;
            transition: transform 0.3s;
            max-width: 95%;
            margin-left: auto; 
            margin-right: auto; 
        }
        .articles h3 {
            margin-bottom: 10px;
            color: #ffcc00;
        }
        .article {
            margin: 10px 0;
            text-align: left;
        }
        .article p {
            margin: 5px 0;
        }

        @media (max-width: 600px) {
}

@media (min-width: 601px) and (max-width: 900px) {
}

@media (min-width: 901px) {
}
    </style>
</head>
<body>
    <header>
        <h2>نظام الامتحانات الإلكترونية</h2>
        <nav>
            <a href="P1.html"><i class="fas fa-home"></i> الرئيسية</a>
            <a href=""><i class="fas fa-list"></i> المواضيع</a>
            <a href="search.html"><i class="fas fa-search"></i> بحث</a>
            <a href="courses.html"><i class="fas fa-chalkboard-teacher"></i> الدورات</a>
            <a  href="articles.html"><i class="fas fa-newspaper"></i> المقالات</a>
            <a href="about.html"><i class="fas fa-info-circle"></i> حولنا</a>
            <a href="contact.html"><i class="fas fa-phone-alt"></i> اتصل بنا</a>
            <a href="services.html"><i class="fas fa-cogs"></i> الخدمات</a>
            <a href="feedback.html"><i class="fas fa-star"></i> قيمنا</a> <!-- أيقونة قيمنا -->
        </nav>
    </header>

    <div class="container">
        <a href="student_page.php" class="button">اضغط هنا للدخول في منصتنا</a>
        
        <h1>مرحبًا بك في نظام الامتحانات الإلكترونية</h1>
        <p>نحن نقدم لك منصة متكاملة لإجراء الامتحانات الإلكترونية بسهولة وأمان.</p>

        <h2>المقالات</h2>
        <div class="articles">
            <div class="article">
                <h3>كيف تجهز نفسك للاختبارات الإلكترونية؟</h3>
                <p>نصائح مهمة للطلاب للنجاح في الامتحانات الإلكترونية، بما في ذلك كيفية إدارة الوقت بفعالية...</p>
            </div>
            <div class="article">
                <h3>فوائد استخدام نظام الامتحانات الإلكترونية</h3>
                <p>تعرف على المزايا التي يوفرها النظام التعليمي الإلكتروني للمدارس والطلاب...</p>
            </div>
            <div class="article">
                <h3>دليل المعلم لإنشاء امتحانات فعالة</h3>
                <p>استراتيجيات لإنشاء امتحانات تساعد على تقييم الطلاب بشكل شامل وتحسين أدائهم...</p>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-links">
            <a href="terms.html">شروط الاستخدام</a>
            <a href="privacy.html">سياسة الخصوصية</a>
        </div>
        <p>&copy; نظام موقع منصة امتحانات الكترونية للعام <?php echo gmdate("Y") ?> &nbsp; جميع الحقوق محفوظة</p>
    </footer>

    <script>
       
    </script>
</body>
</html>