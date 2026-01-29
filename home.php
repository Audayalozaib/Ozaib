<?php
session_start();
require 'db.php';

// التحقق من تسجيل دخول المستخدم
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// جلب معلومات المستخدم
try {
    $stmt = $pdo->prepare('SELECT id, username, created_at FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        session_destroy();
        header('Location: login.php');
        exit;
    }
} catch (PDOException $e) {
    die('حدث خطأ في قاعدة البيانات: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الصفحة الرئيسية</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
            direction: rtl;
        }
        
        .navbar {
            background-color: #007bff;
            padding: 1rem;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            color: white;
        }
        
        .navbar h1 {
            margin: 0;
            font-size: 1.5rem;
        }
        
        .navbar a {
            color: white;
            text-decoration: none;
            background-color: #dc3545;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .navbar a:hover {
            background-color: #c82333;
        }
        
        .container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .user-info {
            background-color: #e7f3ff;
            padding: 1.5rem;
            border-radius: 8px;
            border-right: 4px solid #007bff;
            margin-bottom: 1.5rem;
        }
        
        .user-info p {
            margin: 0.5rem 0;
            font-size: 1rem;
        }
        
        .user-info strong {
            color: #007bff;
        }
        
        .welcome-message {
            text-align: center;
            color: #333;
            margin: 1rem 0;
        }
        
        .welcome-message h2 {
            color: #007bff;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>تطبيق إدارة المستخدمين</h1>
        <a href="logout.php">تسجيل الخروج</a>
    </div>
    
    <div class="container">
        <div class="welcome-message">
            <h2>أهلاً وسهلاً بك! 👋</h2>
            <p>تم تسجيل دخولك بنجاح</p>
        </div>
        
        <div class="user-info">
            <p><strong>اسم المستخدم:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>معرف المستخدم:</strong> <?php echo htmlspecialchars($user['id']); ?></p>
            <p><strong>تاريخ الإنشاء:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
        </div>
        
        <div style="text-align: center; margin-top: 2rem;">
            <p style="color: #666;">يمكنك الآن استخدام التطبيق بكامل إمكانياته</p>
        </div>
    </div>
</body>
</html>
