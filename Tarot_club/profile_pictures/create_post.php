<?php
session_start();
include 'db.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    $image = NULL;

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "post_images/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image = $_FILES["image"]["name"];
    }

    $query = "INSERT INTO posts (user_id, title, content, image) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isss", $user_id, $title, $content, $image);

    if ($stmt->execute()) {
        echo "โพสต์สำเร็จ! <a href='index.php'>กลับไปหน้าแรก</a>";
    } else {
        echo "เกิดข้อผิดพลาด: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สร้างโพสต์ใหม่</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>สร้างโพสต์ใหม่</h1>
    <form method="POST" enctype="multipart/form-data">
        <label>หัวข้อโพสต์:</label>
        <input type="text" name="title" required><br>

        <label>เนื้อหาโพสต์:</label>
        <textarea name="content" required></textarea><br>

        <label>อัปโหลดรูปภาพ (ถ้ามี):</label>
        <input type="file" name="image"><br>

        <button type="submit">โพสต์</button>
    </form>
</body>
</html>
