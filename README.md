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
