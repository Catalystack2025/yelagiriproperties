let heroImages = [];
let currentSlide = 0;

fetch('./data/content.json')
  .then(res => res.json())
  .then(data => {

    // ✅ READ HERO OBJECT
    const heroData = data.hero;
    if (!heroData) return;

    // Text
    document.getElementById('heroTitle').textContent = heroData.title;
    document.getElementById('heroDescription').textContent = heroData.description;

    // Slides
    heroImages = heroData.slides;
    const hero = document.getElementById('hero');

    hero.style.backgroundImage = `url(${heroImages[0]})`;
    hero.style.backgroundSize = 'cover';
    hero.style.backgroundPosition = 'center';

    setInterval(() => {
      currentSlide = (currentSlide + 1) % heroImages.length;
      hero.style.backgroundImage = `url(${heroImages[currentSlide]})`;
    }, heroData.interval || 4000);

  })
  .catch(err => console.error('Hero error:', err));


  //blog section //
  let allBlogs = [];
let visibleBlogs = 4;

fetch('./data/content.json')
  .then(res => res.json())
  .then(data => {
    allBlogs = data.blogs || [];
    renderBlogGrid();
  });

function renderBlogGrid() {
  const grid = document.getElementById('blogGrid');
  const loadMoreBtn = document.getElementById('loadMoreBlogs');
  grid.innerHTML = '';

  allBlogs.slice(0, visibleBlogs).forEach((blog, index) => {
    const card = document.createElement('article');
    card.className = 'blog-card';
    card.style.cursor = 'pointer';

    card.innerHTML = `
      <img src="${blog.image}" alt="${blog.title}">
      <div class="blog-content">
        <span class="blog-date">${blog.date}</span>
        <h3>${blog.title}</h3>
        <p>${blog.excerpt}</p>
        <span class="read-more">Read More →</span>
      </div>
    `;

    card.addEventListener('click', () => showBlogDetails(blog));
    grid.appendChild(card);
  });

  loadMoreBtn.style.display =
    visibleBlogs >= allBlogs.length ? 'none' : 'inline-block';

  loadMoreBtn.onclick = () => {
    visibleBlogs += 4;
    renderBlogGrid();
  };
}

function showBlogDetails(blog) {
  const detailSection = document.getElementById('blogDetailSection');

  document.getElementById('blogImage').src = blog.image;
  document.getElementById('blogDate').textContent = blog.date;
  document.getElementById('blogTitle').textContent = blog.title;
  document.getElementById('blogContent').innerHTML = blog.content;

  detailSection.style.display = 'block';

  detailSection.scrollIntoView({
    behavior: 'smooth',
    block: 'start'
  });
}

document.getElementById('backToBlogs').addEventListener('click', () => {
  document.getElementById('blogDetailSection').style.display = 'none';
  document.getElementById('blogListSection').scrollIntoView({
    behavior: 'smooth'
  });
});


//properties section //
/**
 * Global State
 */
let propertyData = [];

/**
 * Initialize
 * Fetches data from external content.json
 */
async function init() {
    try {
        // Fetching the JSON data from the separate file
        const response = await fetch('content.json');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        propertyData = data.properties;

        // Initial render
        renderListings();
        setupEventListeners();
        
    } catch (error) {
        console.error("Error loading property data:", error);
        displayErrorMessage();
    }
}

/**
 * UI Rendering
 */
