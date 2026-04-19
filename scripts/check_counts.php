<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=trackflow', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $users = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $socials = $pdo->query('SELECT COUNT(*) FROM social_accounts')->fetchColumn();

    echo "users_count=" . $users . "\n";
    echo "social_accounts_count=" . $socials . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
