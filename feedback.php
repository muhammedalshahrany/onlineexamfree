<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقييمنا</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #282c34;
            color: white;
            font-family: 'Arial', sans-serif;
            flex-direction: column;
            position: relative;
            user-select: none; /* منع تحديد النص */
            transition: background-color 0.5s; /* تأثير الانتقال للخلفية */
        }
        h1 { margin-bottom: 10px; font-size: 36px; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7); }
        .subtitle { margin-bottom: 20px; font-size: 20px; color: #ccc; text-align: center; }
        .heart, .like-button { font-size: 100px; cursor: pointer; transition: transform 0.1s, color 0.3s; margin: 0 20px; }
        .heart:hover, .like-button:hover { transform: scale(1.1); }
        .heart.active { color: red; }
        .like-button.active { color: blue; }
        .count { position: absolute; top: 0; right: 0; background: rgba(0, 0, 0, 0.7); border-radius: 50%; padding: 5px 10px; font-size: 20px; color: white; }
        .rating-input { margin-top: 20px; padding: 10px; border-radius: 5px; border: none; width: 400px; font-size: 18px; resize: both; overflow: auto; transition: border 0.3s; }
        .rating-input:focus { border: 2px solid #007BFF; outline: none; }
        .button { margin-top: 10px; padding: 10px 20px; background-color: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s, transform 0.2s; }
        .button:hover { background-color: #0056b3; }
        .message { display: none; margin-top: 20px; font-size: 24px; color: #ffcc00; text-align: center; text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7); }
        .navigation-buttons { display: none; margin-top: 20px; }
    </style>
</head>
<body>

<h1>✨ تقييمنا ✨</h1>
<p class="subtitle">شاركنا رأيك بقدر ما ترغب! كل ضغطة تعبر عن مدى رضائك تجاه منصتنا.</p>

<form id="rating-form" method="POST" action="command.php">
    <div>
        <div>
            <i class="fas fa-heart heart" id="heart">
                <span id="heart-count" class="count">0</span>
            </i>
        </div>
        <div>
            <i class="fas fa-thumbs-up like-button" id="like-button">
                <span id="like-count" class="count">0</span>
            </i>
        </div>
    </div>
    <textarea class="rating-input" id="rating" name="rating" placeholder="أدخل تقييمك هنا"></textarea>

    <!-- حقول مخفية لتخزين القيم -->
    <input type="hidden" id="heart_count" name="heart_count" value="0">
    <input type="hidden" id="like_count" name="like_count" value="0">

    <button class="button" type="submit" id="submit">إرسال</button>
</form>

<!-- الرسالة بعد الإرسال -->
<div class="message" id="reward-message">
   ان عدد الضغطات التي شاركتنا بها وتعليقك سيكون لها اثر طيب فينا وسنمنح اختبارات مجانيه من خلال التقييم باكثر عدد من الضغطات
</div>
<div class="message" id="thank-you-message">
    شكرًا لك على تقييمك! ✨
</div>

<div class="navigation-buttons" id="navigation-buttons">
    <a href="student_page.php" class="button">العودة إلى الصفحة الرئيسية</a>
    <a href="logout.php" class="button">تسجيل الخروج</a>
</div>

<script>
    let heartCount = 0; 
    let likeCount = 0;

    const updateCountDisplay = (element, count) => {
        element.textContent = count;   
    };

    document.getElementById('heart').addEventListener('click', () => {
        heartCount++;
        document.getElementById('heart_count').value = heartCount; // تحديث القيمة
        const heart = document.getElementById('heart');
        heart.classList.add('active'); // تغيير لون القلب إلى الأحمر
        document.body.style.backgroundColor = 'rgba(255, 0, 0, 0.1)'; // تغيير لون الخلفية
        setTimeout(() => {
            document.body.style.backgroundColor = '#282c34'; // إعادة اللون الأصلي
        }, 300);
        updateCountDisplay(document.getElementById('heart-count'), heartCount); // تحديث عدد الضغطات
    });

    document.getElementById('like-button').addEventListener('click', () => {
        likeCount++;
        document.getElementById('like_count').value = likeCount; // تحديث القيمة
        const likeButton = document.getElementById('like-button');
        likeButton.classList.add('active'); // تغيير لون زر الإعجاب إلى الأزرق
        document.body.style.backgroundColor = 'rgba(0, 0, 255, 0.1)'; // تغيير لون الخلفية
        setTimeout(() => {
            document.body.style.backgroundColor = '#282c34'; // إعادة اللون الأصلي
        }, 300);
        updateCountDisplay(document.getElementById('like-count'), likeCount); // تحديث عدد الضغطات
    });

    // عرض الرسالة بعد إرسال التقييم
    const form = document.getElementById('rating-form');
    form.addEventListener('submit', (event) => {
        event.preventDefault(); // منع الإرسال الافتراضي للنموذج
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('reward-message').style.display = 'block'; // عرض رسالة المكافأة
            document.getElementById('thank-you-message').style.display = 'block'; // عرض رسالة الشكر
            document.getElementById('navigation-buttons').style.display = 'block'; // عرض الأزرار
        })
        .catch(error => console.error('Error:', error));
    });
</script>

</body>
</html>