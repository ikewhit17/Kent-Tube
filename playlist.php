<?php
include "database.php";
session_start();

// -------------------------------
// Validate playlist ID
// -------------------------------
$playlist_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($playlist_id <= 0) {
    http_response_code(404);
    exit("Playlist not found.");
}

// Fetch playlist info
$stmt = mysqli_prepare($conn, "SELECT name, description FROM playlists WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $playlist_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$playlist = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$playlist) {
    http_response_code(404);
    exit("Playlist does not exist.");
}

// Fetch videos in this playlist
$q = "
SELECT v.id, v.title, v.thumbnail_path 
FROM playlist_videos pv
JOIN videos v ON pv.video_id = v.id
WHERE pv.playlist_id = ?
ORDER BY pv.id DESC
";

$stmt2 = mysqli_prepare($conn, $q);
mysqli_stmt_bind_param($stmt2, "i", $playlist_id);
mysqli_stmt_execute($stmt2);
$videos = mysqli_stmt_get_result($stmt2);
mysqli_stmt_close($stmt2);

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($playlist['name']) ?> — Playlist</title>
  <link rel="stylesheet" href="styles.css">

  <style>
  .playlist-header {
    margin-bottom: 20px;
  }

  .playlist-desc {
    color: #666;
    margin-bottom: 20px;
  }

  .video-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 16px;
  }

  .video-card {
    background: white;
    border: 1px solid #ccc;
    padding: 8px;
    border-radius: 8px;
    text-decoration: none;
    color: black;
  }

  .video-card img {
    width: 100%;
    height: 100px;
    object-fit: cover;
    border-radius: 6px;
  }

  .video-card-title {
    margin-top: 8px;
    font-size: 14px;
    font-weight: 600;
  }
  </style>
</head>
<body>
  <div class="app layout-playlist">

    <aside class="sidebar">
      <div class="logo-row">
        <div class="logo-mark">🐺</div>
        <div class="logo-text">KENT-TUBE</div>
      </div>

      <nav class="side-nav">
        <a class="nav-btn" href="home.php">Home</a>
        <a class="nav-btn" href="history.php">History</a>
        <a class="nav-btn" href="saved.php">Saved</a>
        <a class="nav-btn active" href="playlists.php">Playlists</a>
      </nav>
    </aside>

    <main class="main">
      <header class="topbar">
        <button class="settings">⚙️</button>
        <div class="search-wrap"><input id="search" placeholder="Search Bar"></div>
         <script>
document.getElementById("search").addEventListener("keypress", function(e) {
  if (e.key === "Enter") {
    const q = encodeURIComponent(this.value.trim());
    if (q.length > 0) {
      window.location.href = "search.php?q=" + q;
    }
  }
});
</script>
        <div class="profile">
          <div class="avatar"></div>
          <div class="profile-name">
    <?= htmlspecialchars($_SESSION["username"] ?? "Guest") ?>
</div>

        </div>
      </header>

      <section class="content">
        
        <div class="playlist-header">
          <h1><?= htmlspecialchars($playlist['name']) ?></h1>
          <?php if (!empty($playlist['description'])): ?>
            <div class="playlist-desc"><?= htmlspecialchars($playlist['description']) ?></div>
          <?php endif; ?>
        </div>

        <hr>

        <div class="video-grid">
          <?php if (mysqli_num_rows($videos) === 0): ?>
            
            <p>No videos in this playlist yet.</p>

          <?php else: ?>

            <?php while ($v = mysqli_fetch_assoc($videos)): 
              $thumb = $v['thumbnail_path'] ?: 'assets/default-thumb.jpg';
            ?>
              <a class="video-card" href="player.php?id=<?= $v['id'] ?>">
                <img src="<?= htmlspecialchars($thumb) ?>" alt="">
                <div class="video-card-title"><?= htmlspecialchars($v['title']) ?></div>
              </a>
            <?php endwhile; ?>

          <?php endif; ?>
        </div>

      </section>
    </main>

  </div>
</body>
</html>
