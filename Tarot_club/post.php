<?php
session_start();
include 'db.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    die("❌ กรุณาเข้าสู่ระบบก่อนโพสต์!");
}

// ตรวจสอบว่ามีการส่งโพสต์หรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $content = trim($_POST['content']);
    $image = '';

    // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
    if (!empty($_FILES['image']['name'])) {
        $image = time() . "_" . basename($_FILES['image']['name']);
        $target_dir = "uploads/";
        $target_file = $target_dir . $image;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            die("❌ อัปโหลดรูปภาพไม่สำเร็จ!");
        }
    }

    // ✅ เพิ่มโพสต์ลงฐานข้อมูล
    $query = "INSERT INTO posts (user_id, content, image, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $user_id, $content, $image);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "✅ โพสต์สำเร็จ!";
    } else {
        $_SESSION['message'] = "❌ เกิดข้อผิดพลาดในการโพสต์!";
    }

    header("Location: index.php"); // กลับไปหน้า index
    exit();
} else {
    die("❌ ไม่ได้รับข้อมูลโพสต์!");
}
?>
