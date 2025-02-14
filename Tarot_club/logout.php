<?php
session_start();

// ลบเซสชันคุกกี้
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// ทำลายเซสชันทั้งหมด
session_unset();
session_destroy();

// เปลี่ยนเส้นทางไปยังหน้า index.php
header("Location: auth_form.php");
exit();
?>
