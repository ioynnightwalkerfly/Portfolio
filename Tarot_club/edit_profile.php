<?php
session_start();
include 'db.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];

    // ถ้ามีการอัปโหลดรูปโปรไฟล์ใหม่
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "profile_pictures/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file);

        $query = "UPDATE users SET username = ?, email = ?, profile_picture = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $new_username, $new_email, $_FILES["profile_picture"]["name"], $user_id);
    } else {
        $query = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $new_username, $new_email, $user_id);
    }

    if ($stmt->execute()) {
        echo "อัปเดตโปรไฟล์สำเร็จ! <a href='profile.php'>กลับไปที่โปรไฟล์</a>";
    } else {
        echo "เกิดข้อผิดพลาด: " . $conn->error;
    }
}

// ดึงข้อมูลผู้ใช้
$query = "SELECT username, email FROM users WHERE id = ?";
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
    <title>แก้ไขโปรไฟล์</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h1>แก้ไขโปรไฟล์</h1>
    <form method="POST" enctype="multipart/form-data">
        <label>ชื่อผู้ใช้:</label>
        <input type="text" name="username" value="<?php echo $user['username']; ?>" required><br>

        <label>อีเมล:</label>
        <input type="email" name="email" value="<?php echo $user['email']; ?>" required><br>

        <label>รูปโปรไฟล์ใหม่:</label>
        <input type="file" name="profile_picture"><br>

        <button type="submit">บันทึกการเปลี่ยนแปลง</button>
    </form>

</body>
</html>
