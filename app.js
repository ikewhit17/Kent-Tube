// Minimal Kent-Tube client JS
// – No mock data
// – No DOM overwrites of the player/description
// – Only handles sidebar navigation buttons that use data-page

document.addEventListener('click', (e) => {
  const btn = e.target.closest('[data-page]');
  if (!btn) return;
  const p = btn.getAttribute('data-page');
  if (!p) return;

  switch (p) {
    case 'home':       location.href = 'home.php'; break;
    case 'history':    location.href = 'history.php'; break;
    case 'saved':      location.href = 'saved.php'; break;
    case 'playlists':  location.href = 'playlists.php'; break;
    default:           /* no-op */ break;
  }
});

// Optional: mark current nav active by URL
document.addEventListener('DOMContentLoaded', () => {
  const path = location.pathname.toLowerCase();
  const map = new Map([
    ['home.php', 'home'],
    ['history.php', 'history'],
    ['saved.php', 'saved'],
    ['playlists.php', 'playlists'],
  ]);
  for (const [file, page] of map) {
    if (path.endsWith('/' + file)) {
      const btn = document.querySelector(`[data-page="${page}"]`);
      if (btn) btn.classList.add('active');
    }
  }
});
