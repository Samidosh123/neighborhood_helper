<?php
function categorizeIssue($title, $description) {
    global $conn; // use existing DB connection
    $text = strtolower($title . ' ' . $description);
// add classification dataset :
    // Step 1: Predict category name based on keywords
    if (preg_match('/light|lamp|dark|bulb|streetlight|power/', $text)) {
        $categoryName = 'Streetlight';
    } elseif (preg_match('/water|pipe|leak|sewage|flood|tap/', $text)) {
        $categoryName = 'Water Leakage';
    } elseif (preg_match('/trash|garbage|dump|waste|litter/', $text)) {
        $categoryName = 'Garbage';
    } elseif (preg_match('/road|pothole|tarmac|path/', $text)) {
        $categoryName = 'Road Issue';
    } elseif (preg_match('/noise|crime|theft|security|fight|murder|rape|killer/', $text)) {
        $categoryName = 'Security';
    } elseif (preg_match('/school|class|teacher|education/', $text)) {
        $categoryName = 'Education';
    } else {
        $categoryName = 'Other';
    }

    // Step 2: Get the category ID from the database
    $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ? LIMIT 1");
    $stmt->bind_param("s", $categoryName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['id']; // return the numeric ID
    } else {
        return null; // if category not found
    }
}
?>