function renderListings() {
    const loc = document.getElementById('locationFilter').value;
    const type = document.getElementById('typeFilter').value;
    const grid = document.getElementById('propertyGrid');

    const filtered = propertyData.filter(p => 
        (loc === 'all' || p.location === loc) && 
        (type === 'all' || p.type === type)
    );

    if (filtered.length === 0) {
        grid.innerHTML = `<p style="padding: 40px; text-align: center; grid-column: 1/-1;">No properties found matching your criteria.</p>`;
        return;
    }

    grid.innerHTML = filtered.map(p => `
        <div class="property-card" onclick="showDetail(${p.id})">
            <div class="card-image-wrapper">
                <img src="${p.images[0]}" alt="${p.name}">
                <div class="type-tag">${p.type}</div>
                <div class="price-tag">${p.price}</div>
            </div>
            <div class="card-body">
                <h3 class="card-title">${p.name}</h3>
                <p class="card-location">${p.location}</p>
                <div class="card-footer">
                    <span style="font-size:13px; font-weight:700;">${p.size}</span>
                    <span style="color:var(--primary); font-weight:700;">DETAILS →</span>
                </div>
            </div>
        </div>
    `).join('');
}

function showDetail(id) {
    const p = propertyData.find(item => item.id === id);
    if (!p) return;

    document.getElementById('listingPage').classList.add('hidden');
    document.getElementById('detailPage').classList.remove('hidden');
    document.getElementById('navBack').classList.remove('hidden');
    window.scrollTo(0, 0);

    const container = document.getElementById('detailContent');
    container.innerHTML = `
        <div class="detail-view">
            <div>
                <div class="gallery-main-wrapper">
                    <img id="mainImage" src="${p.images[0]}" class="gallery-main">
                </div>
                <div class="gallery-thumbs">
                    ${p.images.map((img, i) => `
                        <img src="${img}" class="gallery-thumb ${i===0?'active':''}" onclick="updateGallery(this, '${img}')">
                    `).join('')}
                </div>
                
                <div class="content-box">
                    <div class="detail-header">
                        <div>
                            <h1 class="detail-title">${p.name}</h1>
                            <p class="card-location">${p.location} • Premium ${p.type}</p>
                        </div>
                        <div class="detail-price">${p.price}</div>
                    </div>
                    <h2 class="section-title">Description</h2>
                    <p style="margin-bottom:40px; color:var(--text-muted);">${p.description}</p>
                    <h2 class="section-title">Amenities</h2>
                    <div class="amenity-grid">
                        ${p.amenities.map(a => `
                            <div class="amenity-item">
                                <div class="amenity-icon">${a.icon}</div>
                                ${a.name}
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
            <div class="sidebar">
                <div class="inquiry-card">
                    <h3>Interested?</h3>
                    <p style="font-size:14px; margin-bottom:20px; opacity:0.8;">Send an inquiry for ${p.name}</p>
                    <form onsubmit="handleInquiry(event)">
                        <div class="form-group"><label>Full Name</label><input type="text" class="form-input" required></div>
                        <div class="form-group"><label>Email</label><input type="email" class="form-input" required></div>
                        <button type="submit" class="submit-btn">Send Inquiry</button>
                    </form>
                </div>
            </div>
        </div>
    `;
}

/**
 * Interactions & Utilities
 */
function updateGallery(thumb, src) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
}

function handleInquiry(e) {
    e.preventDefault();
    alert("Thank you! Your inquiry has been sent to our agents.");
}

function showListing() {
    document.getElementById('listingPage').classList.remove('hidden');
    document.getElementById('detailPage').classList.add('hidden');
    document.getElementById('navBack').classList.add('hidden');
}

function displayErrorMessage() {
    const grid = document.getElementById('propertyGrid');
    grid.innerHTML = `
        <div style="grid-column: 1/-1; padding: 50px; text-align: center; background: white; border-radius: 12px; border: 1px solid #ffcdd2;">
            <h3 style="color: #d32f2f;">Failed to Load Data</h3>
            <p>Please ensure you are running this project on a local web server (e.g., VS Code Live Server).</p>
        </div>
    `;
}

function setupEventListeners() {
    document.getElementById('locationFilter').addEventListener('change', renderListings);
    document.getElementById('typeFilter').addEventListener('change', renderListings);
    document.getElementById('logoBtn').addEventListener('click', showListing);
    document.getElementById('backBtn').addEventListener('click', showListing);
}

// Start app
document.addEventListener('DOMContentLoaded', init);