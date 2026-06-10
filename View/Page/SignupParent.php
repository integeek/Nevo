<?php
  session_start();
  $errorMessage = $_SESSION["error"] ?? "";
  unset($_SESSION["error"]);

  $successMessage = $_SESSION["success"] ?? "";
  unset($_SESSION["success"]);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Baloo+2:wght@700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <link rel="stylesheet" href="../Style/SignupParent.css">
    <link rel="stylesheet" href="../Style/Variables.css">
    <script src="../Script/TogglePassword.js" defer></script>
    <script src="../Script/validatorCriters.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
  </head>
  <body>
    <nav>
      <a href="HomeLogin.php" class="nav-logo">
        <div class="logo-icon">✦</div>
        Nevo
      </a>
    </nav>

    <main>
      <div class="page-center">
        <div class="register-card">
          <div class="card-header-block">
            <h1 class="card-title">Create your family account</h1>
            <p class="card-subtitle">A safe place to grow healthy habits together</p>
          </div>
          <form action="../../Controller/Authentication/Signup.php" method="post" class="groupForm">
            <label>Name</label>
            <div class="input-wrap">
              <input type="text" class="form-input" name="name" placeholder="Sarah" id="nameInput"/>
            </div>

            <label>Email</label>
            <div class="input-wrap">
              <input type="email" class="form-input" name="email" placeholder="email@mail.com" id="emailInput"/>
            </div>

            <label>Password</label>
            <div class="input-wrap">
              <input type="password" class="form-input" name="password" placeholder="••••••••" id="passwordInput"/>
              <img class="toggle-pass" id="toggleBtn" src="../Assets/img/icon-eye-open.png" alt="Show/Hide password" >
            </div>

            <div class="validatorCriters">
              <div class="digit"><i class="far fa-check-circle"></i> &nbsp;Your password must contain at least one number</div>
              <div class="uppercase"><i class="far fa-check-circle"></i> &nbsp;Your password must contain at least one uppercase letter</div>
              <div class="lowercase"><i class="far fa-check-circle"></i> &nbsp;Your password must contain at least one lowercase letter</div>
              <div class="length"><i class="far fa-check-circle"></i> &nbsp;Your password must contain at least 8 characters</div>
            </div>
            <button class="btn-register" onclick="handleRegister()">Create my account <span>→</span></button>
            <p class="terms-text">By creating an account, you agree to our <a href="#">Terms</a> and <a href="#">Privacy Policy</a>.</p>
            <p class="card-footer-text"> Already have an account? <a href="LoginParent.php">Log in</a></p>
            
            <div style="color: red; margin-bottom: 1rem;">
              <?= htmlspecialchars($errorMessage) ?>
            </div>
            <div style="color: black; margin-bottom: 1rem; font-weight: bold;">
              <?= htmlspecialchars($successMessage) ?>
            </div>
          </form>
        </div>
      </div>
    </main>
  </body>
</html>