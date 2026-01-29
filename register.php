<?php
session_start();
require 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // التحقق من الحقول الفارغة
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'جميع الحقول مطلوبة';
    }
    // التحقق من تطابق كلمات المرور
    elseif ($password !== $confirm_password) {
        $error = 'كلمات المرور غير متطابقة';
    }
    // التحقق من طول كلمة المرور
    elseif (strlen($password) < 6) {
        $error = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
    }
    // التحقق من طول اسم المستخدم
    elseif (strlen($username) < 3) {
        $error = 'اسم المستخدم يجب أن يكون 3 أحرف على الأقل';
    }
    else {
        try {
            // التحقق من عدم وجود اسم المستخدم مسبقاً
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
            $stmt->execute([$username]);
            
            if ($stmt->rowCount() > 0) {
                $error = 'اسم المستخدم موجود بالفعل';
            } else {
                // تشفير كلمة المرور باستخدام bcrypt
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                
                // إدراج المستخدم الجديد
                $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
                $stmt->execute([$username, $hashed_password]);
                
                $success = 'تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول.';
                // إعادة توجيه بعد ثانيتين
                header('Refresh: 2; url=login.php');
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
    <title>إنشاء حساب</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>إنشاء حساب جديد</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
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
            <input 
                type="password" 
                name="confirm_password" 
                placeholder="تأكيد كلمة المرور" 
                required
            >
            <button type="submit">إنشاء الحساب</button>
        </form>
        
        <p>لديك حساب بالفعل؟ <a href="login.php">تسجيل الدخول</a></p>
    </div>
</body>
</html>
