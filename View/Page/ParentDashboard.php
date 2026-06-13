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
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Parent Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="../Style/ParentDashboard.css">
    <link rel="stylesheet" href="../Style/Variables.css">
    <script src="../Script/ParentDashboard.js" defer></script>
  </head>

  <body>
    <div class="hero-bg"></div>
    <nav>
      <a href="#" class="nav-logo">
        <div class="logo-icon">✦</div>
        Nevo
      </a>
      <div style="display:flex; gap:10px; align-items:center;">
        <a href="SettingParent.php" class="nav-btn nav-btn-outline">⚙ Settings</a>
        <a href="SettingFamily.php" class="nav-btn">My family</a>
        <a href="../../Controller/Authentication/Logout.php" class="nav-btn nav-btn-outline">Logout</a>
      </div>
    </nav>

    <main>
      <div class="greeting">
        <h1>Parent dashboard</h1>
        <p>Manage your children's routines, rewards, feelings, and analytics</p>
      </div>

      <div class="managing-bar">
        <span class="managing-label">Select a child to edit their routines and rewards</span>
        <div id="childPillsBar"></div>
      </div>

      <div class="stats-row">
        <div class="stat-card">
          <div class="slabel">Completion</div>
          <div class="sval" id="sCompletion">—</div>
        </div>
        <div class="stat-card">
          <div class="slabel">This Week</div>
          <div class="sval" id="sWeek">—</div>
        </div>
        <div class="stat-card">
          <div class="slabel">Streak</div>
          <div class="sval" id="sStreak">—</div>
        </div>
        <div class="stat-card">
          <div class="slabel">Routines</div>
          <div class="sval" id="sRoutines">—</div>
        </div>
      </div>

      <div class="child-section">
        <div id="setupLabel" class="child-setup-label"></div>

        <div class="tabs-nav">
          <button class="tab-btn active" onclick="switchTab(this,'routines')">
            <img src="../Assets/img/icon-check-routine.svg" alt="Routine icon">
            Routines
          </button>
          <button class="tab-btn" onclick="switchTab(this,'rewards')">
            <img src="../Assets/img/icon-gift2.svg" alt="Gift icon">
            Rewards
          </button>
          <button class="tab-btn" onclick="switchTab(this,'quests')">
            <img src="../Assets/img/icon-target.svg" alt="Quest icon">
            Quests
          </button>
          <button class="tab-btn" onclick="switchTab(this,'feelings')">
            <img src="../Assets/img/icon-heart.svg" alt="Heart icon">
            Feelings
          </button>
          <button class="tab-btn" onclick="switchTab(this,'analytics')">
            <img src="../Assets/img/icon-analytic.svg" alt="Analytics icon">
            Analytics
          </button>
        </div>

        <div class="tab-panel active" id="tab-routines">
          <div class="panel-header">
            <div class="panel-title">Daily Routines</div>
            <button class="add-btn" onclick="openModal('newRoutineModalOverlay')">+ &nbsp; &nbsp;Add Routine</button>
          </div>
          <div id="routinesList"></div>
        </div>

        <div class="tab-panel" id="tab-rewards">
          <div class="panel-header">
            <div class="panel-title">Rewards</div>
            <button class="add-btn" onclick="openModal('newRewardModalOverlay')">+ &nbsp; &nbsp; Add Reward</button>
          </div>
          <div class="rewards-grid" id="rewardsList"></div>
        </div>

        <div class="tab-panel" id="tab-quests">
          <div class="panel-header">
            <div class="panel-title">Quests</div>
            <button class="add-btn" onclick="openModal('newQuestModalOverlay')">+ &nbsp; &nbsp; Add Quest</button>
          </div>
          <div id="questsList"></div>
        </div>

        <div class="tab-panel" id="tab-feelings">
          <div class="panel-header"><div class="panel-title">Recent Feelings</div></div>
          <div id="feelingsList"></div>
        </div>

        <div class="tab-panel" id="tab-analytics">
          <div class="panel-header"><div class="panel-title">Analytics</div></div>
          <div class="chart-card">
            <div class="clabel">Routine completion</div>
            <div id="routineChart"></div>
          </div>
          <div class="chart-card" style="margin-top:14px;">
            <div class="clabel">Feelings distribution</div>
            <div id="feelingsChart"></div>
          </div>
        </div>

        <div class="modal-overlay" id="newRoutineModalOverlay">
          <div class="modal-box">
            <h2>Create routine</h2>
            <div>
              <label>Icon</label>
              <div class="avatar-picker" id="routineIconPicker">
                <div class="avatar-option selected-avatar" style="background:linear-gradient(135deg,#ffe082,#ffd54f)" data-icon="icon-alarm" onclick="pickRoutineIcon(this,'routineIcon')">
                  <img src="../Assets/img/icon-alarm.svg" alt="Alarm icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#c5cae9,#9fa8da)" data-icon="icon-book-solo" onclick="pickRoutineIcon(this,'routineIcon')">
                  <img src="../Assets/img/icon-book-solo.svg" alt="Book icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#b2dfdb,#80cbc4)" data-icon="icon-pills" onclick="pickRoutineIcon(this,'routineIcon')">
                  <img src="../Assets/img/icon-pills.svg" alt="Pills icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#ffccbc,#ffab91)" data-icon="icon-sport" onclick="pickRoutineIcon(this,'routineIcon')">
                  <img src="../Assets/img/icon-sport.svg" alt="Sport icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#ce93d8,#ba68c8)" data-icon="icon-drop-water" onclick="pickRoutineIcon(this,'routineIcon')">
                  <img src="../Assets/img/icon-drop-water.svg" alt="Drop water icon" />
                </div>
              </div>
              <input type="hidden" id="routineIcon" value="icon-alarm" />
              <label for="nameInput">Name</label>
              <input class="modal-input" id="nameInput" type="text" placeholder="Name" maxlength="12" />
              <label for="xpInput">Xp</label>
              <input class="modal-input" id="xpInput" type="text" placeholder="15" maxlength="4" />
              <label for="stepsInput">Steps (one per line)</label>
              <textarea class="modal-input" id="stepsInput" rows="4" placeholder="Put the steps of the routine"></textarea>
            </div>
            <button class="btn-add" onclick="saveRoutine()">Save</button>
            <br />
            <button class="btn-cancel" onclick="closeModal('newRoutineModalOverlay')">Cancel</button>
          </div>
        </div>

        <div class="modal-overlay" id="newRewardModalOverlay">
          <div class="modal-box">
            <h2>Create reward</h2>
            <div>
              <label>Icon</label>
              <div class="avatar-picker" id="rewardIconPicker">
                <div class="avatar-option selected-avatar" style="background:linear-gradient(135deg,#fff9c4,#fff176)" data-icon="icon-star" onclick="pickRewardIcon(this,'rewardIcon')">
                  <img src="../Assets/img/icon-star.svg" alt="Star icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#c5cae9,#9fa8da)" data-icon="icon-gift" onclick="pickRewardIcon(this,'rewardIcon')">
                  <img src="../Assets/img/icon-gift.svg" alt="Gift icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#b2dfdb,#80cbc4)" data-icon="icon-chocolate" onclick="pickRewardIcon(this,'rewardIcon')">
                  <img src="../Assets/img/icon-chocolate.svg" alt="Chocolate icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#ffccbc,#ffab91)" data-icon="icon-movie" onclick="pickRewardIcon(this,'rewardIcon')">
                  <img src="../Assets/img/icon-movie.svg" alt="Movie icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#ce93d8,#ba68c8)" data-icon="icon-slide" onclick="pickRewardIcon(this,'rewardIcon')">
                  <img src="../Assets/img/icon-slide.svg" alt="Slide icon" />
                </div>
              </div>
              <input type="hidden" id="rewardIcon" value="icon-star" />
              <label for="nameRewardInput">Name</label>
              <input class="modal-input" id="nameRewardInput" type="text" placeholder="Name" maxlength="12" />
              <label for="xpRewardInput">Cost in Xp</label>
              <input class="modal-input" id="xpRewardInput" type="text" placeholder="15" maxlength="4" />
              <label>Type</label>
              <div class="type-toggle">
                <button type="button" class="type-btn active" data-value="out_app" onclick="selectRewardType(this,'rewardType')"><img src="../Assets/img/icon-gift.svg" alt=""> Real reward</button>
                <button type="button" class="type-btn" data-value="in_app" onclick="selectRewardType(this,'rewardType')"><img src="../Assets/img/icon-star.svg" alt=""> In-app</button>
              </div>
              <input type="hidden" id="rewardType" value="out_app" />
            </div>
            <button class="btn-add" onclick="saveReward()">Save</button>
            <br />
            <button class="btn-cancel" onclick="closeModal('newRewardModalOverlay')">Cancel</button>
          </div>
        </div>

        <div class="modal-overlay" id="editRoutineModalOverlay">
          <div class="modal-box">
            <h2>Edit routine</h2>
            <div>
              <label>Icon</label>
              <div class="avatar-picker" id="editRoutineIconPicker">
                <div class="avatar-option selected-avatar" style="background:linear-gradient(135deg,#ffe082,#ffd54f)" data-icon="icon-alarm" onclick="pickRoutineIcon(this,'editRoutineIcon')">
                  <img src="../Assets/img/icon-alarm.svg" alt="Alarm icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#c5cae9,#9fa8da)" data-icon="icon-book-solo" onclick="pickRoutineIcon(this,'editRoutineIcon')">
                  <img src="../Assets/img/icon-book-solo.svg" alt="Book icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#b2dfdb,#80cbc4)" data-icon="icon-calendar" onclick="pickRoutineIcon(this,'editRoutineIcon')">
                  <img src="../Assets/img/icon-calendar.svg" alt="Calendar icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#ffccbc,#ffab91)" data-icon="icon-sport" onclick="pickRoutineIcon(this,'editRoutineIcon')">
                  <img src="../Assets/img/icon-sport.svg" alt="Sport icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#ce93d8,#ba68c8)" data-icon="icon-rocket" onclick="pickRoutineIcon(this,'editRoutineIcon')">
                  <img src="../Assets/img/icon-rocket.svg" alt="Rocket icon" />
                </div>
              </div>
              <input type="hidden" id="editRoutineIcon" value="icon-alarm" />
              <label for="editNameInput">Name</label>
              <input class="modal-input" id="editNameInput" type="text" placeholder="Name"/>
              <label for="editXpInput">Xp</label>
              <input class="modal-input" id="editXpInput" type="text" placeholder="15" maxlength="4" />
              <label for="editStepsInput">Steps (one per line)</label>
              <textarea class="modal-input" id="editStepsInput" rows="4" placeholder="Put the steps of the routine"></textarea>
            </div>
            <button class="btn-add" onclick="editRoutine()">Save</button>
            <br />
            <button class="btn-cancel" onclick="closeModal('editRoutineModalOverlay')">Cancel</button>
          </div>
        </div>

        <div class="modal-overlay" id="editRewardModalOverlay">
          <div class="modal-box">
            <h2>Edit reward</h2>
            <div>
              <label>Icon</label>
              <div class="avatar-picker" id="editRewardIconPicker">
                <div class="avatar-option selected-avatar" style="background:linear-gradient(135deg,#fff9c4,#fff176)" data-icon="icon-star" onclick="pickRewardIcon(this,'editRewardIcon')">
                  <img src="../Assets/img/icon-star.svg" alt="Star icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#c5cae9,#9fa8da)" data-icon="icon-gift" onclick="pickRewardIcon(this,'editRewardIcon')">
                  <img src="../Assets/img/icon-gift.svg" alt="Gift icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#b2dfdb,#80cbc4)" data-icon="icon-chocolate" onclick="pickRewardIcon(this,'editRewardIcon')">
                  <img src="../Assets/img/icon-chocolate.svg" alt="Chocolate icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#ffccbc,#ffab91)" data-icon="icon-movie" onclick="pickRewardIcon(this,'editRewardIcon')">
                  <img src="../Assets/img/icon-movie.svg" alt="Movie icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#ce93d8,#ba68c8)" data-icon="icon-slide" onclick="pickRewardIcon(this,'editRewardIcon')">
                  <img src="../Assets/img/icon-slide.svg" alt="Slide icon" />
                </div>
              </div>
              <input type="hidden" id="editRewardIcon" value="icon-star" />
              <label for="editRewardNameInput">Name</label>
              <input class="modal-input" id="editRewardNameInput" type="text" placeholder="Name" />
              <label for="editRewardXpInput">Cost in Xp</label>
              <input class="modal-input" id="editRewardXpInput" type="text" placeholder="15" maxlength="4" />
              <label>Type</label>
              <div class="type-toggle">
                <button type="button" class="type-btn active" data-value="out_app" onclick="selectRewardType(this,'editRewardType')"><img src="../Assets/img/icon-gift.svg" alt=""> Real reward</button>
                <button type="button" class="type-btn" data-value="in_app" onclick="selectRewardType(this,'editRewardType')"><img src="../Assets/img/icon-star.svg" alt=""> In-app</button>
              </div>
              <input type="hidden" id="editRewardType" value="out_app" />
            </div>
            <button class="btn-add" onclick="editReward()">Save</button>
            <br />
            <button class="btn-cancel" onclick="closeModal('editRewardModalOverlay')">Cancel</button>
          </div>
        </div>

        <div class="modal-overlay" id="newQuestModalOverlay">
          <div class="modal-box">
            <h2>Create quest</h2>
            <div>
              <label>Icon</label>
              <div class="avatar-picker" id="questIconPicker">
                <div class="avatar-option selected-avatar" style="background:linear-gradient(135deg,#fff9c4,#fff176)" data-icon="icon-star" onclick="pickQuestIcon(this,'questIcon')">
                  <img src="../Assets/img/icon-star.svg" alt="Star icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#c5cae9,#9fa8da)" data-icon="icon-calendar" onclick="pickQuestIcon(this,'questIcon')">
                  <img src="../Assets/img/icon-calendar.svg" alt="Calendar icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#b2dfdb,#80cbc4)" data-icon="icon-fire" onclick="pickQuestIcon(this,'questIcon')">
                  <img src="../Assets/img/icon-fire.svg" alt="Fire icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#ffccbc,#ffab91)" data-icon="icon-done" onclick="pickQuestIcon(this,'questIcon')">
                  <img src="../Assets/img/icon-done.svg" alt="Done icon" />
                </div>
              </div>
              <input type="hidden" id="questIcon" value="icon-star" />
              <label for="questNameInput">Name</label>
              <input class="modal-input" id="questNameInput" type="text" placeholder="Quest name" maxlength="50" />
              <label for="questXpInput">XP Reward</label>
              <input class="modal-input" id="questXpInput" type="text" placeholder="100" maxlength="4" />
    
            </div>
            <button class="btn-add" onclick="saveQuest()">Create</button>
            <br />
            <button class="btn-cancel" onclick="closeModal('newQuestModalOverlay')">Cancel</button>
          </div>
        </div>

        <div class="modal-overlay" id="editQuestModalOverlay">
          <div class="modal-box">
            <h2>Edit quest</h2>
            <div>
              <label>Icon</label>
              <div class="avatar-picker" id="editQuestIconPicker">
                <div class="avatar-option selected-avatar" style="background:linear-gradient(135deg,#fff9c4,#fff176)" data-icon="icon-star" onclick="pickQuestIcon(this,'editQuestIcon')">
                  <img src="../Assets/img/icon-star.svg" alt="Star icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#c5cae9,#9fa8da)" data-icon="icon-calendar" onclick="pickQuestIcon(this,'editQuestIcon')">
                  <img src="../Assets/img/icon-calendar.svg" alt="Calendar icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#b2dfdb,#80cbc4)" data-icon="icon-fire" onclick="pickQuestIcon(this,'editQuestIcon')">
                  <img src="../Assets/img/icon-fire.svg" alt="Fire icon" />
                </div>
                <div class="avatar-option" style="background:linear-gradient(135deg,#ffccbc,#ffab91)" data-icon="icon-done" onclick="pickQuestIcon(this,'editQuestIcon')">
                  <img src="../Assets/img/icon-done.svg" alt="Done icon" />
                </div>
              </div>
              <input type="hidden" id="editQuestIcon" value="icon-star" />
              <label for="editQuestNameInput">Name</label>
              <input class="modal-input" id="editQuestNameInput" type="text" placeholder="Quest name" maxlength="50" />
              <label for="editQuestXpInput">XP Reward</label>
              <input class="modal-input" id="editQuestXpInput" type="text" placeholder="100" maxlength="4" />
            </div>
            <button class="btn-add" onclick="editQuest()">Save</button>
            <br />
            <button class="btn-cancel" onclick="closeModal('editQuestModalOverlay')">Cancel</button>
          </div>
        </div>
      </div>
    </main>
  </body>
</html>
