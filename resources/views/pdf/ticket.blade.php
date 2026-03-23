<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>E-Ticket #TRX-{{ $transaction->id }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; padding: 20px; }
        .ticket-box { border: 2px dashed #4f46e5; padding: 20px; border-radius: 10px; text-align: center; max-width: 550px; margin: auto; }
        .header { background-color: #4f46e5; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .event-title { font-size: 22px; font-weight: bold; margin: 10px 0; }
        .badge { background: #e0e7ff; color: #3730a3; padding: 5px 12px; border-radius: 15px; font-size: 12px; font-weight: bold; }
        .details { text-align: left; margin: 20px 0; font-size: 14px; line-height: 1.6; }
        .qr-section { margin-top: 20px; padding: 15px; border: 1px solid #eee; display: inline-block; border-radius: 10px; }
        .status-valid { color: #10b981; font-weight: bold; margin-top: 10px; font-size: 14px; }
        .footer { font-size: 11px; color: #777; margin-top: 30px; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
<div class="ticket-box">
    <div class="header">
        <h2 style="margin:0;">TIKETAPP OFFICIAL E-TICKET</h2>
    </div>

    <div class="event-title">{{ $transaction->ticketCategory->event->title }}</div>
    <span class="badge">Kategori: {{ $transaction->ticketCategory->name }}</span>

    <div class="details">
        <p><strong>ID Pesanan:</strong> #TRX-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</p>
        <p><strong>Nama Pemesan:</strong> {{ Auth::user()->name }}</p>
        <p><strong>Waktu:</strong> {{ date('d F Y, H:i', strtotime($transaction->ticketCategory->event->start_date)) }} WIB</p>
        <p><strong>Lokasi:</strong> {{ $transaction->ticketCategory->event->location }}</p>
    </div>

    <div class="qr-section">
        @if($qrImage)
            <img src="{{ $qrImage }}" width="150">
        @else
            <p style="color:red;">QR Code Error</p>
        @endif
        <div class="status-valid">TIKET VALID - LUNAS</div>
    </div>

    <div class="footer">
        Tunjukkan QR Code ini saat check-in di lokasi.<br>
        Tiket ini bersifat rahasia. Dilarang menyebarkan foto tiket Anda.<br>
        &copy; 2026 TiketApp Event Management.
    </div>
</div>
</body>
</html>
