<?php
class Game extends Database {
    public function __construct() {
        parent::__construct();
    }

    public function getAllGames() {
        $query = "
            SELECT g.*, 
                   (SELECT GROUP_CONCAT(gr.name SEPARATOR ', ') 
                    FROM game_genres gg 
                    JOIN genres gr ON gg.genre_id = gr.id 
                    WHERE gg.game_id = g.id) as genre_name,
                   (SELECT COUNT(id) FROM game_keys WHERE game_id = g.id AND status = 'available') as stock
            FROM games g
        ";
        $result = $this->conn->query($query);
        
        $games = [];
        if($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $games[] = $row;
            }
        }
        return $games;
    }

    public function getAllGenres() {
        $query = "SELECT name FROM genres ORDER BY name";
        $result = $this->conn->query($query);
        $genres = [];
        if($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $genres[] = $row['name'];
            }
        }
        return $genres;
    }
}
?>
