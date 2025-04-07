<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file'])) {
    $file = basename($_POST['file']);
    $path = dirname(__DIR__) . '/_backups/' . $file; // ✅ Go one level up

    if (file_exists($path)) {
        // Send to Tally
        $xml = file_get_contents($path);

        $ch = curl_init("http://localhost:9000");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: text/xml"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        echo "Restore Successful: $file";
    } else {
        echo "❌ Backup file not found.";
    }
} else {
    echo "❌ Invalid Request.";
}
