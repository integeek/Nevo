const MAX_XP = 500;

function calculateXpPercent(currentXp) {
    return Math.min((currentXp / MAX_XP) * 100, 100);
}

function canAfford(currentXp, xp_cost) {
    return currentXp >= xp_cost;
}

function isLocked(currentXp, reward) {
    return !canAfford(currentXp, reward.xp_cost) || reward.is_completed;
}

module.exports = { calculateXpPercent, canAfford, isLocked };
