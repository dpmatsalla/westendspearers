function plotMap(data) {
    var activity = data[0];
    data.forEach(activity => {
        var type = activity.type;
        var polylinePoints = L.Polyline.fromEncoded(activity.map.summary_polyline).getLatLngs();
        var polyline = L.polyline(polylinePoints, {
            color: 'blue',
            opacity: 1.0,
            weight: 3
        }).bindPopup('<b>' + activity.name + '</b><br>Dist = ' + activity.distance + ' m').addTo(map);
    });
    return;
}

// Initialize the map centered on Brisbane
var map = L.map('map').setView([ -27.488299, 152.996411], 14);

// Add the Tile Layer (you can change the tile URL to other providers if needed)
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

// Add markers 
var m = [];
m[0] = L.marker([-27.488299, 152.996411]).addTo(map);
m[0].bindPopup("<b>Orleigh Park</b>").openPopup();
m[1] = L.marker([-27.504172, 153.009583]).addTo(map);
m[1].bindPopup("<b><a href='https://goo.gl/maps/sdDeoHEhbGUynhx58'>The Rock</a></b>");
m[2] = L.marker([-27.441405, 153.043811]).addTo(map);
m[2].bindPopup("<b>Breakfast Creek</b>");
m[3] = L.marker([-27.433327, 153.044899]).addTo(map);
m[3].bindPopup("<b><a href='https://www.theblackalbion.com.au/collingwoodblack'>Collingwood Black</a></b>");
m[4] = L.marker([-26.387749, 153.089658]).addTo(map);
m[4].bindPopup("<b><a href='https://hastingsnoosa.com.au/'>The Hastings</a></b>");

plotMap(data);
