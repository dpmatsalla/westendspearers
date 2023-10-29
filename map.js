function plotMap(data) {
    // Add the Tile Layers
    var streets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        'attribution':'google Streets',
        'maxZoom':20,
        'minZoom':0,
        'subdomains':['mt0','mt1','mt2','mt3'],
    }).addTo(map);
    var satellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        'attribution':'google Satellite',
        'maxZoom':20,
        'minZoom':0,
        'subdomains':['mt0','mt1','mt2','mt3'],
    });
    L.control.layers({
        "Streets": streets,
        "Satellite": satellite
    }).addTo(map);
    
    // Add markers 
    var m = [];
    m[0] = L.marker([-27.488299, 152.996411]).addTo(map);
    m[0].bindPopup("<b><a href='https://www.waterwaysguide.org.au/accesspoint-detail/19938'>Orleigh Park Pontoon</a></b>").openPopup();
    m[1] = L.marker([-27.504172, 153.009583]).addTo(map);
    m[1].bindPopup("<b><a href='https://goo.gl/maps/sdDeoHEhbGUynhx58'>The Rock</a></b>");
    m[2] = L.marker([-27.441405, 153.043811]).addTo(map);
    m[2].bindPopup("<b><a href='https://paddling.com/paddle/locations/ross-st-boat-ramp'>Breakfast Creek Boat Ramp</a></b>");
    m[3] = L.marker([-27.433327, 153.044899]).addTo(map);
    m[3].bindPopup("<b><a href='https://www.theblackalbion.com.au/collingwoodblack'>Collingwood Black</a></b>");
    m[4] = L.marker([-26.387749, 153.089658]).addTo(map);
    m[4].bindPopup("<b><a href='https://hastingsnoosa.com.au/'>The Hastings</a></b>");
    
    //plot the map activities from the mapdata.js file
    var activity = data[0];
    data.forEach(activity => {
        var type = activity.type;
        var polylinePoints = L.Polyline.fromEncoded(activity.map.summary_polyline).getLatLngs();
        var polyline = L.polyline(polylinePoints, {
            color: 'darkcyan',
            opacity: 1.0,
            weight: 3
        }).bindPopup('<b>' + activity.name + '</b><br>Dist = ' + activity.distance + ' m').addTo(map);
    });
    return;
}

function getRandomLatLng() {
    const bounds = map.getBounds();
    const southWest = bounds.getSouthWest();
    const northEast = bounds.getNorthEast();

    const randomLat = Math.random() * (northEast.lat - southWest.lat) + southWest.lat;
    const randomLng = Math.random() * (northEast.lng - southWest.lng) + southWest.lng;

    return [randomLat, randomLng];
}

function animateLine(line) {
    let step = 0;
    const deltaX = -0.00002 * windSpeedNow * Math.sin(windDirNow * Math.PI / 180);
    const deltaY = -0.00002 * windSpeedNow * Math.cos(windDirNow * Math.PI / 180);

    const interval = setInterval(() => {
      if (step >= 30) { // Increase the number of steps for smoother movement
        clearInterval(interval);
        map.removeLayer(line);
      } else {
        const newCoords = [
          [line._latlngs[0].lat + deltaY, line._latlngs[0].lng + deltaX],
          [line._latlngs[1].lat + deltaY, line._latlngs[1].lng + deltaX],
        ];
        line.setLatLngs(newCoords);
        step++;
      }
    }, 100); // Increase the interval duration
}

function createLines(count) {
    for (let i = 0; i < count; i++) {
      const startCoords = getRandomLatLng();
      const endCoords = [
        startCoords[0] - 0.00005 * windSpeedNow * Math.cos(windDirNow * Math.PI / 180),
        startCoords[1] - 0.00005 * windSpeedNow * Math.sin(windDirNow * Math.PI / 180)
      ];
      const line = L.polyline([startCoords, endCoords], { 
          color: 'gray', 
          weight: 1
      }).addTo(map);
      animateLine(line);
    }
}
