<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$transactions = App\Models\Transaction::all();
echo "Transactions count: " . $transactions->count() . "\n";
foreach($transactions as $t) {
    echo "ID: {$t->id}, Judul: {$t->judul}\n";
}
