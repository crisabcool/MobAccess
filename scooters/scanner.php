<!DOCTYPE html>
<html>
<head>
    <title>Ler QR Code</title>

    https://unpkg.com/html5-qrcode
</head>
<body>

<h2>Escaneie o QR Code da Scooter</h2>

<div id="reader" style="width:500px;"></div>

<script>

function onScanSuccess(decodedText) {

    window.location =
        "validar_qr.php?codigo=" +
        encodeURIComponent(decodedText);

}

new Html5QrcodeScanner(
    "reader",
    { fps: 10, qrbox: 250 }
).render(onScanSuccess);

</script>

</body>
</html>
