<?php
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo "Invalid Data";
    exit;
}

$configContent = "<?php\n" .
"\$consumer_key = '" . addslashes($data['consumer_key']) . "';\n" .
"\$consumer_secret = '" . addslashes($data['consumer_secret']) . "';\n" .
"\$site_url = '" . addslashes($data['site_url']) . "';\n\n" .
"\$tally_company = \"" . addslashes($data['tally_company']) . "\";\n" .
"?>";

file_put_contents(__DIR__ . '/config.php', $configContent);

echo "Configuration saved!";
?>
