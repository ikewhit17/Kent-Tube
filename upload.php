<?php
include "database.php";

$errors = [];
$ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // 1) Basic validation
  $title = trim($_POST['title'] ?? '');
  $desc  = trim($_POST['description'] ?? '');
  $cat   = trim($_POST['category'] ?? '');
  if ($title === '') { $errors[] = 'Title is required.'; }

  if (!isset($_FILES['video']) || $_FILES['video']['error'] !== UPLOAD_ERR_OK) {
    $errors[] = 'Video file is required.';
  }

  // 2) Validate file
  if (!$errors) {
    $file = $_FILES['video'];

    // 1 GiB limit (adjust as needed)
    if ($file['size'] > 1024*1024*1024) { 
      $errors[] = 'File too large.';
    }

    // Allow only mp4/webm/ogg by mime
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $allowed = ['video/mp4','video/webm','video/ogg'];
    if (!in_array($mime, $allowed, true)) {
      $errors[] = 'Only MP4/WebM/OGG allowed.';
    }

    // Safe filename in uploads/
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $base = bin2hex(random_bytes(8)); // random name
    $relPath = "uploads/{$base}.{$ext}";
    $absPath = __DIR__ . "/" . $relPath;

    // Ensure thumbs dir exists
    $thumbDir = __DIR__ . "/uploads/thumbs";
    if (!is_dir($thumbDir)) { @mkdir($thumbDir, 0775, true); }

    if (!$errors) {
      if (!move_uploaded_file($file['tmp_name'], $absPath)) {
        $errors[] = 'Failed to store file.';
      } else {
        // 3) Try to make a thumbnail with ffmpeg
        $thumbRel = "uploads/thumbs/{$base}.jpg";
        $thumbAbs = __DIR__ . "/" . $thumbRel;

        // Default thumb path if generation fails
        $fallbackThumbRel = 'assets/default-thumb.jpg';

        // Compute a seek time using ffprobe (about 1/3 into video), fallback 2s
        $seek = '00:00:02';
        $duration = null;

        // Only attempt ffmpeg/ffprobe if shell_exec is available
        if (function_exists('shell_exec')) {
          $cmdProbe = "ffprobe -v error -select_streams v:0 -show_entries format=duration -of csv=p=0 "
                    . escapeshellarg($absPath) . " 2>&1";
          $probeOut = @shell_exec($cmdProbe);
          $duration = is_numeric(trim($probeOut)) ? (float)trim($probeOut) : null;

          if (is_numeric($duration) && $duration > 4) {
            $seekSeconds = max(1, (int)($duration / 3));
            $seek = gmdate("H:i:s", $seekSeconds);
          }

          $cmdThumb = "ffmpeg -ss {$seek} -i " . escapeshellarg($absPath)
                    . " -frames:v 1 -vf 'scale=640:-1' -y "
                    . escapeshellarg($thumbAbs) . " 2>&1";
          @shell_exec($cmdThumb);
        }

        // If ffmpeg failed, use fallback
        if (!file_exists($thumbAbs) || filesize($thumbAbs) === 0) {
          $thumbRel = $fallbackThumbRel;
        }

        // 4) Insert DB row
        $stmt = mysqli_prepare($conn, "INSERT INTO videos
          (title, description, file_path, thumbnail_path, uploader, category)
          VALUES (?,?,?,?,?,?)");

        $uploader = 'ike'; // or from your session
        mysqli_stmt_bind_param($stmt, "ssssss",
          $title, $desc, $relPath, $thumbRel, $uploader, $cat);

        if (!mysqli_stmt_execute($stmt)) {
          // rollback file(s) if DB insert fails
          @unlink($absPath);
          if ($thumbRel !== $fallbackThumbRel) { @unlink($thumbAbs); }
          $errors[] = 'DB insert failed.';
        } else {
          $ok = true;
        }
      }
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Upload video — Kent-Tube</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    form {max-width:720px;margin:24px auto;padding:16px;border:1px solid #ccc;border-radius:12px;background:#fff}
    .msg{margin:12px auto;max-width:720px}
    .err{color:#b00}
    .ok{color:#070}
    label{display:block;margin:10px 0 4px}
    input[type=text],textarea,select{width:100%;padding:8px}
    button{margin-top:12px;padding:10px 16px}
  </style>
</head>
<body>
  <?php if ($ok): ?>
    <div class="msg ok">Upload complete.</div>
  <?php elseif ($errors): ?>
    <div class="msg err">
      <?php foreach ($errors as $e) echo htmlspecialchars($e)."<br>"; ?>
    </div>
  <?php endif; ?>

  <form action="upload.php" method="post" enctype="multipart/form-data">
    <h2>Upload a video</h2>
    <label>Title *</label>
    <input type="text" name="title" required>

    <label>Description</label>
    <textarea name="description" rows="4"></textarea>

    <label>Category</label>
    <input type="text" name="category" placeholder="e.g., CIS 24162">

    <label>Video file (MP4/WebM/OGG) *</label>
    <input type="file" name="video" accept="video/mp4,video/webm,video/ogg" required>

    <button type="submit">Upload</button>
  </form>
</body>
</html>
