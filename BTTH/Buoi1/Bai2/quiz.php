<?php
// quiz.php
// Yêu cầu: cùng thư mục có Quiz.txt với format như trong ví dụ của bạn.

// cấu hình
$quizFile = __DIR__ . '/Quiz.txt';
if (!file_exists($quizFile)) {
    echo "<p>Không tìm thấy Quiz.txt ở: $quizFile</p>";
    exit;
}

// đọc file nguyên văn
$raw = file_get_contents($quizFile);

// chuẩn hóa: chuyển các loại newline về \n
$raw = str_replace(["\r\n","\r"], "\n", $raw);

// tách các block câu hỏi (giữa các block thường có một hoặc nhiều dòng trống)
$blocks = preg_split("/\n{2,}/", trim($raw));

// parse từng block
$questions = [];
foreach ($blocks as $b) {
    $lines = array_values(array_filter(array_map('trim', explode("\n", $b)), fn($l) => $l !== ''));
    if (count($lines) === 0) continue;

    // tìm dòng nào chứa "ANSWER:" (có thể nằm cuối)
    $answerLineIndex = null;
    foreach ($lines as $i => $ln) {
        if (stripos($ln, 'ANSWER:') === 0) {
            $answerLineIndex = $i;
            break;
        }
    }

    if ($answerLineIndex === null) {
        // nếu không có ANSWER: bỏ qua
        continue;
    }

    // question phần trước dòng option (thường dòng đầu)
    $questionText = $lines[0];

    // options là các dòng giữa (từ 1 đến answerLineIndex-1), chuẩn hóa dạng "A. text"
    $options = [];
    for ($i = 1; $i < $answerLineIndex; $i++) {
        $ln = $lines[$i];
        // match "X. option text" hoặc "X) option text"
        if (preg_match('/^([A-Z])\s*[.)]\s*(.+)$/u', $ln, $m)) {
            $key = $m[1];
            $text = $m[2];
            $options[$key] = $text;
        } else {
            // nếu không khớp, thêm như dòng phụ (gộp vào option trước)
            if (!empty($options)) {
                end($options);
                $k = key($options);
                $options[$k] .= ' ' . $ln;
            }
        }
    }

    // ANSWER line: có thể "ANSWER: A" hoặc "ANSWER: A, B"
    $ansLine = $lines[$answerLineIndex];
    $ansPart = trim(substr($ansLine, strlen('ANSWER:')));
    // tách theo dấu phẩy hoặc khoảng trắng
    $ansPart = str_replace(['，', ';'], ',', $ansPart);
    $anss = array_filter(array_map(fn($s) => strtoupper(trim($s)), preg_split('/[,\/]+/', $ansPart)));

    // nếu ans chứa chữ và số (vd "C, D") giữ nguyên
    $questions[] = [
        'q' => $questionText,
        'options' => $options,
        'answer' => $anss, // mảng có 1 hoặc nhiều đáp án đúng
    ];
}

// xử lý khi submit
$results = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userAnswers = $_POST['answer'] ?? [];
    $score = 0;
    $total = count($questions);
    $details = [];

    foreach ($questions as $i => $q) {
        $correct = $q['answer']; // array of correct keys, uppercase
        // user may submit single value or array for that question (for multiple correct)
        $ua = $userAnswers[$i] ?? null;
        if (is_array($ua)) {
            $userKeys = array_map('strtoupper', array_map('trim', $ua));
        } elseif ($ua !== null) {
            $userKeys = [strtoupper(trim($ua))];
        } else {
            $userKeys = [];
        }

        // normalize: remove empty
        $userKeys = array_filter($userKeys, fn($x) => $x !== '');

        // decide if correct:
        // require that set of userKeys equals set of correct (order-insensitive)
        $ukset = array_values(array_unique($userKeys));
        $ckset = array_values(array_unique($correct));
        sort($ukset);
        sort($ckset);
        $isCorrect = ($ukset === $ckset);

        if ($isCorrect) $score += 1;

        $details[] = [
            'question' => $q['q'],
            'options' => $q['options'],
            'correct' => $correct,
            'user' => $userKeys,
            'ok' => $isCorrect,
        ];
    }

    $results = [
        'score' => $score,
        'total' => $total,
        'details' => $details,
    ];
}

