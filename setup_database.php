<?php
// Database setup script
$host = 'mysql.railway.internal';
$port = '3306';
$username = 'root';
$password = 'ObYJwedYLfaMitbHKIyiTThRbYvCKawu';

try {
    // Connect without database
    $pdo = new PDO("mysql:host=$host;port=$port", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS Apk_RNR");
    $pdo->exec("USE Apk_RNR");
    
    // Create tables
    $tables_sql = [
        "CREATE TABLE IF NOT EXISTS expiry_settings (
            id INT PRIMARY KEY AUTO_INCREMENT,
            expiry_date DATETIME NOT NULL,
            dialog_title VARCHAR(255) NOT NULL,
            dialog_message TEXT NOT NULL,
            update_link VARCHAR(500) NOT NULL,
            button_text VARCHAR(100) DEFAULT 'UPDATE 🔴',
            exit_text VARCHAR(100) DEFAULT 'Exit App',
            primary_color VARCHAR(7) DEFAULT '#00ff00',
            background_color VARCHAR(7) DEFAULT '#0000ff',
            text_color VARCHAR(7) DEFAULT '#ff00ff',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB",
        
        "CREATE TABLE IF NOT EXISTS analytics (
            id INT PRIMARY KEY AUTO_INCREMENT,
            total_checks INT DEFAULT 0,
            download_clicks INT DEFAULT 0,
            exit_clicks INT DEFAULT 0,
            last_check DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB",
        
        "INSERT IGNORE INTO expiry_settings (expiry_date, dialog_title, dialog_message, update_link) 
         VALUES ('2024-12-31 23:59:59', 'NEED UPDATE VELTRIX MODS V16📢', 'CLICK ON UPDATE FOR V17 ✅', 'https://t.me/RNRCHANNELS')",
        
        "INSERT IGNORE INTO analytics (total_checks) VALUES (0)"
    ];
    
    foreach ($tables_sql as $sql) {
        $pdo->exec($sql);
    }
    
    echo "✅ Database 'Apk_RNR' created successfully!<br>";
    echo "✅ Tables created successfully!<br>";
    echo "✅ Default data inserted!<br>";
    echo "<a href='/'>Go to Home</a>";
    
} catch(PDOException $e) {
    die("❌ Database setup failed: " . $e->getMessage());
}
?>
