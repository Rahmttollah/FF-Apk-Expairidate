<?php
// Railway MySQL database connection using environment variables
$host = getenv('MYSQLHOST') ?: 'mysql.railway.internal';
$port = getenv('MYSQLPORT') ?: '3306';
$username = getenv('MYSQLUSER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: 'ObYJwedYLfaMitbHKIyiTThRbYvCKawu';
$database = getenv('MYSQLDATABASE') ?: 'Apk_RNR';

// Debug - check if variables are loaded
echo "<!-- Debug: Host: $host, Port: $port, User: $username, DB: $database -->";

$dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<!-- Database connected successfully -->";
    
} catch(PDOException $e) {
    // If database doesn't exist, try to create it
    if ($e->getCode() == 1049) { // Unknown database
        try {
            // Connect without database
            $temp_dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
            $temp_pdo = new PDO($temp_dsn, $username, $password);
            $temp_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create database
            $temp_pdo->exec("CREATE DATABASE IF NOT EXISTS `$database`");
            $temp_pdo->exec("USE `$database`");
            
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
                $temp_pdo->exec($sql);
            }
            
            // Insert default data
            $temp_pdo->exec("INSERT IGNORE INTO expiry_settings (expiry_date, dialog_title, dialog_message, update_link) 
                           VALUES ('2024-12-31 23:59:59', 'NEED UPDATE VELTRIX MODS V16📢', 'CLICK ON UPDATE FOR V17 ✅', 'https://t.me/RNRCHANNELS')");
            $temp_pdo->exec("INSERT IGNORE INTO analytics (total_checks) VALUES (0)");
            
            // Now connect with database
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo "<!-- Database created and connected successfully -->";
            
        } catch(PDOException $e2) {
            die("Database creation failed: " . $e2->getMessage());
        }
    } else {
        // Fallback to SQLite
        $database_file = __DIR__ . '/expiry.db';
        try {
            $pdo = new PDO("sqlite:" . $database_file);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("PRAGMA foreign_keys = ON");
            
            // Create SQLite tables
            $sqlite_tables = [
                "CREATE TABLE IF NOT EXISTS expiry_settings (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    expiry_date DATETIME NOT NULL,
                    dialog_title TEXT NOT NULL,
                    dialog_message TEXT NOT NULL,
                    update_link TEXT NOT NULL,
                    button_text TEXT DEFAULT 'UPDATE 🔴',
                    exit_text TEXT DEFAULT 'Exit App',
                    primary_color TEXT DEFAULT '#00ff00',
                    background_color TEXT DEFAULT '#0000ff',
                    text_color TEXT DEFAULT '#ff00ff',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP
                )",
                
                "CREATE TABLE IF NOT EXISTS analytics (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    total_checks INTEGER DEFAULT 0,
                    download_clicks INTEGER DEFAULT 0,
                    exit_clicks INTEGER DEFAULT 0,
                    last_check DATETIME,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )"
            ];
            
            foreach ($sqlite_tables as $sql) {
                $pdo->exec($sql);
            }
            
            // Insert default data
            $pdo->exec("INSERT OR IGNORE INTO expiry_settings (expiry_date, dialog_title, dialog_message, update_link) 
                       VALUES ('2024-12-31 23:59:59', 'NEED UPDATE VELTRIX MODS V16📢', 'CLICK ON UPDATE FOR V17 ✅', 'https://t.me/RNRCHANNELS')");
            $pdo->exec("INSERT OR IGNORE INTO analytics (total_checks) VALUES (0)");
            
            echo "<!-- Using SQLite fallback -->";
            
        } catch(PDOException $e3) {
            die("All database connections failed: " . $e3->getMessage());
        }
    }
}

// Default admin credentials
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123');
?>
