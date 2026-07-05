<?php
class Promo extends Database
{
    public function __construct()
    {
        parent::__construct();
    }

    public function validateCode($code)
    {
        $code = strtoupper(trim($code));
        $stmt = $this->conn->prepare(
            "SELECT * FROM promo_codes WHERE code = ? AND (valid_until IS NULL OR valid_until > NOW())"
        );
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ?: null;
    }

    public function calcDiscount($promo, $subtotal)
    {
        $discount = $subtotal * ($promo['discount_percentage'] / 100);
        if ($promo['max_discount'] > 0) {
            $discount = min($discount, (float) $promo['max_discount']);
        }
        return round($discount, 2);
    }
}   