<?php
// Use the EXACT MySQL connection details from Railway
$host = 'mysql.railway.internal'; // Railway internal host
$port = '3306';
$username = 'root';
$password = 'ObYJwedYLfaMitbHKIyiTThRbYvCKawu';
$database = 'Apk_RNR';

$dsn = "mysql:host=$host;port=$port;charset=utf8mb4";

try {
    // First try to connect without database
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database`");
    $pdo->exec("USE `$database`");
    
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
        ) ENGINE=InnoDB"
    ];
    
    foreach ($tables_sql as $sql) {
        $pdo->exec($sql);
    }
    
    // Insert default data
    $check_data = $pdo->query("SELECT COUNT(*) as count FROM expiry_settings")->fetch();
    if ($check_data['count'] == 0) {
        $pdo->exec("INSERT INTO expiry_settings (expiry_date, dialog_title, dialog_message, update_link) 
                   VALUES ('2024-12-31 23:59:59', 'NEED UPDATE VELTRIX MODS V16📢', 'CLICK ON UPDATE FOR V17 ✅', 'https://t.me/RNRCHANNELS')");
    }
    
    $check_analytics = $pdo->query("SELECT COUNT(*) as count FROM analytics")->fetch();
    if ($check_analytics['count'] == 0) {
        $pdo->exec("INSERT INTO analytics (total_checks) VALUES (0)");
    }
    
} catch(PDOException $e) {
    // If internal host fails, try public URL
    $host_public = 'shuttle.proxy.rlwy.net';
    $port_public = '26547';
    $username_public = 'root';
    $password_public = 'ObYJwedYLfaMitbHKIyiTThRbYvCKawu';
    $database_public = 'Apk_RNR';
    
    $dsn_public = "mysql:host=$host_public;port=$port_public;charset=utf8mb4";
    
    try {
        $pdo = new PDO($dsn_public, $username_public, $password_public);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database if not exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database_public`");
        $pdo->exec("USE `$database_public`");
        
        // Create tables (same as above)
        foreach ($tables_sql as $sql) {
            $pdo->exec($sql);
        }
        
        // Insert default data
        $check_data = $pdo->query("SELECT COUNT(*) as count FROM expiry_settings")->fetch();
        if ($check_data['count'] == 0) {
            $pdo->exec("INSERT INTO expiry_settings (expiry_date, dialog_title, dialog_message, update_link) 
                       VALUES ('2024-12-31 23:59:59', 'NEED UPDATE VELTRIX MODS V16📢', 'CLICK ON UPDATE FOR V17 ✅', 'https://t.me/RNRCHANNELS')");
        }
        
        $check_analytics = $pdo->query("SELECT COUNT(*) as count FROM analytics")->fetch();
        if ($check_analytics['count'] == 0) {
            $pdo->exec("INSERT INTO analytics (total_checks) VALUES (0)");
        }
        
    } catch(PDOException $e2) {
        die("MySQL connection failed: " . $e2->getMessage() . ". Please check your database credentials.");
    }
}

// Default admin credentials
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123');
?>
