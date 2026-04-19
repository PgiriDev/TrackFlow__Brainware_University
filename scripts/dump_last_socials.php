<?php

// Simple DB dump script (local dev). Adjust credentials if needed.
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=trackflow', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query('SELECT * FROM users ORDER BY id DESC LIMIT 1');
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt2 = $pdo->query('SELECT * FROM social_accounts ORDER BY id DESC LIMIT 1');
    $social = $stmt2->fetch(PDO::FETCH_ASSOC);

    echo "---LAST_USER---\n";
    echo json_encode($user, JSON_PRETTY_PRINT) . "\n";
    echo "---LAST_SOCIAL_ACCOUNT---\n";
    echo json_encode($social, JSON_PRETTY_PRINT) . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
