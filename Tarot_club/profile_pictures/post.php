<?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$post_id = $_GET['id'];

// ดึงข้อมูลโพสต์
$query = "SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

// ดึงคอมเมนต์
$comment_query = "SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = ? ORDER BY created_at ASC";
$comment_stmt = $conn->prepare($comment_query);
$comment_stmt->bind_param("i", $post_id);
$comment_stmt->execute();
$comments = $comment_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $post['title']; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

  <h1><?php echo $post['title']; ?></h1>
<p>โดย <strong><?php echo $post['username']; ?></strong> | <?php echo $post['created_at']; ?></p>
<p><?php echo nl2br($post['content']); ?></p>

<?php if ($post['image']): ?>
    <img src="post_images/<?php echo $post['image']; ?>" width="300">
<?php endif; ?>

<!-- ⭐ ปุ่มให้คะแนน -->
<form method="POST" action="rate_post.php">
    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
    <button type="submit">⭐ ให้คะแนนโพสต์นี้</button>
</form>
<p>คะแนนทั้งหมด: <?php echo $post['points']; ?> ⭐</p>

<h2>คอมเมนต์</h2>
<?php while ($comment = $comments->fetch_assoc()): ?>
    <p><strong><?php echo $comment['username']; ?></strong>: <?php echo $comment['comment']; ?></p>
<?php endwhile; ?>

<form method="POST" action="comment.php">
    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
    <textarea name="comment" required></textarea><br>
    <button type="submit">แสดงความคิดเห็น</button>
</form>



</body>
</html>
