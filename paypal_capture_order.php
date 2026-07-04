<?php
header('Content-Type: application/json');
require __DIR__ . '/includes/paypal_config.php';

$input = json_decode(file_get_contents('php://input'), true);
$orderId = $input['order_id'] ?? null;

if (!$orderId) {
    http_response_code(422);
    echo json_encode(['error' => 'order_id is required']);
    exit;
}

try {
    $accessToken = paypal_get_access_token();

    $ch = curl_init(PAYPAL_BASE_URL . "/v2/checkout/orders/$orderId/capture");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => '{}',
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
        ],
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    echo $response; // contains capture status ("COMPLETED") and payer details
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
