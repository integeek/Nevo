const DATA = {
  lila: {
    name:'Mia', icon:'../Assets/img/icon-cat.svg', labelBg:'#f0eafa', labelColor:'#7c3aed',
    stats:{ completion:'72%', week:'30<sub>/42</sub>', streak:'5 days', routines:3 },
    routines:[
      { icon:'../Assets/img/icon-alarm.svg', bg:'#fff8e1', name:'Morning routine', xp:30, steps:'3 steps' },
      { icon:'../Assets/img/icon-drop-water.svg', bg:'#e3f2fd', name:'Stay hydrated', xp:15, steps:'1 step' },
      { icon:'../Assets/img/icon-books.svg', bg:'#e8f5e9', name:'Reading time', xp:15, steps:'1 step' },
    ],
    rewards:[
      { icon:'../Assets/img/icon-book-solo.svg', name:'Extra story time', pts:'30 XP', done:true },
      { icon:'../Assets/img/icon-movie.svg', name:'Movie night pick',  pts:'50 XP', done:false },
    ],
    feelings:{
      entries:[
        { icon:'../Assets/img/icon-awesome.svg', bg:'#dcfce7', text:'Took my medicine without crying !', time:'Today, 09:30' },
        { icon:'../Assets/img/icon-hurt.svg', bg:'#fee2e2', text:'My tummy hurts', time:'Yesterday, 18:30' },
        { icon:'../Assets/img/icon-awesome.svg', bg:'#dcfce7', text:'Best day ever !',  time:'Yesterday, 11:05' },
        { icon:'../Assets/img/icon-sad.svg', bg:'#fef3c7', text:'Tired today', time:'2 days ago' },
      ]
    },
    analytics:{
      bars:[40,70,55,85,60,30,20],
      days:['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
    }
  },
  noah: {
    name:'Emma', icon:'../Assets/img/icon-tiger.svg', labelBg:'#fff3e0', labelColor:'#c2410c',
    stats:{ completion:'58%', week:'18<sub>/31</sub>', streak:'2 days', routines:2 },
    routines:[
      { icon:'../Assets/img/icon-sport.svg', bg:'#e0f7fa', name:'Move your body', xp:20, steps:'2 steps' },
      { icon:'../Assets/img/icon-pills.svg', bg:'#fce4ec', name:'Take your medication', xp:25, steps:'1 step' },
    ],
    rewards:[
      { icon:'../Assets/img/icon-freeze.svg', name:'Freeze one day', pts:'80 XP', done:false },
      { icon:'../Assets/img/icon-chocolate.svg', name:'Extra desserts at diner',    pts:'40 XP', done:true  },
    ],
    feelings:{
      entries:[
        { icon:'../Assets/img/icon-awesome.svg', bg:'#fef3c7', text:'I love art class !', time:'Today, 10:00' },
        { icon:'../Assets/img/icon-lost.svg', bg:'#fee2e2', text:'I am lost',   time:'2 days ago' },
      ]
    },
    analytics:{
      bars:[60,40,30,50,45,70,10],
      days:['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
    }
  }
};

const makeIcon = (src) => `<img src="${src}" style="width:24px; height:24px; object-fit:contain;">`;

function openModal(id) {
    document.getElementById(id).classList.add('active');
    setTimeout(() => document.getElementById(id).focus(), 100);
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}
function openDelete(card, id) {
    currentEditCard = card;
    if (id === 'deleteRoutine') {
        const name = card.querySelector('.routine-name').textContent;
        if (confirm(`Do you really want to delete the routine ${name} ? You will no longer be able to recover the data.`)) {
            currentEditCard.remove();
            currentEditCard = null;
        }
    } else if (id === 'deleteReward') {
        const name = card.querySelector('.reward-name').textContent;
        if (confirm(`Do you really want to delete the reward ${name} ? You will no longer be able to recover the data.`)) {
            currentEditCard.remove();
            currentEditCard = null;
        }
    }
}  

function openEdit(card, id) {
    if (id === 'editRoutineModalOverlay') {
        const name = card.querySelector('.routine-name').textContent ;
        const xp  = card.querySelector('.ri').textContent.replace('XP', '')
        document.getElementById('editNameInput').value = name;
        document.getElementById('editXpInput').value = xp;
    } else if (id === 'editRewardModalOverlay') {
        const name = card.querySelector('.reward-name').textContent ;
        const xp  = card.querySelector('.reward-pts').textContent.replace('XP', '');
        document.getElementById('editRewardNameInput').value = name;
        document.getElementById('editRewardXpInput').value = xp;
    }
    openModal(id);
}

function renderAll(key) {
  const d = DATA[key];

  document.getElementById('sCompletion').textContent = d.stats.completion;
  document.getElementById('sWeek').innerHTML         = d.stats.week;
  document.getElementById('sStreak').textContent     = d.stats.streak;
  document.getElementById('sRoutines').textContent   = d.stats.routines;

  const lbl = document.getElementById('setupLabel');
  lbl.innerHTML = `<span>${makeIcon(d.icon)}</span>&nbsp;${d.name}'s setup`;
  lbl.style.background = d.labelBg;
  lbl.style.color      = d.labelColor;

  document.getElementById('routinesList').innerHTML = d.routines.map(r => `
    <div class="routine-item">
      <div class="routine-icon" style="background:${r.bg}">${makeIcon(r.icon)}</div>
      <div class="routine-body">
        <div class="routine-name">${r.name}</div>
        <div class="routine-meta">
          <span class="ri">${r.xp} XP</span>
          <span>${r.steps}</span>
        </div>
      </div>
      <div class="routine-actions">
        <button class="ibt">&rsaquo;</button>
        <button class="ibt del" onclick="openEdit(this.closest('.routine-item'), 'editRoutineModalOverlay')"><img src="../Assets/img/icon-edit.svg" alt="Edit icon" /></button>
        <button class="ibt del" onclick="openDelete(this.closest('.routine-item'), 'deleteRoutine')"><img src="../Assets/img/icon-delete.svg" alt="Delete icon" /></button>
      </div>
    </div>
  `).join('');

  document.getElementById('rewardsList').innerHTML = d.rewards.map(r => `
    <div class="reward-card">
      <div class="reward-icon">${makeIcon(r.icon)}</div>
      <div class="reward-info">
        <div class="reward-name">${r.name}</div>
        <div class="reward-pts">${r.pts}</div>
        <div class="reward-status"><span class="spill ${r.done?'sdone':'stodo'}">${r.done?'✓ Done':'To do'}</span></div>
      </div>
      <div class="rew-actions">
        <button class="rew-edit" onclick="openEdit(this.closest('.reward-card'), 'editRewardModalOverlay')"><img src="../Assets/img/icon-edit.svg" alt="Edit icon"/></button>
        <button class="rew-delete" onclick="openDelete(this.closest('.reward-card'), 'deleteReward')"><img src="../Assets/img/icon-delete.svg" alt="Delete icon"/></button>
      </div>
    </div>
  `).join('');

  const f = d.feelings;
  document.getElementById('feelingsList').innerHTML = `
    ${f.entries.map(e=>`
      <div class="feeling-row">
        <div class="fmoji">${makeIcon(e.icon)}</div>
        <div class="fbody">
          <div class="ftext">${e.text}</div>
          ${e.time?`<div class="ftime">${e.time}</div>`:''}
        </div>
      </div>
    `).join('')}
  `;

  const a = d.analytics;
  document.getElementById('barChart').innerHTML = a.bars.map((h,i)=>`
    <div class="bar-col"><div class="bar" style="height:${h}%"></div><div class="bday">${a.days[i]}</div></div>
  `).join('');
  document.getElementById('sBold').textContent = a.bold;
  document.getElementById('sSub').textContent  = a.sub;
}

function switchChild(key, el) {
  document.querySelectorAll('.child-pill').forEach(p => p.classList.remove('active'));
  el.classList.add('active');
  renderAll(key);
}

function switchTab(btn, tab) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('tab-' + tab).classList.add('active');
}

renderAll('lila');
