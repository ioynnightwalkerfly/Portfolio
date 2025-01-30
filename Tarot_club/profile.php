<?php
session_start();
include 'db.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$query = "SELECT username, email, profile_picture, points FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ของฉัน</title>
    <link rel="stylesheet" href="style.css"> <!-- ไฟล์ CSS -->
</head>
<body>

    <h1>โปรไฟล์ของฉัน</h1>
    <img src="profile_pictures/<?php echo $user['profile_picture']; ?>" width="150" alt="รูปโปรไฟล์">
    <p><strong>ชื่อผู้ใช้:</strong> <?php echo $user['username']; ?></p>
    <p><strong>อีเมล:</strong> <?php echo $user['email']; ?></p>
    <p><strong>แต้มสะสม:</strong> <?php echo $user['points']; ?> แต้ม</p>


    <a href="edit_profile.php">แก้ไขโปรไฟล์</a> |
    <a href="logout.php">ออกจากระบบ</a>

</body>
</html>
