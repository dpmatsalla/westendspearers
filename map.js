// Initialize the map centered on Brisbane
var map = L.map('map').setView([ -27.488299, 152.996411], 14);

// Add the Tile Layer (you can change the tile URL to other providers if needed)
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

// Add markers 
var orleighParkMarker = L.marker([-27.488299, 152.996411]).addTo(map);
var rockMarker = L.marker([-27.504172, 153.009583]).addTo(map);

// Bind a popup to the marker
orleighParkMarker.bindPopup("<b>Orleigh Park</b>").openPopup();
