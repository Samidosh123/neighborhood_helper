<?php 
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../pages/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Admin Issue Map</title>
        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
          <style>
        #map { height: 600px; width: 100%; margin-top: 10px; }
        .filter-bar {
            margin: 10px 0;
            padding: 10px;
            background: #f4f6f9;
            border-radius: 8px;
        }
        select, button {
            padding: 6px;
            margin-right: 8px;
        }
    </style>
    </head>
    <body>
        <h2>Admin Issue Map</h2>
        <p>view and manage issues reported in the community.</p>
            <!-- Filter Controls -->
    <div class="filter-bar">
        <label>Category:</label>
        <select id="categoryFilter">
            <option value="">All</option>
            <option value="Waste">Waste</option>
            <option value="Lighting">Lighting</option>
            <option value="Roads">Roads</option>
        </select>

        <label>Status:</label>
        <select id="statusFilter">
            <option value="">All</option>
            <option value="Pending">Pending</option>
            <option value="In Progress">In Progress</option>
            <option value="Resolved">Resolved</option>
        </select>

        <button onclick="loadIssues()">Apply Filters</button>
    </div>

    <!-- Map Container -->
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
    var map = L.map('map').setView([-1.2921, 36.8219], 12); // Nairobi

    // Add tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var markersLayer = L.layerGroup().addTo(map);

    function getMarkerColor(status) {
        if (status === "Resolved") return "green";
        if (status === "In Progress") return "orange";
        return "red"; // Pending
    }

    function loadIssues() {
        markersLayer.clearLayers();

        let category = document.getElementById("categoryFilter").value;
        let status = document.getElementById("statusFilter").value;

        fetch('../pages/get_issues.php')
            .then(res => res.json())
            .then(data => {
                data.forEach(issue => {
                    if(issue.latitude && issue.longitude) {
                        // Filtering
                        if (category && issue.category !== category) return;
                        if (status && issue.status !== status) return;

                        var marker = L.circleMarker([issue.latitude, issue.longitude], {
                            radius: 8,
                            color: getMarkerColor(issue.status),
                            fillColor: getMarkerColor(issue.status),
                            fillOpacity: 0.8
                        }).addTo(markersLayer);

                        marker.bindPopup(`
                            <b>${issue.title}</b><br>
                            ${issue.description}<br>
                            <i>Status: ${issue.status}</i><br>
                            <small>Category: ${issue.category}</small><br>
                            <small>Reported on: ${issue.created_at}</small>
                        `);
                    }
                });
            });
    }

    
    loadIssues();
    </script>
    </body>
</html>