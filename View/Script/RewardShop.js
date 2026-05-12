function updateXPBanner() {
    const nextCost = rewards.find(r => r.cost > currentXP)?.cost ?? null;
    if (nextCost) {
    const diff = nextCost - currentXP;
    document.getElementById('xpNext').textContent = `${diff} XP until next reward`;
    const max = nextCost;
    document.getElementById('xpFill').style.width = `${(currentXP / max) * 100}%`;
    } else {
    document.getElementById('xpNext').textContent = 'All rewards unlocked! 🎉';
    document.getElementById('xpFill').style.width = '100%';
    }
}
 
updateXPBanner();