<?php

$m = new mysqli('127.0.0.1', 'root', '', 'educms');
$res = $m->query("SELECT * FROM redirects");
echo "REDIRECTS IN DB:\n";
while ($row = $res->fetch_assoc()) {
    echo "ID: {$row['id']}, Source: {$row['source_url']}, Target: {$row['target_url']}, Type: {$row['redirect_type']}\n";
}
