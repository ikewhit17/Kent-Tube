<?php
  include("database.php");
  session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kent-Tube — Course</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="app layout-course">
    <aside class="sidebar">
      <div class="logo-row">
        <div class="logo-mark">🐺</div>
        <div class="logo-text">KENT-TUBE</div>
      </div>
      <nav class="side-nav" aria-label="Main">
        <button class="nav-btn" data-page="home">Home</button>
        <button class="nav-btn" data-page="history">History</button>
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
        <div class="profile"> <div class="avatar"><img src="Isaac.png"></div><div class="profile-name">
    <?= htmlspecialchars($_SESSION["username"] ?? "Guest") ?>
</div></div>
      </header>

      <section class="course-hero">
        <img src="" alt="Kent-Tube" class="course-logo" aria-hidden="true">
        <div class="course-tabs">
          <button class="tab active">Recent</button>
          <button class="tab">Playlists</button>
          <button class="tab">Assignments</button>
          <button class="tab">Files</button>
        </div>
      </section>

      <section class="content course-content">
        <div id="courseVideos" class="videos-grid"></div>
      </section>
    </main>
  </div>

  <script src="app.js"></script>
</body>
</html>