<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Google Authenticator</title>
</head>
<body>
    <h2>Scan this QR Code with Google Authenticator</h2>
    
    <!-- Display QR Code -->
    <img src="{{ $qrCodeUrl }}" alt="QR Code">

    <p>Or manually enter this key: <strong>{{ $secret }}</strong></p>

    <form method="POST" action="{{ route('2fa.verify') }}">
        @csrf
        <label for="code">Enter 6-digit Code:</label>
        <input type="text" name="code" required>
        <button type="submit">Verify</button>
    </form>
</body>
</html>
