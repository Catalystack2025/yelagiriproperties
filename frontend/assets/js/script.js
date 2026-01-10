/* ----------------------------------------
   MOBILE MENU TOGGLE
---------------------------------------- */
function toggleMenu() {
    const nav = document.getElementById("nav-links");
    if (nav) nav.classList.toggle("active");
}

/* ----------------------------------------
   PAGE LOADER
---------------------------------------- */
const loader = document.getElementById('loader');
const loaderBar = document.getElementById('loaderBar');
let progress = 0;

if (loader && loaderBar) {
    const progressInterval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress >= 90) {
            progress = 95;
            clearInterval(progressInterval);
        }
        loaderBar.style.width = progress + '%';
    }, 150);

    window.addEventListener('load', () => {
        loaderBar.style.width = '100%';
        setTimeout(() => {
            loader.classList.add('loader-hidden');
            setTimeout(() => loader.style.display = 'none', 900);
        }, 600);
    });
}

/* ----------------------------------------
   PROPERTY CARD GENERATOR
---------------------------------------- */
function generateCard(p) {
    return `
        <div class="property-card fade-in">
            <div class="card-image-wrapper">
                <img src="${p.images[0]}" alt="${p.name}">
                <div class="type-tag">${p.type}</div>
            </div>

            <div class="card-body">
                <h3 class="card-title">${p.name}</h3>
                <p class="card-location">📍 ${p.location}</p>
                <p class="card-desc">${p.description}</p>
            </div>

            <div class="card-footer">
                <span class="card-size">📐 ${p.size}</span>
                <a href="property-details.php?id=${p.id}" class="card-link">VIEW DETAILS →</a>
            </div>
        </div>
    `;
}

/* ----------------------------------------
   APPLY FILTERS (FIXED LOCATION LOGIC)
---------------------------------------- */
function applyFilters() {
    // Check if data is available to prevent errors
    if (typeof data === 'undefined' || !data.properties) return;

    // Get filter values and convert to lowercase
    // Default to "all" if the element is missing or value is empty
    const locFilter = (document.getElementById('locationFilter')?.value || "all").toLowerCase();
    const typeFilter = (document.getElementById('typeFilter')?.value || "all").toLowerCase();

    const plotGrid = document.getElementById('plotGrid');
    const villaGrid = document.getElementById('villaGrid');
    const homestayGrid = document.getElementById('homestayGrid');

    // Reset grids if they exist
    if (plotGrid) plotGrid.innerHTML = "";
    if (villaGrid) villaGrid.innerHTML = "";
    if (homestayGrid) homestayGrid.innerHTML = "";

    let plots = 0, villas = 0, homestays = 0;

    // Iterate through properties
    data.properties.forEach(p => {
        const pLocation = p.location.toLowerCase();
        const pType = p.type.toLowerCase();

        // Logic: If filter is "all", it matches everything. 
        // Otherwise, it checks if the property location includes the filter string.
        const locMatch = (locFilter === 'all' || pLocation.includes(locFilter));
        const typeMatch = (typeFilter === 'all' || pType.includes(typeFilter));

        if (locMatch && typeMatch) {
            const cardHTML = generateCard(p);
            
            // Categorize into grids based on type
            if (pType.includes("plot")) {
                plots++;
                if (plotGrid) plotGrid.innerHTML += cardHTML;
            } else if (pType.includes("villa")) {
                villas++;
                if (villaGrid) villaGrid.innerHTML += cardHTML;
            } else if (pType.includes("stay") || pType.includes("home")) {
                homestays++;
                if (homestayGrid) homestayGrid.innerHTML += cardHTML;
            }
        }
    });

    // Toggle section visibility based on whether results were found
    if (document.getElementById('plotSection')) 
        document.getElementById('plotSection').style.display = plots ? "block" : "none";
    if (document.getElementById('villaSection')) 
        document.getElementById('villaSection').style.display = villas ? "block" : "none";
    if (document.getElementById('homestaySection')) 
        document.getElementById('homestaySection').style.display = homestays ? "block" : "none";
}

