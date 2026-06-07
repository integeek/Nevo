<?php
  session_start();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Baloo+2:wght@700;800;900&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="../Style/HomePageChild.css">
    <link rel="stylesheet" href="../Style/Variables.css">
    <script src="../Script/HomePageChild.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
  </head>
  <body>

    <div class="hero-bg"></div>
    <nav>
      <a href="#" class="nav-logo">
        <div class="logo-icon">✦</div>
        Nevo
      </a>
    </nav>

    <div class="page-wrap">

      <div class="hero-header">
        <div class="avatar-circle">🦸</div>
        <div class="hero-name">Hey, Noah !</div>
        <div class="hero-level">Level 3 Adventurer</div>
        <div class="xp-bar-wrap">
          <div class="xp-bar-track"><div class="xp-bar-fill" id="xpBar"></div></div>
          <div class="xp-label">160/200 XP to level 4</div>
        </div>
      </div>

      <div class="stats-row">
        <div class="stat-card">
          <div><img src="../Assets/img/icon-fire.svg" class="stat-icon" alt="Fire Icon"></div>
          <div class="stat-value" id="statStreak">5</div>
          <div class="stat-label">Day Streak</div>
        </div>
        <div class="stat-card">
          <div><img src="../Assets/img/icon-bolt.svg" class="stat-icon"alt="Bolt Icon"></div>
          <div class="stat-value" id="statXP">10</div>
          <div class="stat-label">Today's XP</div>
        </div>
      </div>

      <div class="quick-row">
        <a href="WriteFeelings.php" class="quick-card">
          <div class="quick-icon" style="background: #fce4ec"><img src="../Assets/img/icon-write-feelings.svg"  alt="Pen and paper icon"/></div>
          <div>
            <div class="quick-title">Feelings</div><div class="quick-sub">How are you today?</div>
          </div>
          <span class="quick-arrow">&rsaquo;</span>
        </a>
        <a href="RewardShop.php" class="quick-card">
          <div class="quick-icon" style="background: #e7b90066"><img src="../Assets/img/icon-gift.svg" alt="Gift icon"></div>
          <div><div class="quick-title">Rewards</div><div class="quick-sub">Spend your XP</div></div>
          <span class="quick-arrow">&rsaquo;</span>
        </a>
      </div>

      <div class="positive-message">
        <div class="title-positive">Keep going hero !</div>
        <div class="message-positive">You're doing great</div>
      </div>

      <div class="section-card">
        <div class="section-header">
          <span class="section-title">Today's Progress</span>
          <span class="section-badge" id="progressLabel">1/5</span>
        </div>
        <div class="progress-track"><div class="progress-fill" id="progressBar"></div></div>
        <div class="progress-note" id="progressNote">4 routines remaining. You got this!</div>
      </div>

      <div class="section-heading" style="margin-top: 8px;">Quests</div>
      <div class="quests-list"></div>

      <div class="section-heading">Today's Routines</div>
      <div class="quests-list" id="routines-list"></div>
    </div>
  </body>
</html>