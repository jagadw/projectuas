<?php
class AdminDashboard extends Admin
{
    public function getStats()
    {
        return [
            'games' => $this->countData('games'),
            'available_keys' => $this->countData('game_keys', "status = 'available'"),
            'pending' => $this->countData('transactions', "payment_status = 'pending'"),
            'open_tickets' => $this->countData('tickets', "status = 'open'"),
        ];
    }

    public function getRevenue()
    {
        $stmt = $this->conn->prepare("SELECT COALESCE(SUM(total_amount), 0) total FROM transactions WHERE payment_status = 'settlement'");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    public function getLatestTransactions($limit = 6)
    {
        $stmt = $this->conn->prepare("
            SELECT t.*, u.username
            FROM transactions t
            LEFT JOIN users u ON u.id = t.user_id
            ORDER BY t.created_at DESC
            LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getLowStocks($limit = 6)
    {
        $stmt = $this->conn->prepare("
            SELECT g.id, g.title, COUNT(k.id) available_stock
            FROM games g
            LEFT JOIN game_keys k ON k.game_id = g.id AND k.status = 'available'
            GROUP BY g.id, g.title
            ORDER BY available_stock ASC, g.title ASC
            LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function countData($table, $where = '')
    {
        $sql = "SELECT COUNT(*) total FROM $table";
        if ($where != '') {
            $sql .= " WHERE $where";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }
}
