<?php 
require_once '../includes/db_connect.php';

$sql = "SELECT issues.id, issues.title, issues.description, 
               categories.name AS category, 
               issues.status, estates.name AS estate, 
               issues.latitude, issues.longitude, issues.created_at
        FROM issues
        LEFT JOIN categories ON issues.category_id = categories.id
        LEFT JOIN estates ON issues.estate_id = estates.id
        WHERE issues.latitude IS NOT NULL AND issues.longitude IS NOT NULL";


$result= $conn->query($sql);

$issues =[];
while($row = $result->fetch_assoc()){
    $issues[]=$row;
}

header('content-type: application/json');
echo json_encode($issues);