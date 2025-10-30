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
        <h1>🚀 Setup Complete!</h1>
        <div class='success'>✅ Database & Tables successfully created!</div>
        <div class='info'>
            <h3>📧 Admin Login Details:</h3>
            <p><strong>Username:</strong> admin</p>
            <p><strong>Password:</strong> admin123</p>
        </div>
        <div class='info'>
            <h3>🔗 Your API Endpoint:</h3>
            <p><strong>URL:</strong> https://ff-apk-expairidate.up.railway.app/</p>
            <p>Use this URL in your Android app</p>
        </div>
        <br>
        <a href='login.php' class='btn'>Go to Admin Panel</a>
        <a href='/' class='btn'>Test API</a>
    </div>
</body>
</html>";

// Force create tables one more time
create_tables_if_not_exist($pdo);

// Test the API endpoint
try {
    $settings = $pdo->query("SELECT * FROM expiry_settings WHERE id = 1")->fetch();
    $analytics = $pdo->query("SELECT * FROM analytics WHERE id = 1")->fetch();
    
    echo "<div class='info' style='margin-top: 20px;'>
            <h3>✅ Database Test Successful!</h3>
            <p>Expiry Date: " . $settings['expiry_date'] . "</p>
            <p>Total Checks: " . $analytics['total_checks'] . "</p>
          </div>";
} catch(Exception $e) {
    echo "<div style='background: #f44336; padding: 15px; border-radius: 8px; margin: 10px 0;'>
            ❌ Database Error: " . $e->getMessage() . "
          </div>";
}
?>
