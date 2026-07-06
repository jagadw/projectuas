<?php
require_once 'config/config.php';
require_once 'classes/Ticket.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$ticketModel = new Ticket();
$userId = $_SESSION['user_id'];
$ticketId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil detail tiket dan pastikan tiket milik user yang login
$ticket = $ticketModel->getTicketDetails($ticketId, $userId);
if (!$ticket) {
    header("Location: tickets.php");
    exit;
}

// Tangani kiriman chat baru
if (isset($_POST['send_message'])) {
    $message = trim($_POST['message'] ?? '');
    if (!empty($message)) {
        // Simpan balasan user
        if ($ticketModel->addUserReply($ticketId, $userId, $message)) {
            // Jika kategori general (chatbot), balas otomatis lagi
            if ($ticket['category'] === 'general') {
                $botResponse = $ticketModel->getChatbotResponse($message);
                $ticketModel->addChatbotReply($ticketId, $botResponse);
                $ticketModel->resolveTicket($ticketId);
            }
            
            header("Location: ticket_chat.php?id=" . $ticketId);
            exit;
        }
    }
}

// Tangani penutupan tiket manual
if (isset($_POST['close_ticket'])) {
    $ticketModel->resolveTicket($ticketId);
    header("Location: ticket_chat.php?id=" . $ticketId);
    exit;
}

// Ambil semua balasan
$replies = $ticketModel->getReplies($ticketId);
$myTickets = $ticketModel->getUserTickets($userId);

$pageTitle = 'Chat Bantuan #' . $ticketId;
require_once 'templates/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
    <a href="tickets.php" class="text-muted" style="text-decoration:none; display:flex; align-items:center; gap:6px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Kembali ke Riwayat Tiket
    </a>
    
    <div>
        <?php if ($ticket['status'] !== 'resolved'): ?>
            <form method="POST" style="display:inline;">
                <button type="submit" name="close_ticket" class="btn btn-secondary" style="font-size:12px; padding:6px 12px; border-color:var(--green); color:var(--green);">
                    Tandai Selesai
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="chat-container">
    <!-- Panel Kiri: Riwayat Chat Lainnya -->
    <div class="chat-sidebar hide-on-mobile">
        <div class="chat-sidebar-header">
            <h3 style="margin:0; font-size:15px; font-weight:700;">Tiket Bantuan</h3>
            <a href="tickets.php?action=new" class="nav-icon-btn" title="Buat Tiket Baru" style="width:28px; height:28px;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px; height:16px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </a>
        </div>
        <div class="chat-sidebar-list">
            <?php foreach ($myTickets as $t): ?>
                <a href="ticket_chat.php?id=<?php echo $t['id']; ?>" class="chat-room-item <?php echo $t['id'] === $ticketId ? 'active' : ''; ?>">
                    <div class="chat-room-title">
                        <span>#<?php echo $t['id']; ?> - <?php echo htmlspecialchars(substr($t['subject'], 0, 20)) . (strlen($t['subject']) > 20 ? '...' : ''); ?></span>
                        <?php
                        $statusClass = 'status-' . $t['status'];
                        $statusLabel = $t['status'] === 'resolved' ? 'Selesai' : ($t['status'] === 'processing' ? 'Diproses' : 'Pending');
                        ?>
                        <span class="status-badge <?php echo $statusClass; ?>" style="font-size:9px; padding:1px 6px; min-height:16px;">
                            <?php echo $statusLabel; ?>
                        </span>
                    </div>
                    <div class="chat-room-meta">
                        <span><?php echo $t['category'] === 'payment' ? 'Transaksi' : 'Umum'; ?></span>
                        <span><?php echo date('d/m/y', strtotime($t['created_at'])); ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Panel Kanan: Workspace Chat -->
    <div class="chat-main">
        <div class="chat-header">
            <div>
                <h3><?php echo htmlspecialchars($ticket['subject']); ?></h3>
                <span class="text-muted" style="font-size:12px;">
                    Kategori: <b><?php echo $ticket['category'] === 'payment' ? 'Transaksi & Aktivasi' : 'Pertanyaan Umum'; ?></b>
                </span>
            </div>
            
            <div style="text-align:right;">
                <?php
                $statusClass = 'status-' . $ticket['status'];
                $statusLabel = $ticket['status'] === 'resolved' ? 'Selesai' : ($ticket['status'] === 'processing' ? 'Diproses' : 'Pending');
                ?>
                <span class="status-badge <?php echo $statusClass; ?>">
                    Status: <?php echo $statusLabel; ?>
                </span>
            </div>
        </div>

        <div class="chat-body" id="chatBody">
            <!-- Pesan Pembuka Tiket -->
            <div class="chat-bubble chat-bubble-other">
                <span class="chat-bubble-sender"><?php echo htmlspecialchars($_SESSION['username']); ?> (Pembuat Tiket)</span>
                <p><?php echo render_message($ticket['message']); ?></p>
                
                <?php if (!empty($ticket['attachment'])): ?>
                    <div class="chat-attachment">
                        <a href="public/uploads/tickets/<?php echo $ticket['attachment']; ?>" target="_blank">
                            <img src="public/uploads/tickets/<?php echo $ticket['attachment']; ?>" alt="Bukti Lampiran">
                        </a>
                        <small style="display:block; padding: 6px; font-size:10px; color: var(--text-secondary); text-align:center;">Klik gambar untuk memperbesar</small>
                    </div>
                <?php endif; ?>
                
                <span class="chat-bubble-meta"><?php echo date('H:i', strtotime($ticket['created_at'])); ?></span>
            </div>

            <!-- Pesan Balasan -->
            <?php foreach ($replies as $reply): ?>
                <?php
                if ($reply['sender_role'] === 'user') {
                    $bubbleClass = 'chat-bubble-user';
                    $senderName = htmlspecialchars($reply['username'] ?? 'Saya');
                } elseif ($reply['sender_role'] === 'chatbot') {
                    $bubbleClass = 'chat-bubble-chatbot';
                    $senderName = 'Chatbot SEDEEM';
                } else {
                    $bubbleClass = 'chat-bubble-other';
                    $senderName = 'Admin SEDEEM';
                }
                ?>
                <div class="chat-bubble <?php echo $bubbleClass; ?>">
                    <span class="chat-bubble-sender" style="<?php echo $reply['sender_role'] === 'chatbot' ? 'color: var(--accent);' : ''; ?>">
                        <?php echo $senderName; ?>
                    </span>
                    <p><?php echo render_message($reply['message']); ?></p>
                    <span class="chat-bubble-meta"><?php echo date('H:i', strtotime($reply['created_at'])); ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Form Input Chat -->
        <div class="chat-footer">
            <form method="POST" class="chat-form" id="chatForm">
                <input type="text" name="message" id="chatInput" class="chat-input" placeholder="Tulis tanggapan Anda di sini..." autocomplete="off" required>
                <button type="submit" name="send_message" class="btn" style="padding: 10px 20px;">Kirim</button>
            </form>
        </div>
    </div>
</div>

<script>
// Auto scroll chat body ke paling bawah
const chatBody = document.getElementById('chatBody');
chatBody.scrollTop = chatBody.scrollHeight;

// Tambahkan listener untuk input agar selalu fokus
document.getElementById('chatInput').focus();
</script>

<?php require_once 'templates/footer.php'; ?>
