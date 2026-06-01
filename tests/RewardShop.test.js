const { calculateXpPercent, canAfford, isLocked } = require('./rewardRules');

// XP percentage calculation
test('calculateXpPercent returns correct percentage', () => {
    expect(calculateXpPercent(250)).toBe(50);
});

test('calculateXpPercent caps at 100 when xp exceeds MAX_XP', () => {
    expect(calculateXpPercent(600)).toBe(100);
});

test('calculateXpPercent returns 0 when xp is 0', () => {
    expect(calculateXpPercent(0)).toBe(0);
});

// canAfford
test('canAfford returns true when child has enough xp', () => {
    expect(canAfford(300, 200)).toBe(true);
});

test('canAfford returns true when child has exactly the right amount', () => {
    expect(canAfford(200, 200)).toBe(true);
});

test('canAfford returns false when child does not have enough xp', () => {
    expect(canAfford(100, 200)).toBe(false);
});

// isLocked
test('isLocked returns false when child can afford and reward is not completed', () => {
    expect(isLocked(300, { xp_cost: 200, is_completed: false })).toBe(false);
});

test('isLocked returns true when child cannot afford the reward', () => {
    expect(isLocked(100, { xp_cost: 200, is_completed: false })).toBe(true);
});

test('isLocked returns true when reward is already completed', () => {
    expect(isLocked(300, { xp_cost: 200, is_completed: true })).toBe(true);
});
