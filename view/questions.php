<?php
require_once "common.php";
require_once "connection.php";

$session = new Session();

$db = new Database();

$select = "SELECT q.id, q.thread, q.message, q.image, q.created, ur.username";
$filters = "isActive = :isActive";
$filterValues = ["isActive" => 1];
$page = 1;
$limit = 5;
$offset = 0;
$tagID = "";

if(isGet()) {
    $data = getBody();
    if (isset($data)) {
        if (isset($data["tagID"]) && filter_var($data["tagID"], FILTER_VALIDATE_INT)) {
            $filters .= " AND "."tag = :tag";
            $filterValues["tag"] = $data["tagID"];
            $tagID = $data["tagID"];
        }
        if (isset($data["page"]) && filter_var($data["page"], FILTER_VALIDATE_INT)) {
            $page = $data["page"];
        }
    }
}

$sql = "";
$sql .= $select." "."FROM question q JOIN user ur ON ur.id = q.user";
$sql = !empty($filters) ? $sql." WHERE ".$filters : $sql;
$sql .= " "."ORDER BY created DESC";

$countSQL = !empty($filters) ? "SELECT count(id) FROM question WHERE $filters" : "SELECT count(id) FROM question";
$countStatement = $db->prepare($countSQL);
foreach($filterValues as $key => $val) {
    $countStatement->bindValue(":$key", $val);
}
$countStatement->execute();
$count = $countStatement->fetchColumn();

$totalPage = ceil($count / $limit);
$offset = ($page - 1) * $limit;

$sql .= " "."LIMIT $limit OFFSET $offset";

$statement = $db->prepare($sql);
foreach($filterValues as $key => $val) {
    $statement->bindValue(":$key", $val);
}

$statement->execute();
$questions = $statement->fetchAll();

$sql = "SELECT * FROM tags";
$tagsQuery = $db->prepare($sql);
$tagsQuery->execute();
$tags = $tagsQuery->fetchAll();
$currentPage = $page;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questions - My Stack Overflow-like Website</title>
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
            <h2>Questions</h2>
            <select 
                class="form-select" 
                class="form-select" 
                aria-label="Default select example" 
                name="tagID"
                onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);"
            >
                <option value="/questions.php" <?=$tagID ? 'selected' : ''?> selected>All Modules</option>
                <?php foreach($tags as $tag): ?>
                    <option value="/questions.php?tagID=<?=$tag['id']?>" <?=$tagID == $tag['id'] ? 'selected' : ''?>><?=$tag['name']?></option>
                <?php endforeach ?>
            </select>
            <ul class="question-list">
                <?php if($questions): ?>
                    <?php foreach($questions as $question): ?>
                        <li>
                            <h3><a href="examplequestion.php?id=<?=$question["id"]?>"><?=$question["thread"]?></a></h3>
                            <?php if($question["image"]): ?>
                                    <img src="<?=$question["image"]?>" alt="Image Picture" class="img-thumbnail bg-transparent" width="204" height="192">
                            <?php endif ?>
                            <p>Posted by: <?=$question["username"]?> | Date: <?=$question["created"]?></p>
                            <p><?=$question["message"]?></p>
                        </li>
                    <?php endforeach ?>
                    <?php if($totalPage > 1): ?>
                        <p class="mt-4">
                            Total questions: <?=count($questions)?> <?=$totalPage > 1 ? "| Page:" : ""?> 
                            <?php for($page = 0; $page < $totalPage; ++$page): ?>
                                <a
                                    href="/questions.php?page=<?=$page+1?>" 
                                    class="text-decoration-none <?=$currentPage == $page+1 ? 'text-dark' : ''?>">
                                    <?=$page+1?>
                                </a>
                            <?php endfor ?>
                        </p>
                    <?php endif ?>
                <?php else: ?>
                    <p>No Data Available.</p>
                <?php endif?>
            </ul>
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
