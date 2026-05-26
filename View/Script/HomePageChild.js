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

function renderChild(child) {
  document.querySelector('.hero-name').textContent = `Hey, ${child.fullname} !`;
  document.querySelector('.xp-label').textContent = `${child.xp} XP`;
  document.querySelector('#statXP').textContent = child.xp;
  document.querySelector("#statStreak").textContent = child.streak
  const percent = Math.min((child.xp / MAX_XP) * 100, 100);
  setTimeout(() => document.getElementById('xpBar').style.width = percent + '%', 200);
}

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

function renderRoutines(routines) {
  const list = document.getElementById('routines-list');
  list.innerHTML = '';

  routines.forEach(routine => {
    const steps = JSON.parse(routine.steps || '[]').filter(s => s.id);
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

function setRandomPositiveMessage() {
  const randomIndex = Math.floor(Math.random() * 9);
  const selectedMessage = positiveMessages[randomIndex];
  document.querySelector('.message-positive').textContent = selectedMessage.message;
}

function toggleSteps(questMainEl) {
  questMainEl.closest('.quest-card').classList.toggle('open');
}

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

function updateStepCounter(questCard) {
  const totalSteps = questCard.querySelectorAll('.sub-step').length;
  const doneSteps = questCard.querySelectorAll('.sub-check.done').length;
  const stepCounter = questCard.querySelector('.quest-steps');
  if (stepCounter) {
    stepCounter.textContent = `${doneSteps}/${totalSteps} steps`;
  }
}

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