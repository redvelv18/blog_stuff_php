<?php

print("testing");


// 1. Datubāzes konfigurācija
$servername = "localhost"; // Nomainiet ar savu servera nosaukumu
$username = "phpdiego"; // Nomainiet ar savu datubāzes lietotājvārdu
$password = "password"; // Nomainiet ar savu datubāzes paroli
$database = "postscommentsphp"; // Nomainiet ar savu datubāzes nosaukumu

// 2. Datubāzes savienojuma izveide
$conn = mysqli_connect($servername, $username, $password, $database);

// Pārbaudīt savienojumu
if (!$conn) {
    die("Savienojums ar datubāzi neizdevās: " . mysqli_connect_error());
}

// 3. Datu izgūšanas vaicājuma izveide
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

// 4. Vaicājuma izpilde
$result = mysqli_query($conn, $sql);

// Pārbaudīt, vai vaicājums ir veiksmīgs
if (!$result) {
    die("Kļūda veicot vaicājumu: " . mysqli_error($conn));
}

// 5. Datu apstrāde un hierarhiskas struktūras veidošana
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

// 6. Hierarhiskā masīva attēlošana HTML sarakstā
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

?>

<!DOCTYPE html>
<html lang="lv">

<head>
    <meta charset="UTF-8">
    <title>Ziņas un Komentāri</title>
</head>

<body>
    <h1>Ziņas ar Komentāriem</h1>
    <?php renderList($hierarchicalData); ?>
</body>

</html>

<?php

// 7. Datubāzes savienojuma aizvēršana
mysqli_close($conn);

?>