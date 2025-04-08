<?php
// Your existing PHP code (unchanged)
include 'config.php';

$backupDir =  dirname(__DIR__) . '/_backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir);
}

$url = "$site_url/wp-json/wc/v3/products?consumer_key=$consumer_key&consumer_secret=$consumer_secret";
$response = file_get_contents($url);
$products = json_decode($response, true);

$xml = "<ENVELOPE><HEADER><TALLYREQUEST>Import Data</TALLYREQUEST></HEADER><BODY><IMPORTDATA><REQUESTDESC><REPORTNAME>All Masters</REPORTNAME></REQUESTDESC><REQUESTDATA>";

$productCount = 0;

foreach ($products as $p) {
    $name = htmlspecialchars($p['name']);
    $stock = $p['stock_quantity'] ?? 0;

    $xml .= "<TALLYMESSAGE>
    <STOCKITEM NAME=\"$name\" ACTION=\"Create\">
      <NAME>$name</NAME>
      <BASEUNITS>Nos</BASEUNITS>
      <OPENINGBALANCE>$stock Nos</OPENINGBALANCE>
    </STOCKITEM>
    </TALLYMESSAGE>";

    $productCount++;
}

$xml .= "</REQUESTDATA></IMPORTDATA></BODY></ENVELOPE>";

$timestamp = date("Ymd_His");
$backupFile = "$backupDir/backup_$timestamp.xml";
file_put_contents($backupFile, $xml);

$ch = curl_init("http://localhost:9000");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: text/xml"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

libxml_use_internal_errors(true);
$tally_xml = simplexml_load_string("<ROOT>$response</ROOT>");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Product Sync Result</title>
  <link href="https://fonts.googleapis.com/css2?family=Rubik&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css" >
</head>
<body>
  <div class="gradient-bg">
      <div class="content-lg">
        <h1>Tally Sync Summary</h1>

        <p>🔒 <b>Backup saved:</b> <code>backup_<?= $timestamp ?>.xml</code></p>
        <p><b><?= $productCount ?> Products Synced to Tally Successfully.</b></p>

        <h3>Tally Response</h3>
        <pre><?= htmlspecialchars($response) ?></pre>

        <h3>Summary</h3>
        <ul>
          <?php if ($tally_xml !== false && isset($tally_xml->RESPONSE)): ?>
            <li>🆕 Created: <b><?= $tally_xml->RESPONSE->CREATED ?></b></li>
            <li>🔁 Altered: <b><?= $tally_xml->RESPONSE->ALTERED ?></b></li>
            <li>🗑️ Deleted: <b><?= $tally_xml->RESPONSE->DELETED ?></b></li>
            <li>🚫 Cancelled: <b><?= $tally_xml->RESPONSE->CANCELLED ?></b></li>
            <li>📛 Errors: <b><?= $tally_xml->RESPONSE->ERRORS ?></b></li>
            <li>📥 Ignored: <b><?= $tally_xml->RESPONSE->IGNORED ?></b></li>
            <li>⚠️ Exceptions: <b><?= $tally_xml->RESPONSE->EXCEPTIONS ?></b></li>
          <?php else: ?>
            <li>⚠️ Unable to parse Tally response. Please ensure Tally is running with TDL and correct port.</li>
          <?php endif; ?>
        </ul>
      </div>
  </div>
  
</body>
</html>
