<?php
session_start();
include 'config.php';

if ($_POST['login']) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin'] = true;
        header('Location: admin.php');
        exit();
    } else {
        $error = "Invalid credentials!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        .login-box { 
            background: white; padding: 40px; border-radius: 15px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.1); width: 100%; max-width: 400px;
        }
        h2 { text-align: center; margin-bottom: 30px; color: #333; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; color: #555; }
        input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; }
        button { 
            width: 100%; padding: 12px; background: #667eea; color: white; 
            border: none; border-radius: 8px; cursor: pointer; font-size: 16px;
            transition: background 0.3s;
        }
        button:hover { background: #764ba2; }
        .error { color: red; text-align: center; margin-bottom: 15px; }
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
    </div>
</body>
</html>