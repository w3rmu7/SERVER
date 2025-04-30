<?php
session_start();

//— 설정 파일 경로 —//
define('CONFIG_FILE', __DIR__ . '/access_control.json');
$config_dir = dirname(CONFIG_FILE);

//— 디렉토리 쓰기 권한 확인 —//
if (!is_writable($config_dir)) {
    die("ERROR: 설정 파일을 생성할 디렉토리에 쓰기 권한이 없습니다.\n($config_dir)");
}

//— 파일이 없으면 기본값으로 생성 —//
if (!file_exists(CONFIG_FILE)) {
    $default = [
        'require_login_admin'  => false,
        'require_login_status' => false,
        'require_login_files'  => false,
    ];
    if (file_put_contents(CONFIG_FILE, json_encode($default, JSON_PRETTY_PRINT)) === false) {
        die('ERROR: 설정 파일 생성에 실패했습니다.');
    }
}

//— 현재 설정 불러오기 —//
$config = json_decode(file_get_contents(CONFIG_FILE), true);

//— 로그아웃 처리 —//
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /');
    exit;
}

//— 설정 저장 함수 —//
function saveSetting($key, $value) {
    global $config;
    $config[$key] = $value;
    if (file_put_contents(CONFIG_FILE, json_encode($config, JSON_PRETTY_PRINT)) === false) {
        die('ERROR: 설정 저장에 실패했습니다.');
    }
    header('Location: admin.php');
    exit;
}

//— 토글 처리 —//
if (isset($_GET['set_admin'])) {
    saveSetting('require_login_admin', $_GET['set_admin'] === '1');
}
if (isset($_GET['set_status'])) {
    saveSetting('require_login_status', $_GET['set_status'] === '1');
}
if (isset($_GET['set_files'])) {
    saveSetting('require_login_files', $_GET['set_files'] === '1');
}

//— 접근 제어 —//
if ($config['require_login_admin'] && !isset($_SESSION['user'])) {
    header('Location: /');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>관리자 페이지</title>
  <style>
    body {
      margin: 0;
      padding: 60px 20px;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(180deg, #f8f9fa, #e0eafc);
      color: #333;
      box-sizing: border-box;
    }
    .container {
      max-width: 900px;
      margin: auto;
      background: white;
      padding: 40px 50px;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }
    h1 {
      color: #dc3545;
      font-size: 2rem;
      margin-bottom: 20px;
    }
    .section {
      margin-top: 40px;
    }
    .section h2 {
      font-size: 1.5rem;
      margin-bottom: 15px;
      color: #495057;
      border-bottom: 2px solid #dee2e6;
      padding-bottom: 8px;
    }
    .row {
      display: grid;
      grid-template-columns: 200px 50px auto;
      align-items: center;
      gap: 15px;
      margin-bottom: 20px;
    }
    .label {
      font-weight: bold;
    }
    .switch {
      position: relative;
      display: inline-block;
      width: 50px;
      height: 28px;
    }
    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }
    .slider {
      position: absolute;
      cursor: pointer;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: #ccc;
      transition: .4s;
      border-radius: 34px;
    }
    .slider:before {
      position: absolute;
      content: "";
      height: 20px; width: 20px;
      left: 4px; bottom: 4px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }
    input:checked + .slider {
      background-color: #28a745;
    }
    input:checked + .slider:before {
      transform: translateX(22px);
    }
    .status-text {
      font-size: 1rem;
    }
    .links a {
      display: block;
      padding: 10px 0;
      color: #007bff;
      text-decoration: none;
      font-size: 1.05rem;
      transition: color 0.2s;
    }
    .links a:hover {
      text-decoration: underline;
      color: #0056b3;
    }
    .info-box {
      background: #f1f3f5;
      padding: 20px 30px;
      border-left: 5px solid #6c757d;
      border-radius: 12px;
      margin-top: 15px;
      line-height: 1.6;
      font-size: 1rem;
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
    .logout-button {
      display: inline-block;
      padding: 8px 16px;
      background: #dc3545;
      color: white;
      border-radius: 8px;
      font-weight: bold;
      text-decoration: none;
      font-size: 0.95rem;
      transition: background 0.2s;
    }
    .logout-button:hover {
      background: #c82333;
    }
  </style>
</head>
<body>
  <div class="container">
    <div style="text-align: right; margin-bottom: 20px;">
      <a href="?logout=1" class="logout-button">로그아웃</a>
    </div>

    <h1>🛠️ 관리자 페이지</h1>

    <div class="section">
      <h2>🔐 접근 제어 설정</h2>

      <!-- Admin 페이지 -->
      <form action="admin.php" method="GET" class="row">
        <div class="label">Admin 페이지:</div>
        <input type="hidden" name="set_admin" value="0">
        <label class="switch">
          <input
            type="checkbox"
            name="set_admin"
            value="1"
            onchange="this.form.submit()"
            <?php echo $config['require_login_admin'] ? 'checked' : ''; ?>
          >
          <span class="slider"></span>
        </label>
        <div class="status-text">
          <?php echo $config['require_login_admin'] ? '로그인 필요' : '로그인 불필요'; ?>
        </div>
      </form>

      <!-- Status 페이지 -->
      <form action="admin.php" method="GET" class="row">
        <div class="label">Status 페이지:</div>
        <input type="hidden" name="set_status" value="0">
        <label class="switch">
          <input
            type="checkbox"
            name="set_status"
            value="1"
            onchange="this.form.submit()"
            <?php echo $config['require_login_status'] ? 'checked' : ''; ?>
          >
          <span class="slider"></span>
        </label>
        <div class="status-text">
          <?php echo $config['require_login_status'] ? '로그인 필요' : '로그인 불필요'; ?>
        </div>
      </form>

      <!-- Files 디렉토리 -->
      <form action="admin.php" method="GET" class="row">
        <div class="label">Files 디렉토리:</div>
        <input type="hidden" name="set_files" value="0">
        <label class="switch">
          <input
            type="checkbox"
            name="set_files"
            value="1"
            onchange="this.form.submit()"
            <?php echo $config['require_login_files'] ? 'checked' : ''; ?>
          >
          <span class="slider"></span>
        </label>
        <div class="status-text">
          <?php echo $config['require_login_files'] ? '로그인 필요' : '로그인 불필요'; ?>
        </div>
      </form>
    </div>

    <div class="section">
      <h2>📄 관리 도구</h2>
      <div class="links">
        <a href="/status/status.php">🔍 서버 상태 보기</a>
        <a href="/files/">📁 파일 목록 보기</a>
        <a href="./logs/access_log.php">📄 Access 로그</a>
        <a href="./logs/error_log.php">❗ Error 로그</a>
      </div>
    </div>

    <div class="section">
      <h2>📌 서버 정보</h2>
      <div class="info-box">
        <p><strong>서버 이름:</strong> My Apache Server</p>
        <p><strong>운영체제:</strong> Ubuntu 22.04</p>
        <p><strong>웹 서버:</strong> Apache/2.4.x</p>
        <p><strong>문서 루트:</strong> /var/www/html</p>
      </div>
    </div>
    <br>
    <a class="back-link" href="/">← 메인 페이지로 돌아가기</a>
  </div>
</body>
</html>
