<?php
class AdminTicket extends Admin
{
    public function getTickets($filter)
    {
        if (in_array($filter, ['open', 'closed'], true)) {
            $stmt = $this->conn->prepare("
                SELECT t.*, u.username, u.email
                FROM tickets t
                LEFT JOIN users u ON u.id = t.user_id
                WHERE t.status = ?
                ORDER BY t.created_at DESC
            ");
            $stmt->bind_param("s", $filter);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        $stmt = $this->conn->prepare("
            SELECT t.*, u.username, u.email
            FROM tickets t
            LEFT JOIN users u ON u.id = t.user_id
            ORDER BY t.created_at DESC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateStatus($ticketId, $status)
    {
        if ($ticketId > 0) {
            $stmt = $this->conn->prepare("UPDATE tickets SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $ticketId);
            return $stmt->execute();
        }
        return false;
    }
}
