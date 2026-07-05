<?php
class Feedback extends Database
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getByTransactionAndGame($userId, $transactionId, $gameId)
    {
        $stmt = $this->conn->prepare(
            "SELECT f.* FROM feedbacks f
             JOIN transactions t ON f.user_id = t.user_id
             WHERE f.user_id = ? AND f.game_id = ? AND t.id = ?
             LIMIT 1"
        );
        $stmt->bind_param("iii", $userId, $gameId, $transactionId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function submit($userId, $gameId, $rating, $comment)
    {
        $exists = $this->conn->prepare(
            "SELECT id FROM feedbacks WHERE user_id = ? AND game_id = ?"
        );
        $exists->bind_param("ii", $userId, $gameId);
        $exists->execute();
        if ($exists->get_result()->fetch_assoc()) {
            return false;
        }

        $rating  = max(1, min(5, (int)$rating));
        $comment = htmlspecialchars(strip_tags(trim($comment)), ENT_QUOTES, 'UTF-8');

        $stmt = $this->conn->prepare(
            "INSERT INTO feedbacks (user_id, game_id, rating, comment) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("iiis", $userId, $gameId, $rating, $comment);
        return $stmt->execute();
    }

    public function getAllForAdmin()
    {
        $stmt = $this->conn->prepare(
            "SELECT f.id, f.rating, f.comment, f.created_at,
                    u.username,
                    g.title as game,
                    g.id as game_id
             FROM feedbacks f
             JOIN users u ON f.user_id = u.id
             JOIN games g ON f.game_id = g.id
             ORDER BY f.created_at DESC"
        );
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM feedbacks WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}