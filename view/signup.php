<?php

require_once "common.php";
require_once "connection.php";


$db = new Database();


if(isPost()) {
  $errors = [];
  $data = getBody();
  if(isset($data)) {
    $statement = $db->prepare("SELECT * FROM user WHERE email = :email");
    $statement->bindValue(":email", $data["email"]);
    $statement->execute();
    $record = $statement->fetchObject();
    if ($record) {
        $errors["email"] = [
          "message" => "This email existed!",
          "value" => $data["email"],
          "css" => "is-invalid"
        ];
    }
    if(!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
      $errors["email"] = [
        "message" => "This is invalid email!",
        "value" => $datap["email"],
        "css" => "is-invalid"
      ];
    }
    if (empty($data["firstname"])) {
      $errors["firstname"] = [
        "message" => "This firstname is required!",
        "value" => $datap["firstname"],
        "css" => "is-invalid"
      ];
    }
    if (empty($data["lastname"])) {
      $errors["lastname"] = [
        "message" => "This lastname is required!",
        "value" => $datap["lastname"],
        "css" => "is-invalid"
      ];
    }
    if (empty($data["username"])) {
      $errors["username"] = [
        "message" => "This username is required!",
        "value" => $datap["username"],
        "css" => "is-invalid"
      ];
    }
  
    if (empty($data["password"])) {
      $errors["password"] = [
        "message" => "This password is required!",
        "value" => $datap["password"],
        "css" => "is-invalid"
      ];
    } else {
      if ($data["password"] != $data["passwordconfirm"]) {
        $errors["passwordconfirm"] = [
          "message" => "The confirm password must be the same with password!",
          "value" => $datap["passwordconfirm"],
          "css" => "is-invalid"
        ];
      }
    }
    if(!$errors) {
      $image_path = upload_profile_image();
      $sql = $image_path ? "INSERT INTO user (firstname, lastname, username, email, password, avatar)" : "INSERT INTO user (firstname, lastname, username, email, password)";
      $sql .= $image_path ? " VALUES(:firstname, :lastname, :username, :email, :password, :avatar)" : " VALUES(:firstname, :lastname, :username, :email, :password)";
      $statement = $db->prepare($sql);
      $statement->bindValue(":email", $data["email"]);
      $statement->bindValue(":firstname", $data["firstname"]);
      $statement->bindValue(":lastname", $data["lastname"]);
      $statement->bindValue(":username", $data["username"]);
      $statement->bindValue(":email", $data["email"]);
      $hashpassword = password_hash($data["password"], PASSWORD_DEFAULT);
      $statement->bindValue(":password", $hashpassword);
      if($image_path) {
        $statement->bindValue(":avatar", $image_path);
      }
      $statement->execute();
      $session = new Session();
      $email = $data["email"];
      $session->setFlash("signup-success", "You registered successfully with email=$email");
      redirect("/index.php");
    }
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Huy's Forum Discussion Community</title>
    <link rel="stylesheet" href="/style/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    
</head>
<body>
    <header>
        <a href="index.php"><img src="/images/huylogo.png" alt="Logo" class="logo"></a>
        <h1>Huy's Forum Discussion Community</h1>
        <!-- Login/Logout Section -->

    </header>
    <div class="container d-flex justify-content-center">
    <section class="vh-80" style="background-color: white;">
  <div class="container h-90">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-lg-12 col-xl-11">
        <div class="card text-black" style="border-radius: 25px;">
          <div class="card-body p-md-5">
            <div class="row justify-content-center">
              <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">

                <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4">Sign up</p>

                <form class="mx-1 mx-md-4" action="signup.php" enctype="multipart/form-data" method="post">

                  <div class="d-flex flex-row align-items-center mb-4">
                    <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                    <div data-mdb-input-init class="form-outline flex-fill mb-0">
                      <input
                       type="text" 
                       id="form3Example1c" 
                       class="form-control <?= isset($errors['firstname']) ? $errors['firstname']['css'] : ''?>" 
                       name="firstname" 
                       value="<?=isset($data) ? $data["firstname"] : ''?>"
                      />
                      <?php if(isset($errors['firstname'])): ?>
                        <div class="invalid-feedback"><?=$errors["firstname"]["message"]?></div>
                      <?php endif?>
                      <label class="form-label" for="form3Example1c">First Name</label>
                    </div>
                  </div>

                  <div class="d-flex flex-row align-items-center mb-4">
                    <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                    <div data-mdb-input-init class="form-outline flex-fill mb-0">
                      <input type="text" id="form3Example1c" class="form-control <?= isset($errors['lastname']) ? $errors['lastname']['css'] : ''?>" name="lastname" value="<?=isset($data) ? $data["lastname"] : ''?>"/>
                      <?php if(isset($errors['lastname'])): ?>
                        <div class="invalid-feedback"><?=$errors["lastname"]["message"]?></div>
                      <?php endif?>
                      <label class="form-label" for="form3Example1c">Last Name</label>
                    </div>
                  </div>
                  
                  <div class="d-flex flex-row align-items-center mb-4">
                    <i class="fas fa-envelope fa-lg me-3 fa-fw"></i>
                    <div data-mdb-input-init class="form-outline flex-fill mb-0">
                      <input type="text" id="form3Example3c" class="form-control <?= isset($errors['username']) ? $errors['username']['css'] : ''?>" name="username" value="<?=isset($data) ? $data["username"] : ''?>"/>
                      <?php if(isset($errors['username'])): ?>
                        <div class="invalid-feedback"><?=$errors["username"]["message"]?></div>
                      <?php endif?>
                      <label class="form-label" for="form3Example3c">Username</label>
                    </div>
                  </div>

                  <div class="d-flex flex-row align-items-center mb-4">
                    <i class="fas fa-envelope fa-lg me-3 fa-fw"></i>
                    <div data-mdb-input-init class="form-outline flex-fill mb-0">
                      <input type="email" id="form3Example3c" class="form-control <?= isset($errors['email']) ? $errors['email']['css'] : ''?>" name="email" value="<?=isset($data) ? $data["email"] : ''?>"/>
                      <?php if(isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?=$errors["email"]["message"]?></div>
                      <?php endif?>
                      <label class="form-label" for="form3Example3c">Email</label>
                    </div>
                  </div>

                  <div class="d-flex flex-row align-items-center mb-4">
                    <i class="fas fa-lock fa-lg me-3 fa-fw"></i>
                    <div data-mdb-input-init class="form-outline flex-fill mb-0">
                      <input type="password" id="form3Example4c" class="form-control <?= isset($errors['password']) ? $errors['password']['css'] : ''?>" name="password" value="<?=isset($data) ? $data["password"] : ''?>"/>
                      <?php if(isset($errors['password'])): ?>
                        <div class="invalid-feedback"><?=$errors["password"]["message"]?></div>
                      <?php endif?>
                      <label class="form-label" for="form3Example4c">Create Password</label>
                    </div>
                  </div>

                  <div class="d-flex flex-row align-items-center mb-4">
                    <i class="fas fa-key fa-lg me-3 fa-fw"></i>
                    <div data-mdb-input-init class="form-outline flex-fill mb-0">
                      <input type="password" id="form3Example4cd" class="form-control <?= isset($errors['passwordconfirm']) ? $errors['passwordconfirm']['css'] : ''?>" name="passwordconfirm" value="<?=isset($data) ? $data["passwordconfirm"] : ''?>"/>
                      <?php if(isset($errors['passwordconfirm'])): ?>
                        <div class="invalid-feedback"><?=$errors["passwordconfirm"]["message"]?></div>
                      <?php endif?>
                      <label class="form-label" for="form3Example4cd">Confirm Password</label>
                    </div>
                  </div>

                  <div class="d-flex flex-row align-items-center mb-4">
                    <i class="fas fa-key fa-lg me-3 fa-fw"></i>
                    <div data-mdb-input-init class="form-outline flex-fill mb-0">
                      <input type="file" id="avatar" class="form-control" name="image" accept="image/*"/>
                      <label class="form-label" for="form3Example4cd">User Avatar</label>
                    </div>
                  </div>

                  <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
                    <button type="submit" name="register" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg">Register</button>
                  </div>

                </form>

              </div>
              <div class="col-md-10 col-lg-6 col-xl-7 d-flex align-items-center order-1 order-lg-2">

                    <img src="../images/1.png"
                    class="img-fluid" alt="Sample image">

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</section>

    <footer>
        <p>© Huy's Forum Discussion Community. All rights reserved.</p>
    </footer>

    <script>
        function login() {
            // Placeholder for actual login logic
            var username = document.querySelector('#login-form input[type="text"]').value;
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('logout-section').style.display = 'block';
            document.getElementById('user-name').textContent = username; // Display the username
        }

        function logout() {
            // Placeholder for actual logout logic
            document.getElementById('login-form').style.display = 'block';
            document.getElementById('logout-section').style.display = 'none';
        }
    </script>

<script>
        // Function to redirect to signin.php
        function redirectToSignIn() {
            window.location.href = "signin.php";
        }

        // Check if form submission was successful, then redirect
        <?php if (isset($_SESSION['signup-success'])) { ?>
            redirectToSignIn();
        <?php } ?>
    </script>

</body>
</html>

