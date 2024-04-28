<?php
require_once("common.php");
require_once("connection.php");


$session = new Session();
$db = new Database();

$limit = 5;
$page = 1;
$offset = 0;

if(isGet()) {
    $data = getBody();
    if (isset($data)) {
        if(isset($data["id"]) && filter_var($data["id"], FILTER_VALIDATE_INT)) {
            $id = $data["id"];
            $sql = "
                SELECT q.id, q.thread, q.message, q.image, q.created, q.tag, q.user, ur.username 
                FROM question q JOIN user ur ON ur.id = q.user WHERE q.id = :id
            ";
            $statement = $db->prepare($sql);
            $statement->bindParam(":id", $id, PDO::PARAM_INT);
            $statement->execute();
            $record = $statement->fetchObject();

            $sql = "SELECT count(id) FROM answer WHERE question = :id";
            $statement = $db->prepare($sql);
            $statement->bindParam(":id", $record->id, PDO::PARAM_INT);
            $statement->execute();
            $count = $statement->fetchColumn();

            $sql = "
                SELECT a.id, a.answer, a.created, ur.username, ur.avatar FROM answer a JOIN user ur ON ur.id = a.user WHERE a.question = :id
            ";

            if(isset($data["page"])) {
                $page = $data["page"];
            }

            $totalPageAnswer = ceil($count / $limit);
            $offset = ($page - 1) * $limit;

            $sql .= " "."LIMIT $limit OFFSET $offset";
            $statement = $db->prepare($sql);
            $statement->bindParam(":id", $record->id, PDO::PARAM_INT);
            $statement->execute();
            $answerRecords = $statement->fetchAll();

            if (!$record) {
                header("Location: 404.php");
            }
        } else {
            header("Location: 404.php");
        }
    }
} else {
    header("Location: 403.php");
}

$question = $record;
$answers = $answerRecords;
$currentPage = $page;
$totalPage = $totalPageAnswer;
$sql_tags = "SELECT * FROM tags ORDER BY name ASC";
$statementTag = $db->prepare($sql_tags);
$statementTag->execute();
$tags = $statementTag->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questions and Answers - My Stack Overflow-like Website</title>
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
            <article class="question-answer">
                <?php if($question->image): ?>
                    <div class="profile-picture">
                        <img src="<?=$question->image?>" alt="Profile Picture" class="user-avatar-in-qna">
                    </div>
                <?php endif ?>
                <div class="question-details">
                    <h2>
                        <?=$question->thread?>
                        <a href="/#" data-bs-toggle="modal" data-bs-target="#editQuestion<?=$question->id?>">Edit</a>
                            <span> | </span>
                        <a href="/#" data-bs-toggle="modal" data-bs-target="#deleteQuestion<?=$question->id?>">Delete</a>
                    </h2>
                    <div class="modal" id="deleteQuestion<?=$question->id?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Delete This Question!</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Do you want to delete this Question?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">No</button>
                                <a href="/user-delete-question.php?id=<?=$question->id?>&user=<?=$question->user?>" class="btn btn-outline-danger" role="button">Yes</a>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal" id="editQuestion<?=$question->id?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit This Question!</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="user-edit-question.php" enctype="multipart/form-data" method="post">
                                    <div class="modal-body">  
                                            <div class="mb-3">
                                                <label for="thread" class="col-form-label">Thread:</label>
                                                <input type="text" class="form-control" id="thread" name="thread" value="<?=$question->thread?>">
                                            </div>
                                            <input class="form-control" type="hidden" name="id" value="<?=$question->id?>"/>
                                            <input class="form-control" type="hidden" name="user" value="<?=$question->user?>"/>
                                            <div class="mb-3">
                                                <label for="message" class="col-form-label">Message:</label>
                                                <input type="text" class="form-control" id="message" name="message" value="<?=$question->message?>">
                                            </div>
                                            <select id="selectModule" required name="tag" class="form-select mb-4" aria-label="Default select example">
                                                <?php foreach($tags as $tag): ?>
                                                    <option value="<?=$tag['id']?>" <?=$question->tag == $tag['id'] ? 'selected' : ''?>><?=$tag['name']?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <div data-mdb-input-init class="form-outline flex-fill mb-4">
                                                <input type="file" id="image" class="form-control" name="image" accept="image/*"/>
                                                <label class="form-label" for="form3Example4cd">Image</label>
                                            </div>
                                            <div ta-mdb-input-init class="form-outline flex-fill mb-4">
                                                <p>Image Uploaded Before</p>
                                                <?php if(isset($question->image)): ?>
                                                    <img src="<?=$question->image?>" alt="Image" class="img-thumbnail" width="200" height="200">
                                                <?php endif ?>
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-outline-primary" role="button">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <p>Posted by: <?=$question->username?> | Date: <?=$question->created?></p>
                    <p><?=$question->message?></p>
                    <?php if($session->get("user")): ?>
                        <form action="answer.php" method="post">
                            <div class="row mb-3">
                                <label for="message-text" class="col-form-label">Answer:</label>
                                <textarea class="form-control" id="message-text" name="answer"></textarea>
                            </div>
                            <input type="hidden" class="form-control" name="question" value="<?=$question->id?>"/>
                            <button class="btn btn-success mb-2 mt-2" type="submit">Submit</button>
                        </form>
                    <?php endif ?>
                    <h3>Answers</h3>
                    <ul class="answer-list">
                        <?php foreach($answers as $answer): ?>
                            <li>
                                <?php if(isset($answer["avatar"])): ?>
                                    <div class="profile-picture">
                                        <img src="<?=$answer["avatar"]?>" alt="Profile Picture" class="img-thumbnail rounded" width="90" height="40">
                                    </div>
                                <?php endif ?>
                                <p>Posted by: <?=$answer["username"]?> | Date: <?=$answer["created"]?></p>
                                <p><?=$answer["answer"]?></p>
                            </li>
                        <?php endforeach ?>
                    </ul>
                    <?php if($totalPage > 1): ?>
                        <p class="mt-4">
                            Total answers: <?=count($answers)?> <?=$totalPage > 1 ? "| Page:" : ""?> 
                            <?php for($page = 0; $page < $totalPage; ++$page): ?>
                                <a
                                    href="/examplequestion.php?id=<?=$question->id?>&page=<?=$page+1?>" 
                                    class="text-decoration-none <?=$currentPage == $page+1 ? 'text-dark' : ''?>">
                                    <?=$page+1?>
                                </a>
                            <?php endfor ?>
                        </p>
                    <?php endif ?>
                </div>
            </article>

        </section>
    </div>

    <footer>
        &copy; Huy's Forum Discussion community
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
