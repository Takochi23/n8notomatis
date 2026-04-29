<?php
$host_pooler = 'aws-1-ap-northeast-1.pooler.supabase.com';
$port_pooler = 6543;
$db = 'postgres';
$user_pooler = 'postgres.aoilbslpwurufdstqiqq';
$pass = 'Radit_196163';

echo "Testing Pooler Connection (Port 6543)...\n";
$dsn2 = "pgsql:host=$host_pooler;port=$port_pooler;dbname=$db;sslmode=require";
try {
    $pdo2 = new PDO($dsn2, $user_pooler, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "SUCCESS: Pooler connection works!\n";
} catch (PDOException $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}
