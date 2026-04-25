<?php
try {
    $pdo = new PDO(
        "pgsql:host=aws-1-ap-northeast-1.pooler.supabase.com;port=6543;dbname=postgres",
        "postgres.ckduhrwmmmiauasdirhd",
        "Radit_196163"
    );
    $stmt = $pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'transactions'");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $c) {
        echo $c['column_name'] . " - " . $c['data_type'] . "\n";
    }
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
