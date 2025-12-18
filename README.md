Kent-Tube
=========
Kent-Tube is a full-stack, web-based video-sharing platform inspired by Kent State’s learning environment. What started as a static prototype evolved into a fully functional PHP/MySQL application running on a self-hosted server. The project focuses on real backend systems, dynamic content, and user interaction rather than relying on third-party services.

Features
--------
- **User Authentication**
  - Secure registration and login
  - Password hashing and PHP session management

- **Video Upload & Playback**
  - Upload MP4, WebM, and OGG videos (up to ~1 GB)
  - Videos stored directly on the server
  - HTML5 video player with metadata
  - Automatic view count tracking

- **Thumbnail Generation**
  - Thumbnails generated automatically using FFmpeg
  - Fallback to a default thumbnail if FFmpeg is unavailable

- **Browse & Discover**
  - Home page with uploaded videos and thumbnails
  - Category-based browsing
  - Related videos shown on the player page

- **Search**
  - Search bar that queries the videos database
  - Redirects to a results page showing matching videos

- **Playlists**
  - Create playlists
  - Add videos to playlists via modal UI
  - Playlist/video relationship handled through a join table

- **Comments System**
  - Comment on videos
  - Nested threaded replies
  - Comments tied to users and videos through relational tables

- **Backend Infrastructure**
  - Self-hosted Apache server
  - PHP backend with MySQL/MariaDB
  - SSH access for server management
  - Database managed through phpMyAdmin

Requirements
------------
- PHP 8+
- MySQL or MariaDB
- Apache (or compatible web server)
- FFmpeg / FFprobe (for thumbnail generation)
- Linux-based environment recommended

Setup (Local / Development)
---------------------------
1. Clone the repository into your web server root.
2. Create a MySQL/MariaDB database and update credentials in `database.php`.
3. Import the required tables (`users`, `videos`, `comments`, `playlists`, etc.).
4. Ensure FFmpeg is installed and accessible to PHP.
5. Set correct permissions for the `uploads/` directory.
6. Open `index.php` or `home.php` in your browser.

Notes
-----
- All video metadata is stored in the database; files are stored on disk.
- Sessions are required for playlists and commenting.
- The project intentionally avoids third-party hosting to demonstrate backend knowledge.
- Error reporting can be enabled during development for debugging.

Future Improvements
-------------------
- Likes and subscriptions
- User profiles and avatars
- Improved mobile responsiveness
- Video recommendations based on history
- Pagination and performance optimizations
- Deployment with environment-based configuration

Authors
-------
Kent-Tube was built as a collaborative team project, with contributions across backend systems, frontend styling, UI/UX design, documentation, and presentation.

