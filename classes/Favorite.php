<?php
class Favorite extends Database {
    public function __construct() {
        parent::__construct();
    }

    public function addToFavorite($user_id, $game_id) {
        $cek = $this->conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND game_id = ?");
        $cek->bind_param("ii", $user_id, $game_id);
        $cek->execute();
        $result = $cek->get_result();
        
        if($result->num_rows > 0) return true;

        $stmt = $this->conn->prepare("INSERT INTO favorites (user_id, game_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $game_id);
        return $stmt->execute();
    }

    public function getUserFavorites($user_id) {
        $query = "SELECT f.id as fav_id, g.* FROM favorites f JOIN games g ON f.game_id = g.id WHERE f.user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $fav = [];
        while($row = $result->fetch_assoc()) {
            $fav[] = $row;
        }
        return $fav;
    }

    public function remove($fav_id, $user_id) {
        $stmt = $this->conn->prepare("DELETE FROM favorites WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $fav_id, $user_id);
        return $stmt->execute();
    }
}
?>
