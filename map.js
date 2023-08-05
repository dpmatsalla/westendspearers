// Initialize the map centered on Brisbane
var map = L.map('map').setView([-27.468968, 153.000492], 14);

// Add the Tile Layer (you can change the tile URL to other providers if needed)
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

// Add a marker to Orleigh Park
var orleighParkMarker = L.marker([-27.477742, 153.004248]).addTo(map);

// Bind a popup to the marker
orleighParkMarker.bindPopup("<b>Orleigh Park</b><br>Located here.").openPopup();
