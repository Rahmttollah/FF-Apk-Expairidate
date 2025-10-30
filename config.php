<?php
// Railway MySQL database connection
$host = 'mysql.railway.internal';
$port = '3306';
$username = 'root';
$password = 'ObYJwedYLfaMitbHKIyiTThRbYvCKawu';
$database = 'Apk_RNR';

$dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    // If database doesn't exist, try to create it
    if ($e->getCode() == 1049) { // Unknown database
        try {
            // Connect without database
            $temp_pdo = new PDO("mysql:host=$host;port=$port", $username, $password);
            $temp_pdo->exec("CREATE DATABASE IF NOT EXISTS $database");
            $temp_pdo->exec("USE $database");
            
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
                )",
                
                "CREATE TABLE IF NOT EXISTS analytics (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    total_checks INT DEFAULT 0,
                    download_clicks INT DEFAULT 0,
                    exit_clicks INT DEFAULT 0,
                    last_check DATETIME,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )"
            ];
            
            foreach ($tables_sql as $sql) {
                $temp_pdo->exec($sql);
            }
            
            // Insert default data
            $temp_pdo->exec("INSERT IGNORE INTO expiry_settings (expiry_date, dialog_title, dialog_message, update_link) 
                           VALUES ('2024-12-31 23:59:59', 'NEED UPDATE VELTRIX MODS V16📢', 'CLICK ON UPDATE FOR V17 ✅', 'https://t.me/RNRCHANNELS')");
            $temp_pdo->exec("INSERT IGNORE INTO analytics (total_checks) VALUES (0)");
            
            // Now connect with database
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch(PDOException $e2) {
            die("Database creation failed: " . $e2->getMessage());
        }
    } else {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Default admin credentials
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123');
?>
