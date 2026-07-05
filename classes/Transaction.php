<?php

class Transaction extends Database {

    public function createFromCart(int $userId, array $cartItems, float $finalTotal, ?int $promoId): array {
        $stmt = $this->conn->prepare("SELECT username, email FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        $orderId = 'SEEDEM-' . time() . '-' . $userId;

        $stmt = $this->conn->prepare(
            "INSERT INTO transactions (user_id, promo_id, midtrans_transaction_id, total_amount, payment_status)
             VALUES (?, ?, ?, ?, 'pending')"
        );
        $stmt->bind_param("iisd", $userId, $promoId, $orderId, $finalTotal);
        $stmt->execute();
        $txId = $this->conn->insert_id;

        if (!$txId) return ['success' => false, 'error' => 'Gagal membuat transaksi.'];

        $assignedKeyIds = [];

        foreach ($cartItems as $item) {
            $gameId = $item['game_id'] ?? $item['id'] ?? null;

            if (!$gameId) {
                $this->rollback($txId, $assignedKeyIds);
                return ['success' => false, 'error' => 'Data game tidak valid.'];
            }

            $keyStmt = $this->conn->prepare(
                "SELECT id FROM game_keys WHERE game_id = ? AND status = 'available' LIMIT 1"
            );
            $keyStmt->bind_param("i", $gameId);
            $keyStmt->execute();
            $key = $keyStmt->get_result()->fetch_assoc();

            if (!$key) {
                $this->rollback($txId, $assignedKeyIds);
                return ['success' => false, 'error' => 'Stok key untuk "' . $item['title'] . '" habis. Hubungi admin.'];
            }

            $upd = $this->conn->prepare("UPDATE game_keys SET status = 'sold' WHERE id = ?");
            $upd->bind_param("i", $key['id']);
            $upd->execute();
            $assignedKeyIds[] = $key['id'];

            $detailStmt = $this->conn->prepare(
                "INSERT INTO transaction_details (transaction_id, game_key_id, price) VALUES (?, ?, ?)"
            );
            $detailStmt->bind_param("iid", $txId, $key['id'], $item['price']);
            $detailStmt->execute();
        }

        $itemDetails = [];
        foreach ($cartItems as $item) {
            $itemDetails[] = [
                'id'       => 'GAME-' . ($item['game_id'] ?? $item['id']),
                'price'    => (int) $item['price'],
                'quantity' => 1,
                'name'     => substr($item['title'], 0, 50),
            ];
        }

        $subtotal = array_sum(array_column($cartItems, 'price'));
        $discount = $subtotal - $finalTotal;
        if ($discount > 0) {
            $itemDetails[] = [
                'id'       => 'PROMO',
                'price'    => -(int) $discount,
                'quantity' => 1,
                'name'     => 'Diskon Promo',
            ];
        }

        $baseUrl = rtrim($_ENV['APP_URL'] ?? ((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']), '/');
        $params  = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $finalTotal,
            ],
            'item_details'     => $itemDetails,
            'customer_details' => [
                'first_name' => $user['username'],
                'email'      => $user['email'],
            ],
            'callbacks' => [
                'finish'  => $baseUrl . '/transaction_detail.php?id=' . $txId,
                'error'   => $baseUrl . '/transaction.php?id=' . $txId,
                'pending' => $baseUrl . '/transaction.php?id=' . $txId,
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
        } catch (\Exception $e) {
            $this->rollback($txId, $assignedKeyIds);
            return ['success' => false, 'error' => 'Midtrans error: ' . $e->getMessage()];
        }

        $stmt = $this->conn->prepare("UPDATE transactions SET snap_token = ? WHERE id = ?");
        $stmt->bind_param("si", $snapToken, $txId);
        $stmt->execute();

        return [
            'success'    => true,
            'tx_id'      => $txId,
            'snap_token' => $snapToken,
            'order_id'   => $orderId,
        ];
    }

    private function rollback(int $txId, array $assignedKeyIds): void {
        if (!empty($assignedKeyIds)) {
            $placeholders = implode(',', array_fill(0, count($assignedKeyIds), '?'));
            $types        = str_repeat('i', count($assignedKeyIds));
            $stmt         = $this->conn->prepare(
                "UPDATE game_keys SET status = 'available' WHERE id IN ($placeholders)"
            );
            $stmt->bind_param($types, ...$assignedKeyIds);
            $stmt->execute();
        }

        $d1 = $this->conn->prepare("DELETE FROM transaction_details WHERE transaction_id = ?");
        $d1->bind_param("i", $txId);
        $d1->execute();

        $d2 = $this->conn->prepare("DELETE FROM transactions WHERE id = ?");
        $d2->bind_param("i", $txId);
        $d2->execute();
    }

    public function clearCart(int $userId): void {
        $stmt = $this->conn->prepare("SELECT id FROM carts WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $cart = $stmt->get_result()->fetch_assoc();
        if ($cart) {
            $cartId = $cart['id'];
            $d1 = $this->conn->prepare("DELETE FROM cart_items WHERE cart_id = ?");
            $d1->bind_param("i", $cartId);
            $d1->execute();
            $d2 = $this->conn->prepare("DELETE FROM carts WHERE id = ?");
            $d2->bind_param("i", $cartId);
            $d2->execute();
        }
    }

    public function handleNotification(string $orderId, string $transactionStatus, string $fraudStatus): void {
        $stmt = $this->conn->prepare(
            "SELECT id, payment_status FROM transactions WHERE midtrans_transaction_id = ? LIMIT 1"
        );
        $stmt->bind_param("s", $orderId);
        $stmt->execute();
        $tx = $stmt->get_result()->fetch_assoc();

        if (!$tx || $tx['payment_status'] === 'settlement') return;

        $newStatus = 'pending';

        if ($transactionStatus === 'capture') {
            $newStatus = ($fraudStatus === 'accept') ? 'settlement' : 'deny';
        } elseif ($transactionStatus === 'settlement') {
            $newStatus = 'settlement';
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $newStatus = $transactionStatus;
            $this->releaseKeys((int) $tx['id']);
        }

        $stmt = $this->conn->prepare("UPDATE transactions SET payment_status = ? WHERE id = ?");
        $stmt->bind_param("si", $newStatus, $tx['id']);
        $stmt->execute();
    }

    private function releaseKeys(int $txId): void {
        $stmt = $this->conn->prepare(
            "UPDATE game_keys gk
             JOIN transaction_details td ON td.game_key_id = gk.id
             SET gk.status = 'available'
             WHERE td.transaction_id = ?"
        );
        $stmt->bind_param("i", $txId);
        $stmt->execute();
    }

    public function getTransaction(int $txId, int $userId): ?array {
        $stmt = $this->conn->prepare(
            "SELECT t.*, pc.code AS promo_code
             FROM transactions t
             LEFT JOIN promo_codes pc ON t.promo_id = pc.id
             WHERE t.id = ? AND t.user_id = ?
             LIMIT 1"
        );
        $stmt->bind_param("ii", $txId, $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?: null;
    }

    public function getDetails(int $txId): array {
        $stmt = $this->conn->prepare(
            "SELECT td.price, gk.key_code, g.title, g.image, g.platform, g.id as game_id
             FROM transaction_details td
             JOIN game_keys gk ON td.game_key_id = gk.id
             JOIN games g ON gk.game_id = g.id
             WHERE td.transaction_id = ?"
        );
        $stmt->bind_param("i", $txId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateStatus(int $txId, string $status): void {
        $stmt = $this->conn->prepare("UPDATE transactions SET payment_status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $txId);
        $stmt->execute();
    }

    public function getUserTransactions(int $userId): array {
        $stmt = $this->conn->prepare(
            "SELECT t.*, pc.code AS promo_code,
                    COUNT(td.id) as item_count,
                    MIN(g.title) as first_game,
                    MIN(g.image) as first_image,
                    MIN(g.id) as first_game_id,
                    MIN(g.platform) as first_platform,
                    t.payment_type,
                    t.midtrans_transaction_id
             FROM transactions t
             LEFT JOIN promo_codes pc ON t.promo_id = pc.id
             LEFT JOIN transaction_details td ON t.id = td.transaction_id
             LEFT JOIN game_keys gk ON td.game_key_id = gk.id
             LEFT JOIN games g ON gk.game_id = g.id
             WHERE t.user_id = ?
             GROUP BY t.id
             ORDER BY t.created_at DESC"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}