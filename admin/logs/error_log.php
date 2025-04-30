<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8" />
  <title>Error 로그 뷰어</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
      padding: 40px;
      font-family: 'Segoe UI', sans-serif;
    }

    .container {
      background: #fff;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    pre {
      background: #212529;
      color: #f8f9fa;
      padding: 20px;
      border-radius: 12px;
      max-height: 600px;
      overflow-y: auto;
      font-size: 0.9rem;
    }

    .log-title {
      margin-bottom: 20px;
      color: #dc3545;
    }

    .error {
      color: #dc3545;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1 class="log-title">❗ Error 로그 (최근 100줄)</h1>
    <?php
    $logPath = '/var/www/html/admin/logs/error.log';

    if (!file_exists($logPath)) {
        echo '<p class="error">🚫 로그 파일이 존재하지 않습니다.</p>';
        exit;
    }

    if (!is_readable($logPath)) {
        echo '<p class="error">🚫 로그 파일에 읽기 권한이 없습니다.</p>';
        exit;
    }

    $lines = shell_exec("tail -n 100 " . escapeshellarg($logPath));
    echo '<pre>' . htmlspecialchars($lines) . '</pre>';
    ?>
    <a href="/admin" class="btn btn-outline-primary mt-4">← 관리자 페이지로 돌아가기</a>
  </div>
</body>
</html>
