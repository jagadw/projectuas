<?php
class Ticket extends Database {
    public function __construct() {
        parent::__construct();
    }

    public function createTicket($userId, $category, $subject, $message, $attachment = null) {
        $stmt = $this->conn->prepare("INSERT INTO tickets (user_id, category, subject, message, attachment, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("issss", $userId, $category, $subject, $message, $attachment);
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }

    public function getUserTickets($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM tickets WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTicketDetails($ticketId, $userId) {
        $stmt = $this->conn->prepare("SELECT * FROM tickets WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $ticketId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getReplies($ticketId) {
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

    public function addUserReply($ticketId, $userId, $message) {
        // Tambah balasan
        $stmt = $this->conn->prepare("INSERT INTO ticket_replies (ticket_id, sender_role, sender_id, message) VALUES (?, 'user', ?, ?)");
        $stmt->bind_param("iis", $ticketId, $userId, $message);
        if ($stmt->execute()) {
            // Ubah status tiket kembali ke pending jika sebelumnya diselesaikan/diproses agar admin tahu ada respon baru
            $updateStmt = $this->conn->prepare("UPDATE tickets SET status = 'pending' WHERE id = ?");
            $updateStmt->bind_param("i", $ticketId);
            $updateStmt->execute();
            return true;
        }
        return false;
    }

    public function addChatbotReply($ticketId, $message) {
        $stmt = $this->conn->prepare("INSERT INTO ticket_replies (ticket_id, sender_role, sender_id, message) VALUES (?, 'chatbot', NULL, ?)");
        $stmt->bind_param("is", $ticketId, $message);
        return $stmt->execute();
    }

    public function resolveTicket($ticketId) {
        $stmt = $this->conn->prepare("UPDATE tickets SET status = 'resolved' WHERE id = ?");
        $stmt->bind_param("i", $ticketId);
        return $stmt->execute();
    }

    public function getChatbotResponse($message) {
        $message = strtolower(trim($message));

        // 1. Game Mahal / Termahal
        if (preg_match('/(mahal|paling mahal|termahal|harga tinggi|paling tinggi)/', $message)) {
            return "Game termahal di toko kami saat ini adalah:\n\n" .
                   "1. <b>The Last of Us Part I</b> (Rp 850.000)\n" .
                   "2. <b>Forza Horizon 5</b> (Rp 750.000)\n" .
                   "3. <b>Ghost of Tsushima</b> (Rp 700.000)\n\n" .
                   "Semua game ini merupakan edisi premium original dengan kualitas grafis terbaik!";
        }

        // 2. Game Murah / Termurah / Gratis
        if (preg_match('/(murah|paling murah|termurah|harga rendah|gratis|free|hemat)/', $message)) {
            return "Berikut adalah game dengan harga paling terjangkau di SEDEEM:\n\n" .
                   "1. <b>Valorant</b> (Gratis / Rp 0)\n" .
                   "2. <b>Grand Theft Auto V</b> (Rp 150.000)\n" .
                   "3. <b>Palworld</b> (Rp 245.000)\n\n" .
                   "Cocok banget untuk Anda yang ingin bermain game seru dengan budget hemat!";
        }

        // 3. Game Booming / Terpopuler / Favorit
        if (preg_match('/(favorit|booming|populer|laris|rekomendasi|recomended|bagus|seru|hits)/', $message)) {
            return "Game yang sedang <b>booming & terfavorit</b> pilihan para gamer saat ini:\n\n" .
                   "- <b>Palworld</b>: Game survival dunia terbuka bareng monster lucu yang sangat populer.\n" .
                   "- <b>Ghost of Tsushima</b>: Petualangan samurai legendaris yang grafisnya sangat memukau.\n" .
                   "- <b>Valorant</b>: Game tembak-tembakan taktis 5v5 terpopuler saat ini.\n" .
                   "- <b>Grand Theft Auto V</b>: Game legendaris yang selalu ramai dimainkan.";
        }

        // 4. Cara Beli
        if (preg_match('/(beli|cara beli|cara belanja|pembelian|pesan|order|belinya|langkah beli)/', $message)) {
            return "Untuk membeli redeem code di SEDEEM, silakan ikuti langkah mudah ini:\n\n" .
                   "1. Pilih game yang Anda inginkan di halaman utama.\n" .
                   "2. Klik tombol <b>Beli</b> atau masukkan ke <b>Keranjang Belanja</b>.\n" .
                   "3. Masuk ke halaman Keranjang, masukkan kode promo (jika ada), lalu klik <b>Checkout</b>.\n" .
                   "4. Lakukan pembayaran melalui gerbang pembayaran Midtrans.\n" .
                   "5. Setelah pembayaran berhasil, redeem code Anda akan otomatis muncul di menu <b>Library Key</b> Anda.";
        }

        // 5. Daftar Game / Game yang ada
        if (preg_match('/(game|produk|game apa|daftar game|list game|katalog|game saja|ada game|jual game)/', $message)) {
            return "Di SEDEEM, kami menjual berbagai redeem code game populer. Berikut daftar game yang tersedia saat ini:\n\n" .
                   "- <b>Palworld</b> (Rp 245.000)\n" .
                   "- <b>Grand Theft Auto V</b> (Rp 150.000)\n" .
                   "- <b>Minecraft</b> (Rp 300.000)\n" .
                   "- <b>Valorant</b> (Gratis / Rp 0)\n" .
                   "- <b>Red Dead Redemption 2</b> (Rp 600.000)\n" .
                   "- <b>The Last of Us Part I</b> (Rp 850.000)\n" .
                   "- <b>Forza Horizon 5</b> (Rp 750.000)\n" .
                   "- <b>The Witcher 3: Wild Hunt</b> (Rp 250.000)\n" .
                   "- <b>Ghost of Tsushima</b> (Rp 700.000)\n\n" .
                   "Ketik <b>'game mahal'</b> atau <b>'game murah'</b> untuk melihat rekomendasi harga!";
        }

        // 6. Metode Pembayaran
        if (preg_match('/(bayar|pembayaran|midtrans|qris|gopay|transfer|rekening|bank|kartu kredit|bayarnya|pake apa)/', $message)) {
            return "Kami mendukung berbagai metode pembayaran aman via Midtrans:\n\n" .
                   "- <b>QRIS</b> (GoPay, OVO, Dana, LinkAja)\n" .
                   "- <b>Virtual Account Bank</b> (BCA, Mandiri, BNI, BRI, dll)\n" .
                   "- <b>Kartu Kredit / Debit</b>\n\n" .
                   "Selesaikan pembayaran Anda segera setelah checkout sebelum waktu pembayaran habis.";
        }

        // 7. Cara Redeem (mencocokkan "redeem", "reedem", "redeemnya", "reedemnya", "aktifkan", "klaim", "claim")
        if (preg_match('/(redeem|reedem|key|kode|code|aktifkan|aktivasi|aktif|claim|klaim|cara klaim)/', $message)) {
            return "Cara menggunakan/mengaktifkan redeem code game Anda:\n\n" .
                   "1. Buka menu <b>Library Key</b> di bar navigasi atas.\n" .
                   "2. Salin (copy) kode redeem game yang sudah Anda beli.\n" .
                   "3. Buka platform game terkait (misalnya Steam, Epic Games Launcher, atau PlayStation Store).\n" .
                   "4. Pilih opsi <b>'Activate a Product'</b> atau <b>'Redeem Code'</b>, lalu tempelkan (paste) kode tersebut.\n" .
                   "5. Game akan otomatis masuk ke pustaka (library) akun Anda dan siap untuk di-download.";
        }

        // 8. Promo / Diskon
        if (preg_match('/(diskon|promo|voucher|kupon|potongan|kode)/', $message)) {
            $promosList = "";
            $stmt = $this->conn->prepare("SELECT code, discount_percentage FROM promo_codes WHERE valid_until IS NULL OR valid_until > NOW() LIMIT 5");
            if ($stmt) {
                $stmt->execute();
                $result = $stmt->get_result();
                $promos = $result->fetch_all(MYSQLI_ASSOC);
                if (!empty($promos)) {
                    $promosList = "Berikut adalah beberapa <b>Promo Code</b> yang aktif saat ini:\n";
                    foreach ($promos as $p) {
                        $promosList .= "- Kode: <b>" . htmlspecialchars($p['code']) . "</b> (Potongan " . number_format($p['discount_percentage'], 0) . "%)\n";
                    }
                    $promosList .= "\n";
                }
            }

            if (empty($promosList)) {
                $promosList = "Saat ini belum ada Promo Code khusus yang sedang aktif di database. Silakan pantau halaman utama secara berkala!\n\n";
            }

            return $promosList . "Di See<b>dem</b>, promo tidak berlaku per game, melainkan berupa **Promo Code** (diskon total belanja) saat checkout.\n\n" .
                   "Cara menggunakan:\n" .
                   "1. Masukkan game pilihan Anda ke Keranjang Belanja.\n" .
                   "2. Masukkan kode promo di atas ke kolom <b>'Promo Code'</b> di halaman Keranjang.\n" .
                   "3. Klik tombol <b>Gunakan</b> untuk memotong total harga belanjaan Anda.";
        }


        // 9. Refund policy
        if (preg_match('/(refund|batal|kembalikan|salah beli|tukar)/', $message)) {
            return "Mohon maaf, semua pembelian redeem code game bersifat final. Kode yang telah berhasil terbit dan dikirim <b>tidak dapat dibatalkan, ditukar, atau di-refund</b>. Harap pastikan kembali judul game dan platform yang Anda pilih sebelum membayar.";
        }

        // 10. Greetings (Halo, Hai, P, dll)
        if (preg_match('/(halo|hai|hello|hi|helo|hei|p|pagi|siang|sore|malam)/', $message)) {
            return "Halo! Selamat datang di Layanan Bantuan See<b>dem</b>. Ada yang bisa saya bantu hari ini?\n\nAnda bisa bertanya kepada saya seputar:\n" .
                   "- <b>Cara Beli Game</b> (Ketik 'cara beli')\n" .
                   "- <b>Metode Pembayaran</b> (Ketik 'pembayaran')\n" .
                   "- <b>Cara Aktivasi (Redeem Code)</b> (Ketik 'cara redeem')\n" .
                   "- <b>Promo & Diskon</b> (Ketik 'promo')\n" .
                   "- <b>Daftar Game</b> (Ketik 'daftar game')\n\n" .
                   "Silakan ketikkan pertanyaan Anda!";
        }

        // 11. Terimakasih / Thanks
        if (preg_match('/(terima kasih|terimakasih|makasih|suwun|thanks|thank you|thx|tq)/', $message)) {
            return "Sama-sama! Senang sekali bisa membantu Anda. 😊\n\nJika ada pertanyaan lain seputar See<b>dem</b>, silakan ketik langsung di sini. Semoga harimu menyenangkan!";
        }

        // 12. Identitas Chatbot (Siapa kamu, dll)
        if (preg_match('/(siapa kamu|siapa sih|nama kamu|bot|chatbot)/', $message)) {
            return "Saya adalah Chatbot See<b>dem</b>, asisten virtual yang siap membantu menjawab pertanyaan umum Anda 24/7!\n\nJika Anda memiliki kendala teknis (seperti pembayaran yang tidak masuk), silakan eskalasikan dengan mengirim tiket kategori <b>'Kendala Transaksi / Pembayaran'</b> agar langsung dibantu oleh Admin kami.";
        }

        // 13. Bantuan Umum / DLL
        if (preg_match('/(tanya|bantu|dll|apa aja|fitur|menu|bisa apa)/', $message)) {
            return "Saya diprogram untuk melayani berbagai pertanyaan umum. Berikut beberapa topik yang saya kuasai:\n\n" .
                   "1. <b>Panduan Pembelian</b> (Ketik: 'cara beli')\n" .
                   "2. <b>Info Transaksi</b> (Ketik: 'pembayaran')\n" .
                   "3. <b>Cara Klaim Game</b> (Ketik: 'cara redeem')\n" .
                   "4. <b>Info Diskon</b> (Ketik: 'promo')\n" .
                   "5. <b>Katalog Game</b> (Ketik: 'daftar game', 'game termahal', atau 'game terpopuler')\n\n" .
                   "Silakan ketik kata kunci di atas untuk mencobanya!";
        }

        // Default response
        return "Terima kasih telah menghubungi kami. Saya adalah Chatbot See<b>dem</b>.\n\nPertanyaan Anda belum berhasil saya pahami secara otomatis.\n\nJika ini merupakan pertanyaan umum (seperti cara beli, cara redeem, daftar game), silakan ketik kata kunci singkat seperti 'cara beli', 'cara redeem', 'daftar game', atau 'pembayaran'.\n\nJika Anda mengalami kendala pembayaran gagal, silakan buat tiket baru dengan kategori 'Kendala Transaksi / Pembayaran' serta lampirkan screenshot bukti kendala Anda agar bisa langsung ditangani oleh Admin.";
    }
}
?>
