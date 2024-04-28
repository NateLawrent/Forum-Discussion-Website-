<?php

require_once "common.php";
require_once "connection.php";

$db = new Database();

if(isPost()) {
  $errors = [];
  $data = getBody();
  if(isset($data)) {

    if(!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
      $errors["email"] = [
        "message" => "This is invalid email!",
        "value" => $data["email"],
        "css" => "is-invalid"
      ];
    }
    if (empty($data["password"])) {
      $errors["password"] = [
        "message" => "This password is required!",
        "value" => $data["password"],
        "css" => "is-invalid"
      ];
    }
    $statement = $db->prepare("SELECT id, email, password, isAdmin FROM user WHERE email = :email");
    $statement->bindValue(":email", $data["email"]);
    $statement->execute();
    $record = $statement->fetchObject();
    if (!$record) {
      $errors["email"] = [
        "message" => "This email does not exist!",
        "value" => $data["email"],
        "css" => "is-invalid"
      ];
    }
    if (!password_verify($data["password"], $record->password)) {
      $errors["password"] = [
        "message" => "Password is incorrect",
        "value" => $data["password"],
        "css" => "is-invalid"
      ];
    }
    if(!$errors) {
      login($record);
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
        <img src="/images/huylogo.png" alt="Logo" class="logo">
        <h1>Huy's Forum Discussion Community</h1>
        <!-- Login/Logout Section -->

    </header>
    <section class="vh-90">
        <div class="container-fluid h-custom">
          <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-9 col-lg-6 col-xl-5">
              <img src="/images/1.png"
                class="img-fluid" alt="Sample image">
            </div>
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
              <form action="signin.php" method="post">
                <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                  
            
                </div>
      
                <div class="divider d-flex align-items-center my-4">
              
                </div>
      
                <!-- Email input -->
                <div data-mdb-input-init class="form-outline mb-4">
                  <input type="email" id="form3Example3" name="email"
                    placeholder="Enter a valid email address" 
                    class="form-control form-control-lg <?=isset($errors['email']) ? $errors['email']['css'] : ''?>" name="email" value="<?=isset($data) ? $data["email"] : ''?>"
                  />
                    <?php if(isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?=$errors["email"]["message"]?></div>
                    <?php endif?>
                  <label class="form-label" for="form3Example3">Email address</label>
                </div>
      
                <!-- Password input -->
                <div data-mdb-input-init class="form-outline mb-3">
                  <input type="password" id="form3Example4" class="form-control form-control-lg <?= isset($errors['password']) ? $errors['password']['css'] : ''?>" name="password"
                    placeholder="Enter password" />
                    <?php if(isset($errors['password'])): ?>
                        <div class="invalid-feedback"><?=$errors["password"]["message"]?></div>
                    <?php endif?>
                  <label class="form-label" for="form3Example4">Password</label>
                </div>
      
                <div class="d-flex justify-content-between align-items-center">
               
                    
              
                <div class="text-center text-lg-start mt-4 pt-2">
                  <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
                    style="padding-left: 2.5rem; padding-right: 2.5rem;">Login</button>
                  <p class="small fw-bold mt-2 pt-1 mb-0">Don't have an account? <a href="/signup.php">Register</a></p>
                </div>
      
              </form>
            </div>
          </div>
        </div>
        <div>
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

</body>
</html>
