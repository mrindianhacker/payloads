
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get original filename
    $fileName = $_FILES['file']['name'];
    $fileTmp  = $_FILES['file']['tmp_name'];

    // Prevent overwrite: add timestamp
    $newName = time() . "_" . basename($fileName);

    // Upload to same folder
    $target = __DIR__ . '/' . $newName;

    if (move_uploaded_file($fileTmp, $target)) {
        echo "File uploaded successfully: " . htmlspecialchars($newName);
    } else {
        echo "Upload failed.";
    }
}
?>

<!DOCTYPE html>
<html>
<body>

<h2>Upload Any File</h2>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <button type="submit">Upload File</button>
</form>

</body>
</html>
