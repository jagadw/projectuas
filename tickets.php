<?php
require_once 'config/config.php';
require_once 'classes/Ticket.php';

// Memastikan user sudah login (sudah dilakukan di templates/header.php, tapi aman jika dicek lagi)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$ticketModel = new Ticket();
$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Menangani pembuatan tiket baru
if (isset($_POST['create_ticket'])) {
    $category = $_POST['category'] ?? 'general';
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $attachment = null;

    if (empty($subject) || empty($message)) {
        $error = 'Subjek dan Pesan wajib diisi.';
    } else {
        // Jika kategori pembayaran, proses upload bukti pembayaran (opsional tapi disarankan)
        if ($category === 'payment' && isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['attachment']['tmp_name'];
            $fileName = $_FILES['attachment']['name'];
            $fileSize = $_FILES['attachment']['size'];
            $fileType = $_FILES['attachment']['type'];
            
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($fileExtension, $allowedExtensions)) {
                // Batasi ukuran file (maksimal 2MB)
                if ($fileSize < 2 * 1024 * 1024) {
                    $uploadFileDir = __DIR__ . '/public/uploads/tickets/';
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0777, true);
                    }
                    
                    $newFileName = 'ticket_' . time() . '_' . md5(uniqid()) . '.' . $fileExtension;
                    $dest_path = $uploadFileDir . $newFileName;
                    
                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $attachment = $newFileName;
                    } else {
                        $error = 'Terjadi kesalahan saat mengunggah file bukti.';
                    }
                } else {
                    $error = 'Ukuran berkas bukti terlalu besar (maksimal 2MB).';
                }
            } else {
                $error = 'Format file tidak didukung. Harap unggah file gambar (JPG, PNG, GIF, WEBP).';
            }
        }

        if (empty($error)) {
            // Simpan tiket ke database
            $ticketId = $ticketModel->createTicket($userId, $category, $subject, $message, $attachment);
            
            if ($ticketId) {
                if ($category === 'general') {
                    // Beri balasan dari chatbot otomatis
                    $botResponse = $ticketModel->getChatbotResponse($message);
                    $ticketModel->addChatbotReply($ticketId, $botResponse);
                    
                    // Tandai langsung resolved untuk chatbot agar tidak memenuhi inbox admin
                    $ticketModel->resolveTicket($ticketId);
                } else {
                    // Beri balasan otomatis dari sistem untuk Kategori Pembayaran
                    $systemResponse = "Halo! Terima kasih telah mengirimkan laporan kendala transaksi. Bukti transfer/pembayaran Anda sudah masuk ke sistem kami. Mohon tunggu beberapa saat selagi Admin memverifikasi dan merespon laporan Anda.";
                    $ticketModel->addChatbotReply($ticketId, $systemResponse);
                }
                
                header("Location: ticket_chat.php?id=" . $ticketId);
                exit;
            } else {
                $error = 'Gagal mengirimkan tiket bantuan. Harap coba lagi.';
            }
        }
    }
}

$myTickets = $ticketModel->getUserTickets($userId);
$action = $_GET['action'] ?? 'list';

$pageTitle = 'Bantuan Pelanggan';
require_once 'templates/header.php';
?>

<div class="support-header" style="margin-bottom: 24px;">
    <h2>Layanan Bantuan Pelanggan</h2>
    <p class="text-muted">Hubungi kami untuk kendala pembayaran atau tanyakan seputar cara pembelian.</p>
</div>

<?php if ($error): ?>
    <div class="alert danger" style="background: rgba(229,72,77,0.15); color: var(--red); padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(229,72,77,0.3);">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<?php if ($action === 'new'): ?>
    <!-- Form Pembuatan Tiket Baru -->
    <div class="support-card" style="margin-top: 0; margin-bottom: 40px;">
        <h3 class="support-title" style="margin-top:0;">Buat Tiket Bantuan</h3>
        <p class="support-desc">Pilih kategori kendala Anda untuk mendapatkan bantuan terbaik.</p>
        
        <form method="POST" action="tickets.php?action=new" enctype="multipart/form-data">
            <!-- Pilihan Kategori -->
            <div class="category-select-grid">
                <div class="category-card selected" id="catGeneral" onclick="selectCategory('general')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    <h3>Pertanyaan Umum</h3>
                    <p>Cara beli, info game, promo (Dijawab instan oleh Bot)</p>
                </div>
                <div class="category-card" id="catPayment" onclick="selectCategory('payment')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                    <h3>Kendala Transaksi & Aktivasi</h3>
                    <p>Pembayaran gagal, salah transfer, atau kode redeem error/tidak bisa dipakai (Wajib melampirkan screenshot bukti untuk ditinjau Admin)</p>
                </div>
            </div>
            
            <input type="hidden" name="category" id="ticketCategory" value="general">
            
            <!-- Bidang Input Form -->
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display:block; margin-bottom: 6px; font-weight:600;">Subjek / Judul Permasalahan</label>
                <input type="text" name="subject" class="chat-input" style="width:100%; box-sizing:border-box;" placeholder="Contoh: Menanyakan cara menggunakan promo code" required>
            </div>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display:block; margin-bottom: 6px; font-weight:600;">Deskripsi Kendala</label>
                <textarea name="message" class="chat-input" style="width:100%; height:120px; resize:vertical; box-sizing:border-box;" placeholder="Tuliskan sedetail mungkin kendala atau pertanyaan Anda..." required></textarea>
            </div>
            
            <!-- Upload Bukti (Hanya untuk Kategori Pembayaran) -->
            <div class="form-group" id="attachmentGroup" style="display:none; margin-bottom: 24px;">
                <label style="display:block; margin-bottom: 6px; font-weight:600;">Unggah Bukti Transfer / Screenshot Error Aktivasi</label>
                <input type="file" name="attachment" accept="image/*" class="chat-input" style="width:100%; box-sizing:border-box;">
                <small class="text-muted" style="display:block; margin-top:4px;">Format didukung: JPG, JPEG, PNG, GIF, WEBP. Maksimal ukuran berkas 2MB. (Wajib lampirkan screenshot jika kode redeem tidak bisa dipakai atau ada error saat aktivasi).</small>
            </div>
            
            <div style="display:flex; gap:12px;">
                <button type="submit" name="create_ticket" class="btn" style="flex:1;">Kirim Tiket Bantuan</button>
                <a href="tickets.php" class="btn btn-secondary" style="text-align:center; text-decoration:none; padding:10px 14px;">Batal</a>
            </div>
        </form>
    </div>

