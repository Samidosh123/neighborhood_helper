<?php 
require_once '../includes/db_connect.php';

//add category
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])){
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if(!empty($name)){
        $stmt=$conn->prepare("INSERT INTO categories(name,description) VALUES(?,?)");
        $stmt->bind_param("ss",$name,$description);

        if($stmt->execute()){
            $message="✅Category Added successfully";
        }else{
            $message="⚠️Error". $conn->error;
        }
        $stmt->close();
    }else{
           $message = "⚠️ Category name cannot be empty.";
    }
}
//deleting a category
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "🗑️ Category deleted successfully.";
    } else {
        $message = "⚠️ Error: " . $conn->error;
    }
    $stmt->close();
}
//fetching all the categories 
$result=$conn->query("SELECT*FROM categories ORDER BY id DESC");
?>

<div class="settings-section">
    <h2>Manage categories</h2>
    <?php if(isset($message))echo"<p class='msg'>$message</p>"; ?>

     <!-- Add Category Form -->
    <form method="POST" class="form-box">
        <label>Category Name:</label>
        <input type="text" name="name" required>

        <label>Description:</label>
        <textarea name="description"></textarea>

        <button type="submit" name="add_category">➕ Add Category</button>
    </form>

    <hr>

    <!-- Category List -->
    <h3>Existing Categories</h3>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Category Name</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td>
                <a href="system_settings.php?page=categories&delete=<?= $row['id'] ?>" 
                   onclick="return confirm('Are you sure you want to delete this category?')">🗑️ Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>