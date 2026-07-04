<?php
header('Content-Type: application/json');
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/paypal_config.php';

$input = json_decode(file_get_contents('php://input'), true);
$productId = $input['product_id'] ?? null;

if (!$productId) {
    http_response_code(422);
    echo json_encode(['error' => 'product_id is required']);
    exit;
}

// Look up the real price server-side — never trust a price sent from the browser
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    http_response_code(404);
    echo json_encode(['error' => 'Product not found']);
    exit;
}

try {
    $accessToken = paypal_get_access_token();

    $orderPayload = [
        'intent' => 'CAPTURE',
        'purchase_units' => [[
            'description' => $product['name'],
            'amount' => [
                'currency_code' => 'USD',
                'value' => number_format((float) $product['price'], 2, '.', ''),
            ],
        ]],
    ];

    $ch = curl_init(PAYPAL_BASE_URL . '/v2/checkout/orders');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($orderPayload),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
        ],
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    echo $response; // contains the order "id" the frontend needs
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
