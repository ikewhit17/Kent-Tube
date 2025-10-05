// Lightweight Kent-Tube front-end router and rendering for static demo
const pages = { home: renderHome, course: renderCourse, player: renderPlayer };

document.addEventListener('click', (e)=>{
  const btn = e.target.closest('[data-page]');
  if(btn){
    const p = btn.getAttribute('data-page');
    navigate(p);
  }
});

function navigate(page){
  // simple navigation for demo: load corresponding HTML pages where applicable
  if(page==='home') location.href = 'index.html';
  if(page==='history') location.href = 'history.html';
  if(page==='saved') location.href = 'saved.html';
  if(page==='playlists') location.href = 'playlists.html';
}

function createCourseMock(){
  return [
    {id:'cs101', title:'CS 101 - Intro to Programming'},
    {id:'math220', title:'MATH 220 - Calculus II'},
    {id:'hist200', title:'HIST 200 - Modern History'},
    {id:'bio150', title:'BIO 150 - General Biology'}
  ];
}

// Mock videos used on home and course pages. Each video has an id (courseId-index for course videos or home-x)
const mockVideos = [
  {id: 'home-0', title: 'Welcome to Kent-Tube', course: 'cs101'},
  {id: 'home-1', title: 'How to Record Assignments', course: 'cs101'},
  {id: 'home-2', title: 'Syllabus Overview', course: 'math220'},
  // generate some course-specific videos too (courseId-index)
  {id: 'cs101-0', title: 'Lecture 1 — Intro', course: 'cs101'},
  {id: 'cs101-1', title: 'Lecture 2 — Variables', course: 'cs101'},
  {id: 'math220-0', title: 'Lecture 1 — Limits', course: 'math220'},
];

function getVideoById(id){
  return mockVideos.find(v=>v.id===id);
}

function renderHome(){
  const courses = createCourseMock();
  const container = document.getElementById('courseCards');
  if(!container) return;
  container.innerHTML = '';
  courses.forEach(c=>{
    const div = document.createElement('div'); div.className='course-row';
    div.textContent = c.title;
    div.addEventListener('click', ()=> location.href = `course.html?id=${c.id}`);
    container.appendChild(div);
  });

  // Render the recent video shelf on home page
  const shelf = document.querySelector('.video-shelf');
  if(shelf){
    shelf.innerHTML = '';
    // pick first 3 mock videos as recents
    mockVideos.slice(0,3).forEach(v=>{
      const card = document.createElement('div'); card.className='video-card';
      card.textContent = v.title;
      card.dataset.vid = v.id;
      card.addEventListener('click', ()=> location.href = `player.html?vid=${encodeURIComponent(v.id)}`);
      shelf.appendChild(card);
    });
  }
}

function renderCourse(){
  const params = new URLSearchParams(location.search);
  const idp = params.get('id') || 'unknown';
  const title = createCourseMock().find(c=>c.id===idp)?.title || 'Course';
  const el = document.getElementById('courseTitle');
  if(el) el.textContent = title;

  const container = document.getElementById('courseVideos');
  if(!container) return;
  container.innerHTML = '';
  // Bind tabs and render the default tab content for this course
  bindCourseTabs();
  // default to Recent
  renderCourseTab('recent');
}

function bindCourseTabs(){
  const tabs = document.querySelectorAll('.course-tabs .tab');
  if(!tabs || tabs.length===0) return;
  tabs.forEach(t=> t.addEventListener('click', ()=>{
    tabs.forEach(x=>x.classList.remove('active'));
    t.classList.add('active');
    renderCourseTab(t.textContent.trim().toLowerCase());
  }));
}

function renderCourseTab(name){
  const params = new URLSearchParams(location.search);
  const courseId = params.get('id') || null;
  const container = document.getElementById('courseVideos');
  if(!container) return;
  container.innerHTML = '';
  name = (name||'recent').toLowerCase();
  if(name === 'recent'){
    // show recent videos for this course (or site-wide if no course)
    const recents = courseId ? mockVideos.filter(v=>v.course===courseId) : mockVideos.slice(0,6);
    if(recents.length===0){
      container.textContent = 'No recent videos.';
      return;
    }
    recents.forEach(v=> container.appendChild(createVideoCard(v)));
  } else if(name === 'playlists'){
    // show placeholder playlists for this course; make them link to playlist detail pages
    for(let i=0;i<6;i++){
      const playlistName = courseId ? `${courseId}-playlist-${i+1}` : `Playlist ${i+1}`;
      const a = document.createElement('a');
      a.className = 'video-card';
      a.href = `playlist.html?name=${encodeURIComponent(playlistName)}`;
      a.textContent = courseId ? `Playlist ${i+1}` : `Playlist ${i+1}`;
      container.appendChild(a);
    }
  } else if(name === 'assignments'){
    // show placeholder assignments with record/upload CTA
    for(let i=0;i<4;i++){
      const a = document.createElement('div'); a.className='card';
      a.innerHTML = `<div style='font-weight:700'>Assignment ${i+1}</div><div class='muted'>Due: --</div><div style='margin-top:.5rem'><button class='nav-assign' data-assign='${i}'>Record / Submit</button></div>`;
      a.querySelector('.nav-assign').addEventListener('click', ()=> alert('Open recorder (demo)'));
      container.appendChild(a);
    }
  } else if(name === 'files'){
    // show file list placeholders
    for(let i=0;i<6;i++){
      const f = document.createElement('div'); f.className='card';
      f.innerHTML = `<div style='font-weight:700'>File ${i+1}</div><div class='muted'>PDF · ${Math.floor(Math.random()*200)+20} KB</div>`;
      container.appendChild(f);
    }
  } else {
    container.textContent = 'Unknown tab';
  }
}

function createVideoCard(v){
  const card = document.createElement('div'); card.className='video-card';
  card.textContent = v.title || 'Video';
  card.addEventListener('click', ()=> location.href = `player.html?vid=${encodeURIComponent(v.id)}`);
  return card;
}

function renderPlayer(){
  // Show video info based on vid param
  const params = new URLSearchParams(location.search);
  const vid = params.get('vid');
  // find player UI elements
  const playerEl = document.querySelector('.video-player');
  const descEl = document.querySelector('.desc');
  if(!playerEl) return;
  let video = null;
  if(vid) video = getVideoById(vid);
  if(!video){
    // fallback: if vid not found but id and v present (legacy), build a title
    const course = params.get('id');
    const vnum = params.get('v');
    if(course && vnum!=null){
      video = { id: `${course}-${vnum}`, title: `Lecture ${Number(vnum)+1}`, course };
    }
  }
  playerEl.textContent = video ? video.title : 'Video Player';
  if(descEl) descEl.textContent = video ? `Playing: ${video.title} — course ${video.course || 'N/A'}` : 'Description...';
}

// auto-run page-specific renderers
document.addEventListener('DOMContentLoaded', ()=>{
  renderHome(); renderCourse(); renderPlayer();
});

// playlist page rendering
(function playlistPage(){
  const el = document.getElementById('playlistTitle');
  if(!el) return;
  const params = new URLSearchParams(location.search);
  const name = params.get('name') || 'Playlist';
  el.textContent = name;
  const container = document.getElementById('playlistVideos');
  if(!container) return;
  for(let i=1;i<=8;i++){
    const a = document.createElement('a'); a.className='video-card'; a.href = `player.html?vid=${encodeURIComponent('playlist-'+i)}`;
    a.textContent = 'Video';
    container.appendChild(a);
  }
})();
