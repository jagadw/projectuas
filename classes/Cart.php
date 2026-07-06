<?php
class Cart extends Database {
    public function __construct() {
        parent::__construct();
    }

    public function addToCart($user_id, $game_id) {
        $stmtCart = $this->conn->prepare("SELECT id FROM carts WHERE user_id = ?");
        $stmtCart->bind_param("i", $user_id);
        $stmtCart->execute();
        $resCart = $stmtCart->get_result();
        
        if($resCart->num_rows > 0) {
            $cart = $resCart->fetch_assoc();
            $cart_id = $cart['id'];
        } else {
            $stmtNew = $this->conn->prepare("INSERT INTO carts (user_id) VALUES (?)");
            $stmtNew->bind_param("i", $user_id);
            $stmtNew->execute();
            $cart_id = $this->conn->insert_id;
        }

        $cek = $this->conn->prepare("SELECT id FROM cart_items WHERE cart_id = ? AND game_id = ?");
        $cek->bind_param("ii", $cart_id, $game_id);
        $cek->execute();
        $result = $cek->get_result();
        
        if($result->num_rows > 0) {
            return true;
        }

        $stmt = $this->conn->prepare("INSERT INTO cart_items (cart_id, game_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $cart_id, $game_id);
        return $stmt->execute();
    }

    public function getUserCart($user_id) {
        $query = "SELECT ci.id as cart_id, g.* FROM carts c JOIN cart_items ci ON c.id = ci.cart_id JOIN games g ON ci.game_id = g.id WHERE c.user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $cart = [];
        while($row = $result->fetch_assoc()) {
            $cart[] = $row;
        }
        return $cart;
    }

    public function remove($cart_item_id, $user_id) {
        $stmt = $this->conn->prepare("DELETE ci FROM cart_items ci JOIN carts c ON ci.cart_id = c.id WHERE ci.id = ? AND c.user_id = ?");
        $stmt->bind_param("ii", $cart_item_id, $user_id);
        return $stmt->execute();
    }

    public function removeByGameId($game_id, $user_id) {
        $stmt = $this->conn->prepare("DELETE ci FROM cart_items ci JOIN carts c ON ci.cart_id = c.id WHERE ci.game_id = ? AND c.user_id = ?");
        $stmt->bind_param("ii", $game_id, $user_id);
        return $stmt->execute();
    }
}
?>
