<?php
class AdminTransaction extends Admin
{
    public function getTransactions($filter)
    {
        $where = '';
        $types = '';
        $params = [];

        if ($filter === 'pending') {
            $where = "WHERE t.payment_status = 'pending'";
        } elseif ($filter === 'failed') {
            $where = "WHERE t.payment_status IN ('expire', 'cancel', 'deny')";
        } elseif (in_array($filter, ['settlement', 'expire', 'cancel', 'deny'], true)) {
            $where = 'WHERE t.payment_status = ?';
            $types = 's';
            $params[] = $filter;
        }

        $stmt = $this->conn->prepare("
            SELECT t.*, u.username, p.code promo_code
            FROM transactions t
            LEFT JOIN users u ON u.id = t.user_id
            LEFT JOIN promo_codes p ON p.id = t.promo_id
            $where
            ORDER BY t.created_at DESC, t.id DESC
        ");

        if ($types != '') {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getStatusCounts()
    {
        $stmt = $this->conn->prepare("SELECT payment_status, COUNT(*) total FROM transactions GROUP BY payment_status");
        $stmt->execute();
        $result = $stmt->get_result();

        $counts = [];
        while ($row = $result->fetch_assoc()) {
            $counts[$row['payment_status']] = $row['total'];
        }
        return $counts;
    }

    public function getFailedCount($counts)
    {
        return ($counts['expire'] ?? 0) + ($counts['cancel'] ?? 0) + ($counts['deny'] ?? 0);
    }
}
