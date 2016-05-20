<?php
$ch = curl_init($_GET['url'].'?'.http_build_query($_GET));
$output = curl_exec($ch);
echo $output;
?>
