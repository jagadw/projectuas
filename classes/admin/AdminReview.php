<?php

class AdminReview extends Admin
{
    public function getReviews()
    {
        $stmt = $this->conn->prepare("
            SELECT f.*, u.username, g.title game_title
            FROM feedbacks f
            LEFT JOIN users u ON u.id = f.user_id
            LEFT JOIN games g ON g.id = f.game_id
            ORDER BY f.created_at DESC, f.id DESC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function deleteReview($id)
    {
        if ($id > 0) {
            $stmt = $this->conn->prepare("DELETE FROM feedbacks WHERE id = ?");
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        }
        return false;
    }
}
