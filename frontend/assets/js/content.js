/* =====================================================
   BLOG SECTION
===================================================== */

let allBlogs = [];
let visibleBlogs = 4;

// Load Blogs
fetch('./data/content.json')
  .then(res => res.json())
  .then(data => {
    allBlogs = data.blogs || [];
    renderBlogGrid();
  })
  .catch(err => console.error("Blog Load Error:", err));

function renderBlogGrid() {
  const grid = document.getElementById('blogGrid');
  const loadMoreBtn = document.getElementById('loadMoreBlogs');

  if (!grid) return;

  grid.innerHTML = '';

  allBlogs.slice(0, visibleBlogs).forEach(blog => {
    const card = document.createElement('article');
    card.className = 'blog-card';

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

  if (loadMoreBtn) {
    loadMoreBtn.style.display =
      visibleBlogs >= allBlogs.length ? 'none' : 'inline-block';

    loadMoreBtn.onclick = () => {
      visibleBlogs += 4;
      renderBlogGrid();
    };
  }
}

function showBlogDetails(blog) {
  const detailSection = document.getElementById('blogDetailSection');
  if (!detailSection) return;

  document.getElementById('blogImage').src = blog.image;
  document.getElementById('blogDate').textContent = blog.date;
  document.getElementById('blogTitle').textContent = blog.title;
  document.getElementById('blogContent').innerHTML = blog.content;

  detailSection.style.display = 'block';
  detailSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

const backBtn = document.getElementById('backToBlogs');
if (backBtn) {
  backBtn.addEventListener('click', () => {
    document.getElementById('blogDetailSection').style.display = 'none';
    document.getElementById('blogListSection').scrollIntoView({
      behavior: 'smooth'
    });
  });
}



/* =====================================================
   PROPERTIES SECTION
===================================================== */

let propertyData = [];

// Init
async function init() {
  try {
    const response = await fetch('./data/content.json');
    const data = await response.json();

    propertyData = data.properties || [];

    renderListings();
    setupEventListeners();
  } catch (error) {
    console.error("Property Load Error:", error);
    displayErrorMessage();
  }
}

// Render property listing
function renderListings() {
  const locFilter = document.getElementById('locationFilter');
  const typeFilter = document.getElementById('typeFilter');
  const grid = document.getElementById('propertyGrid');

  if (!grid || !locFilter || !typeFilter) return;

  const loc = locFilter.value;
  const type = typeFilter.value;

  const filtered = propertyData.filter(p =>
    (loc === 'all' || p.location === loc) &&
    (type === 'all' || p.type === type)
  );

  if (filtered.length === 0) {
    grid.innerHTML = `
      <p style="padding: 40px; text-align: center; grid-column: 1/-1;">
        No properties found matching your criteria.
      </p>`;
    return;
  }

  grid.innerHTML = filtered
    .map(
      p => `
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
    `
    )
    .join('');
}

// Property detail view
function showDetail(id) {
  const p = propertyData.find(item => item.id === id);
  if (!p) return;

  const listingPage = document.getElementById('listingPage');
  const detailPage = document.getElementById('detailPage');
  const navBack = document.getElementById('navBack');
  const container = document.getElementById('detailContent');

  if (!container) return;

  listingPage?.classList.add('hidden');
  detailPage?.classList.remove('hidden');
  navBack?.classList.remove('hidden');

  window.scrollTo(0, 0);

  container.innerHTML = `
    <div class="detail-view">
      <div>
        <div class="gallery-main-wrapper">
          <img id="mainImage" src="${p.images[0]}" class="gallery-main">
        </div>

        <div class="gallery-thumbs">
          ${p.images
            .map(
              (img, i) =>
                `<img src="${img}" class="gallery-thumb ${
                  i === 0 ? 'active' : ''
                }" onclick="updateGallery(this, '${img}')">`
            )
            .join('')}
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
            ${p.amenities
              .map(
                a => `
              <div class="amenity-item">
                <div class="amenity-icon">${a.icon}</div>
                ${a.name}
              </div>`
              )
              .join('')}
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
    </div>`;
}

function updateGallery(thumb, src) {
  const main = document.getElementById('mainImage');
  if (!main) return;

  main.src = src;
  document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
  thumb.classList.add('active');
}

function handleInquiry(e) {
  e.preventDefault();
  alert("Thank you! Your inquiry has been sent.");
}

// Show listing page
function showListing() {
  document.getElementById('listingPage')?.classList.remove('hidden');
  document.getElementById('detailPage')?.classList.add('hidden');
  document.getElementById('navBack')?.classList.add('hidden');
}

function displayErrorMessage() {
  const grid = document.getElementById('propertyGrid');
  if (!grid) return;

  grid.innerHTML = `
    <div style="grid-column: 1/-1; padding: 50px; text-align: center; background: white; border-radius: 12px; border: 1px solid #ffcdd2;">
        <h3 style="color: #d32f2f;">Failed to Load Data</h3>
        <p>Please check your JSON file.</p>
    </div>
  `;
}

function setupEventListeners() {
  document.getElementById('locationFilter')?.addEventListener('change', renderListings);
  document.getElementById('typeFilter')?.addEventListener('change', renderListings);
  document.getElementById('logoBtn')?.addEventListener('click', showListing);
  document.getElementById('backBtn')?.addEventListener('click', showListing);
}

// Start AFTER page loads
document.addEventListener('DOMContentLoaded', init);
