<?php
try {
    $db = new PDO(
        "pgsql:host=aws-0-eu-west-1.pooler.supabase.com;port=6543;dbname=postgres",
        "postgres.mrevazltwkhjmjyhxboa",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $db->prepare("DELETE FROM parent WHERE email = 'test_playwright@test.com'")->execute();
    echo "ok";
} catch (Exception $e) {
    die("Cleanup error: " . $e->getMessage());
}
