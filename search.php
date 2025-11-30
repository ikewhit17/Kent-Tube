<?php
include "database.php";
session_start();

$q = trim($_GET['q'] ?? '');

if ($q === '') {
    header("Location: home.php");
    exit;
}

$like = "%$q%";

$stmt = mysqli_prepare($conn, 
    "SELECT id, title, description, thumbnail_path 
     FROM videos 
     WHERE title LIKE ? 
        OR description LIKE ?
     ORDER BY upload_date DESC"
);
mysqli_stmt_bind_param($stmt, "ss", $like, $like);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Search results for "<?= htmlspecialchars($q) ?>" — Kent-Tube</title>
<link rel="stylesheet" href="styles.css">

<style>
.results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 20px;
    padding: 20px;
}
.result-card img {
    width: 100%;
    border-radius: 8px;
}
.result-card {
    text-decoration: none;
    color: #000;
}
</style>
</head>

<body>
<div class="app layout-home">

  <aside class="sidebar">
    <div class="logo-row"><div class="logo-mark">🐺</div><div class="logo-text">KENT-TUBE</div></div>
    <nav class="side-nav">
      <a class="nav-btn" href="home.php">Home</a>
      <a class="nav-btn" href="history.php">History</a>
      <a class="nav-btn" href="saved.php">Saved</a>
      <a class="nav-btn" href="playlists.php">Playlists</a>
    </nav>
  </aside>

  <main class="main">
    <header class="topbar">
      <button class="settings">⚙️</button>
      <div class="search-wrap">
        <input id="search" placeholder="Search Bar" value="<?= htmlspecialchars($q) ?>">
      </div>
      <div class="profile"><div class="avatar"></div><div class="profile-name">Isaac</div></div>
    </header>

    <section class="content">
      <h2>Search results for “<?= htmlspecialchars($q) ?>”</h2>

      <div class="results-grid">
        <?php if (mysqli_num_rows($result) === 0): ?>
          <p>No videos found.</p>
        <?php else: ?>
          <?php while ($v = mysqli_fetch_assoc($result)): ?>
            <a class="result-card" href="player.php?id=<?= $v['id'] ?>">
              <img src="<?= htmlspecialchars($v['thumbnail_path'] ?: 'assets/default-thumb.jpg') ?>">
              <div><?= htmlspecialchars($v['title']) ?></div>
            </a>
          <?php endwhile; ?>
        <?php endif; ?>
      </div>

    </section>
   </main>
</div>

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

</body>
</html>
