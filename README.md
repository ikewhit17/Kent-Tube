
A web-based video-sharing platform prototype inspired by Kent State’s learning environment. This version extends the original static demo into a functional PHP/MySQL web app with user accounts, uploads, and dynamic playback.

What it does
------------
- Register/Login: Secure authentication using PHP sessions and password hashing.
- Upload Videos: Accepts MP4/WebM/OGG files up to 1 GB. Generates thumbnails with FFmpeg, or falls back to a default image.
- Browse Home: Lists uploaded videos with titles and thumbnails, plus quick-access course buttons for sample categories.
- View Videos: Plays uploaded content in an HTML5 player with title, description, upload date, category, and view count. Related videos from the same category appear in a sidebar.

Requirements
------------
- PHP 8
- MySQL/MariaDB
- FFmpeg (for automatic thumbnail generation)

Setup (local/dev)
-----------------
1) Create a MySQL database and user that match your `database.php` configuration.
2) Ensure FFmpeg/FFprobe are installed and accessible to PHP (or adjust the binary paths in `upload.php`).
3) Place the project in your web root and open `index.php`.

Notes
-----
- Sessions back the login flow; uploads and playback rely on the database for metadata.
- Thumbnails are generated on upload when FFmpeg is available; otherwise, a default thumbnail is used.

Next steps (optional)
---------------------
- Add real user video history and playlists.
- Implement comments, likes, and search filtering.
- Improve player responsiveness for mobile devices.
- Deploy to a remote host (Apache/Nginx) with proper environment-based configuration and secrets management.
Kent-Tube
A web-based video-sharing platform prototype inspired by Kent State’s learning environment.
This version extends the original static demo into a functional PHP/MySQL web app with user accounts, uploads, and dynamic playback.

What you can do
Register/Login:
Secure authentication using PHP sessions and password hashing.

Upload Videos:
Upload MP4/WebM/OGG files up to 1 GB each.
Thumbnails are auto-generated via FFmpeg (or fall back to a default image).

Browse Home Page:
Displays uploaded videos with titles and thumbnails.
Includes quick-access course buttons for sample categories.

View Videos:
Watch uploaded content in an HTML5 player.
Title, description, upload date, category, and views are shown below.
Related videos from the same category appear on the right.

Notes:
FFmpeg is required for automatic thumbnail generation.

Next steps (optional)
Add real user video history and playlists.
Implement comments, likes, and search filtering.
Improve player responsiveness for mobile devices.
Deploy to a remote host (e.g., Apache or Nginx server).

Summary
This project demonstrates a simple but complete video-sharing system using:
PHP 8
MySQL/MariaDB
HTML5 + CSS3 + JS
FFmpeg for video thumbnail generation
Designed as a capstone-style educational tool under Kent State’s “Kent-Tube” concept.
