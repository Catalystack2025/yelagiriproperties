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
        <div class="property-card fade-in" onclick="showDetail(${p.id})">
            <div class="card-image-wrapper">
                <img src="${p.images[0]}" alt="${p.name}" loading="lazy">
                <div class="type-tag">${p.type}</div>
            </div>
            <div class="card-body">
                <h3 class="card-title">${p.name}</h3>
                <p class="card-location">📍 ${p.location}</p>
                <p class="card-desc">${p.description}</p>
            </div>
            <div class="card-footer">
                <span class="card-size">📐 ${p.size}</span>
                <span class="card-link">VIEW DETAILS →</span>
            </div>
        </div>
    `;
}

/* ----------------------------------------
   FILTER + LISTING PAGE
---------------------------------------- */
function applyFilters() {
    const loc = document.getElementById('locationFilter')?.value || "all";
    const type = document.getElementById('typeFilter')?.value || "all";

    const plotGrid = document.getElementById('plotGrid');
    const villaGrid = document.getElementById('villaGrid');
    const homestayGrid = document.getElementById('homestayGrid');

    plotGrid.innerHTML = "";
    villaGrid.innerHTML = "";
    homestayGrid.innerHTML = "";

    let plots = 0, villas = 0, homestays = 0;

    data.properties.forEach(p => {
        const locMatch = (loc === 'all' || p.location === loc);
        const typeMatch = (type === 'all' || p.type === type);

        if (locMatch && typeMatch) {
            if (p.type === "Plot") {
                plots++;
                plotGrid.innerHTML += generateCard(p);
            }
            if (p.type === "Villa") {
                villas++;
                villaGrid.innerHTML += generateCard(p);
            }
            if (p.type === "Home Stay") {
                homestays++;
                homestayGrid.innerHTML += generateCard(p);
            }
        }
    });

    document.getElementById('plotSection').style.display = plots ? "block" : "none";
    document.getElementById('villaSection').style.display = villas ? "block" : "none";
    document.getElementById('homestaySection').style.display = homestays ? "block" : "none";
}

/* ----------------------------------------
   DETAIL PAGE LOADER
---------------------------------------- */
function showDetail(id) {
    const p = data.properties.find(x => x.id === id);
    if (!p) return;

    document.getElementById('listingPage').classList.add('hidden');
    document.getElementById('detailPage').classList.remove('hidden');
    document.getElementById('navBack').classList.remove('hidden');

    window.scrollTo(0, 0);

    const detailContent = document.getElementById('detailContent');

    detailContent.innerHTML = `
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
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="tel" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <textarea class="form-textarea" rows="3">I'm interested in ${p.name}...</textarea>
                        </div>
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
                <div><label>Facing Direction</label><p>🧭 ${p.facing}</p></div>
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
   GALLERY THUMB CLICK
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
   INITIALIZE
---------------------------------------- */
window.onload = applyFilters;
