<?php
require_once "common.php";
require_once "connection.php";

$session = new Session();

$db = new Database();

if(isGet()) {
    $sql = "SELECT * FROM tags";
    $statement = $db->prepare($sql);
    $statement->execute();
    $tags = $statement->fetchAll();
}

if(isPost()) {
  $errors = [];
  $data = getBody();
  if(isset($data)) {
    if (empty($data["thread"])) {
        $errors["thread"] = [
          "message" => "This field is required!",
          "value" => $data["thread"],
          "css" => "is-invalid"
        ];
      }
    if (empty($data["message"])) {
      $errors["message"] = [
        "message" => "This field is required!",
        "value" => $data["message"],
        "css" => "is-invalid"
      ];
    }
    if(!$errors) {
        $image_path = upload_question_image();
        var_dump($data);
        $sql = $image_path ? "INSERT INTO question (thread, message, user, image, created, tag, isActive)  " : "INSERT INTO question (thread, message, user, created, tag, isActive)  ";
        $sql .= $image_path ? "VALUES(:thread, :message, :user, :image, :created, :tag, :isActive)" : "VALUES(:thread, :message, :user, :created, :tag, :isActive)";
        $statement = $db->prepare($sql);
        $statement->bindValue(":thread", $data["thread"]);
        $statement->bindValue(":message", $data["message"]);
        $now = new DateTime("now");
        $isActive = 1;
        $statement->bindValue(":isActive", $isActive);
        $statement->bindValue(":created", $now->format('Y-m-d\TH:i:s'));
        $statement->bindParam(":user", $session->get("user"), PDO::PARAM_INT);
        $statement->bindParam(":tag", $data["tag"], PDO::PARAM_INT);
        if($image_path) {
            $statement->bindValue(":image", $image_path);
        }
        $statement->execute();
        redirect("/questions.php");
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ask Question - My Stack Overflow-like Website</title>
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

    
    <div class="container d-flex justify-content-center">
        <form style="width: 26rem;" action="askquestion.php" method="post" enctype="multipart/form-data">
            <h2>Ask Question</h2>
            <form style="width: 26rem;" >
                <!-- Name input -->
                <div data-mdb-input-init class="form-outline mb-4">
                    <input type="text" id="form4Example1" class="form-control <?= isset($errors['thread']) ? $errors['thread']['css'] : ''?>" name="thread"/>
                    <?php if(isset($errors['thread'])): ?>
                        <div class="invalid-feedback"><?=$errors["thread"]["message"]?></div>
                    <?php endif?>
                    <label class="form-label" for="form4Example1">Thread</label>
                </div>

                <select id="selectModule" required name="tag" class="form-select" aria-label="Default select example">
                    <?php foreach($tags as $tag): ?>
                        <option value="<?=$tag['id']?>"><?=$tag['name']?></option>
                    <?php endforeach ?>
                </select>
                <label class="form-label mb-4" for="selectModule">Select A Module</label>

                <div data-mdb-input-init class="form-outline flex-fill mb-4">
                    <input type="file" id="avatar" class="form-control" name="image" accept="image/*"/>
                    <label class="form-label" for="form3Example4cd">User Avatar</label>
                </div>
            
                <!-- Message input -->
                <div data-mdb-input-init class="form-outline mb-4">
                    <textarea class="form-control <?= isset($errors['message']) ? $errors['message']['css'] : ''?>" id="form4Example3" rows="4" name="message"></textarea>
                    <?php if(isset($errors['message'])): ?>
                        <div class="invalid-feedback"><?=$errors["message"]["message"]?></div>
                    <?php endif?>
                    <label class="form-label" for="form4Example3">Message</label>
                </div>
            
                <!-- Submit button -->
                <button data-mdb-ripple-init type="submit" class="btn btn-primary btn-block mb-4">Send</button>
            </form>
      </form>
    </div>


   
    <footer>
        <p>&copy; Huy's Forum Discussion community. All rights reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
