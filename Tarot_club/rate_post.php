<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "กรุณาเข้าสู่ระบบก่อนให้คะแนน!";
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'];

// ตรวจสอบว่าผู้ใช้เคยให้คะแนนโพสต์นี้หรือยัง
$check_query = "SELECT * FROM ratings WHERE user_id = ? AND post_id = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("ii", $user_id, $post_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo "คุณให้คะแนนโพสต์นี้ไปแล้ว!";
    exit();
}

// เพิ่มคะแนนให้โพสต์
$update_post = "UPDATE posts SET points = points + 1 WHERE id = ?";
$update_stmt = $conn->prepare($update_post);
$update_stmt->bind_param("i", $post_id);
$update_stmt->execute();

// เพิ่มแต้มให้เจ้าของโพสต์
$get_post_owner = "SELECT user_id FROM posts WHERE id = ?";
$stmt = $conn->prepare($get_post_owner);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$post_owner_id = $row['user_id'];

$update_user = "UPDATE users SET points = points + 5 WHERE id = ?";
$update_stmt = $conn->prepare($update_user);
$update_stmt->bind_param("i", $post_owner_id);
$update_stmt->execute();

// บันทึกการให้คะแนน
$insert_rating = "INSERT INTO ratings (user_id, post_id) VALUES (?, ?)";
$insert_stmt = $conn->prepare($insert_rating);
$insert_stmt->bind_param("ii", $user_id, $post_id);
$insert_stmt->execute();

echo "คุณให้คะแนนโพสต์นี้เ
