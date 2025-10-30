<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Handle form submission
if (isset($_POST['update_settings'])) {
    $expiry_date = $_POST['expiry_date'];
    $dialog_title = $_POST['dialog_title'];
    $dialog_message = $_POST['dialog_message'];
    $update_link = $_POST['update_link'];
    $button_text = $_POST['button_text'];
    $exit_text = $_POST['exit_text'];
    $primary_color = $_POST['primary_color'];
    $background_color = $_POST['background_color'];
    $text_color = $_POST['text_color'];
    
    try {
        $sql = "UPDATE expiry_settings SET 
                expiry_date = ?, dialog_title = ?, dialog_message = ?, 
                update_link = ?, button_text = ?, exit_text = ?,
                primary_color = ?, background_color = ?, text_color = ?,
                last_updated = datetime('now')
                WHERE id = 1";
        
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$expiry_date, $dialog_title, $dialog_message, $update_link, 
                           $button_text, $exit_text, $primary_color, $background_color, $text_color])) {
            $success = "✅ Settings updated successfully!";
        } else {
            $error = "❌ Failed to update settings!";
        }
    } catch(PDOException $e) {
        $error = "❌ Database error: " . $e->getMessage();
    }
}

// Reset analytics
if (isset($_POST['reset_analytics'])) {
    try {
        $pdo->exec("UPDATE analytics SET total_checks = 0, download_clicks = 0, exit_clicks = 0");
        $success = "✅ Analytics reset successfully!";
    } catch(PDOException $e) {
        $error = "❌ Error resetting analytics: " . $e->getMessage();
    }
}

// Get current settings
try {
    $settings = $pdo->query("SELECT * FROM expiry_settings WHERE id = 1")->fetch();
    $analytics = $pdo->query("SELECT * FROM analytics WHERE id = 1")->fetch();
} catch(PDOException $e) {
    die("❌ Error loading settings: " . $e->getMessage());
}

// Calculate time until expiry
$now = new DateTime();
$expiry = new DateTime($settings['expiry_date']);
$interval = $now->diff($expiry);
$is_expired = $now > $expiry;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Expiry Control</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh; padding: 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { 
            background: white; padding: 30px; border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 30px;
            text-align: center;
            position: relative;
        }
        .cards { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .card { 
            background: white; padding: 30px; border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        h1 { color: #333; margin-bottom: 10px; }
        h2 { color: #444; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #555; font-weight: 500; }
        input, textarea, select { 
            width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;
            font-size: 14px;
        }
        textarea { height: 100px; resize: vertical; }
        .btn { 
            padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer;
            font-size: 14px; font-weight: 500; transition: all 0.3s;
            margin: 5px;
        }
        .btn-primary { background: #667eea; color: white; }
        .btn-primary:hover { background: #764ba2; transform: translateY(-2px); }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-success { background: #27ae60; color: white; }
        .status-card { text-align: center; padding: 20px; }
        .countdown { font-size: 24px; font-weight: bold; margin: 15px 0; }
        .expired { color: #e74c3c; }
        .active { color: #27ae60; }
        .message { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .color-preview { 
            width: 30px; height: 30px; display: inline-block; 
            border-radius: 5px; margin-left: 10px; vertical-align: middle;
            border: 1px solid #ddd;
        }
        .logout { 
            position: absolute; top: 20px; right: 20px; 
            background: #95a5a6; color: white; text-decoration: none;
            padding: 8px 15px; border-radius: 5px; font-size: 14px;
        }
        .logout:hover { background: #7f8c8d; }
        @media (max-width: 768px) {
            .cards { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 App Expiry Control Panel</h1>
            <p>Welcome, <?php echo $_SESSION['admin_user']; ?>!</p>
            <a href="?logout=1" class="logout">🚪 Logout</a>
        </div>

        <?php if (isset($success)): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="cards">
            <!-- Status Card -->
            <div class="card status-card">
                <h2>📊 Current Status</h2>
                <div class="countdown <?php echo $is_expired ? 'expired' : 'active'; ?>">
                    <?php echo $is_expired ? '⏰ EXPIRED' : '⏳ ACTIVE'; ?>
                </div>
                <p><strong>Expiry Date:</strong> <?php echo date('Y-m-d H:i:s', strtotime($settings['expiry_date'])); ?></p>
                <p><strong>Total Checks:</strong> <?php echo $analytics['total_checks']; ?></p>
                <p><strong>Update Clicks:</strong> <?php echo $analytics['download_clicks']; ?></p>
                <p><strong>Exit Clicks:</strong> <?php echo $analytics['exit_clicks']; ?></p>
                
                <form method="POST" style="margin-top: 20px;">
                    <button type="submit" name="reset_analytics" class="btn btn-danger">🔄 Reset Analytics</button>
                </form>
            </div>

            <!-- Settings Card -->
            <div class="card">
                <h2>⚙️ Expiry Settings</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Expiry Date & Time:</label>
                        <input type="datetime-local" name="expiry_date" 
                               value="<?php echo date('Y-m-d\TH:i', strtotime($settings['expiry_date'])); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Dialog Title:</label>
                        <input type="text" name="dialog_title" value="<?php echo htmlspecialchars($settings['dialog_title']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Dialog Message:</label>
                        <textarea name="dialog_message" required><?php echo htmlspecialchars($settings['dialog_message']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Update Link:</label>
                        <input type="url" name="update_link" value="<?php echo htmlspecialchars($settings['update_link']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Button Text:</label>
                        <input type="text" name="button_text" value="<?php echo htmlspecialchars($settings['button_text']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Exit Button Text:</label>
                        <input type="text" name="exit_text" value="<?php echo htmlspecialchars($settings['exit_text']); ?>" required>
                    </div>

                    <h3>🎨 Color Settings</h3>
                    <div class="form-group">
                        <label>Primary Color: 
                            <span class="color-preview" style="background: <?php echo $settings['primary_color']; ?>"></span>
                        </label>
                        <input type="color" name="primary_color" value="<?php echo $settings['primary_color']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Background Color: 
                            <span class="color-preview" style="background: <?php echo $settings['background_color']; ?>"></span>
                        </label>
                        <input type="color" name="background_color" value="<?php echo $settings['background_color']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Text Color: 
                            <span class="color-preview" style="background: <?php echo $settings['text_color']; ?>"></span>
                        </label>
                        <input type="color" name="text_color" value="<?php echo $settings['text_color']; ?>" required>
                    </div>
                    
                    <button type="submit" name="update_settings" class="btn btn-primary">💾 Save Settings</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Live countdown update
        function updateCountdown() {
            const expiryTime = new Date("<?php echo $settings['expiry_date']; ?>").getTime();
            const now = new Date().getTime();
            const distance = expiryTime - now;
            
            if (distance < 0) {
                document.querySelector('.countdown').innerHTML = '⏰ EXPIRED';
                document.querySelector('.countdown').className = 'countdown expired';
            } else {
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                document.querySelector('.countdown').innerHTML = 
                    `⏳ ${days}d ${hours}h ${minutes}m ${seconds}s`;
                document.querySelector('.countdown').className = 'countdown active';
            }
        }
        
        updateCountdown();
        setInterval(updateCountdown, 1000);

        // Update color previews in real-time
        document.querySelectorAll('input[type="color"]').forEach(input => {
            input.addEventListener('input', function() {
                const preview = this.previousElementSibling.querySelector('.color-preview');
                if (preview) {
                    preview.style.backgroundColor = this.value;
                }
            });
        });
    </script>
</body>
</html>
