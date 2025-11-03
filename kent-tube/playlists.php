<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kent-Tube — Playlists</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="app layout-playlists">
    <aside class="sidebar">
      <div class="logo-row"><div class="logo-mark">🐺</div><div class="logo-text">KENT-TUBE</div></div>
      <nav class="side-nav" aria-label="Main">
        <button class="nav-btn" data-page="home">Home</button>
        <button class="nav-btn" data-page="history">History</button>
        <button class="nav-btn" data-page="saved">Saved</button>
        <button class="nav-btn active" data-page="playlists">Playlists</button>
      </nav>
    </aside>

    <main class="main">
      <header class="topbar"><button class="settings">⚙️</button><div class="search-wrap"><input placeholder="Search Bar"></div><div class="profile"><div class="avatar"></div><div class="profile-name">Isaac</div></div></header>

      <section class="content">
        <h1>Playlists</h1>
        <hr>
        <div class="two-col-grid">
          <a class="video-card placeholder" href="playlist.html?name=Playlist%201">Playlist</a>
          <a class="video-card placeholder" href="playlist.html?name=Playlist%202">Playlist</a>
          <a class="video-card placeholder" href="playlist.html?name=Playlist%203">Playlist</a>
          <a class="video-card placeholder" href="playlist.html?name=Playlist%204">Playlist</a>
          <a class="video-card placeholder" href="playlist.html?name=Playlist%205">Playlist</a>
          <a class="video-card placeholder" href="playlist.html?name=Playlist%206">Playlist</a>
        </div>
      </section>

    </main>
  </div>
  <script src="app.js"></script>
</body>
</html>