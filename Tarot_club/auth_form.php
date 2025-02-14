<?php
session_start();
include 'db.php'; // เชื่อมต่อฐานข้อมูล

$message = "";
$messageType = ""; // ✅ เพิ่มค่าเริ่มต้น


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
        $message = "❌ อีเมลหรือชื่อผู้ใช้มีอยู่แล้ว!";
        $messageType = "error";  // ✅ เพิ่ม
    } else {
        // เพิ่มข้อมูลลงในฐานข้อมูล
        $query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $username, $email, $password);
        if ($stmt->execute()) {
            $message = "✅ สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ";
            $messageType = "success";  // ✅ เพิ่ม
        } else {
            $message = "❌ เกิดข้อผิดพลาด!";

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

            // ✅ เปลี่ยนหน้าไป `index.php`
            header("Location: index.php");
            exit();
        } else {
            $message = "❌ รหัสผ่านไม่ถูกต้อง!";
        }
    } else {
        $message = "❌ ไม่พบชื่อผู้ใช้นี้!";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก & ล็อกอิน</title>
    <link rel="stylesheet" href="auth_style.css">
</head>
<body>
    <div class="form-structor">
        <!-- สมัครสมาชิก -->
        <div class="signup">
            <h2 class="form-title" id="signup"><span>or</span>Sign up</h2>
            <div class="form-holder">
                <form method="POST" action="">
                    <input type="text" class="input" name="username" placeholder="Username" required />
                    <input type="email" class="input" name="email" placeholder="Email" required />
                    <input type="password" class="input" name="password" placeholder="Password" required />
                    <button class="submit-btn" type="submit" name="signup">Sign up</button>
                </form>
            </div>
        </div>

        <!-- ล็อกอิน -->
        <div class="login slide-up">
            <div class="center">
                <h2 class="form-title" id="login"><span>or</span>Log in</h2>
                <div class="form-holder">
                    <form method="POST" action="">
                        <input type="text" class="input" name="username" placeholder="Username" required />
                        <input type="password" class="input" name="password" placeholder="Password" required />
                        <button class="submit-btn" type="submit" name="login">Log in</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<!-- ✅ แสดง popup ถ้ามีข้อความแจ้งเตือน -->
<?php if (!empty($message)): ?>
    <div id="popup-message" class="popup <?php echo isset($messageType) ? $messageType : ''; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>


    <script>
        console.clear();
        const loginBtn = document.getElementById('login');
        const signupBtn = document.getElementById('signup');

        loginBtn.addEventListener('click', (e) => {
            let parent = e.target.parentNode.parentNode;
            if (!parent.classList.contains("slide-up")) {
                parent.classList.add('slide-up');
            } else {
                signupBtn.parentNode.classList.add('slide-up');
                parent.classList.remove('slide-up');
            }
        });

        signupBtn.addEventListener('click', (e) => {
            let parent = e.target.parentNode;
            if (!parent.classList.contains("slide-up")) {
                parent.classList.add('slide-up');
            } else {
                loginBtn.parentNode.parentNode.classList.add('slide-up');
                parent.classList.remove('slide-up');
            }
        });

    
    </script>

    <script>
     document.addEventListener("DOMContentLoaded", function () {
        let popup = document.getElementById("popup-message");
        if (popup) {
            setTimeout(function () {
                popup.classList.add("hide"); // ✅ ซ่อน popup หลัง 3 วิ
            }, 3000);
        }
    });
</script>

</body>
</html>
