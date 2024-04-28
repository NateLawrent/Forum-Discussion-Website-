<?php
require_once "common.php";
require_once "connection.php";

$session = new Session();

$db = new Database();
$page = 1;
$limit = 5;
$offset = 0;

if(isGet()) {
    $data = getBody();
    if (isset($data)) {
        if (isset($data["page"]) && filter_var($data["page"], FILTER_VALIDATE_INT)) {
            $page = $data["page"];
        }
    }
}

$sql = "SELECT * FROM tags";

$countSQL = "SELECT count(id) FROM tags";
$countStatement = $db->prepare($countSQL);
$countStatement->execute();
$count = $countStatement->fetchColumn();

$totalPage = ceil($count / $limit);
$offset = ($page - 1) * $limit;

$sql .= " "."LIMIT $limit OFFSET $offset";

$statement = $db->prepare($sql);
$statement->execute();
$tags = $statement->fetchAll();
$currentPage = $page;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tags - My Stack Overflow-like Website</title>
    <link rel="stylesheet" href="/style/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>

    <header>
        <img src="/images/huylogo.png" alt="Logo" class="logo">
        <h1>Huy's Forum Discussion community</h1>
    </header>

    <nav class="nav-center">
        <a href="index.php">Home</a>
        <a href="questions.php">Questions</a>
        <a href="tags.php">Tags</a>
        <a href="users.php">Users</a>
        <?php if($session->get("user")): ?>
            <a href="askquestion.php">Ask Question</a>
        <?php endif ?>
        <a href="contactus.php">Contact Us</a>
        <?php if($session->get("isAdmin")): ?>
            <a href="admin.php">Admin</a>
        <?php endif ?>
        <?php if($session->get("user")): ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="signin.php">Login</a>
        <?php endif ?>
    </nav>

    <div class="container">
        <section class="main-content">
            <h2>Tags</h2>
            <?php if($tags): ?>
            <ul class="tag-list">
                <?php foreach($tags as $tag): ?>
                    <li><a href="questions.php?tagID=<?=$tag['id']?>"><?=$tag["name"]?></a></li>
                <?php endforeach ?>
            </ul>
            <?php if($totalPage > 1): ?>
                <p class="mt-4">
                    Total tags: <?=count($tags)?> <?=$totalPage > 1 ? "| Page:" : ""?> 
                    <?php for($page = 0; $page < $totalPage; ++$page): ?>
                        <a
                            href="/tags.php?page=<?=$page+1?>" 
                            class="text-decoration-none <?=$currentPage == $page+1 ? 'text-dark' : ''?>">
                            <?=$page+1?>
                        </a>
                    <?php endfor ?>
                </p>
                <?php endif ?>
            <?php else: ?>
                <p>No data available.</p>
            <?php endif ?>
        </section>

        <aside class="sidebar">
            <h2>Most Asked Questions</h2>
            <ul>
                <li><a href="#">How to create a responsive layout?</a></li>
                <li><a href="#">Best practices for web development?</a></li>
                <li><a href="#">Introduction to HTML and CSS</a></li>
            </ul>
        </aside>
    </div>

    <footer>
        <p>&copy; Huy's Forum Discussion community. All rights reserved.</p>
    </footer>

</body>
</html>
