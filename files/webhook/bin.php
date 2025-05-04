<?php
// bin.php (디버깅용, 한국 시간 기준으로 출력)
// 1) 시간대 설정
date_default_timezone_set('Asia/Seoul');

// 2) 에러 표시 켜기
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 3) 응답 타입
header('Content-Type: text/plain; charset=utf-8');

// 4) 현재 경로 출력
echo "DEBUG: __DIR__ = " . __DIR__ . "\n";

// 5) data 디렉터리 존재 여부
$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    echo "DEBUG: data 디렉터리 없음, 생성 시도…\n";
    if (!mkdir($dataDir, 0755, true)) {
        echo "ERROR: data 디렉터리 생성 실패: $dataDir\n";
        exit;
    }
    echo "DEBUG: data 디렉터리 생성 성공\n";
} else {
    echo "DEBUG: data 디렉터리 이미 존재\n";
}

// 6) 요청 기록 준비
$record = [
    // 한국 시간 기준, YYYY-MM-DD HH:MM:SS 형식
    'ts'      => date('Y-m-d H:i:s'),
    'method'  => $_SERVER['REQUEST_METHOD'],
    'uri'     => $_SERVER['REQUEST_URI'],
    'ip'      => $_SERVER['REMOTE_ADDR'] ?? '',
    'agent'   => $_SERVER['HTTP_USER_AGENT'] ?? '',
    'get'     => $_GET,
    'post'    => $_POST,
    'body'    => file_get_contents('php://input'),
];

// 7) JSONL 쓰기
$target = "$dataDir/requests.jsonl";
$line   = json_encode($record, JSON_UNESCAPED_UNICODE) . "\n";
$result = @file_put_contents($target, $line, FILE_APPEND | LOCK_EX);

if ($result === false) {
    echo "ERROR: file_put_contents 실패 ($target)\n";
    error_log("bin.php: file_put_contents failed for $target");
    exit;
}

echo "DEBUG: file_put_contents 성공, 바이트 수 = $result\n";

// 8) 최종 응답
echo "OK\n";
?>

<!--
기록 삭제용
sudo truncate -s 0 /var/www/html/files/webhook/data/requests.jsonl
sudo sh -c '> /var/www/html/files/webhook/data/requests.jsonl'
-->