<?php
include 'config.php';

// Create tables
$sql = "
CREATE TABLE IF NOT EXISTS expiry_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    expiry_date DATETIME NOT NULL,
    dialog_title VARCHAR(255) NOT NULL,
    dialog_message TEXT NOT NULL,
    update_link VARCHAR(500) NOT NULL,
    button_text VARCHAR(100) NOT NULL DEFAULT 'UPDATE 🔴',
    exit_text VARCHAR(100) NOT NULL DEFAULT 'Exit App',
    primary_color VARCHAR(7) DEFAULT '#00ff00',
    background_color VARCHAR(7) DEFAULT '#0000ff',
    text_color VARCHAR(7) DEFAULT '#ff00ff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS analytics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    total_checks INT DEFAULT 0,
    download_clicks INT DEFAULT 0,
    exit_clicks INT DEFAULT 0,
    last_check DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO expiry_settings (id, expiry_date, dialog_title, dialog_message, update_link) 
VALUES (1, '2024-12-31 23:59:59', 'NEED UPDATE VELTRIX MODS V16📢', 'CLICK ON UPDATE FOR V17 ✅', 'https://t.me/RNRCHANNELS');

INSERT IGNORE INTO analytics (id, total_checks) VALUES (1, 0);
";

try {
    $pdo->exec($sql);
    echo "✅ Database setup completed successfully!<br>";
    echo "📧 Admin Login: admin<br>";
    echo "🔑 Admin Password: admin123<br>";
    echo "<a href='login.php'>Go to Admin Panel</a>";
} catch(PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?>