<?php
$servername = "localhost"; // เปลี่ยนเป็นค่าที่คุณได้รับ
$username = "root"; // ใส่ Username ที่ได้รับ
$password = ""; // ใส่ Password ที่ได้รับ
$dbname = "trl_db"; // ใส่ชื่อ Database

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
