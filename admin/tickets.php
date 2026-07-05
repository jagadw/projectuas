<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/admin/Admin.php';
require_once __DIR__ . '/../classes/admin/AdminTicket.php';

// Verifikasi akses admin dilakukan oleh admin_header.php, tapi panggil dulu classnya
$adminTicket = new AdminTicket();
$adminId = $_SESSION['user_id'] ?? 0;

$filter = $_GET['status'] ?? 'pending';
$ticketId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';
$success = '';

// Menangani update status dari POST
if (isset($_POST['update_status'])) {
    $targetTicketId = (int)$_POST['ticket_id'];
    $newStatus = $_POST['status'];
    if ($adminTicket->updateStatus($targetTicketId, $newStatus)) {
        $success = "Status tiket berhasil diubah menjadi: " . $newStatus;
        // Jika tiket yang diubah adalah tiket saat ini, segarkan halaman
        if ($targetTicketId === $ticketId) {
            header("Location: tickets.php?status=" . $filter . "&id=" . $ticketId);
            exit;
        }
    } else {
        $error = "Gagal mengubah status tiket.";
    }
}

// Menangani pengiriman balasan admin dari POST
if (isset($_POST['submit_reply'])) {
    $replyMsg = trim($_POST['reply_message'] ?? '');
    if (!empty($replyMsg) && $ticketId > 0) {
        if ($adminTicket->addAdminReply($ticketId, $adminId, $replyMsg)) {
            $success = "Balasan berhasil dikirim.";
            header("Location: tickets.php?status=" . $filter . "&id=" . $ticketId);
            exit;
        } else {
            $error = "Gagal mengirimkan balasan.";
        }
    }
}

// Ambil daftar tiket berdasarkan filter
$tickets = $adminTicket->getTickets($filter);

// Ambil detail tiket yang sedang dibuka
$selectedTicket = null;
$replies = [];
if ($ticketId > 0) {
    $selectedTicket = $adminTicket->getTicketDetails($ticketId);
    if ($selectedTicket) {
        $replies = $adminTicket->getReplies($ticketId);
    }
}

$pageTitle = 'Layanan Pelanggan & Chatbot';
require_once __DIR__ . '/../templates/admin/admin_header.php';
?>

<div style="margin-bottom: 16px; display: flex; justify-content: flex-end;">
    <div class="admin-tabs" style="margin:0;">
        <a href="tickets.php?status=pending" class="<?php echo $filter === 'pending' ? 'active' : ''; ?>">Pending</a>
        <a href="tickets.php?status=processing" class="<?php echo $filter === 'processing' ? 'active' : ''; ?>">Diproses</a>
        <a href="tickets.php?status=resolved" class="<?php echo $filter === 'resolved' ? 'active' : ''; ?>">Selesai</a>
        <a href="tickets.php?status=all" class="<?php echo $filter === 'all' ? 'active' : ''; ?>">Semua</a>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert danger" style="background: rgba(229,72,77,0.15); color: var(--red); padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(229,72,77,0.3);">
        <?php echo e($error); ?>
    </div>
<?php endif; ?>

