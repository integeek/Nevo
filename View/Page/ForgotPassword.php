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
    <title>Forgot password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Baloo+2:wght@700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="../Style/ForgotPassword.css">
    <link rel="stylesheet" href="../Style/Variables.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
  </head>

  <body>
    <nav>
      <a href="#" class="nav-logo">
        <div class="logo-icon">✦</div>
        Nevo
      </a>
    </nav>

    <main>
      <div class="page-center">
        <div class="forgot-card">
          <h1 class="card-title">Forgot password?</h1>
          <p class="card-subtitle">No worries, we'll send you reset instructions.</p>
          <form action="../../Controller/Authentication/ForgotPassword.php" method="post">
            <label>Email</label>
            <div class="input-wrap">
              <input type="email" class="form-input" name="email" placeholder="email@mail.com" id="emailInput" />
            </div>
            <button class="btn-send" >Send reset link →</button>
            <a href="LoginParent.php" class="back-link">← Back to login</a>
            <div class="error" style="color: red; margin-bottom: 1rem;">
              <?= htmlspecialchars($errorMessage) ?>
            </div>
            <div class="success" style="color: black; margin-bottom: 1rem; font-weight: bold;">
              <?= htmlspecialchars($successMessage) ?>
            </div>
          </form>
        </div>
      </div>
    </main>
  </body>
</html>