// Initial call to populate data as soon as DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', applyFilters);
} else {
    applyFilters();
}

/* ----------------------------------------
   SHOW DETAIL PAGE
---------------------------------------- */
function showDetail(id) {
    const p = data.properties.find(x => x.id == id);
    if (!p) return;

    document.getElementById('listingPage').classList.add('hidden');
    document.getElementById('detailPage').classList.remove('hidden');
    document.getElementById('navBack').classList.remove('hidden');

    window.scrollTo(0, 0);

    document.getElementById('detailContent').innerHTML = `
        <div class="detail-header-grid">
            <div class="gallery-column">
                <div class="gallery-main-frame">
                    <img id="mainDisplayImg" src="${p.images[0]}" alt="${p.name}">
                </div>

                <div class="gallery-thumbs-row">
                    ${p.images.map((img, i) => `
                        <div class="gallery-thumb-item ${i === 0 ? 'active' : ''}"
                             onclick="updateGallery(this, '${img}')">
                            <img src="${img}">
                        </div>
                    `).join('')}
                </div>
            </div>

            <div class="sidebar">
                <div class="content-box" style="border-top: 4px solid var(--primary);">
                    <h3 class="form-title">Enquire Now</h3>

                    <form onsubmit="event.preventDefault(); successMsg.classList.remove('hidden'); this.style.opacity='0.5';">
                        <div class="form-group"><label>Name</label><input type="text" required class="form-input"></div>
                        <div class="form-group"><label>Phone</label><input type="tel" required class="form-input"></div>
                        <div class="form-group"><label>Message</label>
                            <textarea class="form-textarea" rows="3">I'm interested in ${p.name}...</textarea></div>
                        <button class="submit-btn">Send Interest</button>

                        <div id="successMsg" class="hidden" style="margin-top:10px; color:var(--primary); font-weight:700;">
                            ✓ Inquiry sent successfully!
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="content-box">
            <h1 class="detail-title">${p.name}</h1>
            <p class="card-location">📍 ${p.location} • <strong>${p.type}</strong></p>

            <div class="stats-grid">
                <div><label>Total Area</label><p>${p.size}</p></div>
                <div><label>Facing</label><p>🧭 ${p.facing}</p></div>
                <div><label>Availability</label><p style="color:var(--primary)">Available</p></div>
                <div><label>Location</label><p>${p.location}</p></div>
            </div>

            <h3>About this Property</h3>
            <p class="desc">${p.description}</p>

            <h3>Key Amenities</h3>
            <div class="amenity-grid">
                ${p.amenities.map(a => `<div class="amenity-item">✓ ${a}</div>`).join('')}
            </div>
        </div>
    `;
}

/* ----------------------------------------
   UPDATE GALLERY IMAGE
---------------------------------------- */
function updateGallery(el, src) {
    document.getElementById("mainDisplayImg").src = src;
    document.querySelectorAll(".gallery-thumb-item").forEach(t => t.classList.remove("active"));
    el.classList.add("active");
}

/* ----------------------------------------
   BACK TO LISTING
---------------------------------------- */
function showListing() {
    document.getElementById('detailPage').classList.add('hidden');
    document.getElementById('listingPage').classList.remove('hidden');
    document.getElementById('navBack').classList.add('hidden');
    applyFilters();
}

/* ----------------------------------------
   HOME PAGE SLIDER
---------------------------------------- */
let currentIndex = 0;
const slides = document.querySelectorAll('.fade-slider .slide');

function showSlide(i) {
    slides.forEach(s => s.classList.remove('active'));
    slides[i].classList.add('active');
}

function nextSlide() { currentIndex = (currentIndex + 1) % slides.length; showSlide(currentIndex); }
function prevSlide() { currentIndex = (currentIndex - 1 + slides.length) % slides.length; showSlide(currentIndex); }

setInterval(nextSlide, 12000);
