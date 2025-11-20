<?php
include "database.php";

$errors = [];
$ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['playlist_name'] ?? '');
  $desc = trim($_POST['playlist_description'] ?? '');

  if ($name === '') {
    $errors[] = 'Name is required.';
  }

  if (!$errors) {
    $stmt = mysqli_prepare($conn, "INSERT INTO playlists (name, description) VALUES (?, ?)");
    if ($stmt) {
      mysqli_stmt_bind_param($stmt, "ss", $name, $desc);
      if (mysqli_stmt_execute($stmt)) {
        $ok = true;
      } else {
        $errors[] = 'Could not save playlist.';
      }
      mysqli_stmt_close($stmt);
    } else {
      $errors[] = 'Could not prepare statement.';
    }
  }
}

$playlists = mysqli_query($conn, "SELECT id, name, description FROM playlists ORDER BY id DESC");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kent-Tube - Playlists</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    .actions {display:flex;justify-content:space-between;align-items:center;margin:0 0 12px}
    .btn {background:#111;color:#fff;border:0;border-radius:6px;padding:8px 12px;cursor:pointer}
    .msg{margin:8px 0;font-weight:600}
    .err{color:#b00}
    .ok{color:#070}
    .modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,0.55);display:none;align-items:center;justify-content:center;z-index:10}
    .modal{background:#fff;border-radius:10px;max-width:420px;width:90%;padding:16px;box-shadow:0 10px 30px rgba(0,0,0,0.2)}
    .modal h3{margin-top:0}
    .modal label{display:block;margin:10px 0 4px}
    .modal input,.modal textarea{width:100%;padding:8px;border:1px solid #ccc;border-radius:6px}
    .modal-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:12px}
    .playlist-card{display:block;padding:14px;border-radius:10px;background:#e6e6e6;text-decoration:none;color:inherit}
    .playlist-card h3{margin:0 0 6px}
    .playlist-card p{margin:0;color:#444;font-size:.95rem;line-height:1.3}
    .empty{color:#666}
  </style>
</head>
<body>
  <div class="app layout-playlists">
    <aside class="sidebar">
      <div class="logo-row"><div class="logo-mark">dY?</div><div class="logo-text">KENT-TUBE</div></div>
      <nav class="side-nav" aria-label="Main">
        <button class="nav-btn" data-page="home">Home</button>
        <button class="nav-btn" data-page="history">History</button>
        <button class="nav-btn" data-page="saved">Saved</button>
        <button class="nav-btn active" data-page="playlists">Playlists</button>
      </nav>
    </aside>

    <main class="main">
      <header class="topbar">
        <button class="settings" title="Settings">?</button>
        <div class="search-wrap"><input placeholder="Search Bar"></div>
        <div class="profile"><div class="avatar"></div><div class="profile-name">Isaac</div></div>
      </header>

      <section class="content">
        <div class="actions">
          <h1>Playlists</h1>
          <button class="btn" id="openCreate">New Playlist</button>
        </div>
        <hr>

        <?php if ($ok): ?>
          <div class="msg ok">Playlist created.</div>
        <?php elseif ($errors): ?>
          <div class="msg err"><?php echo htmlspecialchars(implode(' ', $errors)); ?></div>
        <?php endif; ?>

        <div class="two-col-grid">
          <?php if ($playlists && mysqli_num_rows($playlists) > 0): ?>
            <?php while ($pl = mysqli_fetch_assoc($playlists)): ?>
              <a class="playlist-card" href="playlist.php?id=<?php echo (int)$pl['id']; ?>">
                <h3><?php echo htmlspecialchars($pl['name']); ?></h3>
                <p><?php echo $pl['description'] !== '' ? htmlspecialchars($pl['description']) : 'No description'; ?></p>
              </a>
            <?php endwhile; ?>
          <?php else: ?>
            <div class="empty">No playlists yet. Create one to get started.</div>
          <?php endif; ?>
        </div>
      </section>

    </main>
  </div>

  <div class="modal-backdrop" id="createModal" role="dialog" aria-modal="true" aria-labelledby="createTitle">
    <div class="modal">
      <h3 id="createTitle">Create playlist</h3>
      <form method="post" id="createForm">
        <label for="playlist_name">Name *</label>
        <input type="text" id="playlist_name" name="playlist_name" required maxlength="120">

        <label for="playlist_description">Description</label>
        <textarea id="playlist_description" name="playlist_description" rows="3" maxlength="400"></textarea>

        <div class="modal-actions">
          <button type="button" class="btn" id="cancelCreate">Cancel</button>
          <button type="submit" class="btn">Save</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const modal = document.getElementById('createModal');
    const openBtn = document.getElementById('openCreate');
    const cancelBtn = document.getElementById('cancelCreate');

    function openModal() {
      modal.style.display = 'flex';
      document.getElementById('playlist_name').focus();
    }
    function closeModal() {
      modal.style.display = 'none';
    }

    openBtn?.addEventListener('click', openModal);
    cancelBtn?.addEventListener('click', closeModal);
    modal?.addEventListener('click', (e) => {
      if (e.target === modal) closeModal();
    });
  </script>
  <script src="app.js"></script>
</body>
</html>
