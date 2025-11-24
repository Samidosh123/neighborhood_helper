<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit();
}

$message = "";

// Function to get AI category ID
function categorizeIssue($title, $description) {
    $input_text = $title . " " . $description;
    $input_json = json_encode(["text" => $input_text]);

    // Path to Python predict script
    $cmd = "python C:\\xampp\\htdocs\\neighborhood_helper\\predict.py";

    $descriptorspec = [
        0 => ["pipe", "r"],  // stdin
        1 => ["pipe", "w"],  // stdout
        2 => ["pipe", "w"]   // stderr
    ];

    $process = proc_open($cmd, $descriptorspec, $pipes);

    if (is_resource($process)) {
        // Send input JSON
        fwrite($pipes[0], $input_json);
        fclose($pipes[0]);

        // Get output JSON
        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        // Get errors
        $errors = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        proc_close($process);

        if ($errors) {
            return null; // fallback if Python fails
        } else {
            $result = json_decode($output, true);
            return $result['category'] ?? null;
        }
    }
    return null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $estate_id = $_POST['estate_id'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $imagePath = null;

    // 🧠 AI predicts category
    $category_name = categorizeIssue($title, $description);

    // Map category name to category ID in your DB
    $category_id = null;
    if ($category_name) {
        $stmt_cat = $conn->prepare("SELECT id FROM categories WHERE name = ?");
        $stmt_cat->bind_param("s", $category_name);
        $stmt_cat->execute();
        $stmt_cat->bind_result($category_id);
        $stmt_cat->fetch();
        $stmt_cat->close();
    }

    // Fallback if AI fails
    if (!$category_id) {
        $category_id = 1; // default category ID
    }

    // 🖼️ Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = "uploads/" . $fileName;
            } else {
                $message = "⚠️ Error uploading file.";
            }
        } else {
            $message = "⚠️ Only JPG, JPEG, PNG files are accepted.";
        }
    }

    // 🧾 Insert into database
    $stmt = $conn->prepare("INSERT INTO issues (user_id, title, description, category_id, estate_id, image, latitude, longitude) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issiissd", $user_id, $title, $description, $category_id, $estate_id, $imagePath, $latitude, $longitude);

    if ($stmt->execute()) {
        $message = "✅ Issue reported successfully (AI Category: $category_name)";
    } else {
        $message = "❌ ERROR: " . $conn->error;
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Issue</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <style>
        #map { height: 300px; margin: 10px 0; border-radius: 8px; }
    </style>
</head>
<body class="report-issue">
<div class="report-slideshow">
    <div class="slide fade"><img src="../images/bg5.jpg" alt="Background 1"></div>
    <div class="slide fade"><img src="../images/bg6.jpg" alt="Background 2"></div>
    <div class="slide fade"><img src="../images/bg7.jpg" alt="Background 3"></div>
</div>

<div class="container">
    <div class="form-card">
        <h2>🚩 Report an Issue</h2>

        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Issue Title" required>
            <textarea name="description" placeholder="Describe the issue" required></textarea>

            <!-- Estate Dropdown -->
            <select name="estate_id" required>
                <option value="">-- Select Estate --</option>
                <?php
                $res = $conn->query("SELECT * FROM estates ORDER BY name ASC");
                while ($est = $res->fetch_assoc()) {
                    echo "<option value='{$est['id']}'>{$est['name']} ({$est['location']})</option>";
                }
                ?>
            </select>

            <input type="file" name="image" accept="image/*">

            <h3>📍 Select Location</h3>
            <div id="map"></div>
            <input type="hidden" name="latitude" id="latitude" required>
            <input type="hidden" name="longitude" id="longitude" required>

            <button type="submit">Submit Issue</button>
        </form>

        <?php
        $backLink = "../pages/user_dashboard.php";
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            $backLink = "../admin/dashboard.php";
        }
        ?>
        <a href="<?php echo $backLink; ?>" class="back-link">⬅ Back to Home</a>
    </div>
</div>

<script>
    let slideIndex = 0;
    showSlides();
    function showSlides() {
        let slides = document.querySelectorAll(".report-slideshow .slide");
        slides.forEach(slide => slide.style.display = "none");
        slideIndex++;
        if (slideIndex > slides.length) { slideIndex = 1 }
        slides[slideIndex - 1].style.display = "block";
        setTimeout(showSlides, 5000);
    }
</script>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([-1.2921, 36.8219], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    var marker;
    function onMapClick(e) {
        if (marker) {
            map.removeLayer(marker);
        }
        marker = L.marker(e.latlng).addTo(map);
        document.getElementById("latitude").value = e.latlng.lat;
        document.getElementById("longitude").value = e.latlng.lng;
    }
    map.on('click', onMapClick);
</script>
</body>
</html>
