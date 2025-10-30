<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include 'config.php';

// Update analytics with SQLite compatible datetime
try {
    $pdo->exec("UPDATE analytics SET total_checks = total_checks + 1, last_check = datetime('now')");
} catch(PDOException $e) {
    // Continue even if analytics update fails
}

// Get settings
try {
    $settings = $pdo->query("SELECT * FROM expiry_settings WHERE id = 1")->fetch();
    $analytics = $pdo->query("SELECT * FROM analytics WHERE id = 1")->fetch();
} catch(PDOException $e) {
    // Return error response
    echo json_encode([
        "success" => false,
        "error" => "Database error: " . $e->getMessage()
    ]);
    exit();
}

// Check expiry
$current_date = new DateTime();
$expiry_date = new DateTime($settings['expiry_date']);
$expired = ($current_date >= $expiry_date);

// Handle button clicks
if (isset($_GET['action'])) {
    try {
        if ($_GET['action'] == 'download') {
            $pdo->exec("UPDATE analytics SET download_clicks = download_clicks + 1");
        } elseif ($_GET['action'] == 'exit') {
            $pdo->exec("UPDATE analytics SET exit_clicks = exit_clicks + 1");
        }
    } catch(PDOException $e) {
        // Continue execution even if click tracking fails
    }
}

// Prepare response
$response = [
    "success" => true,
    "expired" => $expired,
    "data" => [
        "expiry_enabled" => true,
        "expiry_date" => $settings['expiry_date'],
        "dialog_title" => $settings['dialog_title'],
        "dialog_message" => $settings['dialog_message'],
        "download_button" => [
            "text" => $settings['button_text'],
            "link" => $settings['update_link']
        ],
        "FFMainActivityX" => [
            [
                "text" => "Join Channel",
                "link" => "https://t.me/RNRCHANNELS",
                "enabled" => true
            ]
        ],
        "exit_button" => $settings['exit_text'],
        "colors" => [
            "primary" => $settings['primary_color'],
            "background" => $settings['background_color'],
            "text" => $settings['text_color']
        ],
        "analytics" => [
            "total_checks" => (int)$analytics['total_checks'],
            "button_clicks" => [
                "download" => (int)$analytics['download_clicks'],
                "exit" => (int)$analytics['exit_clicks']
            ],
            "last_check" => $analytics['last_check']
        ]
    ],
    "dialog" => [
        "title" => $settings['dialog_title'],
        "message" => $settings['dialog_message'],
        "download_button" => [
            "text" => $settings['button_text'],
            "link" => $settings['update_link']
        ],
        "FFMainActivityX" => [
            [
                "text" => "Join Channel",
                "link" => "https://t.me/RNRCHANNELS",
                "enabled" => true
            ]
        ],
        "exit_button" => $settings['exit_text'],
        "colors" => [
            "primary" => $settings['primary_color'],
            "background" => $settings['background_color'],
            "text" => $settings['text_color']
        ]
    ]
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>
