<?php
session_start();
include 'config.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Database se credentials check karo
    try {
        $stmt = $pdo->prepare("SELECT * FROM expiry_settings WHERE id = 1");
        $stmt->execute();
        $settings = $stmt->fetch();
        
        // Hardcoded credentials check (temporary)
        $valid_username = 'admin';
        $valid_password = 'admin123';
        
        if ($username === $valid_username && $password === $valid_password) {
            $_SESSION['admin'] = true;
            $_SESSION['admin_user'] = $username;
            header('Location: admin.php');
            exit();
        } else {
            $error = "Invalid credentials!";
        }
    } catch(PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// If already logged in, redirect to admin
if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header('Location: admin.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        .login-box { 
            background: white; padding: 40px; border-radius: 15px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.1); width: 100%; max-width: 400px;
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h2 { text-align: center; margin-bottom: 30px; color: #333; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #555; font-weight: 500; }
        input { 
            width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; 
            font-size: 16px; transition: border 0.3s;
        }
        input:focus { border-color: #667eea; outline: none; }
        button { 
            width: 100%; padding: 12px; background: #667eea; color: white; 
            border: none; border-radius: 8px; cursor: pointer; font-size: 16px;
            transition: background 0.3s;
        }
        button:hover { background: #764ba2; }
        .error { 
            color: #e74c3c; text-align: center; margin-bottom: 15px; 
            padding: 10px; background: #fdf2f2; border-radius: 5px;
        }
        .credentials {
            background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 20px;
            border-left: 4px solid #667eea;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>🔐 Admin Login</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" value="admin" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" value="admin123" required>
            </div>
            <button type="submit" name="login">Login</button>
        </form>
        
        <div class="credentials">
            <strong>Default Credentials:</strong><br>
            Username: <code>admin</code><br>
            Password: <code>admin123</code>
        </div>
    </div>
</body>
</html>
