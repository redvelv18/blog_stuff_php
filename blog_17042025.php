<?php


//--------------------------------------------//
$servername = "localhost"; 
$username = "phpdiego"; 
$password = "password"; 
$database = "postscommentsphp"; 

//--------------------------------------------//
$conn = mysqli_connect($servername, $username, $password, $database);

$sql = "SELECT
            p.PostID AS post_id,
            p.Title AS post_title,
            p.Content AS post_content,
            c.commentID AS comment_id,
            c.PostID AS comment_post_id,
            c.comment_text AS comment_text,
            c.userID AS comment_user_id
        FROM Posts p
        LEFT JOIN comments c ON p.PostID = c.PostID
        ORDER BY p.PostID, c.commentID";

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
            'author' => $row['comment_author'],
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
        echo "<strong>" . htmlspecialchars($item['title'] ?? $item['author']) . "</strong>";
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
    <title>Ziņas un Komentāri</title>
</head>

<body>
    <h1>Ziņas</h1>
    <?php renderList($hierarchicalData); ?>
</body>

</html>

<?php
//--------------------------------------------//
mysqli_close($conn);

?>