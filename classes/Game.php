<?php
class Game extends Database {
    public function __construct() {
        parent::__construct();
    }

    public function getAllGames() {
        $query = "SELECT * FROM games";
        $result = $this->conn->query($query);
        
        $games = [];
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $games[] = $row;
            }
        }
        return $games;
    }
}
?>
