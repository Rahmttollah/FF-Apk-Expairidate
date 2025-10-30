<?php
// SQLite database connection - Railway compatible
$database_file = __DIR__ . '/expiry.db'; // Direct root folder mein

try {
    $pdo = new PDO("sqlite:" . $database_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("PRAGMA foreign_keys = ON");
    $pdo->exec("PRAGMA journal_mode = WAL");
    
} catch(PDOException $e) {
    // If first attempt fails, try memory database as fallback
    try {
        $pdo = new PDO("sqlite::memory:");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e2) {
        die("Database connection failed: " . $e2->getMessage());
    }
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
        )"
    ];
    
    foreach ($tables_sql as $sql) {
        try {
            $pdo->exec($sql);
        } catch(PDOException $e) {
            // Continue execution
        }
    }
    
    // Insert default data if not exists
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM expiry_settings");
        $result = $stmt->fetch();
        if ($result['count'] == 0) {
            $pdo->exec("INSERT INTO expiry_settings (expiry_date, dialog_title, dialog_message, update_link) 
                       VALUES ('2024-12-31 23:59:59', 'NEED UPDATE VELTRIX MODS V16📢', 'CLICK ON UPDATE FOR V17 ✅', 'https://t.me/RNRCHANNELS')");
        }
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM analytics");
        $result = $stmt->fetch();
        if ($result['count'] == 0) {
            $pdo->exec("INSERT INTO analytics (total_checks) VALUES (0)");
        }
    } catch(PDOException $e) {
        // Continue execution
    }
}
?>
