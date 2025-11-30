<?php
  include("database.php");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kent-Tube — History</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="app layout-history">
    <aside class="sidebar">
      <div class="logo-row"><div class="logo-mark">🐺</div><div class="logo-text">KENT-TUBE</div></div>
      <nav class="side-nav" aria-label="Main">
        <button class="nav-btn" data-page="home">Home</button>
        <button class="nav-btn active" data-page="history">History</button>
        <button class="nav-btn" data-page="saved">Saved</button>
        <button class="nav-btn" data-page="playlists">Playlists</button>
      </nav>
    </aside>

    <main class="main">
      <header class="topbar"><button class="settings">⚙️</button><div class="search-wrap"><input placeholder="Search Bar">
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
    <div class="profile"><div class="avatar"></div><div class="profile-name">Isaac</div></div></header>

      <section class="content">
        <h1>History</h1>
        <hr>
        <div class="two-col-grid">
          <a class="video-card placeholder" href="player.php?vid=history-1">Video</a>
          <a class="video-card placeholder" href="player.php?vid=history-2">Video</a>
          <a class="video-card placeholder" href="player.php?vid=history-3">Video</a>
          <a class="video-card placeholder" href="player.php?vid=history-4">Video</a>
          <a class="video-card placeholder" href="player.php?vid=history-5">Video</a>
          <a class="video-card placeholder" href="player.php?vid=history-6">Video</a>
        </div>
      </section>

    </main>
  </div>
  <script src="app.js"></script>
</body>
</html>