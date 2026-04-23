<?php
include 'db_connection.php';
?>
<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
http_response_code(200);
?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A2Z browser home</title>
    <style>
        :root {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #0f172a;
        }
        body {
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(180deg, #003cff 0%, #00ed6d 40%, #00d358 100%);
            color: white;
            overflow-y: auto;
            overflow-x: auto;
        }
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 32px;
            background: rgba(0, 0, 0, 0.28);
            backdrop-filter: blur(12px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18);
            z-index: 10;
        }
        .brand {
            font-size: 1.3rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
        .nav-links {
            display: flex;
            gap: 16px;
        }
        .nav-links a {
            padding: 12px 18px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.16);
            color: white;
            text-decoration: none;
            font-weight: 700;
            transition: transform 0.2s ease, background-color 0.2s ease;
        }
        .nav-links a:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.4);
        }
        .hero {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding-top: 110px;
        }
        .hero-card {
            width: min(780px, 92vw);
            padding: 40px 36px;
            border-radius: 32px;
            background: rgba(255,255,255,0.14);
            box-shadow: 0 32px 90px rgba(0,0,0,0.14);
            text-align: center;
            backdrop-filter: blur(14px);
        }
        .hero-card h1 {
            margin: 0;
            font-size: clamp(2.8rem, 4vw, 4.2rem);
            line-height: 1;
        }
        .hero-card p {
            margin: 24px auto 0;
            max-width: 640px;
            color: rgba(255,255,255,0.92);
            font-size: 1.05rem;
            line-height: 1.8;
        }
        .task-list {
            margin-top: 32px;
            display: grid;
            gap: 12px;
            color: rgba(255,255,255,0.92);
            text-align: right;
        }
        .task-list li {
            list-style: none;
            padding-left: 24px;
            position: relative;
        }
        .task-list li::before {
            content: '?';
            position: absolute;
            left: 0;
            color: #d9ffdd;
        }
        .call-to-action {
            margin-top: 36px;
            display: inline-block;
            padding: 16px 28px;
            border-radius: 999px;
            background: white;
            color: #00a65d;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .call-to-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 34px rgba(0,0,0,0.14);
        }
        .search-form {
            margin-top: 32px;
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .search-form input[type="search"] {
            width: min(500px, 100%);
            padding: 14px 18px;
            border-radius: 999px;
            border: none;
            outline: none;
            font-size: 1rem;
        }
        .search-form button {
            padding: 14px 28px;
            border: none;
            border-radius: 999px;
            background: #ffffff;
            color: #00a65d;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .search-form button:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.16);
        }
        .task-list-title {
            margin-top: 24px;
            font-size: 1rem;
            color: rgba(255,255,255,0.88);
        }
        .auth-cards {
            margin-top: 32px;
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }
        .auth-card {
            padding: 20px 22px;
            border-radius: 24px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.18);
            text-align: center;
        }
        .auth-card h2 {
            margin: 0 0 14px;
            font-size: 1.15rem;
            color: white;
        }
        .auth-card a {
            display: inline-block;
            margin-top: 12px;
            padding: 12px 24px;
            border-radius: 999px;
            background: #ffffff;
            color: #00a65d;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .auth-card a:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.16);
        }
    </style>
</head>
<body>
    <header>
        <div class="brand">A2Z browser</div>
        <nav class="nav-links">
            <a href="login.php">تسجيل الدخول</a>
            <a href="logout.php">تسجيل الخروج</a>
            <a href="creatnewaccount.php">إنشاء حساب</a>
        </nav>
    </header>
    <main class="hero">
        <section class="hero-card">
            <h1>مرحبا بك في A2Z browser</h1>
            <p>ابحث عن المحتوى المطلوب بسرعة وسهولة باستخدام محرك البحث الخاص بنا.</p>
            <form action="search.php" method="get" class="search-form">
                <input type="search" name="q" placeholder="اكتب كلمة البحث هنا..." aria-label="بحث" required>
                <button type="submit">بحث</button>
            </form>
            <p class="task-list-title">يمكنك البحث عن:</p>
            <ul class="task-list">
                <li>مقالات</li>
                <li>روابط</li>
                <li>مواضيع</li>
            </ul>
            <div class="auth-cards">
                <div class="auth-card">
                    <h2>تسجيل الدخول</h2>
                    <p>ادخل إلى حسابك الموجود بالفعل للوصول إلى لوحة التحكم.</p>
                    <a href="login.php">اذهب لتسجيل الدخول</a>
                </div>
                <div class="auth-card">
                    <h2>إنشاء حساب</h2>
                    <p>أنشئ حساب جديد لتتمكن من استخدام خدمات الموقع.</p>
                    <a href="creatnewaccount.php">اذهب لإنشاء حساب</a>
                </div>
            </div>
        </section>
    </main>
    <foooter style="text-align: center; padding: 24px 0; color: rgba(255,255,255,0.8);">
        &copy; 2026 A2Z جميع الحقوق محفوظة
    </foooter>
    <nav class="nav-links">
        <a href="login.php">تسجيل الدخول</a>
        <a href="logout.php">تسجيل الخروج</a>
        <a href="creatnewaccount.php">إنشاء حساب</a>
    </nav>
</body>
</html>