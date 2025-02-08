<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صفحة البحث</title>
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
            margin: 40px auto;
            padding: 30px;
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
        }
        .input-group {
            margin-bottom: 20px;
        }
        .input-group input {
            padding: 10px;
            width: 70%;
            border: none;
            border-radius: 5px;
        }
        .button {
            background-color: #007BFF;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .results {
            margin-top: 20px;
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

<div class="container">
    <h1> بحث عن : </h1>
    <div class="input-group">
        <input type="text" id="search-input" placeholder="أدخل الكلمات المفتاحية للبحث...">
        <button class="button" onclick="performSearch()">بحث</button>
    </div>
    <div class="results" id="search-results"></div>
</div>

<button class="button" style="display: block; margin: 20px auto;" onclick="goHome()">العودة للصفحة الرئيسية</button>

<script>
    function performSearch() {
        const query = document.getElementById('search-input').value;
        const resultsContainer = document.getElementById('search-results');

        const sampleData = [
           'لا نستطيع توفير نتائج بحث في موضوع معين حتى نبرمج هذا الحقل بلغة تستطيع جلب البيانات من قواعد البيانات او من الموقع نفسه',
           'سيتم تفعيل خيار البحث حالما ننتهي من بناء مشروع متكامل وتجربته معكم بإذن الله'
        ];

        const results = sampleData.filter(item => item.includes(query));
        resultsContainer.innerHTML = '';

        if (results.length > 0) {
            results.forEach(result => {
                const div = document.createElement('div');
                div.textContent = result;
                resultsContainer.appendChild(div);
            });
        } else {
            resultsContainer.textContent = 'لا توجد نتائج مطابقة.';
        }
    }

    function goHome() {
        window.location.href = 'P1.php';  
    }
</script>

</body>
</html>