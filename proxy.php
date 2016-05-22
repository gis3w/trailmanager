<?php
$get = $_GET;
unset($get['url']);

$url = parse_url($_GET['url'], PHP_URL_SCHEME ).'://'.parse_url($_GET['url'], PHP_URL_HOST ).':'.parse_url($_GET['url'], PHP_URL_PORT ).parse_url($_GET['url'], PHP_URL_PATH );
$query = [];
parse_str(parse_url($_GET['url'], PHP_URL_QUERY ), $query);
foreach ($query as $k => $v)
{
    $get[$k] = $v;
}
$ch = curl_init($url.'?'.http_build_query($get));
$output = curl_exec($ch);
echo $output;

