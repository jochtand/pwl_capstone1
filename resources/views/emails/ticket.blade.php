<!DOCTYPE html>
<html>
<head>
    <title>E-Ticket Kamu Sudah Siap!</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
<h2>Halo!</h2>
<p>Terima kasih sudah memesan tiket untuk event <strong>{{ $transaction->ticketCategory->event->title }}</strong>.</p>

<p>Pembayaran kamu sudah berhasil diverifikasi. Bersama email ini, kami melampirkan E-Ticket kamu dalam format PDF.</p>

<p>Mohon unduh lampiran PDF tersebut dan tunjukkan QR Code di dalamnya kepada panitia (bisa di-print atau langsung dari HP) saat acara berlangsung.</p>

<br>
<p>Sampai jumpa di lokasi!</p>
<p><strong>Tim Panitia</strong></p>
</body>
</html>
