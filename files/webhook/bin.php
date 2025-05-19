<?php
// bin.php
date_default_timezone_set('Asia/Seoul');
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/plain; charset=utf-8');

// data 디렉터리 보장
$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

// 모든 헤더 수집 (Apache/Nginx 호환)
$allHeaders = function_exists('getallheaders')
    ? getallheaders()
    : array_filter($_SERVER, function($k){ return strpos($k,'HTTP_')===0; }, ARRAY_FILTER_USE_KEY);

// 요청 기록 준비
$record = [
    'ts'       => date('Y-m-d H:i:s'),
    'method'   => $_SERVER['REQUEST_METHOD'],
    'uri'      => $_SERVER['REQUEST_URI'],
    'ip'       => $_SERVER['REMOTE_ADDR'] ?? '',
    'headers'  => $allHeaders,              // ← 전체 헤더
    'get'      => $_GET,
    'post'     => $_POST,
    'body'     => file_get_contents('php://input'),
];

// JSONL 쓰기
$target = "$dataDir/requests.jsonl";
$line   = json_encode($record, JSON_UNESCAPED_UNICODE) . "\n";
file_put_contents($target, $line, FILE_APPEND | LOCK_EX);

echo "OK\n";
?>

<!--
기록 삭제용
sudo truncate -s 0 /var/www/html/files/webhook/data/requests.jsonl
sudo sh -c '> /var/www/html/files/webhook/data/requests.jsonl'
-->