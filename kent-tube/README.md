Kent-Tube demo

A small static frontend prototype showing a Kent State "Kent-Tube" concept. It is a simple, client-only demo that runs in the browser and uses IndexedDB to store uploaded and recorded videos locally.

How to try

1. Open `index.html` from the `kent-tube` folder in a modern browser (Chrome/Edge/Firefox). Because it uses `getDisplayMedia` for screen capture and IndexedDB, use a secure context (file:// will work in most browsers for local testing; for best results, serve with a local static server).

2. On the Home page you can:
   - Click "Upload Video" to add a video file from your computer (saved to IndexedDB for the demo).
   - Click "Record Assignment" to open a recorder that captures your screen and webcam together and saves the result locally when you stop.

Notes

- This is a front-end demo only (no server or authentication). Professor/student roles are not enforced; it's a UI concept.
- The recording code mixes screen and webcam into a single canvas and records it with MediaRecorder; behavior depends on browser support and permissions.
- Stored videos live in your browser's IndexedDB and are only available on the same machine and browser.

Next steps (optional)

- Add authentication and a backend to store videos persistently.
- Add proper course/channel playlists, comments, and access control.
- Improve recording stability and add progress/preview features.
