<?php
session_start();

//— 접근 제어 설정 파일 로드 —//
define('CONFIG_FILE', __DIR__ . '/../admin/access_control.json');
if (!file_exists(CONFIG_FILE)) {
    die('ERROR: 설정 파일을 찾을 수 없습니다.');
}
$config = json_decode(file_get_contents(CONFIG_FILE), true);

//— Status 접근 제어 —//
if (!empty($config['require_login_status']) && $config['require_login_status'] === true) {
    if (!isset($_SESSION['user'])) {
        header('Location: /');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>서버 상태</title>
  <style>
    /* 기본 레이아웃 */
    body {
      margin: 0;
      padding: 60px 20px;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(180deg, #f8f9fa, #e0eafc);
      color: #333;
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: 100vh;
    }
    h1 {
      font-size: 2rem;
      color: #495057;
      margin-bottom: 30px;
    }
    /* 상태 박스 */
    .status-box {
      background: #fff;
      padding: 30px 40px;
      border-radius: 16px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.08);
      width: 100%;
      max-width: 600px;
      box-sizing: border-box;
      margin: 0 auto;
    }
    /* 그리드로 라벨/값 정렬 */
    .status-row {
      display: grid;
      grid-template-columns: 150px 1fr;
      align-items: center;
      gap: 20px;
      margin-bottom: 14px;
    }
    .label {
      font-weight: bold;
      color: #444;
    }
    .value {
      color: #555;
      word-break: break-all;
    }
    /* 뒤로 가기 */
    .back-link {
      margin-top: 40px;
      font-size: 0.95rem;
      color: #495057;
      text-decoration: none;
    }
    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <h1>🔍 서버 상태</h1>

  <div class="status-box">
    <div class="status-row">
      <span class="label">서버 이름:</span>
      <span class="value">My Apache Server</span>
    </div>
    <div class="status-row">
      <span class="label">운영체제:</span>
      <span class="value">Ubuntu 22.04</span>
    </div>
    <div class="status-row">
      <span class="label">웹 서버:</span>
      <span class="value">Apache/2.4.x</span>
    </div>
    <div class="status-row">
      <span class="label">PHP 버전:</span>
      <span class="value"><?php echo phpversion(); ?></span>
    </div>
    <div class="status-row">
      <span class="label">문서 루트:</span>
      <span class="value">/var/www/html</span>
    </div>
  </div>

  <a href="/" class="back-link">← 메인 페이지로 돌아가기</a>
</body>
</html>
