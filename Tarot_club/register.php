<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        echo "สมัครสมาชิกสำเร็จ! <a href='login.php'>เข้าสู่ระบบ</a>";
    } else {
        echo "เกิดข้อผิดพลาด: " . $conn->error;
    }
}
?>
<form method="POST">
    <input type="text" name="username" placeholder="ชื่อผู้ใช้" required><br>
    <input type="email" name="email" placeholder="อีเมล" required><br>
    <input type="password" name="password" placeholder="รหัสผ่าน" required><br>
    <button type="submit">สมัครสมาชิก</button>
</form>
