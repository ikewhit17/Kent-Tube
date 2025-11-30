<?php
include "database.php";

// fetch latest videos
$sql = "SELECT id, title, thumbnail_path, file_path FROM videos ORDER BY upload_date DESC LIMIT 24";
$res = mysqli_query($conn, $sql);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kent-Tube — Home</title>
  <link rel="stylesheet" href="styles.css">
 
</head>
<body>
<div class="app layout-home">
  <aside class="sidebar">
    <div class="logo-row"><div class="logo-mark">🐺</div><div class="logo-text">KENT-TUBE</div></div>
    <nav class="side-nav" aria-label="Main">
      <button class="nav-btn active" data-page="home">Home</button>
      <button class="nav-btn" data-page="history">History</button>
      <button class="nav-btn" data-page="saved">Saved</button>
      <button class="nav-btn" data-page="playlists">Playlists</button>
    </nav>
  </aside>

  <main class="main">
    <header class="topbar">
      <button class="settings" title="Settings">⚙️</button>
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

      <div class="profile"><div class="avatar"></div><div class="profile-name">Isaac</div></div>
    </header>

    <section class="content">
      <h3>Courses</h3>
<div class="course-buttons">
  <button class="course-btn" onclick="location.href='course.php'">CS 101 - Intro to Programming</button>
  <button class="course-btn" onclick="location.href='course.php'">MATH 220 - Calculus II</button>
  <button class="course-btn" onclick="location.href='course.php'">HIST 200 - Modern History</button>
  <button class="course-btn" onclick="location.href='course.php'">BIO 150 - General Biology</button>
</div>
      <h2>Recents from your courses</h2>
      <div class="video-shelf">
        <?php while ($row = mysqli_fetch_assoc($res)): 
          $id = (int)$row['id'];
          $title = htmlspecialchars($row['title']);
          $thumb = $row['thumbnail_path'] ?: 'assets/default-thumb.jpg';
        ?>
          <a class="video-card" href="player.php?id=<?= $id ?>" title="<?= $title ?>">
            <img src="<?= htmlspecialchars($thumb) ?>" alt="<?= $title ?>">
            <div class="video-title"><?= $title ?></div>
          </a>
        <?php endwhile; ?>
      </div>
      </section>

    </main>
    <div class="floating-brand">🐺</div>
  </div>
  <a class="nav-btn" href="upload.php">Upload</a>
<script src="app.js"></script>
</body>
</html>