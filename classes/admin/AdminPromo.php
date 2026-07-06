<?php
class AdminPromo extends Admin
{
    public function getPromo($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM promo_codes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getPromos()
    {
        $stmt = $this->conn->prepare("SELECT * FROM promo_codes ORDER BY valid_until DESC, id DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function savePromo($data)
    {
        $id = (int) ($data['id'] ?? 0);
        $code = strtoupper(trim($data['code'] ?? ''));
        $discount = (float) ($data['discount_percentage'] ?? 0);
        $maxDiscount = (float) ($data['max_discount'] ?? 0);
        $validUntil = trim($data['valid_until'] ?? '');
        $validUntil = $validUntil ? str_replace('T', ' ', $validUntil) . ':00' : null;

        if ($id > 0) {
            $stmt = $this->conn->prepare("UPDATE promo_codes SET code = ?, discount_percentage = ?, max_discount = ?, valid_until = ? WHERE id = ?");
            $stmt->bind_param("sddsi", $code, $discount, $maxDiscount, $validUntil, $id);
            return $stmt->execute();
        } else {
            $stmt = $this->conn->prepare("INSERT INTO promo_codes (code, discount_percentage, max_discount, valid_until) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sdds", $code, $discount, $maxDiscount, $validUntil);
            return $stmt->execute();
        }
    }

    public function deletePromo($id)
    {
        if ($id > 0) {
            $stmt = $this->conn->prepare("DELETE FROM promo_codes WHERE id = ?");
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        }
        return false;
    }
}
