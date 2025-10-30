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
        <h1>🚀 System Status</h1>";

try {
    $settings = $pdo->query("SELECT * FROM expiry_settings WHERE id = 1")->fetch();
    $analytics = $pdo->query("SELECT * FROM analytics WHERE id = 1")->fetch();
    
    echo "<div class='success'>✅ Database Connected Successfully!</div>";
    echo "<div class='info'>
            <h3>Current Settings:</h3>
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
            <h3>🔗 Quick Links:</h3>
            <a href='login.php' class='btn'>🚀 Admin Panel</a>
            <a href='/' class='btn'>📡 API Test</a>
        </div>
    </div>
</body>
</html>";
?>
