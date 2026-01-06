// function toggleMenu() {
//   const nav = document.getElementById("nav-links");
//   if (nav) {
//     nav.classList.toggle("active");
//   }
// }

document.addEventListener('DOMContentLoaded', () => {
  fetchContent().then(data => {
    const grid = document.getElementById('propertyGrid');
    const locFilter = document.getElementById('locationFilter');
    const typeFilter = document.getElementById('typeFilter');

    function render(list) {
      grid.innerHTML = list.map(p => `
        <div class="property-card" onclick="location.href='property-details.html?id=${p.id}'">
          <img src="${p.images[0]}" alt="${p.name}">
          <div class="info">
            <h3>${p.name}</h3>
            <p>${p.location} • ${p.type}</p>
            <span>${p.size}</span>
            <strong>${p.price}</strong>
          </div>
        </div>
      `).join('');
    }

    function applyFilters() {
      const l = locFilter.value;
      const t = typeFilter.value;

      const filtered = data.properties.filter(p =>
        (l === 'all' || p.location === l) &&
        (t === 'all' || p.type === t)
      );

      render(filtered);
    }

    locFilter.onchange = typeFilter.onchange = applyFilters;
    applyFilters();
  });
});


const id = Number(new URLSearchParams(location.search).get('id'));

fetchContent().then(data => {
  const p = data.properties.find(x => x.id === id);
  if (!p) return;

  document.getElementById('detail').innerHTML = `
    <h1>${p.name}</h1>
    <p>${p.location} • ${p.type}</p>
    <strong>${p.price}</strong>

    <div class="gallery">
      ${p.images.map(i => `<img src="${i}">`).join('')}
    </div>

    <p>${p.description}</p>

    <h3>Amenities</h3>
    <ul>${p.amenities.map(a => `<li>${a}</li>`).join('')}</ul>

    <iframe src="${p.mapUrl}" loading="lazy"></iframe>
  `;
});
