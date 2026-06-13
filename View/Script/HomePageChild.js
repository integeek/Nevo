const MAX_XP = 500;
const positiveMessages = [
  { message: "You're doing great" },
  { message: "Small progress is still progress" },
  { message: "Every effort counts" },
  { message: "Keep the streak alive"},
  { message: "You're stronger than you think" },
  { message: "You made progress today" },
  { message: "Don't give up now" },
  { message: "Tiny steps, big results" },
  { message: "You're leveling up" },
];

window.addEventListener('load', () => {
  setTimeout(() => document.getElementById('xpBar').style.width = '80%', 200);
  updateProgress();
  setRandomPositiveMessage()
});

document.addEventListener('DOMContentLoaded', async () => {
  await initPage();
});

/**
 * Fetches child data, quests, routines from server, then renders them on page and updates progress display
 * @returns {Promise<void>}
 */
async function initPage() {
  try {
    const res  = await fetch("../../Controller/HomePageChild/HomePageChild.php?action=init");
    const data = await res.json();
    if (!data.success) { 
      return; 
    }

    renderChild(data.child);
    renderQuests(data.quests);
    renderRoutines(data.routines);
    updateProgress();
    setRandomPositiveMessage();

  } catch (err) {
    console.error('Network error :', err);
  }
}

/**
 * Updates hero section with child's name and XP, then updates XP bar fill based on current XP
 * @param {Object} child 
 * @returns {void}
 */
function renderChild(child) {
  document.querySelector('.avatar-circle').innerHTML = `<img src="../Assets/img/${child.avatar}.svg"/>`;
  document.querySelector('.hero-name').textContent = `Hey, ${child.fullname} !`;
  document.querySelector('.xp-label').textContent = `${child.xp} XP`;
  document.querySelector('#statXP').textContent = child.xp;
  document.querySelector("#statStreak").textContent = child.streak
  const percent = Math.min((child.xp / MAX_XP) * 100, 100);
  setTimeout(() => document.getElementById('xpBar').style.width = percent + '%', 200);
}

/**
 * Renders quest cards in quest list based on quests data from server, displaying quest name, XP value, type (auto-completed or daily), and completion status
 * Auto-completed quests are styled differently than daily quests
 * @param {Array} quests
 * @returns {void} 
 */
function renderQuests(quests) {
  const list = document.querySelector('.quests-list');
  list.innerHTML = '';

  quests.forEach(quest => {
    const div = document.createElement('div');
    div.className = 'quest-card';
    div.dataset.questId = quest.id;
    div.dataset.xp = quest.xp_value;

    if (quest.is_auto) {
      div.innerHTML = `
        <div class="quest-main">
          <div class="quest-emoji" style="background:#e3f2fd;">
            <img src="../Assets/img/${quest.icon}.svg" alt="${quest.name}">
          </div>
          <div class="quest-info">
            <div class="quest-name ${quest.is_completed ? 'done' : ''}">${quest.name}</div>
            <div class="quest-meta">
              <span class="quest-xp">+ ${quest.xp_value} XP</span>
              <span class="quest-badge badge-auto">✓ Auto-completed</span>
            </div>
          </div>
        </div>`;
    } else {
      div.innerHTML = `
        <div class="quest-main">
          <div class="quest-emoji" style="background:#fff8e1;">
            <img src="../Assets/img/${quest.icon}.svg" alt="${quest.name}">
          </div>
          <div class="quest-info">
            <div class="quest-name ${quest.is_completed ? 'done' : ''}">${quest.name}</div>
            <div class="quest-meta">
              <span class="quest-xp">+ ${quest.xp_value} XP</span>
              <span class="quest-badge badge-daily">Daily</span>
            </div>
          </div>
        </div>`;
    }
    list.appendChild(div);
  });
}

/**
 * Renders routine cards in routine list based on routines data from server, displaying routine name, XP value, completion status, and step completion progress
 * Each routine can be expanded to show its steps, which can be toggled as completed or not completed
 * @param {Array} routines 
 * @returns {void}
 */
function renderRoutines(routines) {
  const list = document.getElementById('routines-list');
  list.innerHTML = '';

  routines.forEach(routine => {
const stepsData = routine.steps || [];
  const steps = Array.isArray(stepsData)
    ? stepsData.filter(s => s.id)
    : JSON.parse(stepsData).filter(s => s.id);
    const done = steps.filter(s => s.is_completed).length;
    const total = steps.length;
    const allDone = routine.is_completed;

    const stepsHTML = steps.map(step => `
      <div class="sub-step" data-step-id="${step.id}" onclick="toggleSubStep(this)">
        <div class="sub-check ${step.is_completed ? 'done' : ''}">${step.is_completed ? '✓' : ''}</div>
        <span class="sub-step-label ${step.is_completed ? 'done' : ''}">${step.name}</span>
      </div>`).join('');

    const div = document.createElement('div');
    div.className = 'quest-card';
    div.dataset.routineId = routine.id;
    div.dataset.xp = routine.xp_value;
    div.innerHTML = `
      <div class="quest-main" onclick="toggleSteps(this)">
        <div class="quest-emoji" style="background:#e8f5e9;">
          <img src="../Assets/img/${routine.icon}.svg" class="quest-img" alt="${routine.name}">
        </div>
        <div class="quest-info">
          <div class="quest-name ${allDone ? 'done' : ''}">${routine.name}</div>
          <div class="quest-meta">
            <span class="quest-xp">+ ${routine.xp_value} XP</span>
            <span class="quest-steps">${done}/${total} steps</span>
          </div>
        </div>
        <span class="quest-chevron">&rsaquo;</span>
        <button class="quest-check ${allDone ? 'done' : ''}" onclick="toggleQuest(event, this)">${allDone ? '✓' : ''}</button>
      </div>
      <div class="sub-steps">
        <div class="sub-steps-inner">${stepsHTML}</div>
      </div>`;
    list.appendChild(div);
  });
}

