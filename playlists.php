<?php
include "database.php";
session_start();

// Handle playlist creation
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["create_playlist"])) {

    $name = trim($_POST["name"] ?? "");
    $desc = trim($_POST["description"] ?? "");

    if ($name !== "") {
        $stmt = mysqli_prepare($conn, 
            "INSERT INTO playlists (name, description) VALUES (?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "ss", $name, $desc);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: playlists.php");
        exit;
    }
}

// Fetch all playlists
$playlists = mysqli_query($conn, "SELECT id, name, description FROM playlists ORDER BY id DESC");
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Playlists — Kent-Tube</title>
<link rel="stylesheet" href="styles.css">
<style>
/* Page Layout */
.playlist-container {
    padding: 20px;
}

/* Playlist Cards */
.playlist-card {
    padding: 15px;
    margin: 10px 0;
    border-radius: 8px;
    background: #ffffff;
    border: 1px solid #ccc;
    cursor: pointer;
}
.playlist-card:hover {
    background: #f3f3f3;
}

/* New Playlist Button */
.new-btn {
    padding: 10px 16px;
    background: #0066ff;
    color: #fff;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    margin-bottom: 15px;
}
.new-btn:hover {
    background: #0053cc;
}

/* Popup Modal */
.modal-bg {
    display: none;
    position: fixed;
    top: 0; left: 0;
    height: 100%; width: 100%;
    background: rgba(0,0,0,0.5);
    justify-content: center; align-items: center;
}

.modal {
    background: white;
    padding: 25px;
    border-radius: 10px;
    width: 350px;
}
.modal input, .modal textarea {
    width: 100%;
    margin: 8px 0;
    padding: 8px;
}
.modal button {
    padding: 8px 14px;
    margin-top: 10px;
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
        <a class="nav-btn active" href="playlists.php">Playlists</a>
      </nav>
    </aside>

    <main class="main playlist-container">

      <h1>Your Playlists</h1>

      <!-- New Playlist Button -->
      <button class="new-btn" onclick="openModal()">+ New Playlist</button>

      <!-- List Playlists -->
      <?php while ($p = mysqli_fetch_assoc($playlists)): ?>
        <div class="playlist-card" onclick="location.href='playlist.php?id=<?= $p['id'] ?>'">
          <h3><?= htmlspecialchars($p['name']) ?></h3>
          <p><?= htmlspecialchars($p['description']) ?></p>
        </div>
      <?php endwhile; ?>

    </main>
</div>

<!-- Popup Modal for Creating Playlist -->
<div class="modal-bg" id="modalBg">
  <div class="modal">
    <h2>Create Playlist</h2>
    <form method="post">
      <input type="text" name="name" placeholder="Playlist name" required>
      <textarea name="description" placeholder="Description (optional)" rows="3"></textarea>
      <br>
      <button type="submit" name="create_playlist">Create</button>
      <button type="button" onclick="closeModal()">Cancel</button>
    </form>
  </div>
</div>

<script>
function openModal() {
  document.getElementById("modalBg").style.display = "flex";
}
function closeModal() {
  document.getElementById("modalBg").style.display = "none";
}
</script>

</body>
</html>
