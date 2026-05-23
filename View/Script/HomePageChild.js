const TOTAL_QUESTS = 5;
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

function setRandomPositiveMessage() {
  const randomIndex = Math.floor(Math.random() * 9);
  const selectedMessage = positiveMessages[randomIndex];
  document.querySelector('.message-positive').textContent = selectedMessage.message;
}

function toggleSteps(questMainEl) {
  questMainEl.closest('.quest-card').classList.toggle('open');
}

function toggleQuest(event, btn) {
  event.stopPropagation();
  const questCard = btn.closest('.quest-card');
  const subSteps = questCard.querySelectorAll('.sub-step');
  const isDone = btn.classList.toggle('done');
  btn.textContent = isDone ? '✓' : '';

  const nameEl = questCard.querySelector('.quest-name');
  nameEl?.classList.toggle('done', isDone);
  subSteps.forEach(step => {
    const check = step.querySelector('.sub-check');
    const label = step.querySelector('.sub-step-label');
    check.classList.toggle('done', isDone);
    check.textContent = isDone ? '✓' : '';
    label.classList.toggle('done', isDone);
  });

  updateStepCounter(questCard);
  updateProgress();
}

function toggleSubStep(el) {
  const questCard = el.closest('.quest-card');
  const check = el.querySelector('.sub-check');
  const label = el.querySelector('.sub-step-label');
  const isDone = check.classList.toggle('done');
  check.textContent = isDone ? '✓' : '';
  label.classList.toggle('done', isDone);
  updateStepCounter(questCard);
  syncQuestState(questCard);
}

function updateStepCounter(questCard) {
  const totalSteps = questCard.querySelectorAll('.sub-step').length;
  const doneSteps = questCard.querySelectorAll('.sub-check.done').length;
  const stepCounter = questCard.querySelector('.quest-steps');
  stepCounter.textContent = `${doneSteps}/${totalSteps} steps`;
}

function syncQuestState(questCard) {
  const totalSteps = questCard.querySelectorAll('.sub-step').length;
  const doneSteps = questCard.querySelectorAll('.sub-check.done').length;
  const questBtn = questCard.querySelector('.quest-check');
  const questName = questCard.querySelector('.quest-name');
  const allDone = totalSteps === doneSteps;
  questBtn.classList.toggle('done', allDone);
  questBtn.textContent = allDone ? '✓' : '';
  questName.classList.toggle('done', allDone);

  updateProgress();
}

function updateProgress() {
  const routineSection = document.getElementById('routines-list');
  const total = routineSection.querySelectorAll('.quest-card').length;
  const done = routineSection.querySelectorAll('.quest-check.done').length;
  const remaining = total - done;
  const pourcentage = Math.round((done / total) * 100);

  document.getElementById('progressBar').style.width = pourcentage + '%';
  document.getElementById('progressLabel').textContent = done + '/' + total;
  document.getElementById('progressNote').textContent =
    remaining === 0
      ? 'All routines done! Amazing work!'
      : remaining + ' routine' + (remaining > 1 ? 's' : '') + ' remaining. You got this!';
}