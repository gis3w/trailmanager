<?php
$get = $_GET;
unset($get['url']);
$ch = curl_init($_GET['url'].'?'.http_build_query($get));
$output = curl_exec($ch);
echo $output;
?>
