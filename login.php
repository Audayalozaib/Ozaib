<?php
session_start();
require 'db.php';

// إذا كان المستخدم مسجل دخول بالفعل، إعادة توجيه إلى الصفحة الرئيسية
if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // التحقق من الحقول الفارغة
    if (empty($username) || empty($password)) {
        $error = 'جميع الحقول مطلوبة';
    } else {
        try {
            // البحث عن المستخدم
            $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            // التحقق من وجود المستخدم والتحقق من كلمة المرور
            if ($user && password_verify($password, $user['password'])) {
                // تعيين جلسة المستخدم
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // إعادة توجيه إلى الصفحة الرئيسية
                header('Location: home.php');
                exit;
            } else {
                $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
            }
        } catch (PDOException $e) {
            $error = 'حدث خطأ في قاعدة البيانات: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>تسجيل الدخول</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input 
                type="text" 
                name="username" 
                placeholder="اسم المستخدم" 
                required
                value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
            >
            <input 
                type="password" 
                name="password" 
                placeholder="كلمة المرور" 
                required
            >
            <button type="submit">تسجيل الدخول</button>
        </form>
        
        <p>ليس لديك حساب؟ <a href="register.php">إنشاء حساب جديد</a></p>
    </div>
</body>
</html>
