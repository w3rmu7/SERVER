<?php
// 보안뉴스 외부 기사 파싱 및 출력 스크립트
// DOMDocument 기반, 캐시, 에러 표시 포함

// 0. 에러 표시 설정
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. 기본 설정
$baseUrl    = 'https://www.boannews.com';
$sourcePath = '/media/t_list.asp?kind=';
$sourceUrl  = $baseUrl . $sourcePath;
$cacheDir   = sys_get_temp_dir() . '/boannews_cache';
$cacheFile  = $cacheDir . '/news.html';
$cacheTTL   = 600; // 캐시 유효 시간(초)

// 2. 캐시 디렉토리 및 권한 확인
if (!is_dir($cacheDir) && !mkdir($cacheDir, 0755, true)) {
    die('캐시 디렉토리 생성 실패: ' . htmlspecialchars($cacheDir, ENT_QUOTES, 'UTF-8'));
}
if (!is_writable($cacheDir)) {
    die('캐시 디렉토리에 쓸 수 없습니다: ' . htmlspecialchars($cacheDir, ENT_QUOTES, 'UTF-8'));
}

// 3. HTML 소스 가져오기 (캐시 활용)
if (file_exists($cacheFile) && time() - filemtime($cacheFile) < $cacheTTL) {
    $html = file_get_contents($cacheFile);
} else {
    $ch = curl_init($sourceUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT      => 'Mozilla/5.0',
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    $html     = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode !== 200 || empty($html)) {
        die('외부 서버 통신 오류: HTTP ' . $httpCode);
    }
    file_put_contents($cacheFile, $html);
}

// 4. 파싱 및 데이터 수집
$articles = [];

if (class_exists('DOMDocument')) {
    libxml_use_internal_errors(true);
    $doc   = new DOMDocument();
    // HTML 인코딩 변환
    $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_NOERROR | LIBXML_NOWARNING);
    libxml_clear_errors();
    $xpath = new DOMXPath($doc);

    // 각 기사 블록 추출
    $blocks = $xpath->query('//div[contains(@class,"news_list")]');
    foreach ($blocks as $block) {
        // 썸네일 URL
        $imgEl = $xpath->query('.//a[1]/img', $block)->item(0);
        $thumb = $imgEl ? $imgEl->getAttribute('src') : null;
        if ($thumb && strpos($thumb, 'http') !== 0) {
            $thumb = $baseUrl . $thumb;
        }

        // 제목과 링크: span.news_txt + view.asp 링크
        $titleEl = $xpath->query('.//span[contains(@class,"news_txt")]', $block)->item(0);
        if ($titleEl) {
            $title = trim($titleEl->textContent);
        } else {
            $title = 'No Title';
        }

        $linkEl = $xpath->query('.//a[contains(@href,"view.asp")]', $block)->item(0);
        if ($linkEl) {
            $href = $linkEl->getAttribute('href');
            if (strpos($href, 'http') !== 0) {
                $href = $baseUrl . $href;
            }
        } else {
            $href = '#';
        }

        // 날짜
        $dateEl = $xpath->query('.//span[contains(@class,"date_txt")]', $block)->item(0);
        $date   = $dateEl ? trim($dateEl->textContent) : '';

        $articles[] = compact('title','href','thumb','date');
    }
} else {
    // Regex fallback
    preg_match_all('/<div[^>]+\bnews_list\b[^>]*>(.*?)<\/div>/is', $html, $blocks);
    foreach ($blocks[1] as $block) {
        // 링크
        preg_match('/<a[^>]+href=["\']([^"\']*view\.asp[^"\']*)["\'][^>]*>(.*?)<\/a>/is', $block, $mLink);
        if (!empty($mLink[1])) {
            $href = $mLink[1];
            if (strpos($href, 'http') !== 0) {
                $href = $baseUrl . $href;
            }
        } else {
            $href = '#';
        }

        // 제목
        if (preg_match('/<span[^>]+class=["\']news_txt["\'][^>]*>(.*?)<\/span>/is', $block, $mTitle)) {
            $title = trim(strip_tags($mTitle[1]));
        } else {
            $title = 'No Title';
        }

        // 썸네일
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/is', $block, $n)) {
            $thumb = $n[1];
            if (strpos($thumb, 'http') !== 0) {
                $thumb = $baseUrl . $thumb;
            }
        } else {
            $thumb = null;
        }

        // 날짜
        if (preg_match('/<span[^>]+class=["\']date_txt["\'][^>]*>(.*?)<\/span>/is', $block, $d)) {
            $date = trim($d[1]);
        } else {
            $date = '';
        }

        $articles[] = compact('title','href','thumb','date');
    }
}

// 5. HTML 출력
?><!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>보안뉴스 외부 기사</title>
    <style>
        .external-news { list-style: none; padding: 0; }
        .external-news li { margin-bottom: 1rem; display: flex; align-items: flex-start; }
        .external-news img { width: 100px; height: auto; margin-right: 1rem; flex-shrink: 0; }
        .external-news .info { flex: 1; }
        .external-news .info a.title-link { text-decoration: none; color: #000; }
        .external-news .info a.title-link:hover { text-decoration: underline; }
        .external-news .info strong { font-size: 1.1rem; display: block; margin-bottom: 0.5rem; }
        .external-news .info time { font-size: 0.9rem; color: #666; }
    </style>
</head>
<body>
    <h1>보안뉴스 외부 기사</h1>
    <ul class="external-news">
    <?php foreach ($articles as $art): ?>
        <li>
            <?php if ($art['thumb']): ?>
                <a href="<?=htmlspecialchars($art['href'], ENT_QUOTES|ENT_HTML5, 'UTF-8')?>" target="_blank">
                    <img src="<?=htmlspecialchars($art['thumb'], ENT_QUOTES|ENT_HTML5, 'UTF-8')?>" alt="썸네일">
                </a>
            <?php endif; ?>
            <div class="info">
                <strong>
                    <a class="title-link" href="<?=htmlspecialchars($art['href'], ENT_QUOTES|ENT_HTML5, 'UTF-8')?>" target="_blank">
                        <?=htmlspecialchars($art['title'], ENT_QUOTES|ENT_HTML5, 'UTF-8')?>
                    </a>
                </strong>
                <time><?=htmlspecialchars($art['date'], ENT_QUOTES|ENT_HTML5, 'UTF-8')?></time>
            </div>
        </li>
    <?php endforeach; ?>
    </ul>
</body>
</html>
