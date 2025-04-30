<?php
session_start();

// 로그인 처리
if (isset($_POST['username']) && isset($_POST['password'])) {
    if (($_POST['username'] === 'admin' && $_POST['password'] === '0214')||($_POST['username'] === '9omau2' && $_POST['password'] === '0414')) {
        $_SESSION['user'] = 'admin';
        header('Location: /');
        exit();
    } else {
        $error = "아이디 또는 비밀번호가 잘못되었습니다.";
    }
}

// 로그아웃 처리
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Welcome to Wermut's Apache Server</title>
  <style>
    
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #f8f9fa, #e0eafc);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      color: #333;
    }
    .container {
      text-align: center;
      background-color: white;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      max-width: 600px;
    }
    h1 {
      font-size: 2.5rem;
      margin-bottom: 10px;
    }
    p {
      font-size: 1.2rem;
      color: #555;
    }
    .links, .auth {
      margin-top: 30px;
    }
    .links a, .auth a {
      text-decoration: none;
      color: #007bff;
      font-weight: bold;
      margin: 0 10px;
      transition: color 0.2s;
    }
    .links a:hover, .auth a:hover {
      color: #0056b3;
    }
    footer {
      margin-top: 40px;
      font-size: 0.9rem;
      color: #888;
    }
    form {
      margin-top: 20px;
    }
    input[type="text"], input[type="password"] {
      padding: 10px;
      width: 80%;
      margin-bottom: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
    button {
      padding: 10px 20px;
      border: none;
      background-color: #007bff;
      color: white;
      border-radius: 8px;
      cursor: pointer;
    }
    button:hover {
      background-color: #0056b3;
    }
    .error {
      color: red;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Apache Server</h1>
    <p>본 서버는 테스트용으로 구성된 아파치 웹 서버입니다.</p>

    <div class="links">
      <a href="/files/">📁 파일 목록</a>
      <a href="/status/status.php">🔍 서버 상태</a>
      <a href="/admin/admin.php">⚙️ 관리자 페이지</a>
    </div>

    <div class="auth">
      <?php if (isset($_SESSION['user'])): ?>
        <p>환영합니다, <?php echo htmlspecialchars($_SESSION['user']); ?>님!</p>
        <a href="/?logout=1">로그아웃</a>
      <?php else: ?>
        <form method="POST">
          <input type="text" name="username" placeholder="아이디"><br>
          <input type="password" name="password" placeholder="비밀번호"><br>
          <button type="submit">로그인</button>
        </form>
        <?php if (isset($error)) echo '<div class="error">' . $error . '</div>'; ?>
      <?php endif; ?>
    </div>

    <footer>
      ⓒ 2025. YourServer. All rights reserved.
    </footer>
  </div>
</body>
</html>