<?php else: ?>
    <!-- Daftar Tiket Aktif Pengguna -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
        <h3 style="margin:0;">Riwayat Bantuan Anda</h3>
        <a href="tickets.php?action=new" class="btn" style="text-decoration:none; font-size:14px; padding: 8px 14px;">Buat Tiket Baru</a>
    </div>

    <?php if (empty($myTickets)): ?>
        <div class="empty-state" style="text-align:center; padding: 48px; background:var(--surface); border: 1px solid var(--border); border-radius:12px;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 48px; height: 48px; color: var(--text-secondary); margin-bottom:16px;">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            <p style="color:var(--text-secondary); margin-bottom:16px;">Belum ada riwayat tiket bantuan.</p>
            <a href="tickets.php?action=new" class="btn" style="text-decoration:none; display:inline-block;">Buat Tiket Pertama</a>
        </div>
    <?php else: ?>
        <div class="table-wrap" style="background:var(--surface); border:1px solid var(--border); border-radius:12px; overflow:hidden;">
            <table class="table" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:var(--surface2); border-bottom: 1px solid var(--border); text-align:left;">
                        <th style="padding:14px;">Subjek</th>
                        <th style="padding:14px;">Kategori</th>
                        <th style="padding:14px;">Status</th>
                        <th style="padding:14px;">Tanggal Dibuat</th>
                        <th style="padding:14px; text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($myTickets as $ticket): ?>
                        <tr style="border-bottom: 1px solid var(--border); transition: background 0.15s;">
                            <td style="padding:14px; font-weight:600; color:var(--text);">
                                <?php echo htmlspecialchars($ticket['subject']); ?>
                            </td>
                            <td style="padding:14px;">
                                <span class="badge" style="background: var(--surface2); border: 1px solid var(--border); padding: 4px 8px; border-radius: 4px; font-size:12px;">
                                    <?php echo $ticket['category'] === 'payment' ? 'Transaksi & Aktivasi' : 'Pertanyaan Umum'; ?>
                                </span>
                            </td>
                            <td style="padding:14px;">
                                <?php
                                $statusClass = 'status-' . $ticket['status'];
                                $statusLabel = 'Pending';
                                if ($ticket['status'] === 'processing') {
                                    $statusLabel = 'Diproses';
                                } elseif ($ticket['status'] === 'resolved') {
                                    $statusLabel = 'Selesai';
                                }
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo $statusLabel; ?>
                                </span>
                            </td>
                            <td style="padding:14px; color:var(--text-secondary); font-size:13px;">
                                <?php echo date('d M Y H:i', strtotime($ticket['created_at'])); ?>
                            </td>
                            <td style="padding:14px; text-align:right;">
                                <a href="ticket_chat.php?id=<?php echo $ticket['id']; ?>" class="btn btn-secondary" style="text-decoration:none; font-size:13px; padding:6px 12px;">Buka Chat</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php endif; ?>

<script>
function selectCategory(category) {
    document.getElementById('ticketCategory').value = category;
    
    // Toggle visual selection
    const generalCard = document.getElementById('catGeneral');
    const paymentCard = document.getElementById('catPayment');
    const attachmentGroup = document.getElementById('attachmentGroup');
    
    if (category === 'general') {
        generalCard.classList.add('selected');
        paymentCard.classList.remove('selected');
        attachmentGroup.style.display = 'none';
    } else {
        paymentCard.classList.add('selected');
        generalCard.classList.remove('selected');
        attachmentGroup.style.display = 'block';
    }
}
</script>

<?php require_once 'templates/footer.php'; ?>
