<?php
$context = stream_context_create(['http' => ['ignore_errors' => true]]);
$resp = file_get_contents('http://127.0.0.1:8000/ajax/transactions?user_id=1', false, $context);
echo "STATUS: " . $http_response_header[0] . "\n";
