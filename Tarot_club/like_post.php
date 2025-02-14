<?php
session_start();
include 'db.php'; // เชื่อมต่อฐานข้อมูล

if (!isset($_SESSION['user_id'])) {
    echo "❌ กรุณาเข้าสู่ระบบก่อน";
    exit();
}

if (isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];

    // เพิ่มจำนวนไลค์ของโพสต์
    $query = "UPDATE posts SET likes = likes + 1 WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $post_id);
    
    if ($stmt->execute()) {
        // ดึงจำนวนไลค์ล่าสุด
        $result = $conn->query("SELECT likes FROM posts WHERE id = $post_id");
        $row = $result->fetch_assoc();
        echo $row['likes']; // ส่งค่าจำนวนไลค์กลับไป
    } else {
        echo "❌ ไม่สามารถกดไลค์ได้";
    }
}
?>
