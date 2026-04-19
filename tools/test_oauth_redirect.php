<?php
$url = 'https://trackflow.mooo.com/auth/google/redirect';
$opts = array('http' => array('method' => 'GET', 'timeout' => 5));
$context = stream_context_create($opts);
$headers = @get_headers($url, 1, $context);
if ($headers === false) {
    echo "Failed to reach $url\n";
    exit(1);
}
var_export($headers);
echo "\n";
