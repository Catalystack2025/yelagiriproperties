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

const IMAGE_BASE = "../admin/uploads/";
const BLUEPRINT_BASE = "../admin/uploads/blueprints/";
const PLACEHOLDER_IMG = "./assets/images/no-image.jpg";

/* ----------------------------------------
   PROPERTY CARD GENERATOR (DB VERSION)
---------------------------------------- */
function generateCard(p) {
    const image = p.main_image ? `${IMAGE_BASE}${p.main_image}` : PLACEHOLDER_IMG;

    return `
        <a class="property-card fade-in" href="property-details.php?id=${p.id}">
            <div class="card-image-wrapper">
                <img src="${image}" alt="${p.name}">
                <div class="type-tag">${p.type}</div>
            </div>

            <div class="card-body">
                <h3 class="card-title">${p.name}</h3>
                <p class="card-location">📍 ${p.location}</p>
                <p class="card-desc">${p.description?.substring(0, 70) || ""}...</p>
            </div>

            <div class="card-footer">
                <span class="card-size">📐 ${p.size} SQFT</span>
                <span class="card-link">VIEW DETAILS →</span>
            </div>
        </a>
    `;
}

/* ----------------------------------------
   APPLY FILTERS
---------------------------------------- */
function applyFilters() {
    if (typeof data === 'undefined' || !data || !data.properties) return;

    const locFilter = (document.getElementById('locationFilter')?.value || "all").toLowerCase();
    const typeFilter = (document.getElementById('typeFilter')?.value || "all").toLowerCase();

    const plotGrid = document.getElementById('plotGrid');
    const villaGrid = document.getElementById('villaGrid');
    const homestayGrid = document.getElementById('homestayGrid');

    if (plotGrid) plotGrid.innerHTML = "";
    if (villaGrid) villaGrid.innerHTML = "";
    if (homestayGrid) homestayGrid.innerHTML = "";

    let plots = 0, villas = 0, homestays = 0;

    data.properties.forEach(p => {
        const pLocation = p.location.toLowerCase();
        const pType = p.type.toLowerCase();

        const locMatch = (locFilter === "all" || pLocation.includes(locFilter));
        const typeMatch = (typeFilter === "all" || pType.includes(typeFilter));

        if (!locMatch || !typeMatch) return;

        const cardHTML = generateCard(p);

        if (pType.includes("plot")) {
            plots++; plotGrid.innerHTML += cardHTML;
        } else if (pType.includes("villa")) {
            villas++; villaGrid.innerHTML += cardHTML;
        } else if (pType.includes("stay") || pType.includes("home")) {
            homestays++; homestayGrid.innerHTML += cardHTML;
        }
    });

    if (document.getElementById('plotSection'))
        document.getElementById('plotSection').style.display = plots ? "block" : "none";
    if (document.getElementById('villaSection'))
        document.getElementById('villaSection').style.display = villas ? "block" : "none";
    if (document.getElementById('homestaySection'))
        document.getElementById('homestaySection').style.display = homestays ? "block" : "none";
}

document.readyState === 'loading'
    ? document.addEventListener('DOMContentLoaded', applyFilters)
    : applyFilters();

/* ----------------------------------------
   SHOW PROPERTY DETAILS (DB Compatible)
---------------------------------------- */
function showDetail(id) {
    if (typeof data === 'undefined' || !data || !data.properties) return;
    const p = data.properties.find(x => x.id == id);
    if (!p) return;

    const image = p.main_image ? `${IMAGE_BASE}${p.main_image}` : PLACEHOLDER_IMG;
    const blueprintHtml = p.blueprint
        ? `<div class="blueprint-preview">
                <h3>Plot Blueprint</h3>
                <img src="${BLUEPRINT_BASE}${p.blueprint}" alt="Blueprint for ${p.name}">
           </div>`
        : "";

    window.location.href = `property-details.php?id=${id}`;
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

