<?php


//--------------------------------------------//
$servername = "localhost";
$username = "phpdiego";
$password = "password";
$database = "postscommentsphp";

//--------------------------------------------//
$conn = mysqli_connect($servername, $username, $password, $database);

$sql = "SELECT
            p.postId AS post_id,
            p.title AS post_title,
            p.content AS post_content,
            c.commentId AS comment_id,
            c.postId AS comment_post_id,
            c.commentText AS comment_text,
            c.userId AS comment_user_id
        FROM Posts p
        LEFT JOIN comments c ON p.postId = c.postId
        ORDER BY p.postId, c.commentId";

//--------------------------------------------//
$result = mysqli_query($conn, $sql);

$hierarchicalData = [];
while ($row = mysqli_fetch_assoc($result)) {
    $postId = $row['post_id'];

    if (!isset($hierarchicalData[$postId])) {
        $hierarchicalData[$postId] = [
            'id' => $row['post_id'],
            'title' => $row['post_title'],
            'content' => $row['post_content'],
            'comments' => []
        ];
    }

    if ($row['comment_id'] !== null) {
        $hierarchicalData[$postId]['comments'][] = [
            'id' => $row['comment_id'],
            'text' => $row['comment_text']
        ];
    }
}

//--------------------------------------------//
function renderList($items)
{
    echo "<ul>";
    foreach ($items as $item) {
        echo "<li>";
        if (isset($item['title'])) {
            echo "<strong>" . htmlspecialchars($item['title']) . "</strong>";
        }
        if (isset($item['content'])) {
            echo "<p>" . htmlspecialchars($item['content']) . "</p>";
        }
        if (isset($item['text'])) {
            echo "<p>" . htmlspecialchars($item['text']) . "</p>";
        }
        if (isset($item['comments']) && !empty($item['comments'])) {
            renderList($item['comments']);
        }
        echo "</li>";
    }
    echo "</ul>";
}
//--------------------------------------------//
?>

<!DOCTYPE html>
<html lang="lv">

<head>
    <meta charset="UTF-8">
    <title>Posts n such</title>
</head>

<body>
    <h1>Posts and comments</h1>
    <?php renderList($hierarchicalData); ?>
</body>

</html>

<?php
//--------------------------------------------//
mysqli_close($conn);

?>