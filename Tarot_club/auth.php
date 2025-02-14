<?php
session_start();
include 'db.php'; // เชื่อมฐานข้อมูล

// ✅ ถ้าผู้ใช้กดปุ่มสมัครสมาชิก
if (isset($_POST['signup'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // เข้ารหัสรหัสผ่าน

    // ตรวจสอบว่ามีอีเมลหรือชื่อผู้ใช้ซ้ำหรือไม่
    $check_query = "SELECT * FROM users WHERE email = ? OR username = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['message'] = "❌ อีเมลหรือชื่อผู้ใช้มีอยู่แล้ว!";
        $_SESSION['messageType'] = "error";
    } else {
        // เพิ่มข้อมูลลงในฐานข้อมูล
        $query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $username, $email, $password);
        if ($stmt->execute()) {
            $_SESSION['message'] = "✅ สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ";
            $_SESSION['messageType'] = "success";
            header("Location: login.php"); // ไปหน้า login
            exit();
        } else {
            $_SESSION['message'] = "❌ เกิดข้อผิดพลาด!";
            $_SESSION['messageType'] = "error";
        }
    }
}

// ✅ ถ้าผู้ใช้กดปุ่มล็อกอิน
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $query = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // ตรวจสอบรหัสผ่าน
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header("Location: index.php"); // ✅ ไปหน้า index.php
            exit();
        } else {
            $_SESSION['message'] = "❌ รหัสผ่านไม่ถูกต้อง!";
            $_SESSION['messageType'] = "error";
        }
    } else {
        $_SESSION['message'] = "❌ ไม่พบชื่อผู้ใช้นี้!";
        $_SESSION['messageType'] = "error";
    }
}
?>
