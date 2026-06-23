<?php
class Transaction extends Database
{
    public function __construct()
    {
        parent::__construct();
    }

    public function createFromCart($userId, $cartItems, $totalAmount, $promoId, $paymentType)
    {
        $this->conn->begin_transaction();

        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO transactions (user_id, promo_id, payment_type, total_amount, payment_status) VALUES (?, ?, ?, ?, 'pending')"
            );
            $stmt->bind_param("iiss", $userId, $promoId, $paymentType, $totalAmount);
            $stmt->execute();
            $transactionId = $this->conn->insert_id;

            foreach ($cartItems as $item) {
                $gameId = $item['id'];
                $price  = $item['price'];

                $keyStmt = $this->conn->prepare(
                    "SELECT id FROM game_keys WHERE game_id = ? AND status = 'available' LIMIT 1 FOR UPDATE"
                );
                $keyStmt->bind_param("i", $gameId);
                $keyStmt->execute();
                $keyRow = $keyStmt->get_result()->fetch_assoc();

                if (!$keyRow) {
                    throw new Exception("Stok key untuk game '{$item['title']}' habis.");
                }

                $keyId = $keyRow['id'];

                $updateKey = $this->conn->prepare("UPDATE game_keys SET status = 'sold' WHERE id = ?");
                $updateKey->bind_param("i", $keyId);
                $updateKey->execute();

                $detailStmt = $this->conn->prepare(
                    "INSERT INTO transaction_details (transaction_id, game_key_id, price) VALUES (?, ?, ?)"
                );
                $detailStmt->bind_param("iid", $transactionId, $keyId, $price);
                $detailStmt->execute();
            }

            $this->conn->commit();
            return $transactionId;

        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function settle($transactionId, $userId)
    {
        $stmt = $this->conn->prepare(
            "UPDATE transactions SET payment_status = 'settlement' WHERE id = ? AND user_id = ? AND payment_status = 'pending'"
        );
        $stmt->bind_param("ii", $transactionId, $userId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function getTransaction($id, $userId)
    {
        $stmt = $this->conn->prepare(
            "SELECT t.*, p.code as promo_code, p.discount_percentage
             FROM transactions t
             LEFT JOIN promo_codes p ON t.promo_id = p.id
             WHERE t.id = ? AND t.user_id = ?"
        );
        $stmt->bind_param("ii", $id, $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function getDetails($transactionId)
    {
        $stmt = $this->conn->prepare(
            "SELECT td.price, gk.key_code, g.title, g.image
             FROM transaction_details td
             JOIN game_keys gk ON td.game_key_id = gk.id
             JOIN games g ON gk.game_id = g.id
             WHERE td.transaction_id = ?"
        );
        $stmt->bind_param("i", $transactionId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserHistory($userId)
    {
        $stmt = $this->conn->prepare(
            "SELECT t.*, p.code as promo_code, COUNT(td.id) as item_count
             FROM transactions t
             LEFT JOIN promo_codes p ON t.promo_id = p.id
             LEFT JOIN transaction_details td ON t.id = td.transaction_id
             WHERE t.user_id = ?
             GROUP BY t.id
             ORDER BY t.created_at DESC"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function clearCart($userId)
    {
        $stmt = $this->conn->prepare(
            "DELETE ci FROM cart_items ci JOIN carts c ON ci.cart_id = c.id WHERE c.user_id = ?"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    }
}