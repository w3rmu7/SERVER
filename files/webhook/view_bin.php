<?php
/**
 * view_bin.php
 *
 * Reads data/requests.jsonl and displays logged requests in an HTML table.
 * 각 요약 행(summary-row) 아래에 디테일 행(details-row)을 미리 삽입해두고,
 * 버튼 클릭 시 해당 디테일 행을 토글합니다.
 */

$file = __DIR__ . '/data/requests.jsonl';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>Logged Requests</title>
  <style>
    body { font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px; }
    table { border-collapse: collapse; width: 100%; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    th, td { border: 1px solid #ddd; padding: 12px; }
    th { background-color: #f2f2f2; text-align: left; }
    tr:nth-child(even) .summary-row { background-color: #fafafa; }
    tr:hover .summary-row { background-color: #f1f1f1; }
    .center { text-align: center; }

    /* 요약(preview) 행 */
    tr.summary-row { cursor: default; }

    /* 디테일 행 */
    tr.details-row td {
      background: #272822;
      padding: 0;    /* pre가 패딩을 가짐 */
    }

    /* JSON 내용 박스 */
    tr.details-row pre {
      margin: 0;               /* td 기본 padding 제거 후 pre에만 패딩 부여 */
      padding: 12px;
      color: #f8f8f2;
      background: #272822;
      white-space: pre-wrap;   /* 자동 줄바꿈 */
      word-break: break-word;  /* 긴 문자열도 줄바꿈 */
      max-height: 400px;       /* 최대 높이 */
      overflow-y: auto;        /* 세로 스크롤 */
      overflow-x: hidden;      /* 가로 스크롤 제거 */
      font-size: 0.9em;
    }

    button.toggle-btn {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 6px 12px;
      cursor: pointer;
      border-radius: 4px;
    }
    button.toggle-btn:hover { background-color: #0056b3; }
  </style>
</head>
<body>
  <h1>Logged Requests</h1>

  <?php if (!is_file($file)): ?>
    <p>아직 기록된 요청이 없습니다.</p>
  <?php else:
    $lines = file($file, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
    $lines = array_reverse($lines);  // 최신 순으로
  ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Time</th>
          <th>Method</th>
          <th>URI</th>
          <th>IP</th>
          <th class="center">Details</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($lines as $i => $line):
        $r = json_decode($line, true) ?: [];
        // JSON_RAW: HTML 이스케이프된 예쁘게 프린트된 문자열
        $jsonRaw = htmlspecialchars(
          json_encode($r, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),
          ENT_QUOTES
        );
      ?>
        <!-- 요약 행 -->
        <tr class="summary-row">
          <td><?= $i + 1 ?></td>
          <td><?= htmlspecialchars($r['ts']  ?? '') ?></td>
          <td><?= htmlspecialchars($r['method']  ?? '') ?></td>
          <td><?= htmlspecialchars($r['uri'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['ip'] ?? '') ?></td>
          <td class="center">
            <button class="toggle-btn">View</button>
          </td>
        </tr>
        <!-- 디테일 행 (기본 숨김) -->
        <tr class="details-row" style="display:none;">
          <td colspan="6">
            <pre><?= $jsonRaw ?></pre>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <script>
    document.querySelectorAll('button.toggle-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        // 버튼의 요약 행 <tr>과, 그 다음 행(디테일 행)을 찾는다
        var summaryRow = this.closest('tr');
        var detailsRow = summaryRow.nextElementSibling;
        if (!detailsRow || !detailsRow.classList.contains('details-row')) return;

        // 토글
        if (detailsRow.style.display === 'table-row') {
          detailsRow.style.display = 'none';
          this.textContent = 'View';
        } else {
          detailsRow.style.display = 'table-row';
          this.textContent = 'Hide';
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
