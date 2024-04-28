<?php
require_once("common.php");
require_once "connection.php";

$session = new Session();
$db = new Database();

$sql_contacts = "SELECT c.id, c.subject, c.message, c.email, ur.firstname, ur.lastname FROM contact c JOIN user ur ON ur.email = c.email ORDER BY ur.firstname, ur.lastname, c.subject ASC";
$statementContact = $db->prepare($sql_contacts);
$statementContact->execute();
$contacts = $statementContact->fetchAll();

$sql_users = "SELECT * FROM user ORDER BY firstname, lastname, email ASC";
$statementUser = $db->prepare($sql_users);
$statementUser->execute();
$users = $statementUser->fetchAll();

$sql_questions = "SELECT q.id, q.thread, q.message, q.user, q.tag, q.created, t.name FROM question q JOIN tags t ON q.tag = t.id ORDER BY thread, message ASC";
$statementQuestion = $db->prepare($sql_questions);
$statementQuestion->execute();
$questions = $statementQuestion->fetchAll();

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
    <title>Huy's Forum Discussion Community</title>
    <link rel="stylesheet" href="/style/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <style>
        nav {
            background-color: #367d39;
            padding: 10px 0;
            margin-bottom: 20px;
        }

        nav a:hover {
            color: #007bff;
        }

        .nav-tabs {
            justify-content: flex-end;
        }

        .nav-tabs .nav-item {
            margin-left: 10px;
        }

        .nav-tabs .nav-link {
            color: #4950    57;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0;
        }

        .nav-tabs .nav-link.active {
            color: #ffffff;
            background-color: #007bff;
            border-color: #007bff;
        }

        .nav-center {
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>
    <header>
        <img src="/images/huylogo.png" alt="Logo" class="logo">
        <h1>Huy's Forum Discussion Community</h1>
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

    <div class="container text-center">
        <section class="main-content">
            <h2>Admin Dashboard</h2>
            <ul class="nav nav-tabs" id="adminTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="contact-tab" data-bs-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="true">Contact Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="users-tab" data-bs-toggle="tab" href="#users" role="tab" aria-controls="users" aria-selected="false">Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="questions-tab" data-bs-toggle="tab" href="#questions" role="tab" aria-controls="questions" aria-selected="false">Questions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tags-tab" data-bs-toggle="tab" href="#tags" role="tab" aria-controls="tags" aria-selected="false">Tags</a>
                </li>
            </ul>
            <div class="tab-content" id="adminTabsContent">
                <div class="tab-pane fade show active" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                    <h3 class="mt-4">Contact Us</h3>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Message</th>
                                    <th>Actions</th>
                                    <!-- <th>Date</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($contacts as $contact): ?>
                                    <tr>
                                        <td><?=$contact["firstname"]." ".$contact["lastname"]?></td>
                                        <td><?=$contact["email"]?></td>
                                        <td><?=$contact["subject"]?></td>
                                        <td><?=$contact["message"]?></td>
                                        <td>
                                            <a href="/#" data-bs-toggle="modal" data-bs-target="#editContact<?=$contact["id"]?>">Edit</a>
                                            <span> | </span>
                                            <a href="/#" data-bs-toggle="modal" data-bs-target="#deleteContact<?=$contact["id"]?>">Delete</a>
                                        </td>
                                    </tr>
                                    <div class="modal" id="deleteContact<?=$contact["id"]?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete This Contact!</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Do you want to delete this contact?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">No</button>
                                                <a href="/delete-contact.php?id=<?=$contact["id"]?>" class="btn btn-outline-danger" role="button">Yes</a>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal" id="editContact<?=$contact["id"]?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit This Contact!</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="edit-contact.php" method="post">
                                                    <div class="modal-body">
                                                        
                                                            <div class="mb-3">
                                                                <label for="subject" class="col-form-label">Subject:</label>
                                                                <input type="text" class="form-control" id="subject" name="subject" value="<?=$contact["subject"]?>">
                                                            </div>
                                                            <input class="form-control" type="hidden" name="id" value="<?=$contact["id"]?>"/>
                                                            <div class="mb-3">
                                                                <label for="message-text" class="col-form-label">Message:</label>
                                                                <textarea class="form-control" id="message-text" name="message"><?=$contact["message"]?></textarea>
                                                            </div>
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-outline-primary" role="button">Submit</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
                    <h3>Users</h3>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Admin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($users as $user): ?>
                                    <tr>
                                        <td><?=$user["firstname"]." ".$user["lastname"]?></td>
                                        <td><?=$user["username"]?></td>
                                        <td><?=$user["email"]?></td>
                                        <td><?=$user["isAdmin"] ? "Yes" : "No"?></td>
                                        <td>
                                            <a href="/#" data-bs-toggle="modal" data-bs-target="#editUser<?=$user["id"]?>">Edit</a>
                                            <span> | </span>
                                            <a href="/#" data-bs-toggle="modal" data-bs-target="#deleteUser<?=$user["id"]?>">Delete</a>
                                        </td>
                                    </tr>
                                    <div class="modal" id="deleteUser<?=$user["id"]?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete This User!</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Do you want to delete this user?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">No</button>
                                                <a href="/delete-user.php?id=<?=$user["id"]?>" class="btn btn-outline-danger" role="button">Yes</a>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal" id="editUser<?=$user["id"]?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit This User!</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="edit-user.php" method="post">
                                                    <div class="modal-body">  
                                                            <div class="mb-3">
                                                                <label for="firstname" class="col-form-label">Firstname:</label>
                                                                <input type="text" class="form-control" id="firstname" name="firstname" value="<?=$user["firstname"]?>">
                                                            </div>
                                                            <input class="form-control" type="hidden" name="id" value="<?=$user["id"]?>"/>
                                                            <div class="mb-3">
                                                                <label for="lastname" class="col-form-label">Lastname:</label>
                                                                <input type="text" class="form-control" id="lastname" name="lastname" value="<?=$user["lastname"]?>">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="email" class="col-form-label">Email:</label>
                                                                <input type="text" class="form-control" id="email" name="email" value="<?=$user["email"]?>">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="username" class="col-form-label">Username:</label>
                                                                <input type="text" class="form-control" id="username" name="username" value="<?=$user["username"]?>">
                                                            </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-outline-primary" role="button">Submit</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="questions" role="tabpanel" aria-labelledby="questions-tab">
                    <h3>Questions</h3>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Author</th>
                                    <th>Thread</th>
                                    <th>Message</th>
                                    <th>Num Answers</th>
                                    <th>Tag</th>
                                    <th>Actions</th>
                                    <!-- <th>Date</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($questions as $question): ?>
                                    <tr>
                                        <!-- <td><?=$question["username"]?></td> -->
                                        <td>
                                            <?php
                                            $sql_user = "SELECT username FROM user WHERE id = :id";
                                            $statementuser = $db->prepare($sql_user);
                                            $statementuser->bindParam(":id", $question["user"], PDO::PARAM_INT);
                                            $statementuser->execute();
                                            $user = $statementuser->fetchColumn();
                                            echo $user;
                                            ?>
                                        </td>
                                        <td><?=$question["thread"]?></td>
                                        <td><?=$question["message"]?></td>
                                        <td>
                                            <?php
                                            $sql_answers = "SELECT COUNT(id) FROM answer WHERE question = :id";
                                            $statementAnswers = $db->prepare($sql_answers);
                                            $statementAnswers->bindParam(":id", $question["id"], PDO::PARAM_INT);
                                            $statementAnswers->execute();
                                            $answers = $statementAnswers->fetchColumn();
                                            echo $answers;
                                            ?>
                                        </td>
                                        <td><?=$question["name"]?></td>
                                        <td>
                                            <a href="/#" data-bs-toggle="modal" data-bs-target="#editQuestion<?=$question["id"]?>">Edit</a>
                                            <span> | </span>
                                            <a href="/#" data-bs-toggle="modal" data-bs-target="#deleteQuestion<?=$question["id"]?>">Delete</a>
                                        </td>
                                    </tr>
                                    <div class="modal" id="deleteQuestion<?=$question["id"]?>" tabindex="-1">
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
                                                <a href="/delete-question.php?id=<?=$question["id"]?>" class="btn btn-outline-danger" role="button">Yes</a>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal" id="editQuestion<?=$question["id"]?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit This Question!</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="edit-question.php" method="post">
                                                    <div class="modal-body">  
                                                            <div class="mb-3">
                                                                <label for="thread" class="col-form-label">Thread:</label>
                                                                <input type="text" class="form-control" id="thread" name="thread" value="<?=$question["thread"]?>">
                                                            </div>
                                                            <input class="form-control" type="hidden" name="id" value="<?=$question["id"]?>"/>
                                                            <div class="mb-3">
                                                                <label for="message" class="col-form-label">Message:</label>
                                                                <input type="text" class="form-control" id="message" name="message" value="<?=$question["message"]?>">
                                                            </div>
                                                            <label for="selectModule" class="col-form-label">Select A Tag</label>
                                                            <select id="selectModule" required name="tag" class="form-select" aria-label="Default select example">
                                                                <?php foreach($tags as $tag): ?>
                                                                    <option value="<?=$tag['id']?>"><?=$tag['name']?></option>
                                                                <?php endforeach ?>
                                                            </select>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-outline-primary" role="button">Submit</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="tags" role="tabpanel" aria-labelledby="tags-tab">
                    <h3>Tags</h3>
                    <a class="btn btn-outline-primary" href="/#" data-bs-toggle="modal" data-bs-target="#addTag">
                        Add Tag
                    </a>
                    <div class="modal" id="addTag" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add This Tag!</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="add-tag.php" method="post">
                                    <div class="modal-body">  
                                            <div class="mb-3">
                                                <label for="name" class="col-form-label">Name:</label>
                                                <input type="text" class="form-control" id="name" name="name">
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-outline-primary" role="button">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($tags as $tag): ?>
                                    <tr>
                                        <td><?=$tag["name"]?></td>
                                        <td>
                                            <a href="/#" data-bs-toggle="modal" data-bs-target="#editTag<?=$tag["id"]?>">Edit</a>
                                            <span> | </span>
                                            <a href="/#" data-bs-toggle="modal" data-bs-target="#deleteTag<?=$tag["id"]?>">Delete</a>
                                        </td>
                                    </tr>
                                    <div class="modal" id="deleteTag<?=$tag["id"]?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete This Tag!</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Do you want to delete this Tag?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">No</button>
                                                <a href="/delete-tag.php?id=<?=$tag["id"]?>" class="btn btn-outline-danger" role="button">Yes</a>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal" id="editTag<?=$tag["id"]?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit This Tag!</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="edit-tag.php" method="post">
                                                    <div class="modal-body">  
                                                            <div class="mb-3">
                                                                <label for="name" class="col-form-label">Name:</label>
                                                                <input type="text" class="form-control" id="name" name="name" value="<?=$tag["name"]?>">
                                                            </div>
                                                            <input class="form-control" type="hidden" name="id" value="<?=$tag["id"]?>"/>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-outline-primary" role="button">Submit</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <footer>
        <p>© Huy's Forum Discussion Community. All rights reserved.</p>
    </footer>

    <script>
        function login() {
            var username = document.querySelector('#login-form input[type="text"]').value;
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('logout-section').style.display = 'block';
            document.getElementById('user-name').textContent = username;
        }

        function logout() {
            document.getElementById('login-form').style.display = 'block';
            document.getElementById('logout-section').style.display = 'none';
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
