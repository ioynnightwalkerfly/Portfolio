<?php
session_start();
include 'db.php';

// ดึงโพสต์ทั้งหมดจากฐานข้อมูล
$query = "SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก</title>
    <link rel="stylesheet" href="style.css">
    
<a href="profile.php">โปรไฟล์ของฉัน</a>
</head>
<body>

    <h1>โพสต์ทั้งหมด</h1>
    <a href="create_post.php">+ สร้างโพสต์ใหม่</a>

    <?php while ($post = $result->fetch_assoc()): ?>
        <div class="post">
            <h2><?php echo $post['title']; ?></h2>
            <p>โดย <strong><?php echo $post['username']; ?></strong> | <?php echo $post['created_at']; ?></p>
            <p><?php echo nl2br($post['content']); ?></p>
            <?php if ($post['image']): ?>
                <img src="post_images/<?php echo $post['image']; ?>" width="300">
            <?php endif; ?>
            <a href="post.php?id=<?php echo $post['id']; ?>">อ่านเพิ่มเติม & คอมเมนต์</a>
        </div>
    <?php endwhile; ?>

</body>
</html>

