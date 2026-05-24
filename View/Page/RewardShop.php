<?php
  session_start();
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Rewards shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="../Style/RewardShop.css">
    <link rel="stylesheet" href="../Style/Variables.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="../Script/RewardShop.js" defer></script>
  </head>
  <body>
    <div class="hero-bg"></div>

    <div class="top-nav">
      <button class="back-btn"><a href="HomePageChild.php">&lsaquo; Back</a></button>
    </div>

    <div class="page-wrap">
      <div class="page-header">
        <h1 class="page-title">Spend your XP !</h1>
        <p class="page-subtitle">Enjoy all your hard work !</p>
      </div>

      <div class="xp-banner">
        <div class="xp-coin"><img src="../Assets/img/icon-star.svg" alt="Star icon"></div>
        <div class="xp-bar-wrap">
          <div class="xp-value" id="xpDisplay">160 <span>XP</span></div>
          <div class="xp-progress">
            <div class="xp-progress-fill" id="xpFill" style="width:62%"></div>
          </div>
        </div>
      </div>

      <div class="row g-3"></div> 

      <div class="positive-message">
        <div class="title-positive">Want a new reward ?</div>
        <div class="message-positive">Ask your parent to add one !</div>
      </div>
    </div>
  </body>
</html>