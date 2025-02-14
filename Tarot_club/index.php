<?php
session_start();
include 'db.php'; // เชื่อมฐานข้อมูล

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 🔹 ดึงข้อมูลผู้ใช้
$query_user = "SELECT username, profile_picture FROM users WHERE id = ?";
$stmt_user = $conn->prepare($query_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

// กำหนดค่าให้ $_SESSION ถ้ายังไม่มี
$_SESSION['username'] = $user['username'];
$_SESSION['profile_picture'] = !empty($user['profile_picture']) ? $user['profile_picture'] : 'default_profile.png';

// 🔹 ดึงโพสต์จากฐานข้อมูล
$query_posts = "SELECT posts.*, users.username, users.profile_picture 
FROM posts 
JOIN users ON posts.user_id = users.id 
ORDER BY posts.created_at DESC";
$result_posts = $conn->query($query_posts);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้า Feed - Tarot Club</title>
    <link rel="stylesheet" href="css/feed_style.css">
    <script src="js/feed_script.js" defer></script>
</head>
<body>

    <!-- 🏠 Header -->
    <header>
        <div class="logo">Tarot Club</div>
        <input type="text" class="search-bar" placeholder="🔍 ค้นหา...">
        <div class="user-info">
            <img src="images/<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" class="profile-pic">
            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="logout-btn">ออกจากระบบ</a>
        </div>
    </header>

    <div class="container">
        <!-- 📌 Sidebar เมนูลัด -->
        <aside class="sidebar">
            <ul>
                <li><a href="index.php">🏠 หน้าหลัก</a></li>
                <li><a href="profile.php">👤 โปรไฟล์</a></li>
                <li><a href="friends.php">🤝 รายชื่อเพื่อน</a></li>
                <li><a href="chat.php">💬 แชท</a></li>
                <li><a href="logout.php">🚪 ออกจากระบบ</a></li>
            </ul>
        </aside>

        <main class="feed">
            <div class="post-box">
                <form action="post.php" method="POST" enctype="multipart/form-data">
                    <textarea name="content" placeholder="คิดอะไรอยู่...?" required></textarea>
                    <input type="file" name="image" accept="image/*">
                    <button type="submit">📢 โพสต์</button>
                </form>
            </div>

            <?php while ($post = $result_posts->fetch_assoc()) { ?>
                <div class="post">
                    <div class="post-header">
                        <img src="images/<?php echo htmlspecialchars($post['profile_picture'] ?? 'default_profile.png'); ?>" class="post-profile-pic">
                        <span class="post-user"><?php echo htmlspecialchars($post['username']); ?></span>
                    </div>

                    <p><?php echo htmlspecialchars($post['content']); ?></p>

                    <?php if (!empty($post['image'])) { ?>
                        <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" class="post-image">
                    <?php } ?>

                    <div class="post-actions">
                        <button class="like-btn" data-post-id="<?php echo $post['id']; ?>">❤️ ถูกใจ</button>
                        <span class="points">⭐ <span id="likes-<?php echo $post['id']; ?>"><?php echo $post['likes']; ?></span> คะแนน</span>
                    </div>


                    <!-- ✅ ปุ่มแก้ไขและลบ (แสดงเฉพาะเจ้าของโพสต์) -->
                    <?php if ($post['user_id'] == $_SESSION['user_id']) { ?>
                        <div class="post-edit-actions">
                            <a href="edit_post.php?post_id=<?php echo $post['id']; ?>" class="edit-btn">✏️ แก้ไข</a>
                            <form action="delete_post.php" method="POST" onsubmit="return confirm('คุณต้องการลบโพสต์นี้ใช่ไหม?');">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <button type="submit" class="delete-btn">🗑️ ลบ</button>
                            </form>
                        </div>
                    <?php } ?>
                </div> 
            <?php } ?>

        </main>
    </div>

</body>
</html>