<div class="chat-container">
    <!-- Panel Kiri: Daftar Tiket Masuk -->
    <div class="chat-sidebar">
        <div class="chat-sidebar-header">
            <h3 style="margin:0; font-size:15px; font-weight:700;">Tiket Masuk (<?php echo count($tickets); ?>)</h3>
        </div>
        <div class="chat-sidebar-list">
            <?php foreach ($tickets as $t): ?>
                <a href="tickets.php?status=<?php echo $filter; ?>&id=<?php echo $t['id']; ?>" class="chat-room-item <?php echo $t['id'] === $ticketId ? 'active' : ''; ?>">
                    <div class="chat-room-title">
                        <span>#<?php echo $t['id']; ?> - <?php echo e(substr($t['subject'], 0, 18)) . (strlen($t['subject']) > 18 ? '...' : ''); ?></span>
                        <span class="status-badge status-<?php echo e($t['status']); ?>" style="font-size:9px; padding:1px 6px; min-height:16px;">
                            <?php echo $t['status'] === 'resolved' ? 'Selesai' : ($t['status'] === 'processing' ? 'Diproses' : 'Pending'); ?>
                        </span>
                    </div>
                    <div class="chat-room-meta">
                        <span>User: <b><?php echo e($t['username'] ?? 'Guest'); ?></b></span>
                        <span style="font-size:11px;"><?php echo date('d/m/y H:i', strtotime($t['created_at'])); ?></span>
                    </div>
                    <div class="chat-room-meta" style="margin-top: 2px;">
                        <span class="badge" style="font-size:10px; background: rgba(var(--accent-rgb), 0.1); padding: 2px 6px; border-radius:4px;">
                            <?php echo $t['category'] === 'payment' ? 'Transaksi' : 'Umum (Bot)'; ?>
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
            <?php if (empty($tickets)): ?>
                <div class="text-muted" style="padding:24px; text-align:center; font-size:13px;">Tidak ada tiket dengan status ini.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Panel Kanan: Workspace Balas Tiket -->
    <div class="chat-main">
        <?php if ($selectedTicket): ?>
            <!-- Header Tiket yang Terpilih -->
            <div class="chat-header">
                <div>
                    <h3 style="margin:0 0 4px 0;">#<?php echo $selectedTicket['id']; ?> - <?php echo e($selectedTicket['subject']); ?></h3>
                    <span class="text-muted" style="font-size:12px;">
                        Pelapor: <b><?php echo e($selectedTicket['username']); ?></b> (<?php echo e($selectedTicket['email']); ?>) | 
                        Kategori: <b><?php echo $selectedTicket['category'] === 'payment' ? 'Transaksi & Aktivasi' : 'Pertanyaan Umum'; ?></b>
                    </span>
                </div>
                
                <!-- Kelola Status Tiket -->
                <div style="display:flex; align-items:center; gap:8px;">
                    <form method="POST" style="display:flex; gap:6px; align-items:center;">
                        <input type="hidden" name="ticket_id" value="<?php echo $selectedTicket['id']; ?>">
                        <input type="hidden" name="update_status" value="1">
                        
                        <?php if ($selectedTicket['status'] !== 'resolved'): ?>
                            <?php if ($selectedTicket['status'] === 'pending'): ?>
                                <button type="submit" name="status" value="processing" class="btn" style="font-size:12px; padding:6px 12px; background: var(--green); border-color: var(--green);">
                                    Proses Tiket
                                </button>
                            <?php endif; ?>
                            <button type="submit" name="status" value="resolved" class="btn btn-secondary" style="font-size:12px; padding:6px 12px; border-color: var(--green); color: var(--green);">
                                Selesaikan Tiket
                            </button>
                        <?php else: ?>
                            <button type="submit" name="status" value="pending" class="btn btn-secondary" style="font-size:12px; padding:6px 12px;">
                                Buka Kembali Tiket
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Isi Chat History -->
            <div class="chat-body" id="adminChatBody">
                <!-- Pesan Awal / Laporan Pelanggan -->
                <div class="chat-bubble chat-bubble-other" style="border-left: 3px solid var(--accent);">
                    <span class="chat-bubble-sender"><?php echo e($selectedTicket['username']); ?> (Pelapor)</span>
                    <p><?php echo render_message($selectedTicket['message']); ?></p>
                    
                    <?php if (!empty($selectedTicket['attachment'])): ?>
                        <div class="chat-attachment">
                            <a href="../public/uploads/tickets/<?php echo $selectedTicket['attachment']; ?>" target="_blank">
                                <img src="../public/uploads/tickets/<?php echo $selectedTicket['attachment']; ?>" alt="Bukti Transfer">
                            </a>
                            <small style="display:block; padding: 6px; font-size:10px; color: var(--text-secondary); text-align:center;">Klik gambar untuk memperbesar</small>
                        </div>
                    <?php endif; ?>
                    <span class="chat-bubble-meta"><?php echo date('H:i', strtotime($selectedTicket['created_at'])); ?></span>
                </div>

                <!-- Balasan-balasan -->
                <?php foreach ($replies as $reply): ?>
                    <?php
                    if ($reply['sender_role'] === 'admin') {
                        $bubbleClass = 'chat-bubble-user';
                        $senderName = 'Admin: ' . htmlspecialchars($reply['username'] ?? 'Saya');
                    } elseif ($reply['sender_role'] === 'chatbot') {
                        $bubbleClass = 'chat-bubble-chatbot';
                        $senderName = 'Chatbot SEDEEM';
                    } else {
                        $bubbleClass = 'chat-bubble-other';
                        $senderName = htmlspecialchars($reply['username'] ?? 'User');
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

            <!-- Form Balasan Admin -->
            <div class="chat-footer">
                <form method="POST" class="chat-form">
                    <input type="hidden" name="submit_reply" value="1">
                    <input type="text" name="reply_message" id="adminChatInput" class="chat-input" placeholder="Ketik solusi atau tanggapan Anda..." autocomplete="off" required>
                    <button type="submit" class="btn" style="padding: 10px 20px;">Kirim</button>
                </form>
            </div>
        <?php else: ?>
            <!-- State Kosong jika tidak ada tiket yang dipilih -->
            <div style="flex:1; display:flex; flex-direction:column; justify-content:center; align-items:center; color: var(--text-secondary); padding:48px; text-align:center;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:64px; height:64px; margin-bottom:16px;">
                    <circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                <h3>Pilih Tiket Bantuan</h3>
                <p>Pilih tiket di sidebar sebelah kiri untuk melihat percakapan lengkap dan membalas pesan.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Auto scroll chat body ke paling bawah jika ada chat aktif
const adminChatBody = document.getElementById('adminChatBody');
if (adminChatBody) {
    adminChatBody.scrollTop = adminChatBody.scrollHeight;
}

// Auto fokus ke kolom chat admin jika ada chat aktif
const adminChatInput = document.getElementById('adminChatInput');
if (adminChatInput) {
    adminChatInput.focus();
}
</script>

<?php
require_once __DIR__ . '/../templates/admin/admin_footer.php';
?>