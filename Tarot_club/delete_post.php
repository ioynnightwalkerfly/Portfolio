<?php
session_start();
include 'db.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบว่ามีค่า post_id หรือไม่
if (isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    // ตรวจสอบว่าโพสต์เป็นของเจ้าของโพสต์จริงหรือไม่
    $query = "DELETE FROM posts WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $post_id, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "✅ ลบโพสต์สำเร็จ!";
    } else {
        $_SESSION['message'] = "❌ ลบโพสต์ไม่สำเร็จ!";
    }
}

header("Location: index.php"); // กลับไปหน้า feed
exit();
