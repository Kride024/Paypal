<?php
// Get these from https://developer.paypal.com/dashboard/applications/sandbox
// Create a "Sandbox" REST API app and paste the Client ID and Secret below.
define('PAYPAL_CLIENT_ID', 'YOUR_SANDBOX_CLIENT_ID');
define('PAYPAL_SECRET', 'YOUR_SANDBOX_SECRET');
define('PAYPAL_BASE_URL', 'https://api-m.sandbox.paypal.com'); // sandbox; use api-m.paypal.com in production

/**
 * Fetch an OAuth2 access token from PayPal using client_credentials grant.
 */
function paypal_get_access_token() {
    $ch = curl_init(PAYPAL_BASE_URL . '/v1/oauth2/token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD => PAYPAL_CLIENT_ID . ':' . PAYPAL_SECRET,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
        CURLOPT_HTTPHEADER => ['Accept: application/json', 'Accept-Language: en_US'],
    ]);
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        throw new Exception('cURL error while getting PayPal token: ' . $err);
    }
    $data = json_decode($response, true);
    if (!isset($data['access_token'])) {
        throw new Exception('Failed to get PayPal access token: ' . $response);
    }
    return $data['access_token'];
}
