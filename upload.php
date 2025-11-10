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
    if ($file['size'] > 1024*1024*1024) { // 1GB
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
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $base = bin2hex(random_bytes(8)); // random name
    $relPath = "uploads/{$base}." . strtolower($ext);
    $absPath = __DIR__ . "/" . $relPath;

    if (!$errors) {
      if (!move_uploaded_file($file['tmp_name'], $absPath)) {
        $errors[] = 'Failed to store file.';
      } else {
        // 3) Insert DB row
        $thumb = null; // add later if you generate thumbnails
        $stmt = mysqli_prepare($conn, "INSERT INTO videos
          (title, description, file_path, thumbnail_path, uploader, category)
          VALUES (?,?,?,?,?,?)");
        $uploader = 'ike'; // or pull from session/login
        mysqli_stmt_bind_param($stmt, "ssssss",
          $title, $desc, $relPath, $thumb, $uploader, $cat);
        if (!mysqli_stmt_execute($stmt)) {
          // rollback file if DB insert fails
          @unlink($absPath);
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
    form {max-width:720px;margin:24px auto;padding:16px;border:1px solid #ccc;border-radius:12px}
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
