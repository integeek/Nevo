<?php
  session_start();
  if (!isset($_SESSION['parent'])) {
    header('Location: LoginParent.php');
    exit;
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Settings</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../Style/SettingParent.css" />
    <link rel="stylesheet" href="../Style/Variables.css">
    <script src="../Script/SettingParent.js" defer></script>
  </head>
  <body>
    <div class="hero-bg"></div>
    <nav>
      <a href="ParentDashboard.php" class="nav-logo">
        <div class="logo-icon">✦</div>
        Nevo
      </a>
      <a href="ParentDashboard.php" class="nav-btn">← Dashboard</a>
    </nav>

    <main>
      <div class="page-wrap">
        <div class="greeting">
          <h1>Setup your account</h1>
          <p>Manage your account and setup the notifications</p>
        </div>

        <div class="settings-card">
          <div class="section-title">Account</div>
          <label class="settings-label">Name</label>
          <input class="settings-input" id="parentNameInput" type="text" placeholder="Your name" />
          <label class="settings-label">Email</label>
          <input class="settings-input" id="parentEmailInput" type="email" disabled />
          <button class="btn-save" id="saveParentBtn" onclick="saveParent()">Save changes</button>
        </div>

        <div class="settings-card">
          <div class="section-title">Privacy & data</div>
          <div class="action-row"><a href="SettingFamily.php">See the members of my family</a></div>
          <div class="action-row danger" onclick="deleteAccount()">Delete my account</div>
        </div>
      </div>
    </main>
  </body>
</html>
