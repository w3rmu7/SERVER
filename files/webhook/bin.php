<?php
// bin.php (디버깅용)
// 1) 에러 표시 켜기
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 2) 응답 타입
header('Content-Type: text/plain; charset=utf-8');

// 3) 현재 경로 출력
echo "DEBUG: __DIR__ = " . __DIR__ . "\n";

// 4) data 디렉터리 존재 여부
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

// 5) 요청 기록 준비
$record = [
    'ts'      => date('c'),
    'method'  => $_SERVER['REQUEST_METHOD'],
    'uri'     => $_SERVER['REQUEST_URI'],
    'ip'      => $_SERVER['REMOTE_ADDR'] ?? '',
    'agent'   => $_SERVER['HTTP_USER_AGENT'] ?? '',
    'get'     => $_GET,
    'post'    => $_POST,
    'body'    => file_get_contents('php://input'),
];

// 6) JSONL 쓰기
$target = "$dataDir/requests.jsonl";
$line   = json_encode($record, JSON_UNESCAPED_UNICODE) . "\n";
$result = @file_put_contents($target, $line, FILE_APPEND | LOCK_EX);

if ($result === false) {
    echo "ERROR: file_put_contents 실패 ($target)\n";
    // PHP 에러 로그에도 남겨 보기
    error_log("bin.php: file_put_contents failed for $target");
    exit;
}

echo "DEBUG: file_put_contents 성공, 바이트 수 = $result\n";

// 7) 최종 응답
echo "OK\n";
