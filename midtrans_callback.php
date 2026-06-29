<?php

$rawBody = file_get_contents('php://input');

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$data = json_decode($rawBody, true);

if (empty($data)) {
    http_response_code(400);
    echo 'Bad Request: empty or invalid JSON';
    exit;
}

$serverKey         = $_ENV['MIDTRANS_SERVER_KEY'];
$orderId           = $data['order_id']         ?? '';
$statusCode        = $data['status_code']      ?? '';
$grossAmount       = $data['gross_amount']     ?? '';
$incomingSignature = $data['signature_key']    ?? '';

$expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

if ($incomingSignature !== $expectedSignature) {
    http_response_code(403);
    echo 'Invalid signature';
    exit;
}

$transactionStatus = $data['transaction_status'] ?? '';
$fraudStatus       = $data['fraud_status']       ?? 'accept';

$conn = new mysqli(
    $_ENV['DB_HOST'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    $_ENV['DB_NAME']
);
$conn->set_charset('utf8mb4');

$stmt = $conn->prepare(
    "SELECT id, payment_status FROM transactions WHERE midtrans_transaction_id = ? LIMIT 1"
);
$stmt->bind_param("s", $orderId);
$stmt->execute();
$tx = $stmt->get_result()->fetch_assoc();

if (!$tx || $tx['payment_status'] === 'settlement') {
    http_response_code(200);
    echo 'OK';
    exit;
}

$txId      = (int) $tx['id'];
$newStatus = 'pending';

if ($transactionStatus === 'capture') {
    $newStatus = ($fraudStatus === 'accept') ? 'settlement' : 'deny';
} elseif ($transactionStatus === 'settlement') {
    $newStatus = 'settlement';
} elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
    $newStatus = $transactionStatus;

    $release = $conn->prepare(
        "UPDATE game_keys gk
         JOIN transaction_details td ON td.game_key_id = gk.id
         SET gk.status = 'available'
         WHERE td.transaction_id = ?"
    );
    $release->bind_param("i", $txId);
    $release->execute();
}

$upd = $conn->prepare("UPDATE transactions SET payment_status = ? WHERE id = ?");
$upd->bind_param("si", $newStatus, $txId);
$upd->execute();

$conn->close();

http_response_code(200);
echo 'OK';