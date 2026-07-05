<?php
class AdminTicket extends Admin
{
    public function getTickets($filter)
    {
        if (in_array($filter, ['pending', 'processing', 'resolved'], true)) {
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

        // Default 'all'
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

    public function getTicketDetails($ticketId)
    {
        $stmt = $this->conn->prepare("
            SELECT t.*, u.username, u.email
            FROM tickets t
            LEFT JOIN users u ON u.id = t.user_id
            WHERE t.id = ?
        ");
        $stmt->bind_param("i", $ticketId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getReplies($ticketId)
    {
        $stmt = $this->conn->prepare("
            SELECT tr.*, u.username 
            FROM ticket_replies tr 
            LEFT JOIN users u ON u.id = tr.sender_id 
            WHERE tr.ticket_id = ? 
            ORDER BY tr.created_at ASC
        ");
        $stmt->bind_param("i", $ticketId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function addAdminReply($ticketId, $adminId, $message)
    {
        $stmt = $this->conn->prepare("INSERT INTO ticket_replies (ticket_id, sender_role, sender_id, message) VALUES (?, 'admin', ?, ?)");
        $stmt->bind_param("iis", $ticketId, $adminId, $message);
        if ($stmt->execute()) {
            // Jika admin membalas tiket yang berstatus pending, ubah otomatis statusnya menjadi 'processing'
            $ticket = $this->getTicketDetails($ticketId);
            if ($ticket && $ticket['status'] === 'pending') {
                $this->updateStatus($ticketId, 'processing');
            }
            return true;
        }
        return false;
    }

    public function updateStatus($ticketId, $status)
    {
        if ($ticketId > 0 && in_array($status, ['pending', 'processing', 'resolved'], true)) {
            $stmt = $this->conn->prepare("UPDATE tickets SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $ticketId);
            return $stmt->execute();
        }
        return false;
    }
}
?>
