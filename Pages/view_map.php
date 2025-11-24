<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Issue Map</title>
        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
        <style>
            #map{
                height: 600px;
                width: 100%;
            }
        </style>
    </head>
    <body>
        <h2>Community Issue Map</h2>
<div id="map"></div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
// Initialize map
var map = L.map('map').setView([-1.2921, 36.8219], 12); // Nairobi default

// Add OpenStreetMap tiles
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// Fetch issues from PHP
fetch('get_issues.php')
  .then(res => res.json())
  .then(data => {
    data.forEach(issue => {
      if(issue.latitude && issue.longitude){
        var marker = L.marker([issue.latitude, issue.longitude]).addTo(map);
        marker.bindPopup(`
          <b>${issue.title}</b><br>
          ${issue.description}<br>
          <i>Status: ${issue.status}</i><br>
          <small>Category: ${issue.category}</small>
        `);
      }
    });
  });
</script>

    </body>
</html>