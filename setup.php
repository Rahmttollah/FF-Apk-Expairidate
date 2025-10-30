<?php
include 'config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Setup Complete</title>
    <style>
        body { font-family: Arial; margin: 40px; background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
        .container { max-width: 600px; margin: 0 auto; background: rgba(255,255,255,0.1); padding: 30px; border-radius: 15px; }
        .success { background: #4CAF50; padding: 15px; border-radius: 8px; margin: 10px 0; }
        .info { background: #2196F3; padding: 15px; border-radius: 8px; margin: 10px 0; }
        .btn { display: inline-block; padding: 12px 24px; background: #FF9800; color: white; text-decoration: none; border-radius: 8px; margin: 10px 5px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🚀 System Ready!</h1>
        <div class='success'>✅ Database connection successful!</div>";

// Test database
try {
    $settings = $pdo->query("SELECT * FROM expiry_settings WHERE id = 1")->fetch();
    $analytics = $pdo->query("SELECT * FROM analytics WHERE id = 1")->fetch();
    
    echo "<div class='info'>
            <h3>✅ Database Test Successful!</h3>
            <p><strong>Expiry Date:</strong> " . $settings['expiry_date'] . "</p>
            <p><strong>Dialog Title:</strong> " . $settings['dialog_title'] . "</p>
            <p><strong>Total Checks:</strong> " . $analytics['total_checks'] . "</p>
          </div>";
} catch(Exception $e) {
    echo "<div style='background: #f44336; padding: 15px; border-radius: 8px; margin: 10px 0;'>
            ❌ Database Error: " . $e->getMessage() . "
          </div>";
}

echo "
        <div class='info'>
            <h3>📧 Admin Login Details:</h3>
            <p><strong>Username:</strong> admin</p>
            <p><strong>Password:</strong> admin123</p>
            <p><em>(Change password in config.php after login)</em></p>
        </div>
        
        <div class='info'>
            <h3>🔗 Your API Endpoint:</h3>
            <p><strong>URL:</strong> https://ff-apk-expairidate.up.railway.app/</p>
            <p>Use this URL in your Android app to check expiry</p>
        </div>
        
        <br>
        <a href='login.php' class='btn'>🚀 Go to Admin Panel</a>
        <a href='/' class='btn'>📡 Test API Endpoint</a>
        <a href='admin.php' class='btn'>⚙️ Direct Admin</a>
    </div>
</body>
</html>";
?>
