<?php
$ch = curl_init('http://localhost/educms/public/admin/login');
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
$res = curl_exec($ch);
file_put_contents('header_out.txt', $res);
echo "DONE writing header_out.txt, length: " . strlen($res);
