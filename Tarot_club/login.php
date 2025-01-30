<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        echo "เข้าสู่ระบบสำเร็จ! <a href='profile.php'>ไปที่โปรไฟล์</a>";
    } else {
        echo "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
}
?>
<form method="POST">
    <input type="text" name="username" placeholder="ชื่อผู้ใช้" required><br>
    <input type="password" name="password" placeholder="รหัสผ่าน" required><br>
    <button type="submit">เข้าสู่ระบบ</button>
</form>
