<?php

if (!file_exists('madeline.php')) {
    copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
}
include 'madeline.php';

use danog\MadelineProto\API;

$sessionFile = 'session.madeline';
$MadelineProto = new API($sessionFile);

// Handle logout
if (isset($_GET['logout'])) {
    if (file_exists($sessionFile)) {
        unlink($sessionFile);
    }
    header('Location: index.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telegram Admin Attribution Bot</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 40px auto; padding: 20px; line-height: 1.6; background: #f4f4f9; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #0088cc; }
        .status { padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .online { background: #e6fffa; color: #2c7a7b; border: 1px solid #b2f5ea; }
        .offline { background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; }
        .btn { display: inline-block; padding: 10px 20px; background: #0088cc; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .btn-danger { background: #e53e3e; }
        pre { background: #eee; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Admin Attribution Bot</h1>
        
        <?php
        try {
            // This will trigger the login UI if not logged in
            $MadelineProto->start();
            $me = $MadelineProto->getSelf();
            ?>
            <div class="status online">
                <strong>Status:</strong> Logged in as <?php echo htmlspecialchars($me['first_name']); ?> (@<?php echo htmlspecialchars($me['username'] ?? 'no_username'); ?>)
            </div>
            <p>The bot is currently running in the background (as long as this page or the worker script is active).</p>
            <p><strong>Instructions:</strong></p>
            <ol>
                <li>Make sure this account is an <strong>Admin</strong> in all source channels.</li>
                <li>Go to your <strong>Private Central Channel</strong> and type <code>/start</code>.</li>
                <li>The bot will now report all new posts in your other channels to that central channel.</li>
            </ol>
            <a href="?logout=1" class="btn btn-danger">Logout / Reset Session</a>
            <?php
        } catch (\Exception $e) {
            ?>
            <div class="status offline">
                <strong>Error:</strong> <?php echo htmlspecialchars($e->getMessage()); ?>
            </div>
            <p>Please refresh the page to try logging in again.</p>
            <?php
        }
        ?>
    </div>
    
    <div style="margin-top: 20px; font-size: 0.8em; color: #666;">
        <p>Note: On InfinityFree, you may need to use a Cron Job to keep <code>bot.php</code> running, or keep this browser tab open.</p>
    </div>
</body>
</html>
