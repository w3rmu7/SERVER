<?php
session_start();

//— 접근 제어 설정 파일 로드 —//
define('CONFIG_FILE', __DIR__ . '/../admin/access_control.json');
if (!file_exists(CONFIG_FILE)) {
    die('ERROR: 설정 파일을 찾을 수 없습니다.');
}
$config = json_decode(file_get_contents(CONFIG_FILE), true);

//— Files 접근 제어 —//
if (!empty($config['require_login_files']) && $config['require_login_files'] === true) {
    if (!isset($_SESSION['user'])) {
        header('Location: /');
        exit();
    }
}

//— 디렉토리 파일 목록 가져오기 —//
$directory = __DIR__;
$files = scandir($directory);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>파일 목록</title>
  <style>
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
      margin: 0;
      font-size: 2rem;
      color: #495057;
    }
    .file-list {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      width: 100%;
      max-width: 800px;
      margin-top: 30px;
    }
    .file-card {
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      text-align: center;
      transition: background 0.3s, transform 0.2s;
    }
    .file-card:hover {
      background: #f1f3f5;
      transform: translateY(-4px);
    }
    .file-card a {
      text-decoration: none;
      color: #007bff;
      font-weight: bold;
      word-break: break-all;
    }
    .file-card a:hover {
      text-decoration: underline;
    }
    .back-link {
      margin-top: 40px;
      text-decoration: none;
      color: #495057;
      font-size: 0.95rem;
    }
    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <h1>📁 파일 목록</h1>
  <div class="file-list">
    <?php
      foreach ($files as $file) {
          if ($file === '.' || $file === '..' || $file === basename(__FILE__)) {
              continue;
          }
          $safe = htmlspecialchars($file, ENT_QUOTES);
          echo "<div class=\"file-card\"><a href=\"{$safe}\">{$safe}</a></div>";
      }
    ?>
  </div>
  <a href="/" class="back-link">← 메인 페이지로 돌아가기</a>
</body>
</html>
