<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include "database.php";
session_start();

// ---------------------------
// Validate video ID
// ---------------------------
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { http_response_code(404); exit("Video not found"); }

// Fetch video data
$stmt = mysqli_prepare($conn, 
  "SELECT id, title, description, file_path, thumbnail_path, views, upload_date, category 
   FROM videos WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$video = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$video) { http_response_code(404); exit("Video not found"); }

// Increase views
mysqli_query($conn, "UPDATE videos SET views = views + 1 WHERE id = $id");

// Fetch playlists for dropdown
$playlists = mysqli_query($conn, "SELECT id, name FROM playlists ORDER BY name ASC");

// Add video to playlist
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_to_playlist"])) {
    $playlist_id = (int)$_POST["playlist_id"];

    if ($playlist_id > 0) {
        $stmt = mysqli_prepare($conn,
            "INSERT IGNORE INTO playlist_videos (playlist_id, video_id) VALUES (?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "ii", $playlist_id, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: player.php?id=" . $id);
        exit;
    }
}

// Fetch related videos
$related = null;
if (!empty($video['category'])) {
    $rstmt = mysqli_prepare($conn,
      "SELECT id, title, thumbnail_path 
       FROM videos 
       WHERE category = ? AND id <> ?
       ORDER BY upload_date DESC LIMIT 8");
    mysqli_stmt_bind_param($rstmt, "si", $video['category'], $id);
    mysqli_stmt_execute($rstmt);
    $related = mysqli_stmt_get_result($rstmt);
    mysqli_stmt_close($rstmt);
}
// Handle new comment submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["new_comment"])) {

    $video_id = $id;
    $user_id = $_SESSION["user_id"] ?? 0; // change based on your login system
    $text = trim($_POST["comment_text"] ?? "");
    $parent = isset($_POST["parent_id"]) ? (int)$_POST["parent_id"] : null;

    if ($user_id > 0 && $text !== "") {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO comments (video_id, user_id, comment_text, parent_id)
             VALUES (?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "iisi", $video_id, $user_id, $text, $parent);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header("Location: player.php?id=" . $id);
    exit;
}
// Fetch all comments for this video
$comments = mysqli_query($conn,
    "SELECT c.id, c.comment_text, c.parent_id, c.created_at,
            u.user AS username
     FROM comments c
     JOIN users u ON c.user_id = u.id
     WHERE c.video_id = $id
     ORDER BY c.created_at ASC"
);

// Build nested structure
$comment_tree = [];
while ($c = mysqli_fetch_assoc($comments)) {
    $parent = $c["parent_id"] ?? 0; 
    if ($parent === NULL) $parent = 0;
    $comment_tree[$parent][] = $c;
}

function render_comments($parent_id, $tree) {
    if (!isset($tree[$parent_id])) return;

    echo "<div class='comment-level'>";

    foreach ($tree[$parent_id] as $c) {

        echo "<div class='comment-box'>";

        echo "<div class='comment-header'>";
        echo "<span class='comment-username'>" . htmlspecialchars($c["username"]) . "</span>";
        echo "<span class='comment-time'>" . $c["created_at"] . "</span>";
        echo "</div>";

        echo "<div class='comment-text'>" . nl2br(htmlspecialchars($c["comment_text"])) . "</div>";

        echo "<button class='reply-btn' onclick='openReply(".$c['id'].")'>Reply</button>";

        // Render replies (children)
        render_comments($c["id"], $tree);

        echo "</div>"; // end comment-box
    }

    echo "</div>"; // end comment-level
}


?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($video['title']) ?> — Kent-Tube</title>
<link rel="stylesheet" href="styles.css">

<style>
  
    .sidebar {
        background-color: #082045;
    }
    .logo-text {
        color: rgb(234, 171, 0);
    }
  
/* ---------------- PLAYER LAYOUT FIX ---------------- */

.video-player video {
    width: 100%;
    max-height: 65vh;
    object-fit: contain;
    background: black;
}

/* ---------------- MODAL FIX ---------------- */

#playlistModalBg {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.55);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 5000;
}

#playlistModal {
    background: white;
    padding: 25px;
    width: 350px;
    border-radius: 10px;
}

#playlistModal select,
#playlistModal button {
    width: 100%;
    padding: 9px;
    margin-top: 10px;
}

.playlist-btn {
    padding: 8px 14px;
    background: #0066ff;
    color: #fff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 10px;
}
.playlist-btn:hover { background: #0053cc; }

/* related videos small */
.related-card img {
    width: 100%;
    height: 90px;
    object-fit: cover;
    border-radius: 6px;
}
.related-card {
    display: block;
    margin-bottom: 12px;
}
</style>

</head>
<body>

<div class="app layout-player">
  
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

    <section class="player-area">
      
      <div class="player-main">

        <div class="video-player">
          <video controls preload="metadata"
            poster="<?= htmlspecialchars($video['thumbnail_path'] ?: 'assets/default-thumb.jpg') ?>">
            <source src="<?= htmlspecialchars($video['file_path']) ?>" type="video/mp4">
          </video>
        </div>

        <h1 class="video-title"><?= htmlspecialchars($video['title']) ?></h1>

        <button class="playlist-btn" onclick="openModal()">➕ Add to Playlist</button>

        <div class="meta">
          <span><?= (int)$video['views'] + 1 ?> views</span>
          <span>•</span>
          <span><?= htmlspecialchars(date("Y-m-d H:i", strtotime($video['upload_date']))) ?></span>
          <?php if ($video['category']): ?>
          <span>•</span><span><?= htmlspecialchars($video['category']) ?></span>
          <?php endif; ?>
        </div>

        <div class="desc"><?= nl2br(htmlspecialchars($video['description'])) ?></div>

<div class="comments-section">

  <h2>Comments</h2>

  <!-- New Comment Form -->
  <form method="post" class="comment-form">
    <textarea name="comment_text" placeholder="Write a comment..." required></textarea>
    <input type="hidden" name="parent_id" id="parent_id" value="">
    <button type="submit" name="new_comment">Post</button>
  </form>

  <hr>

  <!-- Display Comments -->
  <div class="comments">
    <?php render_comments(0, $comment_tree); ?>
  </div>

</div>
      </div>

      <aside class="related">
        <?php if ($related): while ($r = mysqli_fetch_assoc($related)): ?>
          <a class="related-card" href="player.php?id=<?= $r['id'] ?>">
            <img src="<?= htmlspecialchars($r['thumbnail_path'] ?: 'assets/default-thumb.jpg') ?>">
            <div class="title"><?= htmlspecialchars($r['title']) ?></div>
          </a>
        <?php endwhile; endif; ?>
      </aside>

    </section>
  </main>

</div>


<!-- ---------------- MODAL OUTSIDE LAYOUT ---------------- -->
<div id="playlistModalBg">
  <div id="playlistModal">
    <h3>Add to Playlist</h3>

    <form method="POST">
      <select name="playlist_id" required>
        <option value="">Select playlist</option>
        <?php while ($p = mysqli_fetch_assoc($playlists)): ?>
          <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
        <?php endwhile; ?>
      </select>

      <button type="submit" name="add_to_playlist">Add</button>
      <button type="button" onclick="closeModal()">Cancel</button>
    </form>
  </div>
</div>

<script>
function openModal() {
  document.getElementById("playlistModalBg").style.display = "flex";
}
function closeModal() {
  document.getElementById("playlistModalBg").style.display = "none";
}
</script>

<script src="app.js"></script>
<script>
function openReply(id) {
    document.getElementById("parent_id").value = id;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

</body>
</html>
      