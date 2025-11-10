<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<?php
include "database.php";

// validate id
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { http_response_code(404); exit('Video not found'); }

// fetch video
$stmt = mysqli_prepare($conn, "SELECT id, title, description, file_path, thumbnail_path, views, upload_date, category, tags, duration FROM videos WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$video = mysqli_fetch_assoc($result);
if (!$video) { http_response_code(404); exit('Video not found'); }
 // set views
mysqli_query($conn, "UPDATE videos SET views = views + 1 WHERE id = $id");

// fetch some related videos (same category, exclude current video)
$rel = null;
if (!empty($video['category'])) {
  $rstmt = mysqli_prepare($conn, "SELECT id, title, thumbnail_path FROM videos WHERE category = ? AND id <> ? ORDER BY upload_date DESC LIMIT 8");
  mysqli_stmt_bind_param($rstmt, "si", $video['category'], $id);
  mysqli_stmt_execute($rstmt);
  $rel = mysqli_stmt_get_result($rstmt);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($video['title']) ?> — Kent-Tube</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="app layout-player">
  <aside class="sidebar">
    <div class="logo-row"><div class="logo-mark">🐺</div><div class="logo-text">KENT-TUBE</div></div>
    <nav class="side-nav" aria-label="Main">
      <a class="nav-btn" href="home.php">Home</a>
      <a class="nav-btn" href="history.php">History</a>
      <a class="nav-btn" href="saved.php">Saved</a>
      <a class="nav-btn" href="playlists.php">Playlists</a>
    </nav>
  </aside>

  <main class="main">
    <header class="topbar">
      <button class="settings" title="Settings">⚙️</button>
      <div class="search-wrap"><input id="searchPlayer" placeholder="Search Bar"></div>
      <div class="profile"><div class="avatar"></div><div class="profile-name">Isaac</div></div>
    </header>

    <section class="player-area">
      <div class="player-main">
        <div class="video-player">
          <video controls preload="metadata" poster="<?= htmlspecialchars($video['thumbnail_path'] ?: 'assets/default-thumb.jpg') ?>" style="width:100%;max-height:65vh;background:#000;">
            <source src="<?= htmlspecialchars($video['file_path']) ?>" type="video/mp4">
            Your browser does not support HTML5 video.
          </video>
        </div><br><br><br><br>
        <div>
        <h1 class="video-title"><?= htmlspecialchars($video['title']) ?></h1>
        <div class="meta">
          <span><?= (int)$video['views'] + 1 ?> views</span>
          <span>•</span>
          <span><?= htmlspecialchars(date('Y-m-d H:i', strtotime($video['upload_date']))) ?></span>
          <?php if (!empty($video['category'])): ?>
            <span>•</span><span><?= htmlspecialchars($video['category']) ?></span>
          <?php endif; ?>
        </div>
          </div>
        <div class="desc"><?= nl2br(htmlspecialchars($video['description'])) ?></div>

        <div class="comment-box">
          <input placeholder="Write comment..."><button title="Send">✈️</button>
        </div>
        <div class="comments">
          <!-- comments will be made later -->
        </div>
      </div>

      <aside class="related">
        <?php if ($rel && mysqli_num_rows($rel)): while ($r = mysqli_fetch_assoc($rel)): ?>
          <a class="related-card" href="player.php?id=<?= (int)$r['id'] ?>">
            <img src="<?= htmlspecialchars($r['thumbnail_path'] ?: 'assets/default-thumb.jpg') ?>" alt="<?= htmlspecialchars($r['title']) ?>">
            <div class="title"><?= htmlspecialchars($r['title']) ?></div>
          </a>
        <?php endwhile; else: ?>
          <div class="related-card placeholder">No related videos</div>
        <?php endif; ?>
      </aside>
    </section>
  </main>
</div>
<script src="app.js"></script>
</body>
</html>