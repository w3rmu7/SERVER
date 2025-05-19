<?php
// 파일 경로 설정
$file = __DIR__ . '/data/requests.jsonl';

// POST 요청으로 삭제 처리
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    !empty($_POST['logs']) &&
    is_file($file)
) {
    // 원본 라인 읽기
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    // 최근 순서로 배열 뒤집기
    $lines = array_reverse($lines);

    // 삭제할 인덱스 목록
    $deleteIndices = array_map('intval', $_POST['logs']);
    $filtered = [];
    foreach ($lines as $idx => $line) {
        if (!in_array($idx, $deleteIndices, true)) {
            $filtered[] = $line;
        }
    }
    // 파일에 다시 저장 (원래 순서로 복원)
    $filtered = array_reverse($filtered);
    file_put_contents($file, implode("\n", $filtered) . "\n");

    // 새로고침
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// 로그 파일 읽기 (GET 요청 시)
$lines = [];
if (is_file($file)) {
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lines = array_reverse($lines);
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>Logged Requests</title>
  <style>
    /* 기존 색조 유지 */
    body { font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px; }
    .table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    table { border-collapse: collapse; width: 100%; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); table-layout: fixed; }
    th, td { border: 1px solid #ddd; padding: 12px; overflow: hidden; text-overflow: ellipsis; }
    th { background-color: #f2f2f2; text-align: left; }
    tr:nth-child(even) .summary-row { background-color: #fafafa; }
    tr:hover .summary-row { background-color: #f1f1f1; }
    .details-row td { background: #272822; padding: 0; }
    .details-row pre { margin: 0; padding: 12px; color: #f8f8f2; background: #272822; white-space: pre-wrap; word-break: break-word; max-height: 400px; overflow-y: auto; overflow-x: hidden; font-size: 0.9em; }
    td.center { text-align: center; white-space: nowrap; }
    #check-all { display: block; margin: 0 auto;}


    /* 버튼 색상: 원래 블루톤 유지 */
    button.toggle-btn {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 6px 12px;
      cursor: pointer;
      border-radius: 4px;
      min-width: 60px;
      margin-bottom: 10px;
    }
    button.toggle-btn:hover { background-color: #0056b3; }

    th:nth-child(1), td:nth-child(1) { width: 40px; }
  </style>
</head>
<body>
  <h1>Logged Requests</h1>

  <?php if (empty($lines)): ?>
    <p>아직 기록된 요청이 없습니다.</p>
  <?php else: ?>
    <form method="POST" onsubmit="return confirm('정말 선택한 로그를 삭제하시겠습니까?');">
      <button type="submit" class="toggle-btn">Delete</button>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th><input type="checkbox" id="check-all"></th>
              <th>#</th>
              <th>Time</th>
              <th>Method</th>
              <th>URI</th>
              <th>IP</th>
              <th>Details</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($lines as $i => $line): ?>
            <?php $r = json_decode($line, true) ?: []; ?>
            <?php $jsonRaw = htmlspecialchars(json_encode($r, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE), ENT_QUOTES); ?>
            <tr class="summary-row">
              <td class="center"><input type="checkbox" class="log-check" name="logs[]" value="<?= $i ?>"></td>
              <td><?= $i + 1 ?></td>
              <td><?= htmlspecialchars($r['ts'] ?? '') ?></td>
              <td><?= htmlspecialchars($r['method'] ?? '') ?></td>
              <td><?= htmlspecialchars($r['uri'] ?? '') ?></td>
              <td><?= htmlspecialchars($r['ip'] ?? '') ?></td>
              <td class="center"><button type="button" class="toggle-btn">View</button></td>
            </tr>
            <tr class="details-row" style="display:none;">
              <td colspan="7"><pre><?= $jsonRaw ?></pre></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </form>
  <?php endif; ?>

  <script>
    // 전체 선택/해제
    document.getElementById('check-all').addEventListener('change', function() {
      document.querySelectorAll('.log-check').forEach(cb => cb.checked = this.checked);
    });
    // 상세 토글
    document.querySelectorAll('button.toggle-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const tr = this.closest('tr');
        const details = tr.nextElementSibling;
        if (!details.classList.contains('details-row')) return;
        if (details.style.display === 'table-row') {
          details.style.display = 'none'; this.textContent = 'View';
        } else {
          details.style.display = 'table-row'; this.textContent = 'Hide';
        }
      });
    });
  </script>
</body>
</html>


<!--
기록 삭제용
sudo truncate -s 0 /var/www/html/files/webhook/data/requests.jsonl
sudo sh -c '> /var/www/html/files/webhook/data/requests.jsonl'
-->