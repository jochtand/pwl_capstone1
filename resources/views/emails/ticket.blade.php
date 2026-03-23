<!DOCTYPE html>
<html>
<head>
    <title>TiketApp</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
<h2>Halo, {{ Auth::user()->name }}! 👋</h2>
<p>Terima kasih udah beli tiket event <strong>{{ $transaction->ticketCategory->event->title }}</strong> lewat TiketApp.</p>

<p>Pembayaran kamu udah divalidasi dan lunas. E-Ticket resmi kamu udah diselipin di *attachment* email ini ya!</p>

<p>Silakan download file PDF-nya dan siapkan QR Code-nya saat proses validasi di lokasi.</p>

<br>
<p>Salam hangat,<br>
    <strong>Tim TiketApp</strong></p>
</body>
</html>
