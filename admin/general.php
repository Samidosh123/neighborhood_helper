<?PHP 
require_once "../includes/db_connect.php"; 
$settings=[];
$res = $conn->query("SELECT * FROM general_settings");
while($row=$res->fetch_assoc()){
     $settings[$row['setting_key']] = $row['setting_value'];
}
// Update settings
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // Handle text fields
    foreach($_POST as $key => $value){
        if($key != "system_logo"){ // skip logo, it's handled separately
            $stmt = $conn->prepare("REPLACE INTO general_settings (setting_key, setting_value) VALUES (?, ?)");
            $stmt->bind_param("ss", $key, $value);
            $stmt->execute();
        }
    }

    // Handle logo upload
    if(isset($_FILES['system_logo']) && $_FILES['system_logo']['error'] == 0){
        $targetDir = "../uploads/"; 
        if(!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES["system_logo"]["name"]);
        $targetFile = $targetDir . $fileName;

        if(move_uploaded_file($_FILES["system_logo"]["tmp_name"], $targetFile)){
            $stmt = $conn->prepare("REPLACE INTO general_settings (setting_key, setting_value) VALUES ('system_logo', ?)");
            $stmt->bind_param("s", $fileName);
            $stmt->execute();
        }
    }

    echo "<p class='msg'>✅ Settings updated successfully.</p>";
}
?>

<form method="POST" enctype="multipart/form-data" class="form-box">
    <label>System Name:</label>
    <input type="text" name="system_name" value="<?= htmlspecialchars($settings['system_name'] ?? '') ?>">

    <label>Contact Email:</label>
    <input type="email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>">

    <label>Default City:</label>
    <input type="text" name="default_city" value="<?= htmlspecialchars($settings['default_city'] ?? '') ?>">

    <label>System Logo:</label>
    <input type="file" name="system_logo">
    <?php if(!empty($settings['system_logo'])): ?>
        <div style="margin-top:10px;">
            <img src="../uploads/<?= $settings['system_logo'] ?>" alt="Logo" width="120">
        </div>
    <?php endif; ?>

    <button type="submit">💾 Save Settings</button>
</form>