let currentXp = 0;
const MAX_XP = 500;

async function loadRewards() {
  try {
    const response = await fetch('../../Controller/HomePageChild/Reward.php?action=getRewards');
    const data = await response.json();

    if (!data.success) {
      return;
    }

    const container = document.querySelector('.row.g-3');
    container.innerHTML = '';

    data.rewards.forEach(reward => {
      const canAfford = currentXp >= reward.xp_cost;
      const isCompleted = reward.is_completed;
      const isLocked  = !canAfford || reward.is_completed;
      const tagClass  = reward.type === 'in_app' ? 'tag-inapp' : 'tag-real';
      const tagIcon   = reward.type === 'in_app' ? '<img src="../Assets/img/icon-multiples-stars.svg" alt=""> In-app' : '<img src="../Assets/img/icon-gift.svg" alt=""> Real reward';

      const col = document.createElement('div');
      col.className = 'col-6';
      col.innerHTML = `
        <div class="reward-card ${isCompleted ? 'completed' : isLocked ? 'locked' : ''}">
          <div class="reward-emoji"><img src="../Assets/img/${reward.icon}.svg" alt="${reward.name}"></div>
          <div class="reward-name">${reward.name}</div>
          <span class="reward-tag ${tagClass}">${tagIcon}</span>
          <button 
            class="reward-btn ${isCompleted ? 'completed-btn' : isLocked ? 'locked-btn' : 'unlocked'}" 
            ${isCompleted || isLocked ? 'disabled' : ''}
            data-id="${reward.id}"
            data-cost="${reward.xp_cost}">
            ${isCompleted ? '✓ Unlocked' : isLocked ? `<img src="../Assets/img/icon-lock.svg" alt="Lock"> ${reward.xp_cost} XP` : `${reward.xp_cost} XP` }
          </button>
        </div>
      `;
      container.appendChild(col);
    });

    document.querySelectorAll('.reward-btn.unlocked').forEach(btn => {
      btn.addEventListener('click', () => buyReward(btn.dataset.id, parseInt(btn.dataset.cost)));
    });

  } catch (err) {
    console.error('Network error :', err);
  }
}

async function buyReward(reward_id, xp_cost) {
  const formData = new FormData();
  formData.append('action', 'buyReward');
  formData.append('reward_id', reward_id);

  try {
    const response = await fetch('../../Controller/HomePageChild/Reward.php', {
      method: 'POST',
      body: formData
    });

    const data = await response.json();

    if (!data.success) {
      alert(data.message);
      return;
    }

    currentXp = data.new_xp;
    updateXpDisplay();
    await loadRewards();

  } catch (err) {
    console.error('Erreur réseau :', err);
  }
}

async function loadXp() {
  try {
    const response = await fetch('../../Controller/HomePageChild/Reward.php?action=getXp');
    const data = await response.json();

    if (!data.success) {
      return;
    }

    currentXp = data.xp;
    updateXpDisplay();
  } catch (err) {
    console.error('Erreur XP :', err);
  }
}

function updateXpDisplay() {
  const percent = Math.min((currentXp / MAX_XP) * 100, 100);
  document.getElementById('xpDisplay').innerHTML = `${currentXp} <span>XP</span>`;
  document.getElementById('xpFill').style.width = `${percent}%`;
}

document.addEventListener('DOMContentLoaded', async () => {
  await loadXp();
  await loadRewards();
});