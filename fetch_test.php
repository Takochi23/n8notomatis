<?php
$context = stream_context_create(['http' => ['ignore_errors' => true]]);
$resp = file_get_contents('http://127.0.0.1:8000/ajax/transactions', false, $context);
echo "RESPONSE_HEADERS: " . implode(", ", $http_response_header) . "\n";
echo "RESPONSE_BODY: " . $resp . "\n";
