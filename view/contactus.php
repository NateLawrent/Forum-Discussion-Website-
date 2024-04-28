<?php
require_once "common.php";
require_once "connection.php";

$session = new Session();
$isLogined = $session->get("user");

$db = new Database();

if(isPost()) {
    $data = getBody();
    $errors = [];
    if(isset($data)) {
        if (empty($data["subject"])) {
            $errors["subject"] = [
              "message" => "This field is required!",
              "value" => $data["subject"],
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
        if (!$isLogined) {
            if(!isset($data["email"])) {
                $errors["email"] = [
                    "message" => "This field is required!",
                    "value" => $data["email"],
                    "css" => "is-invalid"
                ];
            }
        }
    }

    if (!$errors) {
        if($isLogined) {
            $sql = "SELECT email FROM user WHERE id = :id";
            $userID = $isLogined;
            $statement = $db->prepare($sql);
            $statement->bindParam(":id", $userID);
            $statement->execute();
            $record = $statement->fetchObject();
            $email = $record->email;
            $insert = "INSERT INTO contact(subject, message, email) VALUES(:subject, :message, :email)";
            $statement = $db->prepare($insert);
            $statement->bindValue(":subject", $data["subject"]);
            $statement->bindValue(":message", $data["message"]);
            $statement->bindValue(":email", $email);
            $statement->execute();
            $session->setFlash("contact-success", "Your contact infor was sent");
        } else {
            var_dump($data);
            $insert = "INSERT INTO contact(subject, message, email) VALUES(:subject, :message, :email)";
            $statement = $db->prepare($insert);
            $statement->bindValue(":subject", $data["subject"]);
            $statement->bindValue(":message", $data["message"]);
            $statement->bindValue(":email", $data["email"]);
            $statement->execute();
            $session->setFlash("contact-success", "Your contact infor was sent");
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - My Stack Overflow-like Website</title>
    <link rel="stylesheet" href="/style/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>

<body>

    <header>
        <img src="/images/huylogo.png" alt="Logo" class="logo">
        <h1>Huy's Forum Discussion community</h1>
    </header>

    <nav>
        <a href="index.php">Home</a>
        <a href="questions.php">Questions</a>
        <a href="tags.php">Tags</a>
        <a href="users.php">Users</a>
        <?php if($isLogined): ?>
            <a href="askquestion.php">Ask Question</a>
        <?php endif ?>
        <a href="contactus.php">Contact Us</a>
        <?php if($session->get("isAdmin")): ?>
            <a href="admin.php">Admin</a>
        <?php endif ?>
        <?php if($isLogined): ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="signin.php">Login</a>
        <?php endif ?>
    </nav>
    <?php
        if ($session->getFlash('contact-success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <p><?=$session->getFlash('contact-success')?></p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
    <?php endif; ?>

    <div class="container d-flex justify-content-center">
        <form style="width: 26rem;" action="contactus.php" method="post">
        <form style="width: 26rem;">
        <h2>Contact Us</h2>
            <div data-mdb-input-init class="form-outline mb-4">
                <input type="text" id="form4Example6" class="form-control <?= isset($errors['subject']) ? $errors['subject']['css'] : ''?>" name="subject"/>
                <?php if(isset($errors['subject'])): ?>
                    <div class="invalid-feedback"><?=$errors["subject"]["message"]?></div>
                <?php endif?>
                <label class="form-label" for="form4Example6">Subject</label>
            </div>
        
            <?php if(!$isLogined): ?>
                <!-- Email input -->
                <div data-mdb-input-init class="form-outline mb-4">
                    <input type="email" id="form4Example2" class="form-control <?= isset($errors['email']) ? $errors['message']['css'] : ''?>" name="email"/>
                    <?php if(isset($errors['message'])): ?>
                        <div class="invalid-feedback"><?=$errors["email"]["message"]?></div>
                    <?php endif?>
                    <label class="form-label" for="form4Example2">Email address</label>
                </div>
            <?php endif ?>

            <!-- Message input -->
            <div data-mdb-input-init class="form-outline mb-4">
                <textarea class="form-control <?= isset($errors['message']) ? $errors['message']['css'] : ''?>" id="form4Example5" rows="4" name="message"></textarea>
                <?php if(isset($errors['message'])): ?>
                    <div class="invalid-feedback"><?=$errors["message"]["message"]?></div>
                <?php endif?>
                <label class="form-label" for="form4Example5">Message</label>
            </div>
        
          <button data-mdb-ripple-init type="submit" class="btn btn-primary btn-block mb-4">Send</button>
        </form>
        
      </form>
    </div>

    <footer>
        <p>&copy; Huy's Forum Discussion community. All rights reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