// helper to escape
function h($s){ return htmlspecialchars($s, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }

?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Bài thi trắc nghiệm</title>
  <style>
    body { font-family: system-ui, Arial, sans-serif; padding:20px; max-width:900px; margin:auto; }
    .q { margin:18px 0; padding:12px; border:1px solid #ddd; border-radius:6px; background:#fafafa; }
    .q h3 { margin:0 0 8px 0; font-size:16px; }
    .options { margin-left:18px; }
    .correct { color: green; font-weight:600; }
    .wrong { color: red; font-weight:600; }
    .result { padding:12px; border-radius:6px; margin-bottom:18px; }
    .ok { background:#e6ffed; border:1px solid #b7f0c9; }
    .bad { background:#ffecec; border:1px solid #f0b7b7; }
  </style>
</head>
<body>
  <h1>Bài thi trắc nghiệm</h1>

<?php if ($results !== null): ?>
  <div class="result <?php echo ($results['score'] == $results['total']) ? 'ok' : (($results['score'] / max(1,$results['total']) >= 0.5) ? '' : 'bad'); ?>">
    <strong>Kết quả:</strong>
    Bạn đạt <?php echo $results['score']; ?> / <?php echo $results['total']; ?> điểm.
  </div>

  <?php foreach ($results['details'] as $i => $d): ?>
    <div class="q">
      <h3>Câu <?php echo $i+1; ?>. <?php echo h($d['question']); ?></h3>
      <div class="options">
        <?php foreach ($d['options'] as $k => $txt): ?>
          <div>
            <strong><?php echo h($k); ?>.</strong> <?php echo h($txt); ?>
          </div>
        <?php endforeach; ?>
      </div>
      <p>
        Đáp án đúng:
        <?php foreach ($d['correct'] as $c): ?>
          <span class="correct"><?php echo h($c); ?></span>
        <?php endforeach; ?>
      </p>
      <p>
        Đáp án của bạn:
        <?php if (count($d['user'])===0): ?>
          <span class="wrong">Không trả lời</span>
        <?php else: ?>
          <?php foreach ($d['user'] as $u): ?>
            <span <?php echo in_array($u, $d['correct']) ? 'class="correct"' : 'class="wrong"'; ?>><?php echo h($u); ?></span>
          <?php endforeach; ?>
        <?php endif; ?>
      </p>
    </div>
  <?php endforeach; ?>

  <p><a href="quiz.php">Làm lại</a></p>

<?php else: ?>
  <form method="post" action="quiz.php">
    <?php foreach ($questions as $i => $q): 
      // kiểm tra có nhiều đáp án đúng hay không
      $multi = count($q['answer']) > 1;
    ?>
      <div class="q">
        <h3>Câu <?php echo $i+1; ?>. <?php echo h($q['q']); ?></h3>
        <div class="options">
          <?php foreach ($q['options'] as $k => $txt): ?>
            <label style="display:block; margin:6px 0;">
              <?php if ($multi): ?>
                <input type="checkbox" name="answer[<?php echo $i; ?>][]" value="<?php echo h($k); ?>">
              <?php else: ?>
                <input type="radio" name="answer[<?php echo $i; ?>]" value="<?php echo h($k); ?>">
              <?php endif; ?>
              <strong><?php echo h($k); ?>.</strong> <?php echo h($txt); ?>
            </label>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>

    <button type="submit">Nộp bài</button>
  </form>
<?php endif; ?>

  <hr>
  <p style="font-size:0.9em;color:#666;">Ghi chú: parser đơn giản dựa trên format: 1 dòng câu hỏi; các dòng option bắt đầu bằng "A. ", "B. "...; dòng cuối của block chứa "ANSWER: <danh sách đáp án>" (ví dụ "ANSWER: C" hoặc "ANSWER: C, D").</p>
</body>
</html>
