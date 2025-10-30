<?php
include 'config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Setup Complete</title>
    <style>
        body { font-family: Arial; margin: 40px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Database Setup</h1>";

try {
    $settings = $pdo->query("SELECT * FROM expiry_settings WHERE id = 1")->fetch();
    echo "<div class='success'><h3>✅ SUCCESS!</h3>Database connected and setup completed successfully!</div>";
    echo "<p><strong>Expiry Date:</strong> " . $settings['expiry_date'] . "</p>";
    echo "<p><strong>Title:</strong> " . $settings['dialog_title'] . "</p>";
    echo "<br>";
    echo "<a href='admin.php' class='btn'>Go to Admin Panel</a>";
    echo "<a href='/' class='btn'>Test API</a>";
} catch(Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>
            <h3>❌ ERROR</h3>
            " . $e->getMessage() . "
          </div>";
}

echo "</div></body></html>";
?>