/**
 * Selects random positive message from predefined list and displays it in hero section to encourage child and celebrate their progress
 * @returns {void}
 */
function setRandomPositiveMessage() {
  const randomIndex = Math.floor(Math.random() * 9);
  const selectedMessage = positiveMessages[randomIndex];
  document.querySelector('.message-positive').textContent = selectedMessage.message;
}

/**
 * Toggles visibility of sub-steps for given quest
 * @param {HTMLElement} questMainEl 
 * @returns {void}
 */
function toggleSteps(questMainEl) {
  questMainEl.closest('.quest-card').classList.toggle('open');
}

/**
 * Marks or unmarks routine as completed by toggling "done" class on check button and quest name
 * Sends updated completion status to server to update child's XP accordingly
 * @param {Event} event 
 * @param {HTMLElement} btn 
 * @returns {Promise<void>}
 */
async function toggleQuest(event, btn) {
  event.stopPropagation();
  const questCard = btn.closest('.quest-card');
  const routineId = questCard.dataset.routineId;
  const xpValue = parseInt(questCard.dataset.xp);
  const subSteps = questCard.querySelectorAll('.sub-step');
  const isDone = btn.classList.toggle('done');
  btn.textContent = isDone ? '✓' : '';
  questCard.querySelector('.quest-name')?.classList.toggle('done', isDone);

  subSteps.forEach(step => {
    const check = step.querySelector('.sub-check');
    const label = step.querySelector('.sub-step-label');
    check.classList.toggle('done', isDone);
    check.textContent = isDone ? '✓' : '';
    label.classList.toggle('done', isDone);
  });

  updateStepCounter(questCard);
  updateProgress();

  const fd = new FormData();
  fd.append('action', 'toggleRoutine');
  fd.append('routine_id', routineId);
  fd.append('xp_value', xpValue);
  fd.append('is_completed', isDone);
  await fetch("../../Controller/HomePageChild/HomePageChild.php", 
    { method: 'POST', 
      body: fd 
    });
}

/**
 * Toggles completion status of sub-step
 * Updates step counter and overall routine completion status accordingly, then sends updated step status to server to update child's XP if needed
 * @param {HTMLElement} el 
 * @returns {Promise<void>}
 */
async function toggleSubStep(el) {
  const questCard = el.closest('.quest-card');
  const stepId = el.dataset.stepId;
  const check = el.querySelector('.sub-check');
  const label = el.querySelector('.sub-step-label');
  const isDone = check.classList.toggle('done');

  check.textContent = isDone ? '✓' : '';
  label.classList.toggle('done', isDone);
  updateStepCounter(questCard);
  syncQuestState(questCard);

  const fd = new FormData();
  fd.append('action', 'toggleStep');
  fd.append('step_id', stepId);
  fd.append('is_completed', isDone);
  await fetch("../../Controller/HomePageChild/HomePageChild.php", 
    { method: 'POST', 
      body: fd 
    });
}

/**
 * Updates "X/Y steps" counter on routine card based on how many sub-steps are marked as completed
 * @param {HTMLElement} questCard 
 * @returns {void}
 */
function updateStepCounter(questCard) {
  const totalSteps = questCard.querySelectorAll('.sub-step').length;
  const doneSteps = questCard.querySelectorAll('.sub-check.done').length;
  const stepCounter = questCard.querySelector('.quest-steps');
  if (stepCounter) {
    stepCounter.textContent = `${doneSteps}/${totalSteps} steps`;
  }
}

/**
 * Checks if all sub-steps of routine are completed, and if so, marks routine as completed by toggling "done" class on check button and quest name
 * @param {HTMLElement} questCard 
 * @returns {void}
 */
function syncQuestState(questCard) {
  const total = questCard.querySelectorAll('.sub-step').length;
  const done = questCard.querySelectorAll('.sub-check.done').length;
  const allDone = total === done;
  const btn = questCard.querySelector('.quest-check');
  const name = questCard.querySelector('.quest-name');

  if (btn) { 
    btn.classList.toggle('done', allDone); 
    btn.textContent = allDone ? '✓' : ''; 
  }
  if (name) {
    name.classList.toggle('done', allDone);
  }
  updateProgress();
}

/**
 * Updates progress bar fill and label based on how many routines are completed out of total, and displays encouraging message in hero section based on how many routines are left to complete
 * @returns {void}
 */
function updateProgress() {
  const routineSection = document.getElementById('routines-list');
  const total = routineSection.querySelectorAll('.quest-card').length;
  const done = routineSection.querySelectorAll('.quest-check.done').length;
  const remaining = total - done;
  const pct = total > 0 ? Math.round((done / total) * 100) : 0;

  document.getElementById('progressBar').style.width  = pct + '%';
  document.getElementById('progressLabel').textContent = `${done}/${total}`;
  document.getElementById('progressNote').textContent  = remaining === 0 ? 'All routines done! Amazing work!' : `${remaining} routine${remaining > 1 ? 's' : ''} remaining. You got this!`;
}