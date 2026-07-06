<?php
class AdminKey extends Admin
{
    public function restockKeys($gameId, $rawKeys)
    {
        $keys = array_filter(array_unique(array_map('trim', preg_split('/\r\n|\r|\n|,/', trim($rawKeys)))));
        $inserted = 0;

        foreach ($keys as $keyCode) {
            if ($gameId > 0 && $keyCode !== '') {
                $stmt = $this->conn->prepare("INSERT IGNORE INTO game_keys (game_id, key_code, status) VALUES (?, ?, 'available')");
                $stmt->bind_param("is", $gameId, $keyCode);
                $stmt->execute();
                $inserted += $stmt->affected_rows;
            }
        }

        return $inserted;
    }

    public function getGames()
    {
        $stmt = $this->conn->prepare("SELECT id, title FROM games ORDER BY title ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getStocks()
    {
        $stmt = $this->conn->prepare("
            SELECT g.id, g.title,
                SUM(CASE WHEN k.status = 'available' THEN 1 ELSE 0 END) available_stock,
                SUM(CASE WHEN k.status = 'sold' THEN 1 ELSE 0 END) sold_stock,
                COUNT(k.id) total_stock
            FROM games g
            LEFT JOIN game_keys k ON k.game_id = g.id
            GROUP BY g.id, g.title
            ORDER BY g.title ASC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getLatestKeys($limit = 80)
    {
        $stmt = $this->conn->prepare("
            SELECT k.*, g.title
            FROM game_keys k
            LEFT JOIN games g ON g.id = k.game_id
            ORDER BY k.id DESC
            LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
