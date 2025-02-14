<?php
session_start();
include 'db.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบว่ามีค่า post_id หรือไม่
if (!isset($_GET['post_id'])) {
    header("Location: index.php");
    exit();
}

$post_id = $_GET['post_id'];
$user_id = $_SESSION['user_id'];

// ดึงข้อมูลโพสต์ของเจ้าของโพสต์
$query = "SELECT * FROM posts WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $post_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

// ถ้าไม่มีโพสต์นี้หรือโพสต์ไม่ใช่ของเจ้าของ
if (!$post) {
    header("Location: index.php");
    exit();
}

// ถ้าผู้ใช้กดปุ่มอัปเดตโพสต์
if (isset($_POST['update'])) {
    $content = trim($_POST['content']);
    
    // อัปเดตโพสต์
    $update_query = "UPDATE posts SET content = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sii", $content, $post_id, $user_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "✅ แก้ไขโพสต์สำเร็จ!";
    } else {
        $_SESSION['message'] = "❌ ไม่สามารถแก้ไขโพสต์ได้!";
    }

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขโพสต์</title>
</head>
<body>
    <h2>แก้ไขโพสต์</h2>
    <form action="" method="POST">
        <textarea name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        <button type="submit" name="update">💾 บันทึก</button>
    </form>
    <a href="index.php">🔙 กลับไปหน้าหลัก</a>
</body>
</html>
