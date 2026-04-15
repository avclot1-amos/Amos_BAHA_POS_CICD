<?php
http_response_code(200);
header('Content-Type: application/json');

// Set timezone to Tokyo
date_default_timezone_set("Asia/Singapore");

echo json_encode([
    'status' => 'ok',
    'system' => 'BAHA POS Simulation',
    'time' => date('Y-m-d H:i:s')
]);
?>