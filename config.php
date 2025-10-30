<?php
// SQLite database connection - Railway compatible
$database_file = __DIR__ . '/database/expiry.db';
$database_dir = __DIR__ . '/database';

// Create database directory if not exists
if (!is_dir($database_dir)) {
    mkdir($database_dir, 0755, true);
}

try {
    $pdo = new PDO("sqlite:" . $database_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Enable foreign keys and other SQLite optimizations
    $pdo->exec("PRAGMA foreign_keys = ON");
    $pdo->exec("PRAGMA journal_mode = WAL");
    
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Default admin credentials
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123');

// Create tables if they don't exist
create_tables_if_not_exist($pdo);

function create_tables_if_not_exist($pdo) {
    $tables_sql = [
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
        )",
        
        "INSERT OR IGNORE INTO expiry_settings (id, expiry_date, dialog_title, dialog_message, update_link) 
         VALUES (1, '2024-12-31 23:59:59', 'NEED UPDATE VELTRIX MODS V16📢', 'CLICK ON UPDATE FOR V17 ✅', 'https://t.me/RNRCHANNELS')",
        
        "INSERT OR IGNORE INTO analytics (id, total_checks) VALUES (1, 0)"
    ];
    
    foreach ($tables_sql as $sql) {
        try {
            $pdo->exec($sql);
        } catch(PDOException $e) {
            // Continue even if some queries fail
        }
    }
}
?>
